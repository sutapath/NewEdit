<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('officers')->insert([
            ['citizen_id' => '1449100012201',],
            ['citizen_id' => '1449100012202',],
            ['citizen_id' => '1449100012203',],
            ['citizen_id' => '1449100012204',],
            ['citizen_id' => '1449100012205',],
        ]);
    }
}
