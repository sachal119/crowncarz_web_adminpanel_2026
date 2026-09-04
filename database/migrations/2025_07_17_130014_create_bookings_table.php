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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->string('ref_no')->unique();
        $table->foreignId('passenger_id')->constrained()->onDelete('cascade');
        $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
        $table->enum('payment_type', ['cash', 'card', 'account']);
        $table->string('pickup_address');
        $table->json('via_addresses')->nullable();
        $table->string('dropoff_address');
        $table->enum('status', ['pending', 'assigned', 'on_route', 'completed', 'cancelled'])->default('pending');
        $table->decimal('price', 8, 2)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
