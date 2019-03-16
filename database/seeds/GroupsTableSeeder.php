<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('groups')->delete();
        
        \DB::table('groups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Acme Corporation',
                'user_id' => NULL,
                'selection' => '{"13":true,"36":true}',
                'created_at' => '2017-10-10 13:30:18',
                'updated_at' => '2017-10-10 13:33:44',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Globex Corporation',
                'user_id' => 2,
                'selection' => '{"30":true,"31":true,"34":true}',
                'created_at' => '2017-10-10 13:32:10',
                'updated_at' => '2017-10-10 13:33:39',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Initech',
                'user_id' => 3,
                'selection' => '{"28":true,"33":true,"36":true}',
                'created_at' => '2017-10-10 13:32:37',
                'updated_at' => '2017-10-10 13:33:33',
            ),
        ));
        
        
    }
}