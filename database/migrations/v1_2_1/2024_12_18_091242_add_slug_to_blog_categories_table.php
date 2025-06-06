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
        Schema::table('blog_categories', function (Blueprint $table) {
            if(!Schema::hasColumn('blog_categories','slug')){
                $table->string('slug',50)->nullable()->after('cat_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            if(Schema::hasColumn('blog_categories','slug')){
                $table->dropColumn('slug');
            }
        });
    }
};
