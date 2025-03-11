<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder; 
use Illuminate\Support\Facades\DB;

class IntlscholarsTableSeeder extends Seeder
{
    // Seed the Intlscholars table
    public function run()
    {
        DB::table('intlscholars')->insert([
            ['id' => 1, 'citizen_id' => '1449100012401'],
            ['id' => 2, 'citizen_id' => '1449100012402'],
            ['id' => 3, 'citizen_id' => '1449100012403'],
            ['id' => 4, 'citizen_id' => '1449100012404'],
            ['id' => 5, 'citizen_id' => '1449100012405'],
            ['id' => 6, 'citizen_id' => '1449100012406'],
            ['id' => 7, 'citizen_id' => '1449100012407'],
            ['id' => 8, 'citizen_id' => '1449100012408'],
            ['id' => 9, 'citizen_id' => '1449100012409'],
            ['id' =>10, 'citizen_id' => '1449100012410'],
            ['id' =>11, 'citizen_id' => '1449100012411'],
            ['id' => 12, 'citizen_id' => '1449100012412'],
            ['id' => 13, 'citizen_id' => '1449100012413'],
            ['id' => 14, 'citizen_id' => '1449100012414'],
            ['id' => 15, 'citizen_id' => '1449100012415'],
        ]);
    }
}
