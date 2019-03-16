<?php

use Illuminate\Database\Seeder;

class SocialAccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('social_accounts')->delete();
        
        \DB::table('social_accounts')->insert(array (
            0 => 
            array (
                'id' => 13,
                'platform' => 'instagram',
                'username' => 'simpsonspaper',
                'access_token' => '{"password":""}',
                'auth_data' => NULL,
                'label' => 'simpsonspaper',
                'platform_id' => NULL,
                'user_id' => NULL,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-08-26 18:45:59',
                'updated_at' => '2017-08-26 18:45:59',
            ),
            1 => 
            array (
                'id' => 28,
                'platform' => 'facebook',
                'username' => 'Kevin Darren',
                'access_token' => '{"token":""}',
                'auth_data' => '{"type":"group"}',
                'label' => 'OfficeSpace',
                'platform_id' => '1929432303980177',
                'user_id' => NULL,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-08-26 21:57:10',
                'updated_at' => '2017-08-31 12:51:53',
            ),
            2 => 
            array (
                'id' => 30,
                'platform' => 'facebook',
                'username' => 'LooneyTunes',
                'access_token' => '{"token":""}',
                'auth_data' => '{"type":"page"}',
                'label' => 'LooneyTunes',
                'platform_id' => '272545653214268',
                'user_id' => NULL,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-08-26 22:05:23',
                'updated_at' => '2017-08-26 22:05:23',
            ),
            3 => 
            array (
                'id' => 31,
                'platform' => 'facebook',
                'username' => 'TheSimpsons',
                'access_token' => '{"token":""}',
                'auth_data' => '{"type":"profile"}',
                'label' => 'TheSimpsons',
                'platform_id' => '2131183607108490',
                'user_id' => NULL,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-08-30 18:14:44',
                'updated_at' => '2017-08-30 18:14:44',
            ),
            4 => 
            array (
                'id' => 33,
                'platform' => 'instagram',
                'username' => 'officespace',
                'access_token' => '{"password":"="}',
                'auth_data' => NULL,
                'label' => 'officespace',
                'platform_id' => NULL,
                'user_id' => NULL,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-09-13 16:56:00',
                'updated_at' => '2017-09-13 16:56:00',
            ),
            5 => 
            array (
                'id' => 34,
                'platform' => 'instagram',
                'username' => 'thesimpsons',
                'access_token' => '{"password":""}',
                'auth_data' => NULL,
                'label' => 'thesimpsons',
                'platform_id' => NULL,
                'user_id' => 9,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-10-08 23:22:24',
                'updated_at' => '2017-10-08 23:31:30',
            ),
            6 => 
            array (
                'id' => 36,
                'platform' => 'twitter',
                'username' => 'LooneyTunes',
                'access_token' => '{"oauth_token":"","oauth_token_secret":"","user_id":"","screen_name":"","x_auth_expires":"0"}',
                'auth_data' => NULL,
                'label' => 'officespace',
                'platform_id' => NULL,
                'user_id' => 1,
                'posts' => 0,
                'proxy' => NULL,
                'needs_reauth' => 0,
                'created_at' => '2017-10-10 13:33:19',
                'updated_at' => '2017-10-10 13:33:19',
            ),
        ));
        
        
    }
}