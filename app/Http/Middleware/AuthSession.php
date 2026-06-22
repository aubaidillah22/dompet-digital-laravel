<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('user_id')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Unauthorized. Please login first.'], 401);
            }

            return redirect('/login');
        }

        // Validate user still exists in database
        $user = User::find(session('user_id'));
        if (! $user) {
            session()->flush();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Session expired. User not found.'], 401);
            }

            return redirect('/login');
        }

        return $next($request);
    }
}
