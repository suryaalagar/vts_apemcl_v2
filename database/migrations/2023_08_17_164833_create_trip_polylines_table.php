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
        Schema::create('trip_polylines', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicleid')->nullable();
            $table->string('poc_number')->nullable();
            $table->string('route_name')->nullable();
            $table->longText('polyline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_polylines');
    }
};
