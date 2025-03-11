<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('students')->insert([
            ['citizen_id' => '1449100012101',],
            ['citizen_id' => '1449100012102',],
            ['citizen_id' => '1449100012103',],
            ['citizen_id' => '1449100012104',],
            ['citizen_id' => '1449100012105',],
            ['citizen_id' => '1449100012106',],
            ['citizen_id' => '1449100012107',],
            ['citizen_id' => '1449100012108',],
            ['citizen_id' => '1449100012109',],
            ['citizen_id' => '1449100012110',],
            ['citizen_id' => '1449100012111',],
            ['citizen_id' => '1449100012112',],
            ['citizen_id' => '1449100012113',],
            ['citizen_id' => '1449100012114',],
            ['citizen_id' => '1449100012115',],
        ]);
    }
}
