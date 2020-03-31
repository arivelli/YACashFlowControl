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
		});
	}

}
