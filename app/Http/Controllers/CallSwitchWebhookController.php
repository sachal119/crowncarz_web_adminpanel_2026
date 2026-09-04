<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CallSwitchWebhookController extends Controller
{
    private const LATEST_CALL_CACHE_KEY = 'callswitch.latest_call';

    public function receive(Request $request): JsonResponse
    {
        $expectedToken = (string) config(
            'services.callswitch.webhook_token'
        );

        if ($expectedToken === '') {
            Log::critical(
                'CallSwitch webhook token is not configured.'
            );

            return response()->json([
                'message' => 'Webhook is not configured.',
            ], 503);
        }

        if (! $this->hasValidToken($request, $expectedToken)) {
            $receivedToken = $this->getReceivedToken($request);

            Log::warning(
                'Rejected CallSwitch webhook request.',
                [
                    'ip' => $request->ip(),
                    'token_received' => $receivedToken !== '',
                    'received_token_length' => strlen(
                        $receivedToken
                    ),
                ]
            );

            /*
             * Temporary debug information.
             *
             * The actual expected or received token is intentionally
             * not returned. Fingerprints can be compared safely.
             */
            return response()->json([
                'message' => 'Unauthorized.',
                'debug' => [
                    'token_received' => $receivedToken !== '',

                    'expected_length' => strlen(
                        $expectedToken
                    ),

                    'received_length' => strlen(
                        $receivedToken
                    ),

                    'expected_fingerprint' => substr(
                        hash('sha256', $expectedToken),
                        0,
                        12
                    ),

                    'received_fingerprint' =>
                        $receivedToken === ''
                            ? null
                            : substr(
                                hash(
                                    'sha256',
                                    $receivedToken
                                ),
                                0,
                                12
                            ),

                    'matches' =>
                        $receivedToken !== ''
                        && hash_equals(
                            $expectedToken,
                            $receivedToken
                        ),
                ],
            ], 401);
        }

        $payload = $request->validate([
            'uuid' => [
                'required',
                'string',
                'max:128',
            ],

            'call_type' => [
                'required',
                'string',
                'max:30',
            ],

            'from_type' => [
                'nullable',
                'string',
                'max:30',
            ],

            'from' => [
                'required',
                'string',
                'max:50',
            ],

            'to_type' => [
                'nullable',
                'string',
                'max:30',
            ],

            'to' => [
                'required',
                'string',
                'max:50',
            ],

            'start' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $call = [
            'id' => $payload['uuid']
                .'|'
                .$payload['start'],

            'uuid' => $payload['uuid'],

            'call_type' => strtolower(
                $payload['call_type']
            ),

            'from_type' =>
                $payload['from_type'] ?? null,

            'from' => $payload['from'],

            'to_type' =>
                $payload['to_type'] ?? null,

            'to' => $payload['to'],

            'start' => $payload['start'],

            'received_at' =>
                now()->toIso8601String(),
        ];

        Cache::put(
            self::LATEST_CALL_CACHE_KEY,
            $call,
            now()->addMinutes(10)
        );

        Log::info(
            'CallSwitch call notification received.',
            [
                'uuid' => $call['uuid'],
                'call_type' => $call['call_type'],
            ]
        );

        return response()->json([
            'status' => 'received',
            'id' => $call['id'],
        ]);
    }

    public function latest(): JsonResponse
    {
        return response()->json([
            'call' => Cache::get(
                self::LATEST_CALL_CACHE_KEY
            ),
        ]);
    }

    private function hasValidToken(
        Request $request,
        string $expectedToken
    ): bool {
        $providedTokens = [
            $request->bearerToken(),
            $request->header('Authorization'),
            $request->header('Auth-Token'),
            $request->header('X-Auth-Token'),
        ];

        foreach ($providedTokens as $providedToken) {
            if (
                is_string($providedToken)
                && hash_equals(
                    $expectedToken,
                    $providedToken
                )
            ) {
                return true;
            }
        }

        return false;
    }

    private function getReceivedToken(
        Request $request
    ): string {
        $bearerToken = $request->bearerToken();

        if (
            is_string($bearerToken)
            && $bearerToken !== ''
        ) {
            return $bearerToken;
        }

        $headerNames = [
            'Authorization',
            'Auth-Token',
            'X-Auth-Token',
        ];

        foreach ($headerNames as $headerName) {
            $token = $request->header($headerName);

            if (
                is_string($token)
                && $token !== ''
            ) {
                return $token;
            }
        }

        return '';
    }
}