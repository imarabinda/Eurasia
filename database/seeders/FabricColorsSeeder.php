<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FabricColor;
use Illuminate\Support\Facades\DB;

class FabricColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fabric_colors')->insert([
            ['name' => 'Natural',
            ],
            
            ['name' => 'Tan',
            ],
            
            ['name' => 'Navy Blue',
            ],
            
            ['name' => 'Ivory',
            ],
            
            ['name' => 'Dark Grey',
            ],

            ['name' => 'Medium Grey',
            ],
            
            ['name' => 'Teal',
            ],
            
            ]);
    }
}
