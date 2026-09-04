<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SMSController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'apiKey'   => 'required',
        'msisdn'   => 'required',
        'password' => 'required'
    ]);

    // Call MySMS API
    $response = Http::post("https://api.mysms.com/json/user/login", [
        'apiKey'   => $request->apiKey,
        'msisdn'   => $request->msisdn,
        'password' => $request->password
    ]);

    $data = $response->json();

    if (!isset($data["authToken"])) {
        return response()->json([
            'status' => 'error',
            'message' => 'Login failed',
            'details' => $data
        ], 400);
    }

    // Save token to .env
    $this->saveTokenToEnv($data["authToken"]);

    // Return JSON instead of `back()`
    return response()->json([
        'status' => 'success',
        'message' => 'Token saved to .env successfully.',
        'token' => $data["authToken"]
    ], 200);
}


public function sendSMS(Request $request)
{
    $request->validate([
        'mobile' => 'required',
        'message' => 'required'
    ]);

    $authToken = env('MY_SMS_AUTH_TOKEN');
    

    $response = Http::post('https://api.mysms.com/json/remote/sms/send', [
        'apiKey'=> "fes0Jtm5dUww0YyvQHnDsg",
        'authToken' => $authToken,
        'recipients' => [$request->mobile],
        'message' => $request->message,
        'store' => true
    ]);

    $data = $response->json();

    if (isset($data['success']) && $data['success']) {
        return response()->json(['success' => true]);
    }

    return response()->json([
        'success' => false,
        'message' => $data['message'] ?? 'SMS sending failed',
        'details' => $data
    ]);
}


    private function saveTokenToEnv($token)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            // Remove old value
            $contents = file_get_contents($path);

            $contents = preg_replace(
                '/MY_SMS_AUTH_TOKEN=.*/',
                'MY_SMS_AUTH_TOKEN=' . $token,
                $contents
            );

            // If key does not exist, append it
            if (!str_contains($contents, 'MY_SMS_AUTH_TOKEN=')) {
                $contents .= "\nMY_SMS_AUTH_TOKEN=$token\n";
            }

            file_put_contents($path, $contents);
        }
    }
}
