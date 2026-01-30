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
        Schema::create('broker_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garden_id');
            $table->string('invoice_no');
            $table->unsignedBigInteger('grade');
            $table->unsignedBigInteger('bags');
            $table->decimal('net_kg', 12, 2)->default(0);
            $table->unsignedBigInteger('brokerage')->default(0);
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
        Schema::dropIfExists('broker_purchases');
    }
};
