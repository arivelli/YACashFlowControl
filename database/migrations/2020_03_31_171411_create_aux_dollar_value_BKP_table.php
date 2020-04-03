<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuxDollarValueBKPTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('aux_dollar_value_BKP', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('date')->unique('fecha');
			$table->smallInteger('buyer')->unsigned();
			$table->smallInteger('seller')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('aux_dollar_value_BKP');
	}

}
