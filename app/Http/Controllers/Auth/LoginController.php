<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        $mainDomains = ['localhost', '127.0.0.1', 'www', 'admin'];

        if (!in_array($subdomain, $mainDomains)) {
            // Business partner login
            if (Auth::guard('partner')->attempt($credentials, $remember)) {
                \Log::info('Business partner login successful', ['user_id' => Auth::guard('partner')->id(), 'email' => $request->email]);
                $request->session()->regenerate();
                return redirect()->route('partner.dashboard');
            }
        } else {
            // Main app login
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                $user = $request->user();
                if ($user->isHeadTechnician()) {
                    return redirect()->route('headtech.dashboard');
                }
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }
                if ($user->isInspector()) {
                    return redirect()->route('inspector.dashboard');
                }
                // Institutional Partner User: always redirect to correct dashboard with subdomain
                $mainDomain = 'fundi.info';
                if ($user->isInstitutionalUser() && $host !== $mainDomain && str_ends_with($host, '.' . $mainDomain)) {
                    return redirect()->route('institutional-partner.dashboard', ['subdomain' => $subdomain]);
                }
                // Fallback for all other users
                return redirect('/');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        $mainDomains = ['localhost', '127.0.0.1', 'www', 'admin'];

        if (!in_array($subdomain, $mainDomains)) {
            Auth::guard('partner')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (!in_array($subdomain, $mainDomains)) {
            return redirect()->route('partner.login');
        }
        return redirect('/');
    }
}