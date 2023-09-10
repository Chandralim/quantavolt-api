<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->truncate();
        
        DB::table('customers')->insert([
            'code'        => 'C001',
            'name'        => 'Andre Nasution',
            'address'     => 'Medan',
            'phone_number'=> '085261702211',
            // 'fax_number'  => '0877176617',
            'hp_number'   => '085261702211',
            'note'        => '',
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
