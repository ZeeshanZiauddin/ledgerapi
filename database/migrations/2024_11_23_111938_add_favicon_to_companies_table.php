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
        Schema::table('companies', function (Blueprint $table) {
            // Add favicon column
            if (!Schema::hasColumn('companies', 'favicon')) {
                $table->string('favicon')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Drop favicon column
            if (Schema::hasColumn('companies', 'favicon')) {
                $table->dropColumn('favicon');
            }
        });
    }
};
