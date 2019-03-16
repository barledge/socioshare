<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTeamInvitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('team_invites', function(Blueprint $table)
		{
			$table->foreign('team_id')->references('id')->on('teams')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('team_invites', function(Blueprint $table)
		{
			$table->dropForeign('team_invites_team_id_foreign');
		});
	}

}
