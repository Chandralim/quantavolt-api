<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('quotation_items')->truncate();
        
        DB::table('quotation_items')->insert([
            'code'  => 'R001',
            'name' => 'Test',
            'brand' => 'Test',
            'size'  => 10,
            'model' => 'test',
            'type'  => 'test',
            'unit_code' => 'g',
            'purchase_price' => 2000,
            'shipping_cost'  => 2000,
            'percent'        => 20,
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
