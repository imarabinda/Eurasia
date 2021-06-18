<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SettingsSeeder::class,
            ExtraRoleAndPermissionSeeder::class,
       ]);
    }
}
