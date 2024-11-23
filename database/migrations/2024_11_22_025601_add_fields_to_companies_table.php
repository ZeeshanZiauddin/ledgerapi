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
            if (!Schema::hasColumn('companies', 'email')) {
                $table->string('email')->unique()->after('api_key');
            }

            if (!Schema::hasColumn('companies', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (!Schema::hasColumn('companies', 'logo')) {
                $table->string('logo')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('companies', 'favicon')) {
                $table->string('favicon')->nullable()->after('logo');
            }

            if (!Schema::hasColumn('companies', 'location')) {
                $table->string('location')->nullable()->after('logo');
            }

            if (!Schema::hasColumn('companies', 'status')) {
                $table->boolean('status')->default(true)->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('companies', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('companies', 'logo')) {
                $table->dropColumn('logo');
            }
            if (Schema::hasColumn('companies', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('companies', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

