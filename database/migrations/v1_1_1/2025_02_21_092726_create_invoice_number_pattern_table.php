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
        if(!Schema::hasTable('invoice_number_patterns')){ 
            Schema::create('invoice_number_patterns', function (Blueprint $table) {
                $table->id();
                $table->text('invoice_pattern')->nullable();
                $table->integer('start_increment_number')->nullable()->comment('where to start increment');
                $table->integer('current_increment_number')->nullable()->comment('use during increment by invoice');
                $table->string('pattern_type',10)->nullable()->comment('type-1 = local , type-2 = global');
                $table->string('increment_type',30)->nullable()->comment('type-1 = by invoice , type-2 = by customer');
                $table->timestamps();
                $table->integer('created_by');  
                $table->integer('is_deleted')->default(0); 
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_number_patterns');
    }
};
