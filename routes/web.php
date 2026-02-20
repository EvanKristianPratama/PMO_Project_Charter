<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\CompanyProfile\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardMonitoringController;
use App\Http\Controllers\DigitalInitiative\DigitalInitiativeController;
use App\Http\Controllers\ITInitiative\CharterController;
use App\Http\Controllers\ITInitiative\ITInitiativeController;
use App\Http\Controllers\ITInitiative\MilestoneController;
use App\Http\Controllers\ProgramPlanning\ProgramPlanningController;
use App\Http\Controllers\ProgramImplementation\ProgramImplementationController;
use App\Http\Controllers\StrategicPillar\StrategicPillarController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [SsoController::class, 'showLogin'])->name('login');
    Route::get('/auth/google', [SsoController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SsoController::class, 'handleGoogleCallback']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (any status)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SsoController::class, 'logout'])->name('logout');

});

/*
|--------------------------------------------------------------------------
| Approved User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard-monitoring', DashboardMonitoringController::class)->name('dashboard-monitoring');
    Route::get('/program-planning/rsti-sub-holding', [ProgramPlanningController::class, 'rstiSubHolding'])->name('program-planning.rsti-sub-holding');
    Route::get('/program-planning/program-definition', [ProgramPlanningController::class, 'programDefinition'])->name('program-planning.program-definition');
    Route::get('/program-planning/matrix-dependency', [ProgramPlanningController::class, 'matrixDependency'])->name('program-planning.matrix-dependency');
    Route::redirect('/program-planning/matrix', '/program-planning/matrix-dependency');
    Route::get('/program-implementation', ProgramImplementationController::class)->name('program-implementation.index');
    Route::get('/program-implementation/budgeting', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Budgeting',
    ]))->name('program-implementation.budgeting');
    Route::get('/program-implementation/matrix-dependency', fn () => Inertia::render('ProgramImplementation/MatrixDependency'))->name('program-implementation.matrix-dependency');
    Route::get('/architecture', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Architecture',
    ]))->name('architecture.index');
    Route::get('/program-information', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Program Information',
    ]))->name('program-information.index');
    Route::get('/strategic-pillars/{goal?}', [StrategicPillarController::class, 'index'])->name('strategic-pillars.index');

    // Digital Initiatives
    Route::resource('digital-initiatives', DigitalInitiativeController::class);

    // IT Initiatives & Charters
    Route::get('/roadmap', [ITInitiativeController::class, 'roadmapIndex'])->name('roadmap.index');
    Route::resource('it-initiatives', ITInitiativeController::class)
        ->parameters(['it-initiatives' => 'project']);
    Route::post('/it-initiatives/{project}/charter', [CharterController::class, 'store'])->name('it-initiatives.charter.store');
    Route::post('/it-initiatives/{project}/milestones', [MilestoneController::class, 'store'])->name('it-initiatives.milestones.store');
    Route::delete('/it-initiatives/{project}/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('it-initiatives.milestones.destroy');

    // Company Profile
    Route::resource('companies', CompanyController::class)->only(['index']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'approved', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/permissions', [AdminRoleController::class, 'storePermission'])->name('roles.permissions.store');
    Route::put('/roles/{role}/permissions', [AdminRoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
});

/*
|--------------------------------------------------------------------------
| Catch-all redirect
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));
