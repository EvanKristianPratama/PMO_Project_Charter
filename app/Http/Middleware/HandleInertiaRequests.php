<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => function () use ($request) {
                $user = $request->user();

                if (! $user) {
                    return null;
                }

                $roles = $user->getRoleNames()->values()->all();

                return [
                    'user' => [
                        'id'       => $user->id,
                        'name'     => $user->name,
                        'email'    => $user->email,
                        'avatar'   => $user->avatar,
                        'initials' => $user->initials,
                        'status'   => $user->status,
                        'role'     => $user->primaryRoleName(),
                        'roles'    => $roles,
                    ],
                ];
            },

            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'check_status' => $request->session()->get('check_status'),
            ],
        ];
    }
}
