<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('fixed_pricing', 'unique_key')) {
            Schema::table('fixed_pricing', function (Blueprint $table) {
                $table->string('unique_key', 100)->nullable()->after('vehicle_type');
            });
        }

        DB::table('fixed_pricing')
            ->whereNull('unique_key')
            ->orWhere('unique_key', '')
            ->update([
                'unique_key' => DB::raw("CONCAT(UPPER(TRIM(from_postcode)), '_', UPPER(TRIM(to_postcode)), '_', COALESCE(vehicle_type, '0'))"),
            ]);

        Schema::table('fixed_pricing', function (Blueprint $table) {
            $table->unique('unique_key', 'fixed_pricing_unique_key_unique');
            $table->index(['from_postcode', 'to_postcode'], 'fixed_pricing_from_to_index');
            $table->index(['to_postcode', 'from_postcode'], 'fixed_pricing_to_from_index');
        });
    }

    public function down(): void
    {
        Schema::table('fixed_pricing', function (Blueprint $table) {
            $table->dropUnique('fixed_pricing_unique_key_unique');
            $table->dropIndex('fixed_pricing_from_to_index');
            $table->dropIndex('fixed_pricing_to_from_index');
        });
    }
};
