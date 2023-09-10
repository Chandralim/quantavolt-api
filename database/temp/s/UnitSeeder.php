<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->truncate();

        DB::table('units')->insert([
            'code' => 'm',
            'name' => 'Meter',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'kosong',
            'name' => 'kosong',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'ml',
            'name' => 'Mili liter',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'km',
            'name' => 'Kilometer',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'l',
            'name' => 'Liter',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'pcs',
            'name' => 'Picis',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'kl',
            'name' => 'Kaleng',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'g',
            'name' => 'gram',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'inc',
            'name' => 'inchi',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'btl',
            'name' => 'Botol',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'alt',
            'name' => 'alat',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'mkn',
            'name' => 'makan',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'atk',
            'name' => 'alat tulis kantor',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
        DB::table('units')->insert([
            'code' => 'sbn',
            'name' => 'Sabun',
            'created_by'=> '2',
            'updated_by'=> '2',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
