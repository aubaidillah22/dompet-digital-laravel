<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function index()
    {
        if (session()->has('user_id')) {
            $role = session('role');

            return redirect($role === 'admin' ? '/admin' : '/dashboard');
        }

        return redirect('/login');
    }
}
