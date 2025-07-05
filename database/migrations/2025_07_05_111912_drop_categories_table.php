<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the categories table
        if (Schema::hasTable('categories')) {
            Schema::dropIfExists('categories');
        }

        // Drop the category_book pivot table
        if (Schema::hasTable('category_book')) {
            Schema::dropIfExists('category_book');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
