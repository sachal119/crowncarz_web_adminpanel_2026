<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fixed_pricing')) {
            return;
        }

        if (!Schema::hasIndex('fixed_pricing', 'fixed_pricing_route_index')) {
            Schema::table('fixed_pricing', function (Blueprint $table): void {
                $table->index(
                    ['from_postcode', 'to_postcode'],
                    'fixed_pricing_route_index'
                );
            });
        }

        if (!Schema::hasIndex('fixed_pricing', 'fixed_pricing_reverse_route_index')) {
            Schema::table('fixed_pricing', function (Blueprint $table): void {
                $table->index(
                    ['to_postcode', 'from_postcode'],
                    'fixed_pricing_reverse_route_index'
                );
            });
        }
    }

    public function down(): void
    {
        // Index repair must not modify production pricing data on rollback.
    }
};
