<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FabricTypeColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fabric_type_colors')->insert([
            ['fabric_type_id' => 2,
            'fabric_color_id'=>1],
            
            ['fabric_type_id' => 1,
            'fabric_color_id'=>2],
            
            ['fabric_type_id' => 1,
            'fabric_color_id'=>3],
            
            ['fabric_type_id' => 1,
            'fabric_color_id'=>4],
            
            ['fabric_type_id' => 1,
            'fabric_color_id'=>5],
            
            ['fabric_type_id' => 1,
            'fabric_color_id'=>6],

            ['fabric_type_id' => 1,
            'fabric_color_id'=>7],
            
            ]);
    }
}
