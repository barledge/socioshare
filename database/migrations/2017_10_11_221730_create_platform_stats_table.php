<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlatformStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('platform_stats', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('platform')->nullable();
			$table->bigInteger('followers')->nullable();
			$table->bigInteger('following')->nullable();
			$table->bigInteger('posts')->nullable();
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
		Schema::drop('platform_stats');
	}

}
