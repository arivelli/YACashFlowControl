<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppBalancesAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_balances_accounts', function(Blueprint $table)
		{
			$table->foreign('account_id', 'app_balances_accounts_ibfk_1')->references('id')->on('app_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_balances_accounts', function(Blueprint $table)
		{
			$table->dropForeign('app_balances_accounts_ibfk_1');
		});
	}

}
