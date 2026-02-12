<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAccessAudit;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserAccessService
{
    public function normalizeAppRole(?string $appRole): string
    {
        return strtolower((string) $appRole) === User::APP_ROLE_ADMIN
            ? User::APP_ROLE_ADMIN
            : User::APP_ROLE_USER;
    }

    public function syncAppRole(User $user): void
    {
        $normalizedAppRole = $this->normalizeAppRole($user->app_role);

        if ($user->app_role !== $normalizedAppRole) {
            $user->forceFill(['app_role' => $normalizedAppRole])->save();
        }
    }

    public function ensureDefaultPermissionRole(User $user): void
    {
        if ($user->roles()->exists()) {
            return;
        }

        $viewerRole = Role::query()->where('name', 'Viewer')->first();

        if ($viewerRole) {
            $user->assignRole($viewerRole);
        }
    }

    public function updateAccess(User $targetUser, array $changes, ?User $actor = null): void
    {
        $before = $this->snapshot($targetUser);

        DB::transaction(function () use ($targetUser, $changes): void {
            if (array_key_exists('status', $changes) && $changes['status'] !== null) {
                $targetUser->status = $changes['status'];
            }

            if (array_key_exists('app_role', $changes) && $changes['app_role'] !== null) {
                $targetUser->app_role = $this->normalizeAppRole($changes['app_role']);
            }

            $targetUser->save();

            if (array_key_exists('permission_role', $changes) && $changes['permission_role']) {
                $targetUser->syncRoles([$changes['permission_role']]);
            }

            $this->syncAppRole($targetUser);
            $this->ensureDefaultPermissionRole($targetUser);
        });

        $targetUser->refresh();
        $after = $this->snapshot($targetUser);
        $changedFields = $this->resolveChangedFields($before, $after);

        if ($changedFields === []) {
            return;
        }

        UserAccessAudit::create([
            'user_id'        => $targetUser->id,
            'actor_id'       => $actor?->id,
            'event'          => 'user_access_updated',
            'old_values'     => $before,
            'new_values'     => $after,
            'changed_fields' => $changedFields,
        ]);
    }

    private function snapshot(User $user): array
    {
        return [
            'status'          => $user->status,
            'app_role'        => $this->normalizeAppRole($user->app_role),
            'permission_role' => $user->getRoleNames()->first(),
        ];
    }

    private function resolveChangedFields(array $before, array $after): array
    {
        $changed = [];

        foreach ($after as $key => $value) {
            if (($before[$key] ?? null) !== $value) {
                $changed[] = $key;
            }
        }

        return $changed;
    }
}
