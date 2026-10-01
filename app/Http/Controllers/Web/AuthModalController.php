<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthModalController extends Controller
{
    public function handleEmail(Request $request)
    {
        return redirect()->route('login');
    }

    public function handleGoogle(Request $request)
    {
        return redirect()->route('auth.social.redirect', ['provider' => 'google']);
    }

    public function handleSocial(Request $request, string $provider)
    {
        return redirect()->route('auth.social.redirect', ['provider' => $provider]);
    }

    public function logout(Request $request)
    {
        return redirect()->route('logout');
    }
}

