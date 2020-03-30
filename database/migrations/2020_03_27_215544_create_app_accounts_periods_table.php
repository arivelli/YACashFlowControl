<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppAccountsPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_accounts_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->mediumInteger('settlement_date')->unsigned();
            $table->date('closed_date');
            $table->date('estimated_date');
            $table->integer('closed_amount')->nullable()->unsigned();
            $table->boolean('is_checked')->default(0);
            $table->boolean('is_paid')->default(0);

            $table->timestamps();
            $table->unique(['account_id', 'settlement_date']);
            $table->foreign('account_id')->references('id')->on('app_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_accounts_periods');
    }
}
