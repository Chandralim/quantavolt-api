<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternalUserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('internal.user_permissions')->truncate();

        $aps = DB::table('internal.action_permissions')->get();
        foreach ($aps as $key => $value) {
            DB::table('internal.user_permissions')->insert([
                "user_id"=>1,
                "action_permission_id"=>$value->id,
                "created_by"=>1,
            ]);
        }



        // for ($i=1; $i <= 41 ; $i++) { 
        //     DB::table('user_permissions')->insert([
        //         "user_id"=>1,
        //         "data_permission_id"=>$i,
        //         "created_by"=>1,
        //     ]);
            
        // }
       
    }
}
