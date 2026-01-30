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
        Schema::create('company_garden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companymasters')->onDelete('cascade');
            $table->foreignId('garden_id')->constrained('gardens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_garden');
    }
};
