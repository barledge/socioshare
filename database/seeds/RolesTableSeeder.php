<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => '2017-10-07 23:34:52',
                'updated_at' => '2017-10-07 23:34:52',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'client',
                'guard_name' => 'web',
                'created_at' => '2017-10-07 23:34:52',
                'updated_at' => '2017-10-07 23:34:52',
            ),
        ));
        
        
    }
}