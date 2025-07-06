<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use App\Models\PartnerTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FlutterwaveService;
use App\Models\PartnerTierPayment;

class BillingController extends Controller
{
    protected function getCurrentPartner(Request $request)
    {
        return $request->attributes->get('business_partner');
    }

    public function index(Request $request)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }
        
        $tiers = Tier::with('inspectionPackages')->get();
        $activeTier = PartnerTier::where('business_partner_id', $partner->id)
            ->where('status', 'active')
            ->with('tier')
            ->latest('started_at')
            ->first();
        $remainingRequests = null;
        if ($activeTier) {
            $quota = $activeTier->tier->request_quota;
            $used = $partner->inspectionRequests()
                ->whereMonth('created_at', now()->month)
                ->count();
            $remainingRequests = max(0, $quota - $used);
        }
        // Fetch paid invoices
        $paidInvoices = \App\Models\PartnerTierPayment::whereHas('partnerTier', function ($q) use ($partner) {
            $q->where('business_partner_id', $partner->id);
        })
        ->where('status', 'paid')
        ->with(['partnerTier.tier'])
        ->latest('paid_at')
        ->get();
        // Fetch pending invoices
        $pendingInvoices = \App\Models\PartnerTierPayment::whereHas('partnerTier', function ($q) use ($partner) {
            $q->where('business_partner_id', $partner->id);
        })
        ->where('status', 'pending')
        ->with(['partnerTier.tier'])
        ->latest('created_at')
        ->get();
        return view('business-partner.billing', compact('tiers', 'activeTier', 'remainingRequests', 'paidInvoices', 'pendingInvoices'));
    }

    public function selectTier(Request $request, Tier $tier)
    {
        $partner = $this->getCurrentPartner($request);
        if (!$partner) {
            return redirect()->route('partner.dashboard')->with('error', 'No partner found.');
        }

        // Check if already has an active tier
        $activeTier = \App\Models\PartnerTier::where('business_partner_id', $partner->id)
            ->where('status', 'active')
            ->where('tier_id', $tier->id)
            ->first();
        if ($activeTier) {
            return back()->with('info', 'You already have this tier active.');
        }

        // Create new PartnerTier (pending)
        $partnerTier = \App\Models\PartnerTier::create([
            'business_partner_id' => $partner->id,
            'tier_id' => $tier->id,
            'started_at' => now(),
            'expires_at' => now()->addMonth(), // or based on tier duration
            'status' => 'pending',
        ]);

        // Create PartnerTierPayment (pending)
        $payment = PartnerTierPayment::create([
            'partner_tier_id' => $partnerTier->id,
            'amount' => $tier->price,
            'status' => 'pending',
        ]);

        // Prepare Flutterwave payment data
        $host = $request->getHost();
        $scheme = $request->getScheme();
        $port = $request->getPort();
        $redirectUrl = $scheme . '://' . $host;
        if ($port && $port != 80 && $port != 443) {
            $redirectUrl .= ':' . $port;
        }
        $redirectUrl .= '/billing/flutterwave/callback';
        $flutterwave = new FlutterwaveService();
        $paymentData = [
            'tx_ref' => 'PTIER-' . $payment->id . '-' . time(),
            'amount' => $tier->price,
            'currency' => 'RWF',
            'redirect_url' => $redirectUrl,
            'customer' => [
                'email' => $partner->email,
                'name' => $partner->name,
            ],
            'customizations' => [
                'title' => 'Tier Subscription Payment',
                'description' => 'Payment for ' . $tier->name . ' tier',
            ],
        ];
        $response = $flutterwave->initializePayment($paymentData);
        if (isset($response['status']) && $response['status'] === 'success') {
            $paymentLink = $response['data']['link'] ?? null;
            if ($paymentLink) {
                // Optionally store tx_ref or payment reference
                $payment->update(['payment_method' => 'flutterwave', 'status' => 'pending']);
                return redirect($paymentLink);
            }
        }
        return back()->with('error', 'Failed to initiate payment. Please try again.');
    }

    public function flutterwaveCallback(Request $request)
    {
        $status = $request->query('status');
        $tx_ref = $request->query('tx_ref');
        $transaction_id = $request->query('transaction_id');

        if (!$tx_ref || !$transaction_id) {
            return redirect()->route('partner.billing')->with('error', 'Invalid payment callback.');
        }

        $payment = PartnerTierPayment::where('id', explode('-', $tx_ref)[1] ?? null)->first();
        if (!$payment) {
            return redirect()->route('partner.billing')->with('error', 'Payment record not found.');
        }

        $flutterwave = new FlutterwaveService();
        $verify = $flutterwave->verifyPayment($transaction_id);
        if (isset($verify['status']) && $verify['status'] === 'success' && $verify['data']['status'] === 'successful') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $partnerTier = $payment->partnerTier;
            $partnerTier->update(['status' => 'active', 'started_at' => now(), 'expires_at' => now()->addMonth()]);
            return redirect()->route('partner.billing')->with('success', 'Payment successful! Your tier is now active.');
        } else {
            $payment->update(['status' => 'failed']);
            return redirect()->route('partner.billing')->with('error', 'Payment failed or not completed.');
        }
    }
} 