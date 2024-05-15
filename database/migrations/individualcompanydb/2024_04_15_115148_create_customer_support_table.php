<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_support', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->bigInteger('contact_no')->nullable();
            $table->string('title', 100)->nullable();
            $table->string('budget', 100)->nullable();
            $table->string('audience_type', 50)->nullable();
            $table->string('customer_type', 50)->nullable();
            $table->string('status', 30)->default('Open');
            $table->dateTime('last_call')->nullable();
            $table->dateTime('next_call')->nullable();
            $table->integer('number_of_call')->default(0);
            $table->longText('notes')->nullable();
            $table->string('ticket', 50)->nullable();
            $table->string('web_url', 150)->nullable();
            $table->mediumText('assigned_to')->nullable();
            $table->integer('assigned_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
            $table->string('source', 100)->nullable();
            $table->string('ip', 100)->nullable();
            $table->text('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_support');
    }
};
