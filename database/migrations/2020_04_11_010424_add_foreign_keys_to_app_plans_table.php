<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_plans', function(Blueprint $table)
		{
			$table->foreign('entry_id', 'app_plans_ibfk_1')->references('id')->on('app_entries')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('account_id', 'app_plans_ibfk_2')->references('id')->on('app_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('frequency_id', 'app_plans_ibfk_3')->references('id')->on('aux_frequencies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_plans', function(Blueprint $table)
		{
			$table->dropForeign('app_plans_ibfk_1');
			$table->dropForeign('app_plans_ibfk_2');
			$table->dropForeign('app_plans_ibfk_3');
		});
	}

}
