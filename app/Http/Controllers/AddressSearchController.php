<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressSearchController extends Controller
{
    public function __invoke(Request $request, FirebaseService $firebase): JsonResponse
    {
        $validated = $request->validate(['query' => ['required', 'string', 'min:3', 'max:200']]);

        try {
            // Read the current setting using server credentials; never expose it to the browser.
            $key = $firebase->getData('system_settings/autocomplete_key');
            if (!is_string($key) || trim($key) === '') {
                return response()->json(['message' => 'Address search is not configured. Please check the autocomplete key in System Settings.'], 503);
            }

            $response = Http::acceptJson()->connectTimeout(5)->timeout(12)
                ->get('https://api.os.uk/search/places/v1/find', [
                    'query' => trim($validated['query']),
                    'key' => trim($key),
                    'output_srs' => 'EPSG:4326',
                    'maxresults' => 10,
                ]);

            if (!$response->successful()) {
                Log::warning('Address search provider rejected request', ['status' => $response->status()]);
                return response()->json(['message' => 'Address search is unavailable. Please check the autocomplete API key and service access.'], 502);
            }

            $results = $response->json('results', []);
            if (!is_array($results)) {
                return response()->json(['message' => 'Address search returned an invalid response. Please try again.'], 502);
            }

            return response()->json(['results' => $results]);
        } catch (\Throwable $exception) {
            // Exception messages can contain the provider URL and key.
            Log::warning('Address search unavailable', ['exception_type' => get_class($exception)]);
            return response()->json(['message' => 'Address search is temporarily unavailable. Please try again.'], 503);
        }
    }
}
