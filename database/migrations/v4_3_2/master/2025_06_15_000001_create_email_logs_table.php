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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('report_name', 100)->comment('Name of the report (e.g., Prompt Report, Pending Sample Report)');
            $table->string('from_email', 255)->comment('Sender email address');
            $table->string('to_email', 255)->comment('Recipient email address');
            $table->string('email_subject', 255)->comment('Email subject line');
            $table->longText('email_content')->nullable()->comment('Email body content');
            $table->string('status', 20)->default('pending')->comment('Email status: pending, success, failed');
            $table->integer('sent_by')->comment('User ID who sent the email');
            $table->string('sent_by_name', 255)->nullable()->comment('Name of the user who sent the email');
            $table->dateTime('sent_at')->nullable()->comment('Timestamp when email was sent');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
