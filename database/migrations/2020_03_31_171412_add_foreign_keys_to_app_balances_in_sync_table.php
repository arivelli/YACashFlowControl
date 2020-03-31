<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppBalancesInSyncTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_balances_in_sync', function(Blueprint $table)
		{
			$table->foreign('account_id')->references('id')->on('app_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('balance_account_id')->references('id')->on('app_balances_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('balance_real_id')->references('id')->on('app_balances_real')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('operation_id')->references('id')->on('app_operations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_balances_in_sync', function(Blueprint $table)
		{
			$table->dropForeign('app_balances_in_sync_account_id_foreign');
			$table->dropForeign('app_balances_in_sync_balance_account_id_foreign');
			$table->dropForeign('app_balances_in_sync_balance_real_id_foreign');
			$table->dropForeign('app_balances_in_sync_operation_id_foreign');
		});
	}

}
