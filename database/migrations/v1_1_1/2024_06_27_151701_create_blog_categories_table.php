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
        if(!Schema::hasTable('blog_categories')){ 
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('cat_name',50);
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0); 
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
