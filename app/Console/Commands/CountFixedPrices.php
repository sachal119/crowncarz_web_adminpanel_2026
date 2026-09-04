<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;

class CountFixedPrices extends Command
{
    protected $signature = 'count:fixed-prices';
    protected $description = 'Count total rows in fixed_prices and store result in Firebase';

    protected $firebase;

    public function __construct(FirebaseService $firebaseService)
    {
        parent::__construct();

        // If you inject Firebase via service container
        $this->firebase = $firebaseService->getDatabase();
    }

        public function handle()
{
    $this->info('Counting fixed_prices records...');

    $perPage = 5000; // bigger chunk size for CLI
    $totalCount = 0;
    $lastKey = null;

    do {
        $query = $this->firebase->getReference('fixed_prices')
            ->orderByKey()
            ->limitToFirst($perPage + 12);

        if ($lastKey) {
            $query = $query->startAfter($lastKey);
        }

        $snapshot = $query->getValue();
        $data = $snapshot ? $snapshot : [];

        $rowCount = count($data);

        if ($rowCount > $perPage) {
            // 👉 store last key first
            $lastKey = array_key_last($data);

            // 👉 remove that last element so it's not double-counted
            array_pop($data);

            $rowCount = count($data);
        } else {
            $lastKey = null;
        }

        $totalCount += $rowCount;

        $this->info("Processed {$totalCount} rows so far...");

    } while ($lastKey);

    // ✅ Save result to Firebase
    $this->firebase->getReference('fixed_prices_count')->set($totalCount);

    $this->info("✅ Finished! Total count = {$totalCount} (saved in fixed_prices_count).");
}
}
