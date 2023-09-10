<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->truncate();
        
        DB::table('items')->insert([
            'code'  => 'P001',
            'name'  => 'Laptop', 
            'brand' => 'Test',
            'model' => 'test',
            'type'  => 'test',
            'size'  => 10,
            'color'  => 'Red',
            'stock_min' => 10,
            // 'stock_max' => 40,
            'capital_price' => 1000,
            'unit_code' => 'g',
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('items')->insert([
            'code'  => '000',
            'name'  => 'kosong', 
            'brand' => 'kosong',
            'model' => 'kosong',
            'type'  => 'kosong',
            'size'  => 10,
            'color'  => 'Red',
            'stock_min' => 10,
            // 'stock_max' => 40,
            'capital_price' => 1000,
            'unit_code' => 'kosong',
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
