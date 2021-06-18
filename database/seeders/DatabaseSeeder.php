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
            AdminSeeder::class,
            RoleAndPermissionSeeder::class,
            ProductTypesSeeder::class,
            ProductCategoriesSeeder::class,
            FabricColorsSeeder::class,
            SizesSeeder::class,
            FabricTypesSeeder::class,
            WeltedEdgesColorsSeeder::class,
            ThreadColorsSeeder::class,

            //relation tables
            FabricTypeColorsSeeder::class,
            ProductCategoryTypesSeeder::class,
            ProductFabricTypesSeeder::class,
            
            //new
            SettingsSeeder::class,
            
       ]);
    }
}
