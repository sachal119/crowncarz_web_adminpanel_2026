<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MySmsService
{
    /**
     * Send an SMS and return a normalized result for every caller.
     *
     * @return array{success: bool, message: string, details?: array}
     */
    public function send(string $mobile, string $message): array
    {
        $apiKey = config('services.mysms.api_key');
        $authToken = config('services.mysms.auth_token');

        if (blank($apiKey) || blank($authToken)) {
            Log::error('MySMS credentials are not configured.');

            return [
                'success' => false,
                'message' => 'SMS service is not configured.',
            ];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(20)
                ->post('https://api.mysms.com/json/remote/sms/send', [
                    'apiKey' => $apiKey,
                    'authToken' => $authToken,
                    'recipients' => [$mobile],
                    'message' => $message,
                    'store' => true,
                ]);

            $data = $response->json();
            $data = is_array($data) ? $data : [];

            $successFlag = filter_var($data['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $successCode = array_key_exists('errorCode', $data)
                && (int) $data['errorCode'] === 0;

            if ($response->successful() && ($successFlag || $successCode)) {
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully.',
                    'details' => $data,
                ];
            }

            Log::warning('MySMS rejected an SMS request.', [
                'mobile' => $mobile,
                'http_status' => $response->status(),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'SMS sending failed.',
                'details' => $data,
            ];
        } catch (Throwable $exception) {
            Log::error('MySMS request failed.', [
                'mobile' => $mobile,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Could not connect to the SMS service.',
            ];
        }
    }
}
