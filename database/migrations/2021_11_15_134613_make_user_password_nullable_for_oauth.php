<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // With OAuth in use passwords become optional - OAuth authentication works without a password.
            $table->string('password')->nullable()->change();
        });
    }
};
