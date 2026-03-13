<?php

namespace Tests\Pure\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelPureTest extends TestCase
{
    public function test_app_roles_returns_admin_and_user(): void
    {
        $this->assertSame([User::APP_ROLE_ADMIN, User::APP_ROLE_USER], User::appRoles());
    }

    public function test_app_role_normalizes_value(): void
    {
        $user = new User;
        $user->app_role = 'ADMIN';

        $this->assertSame(User::APP_ROLE_ADMIN, $user->appRole());
    }

    public function test_app_role_defaults_to_user_for_invalid_input(): void
    {
        $user = new User;
        $user->app_role = 'owner';

        $this->assertSame(User::APP_ROLE_USER, $user->appRole());
    }

    public function test_is_admin_user_uses_normalized_app_role(): void
    {
        $user = new User;
        $user->app_role = 'Admin';

        $this->assertTrue($user->isAdminUser());
    }

    public function test_status_helpers_return_expected_flags(): void
    {
        $approved = new User;
        $approved->status = 'approved';
        $this->assertTrue($approved->isApproved());
        $this->assertFalse($approved->isPending());

        $pending = new User;
        $pending->status = 'pending';
        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isRejected());

        $rejected = new User;
        $rejected->status = 'rejected';
        $this->assertTrue($rejected->isRejected());
        $this->assertFalse($rejected->isApproved());
    }

    public function test_initials_accessor_uses_first_two_words(): void
    {
        $user = new User;
        $user->name = 'Evan Kristian Putra';

        $this->assertSame('EK', $user->initials);
    }
}
