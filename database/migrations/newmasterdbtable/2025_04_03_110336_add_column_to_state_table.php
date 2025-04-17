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
        Schema::table('state', function (Blueprint $table) {
            if(!Schema::hasColumn('state','state_code')){
                $table->integer('state_code')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('state', function (Blueprint $table) {
            if(Schema::hasColumn('state','state_code')){
                $table->dropColumn('state_code');
            }
        });
    }
};
