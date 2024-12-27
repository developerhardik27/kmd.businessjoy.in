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
        Schema::create('quotation_other_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('overdue_day')->nullable();
            $table->date('year_start')->default('2024-04-01');
            $table->double('sgst')->nullable();
            $table->double('cgst')->nullable();
            $table->integer('gst')->default(0); 
            $table->integer('quotation_number')->default(0);
            $table->integer('quotation_date')->default(0);
            $table->integer('customer_id')->default(1);
            $table->integer('current_customer_id')->default(1);
            $table->integer('created_by');
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
        Schema::dropIfExists('quotation_other_settings');
    }
};
