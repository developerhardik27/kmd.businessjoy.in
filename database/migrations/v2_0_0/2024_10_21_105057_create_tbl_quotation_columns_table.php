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
        Schema::create('tbl_quotation_columns', function (Blueprint $table) {
            $table->id();
            $table->string('column_name', 50);
            $table->string('column_type', 50);
            $table->string('column_width')->default(0);
            $table->integer('column_order')->nullable();
            $table->tinyInteger('is_hide')->default(0);
            $table->integer('company_id');
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
        Schema::dropIfExists('tbl_quotation_columns');
    }
};
