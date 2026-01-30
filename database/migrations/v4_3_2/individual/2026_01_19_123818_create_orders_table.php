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
            $table->unsignedBigInteger('buyer_party');
            $table->unsignedBigInteger('transport')->nullable();
            $table->string('credit_days')->nullable();
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('totalNetKg', 12, 2)->default(0);
            $table->decimal('totalAmount', 12, 2)->default(0);
            $table->decimal('discountAmount', 12, 2)->default(0);
            $table->decimal('finalAmount', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_delete')->default(0);
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
