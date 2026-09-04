<?php

use App\Services\FirebaseService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mileage_prices')) {
            Schema::create('mileage_prices', function (Blueprint $table): void {
                $table->id();
                $table->string('car_type', 40);
                $table->decimal('from_mileage', 10, 2);
                $table->decimal('to_mileage', 10, 2);
                $table->decimal('cost_per_mileage', 12, 4)->default(0);
                $table->decimal('minimum_price', 12, 2)->default(0);
                $table->timestamps();

                $table->unique(
                    ['car_type', 'from_mileage', 'to_mileage'],
                    'mileage_prices_bracket_unique'
                );
                $table->index(
                    ['car_type', 'from_mileage'],
                    'mileage_prices_lookup_index'
                );
            });
        }

        $firebaseRows = app(FirebaseService::class)
            ->getDatabase()
            ->getReference('mileage_pricing')
            ->getValue() ?? [];

        if (!is_array($firebaseRows) || $firebaseRows === []) {
            throw new RuntimeException(
                'No records were found at the Firebase mileage_pricing node. Nothing was migrated.'
            );
        }

        $records = [];
        $now = now();

        foreach ($firebaseRows as $firebaseKey => $row) {
            if (!is_array($row)) {
                continue;
            }

            $carType = trim((string) ($row['car_type'] ?? ''));
            if ($carType === '') {
                continue;
            }

            $fromMileage = round((float) ($row['from_mileage'] ?? 0), 2);
            $toMileage = round((float) ($row['to_mileage'] ?? 0), 2);

            if ($toMileage < $fromMileage) {
                throw new RuntimeException(
                    "Firebase mileage_pricing/{$firebaseKey} has to_mileage smaller than from_mileage."
                );
            }

            // If Firebase contains the same bracket more than once, the last
            // record wins instead of creating duplicate MySQL rows.
            $bracketKey = mb_strtolower($carType).'|'.number_format($fromMileage, 2, '.', '').'|'.number_format($toMileage, 2, '.', '');
            $records[$bracketKey] = [
                'car_type' => $carType,
                'from_mileage' => $fromMileage,
                'to_mileage' => $toMileage,
                'cost_per_mileage' => round((float) ($row['cost_per_mileage'] ?? 0), 4),
                'minimum_price' => round((float) ($row['minimum_price'] ?? 0), 2),
                'created_at' => $this->firebaseDate($row['created_at'] ?? null, $now),
                'updated_at' => $now,
            ];
        }

        if ($records === []) {
            throw new RuntimeException(
                'Firebase mileage_pricing exists, but it contains no valid mileage brackets.'
            );
        }

        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                DB::table('mileage_prices')->updateOrInsert(
                    [
                        'car_type' => $record['car_type'],
                        'from_mileage' => $record['from_mileage'],
                        'to_mileage' => $record['to_mileage'],
                    ],
                    [
                        'cost_per_mileage' => $record['cost_per_mileage'],
                        'minimum_price' => $record['minimum_price'],
                        'created_at' => $record['created_at'],
                        'updated_at' => $record['updated_at'],
                    ]
                );
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty. A rollback must not delete production
        // mileage prices which may have been edited after this one-time copy.
    }

    private function firebaseDate(mixed $value, Carbon $fallback): Carbon
    {
        if ($value === null || trim((string) $value) === '') {
            return $fallback;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (Throwable) {
            return $fallback;
        }
    }
};
