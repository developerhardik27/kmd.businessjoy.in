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
        Schema::create('transporter_billing', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->nullable();  
            $table->date('bill_date')->default(now());  
            $table->string('lr_no')->nullable();  
            $table->string('con_no')->nullable();  
            $table->string('vehicle_no')->nullable();  
            $table->integer('party')->nullable();  
            $table->double('amount', 10, 2)->nullable();
            $table->string('status', 50)->default('pending');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps(); 
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporter_billing');
    }
};
