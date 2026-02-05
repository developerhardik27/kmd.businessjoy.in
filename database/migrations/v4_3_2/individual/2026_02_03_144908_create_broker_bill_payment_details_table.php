<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('broker_bill_payment_details', function (Blueprint $table) {
            $table->id();
            $table->integer('inv_id');
            $table->string('receipt_number', 20);
            $table->string('transaction_id', 50)->nullable();
            $table->dateTime('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('paid_by', 30)->nullable();
            $table->string('paid_type', 30)->nullable();
            $table->double('amount', 15, 2);
            $table->double('paid_amount', 15, 2);
            $table->double('pending_amount', 15, 2);
            $table->integer('part_payment')->default(0);
            $table->double('tds_amount', 15, 2)->default(0);
            $table->string('challan_no', 50)->nullable();
            $table->string('tds_status', 50)->nullable();
            $table->tinyInteger('tds_credited')->default(0);
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
        Schema::dropIfExists('broker_bill_payment_details');
    }
};
