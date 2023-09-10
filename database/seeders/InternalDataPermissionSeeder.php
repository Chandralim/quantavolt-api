<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternalDataPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('internal.data_permissions')->truncate();

        $dt =[
            // Quotation Item
            // [
            //     "table_name"  => "quotation_item",
            //     "field_name"  => "purchase_price",
            //     "ordinal"     => "1",
            //     "status"      => "hide",
            //     "description" => "Hide Harga Beli",
            // ],
            // [
            //     "table_name"  => "quotation_item",
            //     "field_name"  => "shipping_cost",
            //     "ordinal"     => "2",
            //     "status"      => "hide",
            //     "description" => "Hide Biaya Pengiriman",
            // ],
            // [
            //     "table_name"  => "quotation_item",
            //     "field_name"  => "percent",
            //     "ordinal"     => "3",
            //     "status"      => "hide",
            //     "description" => "Hide Persen",
            // ],
            // [
            //     "table_name"  => "quotation_item",
            //     "field_name"  => "percent",
            //     "ordinal"     => "4",
            //     "status"      => "manage",
            //     "description" => "Manage Persen",
            // ],
        ];



        foreach ($dt as $k => $v) {
            DB::table('internal.data_permissions')->insert([
                "ordinal"=>$v['ordinal'],
                "status"=>$v['status'],
                "table_name"=>$v['table_name'],
                "field_name"=>$v['field_name'],
                "description"=>$v['description'],
            ]);
        }
    }
}
