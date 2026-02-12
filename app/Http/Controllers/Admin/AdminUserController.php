<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use App\Services\UserAccessService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly UserAccessService $userAccessService,
    ) {}

    public function index(UserIndexRequest $request): Response
    {
        $filters = $request->validated();

        $users = User::query()
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when(($filters['status'] ?? null) && ($filters['status'] ?? null) !== 'all', fn ($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(($filters['app_role'] ?? null) && ($filters['app_role'] ?? null) !== 'all', fn ($q) =>
                $q->withAppRole($filters['app_role'])
            )
            ->when(($filters['permission_role'] ?? null) && ($filters['permission_role'] ?? null) !== 'all', fn ($q) =>
                $q->role($filters['permission_role'])
            )
            ->with('roles')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users'            => $users,
            'permissionRoles'  => Role::query()->pluck('name'),
            'appRoles'         => User::appRoles(),
            'stats'            => $this->getStats(),
            'filters'          => [
                'search' => $filters['search'] ?? null,
                'status' => $filters['status'] ?? null,
                'app_role' => $filters['app_role'] ?? null,
                'permission_role' => $filters['permission_role'] ?? null,
            ],
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->userAccessService->updateAccess(
            targetUser: $user,
            changes: $request->validated(),
            actor: $request->user(),
        );

        return back()->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', "User {$user->name} berhasil dihapus.");
    }

    /* ── Helpers ───────────────────────────────────── */

    private function getStats(): array
    {
        return [
            'total'   => User::count(),
            'active'  => User::approved()->count(),
            'pending' => User::pending()->count(),
            'admin'   => User::query()->withAppRole(User::APP_ROLE_ADMIN)->count(),
        ];
    }
}
