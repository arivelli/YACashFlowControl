<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreteTableAppBalancesInSync extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_balances_in_sync', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->integer('balances_id')->unsigned();
            $table->integer('operation_id')->unsigned();

            $table->integer('amount')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('app_accounts');
            $table->foreign('balances_id')->references('id')->on('app_balances_real');
            $table->foreign('operation_id')->references('id')->on('app_operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_balances_in_sync');
    }
}
