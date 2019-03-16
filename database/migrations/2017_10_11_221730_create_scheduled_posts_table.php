<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScheduledPostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scheduled_posts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('post_id')->nullable();
			$table->integer('social_account_id')->nullable();
			$table->text('message')->nullable();
			$table->text('files', 65535)->nullable();
			$table->integer('position')->nullable()->default(0);
			$table->boolean('active')->nullable();
			$table->string('status')->default('UNSENT');
			$table->text('link_preview')->nullable();
			$table->string('error_log')->nullable();
			$table->string('external_id')->nullable();
			$table->dateTime('queued_for')->nullable();
			$table->dateTime('scheduled_at')->nullable();
			$table->dateTime('posted_at')->nullable();
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
		Schema::drop('scheduled_posts');
	}

}
