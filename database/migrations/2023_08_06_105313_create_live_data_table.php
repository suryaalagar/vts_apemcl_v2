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
        Schema::create('live_data', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicle_id')->nullable();
            $table->string('vehicle_name',40)->nullable();
            $table->tinyInteger('vehicle_current_status')->nullable();
            $table->tinyInteger('vehicle_status')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->tinyInteger('ignition')->nullable();
            $table->tinyInteger('ac_status')->nullable();
            $table->float('speed')->default(0);
            $table->integer('angle')->nullable();
            $table->double('odometer')->default(0);
            $table->dateTime('device_updatedtime')->nullable();
            $table->float('temperature')->nullable();
            $table->float('device_battery_volt')->nullable();
            $table->float('vehicle_battery_volt')->nullable();
            $table->dateTime('last_ignition_on_time')->nullable();
            $table->dateTime('last_ignition_off_time')->nullable();
            $table->float('fuel_litre')->nullable();
            $table->tinyInteger('vehicle_sleep')->nullable();
            $table->tinyInteger('imobilizer_status')->nullable();
            $table->smallInteger('altitude')->default(0);
            $table->string('gpssignal')->nullable();
            $table->string('gsm_status')->nullable();
            $table->float('rpm_value')->nullable();
            $table->enum('sec_engine_status',[1,0])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_data');
    }
};
