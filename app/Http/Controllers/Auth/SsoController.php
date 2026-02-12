<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SsoController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                    'status'    => 'pending',
                    'role'      => 'Viewer',
                ]);
            }
        } else {
            $user->update([
                'name'   => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        $this->ensureRoleIsSynced($user);
        Auth::login($user, remember: true);

        return $this->redirectByStatus($user);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    /* ── Helpers ───────────────────────────────────── */

    private function ensureRoleIsSynced(User $user): void
    {
        $assignedRole = $user->getRoleNames()->first();
        $storedRole = trim((string) $user->role);
        $storedRole = $storedRole !== '' ? $storedRole : 'Viewer';

        if (! $assignedRole) {
            $roleToAssign = Role::query()->where('name', $storedRole)->first();

            if (! $roleToAssign) {
                $roleToAssign = Role::query()
                    ->whereRaw('LOWER(name) = ?', [strtolower($storedRole)])
                    ->first();
            }

            $roleToAssign ??= Role::query()->where('name', 'Viewer')->first();

            if ($roleToAssign) {
                $user->syncRoles([$roleToAssign->name]);
                $assignedRole = $roleToAssign->name;
            }
        }

        $effectiveRole = $assignedRole ?: $storedRole;

        if ($user->role !== $effectiveRole) {
            $user->forceFill(['role' => $effectiveRole])->save();
        }
    }

    private function redirectByStatus(User $user): RedirectResponse
    {
        if ($user->isPending() || $user->isRejected()) {
            Auth::logout();
            
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login', [
                'status' => $user->status,
                'name'   => $user->name,
                'email'  => $user->email,
            ]);
        }

        if ($user->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
