<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FabricType;
use Illuminate\Support\Facades\DB;

class FabricTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fabric_types')->insert([
            ['name' => 'Cotton',
            ],
            
            ['name' => 'Linen',
            ],
            
            ]);
    }
}
