<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppAccountsPeriodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_accounts_periods', function(Blueprint $table)
		{
			$table->foreign('account_id')->references('id')->on('app_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_accounts_periods', function(Blueprint $table)
		{
			$table->dropForeign('app_accounts_periods_account_id_foreign');
		});
	}

}
