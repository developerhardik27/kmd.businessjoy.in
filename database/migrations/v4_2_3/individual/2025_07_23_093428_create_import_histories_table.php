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
        Schema::create('import_histories', function (Blueprint $table) {
            $table->id();
            $table->dateTime('import_date'); 
            $table->integer('total_count'); 
            $table->integer('imported_count')->default(0); 
            $table->longText('success_rows')->nullable(); 
            $table->integer('fail_count')->default(0);
            $table->longText('error_rows')->nullable(); 
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
        Schema::dropIfExists('import_histories');
    }
};
