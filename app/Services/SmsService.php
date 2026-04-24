<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send($to, $message)
    {
        $apiKey = env('TELNYX_API_KEY');
        $from = env('TELNYX_FROM');

        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.telnyx.com/v2/messages', [
                'from' => $from,
                'to' => $to,
                'text' => $message,
            ]);

            Log::info('TELNYX RESPONSE:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('SMS ERROR: ' . $e->getMessage());
        }
    }
}
