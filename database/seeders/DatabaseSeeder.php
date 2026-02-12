<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedAdminUser();
    }

    private function seedRoles(): void
    {
        $roles = ['Admin', 'PMO', 'Architect', 'DevLead', 'Viewer'];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }

    private function seedAdminUser(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@pmo.local'],
            [
                'name'     => 'Admin PMO',
                'password' => bcrypt('password'),
                'status'   => 'approved',
                'app_role' => User::APP_ROLE_ADMIN,
            ]
        );

        $admin->forceFill(['app_role' => User::APP_ROLE_ADMIN])->save();
        $admin->syncRoles(['Admin']);
    }
}
