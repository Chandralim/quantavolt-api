<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternalActionPermissionSeeder extends Seeder
{
    /**  
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('internal.action_permissions')->truncate();

        $dt = [
            [
                "name" => "user",
                "ordinal" => "1",
                "action" => "view",
                "description" => "Melihat Data Pengguna",
            ],
            [
                "name" => "user",
                "ordinal" => "2",
                "action" => "add",
                "description" => "Menambah Data Pengguna",
            ],
            [
                "name" => "user",
                "ordinal" => "3",
                "action" => "edit",
                "description" => "Mengubah Data Pengguna",
            ],
            [
                "name" => "user",
                "ordinal" => "4",
                "action" => "remove",
                "description" => "Menghapus Data Pengguna",
            ],
            [
                "name" => "user_permission",
                "ordinal" => "1",
                "action" => "view",
                "description" => "Melihat Data Izin Pengguna",
            ],
            [
                "name" => "user_permission",
                "ordinal" => "2",
                "action" => "add",
                "description" => "Menambah Data Izin Pengguna",
            ],
            [
                "name" => "user_permission",
                "ordinal" => "3",
                "action" => "edit",
                "description" => "Mengubah Data Izin Pengguna",
            ],
            [
                "name" => "user_permission",
                "ordinal" => "4",
                "action" => "remove",
                "description" => "Menghapus Data Izin Pengguna",
            ],
            [
                "name" => "institute",
                "ordinal" => "1",
                "action" => "view",
                "description" => "Melihat Data Institut",
            ],
            [
                "name" => "institute",
                "ordinal" => "2",
                "action" => "add",
                "description" => "Menambah Data Institut",
            ],
            [
                "name" => "institute",
                "ordinal" => "3",
                "action" => "edit",
                "description" => "Mengubah Data Institut",
            ],
            [
                "name" => "institute",
                "ordinal" => "4",
                "action" => "remove",
                "description" => "Menghapus Data Institut",
            ],
            [
                "name" => "member",
                "ordinal" => "1",
                "action" => "view",
                "description" => "Melihat Data Member",
            ],
            [
                "name" => "member",
                "ordinal" => "2",
                "action" => "add",
                "description" => "Menambah Data Member",
            ],
            [
                "name" => "member",
                "ordinal" => "3",
                "action" => "edit",
                "description" => "Mengubah Data Member",
            ],
            [
                "name" => "member",
                "ordinal" => "4",
                "action" => "remove",
                "description" => "Menghapus Data Member",
            ],
        ];

        foreach ($dt as $k => $v) {
            DB::table('internal.action_permissions')->insert([
                "name" => $v['name'],
                "ordinal" => $v['ordinal'],
                "action" => $v['action'],
                "description" => $v['description'],
            ]);
        }
    }
}
