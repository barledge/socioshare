<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSocialAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_accounts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('platform')->nullable();
			$table->string('username')->nullable();
			$table->text('access_token', 65535)->nullable();
			$table->text('auth_data')->nullable();
			$table->string('label')->nullable();
			$table->string('platform_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('posts')->nullable()->default(0);
			$table->string('proxy')->nullable();
			$table->boolean('needs_reauth')->nullable()->default(0);
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
		Schema::drop('social_accounts');
	}

}
