<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        $wallets = Wallet::where('user_id', $userId)->get();

        $result = $wallets->map(function ($wallet) {
            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'type' => $wallet->type,
                'icon' => $wallet->icon,
                'color' => $wallet->color,
                'balance' => $wallet->balance,
                'savings_target' => (float) $wallet->savings_target,
            ];
        });

        return response()->json([
            'success' => true,
            'wallets' => $result,
        ]);
    }

    public function transfer(Request $request)
    {
        $userId = session('user_id');

        $data = $request->validate([
            'from_wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id|different:from_wallet_id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        // Verify both wallets belong to user
        $fromWallet = Wallet::where('id', $data['from_wallet_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        $toWallet = Wallet::where('id', $data['to_wallet_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        // Check sufficient balance
        $fromBalance = $fromWallet->balance;
        if ($fromBalance < $data['amount']) {
            return response()->json([
                'success' => false,
                'error' => 'Saldo tidak mencukupi di wallet asal.',
            ], 400);
        }

        $today = now()->toDateString();
        $note = $data['note'] ?? 'Transfer antar wallet';

        // Create expense transaction for source wallet
        Transaction::create([
            'user_id' => $userId,
            'wallet_id' => $fromWallet->id,
            'transaction_date' => $today,
            'type' => 'expense',
            'category' => 'Transfer ke '.$toWallet->name,
            'amount' => $data['amount'],
            'note' => 'Transfer ke '.$toWallet->name.' — '.$note,
        ]);

        // Create income transaction for destination wallet
        Transaction::create([
            'user_id' => $userId,
            'wallet_id' => $toWallet->id,
            'transaction_date' => $today,
            'type' => 'income',
            'category' => 'Transfer dari '.$fromWallet->name,
            'amount' => $data['amount'],
            'note' => 'Transfer dari '.$fromWallet->name.' — '.$note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transfer berhasil!',
        ]);
    }

    public function savingsTarget()
    {
        $userId = session('user_id');

        $savingsWallet = Wallet::where('user_id', $userId)
            ->where('type', 'savings')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'savings_target' => (float) $savingsWallet->savings_target,
        ]);
    }

    public function updateSavingsTarget(Request $request)
    {
        $userId = session('user_id');

        $data = $request->validate([
            'savings_target' => 'required|numeric|min:0|max:9999999999999',
        ]);

        $savingsWallet = Wallet::where('user_id', $userId)
            ->where('type', 'savings')
            ->firstOrFail();

        $savingsWallet->update([
            'savings_target' => (int) $data['savings_target'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Target tabungan berhasil diperbarui!',
            'savings_target' => (float) $savingsWallet->fresh()->savings_target,
        ]);
    }
}
