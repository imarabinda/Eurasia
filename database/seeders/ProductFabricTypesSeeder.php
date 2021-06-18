<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductFabricTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_fabric_types')->insert([
            //embroided
            ['product_type_id' => 1,
            'fabric_type_id'=>1],
            
            ['product_type_id' => 1,
            'fabric_type_id'=>2],
            //printed
            ['product_type_id' => 2,
            'fabric_type_id'=>1],
            
            ['product_type_id' => 2,
            'fabric_type_id'=>2]
            
           
            
            ]);
    }
}
