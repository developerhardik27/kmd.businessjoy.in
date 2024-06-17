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
        Schema::create('invoice_number_patterns', function (Blueprint $table) {
            $table->id();
            $table->text('invoice_pattern')->nullable();
            $table->integer('start_increment_number')->nullable();
            $table->integer('current_increment_number')->nullable();
            $table->string('pattern_type',10)->nullable();
            $table->string('increment_type',30)->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP')); 
            $table->integer('created_by');  
            $table->integer('is_deleted')->default(0);  
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_number_patterns');
    }
};
