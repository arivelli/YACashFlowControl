<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAppEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('app_entries', function(Blueprint $table)
		{
			$table->foreign('area_id', 'app_entries_ibfk_1')->references('id')->on('app_areas')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('category_id', 'app_entries_ibfk_2')->references('id')->on('app_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('app_entries', function(Blueprint $table)
		{
			$table->dropForeign('app_entries_ibfk_1');
			$table->dropForeign('app_entries_ibfk_2');
		});
	}

}
