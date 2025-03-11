<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder; 
use Illuminate\Support\Facades\DB;

class MembersTableSeeder extends Seeder
{
    // Seed the members table
    public function run()
    {
        DB::table('members')->insert([
            ['id' => 1, 'citizen_id' => '1449100012301'],
            ['id' => 2, 'citizen_id' => '1449100012302'],
            ['id' => 3, 'citizen_id' => '1449100012303'],
            ['id' => 4, 'citizen_id' => '1449100012304'],
            ['id' => 5, 'citizen_id' => '1449100012305'],
        ]);
    }
}
