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
        Schema::create('keyoff_keyon_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('s_lat')->default(0);
            $table->double('s_lng')->default(0);
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->double('e_lat')->default(0);
            $table->double('e_lng')->default(0);
            $table->double('start_odometer')->default(0);
            $table->double('end_odometer')->default(0);
            $table->double('total_km')->default(0);
            $table->string('start_fuel_litre',45)->nullable();
            $table->string('end_fuel_litre',45)->nullable();
            $table->string('vehicle_battery',45)->nullable();
            $table->string('device_battery',45)->nullable();
            $table->Text('start_location')->nullable();  
            $table->Text('end_location')->nullable();  
            $table->smallInteger('flag')->nullable();      	
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyoff_keyon_reports');
    }
};
