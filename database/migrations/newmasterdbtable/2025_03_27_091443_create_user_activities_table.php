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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('username')->nullable();  // Username
            $table->string('ip')->nullable();  // IP address
            $table->string('country')->nullable();  // Country based on IP
            $table->string('device')->nullable();  // Device type (mobile/desktop/tablet)
            $table->string('browser')->nullable();  // Browser name
            $table->enum('status', ['success', 'fail'])->nullable();  // Login status
            $table->enum('via', ['direct', 'superadmin'])->nullable();  // Via - Direct/SuperAdmin
            $table->integer('company_id')->nullable();  // Company ID
            $table->string('message')->nullable();  // Company ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
