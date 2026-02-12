<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserAccessAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard_from_dashboard_route(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = $this->createUser(appRole: User::APP_ROLE_USER, permissionRole: 'Admin');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_updating_permission_role_does_not_change_app_role(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');
        $target = $this->createUser(appRole: User::APP_ROLE_USER, permissionRole: 'Viewer');

        $response = $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'permission_role' => 'PMO',
        ]);

        $response->assertRedirect();

        $target->refresh();

        $this->assertSame(User::APP_ROLE_USER, $target->appRole());
        $this->assertTrue($target->hasRole('PMO'));
        $this->assertDatabaseHas('user_access_audits', [
            'user_id' => $target->id,
            'actor_id' => $admin->id,
            'event' => 'user_access_updated',
        ]);
    }

    public function test_updating_app_role_does_not_change_permission_role(): void
    {
        $admin = $this->createUser(appRole: User::APP_ROLE_ADMIN, permissionRole: 'Viewer');
        $target = $this->createUser(appRole: User::APP_ROLE_USER, permissionRole: 'PMO');

        $response = $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'app_role' => User::APP_ROLE_ADMIN,
        ]);

        $response->assertRedirect();

        $target->refresh();

        $this->assertSame(User::APP_ROLE_ADMIN, $target->appRole());
        $this->assertTrue($target->hasRole('PMO'));

        /** @var UserAccessAudit $audit */
        $audit = UserAccessAudit::query()->latest('id')->firstOrFail();
        $this->assertContains('app_role', $audit->changed_fields);
        $this->assertNotContains('permission_role', $audit->changed_fields);
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
