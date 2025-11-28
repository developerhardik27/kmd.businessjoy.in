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
        Schema::create('transporter_billing_payment', function (Blueprint $table) {
            $table->id();
            $table->integer('transporter_billing_id');
            $table->string('receipt_number',20);
            $table->string('transaction_id', 50)->nullable();
            $table->dateTime('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('paid_by', 30)->nullable();
            $table->string('paid_type', 30)->nullable();
            $table->double('amount', 10, 2);
            $table->double('paid_amount', 10, 2);
            $table->double('pending_amount', 10, 2);
            $table->integer('part_payment')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporter_billing_payment');
    }
};
