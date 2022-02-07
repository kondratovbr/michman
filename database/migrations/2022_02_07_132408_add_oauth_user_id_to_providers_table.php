<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOAuthUserIdToProvidersTable extends Migration
{
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->foreignId('oauth_user_id')->nullable()->references('id')->on('oauth_users');
        });
    }
}
