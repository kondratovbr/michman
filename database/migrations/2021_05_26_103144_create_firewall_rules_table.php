<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirewallRulesTable extends Migration
{
    public function up()
    {
        Schema::create('firewall_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->references('id')->on('servers');

            $table->string('name');
            $table->string('port');
            $table->string('from_ip')->nullable();
            $table->boolean('can_delete')->nullable();
            $table->string('status');

            $table->timestamps();
        });
    }
}
