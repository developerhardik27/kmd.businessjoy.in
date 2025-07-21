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
        Schema::create('blog_settings', function (Blueprint $table) {
            $table->id();
            $table->string('details_endpoint')->nullable();
            $table->string('img_allowed_filetype')->nullable();
            $table->string('img_max_size')->nullable();
            $table->string('img_width')->nullable();
            $table->string('img_height')->nullable();
            $table->string('thumbnail_img_width')->nullable();
            $table->string('thumbnail_img_height')->nullable();
            $table->integer('validate_dimension')->default(0)->comment('if 1 then do validate dimension during store and update blog');
            $table->integer('updated_by')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_settings');
    }
};
