<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBooksTableAddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('books', 'settings')) {
            Schema::table('books', function (Blueprint $table) {
                $table->json('settings')->after('description')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('books', 'settings')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('settings');
            });
        }
    }
}
