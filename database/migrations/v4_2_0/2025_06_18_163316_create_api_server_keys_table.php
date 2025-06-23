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
        Schema::create('api_server_keys', function (Blueprint $table) {
            $table->id();
            $table->string('server_key')->unique();
            $table->string('module')->nullable();
            $table->string('title')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable(); 
            $table->timestamps();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_server_keys');
    }
};
