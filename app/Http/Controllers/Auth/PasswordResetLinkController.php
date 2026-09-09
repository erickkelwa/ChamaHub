<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Log::info('Password reset requested for: ' . $request->email);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            Log::info('Password reset status: ' . $status);

            if ($status == Password::RESET_LINK_SENT) {
                return back()->with('status', 'We have emailed your password reset link! Please check your inbox (and spam folder).');
            }

            // Map error status to user-friendly messages
            $errorMessage = match($status) {
                Password::INVALID_USER  => 'We could not find a user with that email address. Please check and try again.',
                Password::RESET_THROTTLED => 'Please wait a moment before requesting another reset link.',
                default => 'Something went wrong. Please try again.',
            };

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $errorMessage]);

        } catch (\Exception $e) {
            Log::error('Password reset exception: ' . $e->getMessage());
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Failed to send reset email. Please contact support or try again later.']);
        }
    }
}
