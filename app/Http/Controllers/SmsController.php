<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('📩 SMS WEBHOOK RECEIVED', $request->all());

        $from = $request->input('data.payload.from.phone_number') ?? 'unknown';
        $text = $request->input('data.payload.text') ?? 'no message';

        Log::info("Incoming SMS from {$from}: {$text}");

        return response()->json(['status' => 'ok']);
    }
}
