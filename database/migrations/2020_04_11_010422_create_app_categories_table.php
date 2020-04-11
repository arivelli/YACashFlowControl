<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('category', 50);
			$table->boolean('affect_capital')->nullable()->default(0);
			$table->boolean('is_extraordinary')->nullable()->default(0);
			$table->string('notes', 5000)->nullable();
			$table->boolean('is_active')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('app_categories');
	}

}
