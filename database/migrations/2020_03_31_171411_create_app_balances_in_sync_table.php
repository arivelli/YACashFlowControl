<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppBalancesInSyncTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_balances_in_sync', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('account_id')->unsigned()->index();
			$table->integer('operation_id')->unsigned()->index();
			$table->integer('balance_real_id')->unsigned()->index();
			$table->integer('balance_real_amount');
			$table->integer('balance_account_id')->unsigned()->index();
			$table->integer('balance_account_amount');
			$table->boolean('in_sync');
			$table->timestamps();
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
