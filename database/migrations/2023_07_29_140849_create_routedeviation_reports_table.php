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
        Schema::create('routedeviation_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('route_id')->nullable();
            $table->string('route_name')->nullable();
            $table->string('device_imei')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->string('route_deviate_outtime')->nullable();
            $table->string('route_deviate_intime')->nullable();
            $table->string('route_out_location')->nullable();
            $table->string('route_in_location')->nullable();
            $table->string('route_out_lat')->nullable();
            $table->string('route_out_lng')->nullable();
            $table->string('route_in_lat')->nullable();
            $table->string('route_in_lng')->nullable();
            $table->integer('location_status')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routedeviation_reports');
    }
};
