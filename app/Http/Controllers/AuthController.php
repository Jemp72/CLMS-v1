<?php

namespace App\Http\Controllers;

use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $user = SystemUser::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        session([
            'logged_in' => true,
            'role' => $user->role_type,
            'user_name' => trim($user->first_name . ' ' . $user->last_name),
            'user_email' => $user->email,
            'user_avatar' => strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)),
        ]);

        return redirect()->route('dashboard');
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