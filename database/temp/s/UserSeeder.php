<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([
            'username' => "chandra",
            'password' => Hash::make('password1234'),
            'can_login'=> true,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);

        DB::table('users')->insert([
            'username' => "rian",
            'password' => Hash::make('1234password'),
            'can_login'=> true,
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);
        
        DB::table('users')->insert([
            'username' => "koakin",
            'password' => Hash::make('password'),
            'can_login'=> true,
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);

        DB::table('users')->insert([
            'username' => "ria",
            'password' => Hash::make('password'),
            'can_login'=> true,
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);

        DB::table('users')->insert([
            'username' => "anti",
            'password' => Hash::make('password'),
            'can_login'=> true,
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);

        DB::table('users')->insert([
            'username' => "dina",
            'password' => Hash::make('password'),
            'can_login'=> true,
            'created_by'=> '1',
            'updated_by'=> '1',
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
