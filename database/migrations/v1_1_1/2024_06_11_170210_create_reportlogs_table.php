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
        if(!Schema::hasTable('reportlogs')){
            Schema::create('reportlogs', function (Blueprint $table) {
                $table->id(); 
                $table->string('module_name')->nullable();
                $table->date('from_date');
                $table->date('to_date'); 
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP')); 
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
        Schema::dropIfExists('reportlogs');
    }
};
