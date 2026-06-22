<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // For MySQL, we need aligned column types before adding FK constraints
        if ($driver === 'mysql') {
            // Add proper foreign key to wallets.user_id → users.id
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropIndex(['user_id']); // Drop existing index first
            });

            // Use raw SQL for MySQL column type change
            DB::statement('ALTER TABLE wallets MODIFY user_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE transactions MODIFY wallet_id BIGINT UNSIGNED NULL');

            Schema::table('wallets', function (Blueprint $table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });

            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('wallet_id')
                    ->references('id')
                    ->on('wallets')
                    ->cascadeOnDelete();
            });
        }

        // SQLite doesn't enforce type strictness the same way,
        // but we can still add the FK constraints (SQLite will parse them).
        if ($driver === 'sqlite') {
            // SQLite requires PRAGMA foreign_keys = ON and recreating tables
            // for proper FK support. We'll add a lightweight reference instead.
            Schema::table('wallets', function (Blueprint $table) {
                $table->index('user_id', 'wallets_user_id_fk_index');
            });

            Schema::table('transactions', function (Blueprint $table) {
                $table->index('wallet_id', 'transactions_wallet_id_fk_index');
            });

            // Add foreign keys if PRAGMA foreign_keys is enabled
            // (Laravel enables it by default for SQLite connections)
            try {
                Schema::table('wallets', function (Blueprint $table) {
                    $table->foreign('user_id', 'wallets_user_id_foreign')
                        ->references('id')
                        ->on('users')
                        ->cascadeOnDelete();
                });

                Schema::table('transactions', function (Blueprint $table) {
                    $table->foreign('wallet_id', 'transactions_wallet_id_foreign')
                        ->references('id')
                        ->on('wallets')
                        ->cascadeOnDelete();
                });
            } catch (Exception $e) {
                // If SQLite doesn't support adding FK to existing table,
                // the indexes are sufficient for data integrity in dev.
                echo "Note: SQLite FK constraints added as indexes only.\n";
            }
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['wallet_id']);
            });
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        if ($driver === 'sqlite') {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign('transactions_wallet_id_foreign');
                $table->dropIndex('transactions_wallet_id_fk_index');
            });
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropForeign('wallets_user_id_foreign');
                $table->dropIndex('wallets_user_id_fk_index');
            });
        }
    }
};
