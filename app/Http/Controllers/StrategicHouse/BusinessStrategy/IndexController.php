<?php

namespace App\Http\Controllers\StrategicHouse\BusinessStrategy;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('strategic-house.index', [
            'view' => 'business-strategy',
        ]);
    }
}
