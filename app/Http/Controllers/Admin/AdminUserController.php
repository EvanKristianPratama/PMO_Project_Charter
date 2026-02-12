<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->when($request->search, fn ($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )
            ->when($request->status && $request->status !== 'all', fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->role && $request->role !== 'all', fn ($q) =>
                $q->role($request->role)
            )
            ->with('roles')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users'   => $users,
            'roles'   => Role::pluck('name'),
            'stats'   => $this->getStats(),
            'filters' => $request->only(['search', 'status', 'role']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected',
            'role'   => 'sometimes|string|exists:roles,name',
        ]);

        if (isset($validated['status'])) {
            $user->update(['status' => $validated['status']]);
        }

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
            $user->update(['role' => $validated['role']]);
        }

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
            'admin'   => User::role('Admin')->count(),
        ];
    }
}
