<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid()->after('id')->nullable();
        });

        User::lazy()->each(function (User $user) {
            $user->uuid = Str::uuid();
            $user->save();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid()->after('id')->change();
        });
    }
};
