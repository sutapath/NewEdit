<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolePermissions = [


// admin
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 1, 'permission_id' => 2],
            ['role_id' => 1, 'permission_id' => 3],
            ['role_id' => 1, 'permission_id' => 4],
            ['role_id' => 1, 'permission_id' => 6],
            ['role_id' => 1, 'permission_id' => 7],
            ['role_id' => 1, 'permission_id' => 9],
            ['role_id' => 1, 'permission_id' => 10],
            ['role_id' => 1, 'permission_id' => 11],
            ['role_id' => 1, 'permission_id' => 12],
            ['role_id' => 1, 'permission_id' => 13],
            ['role_id' => 1, 'permission_id' => 14],
            ['role_id' => 1, 'permission_id' => 18],


// officer
            ['role_id' => 2, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
            ['role_id' => 2, 'permission_id' => 4],
            ['role_id' => 2, 'permission_id' => 6],
            ['role_id' => 2, 'permission_id' => 7],
            ['role_id' => 2, 'permission_id' => 9],
            ['role_id' => 2, 'permission_id' => 10],
            ['role_id' => 2, 'permission_id' => 11],
            ['role_id' => 2, 'permission_id' => 12],
            ['role_id' => 2, 'permission_id' => 13],
            ['role_id' => 2, 'permission_id' => 14],
            ['role_id' => 2, 'permission_id' => 18],



// scholar
            ['role_id' => 3, 'permission_id' => 12],
            ['role_id' => 3, 'permission_id' => 13],
            ['role_id' => 3, 'permission_id' => 14],
            ['role_id' => 3, 'permission_id' => 15],
            ['role_id' => 3, 'permission_id' => 16],
            ['role_id' => 3, 'permission_id' => 17],





// student
            ['role_id' => 4, 'permission_id' => 5],
            ['role_id' => 4, 'permission_id' => 6],
            ['role_id' => 4, 'permission_id' => 7],
            ['role_id' => 4, 'permission_id' => 8],
            ['role_id' => 4, 'permission_id' => 11],


// member
            ['role_id' => 5, 'permission_id' => 5],
            ['role_id' => 5, 'permission_id' => 6],
            ['role_id' => 5, 'permission_id' => 7],
            ['role_id' => 5, 'permission_id' => 8],
            ['role_id' => 5, 'permission_id' => 9],
            ['role_id' => 5, 'permission_id' => 10],
            ['role_id' => 5, 'permission_id' => 11],
        ];

        // Insert the data into the `role_has_permissions` table
        DB::table('role_has_permissions')->insert($rolePermissions);
    }
}
