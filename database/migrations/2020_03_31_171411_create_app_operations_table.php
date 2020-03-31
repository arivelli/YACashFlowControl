<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppOperationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_operations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->unsigned()->index('entries_id');
			$table->integer('account_id')->unsigned()->index('account_id');
			$table->integer('entry_type')->unsigned();
			$table->integer('area_id')->unsigned();
			$table->integer('category_id')->unsigned();
			$table->integer('estimated_amount');
			$table->date('estimated_date');
			$table->integer('settlement_date')->unsigned();
			$table->string('settlement_week', 20);
			$table->integer('plan_id')->unsigned();
			$table->integer('number')->unsigned();
			$table->string('currency', 3);
			$table->integer('amount');
			$table->boolean('is_done')->default(0);
			$table->string('detail', 250);
			$table->date('operation_date')->nullable();
			$table->integer('operation_amount')->nullable();
			$table->integer('dollar_value');
			$table->integer('in_dollars')->nullable();
			$table->string('notes', 1000)->nullable();
			$table->integer('created_by')->unsigned();
			$table->timestamps();
			$table->integer('updated_by')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('app_operations');
	}

}
