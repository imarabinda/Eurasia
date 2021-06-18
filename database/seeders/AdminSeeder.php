<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([[
            'name' => 'eurasia',
            'email' => 'eurasiadecor1@gmail.com',
            'email_verified_at'=> now(),
            'password' => '$2y$10$pyeo1S0A/E9CVrnoejxnbeK2OLDF.fRn2qKQTudXDlN0eph7fsb1W',
            'remember_token'=>Str::random(10),
            'is_active'=>1,
            'first_name'=>'Eurasia',
            'last_name'=>'Decor',
            'phone'=>'6290063557',
            'created_at'=> now()
        ],
        [
            'name' => 'arabinda',
            'email' => 'arabinda.gamerx@gmail.com',
            'first_name'=>'Arabinda',
            'last_name'=>'Baidya',
            'email_verified_at'=>now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token'=>Str::random(10),
            'is_active'=>1,
            'phone'=>'8334012642',
            'created_at'=>now()
        ]]);
    }
}
