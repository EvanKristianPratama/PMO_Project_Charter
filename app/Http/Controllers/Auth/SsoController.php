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

    public function showLogin(): Response|RedirectResponse
    {
        // Auto-login for offline desktop mode: Paksa masuk sebagai User Biasa (bukan Admin)
        try {
            // Auto-sync jika tabel users belum ada di database SQLite lokal
            if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                \Illuminate\Support\Facades\Artisan::call('app:sync-to-sqlite');
            }

            $user = User::first();
            
            if (!$user) {
                // Buat user offline default jika database kosong
                $user = User::create([
                    'name' => 'Offline User',
                    'email' => 'user@pmo.local',
                    'status' => 'approved',
                    'app_role' => 'user',
                ]);
            } else {
                // Paksa update di database agar user ini berstatus approved dan memiliki role biasa (bukan admin)
                $user->update([
                    'status' => 'approved',
                    'app_role' => 'user',
                ]);
            }
            
            if ($user) {
                Auth::login($user, remember: true);
                // Log login activity
                \App\Services\ActivityLogService::login($user);
                return redirect()->route('strategic-house.index');
            }
        } catch (\Exception $e) {
            // Jika database kosong / tabel belum ada, tampilkan halaman login biasa dengan opsi sinkronisasi
            return Inertia::render('Auth/Login', [
                'error' => 'Database belum tersinkronisasi. Silakan jalankan perintah php artisan app:sync-to-sqlite.'
            ]);
        }

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
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'status' => 'pending',
                    'app_role' => User::APP_ROLE_USER,
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

        // For offline desktop mode, ALWAYS go to strategic-house
        return redirect()->route('strategic-house.index');
    }
}
