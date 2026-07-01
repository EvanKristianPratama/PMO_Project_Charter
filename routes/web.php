<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DatabaseSwitcherController;
use App\Http\Controllers\Admin\SyncController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\PublicSyncController;
use App\Http\Controllers\MasterData\ActivityLogController as MasterDataActivityLogController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\MasterData\MstInitiative\MstInitiativeController;
use App\Http\Controllers\MasterData\MstPicProject\MstPicProjectController;
use App\Http\Controllers\MasterData\ProjectCharter\ProjectCharterController as MasterDataProjectCharterController;
use App\Http\Controllers\MasterData\ScopeCharter\ScopeCharterController;
use Modules\ITSP\Controllers\ProgramImplementation\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware("guest")->group(function () {
    Route::get("/login", [SsoController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "login"]);
    Route::get("/auth/google", [
        SsoController::class,
        "redirectToGoogle",
    ])->name("auth.google");
    Route::get("/auth/google/callback", [
        SsoController::class,
        "handleGoogleCallback",
    ]);
    Route::post("/public/sync-master", [
        PublicSyncController::class,
        "sync",
    ])->name("public.sync");
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (any status)
|--------------------------------------------------------------------------
*/

Route::middleware("auth")->group(function () {
    Route::post("/logout", [SsoController::class, "logout"])->name("logout");
});

/*
|--------------------------------------------------------------------------
| Approved User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(["auth", "approved"])->group(function () {
    Route::get("/dashboard", DashboardController::class)->name("dashboard");
    Route::get("/blank", fn() => Inertia::render("BlankPage/Index"))->name(
        "blank",
    );

    // ═══ Master Data ═══════════════════════════════════════════════
    Route::get("/master-data", MasterDataController::class)->name(
        "master-data.index",
    );
    Route::get("/master-data/log-aktivitas", [
        MasterDataActivityLogController::class,
        "index",
    ])->name("master-data.activity-log.index");

    // Master Data → Master Initiative CRUD
    Route::prefix("/master-data/master-initiatives")
        ->name("master-data.mst-initiatives.")
        ->group(function () {
            Route::get("/", [MstInitiativeController::class, "index"])->name(
                "index",
            );
            Route::get("/create", [
                MstInitiativeController::class,
                "create",
            ])->name("create");
            Route::post("/", [MstInitiativeController::class, "store"])->name(
                "store",
            );
            Route::get("/{mstInitiative}/edit", [
                MstInitiativeController::class,
                "edit",
            ])->name("edit");
            Route::put("/{mstInitiative}", [
                MstInitiativeController::class,
                "update",
            ])->name("update");
            Route::delete("/{mstInitiative}", [
                MstInitiativeController::class,
                "destroy",
            ])->name("destroy");

            // Status history
            Route::post("/{mstInitiative}/status", [
                MstInitiativeController::class,
                "storeStatus",
            ])->name("status.store");
            Route::put("/status/{status}", [
                MstInitiativeController::class,
                "updateStatus",
            ])->name("status.update");
            Route::delete("/status/{status}", [
                MstInitiativeController::class,
                "destroyStatus",
            ])->name("status.destroy");
        });

    // Master Data → Scope Charter CRUD (trs_sc_initiative + trs_sc_status_implementation)
    Route::prefix("/master-data/scope-charter")
        ->name("master-data.scope-charter.")
        ->group(function () {
            Route::get("/", [ScopeCharterController::class, "index"])->name(
                "index",
            );
            Route::post("/", [ScopeCharterController::class, "store"])->name(
                "store",
            );
            Route::put("/{scopeCharter}", [
                ScopeCharterController::class,
                "update",
            ])->name("update");
            Route::delete("/{scopeCharter}", [
                ScopeCharterController::class,
                "destroy",
            ])->name("destroy");
        });

    // Master Data → Project Charter CRUD (trs_projects + trs_pc_status_implementation)
    Route::prefix("/master-data/project-charter")
        ->name("master-data.project-charter.")
        ->group(function () {
            Route::get("/", [
                MasterDataProjectCharterController::class,
                "index",
            ])->name("index");
            Route::post("/", [
                MasterDataProjectCharterController::class,
                "store",
            ])->name("store");
            Route::put("/{projectCharter}", [
                MasterDataProjectCharterController::class,
                "update",
            ])->name("update");
            Route::delete("/{projectCharter}", [
                MasterDataProjectCharterController::class,
                "destroy",
            ])->name("destroy");
        });

    // Master Data → PIC Project CRUD
    Route::prefix("/master-data/pic-projects")
        ->name("master-data.pic-projects.")
        ->group(function () {
            Route::get("/", [MstPicProjectController::class, "index"])->name(
                "index",
            );
            Route::post("/", [MstPicProjectController::class, "store"])->name(
                "store",
            );
            Route::put("/{picProject}", [
                MstPicProjectController::class,
                "update",
            ])->name("update");
            Route::delete("/{picProject}", [
                MstPicProjectController::class,
                "destroy",
            ])->name("destroy");
        });

    // Cloud Data Synchronization
    Route::get("/sync", [SyncController::class, "index"])->name("sync.index");
    Route::post("/sync/pull", [SyncController::class, "pull"])->name(
        "sync.pull",
    );

    // ═══ Backward Compatibility Redirects (ITSP) ═══════════════════
    Route::any('/strategic-house/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/strategic-house' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/strategic-pillars/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/strategic-pillars' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/program-planning/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/program-planning' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/program-evalution/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/program-evalution' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/program-implementation/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/program-implementation' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/digital-initiatives/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/digital-initiatives' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/it-initiatives/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/it-initiatives' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/roadmap/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itsp/roadmap' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    // ═══ Backward Compatibility Redirects (ITOM) ═══════════════════
    Route::any('/business-process/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/business-process' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/operating-model/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/operating-model' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/service-portofolio/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/service-portofolio' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/policy/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/policy' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/raci/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/raci' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/resource-management/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/resource-management' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/bpmn-workflow/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/bpmn-workflow' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');

    Route::any('/libary/{any?}', function ($any = null) {
        $query = request()->getQueryString();
        return redirect('/itom/libary' . ($any ? '/' . $any : '') . ($query ? '?' . $query : ''), 301);
    })->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(["auth", "approved", "admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/dashboard", AdminDashboardController::class)->name(
            "dashboard",
        );
        Route::post("/switch-database", [
            DatabaseSwitcherController::class,
            "switch",
        ])->name("switch-database");
        Route::get("/users", [AdminUserController::class, "index"])->name(
            "users.index",
        );
        Route::post("/users", [AdminUserController::class, "store"])->name(
            "users.store",
        );
        Route::put("/users/{user}", [
            AdminUserController::class,
            "update",
        ])->name("users.update");
        Route::delete("/users/{user}", [
            AdminUserController::class,
            "destroy",
        ])->name("users.destroy");
        Route::get("/roles", [AdminRoleController::class, "index"])->name(
            "roles.index",
        );
        Route::post("/roles", [AdminRoleController::class, "store"])->name(
            "roles.store",
        );
        Route::post("/roles/permissions", [
            AdminRoleController::class,
            "storePermission",
        ])->name("roles.permissions.store");
        Route::put("/roles/{role}/permissions", [
            AdminRoleController::class,
            "updatePermissions",
        ])->name("roles.permissions.update");
        Route::delete("/roles/{role}", [
            AdminRoleController::class,
            "destroy",
        ])->name("roles.destroy");
        Route::get("/backup", [BackupController::class, "index"])->name(
            "backup.index",
        );
        Route::get("/backup/download/{file}", [
            BackupController::class,
            "download",
        ])
            ->where("file", "[A-Za-z0-9._-]+")
            ->name("backup.download");
        Route::post("/backup/run", [BackupController::class, "run"])->name(
            "backup.run",
        );
        Route::put("/backup/settings", [
            BackupController::class,
            "updateRetention",
        ])->name("backup.settings.update");
        Route::get("/activity-log", [
            ActivityLogController::class,
            "index",
        ])->name("activity-log.index");
    });

/*
|--------------------------------------------------------------------------
| Catch-all redirect
|--------------------------------------------------------------------------
*/

Route::get("/", fn() => redirect()->route("login"));
