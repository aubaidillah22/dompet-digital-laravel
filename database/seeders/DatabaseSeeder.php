<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ────────────────────────
        $admin = User::create([
            'username' => 'admin',
            'full_name' => 'Administrator',
            'password' => 'admin123',
            'role' => 'admin',
            'quote' => 'Kesuksesan adalah hasil dari kebiasaan kecil yang dilakukan setiap hari',
        ]);

        $adminMain = Wallet::create([
            'user_id' => $admin->id, 'name' => 'Dompet Utama', 'type' => 'main',
            'icon' => 'fa-wallet', 'color' => '#f0b429',
        ]);
        Wallet::create([
            'user_id' => $admin->id, 'name' => 'Tabungan', 'type' => 'savings',
            'icon' => 'fa-piggy-bank', 'color' => '#10b981',
        ]);

        // ── Regular users ────────────────
        $users = [
            ['username' => 'andi', 'full_name' => 'Andi Pratama', 'quote' => 'Menabung hari ini untuk masa depan yang lebih cerah'],
            ['username' => 'siti', 'full_name' => 'Siti Rahmawati', 'quote' => 'Rejeki tidak akan tertukar, usaha tidak akan mengkhianati'],
            ['username' => 'budi', 'full_name' => 'Budi Santoso', 'quote' => 'Investasi terbaik adalah investasi pada diri sendiri'],
        ];

        $userWallets = []; // [user_id => ['main' => id, 'savings' => id]]

        foreach ($users as $u) {
            $user = User::create([
                'username' => $u['username'], 'full_name' => $u['full_name'],
                'password' => 'user1234', 'role' => 'user',
                'quote' => $u['quote'] ?? null,
            ]);
            $main = Wallet::create([
                'user_id' => $user->id, 'name' => 'Dompet Utama', 'type' => 'main',
                'icon' => 'fa-wallet', 'color' => '#f0b429',
            ]);
            $savings = Wallet::create([
                'user_id' => $user->id, 'name' => 'Tabungan', 'type' => 'savings',
                'icon' => 'fa-piggy-bank', 'color' => '#10b981',
            ]);
            $userWallets[$user->id] = ['main' => $main->id, 'savings' => $savings->id];
        }

        // ── Transactions per user ────────
        // Each user gets transactions split across Main + Savings wallets,
        // plus an internal transfer to demonstrate the transfer feature.

        // Andi (user_id=2) — Mahasiswa aktif, kiriman rutin 1.5jt/bulan
        $tx = [
            // Dompet Utama — pemasukan & pengeluaran sehari-hari
            ['wallet' => 'main', 'date' => '2026-01-05', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 1500000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-01-10', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 450000,  'note' => 'Belanja bulanan'],
            ['wallet' => 'main', 'date' => '2026-01-15', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-01-20', 'type' => 'expense', 'cat' => 'Gaya Hidup',             'amount' => 150000,  'note' => 'Nongkrong'],
            // Transfer ke Tabungan (setelah kiriman Jan)
            ['wallet' => 'main', 'date' => '2026-01-07', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 300000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-01-07', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 300000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
            // Feb
            ['wallet' => 'main', 'date' => '2026-02-03', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 1500000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-02-10', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 480000,  'note' => 'Belanja bulanan'],
            ['wallet' => 'main', 'date' => '2026-02-15', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-02-22', 'type' => 'expense', 'cat' => 'Gaya Hidup',             'amount' => 80000,   'note' => 'Kuota internet'],
            // Transfer ke Tabungan (setelah kiriman Feb)
            ['wallet' => 'main', 'date' => '2026-02-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 350000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-02-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 350000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
            // Mar
            ['wallet' => 'main', 'date' => '2026-03-03', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 1500000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-03-10', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 460000,  'note' => 'Belanja bulanan'],
            ['wallet' => 'main', 'date' => '2026-03-15', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-03-20', 'type' => 'expense', 'cat' => 'Sosial & Hadiah',        'amount' => 100000,  'note' => 'Hadiah teman ulang tahun'],
            // Transfer ke Tabungan (setelah kiriman Mar)
            ['wallet' => 'main', 'date' => '2026-03-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 400000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-03-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 400000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
        ];

        // Siti (user_id=3) — Kiriman lebih besar 2jt/bulan
        $tx2 = [
            // Jan
            ['wallet' => 'main', 'date' => '2026-01-03', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 2000000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-01-08', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 350000,  'note' => 'Belanja'],
            ['wallet' => 'main', 'date' => '2026-01-15', 'type' => 'expense', 'cat' => 'Gaya Hidup',             'amount' => 200000,  'note' => 'Jajan & kopi'],
            // Transfer ke Tabungan
            ['wallet' => 'main', 'date' => '2026-01-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 500000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-01-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 500000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
            // Feb
            ['wallet' => 'main', 'date' => '2026-02-03', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 2000000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-02-08', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 400000,  'note' => 'Belanja'],
            ['wallet' => 'main', 'date' => '2026-02-15', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-02-20', 'type' => 'expense', 'cat' => 'Gaya Hidup',             'amount' => 120000,  'note' => 'Nonton bioskop'],
            // Transfer ke Tabungan
            ['wallet' => 'main', 'date' => '2026-02-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 500000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-02-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 500000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
            // Mar
            ['wallet' => 'main', 'date' => '2026-03-03', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 2000000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-03-08', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 380000,  'note' => 'Belanja'],
            ['wallet' => 'main', 'date' => '2026-03-15', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-03-18', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 500000,  'note' => 'Bayar hutang teman'],
            // Transfer ke Tabungan
            ['wallet' => 'main', 'date' => '2026-03-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 600000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-03-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 600000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
        ];

        // Budi (user_id=4) — Kiriman 1jt/bulan + jualan
        $tx3 = [
            // Jan
            ['wallet' => 'main', 'date' => '2026-01-02', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 1000000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-01-08', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 300000,  'note' => 'Belanja'],
            ['wallet' => 'main', 'date' => '2026-01-20', 'type' => 'expense', 'cat' => 'Gaya Hidup',             'amount' => 100000,  'note' => 'Kuota internet'],
            // Transfer ke Tabungan
            ['wallet' => 'main', 'date' => '2026-01-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 200000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-01-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 200000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
            // Feb
            ['wallet' => 'main', 'date' => '2026-02-02', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 1000000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-02-07', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 350000,  'note' => 'Belanja'],
            ['wallet' => 'main', 'date' => '2026-02-14', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-02-18', 'type' => 'expense', 'cat' => 'Gaya Hidup',             'amount' => 50000,   'note' => 'Jajan'],
            // Transfer ke Tabungan
            ['wallet' => 'main', 'date' => '2026-02-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 150000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-02-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 150000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
            // Mar
            ['wallet' => 'main', 'date' => '2026-03-02', 'type' => 'income',  'cat' => 'Kiriman & Hibah',      'amount' => 1000000, 'note' => 'Kiriman orang tua'],
            ['wallet' => 'main', 'date' => '2026-03-07', 'type' => 'expense', 'cat' => 'Kebutuhan Pokok',       'amount' => 320000,  'note' => 'Belanja'],
            ['wallet' => 'main', 'date' => '2026-03-14', 'type' => 'expense', 'cat' => 'Pendidikan & Pesantren', 'amount' => 300000,  'note' => 'SPP'],
            ['wallet' => 'main', 'date' => '2026-03-22', 'type' => 'income',  'cat' => 'Finansial & Hutang',     'amount' => 200000,  'note' => 'Hasil jualan online'],
            // Transfer ke Tabungan
            ['wallet' => 'main', 'date' => '2026-03-05', 'type' => 'expense', 'cat' => 'Finansial & Hutang',     'amount' => 200000,  'note' => 'Transfer ke Tabungan — Nabung'],
            ['wallet' => 'savings', 'date' => '2026-03-05', 'type' => 'income', 'cat' => 'Finansial & Hutang',  'amount' => 200000,  'note' => 'Transfer dari Dompet Utama — Nabung'],
        ];

        $allUserTx = [2 => $tx, 3 => $tx2, 4 => $tx3];

        foreach ($allUserTx as $userId => $transactions) {
            $wallets = $userWallets[$userId];
            foreach ($transactions as $t) {
                $walletId = $t['wallet'] === 'savings' ? $wallets['savings'] : $wallets['main'];
                Transaction::create([
                    'user_id' => $userId,
                    'wallet_id' => $walletId,
                    'transaction_date' => $t['date'],
                    'type' => $t['type'],
                    'category' => $t['cat'],
                    'amount' => $t['amount'],
                    'note' => $t['note'],
                ]);
            }
        }
    }
}
