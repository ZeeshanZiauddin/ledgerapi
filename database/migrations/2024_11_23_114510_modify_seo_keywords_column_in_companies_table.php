<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Check if the 'seo_keywords' column exists before modifying it
            if (Schema::hasColumn('companies', 'seo_keywords')) {
                // Modify 'seo_keywords' column to TEXT
                $table->text('seo_keywords')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            // Revert the column back to string if rolling back
            if (Schema::hasColumn('companies', 'seo_keywords')) {
                $table->string('seo_keywords')->change();
            }
        });
    }
};
