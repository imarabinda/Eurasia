<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeltedEdgesColor;
use Illuminate\Support\Facades\DB;

class WeltedEdgesColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('welted_edges_colors')->insert([
            ['name' => 'Natural',
            ],
            
            ['name' => 'Ivory',
            ],
            
            ['name' => 'Dark Grey',
            ],

            ['name' => 'Light Grey',
            ],
            
            ['name' => 'Beige',
            ],
            
            ]);
    }
}
