<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppGatewayService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected ?FirebaseService $firebase;

    public function __construct(?string $apiKey = null, ?string $baseUrl = null, ?FirebaseService $firebase = null)
    {
        $this->firebase = $firebase ?? app(FirebaseService::class);

        // Fetch dynamic credentials from Firebase system_settings if available
        $dynamicSettings = [];
        try {
            if ($this->firebase) {
                $dynamicSettings = $this->firebase->getData('system_settings') ?? [];
            }
        } catch (Throwable $e) {
            // Fallback gracefully if Firebase isn't reachable
        }

        $defaultBaseUrl = config('services.whatsapp.base_url', env('WHATSAPP_GATEWAY_URL', 'https://sachalabdullah.shop'));
        $defaultApiKey  = config('services.whatsapp.api_key', env('WHATSAPP_API_KEY', ''));

        $this->baseUrl = rtrim($baseUrl ?: ($dynamicSettings['whatsapp_gateway_url'] ?? $defaultBaseUrl), '/');
        $this->apiKey  = $apiKey ?: ($dynamicSettings['whatsapp_api_key'] ?? $defaultApiKey);
    }

    /**
     * Get Base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get API Key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Check if API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty(trim($this->apiKey));
    }

    /**
     * Format phone number to international standard (digits only without leading + or 00).
     * Automatically converts UK 07... numbers to 447...
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except leading plus if any
        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));
        $cleaned = ltrim($cleaned, '+');

        // Remove double zero prefix if present (e.g. 0044 -> 44)
        if (str_starts_with($cleaned, '00')) {
            $cleaned = substr($cleaned, 2);
        }

        // Convert UK standard format (07... or 01... or 02...) to 44...
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '44' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Get Connection Status and QR code if not connected
     * 
     * @return array{connected: bool, status: string, qr: string|null, phone: string|null, tenant: array|null, message?: string}
     */
    public function getStatus(): array
    {
        if (!$this->isConfigured()) {
            return [
                'connected' => false,
                'status'    => 'NOT_CONFIGURED',
                'qr'        => null,
                'phone'     => null,
                'tenant'    => null,
                'message'   => 'WhatsApp API Key is not configured. Please enter your API Key in Settings.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json'
            ])->timeout(12)->get("{$this->baseUrl}/api/v1/status");

            if ($response->successful()) {
                $data = $response->json();
                $tenant = $data['tenant'] ?? null;
                $phone = $tenant['phone'] ?? null;
                $connected = (bool) ($data['connected'] ?? false);
                $status = $data['status'] ?? ($connected ? 'CONNECTED' : 'DISCONNECTED');

                return [
                    'connected' => $connected,
                    'status'    => $status,
                    'qr'        => $data['qr'] ?? null,
                    'phone'     => $phone,
                    'tenant'    => $tenant,
                    'connect_url' => $data['connect_url'] ?? null
                ];
            }

            $errorData = $response->json();
            return [
                'connected' => false,
                'status'    => 'ERROR',
                'qr'        => null,
                'phone'     => null,
                'tenant'    => null,
                'message'   => $errorData['message'] ?? 'Gateway returned error: HTTP ' . $response->status()
            ];
        } catch (Throwable $e) {
            Log::error('WhatsApp Status Check Error: ' . $e->getMessage());
            return [
                'connected' => false,
                'status'    => 'UNREACHABLE',
                'qr'        => null,
                'phone'     => null,
                'tenant'    => null,
                'message'   => 'Could not reach WhatsApp Gateway (' . $e->getMessage() . ')'
            ];
        }
    }

    /**
     * Check if WhatsApp line is active & ready
     */
    public function isConnected(): bool
    {
        return $this->getStatus()['connected'];
    }

    /**
     * Send Simple Text Message
     * 
     * @return array{success: bool, message: string, details?: array}
     */
    public function sendTextMessage(string $phone, string $message): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WhatsApp Gateway API key is not configured.'
            ];
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'message' => 'Invalid phone number provided.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json'
            ])
            ->timeout(30)
            ->post("{$this->baseUrl}/api/v1/send", [
                'phone'   => $formattedPhone,
                'message' => $message
            ]);

            $data = $response->json() ?? [];

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'message' => $data['message'] ?? 'WhatsApp message sent successfully.',
                    'details' => $data
                ];
            }

            Log::warning('WhatsApp Send Text rejected', [
                'phone'    => $formattedPhone,
                'status'   => $response->status(),
                'response' => $data
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to send WhatsApp message.',
                'details' => $data
            ];
        } catch (Throwable $e) {
            Log::error('WhatsApp Send Text Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'WhatsApp Gateway connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send In-Memory PDF (e.g. from DomPDF) via Base64 JSON
     * 
     * @return array{success: bool, message: string, details?: array}
     */
    public function sendPdfBinary(string $phone, string $rawPdfContent, string $fileName = 'statement.pdf', ?string $caption = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WhatsApp Gateway API key is not configured.'
            ];
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'message' => 'Invalid phone number provided.'
            ];
        }

        try {
            $payload = [
                'phone'        => $formattedPhone,
                'media_base64' => base64_encode($rawPdfContent),
                'mimetype'     => 'application/pdf',
                'filename'     => $fileName
            ];

            if (!empty($caption)) {
                $payload['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json'
            ])
            ->timeout(60)
            ->post("{$this->baseUrl}/api/v1/send", $payload);

            $data = $response->json() ?? [];

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'message' => $data['message'] ?? 'WhatsApp PDF sent successfully.',
                    'details' => $data
                ];
            }

            Log::warning('WhatsApp Send PDF Binary rejected', [
                'phone'    => $formattedPhone,
                'status'   => $response->status(),
                'response' => $data
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to send WhatsApp PDF document.',
                'details' => $data
            ];
        } catch (Throwable $e) {
            Log::error('WhatsApp Send PDF Binary Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'WhatsApp PDF sending failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send PDF Document via Local File Path (Multipart Form-Data)
     */
    public function sendPdfFile(string $phone, string $filePath, ?string $caption = null, ?string $fileName = null): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'PDF file does not exist on disk.'];
        }

        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'WhatsApp Gateway API key is not configured.'];
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        $fileName = $fileName ?: basename($filePath);

        try {
            $data = ['phone' => $formattedPhone];
            if ($caption) $data['caption'] = $caption;
            if ($fileName) $data['filename'] = $fileName;

            $fileResource = fopen($filePath, 'r');

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json'
            ])
            ->attach('file', $fileResource, $fileName)
            ->timeout(60)
            ->post("{$this->baseUrl}/api/v1/send", $data);

            $result = $response->json() ?? [];

            if ($response->successful() && ($result['success'] ?? false)) {
                return [
                    'success' => true,
                    'message' => $result['message'] ?? 'WhatsApp PDF sent successfully.',
                    'details' => $result
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send WhatsApp PDF file.',
                'details' => $result
            ];
        } catch (Throwable $e) {
            Log::error('WhatsApp Send PDF File Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
