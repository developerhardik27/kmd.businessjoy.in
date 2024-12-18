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
        Schema::table('blog_tags', function (Blueprint $table) {
            if(!Schema::hasColumn('blog_tags','slug')){
                $table->string('slug',50)->nullable()->after('tag_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_tags', function (Blueprint $table) {
            if(!Schema::hasColumn('blog_tags','slug')){
                $table->dropColumn('slug',50);
            }
        });
    }
};
