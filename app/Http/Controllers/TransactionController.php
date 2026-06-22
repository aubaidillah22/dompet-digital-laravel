<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    private function dateFormatYearMonth($column)
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            return "DATE_FORMAT($column, '%Y-%m')";
        }

        return "strftime('%Y-%m', $column)";
    }

    private function dateFormatYear($column)
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            return "YEAR($column)";
        }

        return "strftime('%Y', $column)";
    }

    public function index(Request $request)
    {
        $userId = session('user_id');
        $month = $request->query('month');
        $walletId = $request->query('wallet_id');
        $page = max(1, (int) $request->query('page', 1));
        $limit = max(1, min(100, (int) $request->query('limit', 10)));
        $offset = ($page - 1) * $limit;

        $query = Transaction::where('user_id', $userId);

        if ($month) {
            $query->whereRaw($this->dateFormatYearMonth('transaction_date').' = ?', [$month]);
        }

        if ($walletId) {
            $query->where('wallet_id', $walletId);
        }

        $totalCount = (clone $query)->count();

        $transactions = (clone $query)
            ->with('wallet')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $summaryQuery = Transaction::where('user_id', $userId);
        if ($month) {
            $summaryQuery->whereRaw($this->dateFormatYearMonth('transaction_date').' = ?', [$month]);
        }
        if ($walletId) {
            $summaryQuery->where('wallet_id', $walletId);
        }

        $summary = (clone $summaryQuery)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_transactions
            ")
            ->first();

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'summary' => [
                'total_income' => $summary->total_income,
                'total_expense' => $summary->total_expense,
                'balance' => $summary->total_income - $summary->total_expense,
                'total_transactions' => $summary->total_transactions,
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalCount,
                'total_pages' => (int) ceil($totalCount / $limit),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'wallet_id' => 'nullable|exists:wallets,id',
        ]);

        $data['user_id'] = session('user_id');

        // Validate wallet belongs to user
        if (! empty($data['wallet_id'])) {
            $wallet = Wallet::where('id', $data['wallet_id'])
                ->where('user_id', $data['user_id'])
                ->first();
            if (! $wallet) {
                return response()->json([
                    'success' => false,
                    'error' => 'Wallet tidak ditemukan atau bukan milik Anda.',
                ], 403);
            }
        } else {
            // Assign to default wallet if not specified
            $defaultWallet = Wallet::where('user_id', $data['user_id'])
                ->where('type', 'main')
                ->first();
            if ($defaultWallet) {
                $data['wallet_id'] = $defaultWallet->id;
            }
        }

        $transaction = Transaction::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan',
            'transaction_id' => $transaction->id,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = session('user_id');

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $data = $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'wallet_id' => 'nullable|exists:wallets,id',
        ]);

        // Validate wallet belongs to user
        if (! empty($data['wallet_id'])) {
            $wallet = Wallet::where('id', $data['wallet_id'])
                ->where('user_id', $userId)
                ->first();
            if (! $wallet) {
                return response()->json([
                    'success' => false,
                    'error' => 'Wallet tidak ditemukan atau bukan milik Anda.',
                ], 403);
            }
        }

        $transaction->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diupdate',
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', session('user_id'))
            ->firstOrFail();

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }

    public function getMonthly(Request $request)
    {
        $userId = session('user_id');
        $year = $request->query('year', date('Y'));
        $walletId = $request->query('wallet_id');

        $query = Transaction::where('user_id', $userId)
            ->whereRaw($this->dateFormatYear('transaction_date').' = ?', [$year]);

        if ($walletId) {
            $query->where('wallet_id', $walletId);
        }

        $monthly = (clone $query)
            ->selectRaw('
                '.$this->dateFormatYearMonth('transaction_date')." as month,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'monthly' => $monthly,
        ]);
    }

    public function getBalance(Request $request)
    {
        $userId = session('user_id');
        $walletId = $request->query('wallet_id');

        $query = Transaction::where('user_id', $userId);

        if ($walletId) {
            $query->where('wallet_id', $walletId);
        }

        $result = (clone $query)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
            ")
            ->first();

        $balance = $result->total_income - $result->total_expense;

        return response()->json([
            'success' => true,
            'total_income' => $result->total_income,
            'total_expense' => $result->total_expense,
            'balance' => $balance,
        ]);
    }
}
