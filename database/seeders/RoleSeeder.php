<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $officer = Role::create(['name' => 'officer']);
        $scholar = Role::create(['name' => 'scholar']);
        $student = Role::create(['name' => 'student']);
        $member = Role::create(['name' => 'member']);
        $member = Role::create(['name' => 'intlscholar']);
        $member = Role::create(['name' => 'manager']);
 
    }
}
