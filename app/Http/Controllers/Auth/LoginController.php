<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $company = config('company');
        return view('pages.signin', compact('company'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_credential' => 'required|string',
            'password' => 'required|string',
        ]);

        $credential = trim($request->input('login_credential'));
        $password = $request->input('password');

        // Determine if login_credential is email or phone number
        $fieldType = filter_var($credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $credential, 'password' => $password], $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Role-Based Redirection (Admin / Vendor / User)
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome to Super Admin Control Panel!');
            } elseif ($user->role === 'vendor') {
                return redirect()->intended(route('vendor.dashboard'))->with('success', 'Welcome to Hotel Partner Portal!');
            }

            return redirect()->intended(route('profile'))->with('success', 'Signed in successfully!');
        }

        return back()->withErrors([
            'login_credential' => 'The provided credentials do not match our records.',
        ])->onlyInput('login_credential');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }
}
