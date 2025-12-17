<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            // Log Success
            LoginHistory::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
            ]);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (ValidationException $e) {
            // Log Failure
            // Determine if it was a lockout or just bad credentials
            $status = 'failed';
            if (str_contains(json_encode($e->errors()), 'throttle')) {
                $status = 'lockout';
            }

            // We might not have a user_id if login failed, but we can try to find user by email if we wanted to be invasive.
            // For now, tracking IP is key.
            LoginHistory::create([
                'user_id' => null, // Optional: look up user by email if needed, but privacy constraints usually apply.
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $status,
            ]);

            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
