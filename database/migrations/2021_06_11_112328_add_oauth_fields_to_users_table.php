<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOauthFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('oauth_provider')->nullable();
            $table->string('oauth_id')->nullable();

            // With OAuth in use passwords become optional - OAuth authentication works without a password.
            $table->string('password')->nullable()->change();
        });
    }
}
