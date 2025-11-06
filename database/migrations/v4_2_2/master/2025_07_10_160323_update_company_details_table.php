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
         Schema::table('company_details', function (Blueprint $table) {
            $table->text('img')->change();
            $table->text('pr_sign_img')->change();
            $table->text('watermark_img')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('company_details', function (Blueprint $table) {
            $table->string('img')->change();
            $table->string('pr_sign_img')->change();
            $table->string('watermark_img')->change();
        });
    }
};
