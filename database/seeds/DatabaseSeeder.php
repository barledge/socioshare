<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        $this->call(ScheduledPostsTableSeeder::class);
        $this->call(SocialAccountsTableSeeder::class);
    }
}
