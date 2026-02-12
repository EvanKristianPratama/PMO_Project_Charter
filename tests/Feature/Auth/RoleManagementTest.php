<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_role_management_page(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');

        Permission::findOrCreate('projects.view');
        Permission::findOrCreate('projects.update');

        $response = $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'FinanceLead',
            'permissions' => ['projects.view', 'projects.update'],
        ]);

        $response->assertRedirect();

        /** @var Role $role */
        $role = Role::query()->where('name', 'FinanceLead')->firstOrFail();

        $this->assertTrue($role->hasPermissionTo('projects.view'));
        $this->assertTrue($role->hasPermissionTo('projects.update'));
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');

        $role = Role::findOrCreate('PMO');
        Permission::findOrCreate('projects.view');
        Permission::findOrCreate('projects.delete');

        $role->syncPermissions(['projects.view']);

        $response = $this->actingAs($admin)->put(route('admin.roles.permissions.update', $role), [
            'permissions' => ['projects.delete'],
        ]);

        $response->assertRedirect();

        $role->refresh();
        $this->assertFalse($role->hasPermissionTo('projects.view'));
        $this->assertTrue($role->hasPermissionTo('projects.delete'));
    }

    public function test_non_admin_cannot_manage_roles(): void
    {
        $user = $this->createUser(appRole: User::APP_ROLE_USER, permissionRole: 'Admin');

        $response = $this->actingAs($user)->post(route('admin.roles.store'), [
            'name' => 'Analyst',
        ]);

        $response->assertForbidden();
    }

    private function createUser(string $appRole, string $permissionRole): User
    {
        $this->seedPermissionRoles();

        $user = User::factory()->create([
            'status' => 'approved',
            'app_role' => $appRole,
        ]);

        $user->syncRoles([$permissionRole]);

        return $user;
    }

    private function seedPermissionRoles(): void
    {
        foreach (['Admin', 'Viewer', 'PMO'] as $roleName) {
            Role::findOrCreate($roleName);
        }
    }
}
