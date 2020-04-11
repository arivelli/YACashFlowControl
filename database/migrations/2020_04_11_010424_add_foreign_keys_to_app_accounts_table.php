<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_accounts', function(Blueprint $table)
		{
			$table->foreign('entry_id', 'app_accounts_ibfk_1')->references('id')->on('app_entries')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('plan_id', 'app_accounts_ibfk_2')->references('id')->on('app_plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_accounts', function(Blueprint $table)
		{
			$table->dropForeign('app_accounts_ibfk_1');
			$table->dropForeign('app_accounts_ibfk_2');
		});
	}

}
