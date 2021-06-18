<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;

class ProductTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_types')->insert([
            ['name' => 'Embroidered',
            ],
            
            ['name' => 'Printed',
            ],

            ['name' => 'Hand crafted',
            ],
            ]);
    }
}
