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
        $this->ensureMileagePricingTable();

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
                DB::table('mileage_pricing')->updateOrInsert(
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
        // Do not delete production pricing data during rollback.
    }

    private function ensureMileagePricingTable(): void
    {
        if (!Schema::hasTable('mileage_pricing')) {
            Schema::create('mileage_pricing', function (Blueprint $table): void {
                $table->id();
                $table->string('car_type', 40);
                $table->decimal('from_mileage', 10, 2);
                $table->decimal('to_mileage', 10, 2);
                $table->decimal('cost_per_mileage', 12, 4)->default(0);
                $table->decimal('minimum_price', 12, 2)->default(0);
                $table->timestamps();
                $table->unique(['car_type', 'from_mileage', 'to_mileage'], 'mileage_pricing_bracket_unique');
                $table->index(['car_type', 'from_mileage'], 'mileage_pricing_lookup_index');
            });

            return;
        }

        // Preserve the old columns/data, but allow new Firebase-format rows.
        Schema::table('mileage_pricing', function (Blueprint $table): void {
            if (Schema::hasColumn('mileage_pricing', 'slab_from')) {
                $table->decimal('slab_from', 5, 2)->nullable()->change();
            }
            if (Schema::hasColumn('mileage_pricing', 'slab_to')) {
                $table->decimal('slab_to', 5, 2)->nullable()->change();
            }
            if (Schema::hasColumn('mileage_pricing', 'price')) {
                $table->decimal('price', 8, 2)->nullable()->change();
            }
        });

        $columns = [
            'car_type' => fn (Blueprint $table) => $table->string('car_type', 40)->nullable(),
            'from_mileage' => fn (Blueprint $table) => $table->decimal('from_mileage', 10, 2)->nullable(),
            'to_mileage' => fn (Blueprint $table) => $table->decimal('to_mileage', 10, 2)->nullable(),
            'cost_per_mileage' => fn (Blueprint $table) => $table->decimal('cost_per_mileage', 12, 4)->default(0),
            'minimum_price' => fn (Blueprint $table) => $table->decimal('minimum_price', 12, 2)->default(0),
        ];

        foreach ($columns as $column => $addColumn) {
            if (!Schema::hasColumn('mileage_pricing', $column)) {
                Schema::table('mileage_pricing', $addColumn);
            }
        }

        $this->makeUnrelatedRequiredColumnsNullable();

        if (!Schema::hasIndex('mileage_pricing', 'mileage_pricing_bracket_unique')) {
            Schema::table('mileage_pricing', function (Blueprint $table): void {
                $table->unique(['car_type', 'from_mileage', 'to_mileage'], 'mileage_pricing_bracket_unique');
            });
        }

        if (!Schema::hasIndex('mileage_pricing', 'mileage_pricing_lookup_index')) {
            Schema::table('mileage_pricing', function (Blueprint $table): void {
                $table->index(['car_type', 'from_mileage'], 'mileage_pricing_lookup_index');
            });
        }
    }

    private function makeUnrelatedRequiredColumnsNullable(): void
    {
        // Some live installations contain columns copied from an older pricing
        // schema (for example from_postcode). They are unrelated to mileage
        // brackets, but MySQL rejects inserts while they are NOT NULL without
        // a default. Preserve the columns and their data; only relax that
        // constraint so Firebase-format records can be inserted.
        $usedColumns = [
            'id',
            'car_type',
            'from_mileage',
            'to_mileage',
            'cost_per_mileage',
            'minimum_price',
            'created_at',
            'updated_at',
        ];

        $columns = DB::select(
            <<<'SQL'
                SELECT COLUMN_NAME, COLUMN_TYPE, CHARACTER_SET_NAME, COLLATION_NAME
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'mileage_pricing'
                  AND IS_NULLABLE = 'NO'
                  AND COLUMN_DEFAULT IS NULL
                  AND EXTRA NOT LIKE '%auto_increment%'
                  AND EXTRA NOT LIKE '%GENERATED%'
            SQL
        );

        foreach ($columns as $column) {
            $name = (string) $column->COLUMN_NAME;
            if (in_array($name, $usedColumns, true)) {
                continue;
            }

            $quotedName = str_replace('`', '``', $name);
            $definition = (string) $column->COLUMN_TYPE;

            if (!empty($column->CHARACTER_SET_NAME)) {
                $definition .= ' CHARACTER SET '.preg_replace('/[^A-Za-z0-9_]/', '', (string) $column->CHARACTER_SET_NAME);
            }
            if (!empty($column->COLLATION_NAME)) {
                $definition .= ' COLLATE '.preg_replace('/[^A-Za-z0-9_]/', '', (string) $column->COLLATION_NAME);
            }

            DB::statement(
                "ALTER TABLE `mileage_pricing` MODIFY COLUMN `{$quotedName}` {$definition} NULL"
            );
        }
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
