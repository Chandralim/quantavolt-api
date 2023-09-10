<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->truncate();
        
        DB::table('projects')->insert([
            'no'  => 'C001',
            'title'  => '3D miniatur kantor bupati', 
            'date' => date("Y-m-d H:i:s"),
            'location' => 'Jakarta',
            'customer_code'  => '001',
            'type'  => 'jasa',
            'date_start'  => date("Y-m-d H:i:s"),
            'date_finish'  => date("Y-m-d H:i:s"),
            'status' => 'selesai',
            'note' => '40',
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
