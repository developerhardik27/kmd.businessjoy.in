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
        if(!Schema::hasTable('blogs')){ 
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title',255)->nullable();
                $table->mediumText('content')->nullable();
                $table->string('slug',255)->nullable();
                $table->string('tag_ids',255)->nullable();
                $table->string('cat_ids',255)->nullable();
                $table->string('meta_dsc',255)->nullable();
                $table->string('meta_keywords',255)->nullable();
                $table->string('img',100)->nullable();
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('blogs');
    }
};
