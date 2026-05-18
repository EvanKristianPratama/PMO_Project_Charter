<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\UserAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

class SsoController extends Controller
{
    public function __construct(
        private readonly UserAccessService $userAccessService,
    ) {}

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

            // 🔍 Smart Sync: Check Cloud Master for existing approved user profile
            $cloudUser = null;
            try {
                $cloudUser = \Illuminate\Support\Facades\DB::connection('cloud')
                    ->table('users')
                    ->where('email', $googleUser->getEmail())
                    ->first();
            } catch (\Exception $e) {
                // Silently ignore if offline, fall back to safe default
            }

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // Sync roles/status if local is outdated and cloud was fetched
                    'status' => $cloudUser ? $cloudUser->status : $user->status,
                    'app_role' => $cloudUser ? $cloudUser->app_role : $user->app_role,
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // Use cloud status/role, fallback to safe pending if completely new everywhere
                    'status' => $cloudUser ? $cloudUser->status : 'pending',
                    'app_role' => $cloudUser ? $cloudUser->app_role : User::APP_ROLE_USER,
                ]);
            }
        } else {
            $user->update([
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        $this->userAccessService->syncAppRole($user);
        $this->userAccessService->ensureDefaultPermissionRole($user);
        Auth::login($user, remember: true);

        return $this->redirectByStatus($user);
    }

    public function logout(): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            ActivityLogService::logout($user);
        }

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByStatus(User $user): RedirectResponse
    {
        if ($user->isPending() || $user->isRejected()) {
            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login', [
                'status' => $user->status,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        // Catat login berhasil
        ActivityLogService::login($user);

        return redirect()->route('strategic-house.index');
    }
}
