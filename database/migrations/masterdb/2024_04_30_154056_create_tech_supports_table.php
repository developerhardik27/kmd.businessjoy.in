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
        Schema::create('tech_supports', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->bigInteger('contact_no')->nullable();
            $table->string('module_name', 50)->nullable();
            $table->mediumText('description')->nullable();
            $table->string('attachment')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('status', 30)->default('Open');
            $table->longText('remarks')->nullable();
            $table->string('ticket', 50)->nullable();
            $table->mediumText('assigned_to')->nullable();
            $table->integer('assigned_by')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tech_supports');
    }
};
