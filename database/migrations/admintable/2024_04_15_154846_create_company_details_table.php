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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('email',50);
            $table->bigInteger('contact_no');
            $table->mediumText('address');
            $table->string('gst_no',50)->nullable();
            $table->integer('country');
            $table->integer('state');
            $table->integer('city');
            $table->integer('pincode');
            $table->string('img',50)->nullable();
            $table->string('pr_sign_img',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
