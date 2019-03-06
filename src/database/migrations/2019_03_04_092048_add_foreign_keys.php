<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->integer('refStatusFrom')->unsigned()->nullable(true);
            $table->foreign('refStatusFrom')->references('id')->on('workflow_status');
            $table->integer('refStatusTo')->unsigned();
            $table->foreign('refStatusTo')->references('id')->on('workflow_status');
            $table->integer('refAction')->unsigned();
            $table->foreign('refAction')->references('id')->on('workflow_actions');
            $table->integer('refModule')->unsigned();
            $table->foreign('refModule')->references('id')->on('workflow_modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropForeign(['refStatusFrom']);
            $table->dropForeign(['refStatusTo']);
            $table->dropForeign(['refAction']);
            $table->dropForeign(['refModule']);
        });
    }
}
