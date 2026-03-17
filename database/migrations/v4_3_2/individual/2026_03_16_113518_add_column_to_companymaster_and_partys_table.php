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
        // companymasters table
        if (Schema::hasTable('companymasters')) {
            Schema::table('companymasters', function (Blueprint $table) {
                if (!Schema::hasColumn('companymasters', 'tmco')) {
                    $table->string('tmco')->nullable();
                }
            });
        }

        // partys table
        if (Schema::hasTable('partys')) {
            Schema::table('partys', function (Blueprint $table) {

                if (!Schema::hasColumn('partys', 'code')) {
                    $table->string('code')->nullable();
                }

                if (!Schema::hasColumn('partys', 'tmco')) {
                    $table->string('tmco')->nullable();
                }

                if (!Schema::hasColumn('partys', 'c')) {
                    $table->string('c')->default('0');
                }

                if (!Schema::hasColumn('partys', 'bill_to')) {
                    $table->string('bill_to')->nullable();
                }

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // companymasters table
        if (Schema::hasTable('companymasters')) {
            Schema::table('companymasters', function (Blueprint $table) {
                if (Schema::hasColumn('companymasters', 'tmco')) {
                    $table->dropColumn('tmco');
                }
            });
        }

        // partys table
        if (Schema::hasTable('partys')) {
            Schema::table('partys', function (Blueprint $table) {

                $columns = [];

                if (Schema::hasColumn('partys', 'code')) {
                    $columns[] = 'code';
                }

                if (Schema::hasColumn('partys', 'tmco')) {
                    $columns[] = 'tmco';
                }

                if (Schema::hasColumn('partys', 'c')) {
                    $columns[] = 'c';
                }

                if (Schema::hasColumn('partys', 'bill_to')) {
                    $columns[] = 'bill_to';
                }

                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};