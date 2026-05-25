<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('logged_in')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Demo: accept any credentials
        session([
            'logged_in' => true,
            'role' => 'admin',
            'user_name' => 'Administrator',
            'user_email' => 'admin@usep.edu.ph',
            'user_avatar' => 'AD',
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()->route('login');
    }

    public function switchRole(Request $request)
    {
        $newRole = session('role') === 'admin' ? 'instructor' : 'admin';

        session([
            'role' => $newRole,
            'user_name' => $newRole === 'admin' ? 'Administrator' : 'Prof. Juan Reyes',
            'user_email' => $newRole === 'admin' ? 'admin@usep.edu.ph' : 'jreyes@usep.edu.ph',
            'user_avatar' => $newRole === 'admin' ? 'AD' : 'JR',
        ]);

        return redirect()->route('dashboard');
    }
}