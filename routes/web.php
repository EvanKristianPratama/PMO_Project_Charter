<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DatabaseSwitcherController;
use App\Http\Controllers\Admin\SyncController;
use App\Http\Controllers\BusinessProcess\BusinessCapability\BusinessCapabilityController;
use App\Http\Controllers\BusinessProcess\OrganizationStructure\OrganizationController as BusinessProcessOrganizationStructureController;
use App\Http\Controllers\BusinessProcess\ProsesBisnis\ProsesBisnisController as BusinessProcessProsesBisnisController;
use App\Http\Controllers\ResourceManagement\ResourceManagementController as MainResourceManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\BpmnWorkflowController;
use App\Http\Controllers\LibaryController;
use App\Http\Controllers\MasterData\ActivityLogController as MasterDataActivityLogController;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Http\Controllers\MasterData\MstInitiative\MstInitiativeController;
use App\Http\Controllers\MasterData\MstPicProject\MstPicProjectController;
use App\Http\Controllers\MasterData\ProjectCharter\ProjectCharterController as MasterDataProjectCharterController;
use App\Http\Controllers\MasterData\ScopeCharter\ScopeCharterController;
use App\Http\Controllers\Policy\EITOrganizationController;
use App\Http\Controllers\Policy\GeneralPolicyController;
use App\Http\Controllers\Policy\PolicyController;
use App\Http\Controllers\Policy\InfoflowController;
use App\Http\Controllers\Policy\ItspInfoflowController;
use App\Http\Controllers\Policy\PracticeRoleController;
use App\Http\Controllers\Policy\ProcedureController;
use App\Http\Controllers\Policy\RegulationController;
use App\Http\Controllers\Policy\ResponsibleController;
use App\Http\Controllers\Policy\RoleController;
use App\Http\Controllers\ProgramEvaluation\ReviewAktorController;
use App\Http\Controllers\ProgramEvaluation\ReviewDashboardController;
use App\Http\Controllers\ProgramEvaluation\ReviewDocumentController;
use App\Http\Controllers\ProgramEvaluation\ReviewTimelineController;
use App\Http\Controllers\ProgramEvaluation\TrsReviewPCController;
use App\Http\Controllers\ProgramEvaluation\TrsReviewScController;
use App\Http\Controllers\ProgramImplementation\DashboardController;
use App\Http\Controllers\ProgramImplementation\ProgramImplementationController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\DigitalInitiatives\DigitalInitiativeController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\CharterController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\ITInitiativeController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\MilestoneController;
use App\Http\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\VersionAnalysisController;
use App\Http\Controllers\ProgramImplementation\ResourceManagementController;
use App\Http\Controllers\ProgramImplementation\Roadmap\RoadmapController;
use App\Http\Controllers\ProgramImplementation\ValueCreationController;
use App\Http\Controllers\ProgramPlanning\BusinessStrategy\IndexController as ProgramPlanningBusinessStrategyIndexController;
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
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\CreateController as ProgramDefinitionDigitalInitiativesRoadmapCreateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\EditController as ProgramDefinitionDigitalInitiativesRoadmapEditController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\IndexController as ProgramDefinitionDigitalInitiativesRoadmapIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MilestoneController as ProgramDefinitionDigitalInitiativesRoadmapMilestoneController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Summary\IndexController as ProgramDefinitionDigitalInitiativesSummaryIndexController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\UpdateController as ProgramDefinitionDigitalInitiativesUpdateController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\IndexController as ProgramDefinitionController;
use App\Http\Controllers\ProgramPlanning\ProgramDefinition\ITInitiatives\IndexController as ProgramDefinitionITInitiativesController;
use App\Http\Controllers\ProgramPlanning\ProgramPlanningController;
use App\Http\Controllers\PublicSyncController;
use App\Http\Controllers\StrategicHouse\BusinessStrategy\BusinessStrategyController as StrategicHouseBusinessStrategyManageController;
use App\Http\Controllers\StrategicHouse\BusinessStrategy\IndexController as StrategicHouseBusinessStrategyController;
use App\Http\Controllers\StrategicHouse\IndexController as StrategicHouseController;
use App\Http\Controllers\StrategicHouse\InitiativeRelation\InitiativeRelationController as StrategicHouseInitiativeRelationController;
use App\Http\Controllers\StrategicHouse\InitiativeSupport\IndexController as StrategicHouseInitiativeSupportIndexController;
use App\Http\Controllers\StrategicHouse\ItBuildingBlock\IndexController as StrategicHouseItBuildingBlockController;
use App\Http\Controllers\StrategicHouse\MapTechnology\MapTechnologyController;
use App\Http\Controllers\StrategicHouse\RoadMap\IndexController as StrategicHouseRoadmapIndexController;
use App\Http\Controllers\StrategicHouse\RoadMap\SummaryController as StrategicHouseRoadmapSummaryController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\GoalController as StrategicHouseStrategicPillarGoalController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\IndexController as StrategicHouseStrategicPillarsIndexController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\InitiativeTaggingController as StrategicHouseInitiativeTaggingController;
use App\Http\Controllers\StrategicHouse\StrategicPillars\ThemeController as StrategicHouseStrategicPillarThemeController;
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
    Route::get('/blank', fn () => Inertia::render('BlankPage/Index'))->name('blank');
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

    // Master Data → PIC Project CRUD
    Route::prefix('/master-data/pic-projects')->name('master-data.pic-projects.')->group(function () {
        Route::get('/', [MstPicProjectController::class, 'index'])->name('index');
        Route::post('/', [MstPicProjectController::class, 'store'])->name('store');
        Route::put('/{picProject}', [MstPicProjectController::class, 'update'])->name('update');
        Route::delete('/{picProject}', [MstPicProjectController::class, 'destroy'])->name('destroy');
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
    Route::get('/business-process', fn () => redirect()->route('business-process.proses-bisnis.index'))->name('business-process.index');
    Route::get('/business-process/organization-structure', [BusinessProcessOrganizationStructureController::class, 'index'])->name('business-process.organization-structure');
    Route::post('/business-process/organization-structure', [BusinessProcessOrganizationStructureController::class, 'store'])->name('business-process.organization-structure.store');
    Route::post('/business-process/organization-structure/company', [BusinessProcessOrganizationStructureController::class, 'storeCompany'])->name('business-process.organization-structure.company.store');
    Route::put('/business-process/organization-structure/company/{id}', [BusinessProcessOrganizationStructureController::class, 'updateCompany'])->name('business-process.organization-structure.company.update');
    Route::delete('/business-process/organization-structure/company/{id}', [BusinessProcessOrganizationStructureController::class, 'destroyCompany'])->name('business-process.organization-structure.company.destroy');
    Route::post('/business-process/organization-structure/group', [BusinessProcessOrganizationStructureController::class, 'storeGroup'])->name('business-process.organization-structure.group.store');
    Route::put('/business-process/organization-structure/group/{id}', [BusinessProcessOrganizationStructureController::class, 'updateGroup'])->name('business-process.organization-structure.group.update');
    Route::delete('/business-process/organization-structure/group/{id}', [BusinessProcessOrganizationStructureController::class, 'destroyGroup'])->name('business-process.organization-structure.group.destroy');
    Route::put('/business-process/organization-structure/{organization}', [BusinessProcessOrganizationStructureController::class, 'update'])->name('business-process.organization-structure.update');
    Route::delete('/business-process/organization-structure/{organization}', [BusinessProcessOrganizationStructureController::class, 'destroy'])->name('business-process.organization-structure.destroy');
    Route::post('/business-process/organization-structure/bod', [BusinessProcessOrganizationStructureController::class, 'storeBod'])->name('business-process.organization-structure.bod.store');
    Route::put('/business-process/organization-structure/bod/{id}', [BusinessProcessOrganizationStructureController::class, 'updateBod'])->name('business-process.organization-structure.bod.update');
    Route::delete('/business-process/organization-structure/bod/{id}', [BusinessProcessOrganizationStructureController::class, 'destroyBod'])->name('business-process.organization-structure.bod.destroy');
    Route::post('/business-process/organization-structure/sk', [BusinessProcessOrganizationStructureController::class, 'storeSk'])->name('business-process.organization-structure.sk.store');
    Route::put('/business-process/organization-structure/sk/{id}', [BusinessProcessOrganizationStructureController::class, 'updateSk'])->name('business-process.organization-structure.sk.update');
    Route::delete('/business-process/organization-structure/sk/{id}', [BusinessProcessOrganizationStructureController::class, 'destroySk'])->name('business-process.organization-structure.sk.destroy');
    Route::post('/business-process/organization-structure/functional', [BusinessProcessOrganizationStructureController::class, 'storeFunctional'])->name('business-process.organization-structure.functional.store');
    Route::put('/business-process/organization-structure/functional/{id}', [BusinessProcessOrganizationStructureController::class, 'updateFunctional'])->name('business-process.organization-structure.functional.update');
    Route::delete('/business-process/organization-structure/functional/{id}', [BusinessProcessOrganizationStructureController::class, 'destroyFunctional'])->name('business-process.organization-structure.functional.destroy');
    Route::post('/business-process/organization-structure/functional/member', [BusinessProcessOrganizationStructureController::class, 'storeFunctionalMember'])->name('business-process.organization-structure.functional.member.store');
    Route::delete('/business-process/organization-structure/functional/member', [BusinessProcessOrganizationStructureController::class, 'destroyFunctionalMember'])->name('business-process.organization-structure.functional.member.destroy');
    Route::post('/business-process/organization-structure/functional/structure', [BusinessProcessOrganizationStructureController::class, 'storeFunctionalStructure'])->name('business-process.organization-structure.functional.structure.store');
    Route::delete('/business-process/organization-structure/functional/structure', [BusinessProcessOrganizationStructureController::class, 'destroyFunctionalStructure'])->name('business-process.organization-structure.functional.structure.destroy');
    Route::get('/business-process/informatic-system', fn () => Inertia::render('BusinessProcess/InformaticSystem/Index'))->name('business-process.informatic-system');
    Route::get('/business-process/business-capability', [BusinessCapabilityController::class, 'index'])->name('business-process.business-capability.index');
    Route::post('/business-process/business-capability', [BusinessCapabilityController::class, 'store'])->name('business-process.business-capability.store');
    Route::put('/business-process/business-capability/{businessCapability}', [BusinessCapabilityController::class, 'update'])->name('business-process.business-capability.update');
    Route::delete('/business-process/business-capability/{businessCapability}', [BusinessCapabilityController::class, 'destroy'])->name('business-process.business-capability.destroy');

    // Proses Bisnis (Business Process) CRUD under Architecture
    Route::get('/business-process/proses-bisnis', [BusinessProcessProsesBisnisController::class, 'index'])->name('business-process.proses-bisnis.index');
    Route::get('/business-process/proses-bisnis/manage', [BusinessProcessProsesBisnisController::class, 'manage'])->name('business-process.proses-bisnis.manage');
    Route::post('/business-process/proses-bisnis', [BusinessProcessProsesBisnisController::class, 'store'])->name('business-process.proses-bisnis.store');
    Route::put('/business-process/proses-bisnis/{id}', [BusinessProcessProsesBisnisController::class, 'update'])->name('business-process.proses-bisnis.update');
    Route::delete('/business-process/proses-bisnis/{id}', [BusinessProcessProsesBisnisController::class, 'destroy'])->name('business-process.proses-bisnis.destroy');
    // APQC CRUD under Architecture
    Route::post('/business-process/apqc', [BusinessProcessProsesBisnisController::class, 'storeApqc'])->name('business-process.apqc.store');
    Route::put('/business-process/apqc/{id}', [BusinessProcessProsesBisnisController::class, 'updateApqc'])->name('business-process.apqc.update');
    Route::delete('/business-process/apqc/{id}', [BusinessProcessProsesBisnisController::class, 'destroyApqc'])->name('business-process.apqc.destroy');

    // Proses Bisnis v2 CRUD under Architecture
    Route::post('/business-process/proses-bisnis-v2', [BusinessProcessProsesBisnisController::class, 'storeProsesBisnisV2'])->name('business-process.proses-bisnis-v2.store');
    Route::put('/business-process/proses-bisnis-v2/{id}', [BusinessProcessProsesBisnisController::class, 'updateProsesBisnisV2'])->name('business-process.proses-bisnis-v2.update');
    Route::delete('/business-process/proses-bisnis-v2/{id}', [BusinessProcessProsesBisnisController::class, 'destroyProsesBisnisV2'])->name('business-process.proses-bisnis-v2.destroy');

    // Function CRUD under Architecture
    Route::post('/business-process/function', [BusinessProcessProsesBisnisController::class, 'storeFunction'])->name('business-process.function.store');
    Route::put('/business-process/function/{id}', [BusinessProcessProsesBisnisController::class, 'updateFunction'])->name('business-process.function.update');
    Route::delete('/business-process/function/{id}', [BusinessProcessProsesBisnisController::class, 'destroyFunction'])->name('business-process.function.destroy');

    // KPI CRUD under Architecture
    Route::post('/business-process/kpi', [BusinessProcessProsesBisnisController::class, 'storeKpi'])->name('business-process.kpi.store');
    Route::put('/business-process/kpi/{id}', [BusinessProcessProsesBisnisController::class, 'updateKpi'])->name('business-process.kpi.update');
    Route::delete('/business-process/kpi/{id}', [BusinessProcessProsesBisnisController::class, 'destroyKpi'])->name('business-process.kpi.destroy');

    Route::get('/resources-management', fn () => redirect()->route('resource-management.index'))
        ->name('resources-management.index');

    // Resource Management CRUD
    Route::prefix('/resource-management')->name('resource-management.')->group(function () {
        Route::get('/', [MainResourceManagementController::class, 'index'])->name('index');
        Route::post('/', [MainResourceManagementController::class, 'store'])->name('store');
        Route::put('/{resource}', [MainResourceManagementController::class, 'update'])->name('update');
        Route::delete('/{resource}', [MainResourceManagementController::class, 'destroy'])->name('destroy');
    });
    Route::get('/service-portofolio', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Service Portofolio',
    ]))->name('service-portofolio.index');

    // Policy CRUD (mst_general_policy, mst_objective & mst_practice)
    Route::prefix('/policy')->name('policy.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('policy.regulation.index');
        })->name('index');

        // Guidance Intro & Outro chapters (Bab I & Bab V)
        Route::get('/guidance/introduction', [GeneralPolicyController::class, 'introduction'])->name('guidance.introduction');
        Route::get('/guidance/closing', [GeneralPolicyController::class, 'closing'])->name('guidance.closing');

        // Kebijakan Umum (General Policy) CRUD
        Route::get('/general', [GeneralPolicyController::class, 'index'])->name('general.index');
        Route::get('/general/manage', [GeneralPolicyController::class, 'manage'])->name('general.manage');
        Route::post('/general', [GeneralPolicyController::class, 'store'])->name('general.store');
        Route::put('/general/{id}', [GeneralPolicyController::class, 'update'])->name('general.update');
        Route::delete('/general/{id}', [GeneralPolicyController::class, 'destroy'])->name('general.destroy');

        // Kebijakan Khusus (Specific Policy) CRUD
        Route::get('/specific', function () {
            return redirect()->route('policy.general.index');
        })->name('specific.index');
        Route::get('/specific/manage', [PolicyController::class, 'manage'])->name('specific.manage');
        Route::post('/objective', [PolicyController::class, 'storeObjective'])->name('objective.store');
        Route::put('/objective/{objective}', [PolicyController::class, 'updateObjective'])->name('objective.update');
        Route::delete('/objective/{objective}', [PolicyController::class, 'destroyObjective'])->name('objective.destroy');
        Route::post('/practice', [PolicyController::class, 'storePractice'])->name('practice.store');
        Route::put('/practice/{practice}', [PolicyController::class, 'updatePractice'])->name('practice.update');
        Route::delete('/practice/{practice}', [PolicyController::class, 'destroyPractice'])->name('practice.destroy');

        // Roles & Responsibilities CRUD
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/manage', [RoleController::class, 'manage'])->name('roles.manage');
        Route::post('/roles/role', [RoleController::class, 'storeRole'])->name('roles.role.store');
        Route::put('/roles/role/{role}', [RoleController::class, 'updateRole'])->name('roles.role.update');
        Route::delete('/roles/role/{role}', [RoleController::class, 'destroyRole'])->name('roles.role.destroy');
        Route::post('/roles/responsibility', [RoleController::class, 'storeResponsibility'])->name('roles.responsibility.store');
        Route::put('/roles/responsibility/{responsibility}', [RoleController::class, 'updateResponsibility'])->name('roles.responsibility.update');
        Route::delete('/roles/responsibility/{responsibility}', [RoleController::class, 'destroyResponsibility'])->name('roles.responsibility.destroy');

        // Mapped Responsibles from Master Data
        Route::post('/roles/mapped-responsible', [RoleController::class, 'storeMappedResponsible'])->name('roles.mapped-responsible.store');
        Route::delete('/roles/mapped-responsible/{roleId}/{responsibleId}', [RoleController::class, 'destroyMappedResponsible'])->name('roles.mapped-responsible.destroy');
        Route::post('/roles/responsible-practice', [RoleController::class, 'updateResponsiblePractice'])->name('roles.responsible-practice.update');
        Route::post('/roles/objective-responsible/{objectiveId}', [RoleController::class, 'updateObjectiveResponsibles'])->name('roles.objective-responsible.update');

        // Regulasi (Regulation) CRUD
        Route::get('/regulation/{id}/preview', [RegulationController::class, 'previewData'])->name('regulation.preview');
        Route::get('/regulation', [RegulationController::class, 'index'])->name('regulation.index');
        Route::post('/regulation', [RegulationController::class, 'store'])->name('regulation.store');
        Route::put('/regulation/{id}', [RegulationController::class, 'update'])->name('regulation.update');
        Route::delete('/regulation/{id}', [RegulationController::class, 'destroy'])->name('regulation.destroy');

        // Prosedur (Procedure) placeholder
        Route::get('/procedure', [ProcedureController::class, 'index'])->name('procedure.index');
        Route::get('/procedure/manage', [ProcedureController::class, 'manage'])->name('procedure.manage');
        Route::post('/procedure/actor', [ProcedureController::class, 'storeActor'])->name('procedure.actor.store');
        Route::put('/procedure/actor/{id}', [ProcedureController::class, 'updateActor'])->name('procedure.actor.update');
        Route::delete('/procedure/actor/{id}', [ProcedureController::class, 'destroyActor'])->name('procedure.actor.destroy');
        Route::post('/procedure/category', [ProcedureController::class, 'storeCategory'])->name('procedure.category.store');
        Route::put('/procedure/category/{id}', [ProcedureController::class, 'updateCategory'])->name('procedure.category.update');
        Route::delete('/procedure/category/{id}', [ProcedureController::class, 'destroyCategory'])->name('procedure.category.destroy');
        Route::post('/procedure/sop', [ProcedureController::class, 'storeSop'])->name('procedure.sop.store');
        Route::put('/procedure/sop/{id}', [ProcedureController::class, 'updateSop'])->name('procedure.sop.update');
        Route::delete('/procedure/sop/{id}', [ProcedureController::class, 'destroySop'])->name('procedure.sop.destroy');
        Route::post('/procedure/diagram', [ProcedureController::class, 'storeDiagram'])->name('procedure.diagram.store');
        Route::put('/procedure/diagram/{id}', [ProcedureController::class, 'updateDiagram'])->name('procedure.diagram.update');
        Route::delete('/procedure/diagram/{id}', [ProcedureController::class, 'destroyDiagram'])->name('procedure.diagram.destroy');
        Route::post('/procedure/tko-content', [ProcedureController::class, 'storeOrUpdateTkoContent'])->name('procedure.tko-content.store');
        Route::post('/procedure/tko-content/save-structured', [ProcedureController::class, 'saveStructuredDocument'])->name('procedure.tko-content.save-structured');
        Route::post('/procedure/section', [ProcedureController::class, 'storeSection'])->name('procedure.section.store');
        Route::put('/procedure/section/{id}', [ProcedureController::class, 'updateSection'])->name('procedure.section.update');
        Route::delete('/procedure/section/{id}', [ProcedureController::class, 'destroySection'])->name('procedure.section.destroy');

        Route::get('/organization', [EITOrganizationController::class, 'index'])->name('organization.index');
        Route::post('/organization/steering', [EITOrganizationController::class, 'storeSteering'])->name('organization.steering.store');
        Route::put('/organization/steering/{id}', [EITOrganizationController::class, 'updateSteering'])->name('organization.steering.update');
        Route::delete('/organization/steering/{id}', [EITOrganizationController::class, 'destroySteering'])->name('organization.steering.destroy');

        // Matriks RACI (RACI Matrix) mapping
        Route::get('/raci', [PracticeRoleController::class, 'index'])->name('raci.index');
        Route::get('/raci/manage', [PracticeRoleController::class, 'manage'])->name('raci.manage');
        Route::post('/raci', [PracticeRoleController::class, 'update'])->name('raci.update');

        // GAMO Information Flow
        Route::get('/infoflow', [InfoflowController::class, 'index'])->name('infoflow.index');

        // ITSP Information Flow
        Route::get('/itsp-infoflow', [ItspInfoflowController::class, 'index'])->name('itsp-infoflow.index');
        Route::get('/itsp-infoflow/data', [ItspInfoflowController::class, 'getData'])->name('itsp-infoflow.data');
        Route::post('/itsp-infoflow/save', [ItspInfoflowController::class, 'saveData'])->name('itsp-infoflow.save');
        Route::get('/itsp-infoflow/manage', [ItspInfoflowController::class, 'manage'])->name('itsp-infoflow.manage');
        Route::post('/itsp-infoflow/sync', [ItspInfoflowController::class, 'syncFromCobit'])->name('itsp-infoflow.sync');
        Route::post('/itsp-infoflow/inputs', [ItspInfoflowController::class, 'storeInput'])->name('itsp-infoflow.input.store');
        Route::put('/itsp-infoflow/inputs/{id}', [ItspInfoflowController::class, 'updateInput'])->name('itsp-infoflow.input.update');
        Route::delete('/itsp-infoflow/inputs/{id}', [ItspInfoflowController::class, 'destroyInput'])->name('itsp-infoflow.input.destroy');
        Route::post('/itsp-infoflow/outputs', [ItspInfoflowController::class, 'storeOutput'])->name('itsp-infoflow.output.store');
        Route::put('/itsp-infoflow/outputs/{id}', [ItspInfoflowController::class, 'updateOutput'])->name('itsp-infoflow.output.update');
        Route::delete('/itsp-infoflow/outputs/{id}', [ItspInfoflowController::class, 'destroyOutput'])->name('itsp-infoflow.output.destroy');

        // Master Responsible CRUD
        Route::get('/responsible', [ResponsibleController::class, 'manage'])->name('responsible.manage');
        Route::post('/responsible', [ResponsibleController::class, 'store'])->name('responsible.store');
        Route::put('/responsible/{id}', [ResponsibleController::class, 'update'])->name('responsible.update');
        Route::delete('/responsible/{id}', [ResponsibleController::class, 'destroy'])->name('responsible.destroy');
    });

    Route::get('/program-information', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Program Information',
    ]))->name('program-information.index');

    // Program Evaluation
    Route::redirect('/program-evalution', '/program-evalution/review-summary');
    Route::get('/program-evalution/review', [TrsReviewPCController::class, 'index'])->name('program-evaluation.index');
    Route::post('/program-evalution/review', [TrsReviewPCController::class, 'store'])->name('program-evaluation.store');
    Route::get('/program-evalution/review/{trsReviewPC}', [TrsReviewPCController::class, 'show'])->name('program-evaluation.show');
    Route::put('/program-evalution/review/{trsReviewPC}', [TrsReviewPCController::class, 'update'])->name('program-evaluation.update');
    Route::get('/program-evalution/review-timeline', [ReviewTimelineController::class, 'index'])->name('program-evaluation.review-timeline');
    Route::get('/program-evalution/review-dashboard', [ReviewDashboardController::class, 'index'])->name('program-evaluation.review-dashboard');
    Route::get('/program-evalution/review-summary', [ReviewDashboardController::class, 'summary'])->name('program-evaluation.review-summary');
    Route::get('/program-evalution/review-document', [ReviewDocumentController::class, 'index'])->name('program-evaluation.review-document');
    Route::get('/program-evalution/review-analysis', [ReviewAktorController::class, 'index'])->name('program-evaluation.review-aktor');
    Route::get('/program-evalution/report', fn () => Inertia::render('Placeholder/Index', [
        'title' => 'Program Evaluation Report',
    ]))->name('program-evaluation.report');
    Route::post('/program-evalution/review-timeline/review-status-implementation/{statusId}', [ReviewTimelineController::class, 'updateReviewStatusImplementation'])->name('program-evaluation.review-timeline.review-status.update');

    // Summary Review Notes (TrsReviewSc)
    Route::post('/program-evaluation/summary-review/notes', [TrsReviewScController::class, 'store'])->name('program-evaluation.summary-review.notes.store');
    Route::put('/program-evaluation/summary-review/notes/{trsReviewSc}', [TrsReviewScController::class, 'update'])->name('program-evaluation.summary-review.notes.update');
    Route::delete('/program-evaluation/summary-review/notes/{trsReviewSc}', [TrsReviewScController::class, 'destroy'])->name('program-evaluation.summary-review.notes.destroy');

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
    Route::get('/it-initiatives/value-creation', ValueCreationController::class.'@index')->name('it-initiatives.value-creation');
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

    Route::get('/libary', [LibaryController::class, 'index'])->name('libary.index');
    Route::post('/libary/upload', [LibaryController::class, 'upload'])->name('libary.upload');
    Route::get('/libary/document/{uuid}/preview', [LibaryController::class, 'previewFile'])->name('libary.document.preview');
    Route::get('/libary/document/{uuid}/download', [LibaryController::class, 'downloadFile'])->name('libary.document.download');
    Route::delete('/libary/document/{uuid}', [LibaryController::class, 'destroy'])->name('libary.document.destroy');
    Route::get('/libary/{uuid}', [LibaryController::class, 'show'])->name('libary.show');

    // BPMN Workflow Controller (Proof of Concept)
    Route::prefix('/bpmn-workflow')->name('bpmn-workflow.')->group(function () {
        Route::get('/', [BpmnWorkflowController::class, 'index'])->name('index');
        Route::post('/', [BpmnWorkflowController::class, 'store'])->name('store');
        Route::delete('/{bpmnWorkflow}', [BpmnWorkflowController::class, 'destroy'])->name('destroy');
        Route::post('/trigger-action', [BpmnWorkflowController::class, 'triggerAction'])->name('trigger-action');
        Route::post('/{id}/sync-from-sop', [BpmnWorkflowController::class, 'syncFromSop'])->name('sync-from-sop');
    });
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
