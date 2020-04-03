<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppBalancesRealTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_balances_real', function(Blueprint $table)
		{
			$table->foreign('last_operation_id')->references('id')->on('app_operations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_balances_real', function(Blueprint $table)
		{
			$table->dropForeign('app_balances_real_last_operation_id_foreign');
		});
	}

}
