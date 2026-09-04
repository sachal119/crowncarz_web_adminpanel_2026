<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('fixed_pricing', function (Blueprint $table) {
    $table->id();
    $table->string('from_postcode');
    $table->string('to_postcode');
    $table->decimal('company_price', 8, 2);
    $table->decimal('driver_price', 8, 2);
    $table->decimal('agent_commission', 8, 2)->default(0);
    $table->string('type')->nullable();
    $table->string('vehicle_type')->nullable();
    $table->timestamps();
});
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_pricing');
    }
};
