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
        Schema::create('page_load_logs', function (Blueprint $table) {
            $table->id();
            $table->string('page_url')->nullable();
            $table->string('controller')->nullable();
            $table->string('method')->nullable();
            $table->string('view_name')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->float('load_time')->nullable();
            $table->timestamp('date_time')->nullable();
            $table->string('username')->nullable();
            $table->string('db_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_load_logs');
    }
};
