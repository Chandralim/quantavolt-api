<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // InternalUserSeeder::class,
            InternalActionPermissionSeeder::class,
            // InternalDataPermissionSeeder::class,
            InternalUserPermissionSeeder::class,
            // UnitSeeder::class,
            // QuotationItemSeeder::class,
            // ItemSeeder::class,
            // CustomerSeeder::class,
            // ProjectSeeder::class,
            // DbSeeder20230907::class,
        ]);
    }
}
