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
        Schema::create('tripplan_reports', function (Blueprint $table) {
            $table->increments('trip_id');
            $table->integer('client_id')->nullable();
            $table->integer('vehicleid')->nullable();
            $table->integer('route_id')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->integer('start_location')->nullable();
            $table->integer('end_location')->nullable();
            $table->string('poc_number')->nullable();
            $table->string('route_name')->nullable();
            $table->integer('start_geo_id')->nullable();
            $table->integer('end_geo_id')->nullable();
            $table->integer('geo_status')->nullable();
            $table->integer('status')->nullable();
            $table->dateTime('trip_date')->nullable();
            $table->string('trip_type')->nullable();
            $table->string('parking_duration')->nullable();
            $table->string('idle_duration')->nullable();
            $table->double('start_odometer')->default(0);
            $table->double('end_odometer')->default(0);
            $table->double('distance')->default(0);
            $table->double('s_lat')->default(0);
            $table->double('s_lng')->default(0);
            $table->double('e_lat')->default(0);
            $table->double('e_lng')->default(0);
            $table->integer('flag')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tripplan_reports');
    }
};
