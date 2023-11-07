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
        Schema::create('parking_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehicle_id')->default(0);
            $table->bigInteger('deviceimei')->nullable();
            $table->double('s_lat')->default(0);
            $table->double('s_lng')->default(0);
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->double('e_lat')->default(0);
            $table->double('e_lng')->default(0);
            $table->longText('start_location')->nullable(); 
            $table->longText('end_location')->nullable(); 
            $table->smallInteger('flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_reports');
    }
};
