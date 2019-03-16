<?php

use Illuminate\Database\Seeder;

class ScheduledPostsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('scheduled_posts')->delete();
        
        \DB::table('scheduled_posts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'post_id' => 1,
                'social_account_id' => 36,
                'message' => 'Hello, this is an example post that will be posted on facebook, twitter and instagram',
                'files' => '[]',
                'position' => 1,
                'active' => 1,
                'status' => 'UNSENT',
                'link_preview' => NULL,
                'error_log' => NULL,
                'queued_for' => '2017-10-10 17:07:00',
                'scheduled_at' => NULL,
                'posted_at' => NULL,
                'created_at' => '2017-10-10 15:09:44',
                'updated_at' => '2017-10-10 15:09:44',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'post_id' => 1,
                'social_account_id' => 33,
                'message' => 'Hello, this is an example post that will be posted on facebook, twitter and instagram',
                'files' => '[]',
                'position' => 2,
                'active' => 1,
                'status' => 'UNSENT',
                'link_preview' => NULL,
                'error_log' => NULL,
                'queued_for' => '2017-10-10 17:07:00',
                'scheduled_at' => NULL,
                'posted_at' => NULL,
                'created_at' => '2017-10-10 15:09:44',
                'updated_at' => '2017-10-10 15:09:44',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'post_id' => 1,
                'social_account_id' => 28,
                'message' => 'Hello, this is an example post that will be posted on facebook, twitter and instagram',
                'files' => '[]',
                'position' => 3,
                'active' => 1,
                'status' => 'UNSENT',
                'link_preview' => NULL,
                'error_log' => NULL,
                'queued_for' => '2017-10-10 17:07:00',
                'scheduled_at' => NULL,
                'posted_at' => NULL,
                'created_at' => '2017-10-10 15:09:44',
                'updated_at' => '2017-10-10 15:09:44',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}