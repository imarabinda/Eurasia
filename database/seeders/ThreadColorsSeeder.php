<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThreadColor;
use Illuminate\Support\Facades\DB;

class ThreadColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        DB::table('thread_colors')->insert([
            
            ['name' => 'Dark Grey',
            'background'=>'#A9A9A9',
            'color_code'=>'35',
            'color'=>'white',],
            
            ['name' => 'Beige',
            'background'=>'#f5f5dc',
            'color_code'=>'136',
            'color'=>'black',],
            
            ['name' => 'Ivory',
            'background'=>'#fffff0',
            'color_code'=>'Kora',
            'color'=>'black',],
            
            ['name' => 'Pearl Grey',
            'background'=>'#b0b7be.',
            'color_code'=>'32L',
            'color'=>'black',],

            ['name' => 'Aqua Blue',
            'background'=>'#00FFFF',
            'color_code'=>'185',
            'color'=>'black',],

            ['name' => 'Gold',
            'background'=>'#FFD700',
            'color_code'=>'141',
            'color'=>'black',],

            ['name' => 'Lime Green',
            'background'=>'#32CD32',
            'color_code'=>'149',
            'color'=>'white',],

            ['name' => 'Medium Grey',
            'background'=>'#D3D3D3',
            'color'=>'black',
            'color_code'=>'262',],

            ['name' => 'Light Sea Green',
            'background'=>'#20b2aa',
            'color'=>'white',
            'color_code'=>'101 LL',],          
            
            ['name' => 'Teal',
            'background'=>'#008080',
            'color'=>'white',
            'color_code'=>'273',],          
            

            ['name' => 'Red',
            'background'=>'#ff0000',
            'color'=>'white',
            'color_code'=>'26L',],          
            
            ]);
    }
}
