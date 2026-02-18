<?php

namespace App\Http\Controllers\ProgramPlanning;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramPlanningController extends Controller
{
    public function rstiSubHolding(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('ProgramPlanning/RstiSubHolding');
    }

    public function programDefinition(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('ProgramPlanning/ProgramDefinition');
    }

    public function matrixDependency(): Response|RedirectResponse
    {
        if (request()->user()?->isAdminUser()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('ProgramPlanning/MatrixDependency');
    }
}
