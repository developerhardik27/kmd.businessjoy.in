<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('mobile', 20)->unique();
            $table->text('address')->nullable();

            $table->text('bank_details')->nullable();
            $table->string('cv_resume')->nullable();
            $table->json('id_proofs')->nullable();
            $table->json('address_proofs')->nullable();
            $table->json('other_attachments')->nullable();


            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->boolean('is_active')->default(1);
            $table->boolean('is_deleted')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
