<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $partner = $request->attributes->get('business_partner');
        
        \Log::info('LoginController showLoginForm', [
            'partner_from_request' => $partner ? $partner->id : null,
            'partner_from_session' => session('current_business_partner'),
            'request_attributes' => $request->attributes->all()
        ]);
        
        return view('partner.login', compact('partner'));
    }

    public function login(Request $request)
    {
        $partner = $request->attributes->get('business_partner');
        
        \Log::info('LoginController login', [
            'partner_from_request' => $partner ? $partner->id : null,
            'partner_from_session' => session('current_business_partner'),
            'request_attributes' => $request->attributes->all()
        ]);
        
        if (!$partner) {
            \Log::error('Partner is null in login method');
            return back()->withErrors(['email' => 'Partner not found.']);
        }
        
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Check if user belongs to this partner
            if ($user->businessPartners()->where('business_partner_id', $partner->id)->exists()) {
                return redirect()->route('partner.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'You do not belong to this partner.']);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('partner.login');
    }
} 