<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Username atau password salah']);
        }

        $request->session()->regenerate();
        session([
            'user_id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'role' => $user->role,
            'avatar' => $user->avatar ?? 0,
            'quote' => $user->quote ?? null,
        ]);

        return redirect()->intended($user->isAdmin() ? '/admin' : '/dashboard');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|min:3|max:50|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'user',
        ]);

        // Create default wallets
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

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function checkSession(Request $request)
    {
        if (! session()->has('user_id')) {
            return response()->json([
                'success' => false,
                'authenticated' => false,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => session('user_id'),
                'username' => session('username'),
                'full_name' => session('full_name'),
                'role' => session('role'),
                'avatar' => session('avatar', 0),
                'quote' => session('quote'),
            ],
        ]);
    }
}
