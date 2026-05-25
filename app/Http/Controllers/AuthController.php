<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('logged_in')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate against system_users. Validation is delegated to LoginRequest
     * to match the rest of the modules (booking, logging, enrollment).
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        session([
            'logged_in'   => true,
            'user_id'     => $user->system_user_id,
            'user_name'   => trim($user->first_name . ' ' . $user->last_name),
            'user_email'  => $user->email,
            'user_avatar' => strtoupper(substr($user->first_name ?? '', 0, 1)
                                       . substr($user->last_name ?? '', 0, 1)),
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
