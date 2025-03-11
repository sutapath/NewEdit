<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
      Permission::create(['name' => 'scholarships.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'scholarships.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'scholarships.update', 'guard_name' => 'web']);
        Permission::create(['name' => 'scholarships.delete', 'guard_name' => 'web']);
        
        Permission::create(['name' => 'applyscholars.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'applyscholars.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'applyscholars.update', 'guard_name' => 'web']);
        Permission::create(['name' => 'applyscholars.delete', 'guard_name' => 'web']);
        
        Permission::create(['name' => 'activities.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'activity.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'activities.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'activities.update', 'guard_name' => 'web']);
        Permission::create(['name' => 'activities.delete', 'guard_name' => 'web']);
        
        Permission::create(['name' => 'activity_saves.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'activity_saves.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'activity_saves.update', 'guard_name' => 'web']);
        Permission::create(['name' => 'activity_saves.delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'activity_saves.update_status', 'guard_name' => 'web']);
        
        Permission::create(['name' => 'view.activitysave', 'guard_name' => 'web']);
        Permission::create(['name' => 'create.activitysave', 'guard_name' => 'web']);
        Permission::create(['name' => 'update.activitysave', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete.activitysave', 'guard_name' => 'web']);
        
        Permission::create(['name' => 'docs_sends.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'docs_sends.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'docs_sends.update', 'guard_name' => 'web']);
        Permission::create(['name' => 'docs_sends.delete', 'guard_name' => 'web']);
    






}
}
