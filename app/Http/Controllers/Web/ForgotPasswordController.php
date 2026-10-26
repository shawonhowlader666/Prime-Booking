<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

            // Dispatch transactional email via EmailTemplate engine
            try {
                $template = \App\Models\EmailTemplate::getTemplate('password_reset');
                if ($template && $template->is_active) {
                    $rendered = $template->render([
                        'user_name' => $user->name,
                        'reset_url' => $resetUrl,
                    ]);
                    \Illuminate\Support\Facades\Mail::html($rendered['body'], function ($m) use ($user, $rendered) {
                        $m->to($user->email, $user->name)->subject($rendered['subject']);
                    });
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Password reset mail dispatch notice: ' . $e->getMessage());
            }
        }

        return back()->with('status', 'If an account exists with this email address, password reset instructions have been dispatched.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'This password reset token has expired. Please request a new one.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User account not found.');
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        try {
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }
        } catch (\Throwable $e) {}

        return redirect()->route('signin')->with('success', 'Your password has been reset successfully! Please sign in with your new password.');
    }
}
