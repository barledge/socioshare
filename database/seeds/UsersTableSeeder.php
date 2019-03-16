<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => '$2y$10$sBizSfoKvfcyjxd635nE8ulB1T0ZmIDq9RdfjUxI.kB2I2jv4e4KO',
                'facebook_app_key' => NULL,
                'facebook_app_secret' => NULL,
                'remember_token' => 'xAf7g8BMN0fBVjq8YV3jv8hrEfJV24Sj2YbM1iyUvnGTq9Y8eEbrDWYbggL3',
                'created_at' => '2017-08-10 12:42:17',
                'updated_at' => '2017-08-30 11:30:05',
                'current_team_id' => 2,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Globex Corporation',
                'email' => 'client@example.com',
                'password' => '$2y$10$oUZZBpTSkRuDu6dOy0gf3eN.4HOibkX9gAegduF26LJw7wqBWrHEm',
                'facebook_app_key' => NULL,
                'facebook_app_secret' => NULL,
                'remember_token' => NULL,
                'created_at' => '2017-10-10 13:32:10',
                'updated_at' => '2017-10-10 13:32:10',
                'current_team_id' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Initech',
                'email' => 'test@example.com',
                'password' => '$2y$10$o.xZmtvEp0616Z3skwoAiOu5ivncwg/H0p3tdHzVGZM1G7igknlcu',
                'facebook_app_key' => NULL,
                'facebook_app_secret' => NULL,
                'remember_token' => NULL,
                'created_at' => '2017-10-10 13:32:37',
                'updated_at' => '2017-10-10 13:32:37',
                'current_team_id' => NULL,
            ),
        ));
        
        
    }
}