<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing table
        Schema::dropIfExists('purchases');

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();  
            $table->integer('supplier_id')->nullable();
            $table->string('payment_terms')->nullable();
            $table->integer('currency')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->string('shipping_carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('reference_number')->nullable();
            $table->mediumText('note_to_supplier')->nullable();
            $table->double('taxes')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('shipping')->nullable();
            $table->double('discount')->nullable();
            $table->double('total')->nullable();
            $table->string('status')->default('draft'); 
            $table->integer('total_items')->default(0);  
            $table->integer('accepted')->default(0); 
            $table->integer('rejected')->default(0); 
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
        // Drop the table in case of rollback
        Schema::dropIfExists('purchases');
    }
};
