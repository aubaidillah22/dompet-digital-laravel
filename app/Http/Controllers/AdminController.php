<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function requireAdmin()
    {
        if (session('role') !== 'admin') {
            abort(403, 'Access denied. Admin only.');
        }
    }

    public function dashboard()
    {
        $this->requireAdmin();

        return view('admin.dashboard');
    }

    public function getStats()
    {
        $this->requireAdmin();
        $totalUsers = User::count();
        $totalTransactions = Transaction::count();
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');

        // Wallet stats
        $mainWallets = Wallet::where('type', 'main')->get();
        $savingsWallets = Wallet::where('type', 'savings')->get();
        $totalMainBalance = $mainWallets->sum(function ($w) {
            return $w->balance;
        });
        $totalSavingsBalance = $savingsWallets->sum(function ($w) {
            return $w->balance;
        });

        $chartData = User::where('role', 'user')
            ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
            ->selectRaw("
                users.username,
                users.full_name,
                users.id,
                COALESCE(SUM(CASE WHEN transactions.type = 'expense' THEN transactions.amount ELSE 0 END), 0) as total_expense,
                COALESCE(SUM(CASE WHEN transactions.type = 'income' THEN transactions.amount ELSE 0 END), 0) as total_income
            ")
            ->groupBy('users.id', 'users.username', 'users.full_name')
            ->orderByDesc('total_expense')
            ->get();

        // Get wallet balances per user
        $mainWalletBalances = Wallet::where('type', 'main')->get()->keyBy('user_id');
        $savingsWalletBalances = Wallet::where('type', 'savings')->get()->keyBy('user_id');

        $walletChartData = User::where('role', 'user')->get()->map(function ($user) use ($mainWalletBalances, $savingsWalletBalances) {
            return [
                'full_name' => $user->full_name,
                'username' => $user->username,
                'main_balance' => isset($mainWalletBalances[$user->id]) ? $mainWalletBalances[$user->id]->balance : 0,
                'savings_balance' => isset($savingsWalletBalances[$user->id]) ? $savingsWalletBalances[$user->id]->balance : 0,
            ];
        })->sortByDesc('main_balance')->values();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_users' => $totalUsers,
                'total_transactions' => $totalTransactions,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'total_main_balance' => $totalMainBalance,
                'total_savings_balance' => $totalSavingsBalance,
            ],
            'chart_data' => $chartData,
            'wallet_chart_data' => $walletChartData,
        ]);
    }

    public function getUsers(Request $request)
    {
        $this->requireAdmin();

        $page = max(1, (int) $request->query('page', 1));
        $limit = max(1, min(100, (int) $request->query('limit', 10)));
        $search = $request->query('search', '');
        $offset = ($page - 1) * $limit;

        // Count total (without join for performance)
        $countQuery = User::query();
        if ($search) {
            $countQuery->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        $totalCount = $countQuery->count();

        // Fetch paginated users with stats
        $query = User::leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
            ->selectRaw("
                users.id,
                users.username,
                users.full_name,
                users.role,
                users.created_at as joined_date,
                COALESCE(COUNT(transactions.id), 0) as total_transactions,
                COALESCE(SUM(CASE WHEN transactions.type = 'income' THEN transactions.amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN transactions.type = 'expense' THEN transactions.amount ELSE 0 END), 0) as total_expense,
                COALESCE(SUM(CASE WHEN transactions.type = 'income' THEN transactions.amount ELSE -transactions.amount END), 0) as balance
            ");

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                    ->orWhere('users.full_name', 'like', "%{$search}%");
            });
        }

        $query->groupBy('users.id', 'users.username', 'users.full_name', 'users.role', 'users.created_at');

        $users = $query->orderByDesc('users.id')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Get wallet balances for each user
        $walletBalances = Wallet::where('type', 'main')
            ->get()
            ->keyBy('user_id')
            ->map(function ($w) {
                return $w->balance;
            });
        $savingsBalances = Wallet::where('type', 'savings')
            ->get()
            ->keyBy('user_id')
            ->map(function ($w) {
                return $w->balance;
            });

        $users->transform(function ($user) use ($walletBalances, $savingsBalances) {
            $user->main_wallet_balance = $walletBalances->get($user->id, 0);
            $user->savings_wallet_balance = $savingsBalances->get($user->id, 0);

            return $user;
        });

        return response()->json([
            'success' => true,
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalCount,
                'total_pages' => (int) ceil($totalCount / max($limit, 1)),
            ],
        ]);
    }

    public function addUser(Request $request)
    {
        $this->requireAdmin();
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|min:3|max:50|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        // Create default wallets for new user
        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Dompet Utama',
            'type' => 'main',
            'icon' => 'fa-wallet',
            'color' => '#f0b429',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Tabungan',
            'type' => 'savings',
            'icon' => 'fa-piggy-bank',
            'color' => '#10b981',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'user_id' => $user->id,
        ], 201);
    }

    public function editUser(Request $request, $id)
    {
        $this->requireAdmin();
        $user = User::findOrFail($id);

        $rules = [
            'full_name' => 'sometimes|string|max:100',
            'role' => 'sometimes|in:user,admin',
            'password' => 'sometimes|string|min:8',
        ];

        $data = $request->validate($rules);

        $updates = [];
        if (! empty($data['full_name'])) {
            $updates['full_name'] = $data['full_name'];
        }
        if (! empty($data['role'])) {
            if ($id == session('user_id') && $data['role'] === 'user') {
                return response()->json(['error' => 'Anda tidak bisa mengubah role sendiri'], 400);
            }
            $updates['role'] = $data['role'];
        }
        if (! empty($data['password'])) {
            $updates['password'] = $data['password'];
        }

        if (empty($updates)) {
            return response()->json(['error' => 'Tidak ada data yang diupdate'], 400);
        }

        $user->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
        ]);
    }

    public function deleteUser($id)
    {
        $this->requireAdmin();
        if ($id == session('user_id')) {
            return response()->json(['error' => 'Anda tidak bisa menghapus akun sendiri'], 400);
        }

        $user = User::findOrFail($id);
        $user->transactions()->delete();
        $user->wallets()->delete(); // Delete wallets to avoid orphaned records
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus',
        ]);
    }

    public function getUserTransactions(Request $request, $userId)
    {
        $this->requireAdmin();
        $page = max(1, (int) $request->query('page', 1));
        $limit = max(1, min(100, (int) $request->query('limit', 10)));
        $offset = ($page - 1) * $limit;

        $day = $request->query('day');
        $month = $request->query('month');
        $year = $request->query('year');

        $query = Transaction::where('user_id', $userId);

        if ($day) {
            $query->whereDay('transaction_date', (int) $day);
        }
        if ($month) {
            $query->whereMonth('transaction_date', (int) $month);
        }
        if ($year) {
            $query->whereYear('transaction_date', (int) $year);
        }

        $totalCount = (clone $query)->count();

        $transactions = (clone $query)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalCount,
                'total_pages' => (int) ceil($totalCount / $limit),
            ],
        ]);
    }
}
