<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\UserAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserAccessService $userAccessService,
    ) {}

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 🔍 Smart Sync: Check Cloud Master for existing user profile first (same as SSO logic)
        $cloudUser = null;
        try {
            $cloudUser = \Illuminate\Support\Facades\DB::connection('cloud')
                ->table('users')
                ->where('email', $request->email)
                ->first();
        } catch (\Exception $e) {
            // Silently ignore if offline
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Set direct approval by user request override
            'status' => 'approved',
            'app_role' => $cloudUser ? $cloudUser->app_role : User::APP_ROLE_USER,
        ]);

        $this->userAccessService->syncAppRole($user);
        $this->userAccessService->ensureDefaultPermissionRole($user);

        Auth::login($user, remember: true);

        return $this->redirectByStatus($user);
    }

    public function login(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'login_identity' => ['required', 'string'], // Replaces email conceptually with Name field
            'password' => ['required'],
        ]);

        $credentials = [
            'name' => $input['login_identity'],
            'password' => $input['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            /** @var User $user */
            $user = Auth::user();
            
            return $this->redirectByStatus($user);
        }

        return back()->withErrors([
            'login_identity' => 'Kredensial yang Anda berikan tidak cocok dengan data kami.',
        ])->onlyInput('login_identity');
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

        // Log activity
        ActivityLogService::login($user);

        return redirect()->route('blank');
    }
}
