<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('posts')->delete();
        
        \DB::table('posts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'message' => 'Hello, this is an example post that will be posted on facebook, twitter and instagram',
                'files' => '[]',
                'is_draft' => 0,
                'fine_tune' => 0,
                'position' => 1,
                'groups' => '3',
                'link_preview' => NULL,
                'social_account_list' => '[36,33,28]',
                'scheduled_at' => NULL,
                'created_at' => '2017-10-10 15:09:44',
                'updated_at' => '2017-10-10 15:09:44',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}