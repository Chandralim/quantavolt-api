<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class FakeY extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // DB::table('members')->insert([
        //     'username' => "chandra888lim@gmail.com",
        // ]);
        $faker = Faker::create('id_ID');
        for ($i = 1; $i <= 200; $i++) {

            // insert data ke table pegawai menggunakan Faker
            DB::table('members')->insert([
                'username' => $faker->username,
                'fullname' => $faker->name,
            ]);
        }
    }
}
