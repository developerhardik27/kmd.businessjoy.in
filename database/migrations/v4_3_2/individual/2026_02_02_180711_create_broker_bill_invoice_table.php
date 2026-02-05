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
        Schema::create('broker_bill_invoice', function (Blueprint $table) {
            $table->id();
            $table->integer('garden_id');
            $table->integer('company_id');
            $table->integer('garden_company_id');
            $table->text('invoice_no')->nullable();
            $table->text('invoice_date');
            $table->double('totalamount',15,2);
            $table->double('igst',15,2);
            $table->double('grand_total',15,2);
            $table->text('status');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
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
        Schema::dropIfExists('broker_bill_invoice');
    }
};
