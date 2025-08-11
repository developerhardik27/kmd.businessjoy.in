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
        Schema::create('lead_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('country')->default(0)->comment('0 - hide, 1 - show');
            $table->string('country_default_value')->nullable();
            $table->integer('state')->default(0)->comment('0 - hide, 1 - show');
            $table->string('state_default_value')->nullable();
            $table->integer('city')->default(0)->comment('0 - hide, 1 - show');
            $table->string('city_default_value')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_settings');
    }
};
