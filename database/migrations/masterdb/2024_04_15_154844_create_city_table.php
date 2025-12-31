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
        Schema::create('city', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('state_id');
            $table->string('city_name', 50);
            $table->unsignedBigInteger('created_by')->default(1);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('is_active')->default(1); 
            $table->tinyInteger('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city');
    }
};
