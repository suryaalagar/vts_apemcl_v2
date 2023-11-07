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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->integer('route_id')->nullable();
            $table->string('routename')->nullable();
            $table->double('route_start_lat')->nullable();
            $table->double('route_start_lng')->nullable();
            $table->double('route_end_lat')->nullable();
            $table->double('route_end_lng')->nullable();
            $table->longText('route_polyline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
