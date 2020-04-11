<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppBalancesRealTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_balances_real', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('settlement_date')->unsigned();
			$table->string('grouped_by', 15);
			$table->integer('foreign_id')->unsigned();
			$table->integer('last_operation_id')->unsigned()->index('app_balances_real_last_operation_id_foreign');
			$table->integer('amount');
			$table->timestamps();
			$table->unique(['settlement_date','grouped_by','foreign_id']);
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
