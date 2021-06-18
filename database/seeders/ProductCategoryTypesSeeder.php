<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoryTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_category_types')->insert([
            ['product_category_id' => 1,
            'product_type_id'=>1],
            ['product_category_id' => 1,
            'product_type_id'=>2],
            ['product_category_id' => 1,
            'product_type_id'=>3]
            
            ]);
    }
}
