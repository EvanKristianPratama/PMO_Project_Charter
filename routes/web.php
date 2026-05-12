<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DatabaseSwitcherController;
use App\Http\Controllers\Admin\SyncController;
use App\Http\Controllers\Architecture\BusinessCapability\BusinessCapabilityController;
use App\Http\Controllers\Architecture\OrganizationStructure\IndexController as ArchitectureOrganizationStructureIndexController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\PublicSyncController;
use App\Http\Controllers\MasterData\ActivityLogController as MasterDataActivityLogController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\MasterData\MstInitiative\MstInitiativeController;
use App\Http\Controllers\MasterData\ProjectCharter\ProjectCharterController as MasterDataProjectCharterController;
use App\Http\Controllers\MasterData\ScopeCharter\ScopeCharterController;
use App\Http\Controllers\ProgramEvaluation\ReviewTimelineController;
use App\Http\Controllers\ProgramEvaluation\ReviewDashboardController;
use App\Http\Controllers\ProgramEvaluation\TrsReviewPCController;
use App\Http\Controllers\ProgramImplementation\DashboardController;
use App\Http\Controllers\StrategicHouse\BusinessStrategy\IndexController as StrategicHouseBusinessStrategyController;
use App\Http\Controllers\StrategicHouse\BusinessStrategy\BusinessStrategyController as StrategicHouseBusinessStrategyManageController;
use App\Http\Controllers\StrategicHouse\ItBuildingBlock\IndexController as StrategicHouseItBuildingBlockController;
use App\Http\Controllers\ProgramImplementation\ProgramImplementationController;
use App\Http\Controllers\ProgramImplementation\ResourceManagementController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\DigitalInitiatives\DigitalInitiativeController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\CharterController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\ITInitiativeController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\MilestoneController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\VersionAnalysisController;
use App\Http\Controllers\ProgramImplementation\Roadmap\RoadmapController;
use App\Http\Controllers\ProgramPlanning\DashboardController as PlanningDashboardController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\CreateController as ProgramDefinitionDigitalInitiativesAppendixCreateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\EditController as ProgramDefinitionDigitalInitiativesAppendixEditController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\IndexController as ProgramDefinitionDigitalInitiativesAppendixIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\StoreController as ProgramDefinitionDigitalInitiativesAppendixStoreController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\UpdateController as ProgramDefinitionDigitalInitiativesAppendixUpdateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\CreateController as ProgramDefinitionDigitalInitiativesCompendiumCreateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\EditController as ProgramDefinitionDigitalInitiativesCompendiumEditController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\IndexController as ProgramDefinitionDigitalInitiativesCompendiumIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\StoreController as ProgramDefinitionDigitalInitiativesCompendiumStoreController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\UpdateController as ProgramDefinitionDigitalInitiativesCompendiumUpdateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\EditController as ProgramDefinitionDigitalInitiativesEditController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\IndexController as ProgramDefinitionDigitalInitiativesController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Mapping\IndexController as ProgramDefinitionDigitalInitiativesMappingIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\MasterDigitalInitiative\IndexController as ProgramDefinitionDigitalInitiativesMasterIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Summary\IndexController as ProgramDefinitionDigitalInitiativesSummaryIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\CreateController as ProgramDefinitionDigitalInitiativesRoadmapCreateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\EditController as ProgramDefinitionDigitalInitiativesRoadmapEditController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\IndexController as ProgramDefinitionDigitalInitiativesRoadmapIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MilestoneController as ProgramDefinitionDigitalInitiativesRoadmapMilestoneController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\UpdateController as ProgramDefinitionDigitalInitiativesUpdateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\IndexController as ProgramDefinitionController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\ITInitiatives\IndexController as ProgramDefinitionITInitiativesController;
use App\Http\Controllers\ProgramPlanning\ProgramPlanningController;
// ProfileController removed: database connection switching feature disabled
use App\Http\Controllers\StrategicHouse\IndexController as StrategicHouseController;
use App\Http\Controllers\StrategicHouse\InitiativeRelation\InitiativeRelationController as StrategicHouseInitiativeRelationController;
use App\Http\Controllers\StrategicHouse\InitiativeSupport\IndexController as StrategicHouseInitiativeSupportIndexController;
use App\Http\Controllers\StrategicHouse\MapTechnology\MapTechnologyController;
use App\Http\Controllers\StrategicHouse\RoadMap\IndexController as StrategicHouseRoadmapIndexController;
use App\Http\Controllers\StrategicHouse\RoadMap\SummaryController as StrategicHouseRoadmapSummaryController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\GoalController as StrategicHouseStrategicPillarGoalController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\IndexController as StrategicHouseStrategicPillarsIndexController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\InitiativeTaggingController as StrategicHouseInitiativeTaggingController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\ThemeController as StrategicHouseStrategicPillarThemeController;
use App\Http\Controllers\ProgramPlanning\BusinessStrategy\IndexController as ProgramPlanningBusinessStrategyIndexController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [SsoController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    // Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/google', [SsoController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SsoController::class, 'handleGoogleCallback']);
    Route::post('/public/sync-master', [PublicSyncController::class, 'sync'])->name('public.sync');
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
    Route::get('/program-planning', PlanningDashboardController::class)->name('program-planning');
    // Program Planning → Business Strategy (separate from Strategic House)
    Route::get('/program-planning/business-strategy', ProgramPlanningBusinessStrategyIndexController::class)
        ->name('program-planning.business-strategy');
    Route::get('/program-planning/rsti-sub-holding', [ProgramPlanningController::class, 'rstiSubHolding'])->name('program-planning.rsti-sub-holding');
    Route::get('/program-planning/program-definition', ProgramDefinitionController::class)->name('program-planning.program-definition');
    Route::get('/program-planning/program-definition/digital-initiatives', ProgramDefinitionDigitalInitiativesController::class)->name('program-planning.program-definition.digital-initiatives');
    Route::get('/program-planning/program-definition/digital-initiatives/{digitalInitiative}/edit', ProgramDefinitionDigitalInitiativesEditController::class)
        ->whereNumber('digitalInitiative')
        ->name('program-planning.program-definition.digital-initiatives.edit');
    Route::put('/program-planning/program-definition/digital-initiatives/{digitalInitiative}', ProgramDefinitionDigitalInitiativesUpdateController::class)
        ->whereNumber('digitalInitiative')
        ->name('program-planning.program-definition.digital-initiatives.update');
    Route::get('/program-planning/program-definition/digital-initiatives/{initiative}/summary', ProgramDefinitionDigitalInitiativesSummaryIndexController::class)
        ->whereNumber('initiative')
        ->name('program-planning.program-definition.digital-initiatives.summary.index');

    Route::prefix('/program-planning/program-definition/digital-initiatives/appendix')->name('program-planning.program-definition.digital-initiatives.appendix.')->group(function () {
        Route::get('/', ProgramDefinitionDigitalInitiativesAppendixIndexController::class)->name('index');
        Route::get('/create', ProgramDefinitionDigitalInitiativesAppendixCreateController::class)->name('create');
        Route::post('/', ProgramDefinitionDigitalInitiativesAppendixStoreController::class)->name('store');
        Route::get('/{scInitiative}/edit', ProgramDefinitionDigitalInitiativesAppendixEditController::class)->name('edit');
        Route::put('/{scInitiative}', ProgramDefinitionDigitalInitiativesAppendixUpdateController::class)->name('update');
    });
    Route::prefix('/program-planning/program-definition/digital-initiatives/compendium')->name('program-planning.program-definition.digital-initiatives.compendium.')->group(function () {
        Route::get('/', ProgramDefinitionDigitalInitiativesCompendiumIndexController::class)->name('index');
        Route::get('/create', ProgramDefinitionDigitalInitiativesCompendiumCreateController::class)->name('create');
        Route::post('/', ProgramDefinitionDigitalInitiativesCompendiumStoreController::class)->name('store');
        Route::get('/{scInitiative}/edit', ProgramDefinitionDigitalInitiativesCompendiumEditController::class)->name('edit');
        Route::put('/{scInitiative}', ProgramDefinitionDigitalInitiativesCompendiumUpdateController::class)->name('update');
    });
    Route::get('/program-planning/program-definition/digital-initiatives/mapping', ProgramDefinitionDigitalInitiativesMappingIndexController::class)->name('program-planning.program-definition.digital-initiatives.mapping.index');
    Route::get('/program-planning/program-definition/digital-initiatives/master', ProgramDefinitionDigitalInitiativesMasterIndexController::class)->name('program-planning.program-definition.digital-initiatives.master.index');
    Route::prefix('/program-planning/program-definition/digital-initiatives/roadmap')->name('program-planning.program-definition.digital-initiatives.roadmap.')->group(function () {
        Route::get('/', ProgramDefinitionDigitalInitiativesRoadmapIndexController::class)->name('index');
        Route::get('/create', ProgramDefinitionDigitalInitiativesRoadmapCreateController::class)->name('create');
        Route::get('/edit', ProgramDefinitionDigitalInitiativesRoadmapEditController::class)->name('edit');
        Route::post('/', [ProgramDefinitionDigitalInitiativesRoadmapMilestoneController::class, 'store'])->name('store');
        Route::put('/{masterMilestone}', [ProgramDefinitionDigitalInitiativesRoadmapMilestoneController::class, 'update'])->name('update');
        Route::delete('/{masterMilestone}', [ProgramDefinitionDigitalInitiativesRoadmapMilestoneController::class, 'destroy'])->name('destroy');
    });

    // ═══ Master Data ═══════════════════════════════════════════════
    Route::get('/master-data', MasterDataController::class)->name('master-data.index');
    Route::get('/master-data/log-aktivitas', [MasterDataActivityLogController::class, 'index'])->name('master-data.activity-log.index');

    // Master Data → Master Initiative CRUD
    Route::prefix('/master-data/master-initiatives')->name('master-data.mst-initiatives.')->group(function () {
        Route::get('/', [MstInitiativeController::class, 'index'])->name('index');
        Route::get('/create', [MstInitiativeController::class, 'create'])->name('create');
        Route::post('/', [MstInitiativeController::class, 'store'])->name('store');
        Route::get('/{mstInitiative}/edit', [MstInitiativeController::class, 'edit'])->name('edit');
        Route::put('/{mstInitiative}', [MstInitiativeController::class, 'update'])->name('update');
        Route::delete('/{mstInitiative}', [MstInitiativeController::class, 'destroy'])->name('destroy');

        // Status history
        Route::post('/{mstInitiative}/status', [MstInitiativeController::class, 'storeStatus'])->name('status.store');
        Route::put('/status/{status}', [MstInitiativeController::class, 'updateStatus'])->name('status.update');
        Route::delete('/status/{status}', [MstInitiativeController::class, 'destroyStatus'])->name('status.destroy');
    });

    // Master Data → Business Capability CRUD (mst_business_capability)
    Route::prefix('/master-data/business-capabilities')->name('master-data.business-capabilities.')->group(function () {
        Route::get('/', [BusinessCapabilityController::class, 'index'])->name('index');
        Route::post('/', [BusinessCapabilityController::class, 'store'])->name('store');
        Route::put('/{businessCapability}', [BusinessCapabilityController::class, 'update'])->name('update');
        Route::delete('/{businessCapability}', [BusinessCapabilityController::class, 'destroy'])->name('destroy');
    });

    // Master Data → Scope Charter CRUD (trs_sc_initiative + trs_sc_status_implementation)
    Route::prefix('/master-data/scope-charter')->name('master-data.scope-charter.')->group(function () {
        Route::get('/', [ScopeCharterController::class, 'index'])->name('index');
        Route::post('/', [ScopeCharterController::class, 'store'])->name('store');
        Route::put('/{scopeCharter}', [ScopeCharterController::class, 'update'])->name('update');
        Route::delete('/{scopeCharter}', [ScopeCharterController::class, 'destroy'])->name('destroy');
    });

    // Master Data → Project Charter CRUD (trs_projects + trs_pc_status_implementation)
    Route::prefix('/master-data/project-charter')->name('master-data.project-charter.')->group(function () {
        Route::get('/', [MasterDataProjectCharterController::class, 'index'])->name('index');
        Route::post('/', [MasterDataProjectCharterController::class, 'store'])->name('store');
        Route::put('/{projectCharter}', [MasterDataProjectCharterController::class, 'update'])->name('update');
        Route::delete('/{projectCharter}', [MasterDataProjectCharterController::class, 'destroy'])->name('destroy');
    });

    Route::get('/program-planning/program-definition/it-initiatives', ProgramDefinitionITInitiativesController::class)->name('program-planning.program-definition.it-initiatives');
    Route::redirect('/program-planning/initiative', '/strategic-house/initiative-relation');
    Route::prefix('/strategic-house/initiative-relation')->name('initiative-relations.')->group(function () {
        Route::get('/', [StrategicHouseInitiativeRelationController::class, 'index'])->name('index');
        Route::get('/create', [StrategicHouseInitiativeRelationController::class, 'create'])->name('create');
        Route::post('/', [StrategicHouseInitiativeRelationController::class, 'store'])->name('store');
        Route::post('/sync-positions', [StrategicHouseInitiativeRelationController::class, 'syncPositions'])->name('sync-positions');
        Route::get('/{initiativeRelation}/edit', [StrategicHouseInitiativeRelationController::class, 'edit'])
            ->whereNumber('initiativeRelation')
            ->name('edit');
        Route::get('/{initiativeRelation}', [StrategicHouseInitiativeRelationController::class, 'show'])
            ->whereNumber('initiativeRelation')
            ->name('show');
        Route::put('/{initiativeRelation}', [StrategicHouseInitiativeRelationController::class, 'update'])
            ->whereNumber('initiativeRelation')
            ->name('update');
        Route::delete('/{initiativeRelation}', [StrategicHouseInitiativeRelationController::class, 'destroy'])
            ->whereNumber('initiativeRelation')
            ->name('destroy');
    });
    Route::get('/program-planning/initiative-relation', static fn () => redirect('/strategic-house/initiative-relation'));
    Route::get('/program-planning/initiative-relation/create', static fn () => redirect('/strategic-house/initiative-relation/create'));
    Route::get('/program-planning/initiative-relation/{initiativeRelation}/edit', [StrategicHouseInitiativeRelationController::class, 'edit'])
        ->whereNumber('initiativeRelation');
    Route::get('/program-planning/initiative-relation/{initiativeRelation}', [StrategicHouseInitiativeRelationController::class, 'show'])
        ->whereNumber('initiativeRelation');
    Route::post('/program-planning/initiative-relation', [StrategicHouseInitiativeRelationController::class, 'store']);
    Route::put('/program-planning/initiative-relation/{initiativeRelation}', [StrategicHouseInitiativeRelationController::class, 'update'])
        ->whereNumber('initiativeRelation');
    Route::delete('/program-planning/initiative-relation/{initiativeRelation}', [StrategicHouseInitiativeRelationController::class, 'destroy'])
        ->whereNumber('initiativeRelation');
    Route::redirect('/program-implementation/initiative-relation', '/strategic-house/initiative-relation');
    Route::redirect('/program-implementation/initiative-relation/create', '/strategic-house/initiative-relation/create');
    Route::get('/program-implementation/initiative-relation/{initiativeRelation}/edit', static function (string $initiativeRelation) {
        return redirect("/strategic-house/initiative-relation/{$initiativeRelation}/edit");
    });
    Route::get('/program-implementation', ProgramImplementationController::class)->name('program-implementation.index');

    // IT Building Blocks (moved to Program Planning)
    Route::get('/program-planning/it-building-blocks', StrategicHouseItBuildingBlockController::class)->name('program-planning.it-building-blocks.index');
    Route::post('/program-planning/it-building-blocks', [StrategicHouseItBuildingBlockController::class, 'store'])->name('program-planning.it-building-blocks.store');
    Route::delete('/program-planning/it-building-blocks/primary/{primary}', [StrategicHouseItBuildingBlockController::class, 'destroyPrimary'])->name('program-planning.it-building-blocks.primary.destroy');
    Route::delete('/program-planning/it-building-blocks/primary/{primary}/secondary/{secondary}', [StrategicHouseItBuildingBlockController::class, 'destroySecondary'])->name('program-planning.it-building-blocks.secondary.destroy');
    Route::delete('/program-planning/it-building-blocks/primary/{primary}/secondary/{secondary}/initiative/{initiative}', [StrategicHouseItBuildingBlockController::class, 'destroyInitiative'])->name('program-planning.it-building-blocks.initiative.destroy');
    Route::post('/program-planning/it-building-blocks/initiatives/bulk-delete', [StrategicHouseItBuildingBlockController::class, 'destroyInitiatives'])->name('program-planning.it-building-blocks.initiative.bulk-destroy');

    // Redirects untuk backward-compatibility: URL lama → URL baru
    Route::redirect('/program-implementation/it-building-blocks', '/program-planning/it-building-blocks');
    Route::post('/program-implementation/it-building-blocks', [StrategicHouseItBuildingBlockController::class, 'store']);
    Route::redirect('/program-implementation/it-building-blocks/primary/{primary}', '/program-planning/it-building-blocks');
    Route::redirect('/program-implementation/it-building-blocks/primary/{primary}/secondary/{secondary}', '/program-planning/it-building-blocks');

    Route::get('/program-implementation/resources-management', ResourceManagementController::class)
        ->name('program-implementation.resources-management.index');
    Route::get('/program-implementation/budgeting', fn () => redirect()->route('program-implementation.resources-management.index'))
        ->name('program-implementation.budgeting');
    Route::get('/architecture', fn () => Inertia::render('Architecture/Index'))->name('architecture.index');
    Route::get('/architecture/organization-structure', ArchitectureOrganizationStructureIndexController::class)->name('architecture.organization-structure');
    Route::get('/architecture/informatic-system', fn () => Inertia::render('Architecture/InformaticSystem/Index'))->name('architecture.informatic-system');
    Route::get('/resources-management', fn () => redirect()->route('program-implementation.resources-management.index'))
        ->name('resources-management.index');
    Route::get('/service-portofolio', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Service Portofolio',
    ]))->name('service-portofolio.index');
    Route::get('/policy', fn () => Inertia::render('Policy/Index'))->name('policy.index');
    Route::get('/program-information', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Program Information',
    ]))->name('program-information.index');

    // Program Evaluation
    Route::redirect('/program-evalution', '/program-evalution/review');
    Route::get('/program-evalution/review', [TrsReviewPCController::class, 'index'])->name('program-evaluation.index');
    Route::post('/program-evalution/review', [TrsReviewPCController::class, 'store'])->name('program-evaluation.store');
    Route::get('/program-evalution/review/{trsReviewPC}', [TrsReviewPCController::class, 'show'])->name('program-evaluation.show');
    Route::put('/program-evalution/review/{trsReviewPC}', [TrsReviewPCController::class, 'update'])->name('program-evaluation.update');
    Route::get('/program-evalution/review-timeline', [ReviewTimelineController::class, 'index'])->name('program-evaluation.review-timeline');
    Route::get('/program-evalution/review-dashboard', [ReviewDashboardController::class, 'index'])->name('program-evaluation.review-dashboard');
    Route::get('/program-evalution/review-summary', [ReviewDashboardController::class, 'summary'])->name('program-evaluation.review-summary');
    Route::post('/program-evalution/review-timeline/review-status-implementation/{statusId}', [ReviewTimelineController::class, 'updateReviewStatusImplementation'])->name('program-evaluation.review-timeline.review-status.update');

    // Summary Review Notes (TrsReviewSc)
    Route::post('/program-evaluation/summary-review/notes', [\App\Http\Controllers\ProgramEvaluation\TrsReviewScController::class, 'store'])->name('program-evaluation.summary-review.notes.store');
    Route::put('/program-evaluation/summary-review/notes/{trsReviewSc}', [\App\Http\Controllers\ProgramEvaluation\TrsReviewScController::class, 'update'])->name('program-evaluation.summary-review.notes.update');
    Route::delete('/program-evaluation/summary-review/notes/{trsReviewSc}', [\App\Http\Controllers\ProgramEvaluation\TrsReviewScController::class, 'destroy'])->name('program-evaluation.summary-review.notes.destroy');

    // Strategic House
    Route::prefix('/strategic-house')->name('strategic-house.')->group(function () {
        Route::get('/', StrategicHouseController::class)->name('index');
        Route::get('/business-strategy', StrategicHouseBusinessStrategyController::class)->name('business-strategy.index');
        Route::post('/business-strategy', [StrategicHouseBusinessStrategyManageController::class, 'store'])->name('business-strategy.store');
        Route::put('/business-strategy/bulk-update', [StrategicHouseBusinessStrategyManageController::class, 'bulkUpdate'])->name('business-strategy.bulk-update');
        Route::put('/business-strategy/{businessStrategy}', [StrategicHouseBusinessStrategyManageController::class, 'update'])->name('business-strategy.update');
        Route::delete('/business-strategy/{businessStrategy}', [StrategicHouseBusinessStrategyManageController::class, 'destroy'])->name('business-strategy.destroy');
        Route::prefix('/initiative-support')->name('initiative-support.')->group(function () {
            Route::get('/', StrategicHouseInitiativeSupportIndexController::class)->name('index');
            Route::post('/', [StrategicHouseInitiativeSupportIndexController::class, 'store'])->name('store');
            Route::post('/mappings/delete', [StrategicHouseInitiativeSupportIndexController::class, 'destroyMappings'])->name('mappings.destroy');
        });
        Route::get('/roadmap', StrategicHouseRoadmapIndexController::class)->name('roadmap.index');
        Route::get('/roadmap-summary', StrategicHouseRoadmapSummaryController::class)->name('roadmap-summary.index');

        // Map Technology management
        Route::prefix('/map-technology')->name('map-technology.')->group(function () {
            Route::post('/', [MapTechnologyController::class, 'store'])->name('store');
            Route::post('/bulk-destroy', [MapTechnologyController::class, 'bulkDestroy'])->name('bulk-destroy');
        });
    });
    Route::get('/strategic-house/strategic-pillars/{goal?}', StrategicHouseStrategicPillarsIndexController::class)->name('strategic-pillars.index');
    Route::post('/strategic-house/strategic-pillars/goals', [StrategicHouseStrategicPillarGoalController::class, 'store'])->name('strategic-pillars.goals.store');
    Route::put('/strategic-house/strategic-pillars/goals/{goal}', [StrategicHouseStrategicPillarGoalController::class, 'update'])->name('strategic-pillars.goals.update');
    Route::delete('/strategic-house/strategic-pillars/goals/{goal}', [StrategicHouseStrategicPillarGoalController::class, 'destroy'])->name('strategic-pillars.goals.destroy');
    Route::post('/strategic-house/strategic-pillars/themes', [StrategicHouseStrategicPillarThemeController::class, 'store'])->name('strategic-pillars.themes.store');
    Route::put('/strategic-house/strategic-pillars/themes/{theme}', [StrategicHouseStrategicPillarThemeController::class, 'update'])->name('strategic-pillars.themes.update');
    Route::delete('/strategic-house/strategic-pillars/themes/{theme}', [StrategicHouseStrategicPillarThemeController::class, 'destroy'])->name('strategic-pillars.themes.destroy');
    Route::post('/strategic-house/strategic-pillars/tagging', [StrategicHouseInitiativeTaggingController::class, 'store'])->name('strategic-pillars.tagging.store');
    Route::delete('/strategic-house/strategic-pillars/tagging/{tagging}', [StrategicHouseInitiativeTaggingController::class, 'destroy'])->name('strategic-pillars.tagging.destroy');

    Route::redirect('/program-planning/strategic-house', '/strategic-house');
    Route::redirect('/program-planning/strategic-house/business-strategy', '/strategic-house/business-strategy');
    Route::post('/program-planning/strategic-house/business-strategy', [StrategicHouseBusinessStrategyManageController::class, 'store']);
    Route::put('/program-planning/strategic-house/business-strategy/bulk-update', [StrategicHouseBusinessStrategyManageController::class, 'bulkUpdate']);
    Route::put('/program-planning/strategic-house/business-strategy/{businessStrategy}', [StrategicHouseBusinessStrategyManageController::class, 'update']);
    Route::delete('/program-planning/strategic-house/business-strategy/{businessStrategy}', [StrategicHouseBusinessStrategyManageController::class, 'destroy']);
    Route::get('/program-planning/strategic-house/initiative-support', static fn () => redirect('/strategic-house/initiative-support'));
    Route::post('/program-planning/strategic-house/initiative-support', [StrategicHouseInitiativeSupportIndexController::class, 'store']);
    Route::post('/program-planning/strategic-house/initiative-support/mappings/delete', [StrategicHouseInitiativeSupportIndexController::class, 'destroyMappings']);
    Route::redirect('/program-planning/strategic-house/roadmap', '/strategic-house/roadmap');
    Route::get('/program-planning/strategic-house/roadmap-summary', StrategicHouseRoadmapSummaryController::class)
        ->name('program-planning.strategic-house.roadmap-summary.index');
    Route::get('/strategic-pillars/{goal?}', StrategicHouseStrategicPillarsIndexController::class);
    Route::post('/strategic-pillars/goals', [StrategicHouseStrategicPillarGoalController::class, 'store']);
    Route::put('/strategic-pillars/goals/{goal}', [StrategicHouseStrategicPillarGoalController::class, 'update']);
    Route::delete('/strategic-pillars/goals/{goal}', [StrategicHouseStrategicPillarGoalController::class, 'destroy']);
    Route::post('/strategic-pillars/themes', [StrategicHouseStrategicPillarThemeController::class, 'store']);
    Route::put('/strategic-pillars/themes/{theme}', [StrategicHouseStrategicPillarThemeController::class, 'update']);
    Route::delete('/strategic-pillars/themes/{theme}', [StrategicHouseStrategicPillarThemeController::class, 'destroy']);
    Route::post('/strategic-pillars/tagging', [StrategicHouseInitiativeTaggingController::class, 'store']);
    Route::delete('/strategic-pillars/tagging/{tagging}', [StrategicHouseInitiativeTaggingController::class, 'destroy']);

    // Digital Initiatives
    Route::post('/digital-initiatives/implementation-status', [DigitalInitiativeController::class, 'storeImplementationStatus'])
        ->name('digital-initiatives.implementation-status.store');
    Route::put('/digital-initiatives/implementation-status/{statusId}', [DigitalInitiativeController::class, 'updateImplementationStatus'])
        ->name('digital-initiatives.implementation-status.update');
    Route::delete('/digital-initiatives/implementation-status/{statusId}', [DigitalInitiativeController::class, 'destroyImplementationStatus'])
        ->name('digital-initiatives.implementation-status.destroy');
    Route::resource('digital-initiatives', DigitalInitiativeController::class);
    Route::put('/digital-initiatives/{digital_initiative}/project-status-history/{history}', [DigitalInitiativeController::class, 'updateProjectStatusHistory'])->name('digital-initiatives.project-status-history.update');
    Route::delete('/digital-initiatives/{digital_initiative}/project-status-history/{history}', [DigitalInitiativeController::class, 'destroyProjectStatusHistory'])->name('digital-initiatives.project-status-history.destroy');

    // IT Initiatives & Charters
    // Roadmap — dedicated controller (all programs & per-program views)
    Route::get('/roadmap', [RoadmapController::class, 'index'])->name('roadmap.index');
    Route::get('/roadmap/add', [RoadmapController::class, 'add'])->name('roadmap.add');
    Route::get('/roadmap/edit', [RoadmapController::class, 'edit'])->name('roadmap.edit');
    Route::get('/roadmap/initiative/{initiative}', [RoadmapController::class, 'show'])->name('roadmap.show');

    Route::resource('it-initiatives', ITInitiativeController::class)
        ->parameters(['it-initiatives' => 'project']);
    Route::post('/it-initiatives/{project}/charter', [CharterController::class, 'store'])->name('it-initiatives.charter.store');
    Route::put('/it-initiatives/{project}/charter/{charter}', [CharterController::class, 'update'])->name('it-initiatives.charter.update');
    Route::post('/it-initiatives/{project}/version-analysis', [VersionAnalysisController::class, 'store'])->name('it-initiatives.version-analysis.store');
    Route::put('/it-initiatives/{project}/version-analysis/{analysis}', [VersionAnalysisController::class, 'update'])->name('it-initiatives.version-analysis.update');
    Route::post('/it-initiatives/{project}/milestones/versions', [MilestoneController::class, 'createVersion'])->name('it-initiatives.milestones.versions.store');
    Route::post('/it-initiatives/{project}/milestones', [MilestoneController::class, 'store'])->name('it-initiatives.milestones.store');
    Route::put('/it-initiatives/{project}/milestones/{milestone}', [MilestoneController::class, 'update'])->name('it-initiatives.milestones.update');
    Route::delete('/it-initiatives/{project}/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('it-initiatives.milestones.destroy');
    Route::post('/it-initiatives/{project}/implementation-status', [ITInitiativeController::class, 'storeImplementationStatus'])->name('it-initiatives.implementation-status.store');

    Route::put('/implementation-status/{id}', [ITInitiativeController::class, 'updateImplementationStatus'])->name('it-initiatives.implementation-status.update');
    Route::delete('/implementation-status/{id}', [ITInitiativeController::class, 'destroyImplementationStatus'])->name('it-initiatives.implementation-status.destroy');
    Route::put('/it-initiatives/{project}/project-status-history/{history}', [ITInitiativeController::class, 'updateProjectStatusHistory'])->name('it-initiatives.project-status-history.update');
    Route::delete('/it-initiatives/{project}/project-status-history/{history}', [ITInitiativeController::class, 'destroyProjectStatusHistory'])->name('it-initiatives.project-status-history.destroy');
    Route::put('/it-initiatives/{project}/mapping', [ITInitiativeController::class, 'updateMapping'])->name('it-initiatives.mapping.update');

    // Cloud Data Synchronization (Accessible for all validated app instances)
    Route::get('/sync', [SyncController::class, 'index'])->name('sync.index');
    Route::post('/sync/pull', [SyncController::class, 'pull'])->name('sync.pull');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'approved', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::post('/switch-database', [DatabaseSwitcherController::class, 'switch'])->name('switch-database');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/permissions', [AdminRoleController::class, 'storePermission'])->name('roles.permissions.store');
    Route::put('/roles/{role}/permissions', [AdminRoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup/download/{file}', [BackupController::class, 'download'])
        ->where('file', '[A-Za-z0-9._-]+')
        ->name('backup.download');
    Route::post('/backup/run', [BackupController::class, 'run'])->name('backup.run');
    Route::put('/backup/settings', [BackupController::class, 'updateRetention'])->name('backup.settings.update');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});

/*
|--------------------------------------------------------------------------
| Catch-all redirect
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));
