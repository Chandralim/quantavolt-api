<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DbSeeder20230907 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dt =[
            [
                "name"=>"project_material_item",
                "ordinal"=>"1",
                "action"=>"view",
                "description"=>"Melihat Data Material Item Kebutuhan Proyek",
            ],
            [
                "name"=>"project_material_item",
                "ordinal"=>"2",
                "action"=>"add",
                "description"=>"Menambah Data Material Item Kebutuhan Proyek",
            ],
            [
                "name"=>"project_material_item",
                "ordinal"=>"3",
                "action"=>"edit",
                "description"=>"Mengubah Data Material Item Kebutuhan Proyek",
            ],
            [
                "name"=>"project_material_item",
                "ordinal"=>"4",
                "action"=>"remove",
                "description"=>"Menghapus Data Material Item Kebutuhan Proyek",
            ],

        ];

        \App\Helpers\MyLog::logging("run","permissions");

        foreach ($dt as $k => $v) {
            if(!DB::table('action_permissions')
            ->where("name",$v['name'])
            ->where("ordinal",$v['ordinal'])
            ->where("action",$v['action'])
            ->where("description",$v['description'])
            ->first())
            if (DB::table('action_permissions')->insert([
                "name"=>$v['name'],
                "ordinal"=>$v['ordinal'],
                "action"=>$v['action'],
                "description"=>$v['description'],
            ]))
            \App\Helpers\MyLog::logging("insert","permissions");
            
        }

        if(
            DB::table('data_permissions')
            ->where("table_name","project_working_tool")
            ->where("field_name","working_tool_code")
            ->update([
                "field_name"=>"item_code",
            ])
        )
        \App\Helpers\MyLog::logging("update 1","permissions");


        if(DB::table('data_permissions')
        ->where("table_name","project_working_tool")
        ->where("field_name","working_tool_name")
        ->update([
            "field_name"=>"item_name",
        ]))
        \App\Helpers\MyLog::logging("update 2","permissions");
        


        

        



        // foreach ($dt as $k => $v) {
        //     DB::table('data_permissions')->insert([
        //         "ordinal"=>$v['ordinal'],
        //         "status"=>$v['status'],
        //         "table_name"=>$v['table_name'],
        //         "field_name"=>$v['field_name'],
        //         "description"=>$v['description'],
        //     ]);
        // }
    }
}
