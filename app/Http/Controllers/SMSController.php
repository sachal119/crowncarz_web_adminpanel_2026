<?php

namespace App\Http\Controllers;

use App\Services\MySmsService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SMSController extends Controller
{
    public function previewData(Request $request)
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:200',
                'regex:~\A(?:drivers/[A-Za-z0-9_-]+|sms_templates/(?:driver_details|booking_confirmation|onroute|arrival|complete))\z~'],
        ]);

        try {
            $data = app(FirebaseService::class)->getData($validated['path']);
            // Return only fields needed by the preview, never the full driver record.
            if (is_array($data)) {
                $fields = str_starts_with($validated['path'], 'drivers/')
                    ? ['name', 'phone'] : ['message'];
                $data = array_intersect_key($data, array_flip($fields));
            } else {
                $data = null;
            }

            return response()->json(['data' => $data])->header('Cache-Control', 'no-store');
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to load SMS preview data. Please try again or contact your administrator.',
            ], 503);
        }
    }

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


public function sendSMS(Request $request, MySmsService $smsService)
{
    $request->validate([
        'mobile' => 'required|string|max:30',
        'message' => 'required|string|max:2000',
    ]);

    $result = $smsService->send(
        (string) $request->string('mobile')->trim(),
        (string) $request->string('message')
    );

    return response()->json($result, $result['success'] ? 200 : 502);
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
