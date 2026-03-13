<?php

namespace Tests\Pure\Services;

use App\Models\User;
use App\Services\UserAccessService;
use PHPUnit\Framework\TestCase;

class UserAccessServiceTest extends TestCase
{
    public function test_normalize_app_role_returns_admin_for_admin_value(): void
    {
        $service = new UserAccessService;

        $this->assertSame(User::APP_ROLE_ADMIN, $service->normalizeAppRole(User::APP_ROLE_ADMIN));
    }

    public function test_normalize_app_role_is_case_insensitive_for_admin(): void
    {
        $service = new UserAccessService;

        $this->assertSame(User::APP_ROLE_ADMIN, $service->normalizeAppRole('ADMIN'));
    }

    public function test_normalize_app_role_defaults_to_user_for_unknown_value(): void
    {
        $service = new UserAccessService;

        $this->assertSame(User::APP_ROLE_USER, $service->normalizeAppRole('super-admin'));
        $this->assertSame(User::APP_ROLE_USER, $service->normalizeAppRole(null));
    }

    public function test_sync_app_role_updates_invalid_role_and_marks_saved(): void
    {
        $service = new UserAccessService;
        $user = new FakeUser;
        $user->app_role = 'not-valid';

        $service->syncAppRole($user);

        $this->assertSame(User::APP_ROLE_USER, $user->app_role);
        $this->assertTrue($user->saved);
        $this->assertSame(['app_role' => User::APP_ROLE_USER], $user->forced);
    }

    public function test_sync_app_role_does_not_save_when_role_is_already_normalized(): void
    {
        $service = new UserAccessService;
        $user = new FakeUser;
        $user->app_role = User::APP_ROLE_ADMIN;

        $service->syncAppRole($user);

        $this->assertFalse($user->saved);
        $this->assertSame([], $user->forced);
    }
}

class FakeUser extends User
{
    public bool $saved = false;

    public array $forced = [];

    public function forceFill(array $attributes)
    {
        $this->forced = $attributes;

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function save(array $options = [])
    {
        $this->saved = true;

        return true;
    }
}
