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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('garden_id');
            $table->string('invoice_no');
            $table->unsignedBigInteger('grade')->nullable();
            $table->decimal('bags', 12, 2)->default(0);
            $table->decimal('kg', 12, 2)->default(0);
            $table->decimal('net_kg', 12, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_delete')->default(0);
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
