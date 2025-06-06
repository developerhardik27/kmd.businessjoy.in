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
        Schema::table('user_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_permissions', 'created_by')) {
                $table->integer('created_by');
            }
            if (!Schema::hasColumn('user_permissions', 'updated_by')) {
                $table->integer('updated_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('user_permissions', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('user_permissions', 'updated_by')) {
                $table->dropColumn('updated_by')->nullable();
            }
        });
    }
};
