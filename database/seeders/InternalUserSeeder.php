<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InternalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('internal.users')->truncate();

        DB::table('internal.users')->insert([
            'email' => "chandra888lim@gmail.com",
            'password' => Hash::make('chandra123'),
            'can_login' => true,
            'role' => 'Owner',
            'created_at' => \App\Helpers\MyLib::manualMillis(date("Y-m-d H:i:s")),
            'updated_at' => \App\Helpers\MyLib::manualMillis(date("Y-m-d H:i:s")),
            // 'temp_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}
