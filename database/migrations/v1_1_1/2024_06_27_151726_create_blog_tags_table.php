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
        if (!Schema::hasTable('blog_tags')) {
            Schema::create('blog_tags', function (Blueprint $table) {
                $table->id();
                $table->string('tag_name', 100);
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
        Schema::dropIfExists('blog_tags');
    }
};
