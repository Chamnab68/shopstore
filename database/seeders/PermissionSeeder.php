<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
   public function run(): void
{
    // បន្ថែម field 'module' ទៅតាមលក្ខខណ្ឌ database របស់បង
    $permissions = [
        [
            'name' => 'View Analytics',
            'description' => 'Can view loyalty metrics and churn reports',
            'module' => 'analytics'
        ],
        [
            'name' => 'Manage Products',
            'description' => 'Can CRUD products and stock inventory',
            'module' => 'catalog'
        ],
        [
            'name' => 'Manage Rewards',
            'description' => 'Can create and edit loyalty rewards catalog',
            'module' => 'loyalty'
        ],
        [
            'name' => 'Manage Tiers',
            'description' => 'Can configure membership tier thresholds',
            'module' => 'loyalty'
        ],
        [
            'name' => 'View Activity Logs',
            'description' => 'Can monitor system audit logs and trail data',
            'module' => 'system'
        ],
    ];

    foreach ($permissions as $perm) {
        Permission::updateOrCreate(['name' => $perm['name']], $perm);
    }
}
}