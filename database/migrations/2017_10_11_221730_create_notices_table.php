<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNoticesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notices', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('message')->nullable();
			$table->text('response')->nullable();
			$table->string('error')->nullable();
			$table->string('platform')->nullable();
			$table->string('type')->nullable();
			$table->integer('type_id')->nullable();
			$table->integer('social_account_id')->nullable();
			$table->string('social_account_label')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('notices');
	}

}
