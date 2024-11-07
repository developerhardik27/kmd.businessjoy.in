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
        Schema::create('bj_partners', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_tax_id_number')->nullable();
            $table->mediumText('company_address')->nullable();
            $table->mediumText('company_area')->nullable();
            $table->mediumText('company_pincode')->nullable();
            $table->mediumText('company_country')->nullable();
            $table->mediumText('company_state')->nullable();
            $table->mediumText('company_city')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('contact_person_mobile')->nullable();
            $table->timestamps();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bj_partners');
    }
};
