<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTableAddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'settings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('settings')->after('remember_token')->nullable();
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
        if (Schema::hasColumn('users', 'settings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('settings');
            });
        }
    }
}
