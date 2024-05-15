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
        Schema::create('reminder', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->dateTime('next_reminder_date')->nullable();
            $table->mediumText('before_service_note')->nullable();
            $table->mediumText('after_service_note')->nullable();
            $table->string('reminder_status', 30)->default('pending');
            $table->string('service_type', 50)->nullable();
            $table->integer('amount')->nullable();
            $table->dateTime('service_completed_date')->nullable();
            $table->string('product_unique_id',50)->nullable();
            $table->string('product_name',50)->nullable();
            $table->mediumText('assigned_to')->nullable();
            $table->integer('assigned_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder');
    }
};
