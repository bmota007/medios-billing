<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelnyxService
{
    /**
     * Sends an SMS via Telnyx API
     * Hardened for Production - Medios Billing
     */
    public function sendSms($phone, $message)
    {
        // 🛠️ THE FIX: Smart Sanitizer
        // Strips all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Ensures the number starts with exactly one +1
        if (strlen($clean) == 10) {
            $phone = '+1' . $clean;
        } elseif (strlen($clean) == 11 && substr($clean, 0, 1) == '1') {
            $phone = '+' . $clean;
        } else {
            $phone = '+' . $clean; // International or already prefixed fallback
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('TELNYX_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.telnyx.com/v2/messages', [
            'from' => env('TELNYX_FROM'),
            'to' => $phone,
            'text' => $message,
        ]);

        // 🔥 LOG EVERYTHING FOR TROUBLESHOOTING
        Log::info('TELNYX SMS DISPATCH', [
            'to' => $phone,
            'message' => $message,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return $response;
    }
}
