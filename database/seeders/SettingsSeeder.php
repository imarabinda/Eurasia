<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        
        \App\Models\Setting::insert([
            [
                'name'=>'default_rate',
                'value'=> '40.00',
            ]
        ]);
    }
}
