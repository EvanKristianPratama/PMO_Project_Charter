<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolePermissionStoreRequest;
use App\Http\Requests\Admin\RolePermissionUpdateRequest;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Services\RoleManagementService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    public function __construct(
        private readonly RoleManagementService $roleManagementService,
    ) {}

    public function index(): Response
    {
        $assignedCounts = $this->roleManagementService->assignedUserCounts();
        $protectedRoles = $this->protectedRoleNames();

        $roles = Role::query()
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function (Role $role) use ($assignedCounts, $protectedRoles): array {
                $assignedUsers = $assignedCounts[$role->id] ?? 0;

                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->values()->all(),
                    'assigned_users' => $assignedUsers,
                    'can_delete' => $assignedUsers === 0 && ! in_array($role->name, $protectedRoles, true),
                ];
            })
            ->values();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'permissions' => Permission::query()->orderBy('name')->pluck('name'),
            'protectedRoles' => $protectedRoles,
        ]);
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = $this->roleManagementService->createRole(
            name: $validated['name'],
            permissions: $validated['permissions'] ?? [],
        );

        return back()->with('success', "Role {$role->name} berhasil dibuat.");
    }

    public function storePermission(RolePermissionStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $permission = $this->roleManagementService->createPermission($validated['name']);

        return back()->with('success', "Permission {$permission->name} berhasil dibuat.");
    }

    public function updatePermissions(RolePermissionUpdateRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $this->roleManagementService->syncRolePermissions(
            role: $role,
            permissions: $validated['permissions'] ?? [],
        );

        return back()->with('success', "Akses role {$role->name} berhasil diperbarui.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoleNames(), true)) {
            return back()->with('error', "Role {$role->name} dilindungi sistem dan tidak bisa dihapus.");
        }

        if ($this->roleManagementService->roleHasAssignments($role)) {
            return back()->with('error', "Role {$role->name} masih dipakai user dan tidak bisa dihapus.");
        }

        $this->roleManagementService->deleteRole($role);

        return back()->with('success', "Role {$role->name} berhasil dihapus.");
    }

    private function protectedRoleNames(): array
    {
        return ['Admin'];
    }
}
