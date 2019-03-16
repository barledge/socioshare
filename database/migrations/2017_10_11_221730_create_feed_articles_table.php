<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedArticlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feed_articles', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title')->nullable();
			$table->text('description', 65535)->nullable();
			$table->text('content', 65535)->nullable();
			$table->string('image')->nullable();
			$table->string('image_bucket')->nullable();
			$table->string('source')->nullable();
			$table->string('source_url')->nullable();
			$table->string('link')->nullable();
			$table->dateTime('published_at')->nullable();
			$table->string('twitter_status')->nullable()->default('UNSENT');
			$table->string('facebook_status')->nullable()->default('UNSENT');
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
		Schema::drop('feed_articles');
	}

}
