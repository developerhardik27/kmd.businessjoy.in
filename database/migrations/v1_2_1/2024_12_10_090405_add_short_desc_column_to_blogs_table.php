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
        Schema::table('blogs', function (Blueprint $table) {
            if(!Schema::hasColumn('blogs','short_desc')){
                $table->text('short_desc')->after('img')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if(Schema::hasColumn('blogs','short_desc')){
                $table->dropColumn('blogs');
            }
        });
    }
};
