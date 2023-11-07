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
        Schema::create('play_back_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->double('speed')->default(0);
            $table->double('odometer')->default(0);
            $table->double('angle')->default(0);
            $table->dateTime('device_datetime')->nullable();
            $table->tinyInteger('ignition')->default(0);
            $table->tinyInteger('ac_status')->default(0);
            $table->timestamp('timestamp')->nullable();
            $table->tinyInteger('packet_status')->default(0);
            $table->longText('packet_details')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_back_history');
    }
};
