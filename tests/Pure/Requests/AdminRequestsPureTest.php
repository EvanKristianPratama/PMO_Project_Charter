<?php

namespace Tests\Pure\Requests;

use App\Http\Requests\Admin\RolePermissionStoreRequest;
use App\Http\Requests\Admin\RolePermissionUpdateRequest;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use PHPUnit\Framework\TestCase;

class AdminRequestsPureTest extends TestCase
{
    public function test_user_update_authorize_returns_false_without_authenticated_user(): void
    {
        $request = new UserUpdateRequest;
        $request->setUserResolver(static fn () => null);

        $this->assertFalse($request->authorize());
    }

    public function test_user_update_authorize_returns_true_for_admin_user(): void
    {
        $request = new UserUpdateRequest;
        $request->setUserResolver(static fn () => new AdminProbeUser);

        $this->assertTrue($request->authorize());
    }

    public function test_role_store_prepare_for_validation_normalizes_name_spacing(): void
    {
        $request = new RoleStoreRequest;
        $request->merge(['name' => '  Admin    Super  ']);

        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertSame('Admin Super', $request->input('name'));
    }

    public function test_role_permission_store_prepare_for_validation_normalizes_name_spacing(): void
    {
        $request = new RolePermissionStoreRequest;
        $request->merge(['name' => '  permission    manage-users  ']);

        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);

        $this->assertSame('permission manage-users', $request->input('name'));
    }

    public function test_role_permission_update_rules_include_permissions_array_validation(): void
    {
        $request = new RolePermissionUpdateRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('permissions', $rules);
        $this->assertArrayHasKey('permissions.*', $rules);
    }

    public function test_user_index_rules_include_permission_role_callable_guard(): void
    {
        $request = new UserIndexRequest;

        $rules = $request->rules();

        $permissionRoleRules = $rules['permission_role'];
        $this->assertIsArray($permissionRoleRules);

        $hasClosure = false;
        foreach ($permissionRoleRules as $rule) {
            if ($rule instanceof \Closure) {
                $hasClosure = true;
                break;
            }
        }

        $this->assertTrue($hasClosure);
    }
}

class AdminProbeUser
{
    public function isAdminUser(): bool
    {
        return true;
    }
}
