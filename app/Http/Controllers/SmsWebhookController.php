<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('📩 SMS WEBHOOK RECEIVED', $request->all());

        // Telnyx sends different event types
        $data = $request->all();

        // DELIVERY STATUS
        if (isset($data['data']['event_type'])) {
            $event = $data['data']['event_type'];

            if ($event === 'message.sent') {
                Log::info('SMS Sent');
            }

            if ($event === 'message.delivered') {
                Log::info('SMS Delivered');
            }

            if ($event === 'message.failed') {
                Log::error('SMS Failed');
            }
        }

        // INCOMING MESSAGE (customer replies)
        if (isset($data['data']['payload']['text'])) {

            $text = $data['data']['payload']['text'];
            $from = $data['data']['payload']['from']['phone_number'];

            Log::info("Incoming SMS from {$from}: {$text}");

            // Example: STOP logic
            if (strtoupper($text) === 'STOP') {
                Log::info("Customer opted out: {$from}");
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
