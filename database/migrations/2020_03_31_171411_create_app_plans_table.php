<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->unsigned()->index('entry_id');
			$table->integer('account_type')->unsigned();
			$table->integer('account_id')->unsigned()->index('account_id');
			$table->smallInteger('plan');
			$table->boolean('frequency_id')->nullable()->index('frequency_id');
			$table->string('currency_plan', 3);
			$table->boolean('detail_format');
			$table->integer('amount');
			$table->integer('total_in_dollars')->nullable();
			$table->date('first_execution');
			$table->integer('first_execution_settlement')->unsigned();
			$table->boolean('estimated_based_on')->nullable()->default(1);
			$table->string('estimated_offset', 10)->nullable();
			$table->boolean('notification_to');
			$table->string('notification_offset', 10);
			$table->boolean('is_completed');
			$table->boolean('is_proccesed');
			$table->string('notes', 1000)->nullable();
			$table->boolean('compute_operations_flag');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('app_plans');
	}

}
