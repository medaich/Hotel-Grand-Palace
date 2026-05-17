<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 10)->unique();
            $table->string('room_type', 50)->nullable();
            $table->integer('floor')->nullable();
            $table->integer('capacity')->default(2);
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->string('status', 20)->default('available');
            $table->text('description')->nullable();
            $table->text('amenities')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
