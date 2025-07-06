<?php

use App\Enums\UserBookStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            $table->text('status')
                ->default(UserBookStatus::PlanToRead->name)
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
