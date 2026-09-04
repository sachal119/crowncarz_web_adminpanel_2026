<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pricing_percentages')) {
            Schema::create('pricing_percentages', function (Blueprint $table): void {
                $table->unsignedTinyInteger('id')->primary();
                $table->decimal('estate', 7, 3)->default(0);
                $table->decimal('mpv', 7, 3)->default(0);
                $table->decimal('seater8', 7, 3)->default(0);
                $table->decimal('executive', 7, 3)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // This repair migration must never remove a table that may predate it.
    }
};
