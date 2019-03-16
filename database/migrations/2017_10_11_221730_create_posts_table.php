<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('message')->nullable();
			$table->text('files')->nullable();
			$table->boolean('is_draft')->nullable();
			$table->boolean('fine_tune')->nullable();
			$table->integer('position')->nullable();
			$table->text('groups')->nullable();
			$table->text('link_preview')->nullable();
			$table->text('social_account_list', 65535)->nullable();
			$table->dateTime('scheduled_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('posts');
	}

}
