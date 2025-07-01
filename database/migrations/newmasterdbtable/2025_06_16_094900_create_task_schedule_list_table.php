<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_schedule_list', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('command')->unique();
            $table->string('description')->nullable();
            $table->string('schedule'); 
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('task_schedule_list');
    }
};
