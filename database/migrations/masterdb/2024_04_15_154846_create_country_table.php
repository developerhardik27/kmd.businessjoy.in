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
        Schema::create('country', function (Blueprint $table) {
            $table->id();
            $table->string('country_name',50)->nullable();
            $table->string('iso',3)->nullable();
            $table->string('numric_code',3)->nullable();
            $table->string('iso',2)->nullable();
            $table->string('phonecode',10)->nullable();
            $table->string('capital',100)->nullable();
            $table->string('currency',100)->nullable();
            $table->string('currency_name',100)->nullable();
            $table->string('currency_symbol',100)->nullable();
            $table->string('nationality',255)->nullable();
            $table->string('timezones',100)->nullable();
            $table->timestamps();
            $table->tinyInteger('flag',1)->nullable();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country');
    }
};
