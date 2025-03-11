<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // เรียกใช้งาน Seeder ตามลำดับที่แนะนำ
        // $this->call(PermissionSeeder::class);
        // $this->call(RoleSeeder::class);
        $this->call(RoleHasPermissionSeeder::class);
        // // $this->call(AdminSeeder::class);
        // $this->call(OfficersTableSeeder::class);
        // $this->call(ScholarsTableSeeder::class);
        // $this->call(StudentsTableSeeder::class);
    }
}
