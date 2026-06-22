<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'full_name',
        'password',
        'role',
        'avatar',
        'quote',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getAvatarStyleAttribute()
    {
        $avatars = [
            ['icon' => 'fa-user', 'bg' => '#3b82f6', 'label' => 'Default'],
            ['icon' => 'fa-user-tie', 'bg' => '#d4940a', 'label' => 'Profesional'],
            ['icon' => 'fa-user-astronaut', 'bg' => '#8b5cf6', 'label' => 'Petualang'],
            ['icon' => 'fa-user-ninja', 'bg' => '#1e293b', 'label' => 'Ninja'],
            ['icon' => 'fa-user-graduate', 'bg' => '#10b981', 'label' => 'Pelajar'],
            ['icon' => 'fa-user-md', 'bg' => '#ef4444', 'label' => 'Dokter'],
            ['icon' => 'fa-user-gear', 'bg' => '#f59e0b', 'label' => 'Teknisi'],
            ['icon' => 'fa-user-secret', 'bg' => '#6b7280', 'label' => 'Agen'],
            ['icon' => 'fa-user', 'bg' => '#ec4899', 'label' => 'Cantik'],
            ['icon' => 'fa-user', 'bg' => '#06b6d4', 'label' => 'Santai'],
            ['icon' => 'fa-user', 'bg' => '#e11d48', 'label' => 'Ganteng'],
            /* ── Hewan ──── */
            ['icon' => 'fa-cat', 'bg' => '#f97316', 'label' => 'Kucing'],
            ['icon' => 'fa-dog', 'bg' => '#d97706', 'label' => 'Anjing'],
            ['icon' => 'fa-fish', 'bg' => '#0ea5e9', 'label' => 'Ikan'],
            ['icon' => 'fa-horse', 'bg' => '#78350f', 'label' => 'Kuda'],
            ['icon' => 'fa-frog', 'bg' => '#22c55e', 'label' => 'Katak'],
            ['icon' => 'fa-crow', 'bg' => '#6b21a8', 'label' => 'Gagak'],
            ['icon' => 'fa-dragon', 'bg' => '#65a30d', 'label' => 'Buaya'],
            ['icon' => 'fa-worm', 'bg' => '#059669', 'label' => 'Ular'],
        ];
        $index = $this->avatar ?? 0;

        return $avatars[$index] ?? $avatars[0];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getBalanceAttribute()
    {
        return $this->transactions()
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) - 
                 COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as balance"
            )
            ->value('balance');
    }
}
