<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Database;
use App\Services\FirebaseService;

class DeleteFixedPrices extends Command
{
    protected $signature = 'delete:fixed-prices';
    protected $description = 'Delete the entire fixed_prices node in Firebase Realtime Database';

    protected $firebase;

    public function __construct(FirebaseService $firebaseService)
    {
        parent::__construct();

        // If you inject Firebase via service container
        $this->firebase = $firebaseService->getDatabase();
    }

    public function handle()
    {
        $this->info('Starting chunked deletion of fixed_prices children...');

        $chunkSize = 10000;
        $ref = $this->firebase->getReference('fixed_prices');
        $rootRef = $this->firebase->getReference(); // Get reference to the database root
        $lastKey = null;
        $totalDeleted = 0;

        // --- Part 1: Chunked Deletion of Children ---
        do {
            $query = $ref->orderByKey()->limitToFirst($chunkSize + 1);

            if ($lastKey) {
                // IMPORTANT: When using startAfter, you must pass the VALUE of the key, not the key itself
                // Kreait's startAfter usually handles this, but your current logic relies on array_key_last
                // We'll proceed with your original key logic which seems to work for chunking
                $query = $query->startAt($lastKey);
            }

            // The 'limitToFirst($chunkSize + 1)' logic ensures we detect if there's a next page
            $snapshot = $query->getValue();
            $data = $snapshot ? $snapshot : [];
            $count = count($data);

            if ($count > $chunkSize) {
                $keys = array_keys($data);
                $lastKey = $keys[$chunkSize]; // The key we start the next batch AFTER
                unset($data[$lastKey]); // Remove the key we saved for the next batch
            } else {
                $lastKey = null;
            }

            $updates = [];
            foreach ($data as $key => $val) {
                $updates[$key] = null; // null = delete
            }

            if (!empty($updates)) {
                $ref->update($updates); // Deleting children of 'fixed_prices'
                $deleted = count($updates);
                $totalDeleted += $deleted;
                $this->info("Deleted {$deleted} records (total {$totalDeleted})...");
            }

        } while ($lastKey);

        $this->info("Successfully deleted all {$totalDeleted} children. Now attempting final node removal...");

        // ----------------------------------------------------------------------
        // --- Part 2: CRITICAL FIX - Delete the Parent Node via Root Update ---
        // ----------------------------------------------------------------------
        try {
            // Delete 'fixed_prices' by setting its value to null on the parent (root)
            // This is a minimal-payload operation that avoids the "Data to write exceeds" error.
            $rootRef->update([
                'fixed_prices' => null 
            ]);
            
            $this->info('✅ Successfully deleted the **fixed_prices** node via root update.');

        } catch (\Throwable $e) {
            $this->error('❌ FAILED to complete the final node removal: ' . $e->getMessage());
        }

        $this->info("✅ Finished job. Total records deleted: {$totalDeleted}");
    }
}