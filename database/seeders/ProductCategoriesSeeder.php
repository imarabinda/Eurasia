<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('product_categories')->insert([
            ['name' => 'Pillow Cover',
             'image'=>'no-image.png',
             ],
            
            ['name' => 'Placemats',
             'image'=>'no-image.png',
            ],

            ['name' => 'Runner',
             'image'=>'no-image.png',
            ],

            ['name' => 'Curtain',
             'image'=>'no-image.png',
            ],
            ]);
    }

}
