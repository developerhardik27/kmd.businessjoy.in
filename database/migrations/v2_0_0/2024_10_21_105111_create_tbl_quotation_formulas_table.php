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
        Schema::create('tbl_quotation_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('first_column', 50);
            $table->char('operation', 1);
            $table->string('second_column', 50);
            $table->string('output_column', 50);
            $table->integer('formula_order')->nullable();
            $table->integer('company_id');
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
        Schema::dropIfExists('tbl_quotation_formulas');
    }
};
