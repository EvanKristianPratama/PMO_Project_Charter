<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementService
{
    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::query()->create([
            'name' => $this->normalizeName($name),
            'guard_name' => $this->guardName(),
        ]);

        if ($permissions !== []) {
            $role->syncPermissions($permissions);
        }

        $this->resetPermissionCache();

        return $role->refresh();
    }

    public function createPermission(string $name): Permission
    {
        $permission = Permission::query()->create([
            'name' => $this->normalizeName($name),
            'guard_name' => $this->guardName(),
        ]);

        $this->resetPermissionCache();

        return $permission;
    }

    public function syncRolePermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
        $this->resetPermissionCache();
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
        $this->resetPermissionCache();
    }

    public function roleHasAssignments(Role $role): bool
    {
        $tableName = config('permission.table_names.model_has_roles');

        return DB::table($tableName)
            ->where('role_id', $role->id)
            ->exists();
    }

    public function assignedUserCounts(): array
    {
        $tableName = config('permission.table_names.model_has_roles');

        return DB::table($tableName)
            ->select('role_id', DB::raw('COUNT(*) as total'))
            ->groupBy('role_id')
            ->pluck('total', 'role_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('/\s+/', ' ', trim($name)) ?? trim($name);
    }

    private function resetPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function guardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }
}
