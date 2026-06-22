<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    public function index()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        return view('dashboard.index');
    }

    public function tabungan()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        return view('dashboard.tabungan');
    }

    public function dompet()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        return view('dompet.index');
    }

    public function profile()
    {
        if (! session()->has('user_id')) {
            return redirect('/login');
        }

        return view('profile.index');
    }

    public function stats(Request $request)
    {
        $userId = session('user_id');

        // ── Wallets ────────────────────────────────
        $wallets = Wallet::where('user_id', $userId)->get();
        $mainWallet = $wallets->where('type', 'main')->first();
        $savingsWallet = $wallets->where('type', 'savings')->first();

        // ── All-time summary ───────────────────────
        $allSummary = Transaction::where('user_id', $userId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_transactions
            ")
            ->first();

        // ── This month summary ─────────────────────
        $thisMonth = date('Y-m');
        $thisMonthSummary = Transaction::where('user_id', $userId)
            ->whereRaw($this->dateFormatYearMonth('transaction_date').' = ?', [$thisMonth])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_transactions
            ")
            ->first();

        // ── Last month summary ─────────────────────
        $lastMonth = date('Y-m', strtotime('-1 month'));
        $lastMonthSummary = Transaction::where('user_id', $userId)
            ->whereRaw($this->dateFormatYearMonth('transaction_date').' = ?', [$lastMonth])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_transactions
            ")
            ->first();

        // ── Monthly trends (last 12 months) ─────────
        $startMonth = date('Y-m', strtotime('-11 months'));
        $monthlyTrends = Transaction::where('user_id', $userId)
            ->whereRaw($this->dateFormatYearMonth('transaction_date').' >= ?', [$startMonth])
            ->selectRaw('
                '.$this->dateFormatYearMonth('transaction_date')." as month,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
            ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Top expense categories ─────────────────
        $topExpenseCategories = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── Top income categories ──────────────────
        $topIncomeCategories = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── Recent transactions (last 10) ───────────
        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('wallet')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // ── Savings growth (monthly, for savings wallet) ─
        $savingsGrowth = [];
        if ($savingsWallet) {
            $savingsGrowth = Transaction::where('user_id', $userId)
                ->where('wallet_id', $savingsWallet->id)
                ->where('type', 'income')
                ->selectRaw('
                    '.$this->dateFormatYearMonth('transaction_date').' as month,
                    SUM(amount) as total
                ')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return response()->json([
            'success' => true,
            'wallets' => [
                'main' => $mainWallet ? ['id' => $mainWallet->id, 'balance' => $mainWallet->balance, 'name' => $mainWallet->name] : null,
                'savings' => $savingsWallet ? ['id' => $savingsWallet->id, 'balance' => $savingsWallet->balance, 'name' => $savingsWallet->name, 'savings_target' => (float) $savingsWallet->savings_target] : null,
            ],
            'summary' => [
                'all' => [
                    'total_income' => (float) $allSummary->total_income,
                    'total_expense' => (float) $allSummary->total_expense,
                    'balance' => (float) $allSummary->total_income - (float) $allSummary->total_expense,
                    'total_transactions' => (int) $allSummary->total_transactions,
                ],
                'this_month' => [
                    'month' => $thisMonth,
                    'total_income' => (float) $thisMonthSummary->total_income,
                    'total_expense' => (float) $thisMonthSummary->total_expense,
                    'balance' => (float) $thisMonthSummary->total_income - (float) $thisMonthSummary->total_expense,
                    'total_transactions' => (int) $thisMonthSummary->total_transactions,
                ],
                'last_month' => [
                    'month' => $lastMonth,
                    'total_income' => (float) $lastMonthSummary->total_income,
                    'total_expense' => (float) $lastMonthSummary->total_expense,
                    'balance' => (float) $lastMonthSummary->total_income - (float) $lastMonthSummary->total_expense,
                    'total_transactions' => (int) $lastMonthSummary->total_transactions,
                ],
            ],
            'monthly_trends' => $monthlyTrends->map(fn ($m) => [
                'month' => $m->month,
                'total_income' => (float) $m->total_income,
                'total_expense' => (float) $m->total_expense,
            ]),
            'top_expense_categories' => $topExpenseCategories->map(fn ($c) => [
                'category' => $c->category,
                'total' => (float) $c->total,
            ]),
            'top_income_categories' => $topIncomeCategories->map(fn ($c) => [
                'category' => $c->category,
                'total' => (float) $c->total,
            ]),
            'recent_transactions' => $recentTransactions->map(fn ($t) => [
                'id' => $t->id,
                'transaction_date' => $t->transaction_date,
                'type' => $t->type,
                'category' => $t->category,
                'amount' => (float) $t->amount,
                'note' => $t->note,
                'wallet' => $t->wallet ? ['name' => $t->wallet->name, 'type' => $t->wallet->type] : null,
            ]),
            'savings_growth' => $savingsGrowth->map(fn ($s) => [
                'month' => $s->month,
                'total' => (float) $s->total,
            ]),
        ]);
    }
}
