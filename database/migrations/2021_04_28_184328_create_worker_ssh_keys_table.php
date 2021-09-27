<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkerSshKeysTable extends Migration
{
    public function up()
    {
        Schema::create('worker_ssh_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->text('public_key');
            $table->text('private_key');
            $table->string('name');
            // ID issued by a server provider after we add the key to the user's account.
            $table->string('external_id')->nullable();

            $table->timestamps();
        });
    }
}
