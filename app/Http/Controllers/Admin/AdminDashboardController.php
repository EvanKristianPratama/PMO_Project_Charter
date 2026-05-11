<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'admin_users' => User::query()->withAppRole(User::APP_ROLE_ADMIN)->count(),
                'pending_users' => User::pending()->count(),
                'permission_roles' => Role::count(),
            ],
            'recentUsers' => User::query()
                ->latest()
                ->limit(6)
                ->get(['id', 'name', 'email', 'status', 'app_role', 'created_at'])
                ->map(fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'app_role' => $user->appRole(),
                    'created_at' => $user->created_at?->toDateTimeString(),
                ]),
        ]);
    }
}
