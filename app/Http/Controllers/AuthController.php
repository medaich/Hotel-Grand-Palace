<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember_me');

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password'], 'is_active' => 1], $remember)) {
            $request->session()->regenerate();
            
            // Update last login
            auth()->user()->update(['last_login' => now()]);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials for user: ' . $credentials['username'],
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $token = md5($user->username . time());
            $user->update(['reset_token' => $token]);

            $resetUrl = route('password.reset', ['token' => $token, 'user' => $user->id]);
            
            return back()->with('success', "Reset link (for demo — would be emailed in production):<br><a href='{$resetUrl}'>{$resetUrl}</a>");
        }

        return back()->withErrors(['email' => 'No account found with email: ' . $request->email]);
    }

    public function showResetForm($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'user_id' => $request->query('user')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required'
        ]);

        $user = \App\Models\User::where('id', $request->user_id)
                                ->where('reset_token', $request->token)
                                ->first();

        if ($user) {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
                'reset_token' => null
            ]);
            return redirect()->route('login')->with('success', 'Password reset successful. You can now login.');
        }

        return back()->withErrors(['token' => 'Invalid or expired token.']);
    }
}
