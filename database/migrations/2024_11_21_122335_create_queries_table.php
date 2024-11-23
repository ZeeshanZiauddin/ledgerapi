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
        Schema::create('queries', function (Blueprint $table) {
            $table->id();
            $table->string('departure');
            $table->string('arrival');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->json('passengers'); // Store passenger counts as JSON
            $table->json('date_range'); // Store date range as JSON
            $table->unsignedBigInteger('company_id'); // Add company_id as a foreign key
            $table->timestamps();

            // Add foreign key constraint to company_id, referencing the companies table
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::dropIfExists('queries');
    }
};
