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
                        
            $table->integer('operation_id')->unsigned();
            $table->integer('balance_real_id')->unsigned();
            $table->integer('balance_real_amount');

            $table->integer('balance_account_id')->unsigned();
            $table->integer('balance_account_amount');
            $table->boolean('in_sync');
            

            $table->timestamps();

            $table->index('account_id');
            $table->foreign('account_id')->references('id')->on('app_accounts');

            $table->index('balance_real_id');
            $table->foreign('balance_real_id')->references('id')->on('app_balances_real');

            $table->index('operation_id');
            $table->foreign('operation_id')->references('id')->on('app_operations');

            $table->index('balance_account_id');
            $table->foreign('balance_account_id')->references('id')->on('app_balances_accounts');
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
