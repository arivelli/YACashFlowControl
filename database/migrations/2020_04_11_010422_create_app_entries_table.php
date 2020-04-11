<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_entries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('entry_type')->index('entry_type');
			$table->integer('category_id')->unsigned()->nullable()->index('category_id');
			$table->integer('area_id')->unsigned()->nullable()->index('area_id');
			$table->date('date');
			$table->string('concept', 50);
			$table->string('currency', 3);
			$table->integer('real_amount');
			$table->integer('one_pay_amount');
			$table->integer('dollar_value');
			$table->boolean('is_extraordinary')->default(1);
			$table->boolean('affect_capital')->default(1);
			$table->string('notes', 300)->nullable();
			$table->string('plan', 5000)->nullable();
			$table->boolean('is_done')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('created_by');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('app_entries');
	}

}
