<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_accounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('entry_id')->unsigned()->nullable()->index('entry_id');
			$table->integer('plan_id')->unsigned()->nullable()->index('plan_id');
			$table->string('name', 25);
			$table->string('bank', 30)->nullable();
			$table->boolean('type')->default(1);
			$table->string('cbu', 50);
			$table->string('number', 50)->nullable();
			$table->string('currency', 3);
			$table->string('notes', 5000)->nullable();
			$table->boolean('is_active')->default(1);
			$table->timestamps();
			$table->integer('created_by')->unsigned()->default(1);
			$table->integer('updated_by')->unsigned()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('app_accounts');
	}

}
