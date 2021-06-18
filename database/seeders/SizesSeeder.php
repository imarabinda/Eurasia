<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;
use Illuminate\Support\Facades\DB;

class SizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('sizes')->insert([
            ['height' => '12',
            'width'=>'18',
            ],
             
            ['height' => '12',
            'width'=>'20',
            ],
            
             
            ['height' => '14',
            'width'=>'20',
            ], 
            ['height' => '14',
            'width'=>'24',
            ], 
            
            ['height' => '14',
            'width'=>'14',
            ],

            ['height' => '16',
            'width'=>'16',
            ],

             
            ['height' => '18',
            'width'=>'18',
            ],
             
            ['height' => '20',
            'width'=>'20',
            ],
            
            ]);
        
    }
}
