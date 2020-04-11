<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppAccountsPeriodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_accounts_periods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('account_id')->unsigned();
			$table->integer('settlement_date')->unsigned();
			$table->date('closed_date');
			$table->date('estimated_date');
			$table->integer('closed_amount')->unsigned()->nullable();
			$table->boolean('is_checked')->default(0);
			$table->boolean('is_paid')->default(0);
			$table->timestamps();
			$table->unique(['account_id','settlement_date']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('app_accounts_periods');
	}

}
