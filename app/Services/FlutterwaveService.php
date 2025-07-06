<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FlutterwaveService
{
    protected $baseUrl = 'https://api.flutterwave.com/v3';

    public function initializePayment($data)
    {
        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->post($this->baseUrl . '/payments', $data);
        return $response->json();
    }

    public function verifyPayment($transactionId)
    {
        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->get($this->baseUrl . '/transactions/' . $transactionId . '/verify');
        return $response->json();
    }
} 