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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();

            // Letter fields
            $table->string('letter_name');
            $table->string('header_image');
            $table->string('header_align');
            $table->integer('header_width');
            $table->text('header_content');

            $table->text('body_content');

            $table->string('footer_image');
            $table->string('footer_align');
            $table->integer('footer_width');
            $table->text('footer_content');

            // Tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_delete')->default(false);

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
