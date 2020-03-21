<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreteTableAppBalancesReal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_balances_real', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('settlement_date')->unsigned();
            $table->string('grouped_by',25);
            $table->integer('foreign_id')->unsigned();

            $table->integer('last_operation_id')->unsigned();

            $table->integer('amount');
            $table->timestamps();

            $table->unique(['settlement_date', 'grouped_by' ,'foreign_id']);
            $table->foreign('last_operation_id')->references('id')->on('app_operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_balances_real');
    }
}
