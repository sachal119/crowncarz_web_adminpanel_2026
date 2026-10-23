<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class AdvertisementController extends Controller
{
    protected $firebase;
    protected $db;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->db = $firebase->getDatabase();
    }

    /**
     * Local storage file path used as a resilient fallback when Firebase credentials are dummy/offline.
     */
    protected function getLocalStoragePath(): string
    {
        return storage_path('app/advertisements_local.json');
    }

    /**
     * Read ads from local JSON storage.
     */
    protected function getLocalAds(): array
    {
        $path = $this->getLocalStoragePath();
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    /**
     * Write ads to local JSON storage.
     */
    protected function saveLocalAds(array $ads): void
    {
        $path = $this->getLocalStoragePath();
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, json_encode($ads, JSON_PRETTY_PRINT));
    }

    /**
     * Fetch all advertisements from Firebase with automatic local storage fallback.
     */
    protected function fetchAllAdsFromDb(): array
    {
        try {
            $rawAds = $this->db->getReference('advertisements')->getValue();
            if (is_array($rawAds) && count($rawAds) > 0) {
                // Mirror to local storage for caching/resilience
                $this->saveLocalAds($rawAds);
                return $rawAds;
            }
        } catch (\Exception $e) {
            \Log::warning('Firebase fetch failed, falling back to local JSON: ' . $e->getMessage());
        }

        $localAds = $this->getLocalAds();

        // If both are empty, auto-seed with 3 high quality sample ads
        if (empty($localAds)) {
            $defaultSamples = $this->getDefaultSampleAds();
            $this->saveLocalAds($defaultSamples);
            return $defaultSamples;
        }

        return $localAds;
    }

    /**
     * Store new ad in Firebase with local fallback.
     */
    protected function pushAdToDb(array $adData): string
    {
        try {
            $newRef = $this->db->getReference('advertisements')->push($adData);
            $key = $newRef->getKey();
            if ($key) {
                $local = $this->getLocalAds();
                $local[$key] = $adData;
                $this->saveLocalAds($local);
                return $key;
            }
        } catch (\Exception $e) {
            \Log::warning('Firebase push failed, saving to local fallback: ' . $e->getMessage());
        }

        $key = 'ad_' . uniqid() . '_' . time();
        $local = $this->getLocalAds();
        $local[$key] = $adData;
        $this->saveLocalAds($local);
        return $key;
    }

    /**
     * Update ad in Firebase with local fallback.
     */
    protected function updateAdInDb(string $id, array $adData): void
    {
        try {
            $this->db->getReference("advertisements/{$id}")->update($adData);
        } catch (\Exception $e) {
            \Log::warning('Firebase update failed, updating in local fallback: ' . $e->getMessage());
        }

        $local = $this->getLocalAds();
        if (isset($local[$id])) {
            $local[$id] = array_merge($local[$id], $adData);
        } else {
            $local[$id] = $adData;
        }
        $this->saveLocalAds($local);
    }

    /**
     * Delete ad from Firebase with local fallback.
     */
    protected function deleteAdFromDb(string $id): void
    {
        try {
            $this->db->getReference("advertisements/{$id}")->remove();
        } catch (\Exception $e) {
            \Log::warning('Firebase delete failed, deleting from local fallback: ' . $e->getMessage());
        }

        $local = $this->getLocalAds();
        unset($local[$id]);
        $this->saveLocalAds($local);
    }

    /**
     * Find single ad by ID from Firebase or local storage.
     */
    protected function findAdById(string $id): ?array
    {
        try {
            $ad = $this->db->getReference("advertisements/{$id}")->getValue();
            if (is_array($ad)) {
                return $ad;
            }
        } catch (\Exception $e) {
            \Log::warning('Firebase find failed: ' . $e->getMessage());
        }

        $local = $this->getLocalAds();
        return $local[$id] ?? null;
    }

    /**
     * Get drivers collection safely for the layout navbar.
     */
    protected function getDriversCollection()
    {
        try {
            $driversData = $this->firebase->getData('drivers') ?? [];
            $drivers = collect();
            if (is_array($driversData)) {
                foreach ($driversData as $id => $driver) {
                    if (is_array($driver)) {
                        $driver['id'] = $id;
                        $drivers->push($driver);
                    }
                }
            }
            return $drivers;
        } catch (\Exception $e) {
            \Log::warning('Error loading drivers in AdvertisementController: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Upload an ad image to Firebase Storage with local fallback.
     */
    protected function uploadAdImage($file)
    {
        try {
            $bucket = $this->firebase->getStorage()->getBucket();
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = 'advertisements/' . uniqid('ad_') . '_' . time() . '.' . $extension;

            $bucket->upload(
                file_get_contents($file->getRealPath()),
                [
                    'name' => $fileName,
                    'predefinedAcl' => 'publicRead'
                ]
            );

            return sprintf(
                'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media',
                $bucket->name(),
                urlencode($fileName)
            );
        } catch (\Exception $e) {
            \Log::info('Firebase Storage upload error, falling back to local public storage: ' . $e->getMessage());

            try {
                $destDir = public_path('uploads/advertisements');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0777, true);
                }
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $localName = uniqid('ad_') . '_' . time() . '.' . $extension;
                $file->move($destDir, $localName);

                return asset('uploads/advertisements/' . $localName);
            } catch (\Exception $localErr) {
                \Log::error('Local ad image fallback upload error: ' . $localErr->getMessage());
                return asset('public/images/Car.png');
            }
        }
    }

    /**
     * Default curated sample ads for Crown Carz.
     */
    protected function getDefaultSampleAds(): array
    {
        return [
            'ad_sample_airport_01' => [
                'title' => 'Heathrow & Gatwick Transfers - 15% Off',
                'subtitle' => 'Pre-book your luxury airport transfer today with code AIRPORT15. Fixed prices guaranteed.',
                'image_url' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=80',
                'target_audience' => 'customer',
                'placement' => 'home_banner',
                'action_type' => 'promo_code',
                'action_value' => 'AIRPORT15',
                'button_text' => 'Copy Code',
                'badge_text' => 'HOT OFFER',
                'priority' => 1,
                'status' => 'Active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
                'clicks_count' => 42,
                'views_count' => 318,
                'created_at' => now()->subDays(2)->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            'ad_sample_driver_02' => [
                'title' => 'Weekend Chauffeur & City Rides - 20% Off',
                'subtitle' => 'Travel in luxury with private drivers across Reading, Heathrow, and London.',
                'image_url' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=80',
                'target_audience' => 'customer',
                'placement' => 'home_banner',
                'action_type' => 'promo_code',
                'action_value' => 'WEEKEND20',
                'button_text' => 'Claim 20% Off',
                'badge_text' => 'POPULAR',
                'priority' => 2,
                'status' => 'Active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'clicks_count' => 18,
                'views_count' => 195,
                'created_at' => now()->subDay()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            'ad_sample_vip_03' => [
                'title' => 'Executive Mercedes E-Class & S-Class Fleet',
                'subtitle' => 'Corporate travel, VIP events, and wedding chauffeur services tailored to perfection.',
                'image_url' => 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=1200&q=80',
                'target_audience' => 'customer',
                'placement' => 'popup_modal',
                'action_type' => 'call_phone',
                'action_value' => '+441189474747',
                'button_text' => 'Call To Book',
                'badge_text' => 'VIP LUXURY',
                'priority' => 3,
                'status' => 'Active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(12)->toDateString(),
                'clicks_count' => 77,
                'views_count' => 540,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    /**
     * Display a listing of all advertisements.
     */
    public function index(Request $request)
    {
        $drivers = $this->getDriversCollection();

        $rawAds = $this->fetchAllAdsFromDb();
        $ads = [];

        if (is_array($rawAds)) {
            foreach ($rawAds as $id => $item) {
                if (is_array($item)) {
                    $item['id'] = (string)$id;
                    $item['title'] = $item['title'] ?? 'Untitled Ad';
                    $item['subtitle'] = $item['subtitle'] ?? '';
                    $item['image_url'] = $item['image_url'] ?? asset('public/images/Car.png');
                    $item['target_audience'] = $item['target_audience'] ?? 'all';
                    $item['placement'] = $item['placement'] ?? 'home_banner';
                    $item['action_type'] = $item['action_type'] ?? 'none';
                    $item['action_value'] = $item['action_value'] ?? '';
                    $item['button_text'] = $item['button_text'] ?? 'Learn More';
                    $item['badge_text'] = $item['badge_text'] ?? '';
                    $item['priority'] = isset($item['priority']) ? (int)$item['priority'] : 10;
                    $item['status'] = $item['status'] ?? 'Active';
                    $item['start_date'] = $item['start_date'] ?? null;
                    $item['end_date'] = $item['end_date'] ?? null;
                    $item['clicks_count'] = (int)($item['clicks_count'] ?? 0);
                    $item['views_count'] = (int)($item['views_count'] ?? 0);
                    $item['created_at'] = $item['created_at'] ?? now()->toDateTimeString();

                    $ads[$id] = $item;
                }
            }
        }

        // Sort by priority ascending, then created_at descending
        uasort($ads, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
            }
            return $a['priority'] <=> $b['priority'];
        });

        // Compute statistics
        $stats = [
            'total' => count($ads),
            'active' => 0,
            'inactive' => 0,
            'customer' => 0,
            'driver' => 0,
            'all' => 0,
            'total_clicks' => 0,
            'total_views' => 0,
        ];

        foreach ($ads as $ad) {
            if (($ad['status'] ?? 'Active') === 'Active') {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }

            $aud = $ad['target_audience'] ?? 'all';
            if (isset($stats[$aud])) {
                $stats[$aud]++;
            }

            $stats['total_clicks'] += (int)($ad['clicks_count'] ?? 0);
            $stats['total_views'] += (int)($ad['views_count'] ?? 0);
        }

        return view('advertisements.index', compact('ads', 'stats', 'drivers'));
    }

    /**
     * Show the form for creating a new advertisement.
     */
    public function create()
    {
        $drivers = $this->getDriversCollection();
        return view('advertisements.create', compact('drivers'));
    }

    /**
     * Store a newly created advertisement in Firebase / local DB.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'image_url' => 'nullable|url|max:1000',
            'target_audience' => 'nullable|in:all,customer,driver',
            'placement' => 'required|in:home_banner,popup_modal,booking_screen,driver_feed',
            'action_type' => 'required|in:none,external_url,in_app_screen,promo_code,call_phone',
            'action_value' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:50',
            'priority' => 'nullable|integer|min:0|max:9999',
            'status' => 'required|in:Active,Inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $imageUrl = null;

            if ($request->hasFile('image_file')) {
                $imageUrl = $this->uploadAdImage($request->file('image_file'));
            }

            if (!$imageUrl && $request->filled('image_url')) {
                $imageUrl = trim($request->image_url);
            }

            if (!$imageUrl) {
                $imageUrl = asset('public/images/Car.png');
            }

            $adData = [
                'title' => trim($request->title),
                'subtitle' => trim($request->subtitle ?? ''),
                'image_url' => $imageUrl,
                'target_audience' => $request->input('target_audience', 'customer') ?: 'customer',
                'placement' => $request->placement,
                'action_type' => $request->action_type,
                'action_value' => trim($request->action_value ?? ''),
                'button_text' => trim($request->button_text ?? 'Learn More'),
                'badge_text' => trim($request->badge_text ?? ''),
                'priority' => (int)($request->priority ?? 1),
                'status' => $request->status,
                'start_date' => $request->start_date ?: null,
                'end_date' => $request->end_date ?: null,
                'clicks_count' => 0,
                'views_count' => 0,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            $newKey = $this->pushAdToDb($adData);

            return redirect()->route('advertisements.index')
                ->with('success', "Advertisement '{$request->title}' published successfully!");

        } catch (\Exception $e) {
            \Log::error('Error creating advertisement: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating advertisement: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified advertisement.
     */
    public function edit($id)
    {
        $drivers = $this->getDriversCollection();
        $ad = $this->findAdById($id);

        if (!$ad || !is_array($ad)) {
            return redirect()->route('advertisements.index')
                ->with('error', 'Advertisement not found or already deleted.');
        }

        $ad['id'] = $id;

        return view('advertisements.edit', compact('ad', 'id', 'drivers'));
    }

    /**
     * Update the specified advertisement.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'image_url' => 'nullable|url|max:1000',
            'target_audience' => 'nullable|in:all,customer,driver',
            'placement' => 'required|in:home_banner,popup_modal,booking_screen,driver_feed',
            'action_type' => 'required|in:none,external_url,in_app_screen,promo_code,call_phone',
            'action_value' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'badge_text' => 'nullable|string|max:50',
            'priority' => 'nullable|integer|min:0|max:9999',
            'status' => 'required|in:Active,Inactive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $existing = $this->findAdById($id);

            if (!$existing) {
                return redirect()->route('advertisements.index')->with('error', 'Advertisement not found.');
            }

            $imageUrl = $existing['image_url'] ?? null;

            if ($request->hasFile('image_file')) {
                $uploaded = $this->uploadAdImage($request->file('image_file'));
                if ($uploaded) {
                    $imageUrl = $uploaded;
                }
            } elseif ($request->filled('image_url')) {
                $imageUrl = trim($request->image_url);
            }

            $updateData = [
                'title' => trim($request->title),
                'subtitle' => trim($request->subtitle ?? ''),
                'image_url' => $imageUrl ?: asset('public/images/Car.png'),
                'target_audience' => $request->input('target_audience', $existing['target_audience'] ?? 'customer') ?: 'customer',
                'placement' => $request->placement,
                'action_type' => $request->action_type,
                'action_value' => trim($request->action_value ?? ''),
                'button_text' => trim($request->button_text ?? 'Learn More'),
                'badge_text' => trim($request->badge_text ?? ''),
                'priority' => (int)($request->priority ?? 1),
                'status' => $request->status,
                'start_date' => $request->start_date ?: null,
                'end_date' => $request->end_date ?: null,
                'updated_at' => now()->toDateTimeString(),
            ];

            $this->updateAdInDb($id, $updateData);

            return redirect()->route('advertisements.index')
                ->with('success', "Advertisement '{$request->title}' updated successfully!");

        } catch (\Exception $e) {
            \Log::error("Error updating advertisement {$id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating advertisement: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified advertisement.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $ad = $this->findAdById($id);

            if (!$ad) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Advertisement not found'], 404);
                }
                return redirect()->route('advertisements.index')->with('error', 'Advertisement not found.');
            }

            $title = $ad['title'] ?? 'Advertisement';
            $this->deleteAdFromDb($id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Advertisement '{$title}' deleted successfully."
                ]);
            }

            return redirect()->route('advertisements.index')
                ->with('success', "Advertisement '{$title}' deleted successfully.");

        } catch (\Exception $e) {
            \Log::error("Error deleting advertisement {$id}: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('advertisements.index')->with('error', 'Error deleting: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status between Active and Inactive.
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $ad = $this->findAdById($id);

            if (!$ad) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Advertisement not found'], 404);
                }
                return redirect()->route('advertisements.index')->with('error', 'Advertisement not found.');
            }

            $currentStatus = $ad['status'] ?? 'Active';
            $newStatus = ($currentStatus === 'Active') ? 'Inactive' : 'Active';

            $this->updateAdInDb($id, [
                'status' => $newStatus,
                'updated_at' => now()->toDateTimeString(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $newStatus,
                    'message' => "Advertisement is now {$newStatus}."
                ]);
            }

            return redirect()->route('advertisements.index')
                ->with('success', "Advertisement '{$ad['title']}' is now {$newStatus}.");

        } catch (\Exception $e) {
            \Log::error("Error toggling status for advertisement {$id}: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('advertisements.index')->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Seed high-quality Crown Carz sample advertisements.
     */
    public function seedSamples(Request $request)
    {
        try {
            $samples = $this->getDefaultSampleAds();
            foreach ($samples as $sample) {
                $this->pushAdToDb($sample);
            }

            return redirect()->route('advertisements.index')
                ->with('success', 'Sample advertisements successfully loaded!');

        } catch (\Exception $e) {
            \Log::error('Error seeding advertisements: ' . $e->getMessage());
            return redirect()->route('advertisements.index')
                ->with('error', 'Error loading samples: ' . $e->getMessage());
        }
    }

    // ==========================================================
    // 📱 PUBLIC API ENDPOINTS FOR MOBILE APP (Flutter / iOS / Android)
    // ==========================================================

    /**
     * Get all active advertisements formatted for mobile consumption.
     * Supports ?audience=customer|driver|all and ?placement=home_banner|popup_modal|...
     */
    public function apiActive(Request $request)
    {
        return $this->apiIndex($request, true);
    }

    /**
     * General API endpoint for advertisements.
     */
    public function apiIndex(Request $request, $onlyActive = true)
    {
        try {
            $rawAds = $this->fetchAllAdsFromDb();
            $audienceFilter = strtolower($request->query('audience', $request->query('target', 'all')));
            $placementFilter = strtolower($request->query('placement', ''));
            $now = now();

            $result = [];

            if (is_array($rawAds)) {
                foreach ($rawAds as $id => $ad) {
                    if (!is_array($ad)) continue;

                    $status = $ad['status'] ?? 'Active';
                    if ($onlyActive && $status !== 'Active') {
                        continue;
                    }

                    // Schedule date checks
                    if ($onlyActive) {
                        if (!empty($ad['start_date']) && strtotime($ad['start_date']) > $now->timestamp) {
                            continue; // Campaign hasn't started yet
                        }
                        if (!empty($ad['end_date']) && strtotime($ad['end_date'] . ' 23:59:59') < $now->timestamp) {
                            continue; // Campaign has expired
                        }
                    }

                    // Audience filter: if requesting 'customer', include 'customer' and 'all'
                    $adAudience = strtolower($ad['target_audience'] ?? 'all');
                    if ($audienceFilter !== 'all') {
                        if ($adAudience !== 'all' && $adAudience !== $audienceFilter) {
                            continue;
                        }
                    }

                    // Placement filter: e.g. home_banner, popup_modal
                    $adPlacement = strtolower($ad['placement'] ?? 'home_banner');
                    if (!empty($placementFilter) && $adPlacement !== $placementFilter) {
                        continue;
                    }

                    $result[] = [
                        'id' => (string)$id,
                        'title' => (string)($ad['title'] ?? ''),
                        'subtitle' => (string)($ad['subtitle'] ?? ''),
                        'image_url' => (string)($ad['image_url'] ?? asset('public/images/Car.png')),
                        'target_audience' => $adAudience,
                        'placement' => $adPlacement,
                        'action_type' => (string)($ad['action_type'] ?? 'none'),
                        'action_value' => (string)($ad['action_value'] ?? ''),
                        'button_text' => (string)($ad['button_text'] ?? 'Learn More'),
                        'badge_text' => (string)($ad['badge_text'] ?? ''),
                        'priority' => (int)($ad['priority'] ?? 10),
                        'status' => $status,
                        'start_date' => $ad['start_date'] ?? null,
                        'end_date' => $ad['end_date'] ?? null,
                        'clicks_count' => (int)($ad['clicks_count'] ?? 0),
                        'views_count' => (int)($ad['views_count'] ?? 0),
                        'created_at' => $ad['created_at'] ?? null,
                    ];
                }
            }

            // Sort by priority ascending, then created_at descending
            usort($result, function ($a, $b) {
                if ($a['priority'] === $b['priority']) {
                    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
                }
                return $a['priority'] <=> $b['priority'];
            });

            return response()->json([
                'status' => true,
                'message' => 'Advertisements fetched successfully',
                'count' => count($result),
                'data' => $result,
            ], 200, ['Access-Control-Allow-Origin' => '*']);

        } catch (\Exception $e) {
            \Log::error('API Advertisements Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch advertisements: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Record an ad click event from the app.
     */
    public function apiClick(Request $request, $id)
    {
        try {
            $ad = $this->findAdById($id);

            if (!$ad) {
                return response()->json(['status' => false, 'message' => 'Ad not found'], 404);
            }

            $currentClicks = (int)($ad['clicks_count'] ?? 0);
            $newClicks = $currentClicks + 1;
            $this->updateAdInDb($id, ['clicks_count' => $newClicks]);

            return response()->json([
                'status' => true,
                'message' => 'Click tracked successfully',
                'clicks_count' => $newClicks
            ], 200, ['Access-Control-Allow-Origin' => '*']);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Record an ad impression / view event from the app.
     */
    public function apiView(Request $request, $id)
    {
        try {
            $ad = $this->findAdById($id);

            if (!$ad) {
                return response()->json(['status' => false, 'message' => 'Ad not found'], 404);
            }

            $currentViews = (int)($ad['views_count'] ?? 0);
            $newViews = $currentViews + 1;
            $this->updateAdInDb($id, ['views_count' => $newViews]);

            return response()->json([
                'status' => true,
                'message' => 'View tracked successfully',
                'views_count' => $newViews
            ], 200, ['Access-Control-Allow-Origin' => '*']);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
