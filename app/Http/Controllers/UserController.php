<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $userId = session('user_id');

        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|min:3|max:50|unique:users,username,'.$userId,
        ]);

        $user = User::findOrFail($userId);
        $user->update($data);

        session([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'avatar' => $user->avatar ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => [
                'id' => $userId,
                'full_name' => $data['full_name'],
                'username' => $data['username'],
            ],
        ]);
    }

    public function changePassword(Request $request)
    {
        $userId = session('user_id');

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($userId);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['error' => 'Password lama tidak sesuai'], 401);
        }

        $user->update(['password' => $data['new_password']]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $userId = session('user_id');

        $data = $request->validate([
            'avatar' => 'required|integer|min:0|max:18',
        ]);

        $user = User::findOrFail($userId);
        $user->update(['avatar' => $data['avatar']]);

        // Update session so avatar persists across pages
        session(['avatar' => $data['avatar']]);

        $style = $user->avatar_style;

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui',
            'avatar' => $data['avatar'],
            'style' => $style,
        ]);
    }

    public function updateQuote(Request $request)
    {
        $userId = session('user_id');

        $data = $request->validate([
            'quote' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($userId);
        $user->update(['quote' => $data['quote']]);

        session(['quote' => $data['quote']]);

        return response()->json([
            'success' => true,
            'message' => 'Quote berhasil diperbarui',
            'quote' => $data['quote'],
        ]);
    }
}
