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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('damaged')->default(0);
            $table->integer('quality_control')->default(0);
            $table->integer('safety_stock')->default(0);
            $table->integer('other')->default(0);
            $table->integer('available')->default(0);
            $table->integer('on_hand')->default(0);
            $table->integer('incoming')->default(0);
            $table->string('is_active')->default(1);
            $table->string('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
