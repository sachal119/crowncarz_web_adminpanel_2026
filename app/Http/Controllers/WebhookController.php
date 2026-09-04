<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleCall(Request $request)
    {
        // Optional: Auth Token verification
        $authHeader = $request->header('Authorization');
        $webhookToken = "1bc1253a65714a20bcd70d37eb5efcae"; // YOUR TOKEN HERE

        if ($authHeader !== $webhookToken) {
            Log::error('Invalid Webhook Token');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Log incoming data
        Log::info("New Call Notification", $request->all());

        // Extract needed data
        $uuid       = $request->input('uuid');
        $callType   = $request->input('call_type');
        $from       = $request->input('from');
        $to         = $request->input('to');
        $start      = $request->input('start');

        // Example: Save data to database
        // CallLog::create([
        //    'uuid' => $uuid,
        //    'call_type' => $callType,
        //    'from_number' => $from,
        //    'to_number' => $to,
        //    'start_time' => $start,
        // ]);

        return response()->json(['status' => 'received'], 200);
    }
}
