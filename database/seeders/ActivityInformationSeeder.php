<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityInformation;

class ActivityInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActivityInformation::create([
            'activity_name' => 'กิจกรรมอื่น ๆ',
            'start_date' => '2565-01-01',
            'location' => ' มหาวิทยาลัยราชภัฏสุรินทร์',
            'organizer' => ' มหาวิทยาลัยราชภัฏสุรินทร์',
            'description' => 'กิจกรรมจิตอาสาอื่น ๆ ที่นักศึกษาเข้าร่วมในมหาวิทยาลัยราชภัฏสุรินทร์',
            'limits' => 1000,
            'regis_due' => '2568-01-01',
            'end_date' => '2570-01-01',
            'user_id' => 1, 
        ]);
 
    }
}
