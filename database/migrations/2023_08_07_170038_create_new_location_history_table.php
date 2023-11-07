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
        Schema::create('new_location_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei')->index()->comment('Device IMEI');
            $table->double('lattitute')->comment('Latitude');
            $table->double('longitute')->comment('Longtitude');
            $table->tinyInteger('ignition')->default(0);
            $table->tinyInteger('ac_status')->default(0);
            $table->float('speed')->default(0);
            $table->smallInteger('angle')->default(0);
            $table->dateTime('device_datetime')->comment('Device Time')->nullable();
            $table->double('distance_with_odometer')->comment('Odometer with device')->nullable();
            $table->double('distance_without_odometer')->comment('Odometer without device')->nullable();
            $table->float('device_battery_volt')->default(0);
            $table->float('vehicle_battery_volt')->default(0);
            $table->string('device_battery_percent',10)->nullable();
            $table->string('device_name',15)->nullable();
            $table->string('gpssignal',15)->nullable();
            $table->tinyInteger('gsm_status')->default(0); 
            $table->tinyInteger('gps_statelite')->default(0); 
            $table->smallInteger('altitude')->default(0);
            $table->string('cell_id',30)->nullable();
            $table->tinyInteger('packet_status')->default(0); 
            $table->longText('packet')->nullable();  
            $table->tinyInteger('vehicle_sleep')->default(0); 
            $table->tinyInteger('sec_engine_status')->default(0); 
            $table->tinyInteger('status')->default(0); 
            $table->tinyInteger('io_state')->default(0); 
            $table->dateTime('server_time')->comment('Created TIme Device');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_location_history');
    }
};
