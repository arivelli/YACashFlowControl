<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppOperationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_operations', function(Blueprint $table)
		{
			$table->foreign('entry_id', 'app_operations_ibfk_1')->references('id')->on('app_entries')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('parent_id', 'app_operations_ibfk_2')->references('id')->on('app_operations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('account_id', 'app_operations_ibfk_3')->references('id')->on('app_accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('area_id', 'app_operations_ibfk_4')->references('id')->on('app_areas')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('category_id', 'app_operations_ibfk_5')->references('id')->on('app_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_operations', function(Blueprint $table)
		{
			$table->dropForeign('app_operations_ibfk_1');
			$table->dropForeign('app_operations_ibfk_2');
			$table->dropForeign('app_operations_ibfk_3');
			$table->dropForeign('app_operations_ibfk_4');
			$table->dropForeign('app_operations_ibfk_5');
		});
	}

}
