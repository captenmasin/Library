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
        Schema::table('book_user', function (Blueprint $table) {
            if (Schema::hasColumn('book_user', 'read_at')) {
                Schema::dropColumns('book_user', ['read_at']);
            }

            $table->enum('status', ['reading', 'completed', 'dropped', 'on_hold', 'plan_to_read'])
                ->default('plan_to_read')
                ->after('book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_user', function (Blueprint $table) {
            //
        });
    }
};
