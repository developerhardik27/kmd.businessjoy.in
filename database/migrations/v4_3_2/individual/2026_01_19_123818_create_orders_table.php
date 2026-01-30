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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('buyer_party');
            $table->integer('transport')->nullable();
            $table->string('credit_days')->nullable();
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('totalNetKg', 12, 2)->default(0);
            $table->decimal('totalAmount', 12, 2)->default(0);
            $table->decimal('discountAmount', 12, 2)->default(0);
            $table->decimal('finalAmount', 12, 2)->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
