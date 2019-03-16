<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSocialAccountStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_account_stats', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('social_account_id')->nullable();
			$table->integer('posts')->nullable();
			$table->integer('total_posts')->nullable();
			$table->date('day')->nullable();
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
		Schema::drop('social_account_stats');
	}

}
