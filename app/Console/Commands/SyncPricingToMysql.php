<?php

namespace App\Console\Commands;

use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPricingToMysql extends Command
{
    protected $signature = 'pricing:sync-firebase-to-mysql {--replace : Empty MySQL pricing tables before syncing}';

    protected $description = 'Copy fixed prices, mileage brackets and pricing percentages from Firebase to MySQL';

    public function handle(FirebaseService $firebaseService): int
    {
        $database = $firebaseService->getDatabase();

        if ($this->option('replace')) {
            if (!$this->confirm('This will empty the MySQL pricing tables before syncing. Continue?')) {
                return self::SUCCESS;
            }

            DB::table('fixed_prices')->truncate();
            DB::table('mileage_pricing')->truncate();
            DB::table('pricing_percentages')->truncate();
        }

        $this->syncFixedPrices($database);
        $this->syncMileagePrices($database);
        $this->syncPercentages($database);

        $this->newLine();
        $this->info('Pricing data is ready in MySQL.');

        return self::SUCCESS;
    }

    private function syncFixedPrices($database): void
    {
        $this->info('Syncing fixed prices...');

        $lastKey = null;
        $total = 0;
        $scanned = 0;
        $skipped = 0;
        $batchSize = 1000;

        do {
            $limit = $lastKey === null ? $batchSize : $batchSize + 1;
            $query = $database->getReference('fixed_prices')->orderByKey()->limitToFirst($limit);
            if ($lastKey !== null) {
                $query = $query->startAt($lastKey);
            }

            $firebaseRows = $query->getValue() ?? [];
            if ($firebaseRows === []) {
                break;
            }

            $rows = [];
            $now = now();
            $nextLastKey = $lastKey;
            $newRecords = 0;
            foreach ($firebaseRows as $key => $row) {
                $key = (string) $key;
                if ($lastKey !== null && $key === $lastKey) {
                    continue;
                }

                $nextLastKey = $key;
                $newRecords++;
                $scanned++;

                if (!is_array($row)) {
                    $skipped++;
                    continue;
                }

                $from = $this->normalize($row['from_postcode'] ?? '');
                $to = $this->normalize($row['to_postcode'] ?? '');
                if ($from === '' || $to === '') {
                    $skipped++;
                    continue;
                }

                $rows[] = [
                    'source_key' => 'firebase:'.(string) $key,
                    'from_postcode' => trim((string) ($row['from_postcode'] ?? $from)),
                    'to_postcode' => trim((string) ($row['to_postcode'] ?? $to)),
                    'from_postcode_normalized' => $from,
                    'to_postcode_normalized' => $to,
                    'saloon' => (float) ($row['Saloon'] ?? $row['saloon_fare'] ?? 0),
                    'estate' => (float) ($row['Estate'] ?? $row['estate_fare'] ?? 0),
                    'mpv' => (float) ($row['MPV'] ?? $row['mpv_fare'] ?? $row['seater6_fare'] ?? 0),
                    'seater_8' => (float) ($row['8 Seater'] ?? $row['seater8_fare'] ?? $row['seater7_8_fare'] ?? 0),
                    'executive' => (float) ($row['Executive'] ?? $row['executive_fare'] ?? $row['seater12_16_fare'] ?? 0),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($rows !== []) {
                DB::table('fixed_prices')->upsert(
                    $rows,
                    ['source_key'],
                    ['from_postcode', 'to_postcode', 'from_postcode_normalized', 'to_postcode_normalized', 'saloon', 'estate', 'mpv', 'seater_8', 'executive', 'updated_at']
                );
                $total += count($rows);
                $this->output->write("\rFixed prices processed: {$total}");
            }

            $lastKey = $nextLastKey;
        } while ($newRecords === $batchSize);

        $this->newLine();
        $this->line("Firebase records scanned: {$scanned}; skipped invalid: {$skipped}; MySQL rows written: {$total}");
        $this->line('Current unique MySQL fixed-price rows: '.DB::table('fixed_prices')->count());
    }

    private function syncMileagePrices($database): void
    {
        $this->info('Syncing mileage pricing...');
        $firebaseRows = $database->getReference('mileage_pricing')->getValue() ?? [];
        $rows = [];
        $now = now();

        foreach ($firebaseRows as $row) {
            if (!is_array($row) || empty($row['car_type'])) {
                continue;
            }

            $rows[] = [
                'car_type' => (string) $row['car_type'],
                'from_mileage' => (float) ($row['from_mileage'] ?? 0),
                'to_mileage' => (float) ($row['to_mileage'] ?? 0),
                'cost_per_mileage' => (float) ($row['cost_per_mileage'] ?? 0),
                'minimum_price' => (float) ($row['minimum_price'] ?? 0),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('mileage_pricing')->upsert(
                $chunk,
                ['car_type', 'from_mileage', 'to_mileage'],
                ['cost_per_mileage', 'minimum_price', 'updated_at']
            );
        }

        $this->line('Mileage brackets processed: '.count($rows));
    }

    private function syncPercentages($database): void
    {
        $data = $database->getReference('fixed_pricing_percentages')->getValue() ?? [];
        $percentages = $data['percentages'] ?? [];
        $now = now();

        DB::table('pricing_percentages')->updateOrInsert(
            ['id' => 1],
            [
                'estate' => (float) ($percentages['estate'] ?? 0),
                'mpv' => (float) ($percentages['mpv'] ?? 0),
                'seater8' => (float) ($percentages['seater8'] ?? 0),
                'executive' => (float) ($percentages['executive'] ?? 0),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $this->line('Pricing percentages processed: 1');
    }

    private function normalize(?string $postcode): string
    {
        return preg_replace('/\s+/', ' ', strtoupper(trim((string) $postcode))) ?? '';
    }
}
