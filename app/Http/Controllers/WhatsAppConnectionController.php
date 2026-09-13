<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\WhatsAppGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppConnectionController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Get live connection status & QR code as JSON for real-time polling
     */
    public function getStatus(WhatsAppGatewayService $wa)
    {
        $status = $wa->getStatus();
        return response()->json($status);
    }

    /**
     * Save WhatsApp Gateway URL and API Key into Firebase system_settings (and .env)
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'whatsapp_gateway_url' => 'nullable|url',
            'whatsapp_api_key'     => 'required|string|max:255',
        ]);

        $gatewayUrl = rtrim($request->input('whatsapp_gateway_url', 'https://sachalabdullah.shop'), '/');
        $apiKey = trim($request->input('whatsapp_api_key'));

        try {
            // Save to Firebase system_settings
            $ref = $this->firebase->getDatabase()->getReference('system_settings');
            $ref->update([
                'whatsapp_gateway_url' => $gatewayUrl,
                'whatsapp_api_key'     => $apiKey,
                'whatsapp_updated_at'  => now()->toDateTimeString(),
            ]);

            // Save to .env as well if writable
            $this->saveToEnv([
                'WHATSAPP_GATEWAY_URL' => $gatewayUrl,
                'WHATSAPP_API_KEY'     => $apiKey,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp connection settings saved successfully!'
            ]);
        } catch (Throwable $e) {
            Log::error('Error saving WhatsApp settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a test WhatsApp message
     */
    public function sendTestMessage(Request $request, WhatsAppGatewayService $wa)
    {
        $request->validate([
            'phone'   => 'required|string|max:30',
            'message' => 'nullable|string|max:500',
        ]);

        $phone = $request->input('phone');
        $message = $request->input('message') ?: 'Hello from Crown Carz! Your WhatsApp Gateway connection is active and working perfectly.';

        $result = $wa->sendTextMessage($phone, $message);

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    /**
     * Helper to write keys to .env
     */
    private function saveToEnv(array $data): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) {
            return;
        }

        try {
            $contents = file_get_contents($path);

            foreach ($data as $key => $value) {
                $escapedValue = preg_quote($key, '/');
                if (preg_match("/^{$escapedValue}=.*/m", $contents)) {
                    $contents = preg_replace("/^{$escapedValue}=.*/m", "{$key}=\"{$value}\"", $contents);
                } else {
                    $contents .= "\n{$key}=\"{$value}\"";
                }
            }

            file_put_contents($path, $contents);
        } catch (Throwable $e) {
            Log::warning('Could not update .env file: ' . $e->getMessage());
        }
    }
}
