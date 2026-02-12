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

            'auth' => fn () => $request->user() ? [
                'user' => [
                    'id'       => $request->user()->id,
                    'name'     => $request->user()->name,
                    'email'    => $request->user()->email,
                    'avatar'   => $request->user()->avatar,
                    'initials' => $request->user()->initials,
                    'status'   => $request->user()->status,
                    'roles'    => $request->user()->getRoleNames(),
                ],
            ] : null,

            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'check_status' => $request->session()->get('check_status'),
            ],
        ];
    }
}
