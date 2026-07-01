<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use Modules\ITSP\Controllers\StrategicHouse\StrategicPillars\GoalController as StrategicHouseStrategicPillarGoalController;
use Modules\ITSP\Controllers\StrategicHouse\StrategicPillars\IndexController as StrategicHouseStrategicPillarsIndexController;
use Modules\ITSP\Controllers\StrategicHouse\StrategicPillars\InitiativeTaggingController as StrategicHouseInitiativeTaggingController;
use Modules\ITSP\Controllers\StrategicHouse\StrategicPillars\ThemeController as StrategicHouseStrategicPillarThemeController;
use Modules\ITSP\Controllers\StrategicHouse\IndexController as StrategicHouseController;
use Modules\ITSP\Controllers\StrategicHouse\BusinessStrategy\IndexController as StrategicHouseBusinessStrategyController;
use Modules\ITSP\Controllers\StrategicHouse\BusinessStrategy\BusinessStrategyController as StrategicHouseBusinessStrategyManageController;
use Modules\ITSP\Controllers\StrategicHouse\InitiativeSupport\IndexController as StrategicHouseInitiativeSupportIndexController;
use Modules\ITSP\Controllers\StrategicHouse\RoadMap\IndexController as StrategicHouseRoadmapIndexController;
use Modules\ITSP\Controllers\StrategicHouse\RoadMap\SummaryController as StrategicHouseRoadmapSummaryController;
use Modules\ITSP\Controllers\StrategicHouse\MapTechnology\MapTechnologyController;
use Modules\ITSP\Controllers\ProgramPlanning\DashboardController as PlanningDashboardController;
use Modules\ITSP\Controllers\ProgramPlanning\BusinessStrategy\IndexController as ProgramPlanningBusinessStrategyIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramPlanningController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\IndexController as ProgramDefinitionController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\IndexController as ProgramDefinitionDigitalInitiativesController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\EditController as ProgramDefinitionDigitalInitiativesEditController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\UpdateController as ProgramDefinitionDigitalInitiativesUpdateController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Summary\IndexController as ProgramDefinitionDigitalInitiativesSummaryIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\IndexController as ProgramDefinitionDigitalInitiativesAppendixIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\CreateController as ProgramDefinitionDigitalInitiativesAppendixCreateController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\StoreController as ProgramDefinitionDigitalInitiativesAppendixStoreController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\EditController as ProgramDefinitionDigitalInitiativesAppendixEditController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Appendix\UpdateController as ProgramDefinitionDigitalInitiativesAppendixUpdateController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\IndexController as ProgramDefinitionDigitalInitiativesCompendiumIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\CreateController as ProgramDefinitionDigitalInitiativesCompendiumCreateController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\StoreController as ProgramDefinitionDigitalInitiativesCompendiumStoreController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\EditController as ProgramDefinitionDigitalInitiativesCompendiumEditController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Compendium\UpdateController as ProgramDefinitionDigitalInitiativesCompendiumUpdateController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Mapping\IndexController as ProgramDefinitionDigitalInitiativesMappingIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\IndexController as ProgramDefinitionDigitalInitiativesRoadmapIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\CreateController as ProgramDefinitionDigitalInitiativesRoadmapCreateController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\EditController as ProgramDefinitionDigitalInitiativesRoadmapEditController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\Roadmap\MilestoneController as ProgramDefinitionDigitalInitiativesRoadmapMilestoneController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\DigitalInitiatives\MasterDigitalInitiative\IndexController as ProgramDefinitionDigitalInitiativesMasterIndexController;
use Modules\ITSP\Controllers\ProgramPlanning\ProgramDefinition\ITInitiatives\IndexController as ProgramDefinitionITInitiativesController;
use Modules\ITSP\Controllers\StrategicHouse\InitiativeRelation\InitiativeRelationController as StrategicHouseInitiativeRelationController;
use Modules\ITSP\Controllers\StrategicHouse\ItBuildingBlock\IndexController as StrategicHouseItBuildingBlockController;
use Modules\ITSP\Controllers\ProgramImplementation\ProgramImplementationController;
use Modules\ITSP\Controllers\ProgramImplementation\ResourceManagementController;
use Modules\ITSP\Controllers\ProgramImplementation\Roadmap\RoadmapController;
use Modules\ITSP\Controllers\ProgramImplementation\ValueCreationController;
use Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\DigitalInitiatives\DigitalInitiativeController;
use Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\ITInitiativeController;
use Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\CharterController;
use Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\VersionAnalysisController;
use Modules\ITSP\Controllers\ProgramImplementation\ProjectCharter\ITInitiatives\MilestoneController;
use Modules\ITSP\Controllers\ProgramEvaluation\TrsReviewPCController;
use Modules\ITSP\Controllers\ProgramEvaluation\ReviewTimelineController;
use Modules\ITSP\Controllers\ProgramEvaluation\ReviewDashboardController;
use Modules\ITSP\Controllers\ProgramEvaluation\ReviewDocumentController;
use Modules\ITSP\Controllers\ProgramEvaluation\ReviewAktorController;
use Modules\ITSP\Controllers\ProgramEvaluation\TrsReviewScController;

Route::middleware(["approved"])->group(function () {
    Route::get("/program-planning", PlanningDashboardController::class)->name("program-planning");
    
    // Program Planning → Business Strategy (separate from Strategic House)
    Route::get(
        "/program-planning/business-strategy",
        ProgramPlanningBusinessStrategyIndexController::class,
    )->name("program-planning.business-strategy");
    
    Route::get("/program-planning/rsti-sub-holding", [
        ProgramPlanningController::class,
        "rstiSubHolding",
    ])->name("program-planning.rsti-sub-holding");
    
    Route::get(
        "/program-planning/program-definition",
        ProgramDefinitionController::class,
    )->name("program-planning.program-definition");
    
    Route::get(
        "/program-planning/program-definition/digital-initiatives",
        ProgramDefinitionDigitalInitiativesController::class,
    )->name("program-planning.program-definition.digital-initiatives");
    
    Route::get(
        "/program-planning/program-definition/digital-initiatives/{digitalInitiative}/edit",
        ProgramDefinitionDigitalInitiativesEditController::class,
    )
        ->whereNumber("digitalInitiative")
        ->name("program-planning.program-definition.digital-initiatives.edit");
        
    Route::put(
        "/program-planning/program-definition/digital-initiatives/{digitalInitiative}",
        ProgramDefinitionDigitalInitiativesUpdateController::class,
    )
        ->whereNumber("digitalInitiative")
        ->name("program-planning.program-definition.digital-initiatives.update");
        
    Route::get(
        "/program-planning/program-definition/digital-initiatives/{initiative}/summary",
        ProgramDefinitionDigitalInitiativesSummaryIndexController::class,
    )
        ->whereNumber("initiative")
        ->name("program-planning.program-definition.digital-initiatives.summary.index");

    Route::prefix("/program-planning/program-definition/digital-initiatives/appendix")
        ->name("program-planning.program-definition.digital-initiatives.appendix.")
        ->group(function () {
            Route::get("/", ProgramDefinitionDigitalInitiativesAppendixIndexController::class)->name("index");
            Route::get("/create", ProgramDefinitionDigitalInitiativesAppendixCreateController::class)->name("create");
            Route::post("/", ProgramDefinitionDigitalInitiativesAppendixStoreController::class)->name("store");
            Route::get("/{scInitiative}/edit", ProgramDefinitionDigitalInitiativesAppendixEditController::class)->name("edit");
            Route::put("/{scInitiative}", ProgramDefinitionDigitalInitiativesAppendixUpdateController::class)->name("update");
        });

    Route::prefix("/program-planning/program-definition/digital-initiatives/compendium")
        ->name("program-planning.program-definition.digital-initiatives.compendium.")
        ->group(function () {
            Route::get("/", ProgramDefinitionDigitalInitiativesCompendiumIndexController::class)->name("index");
            Route::get("/create", ProgramDefinitionDigitalInitiativesCompendiumCreateController::class)->name("create");
            Route::post("/", ProgramDefinitionDigitalInitiativesCompendiumStoreController::class)->name("store");
            Route::get("/{scInitiative}/edit", ProgramDefinitionDigitalInitiativesCompendiumEditController::class)->name("edit");
            Route::put("/{scInitiative}", ProgramDefinitionDigitalInitiativesCompendiumUpdateController::class)->name("update");
        });

    Route::get(
        "/program-planning/program-definition/digital-initiatives/mapping",
        ProgramDefinitionDigitalInitiativesMappingIndexController::class,
    )->name("program-planning.program-definition.digital-initiatives.mapping.index");

    Route::get(
        "/program-planning/program-definition/digital-initiatives/master",
        ProgramDefinitionDigitalInitiativesMasterIndexController::class,
    )->name("program-planning.program-definition.digital-initiatives.master.index");

    Route::prefix("/program-planning/program-definition/digital-initiatives/roadmap")
        ->name("program-planning.program-definition.digital-initiatives.roadmap.")
        ->group(function () {
            Route::get("/", ProgramDefinitionDigitalInitiativesRoadmapIndexController::class)->name("index");
            Route::get("/create", ProgramDefinitionDigitalInitiativesRoadmapCreateController::class)->name("create");
            Route::get("/edit", ProgramDefinitionDigitalInitiativesRoadmapEditController::class)->name("edit");
            Route::post("/", [ProgramDefinitionDigitalInitiativesRoadmapMilestoneController::class, "store"])->name("store");
            Route::put("/{masterMilestone}", [ProgramDefinitionDigitalInitiativesRoadmapMilestoneController::class, "update"])->name("update");
            Route::delete("/{masterMilestone}", [ProgramDefinitionDigitalInitiativesRoadmapMilestoneController::class, "destroy"])->name("destroy");
        });

    Route::get(
        "/program-planning/program-definition/it-initiatives",
        ProgramDefinitionITInitiativesController::class,
    )->name("program-planning.program-definition.it-initiatives");

    Route::prefix("/strategic-house/initiative-relation")
        ->name("initiative-relations.")
        ->group(function () {
            Route::get("/", [StrategicHouseInitiativeRelationController::class, "index"])->name("index");
            Route::get("/create", [StrategicHouseInitiativeRelationController::class, "create"])->name("create");
            Route::post("/", [StrategicHouseInitiativeRelationController::class, "store"])->name("store");
            Route::post("/sync-positions", [StrategicHouseInitiativeRelationController::class, "syncPositions"])->name("sync-positions");
            Route::get("/{initiativeRelation}/edit", [StrategicHouseInitiativeRelationController::class, "edit"])
                ->whereNumber("initiativeRelation")
                ->name("edit");
            Route::get("/{initiativeRelation}", [StrategicHouseInitiativeRelationController::class, "show"])
                ->whereNumber("initiativeRelation")
                ->name("show");
            Route::put("/{initiativeRelation}", [StrategicHouseInitiativeRelationController::class, "update"])
                ->whereNumber("initiativeRelation")
                ->name("update");
            Route::delete("/{initiativeRelation}", [StrategicHouseInitiativeRelationController::class, "destroy"])
                ->whereNumber("initiativeRelation")
                ->name("destroy");
        });

    // IT Building Blocks (moved to Program Planning)
    Route::get(
        "/program-planning/it-building-blocks",
        StrategicHouseItBuildingBlockController::class,
    )->name("program-planning.it-building-blocks.index");
    Route::post("/program-planning/it-building-blocks", [
        StrategicHouseItBuildingBlockController::class,
        "store",
    ])->name("program-planning.it-building-blocks.store");
    Route::delete("/program-planning/it-building-blocks/primary/{primary}", [
        StrategicHouseItBuildingBlockController::class,
        "destroyPrimary",
    ])->name("program-planning.it-building-blocks.primary.destroy");
    Route::delete(
        "/program-planning/it-building-blocks/primary/{primary}/secondary/{secondary}",
        [StrategicHouseItBuildingBlockController::class, "destroySecondary"],
    )->name("program-planning.it-building-blocks.secondary.destroy");
    Route::delete(
        "/program-planning/it-building-blocks/primary/{primary}/secondary/{secondary}/initiative/{initiative}",
        [StrategicHouseItBuildingBlockController::class, "destroyInitiative"],
    )->name("program-planning.it-building-blocks.initiative.destroy");
    Route::post(
        "/program-planning/it-building-blocks/initiatives/bulk-delete",
        [StrategicHouseItBuildingBlockController::class, "destroyInitiatives"],
    )->name("program-planning.it-building-blocks.initiative.bulk-destroy");

    Route::get(
        "/program-implementation/resources-management",
        ResourceManagementController::class,
    )->name("program-implementation.resources-management.index");

    Route::get(
        "/program-implementation",
        ProgramImplementationController::class,
    )->name("program-implementation.index");

    // Program Evaluation
    Route::get("/program-evalution/review", [
        TrsReviewPCController::class,
        "index",
    ])->name("program-evaluation.index");
    Route::post("/program-evalution/review", [
        TrsReviewPCController::class,
        "store",
    ])->name("program-evaluation.store");
    Route::get("/program-evalution/review/{trsReviewPC}", [
        TrsReviewPCController::class,
        "show",
    ])->name("program-evaluation.show");
    Route::put("/program-evalution/review/{trsReviewPC}", [
        TrsReviewPCController::class,
        "update",
    ])->name("program-evaluation.update");
    Route::get("/program-evalution/review-timeline", [
        ReviewTimelineController::class,
        "index",
    ])->name("program-evaluation.review-timeline");
    Route::get("/program-evalution/review-dashboard", [
        ReviewDashboardController::class,
        "index",
    ])->name("program-evaluation.review-dashboard");
    Route::get("/program-evalution/review-summary", [
        ReviewDashboardController::class,
        "summary",
    ])->name("program-evaluation.review-summary");
    Route::get("/program-evalution/review-document", [
        ReviewDocumentController::class,
        "index",
    ])->name("program-evaluation.review-document");
    Route::get("/program-evalution/review-analysis", [
        ReviewAktorController::class,
        "index",
    ])->name("program-evaluation.review-aktor");
    Route::get(
        "/program-evalution/report",
        fn() => Inertia::render("Placeholder/Index", [
            "title" => "Program Evaluation Report",
        ]),
    )->name("program-evaluation.report");
    Route::post(
        "/program-evalution/review-timeline/review-status-implementation/{statusId}",
        [ReviewTimelineController::class, "updateReviewStatusImplementation"],
    )->name("program-evaluation.review-timeline.review-status.update");

    // Summary Review Notes (TrsReviewSc)
    Route::post("/program-evaluation/summary-review/notes", [
        TrsReviewScController::class,
        "store",
    ])->name("program-evaluation.summary-review.notes.store");
    Route::put("/program-evaluation/summary-review/notes/{trsReviewSc}", [
        TrsReviewScController::class,
        "update",
    ])->name("program-evaluation.summary-review.notes.update");
    Route::delete("/program-evaluation/summary-review/notes/{trsReviewSc}", [
        TrsReviewScController::class,
        "destroy",
    ])->name("program-evaluation.summary-review.notes.destroy");

    // Strategic House
    Route::prefix("/strategic-house")
        ->name("strategic-house.")
        ->group(function () {
            Route::get("/", StrategicHouseController::class)->name("index");
            Route::get(
                "/business-strategy",
                StrategicHouseBusinessStrategyController::class,
            )->name("business-strategy.index");
            Route::post("/business-strategy", [
                StrategicHouseBusinessStrategyManageController::class,
                "store",
            ])->name("business-strategy.store");
            Route::put("/business-strategy/bulk-update", [
                StrategicHouseBusinessStrategyManageController::class,
                "bulkUpdate",
            ])->name("business-strategy.bulk-update");
            Route::put("/business-strategy/{businessStrategy}", [
                StrategicHouseBusinessStrategyManageController::class,
                "update",
            ])->name("business-strategy.update");
            Route::delete("/business-strategy/{businessStrategy}", [
                StrategicHouseBusinessStrategyManageController::class,
                "destroy",
            ])->name("business-strategy.destroy");
            Route::prefix("/initiative-support")
                ->name("initiative-support.")
                ->group(function () {
                    Route::get(
                        "/",
                        StrategicHouseInitiativeSupportIndexController::class,
                    )->name("index");
                    Route::post("/", [
                        StrategicHouseInitiativeSupportIndexController::class,
                        "store",
                    ])->name("store");
                    Route::post("/mappings/delete", [
                        StrategicHouseInitiativeSupportIndexController::class,
                        "destroyMappings",
                    ])->name("mappings.destroy");
                });
            Route::get(
                "/roadmap",
                StrategicHouseRoadmapIndexController::class,
            )->name("roadmap.index");
            Route::get(
                "/roadmap-summary",
                StrategicHouseRoadmapSummaryController::class,
            )->name("roadmap-summary.index");

            // Map Technology management
            Route::prefix("/map-technology")
                ->name("map-technology.")
                ->group(function () {
                    Route::post("/", [
                        MapTechnologyController::class,
                        "store",
                    ])->name("store");
                    Route::post("/bulk-destroy", [
                        MapTechnologyController::class,
                        "bulkDestroy",
                    ])->name("bulk-destroy");
                });
        });
        
    Route::get(
        "/strategic-house/strategic-pillars/{goal?}",
        StrategicHouseStrategicPillarsIndexController::class,
    )->name("strategic-pillars.index");
    Route::post("/strategic-house/strategic-pillars/goals", [
        StrategicHouseStrategicPillarGoalController::class,
        "store",
    ])->name("strategic-pillars.goals.store");
    Route::put("/strategic-house/strategic-pillars/goals/{goal}", [
        StrategicHouseStrategicPillarGoalController::class,
        "update",
    ])->name("strategic-pillars.goals.update");
    Route::delete("/strategic-house/strategic-pillars/goals/{goal}", [
        StrategicHouseStrategicPillarGoalController::class,
        "destroy",
    ])->name("strategic-pillars.goals.destroy");
    Route::post("/strategic-house/strategic-pillars/themes", [
        StrategicHouseStrategicPillarThemeController::class,
        "store",
    ])->name("strategic-pillars.themes.store");
    Route::put("/strategic-house/strategic-pillars/themes/{theme}", [
        StrategicHouseStrategicPillarThemeController::class,
        "update",
    ])->name("strategic-pillars.themes.update");
    Route::delete("/strategic-house/strategic-pillars/themes/{theme}", [
        StrategicHouseStrategicPillarThemeController::class,
        "destroy",
    ])->name("strategic-pillars.themes.destroy");
    Route::post("/strategic-house/strategic-pillars/tagging", [
        StrategicHouseInitiativeTaggingController::class,
        "store",
    ])->name("strategic-pillars.tagging.store");
    Route::delete("/strategic-house/strategic-pillars/tagging/{tagging}", [
        StrategicHouseInitiativeTaggingController::class,
        "destroy",
    ])->name("strategic-pillars.tagging.destroy");

    // Digital Initiatives
    Route::post("/digital-initiatives/implementation-status", [
        DigitalInitiativeController::class,
        "storeImplementationStatus",
    ])->name("digital-initiatives.implementation-status.store");
    Route::put("/digital-initiatives/implementation-status/{statusId}", [
        DigitalInitiativeController::class,
        "updateImplementationStatus",
    ])->name("digital-initiatives.implementation-status.update");
    Route::delete("/digital-initiatives/implementation-status/{statusId}", [
        DigitalInitiativeController::class,
        "destroyImplementationStatus",
    ])->name("digital-initiatives.implementation-status.destroy");
    Route::resource("digital-initiatives", DigitalInitiativeController::class);
    Route::put(
        "/digital-initiatives/{digital_initiative}/project-status-history/{history}",
        [DigitalInitiativeController::class, "updateProjectStatusHistory"],
    )->name("digital-initiatives.project-status-history.update");
    Route::delete(
        "/digital-initiatives/{digital_initiative}/project-status-history/{history}",
        [DigitalInitiativeController::class, "destroyProjectStatusHistory"],
    )->name("digital-initiatives.project-status-history.destroy");

    // IT Initiatives & Charters
    Route::get(
        "/it-initiatives/value-creation",
        [ValueCreationController::class, "index"],
    )->name("it-initiatives.value-creation");
    
    // Roadmap — dedicated controller (all programs & per-program views)
    Route::get("/roadmap", [RoadmapController::class, "index"])->name("roadmap.index");
    Route::get("/roadmap/add", [RoadmapController::class, "add"])->name("roadmap.add");
    Route::get("/roadmap/edit", [RoadmapController::class, "edit"])->name("roadmap.edit");
    Route::get("/roadmap/initiative/{initiative}", [RoadmapController::class, "show"])->name("roadmap.show");

    Route::resource("it-initiatives", ITInitiativeController::class)->parameters(["it-initiatives" => "project"]);
    Route::post("/it-initiatives/{project}/charter", [CharterController::class, "store"])->name("it-initiatives.charter.store");
    Route::put("/it-initiatives/{project}/charter/{charter}", [CharterController::class, "update"])->name("it-initiatives.charter.update");
    Route::post("/it-initiatives/{project}/version-analysis", [VersionAnalysisController::class, "store"])->name("it-initiatives.version-analysis.store");
    Route::put("/it-initiatives/{project}/version-analysis/{analysis}", [VersionAnalysisController::class, "update"])->name("it-initiatives.version-analysis.update");
    Route::post("/it-initiatives/{project}/milestones/versions", [MilestoneController::class, "createVersion"])->name("it-initiatives.milestones.versions.store");
    Route::post("/it-initiatives/{project}/milestones", [MilestoneController::class, "store"])->name("it-initiatives.milestones.store");
    Route::put("/it-initiatives/{project}/milestones/{milestone}", [MilestoneController::class, "update"])->name("it-initiatives.milestones.update");
    Route::delete("/it-initiatives/{project}/milestones/{milestone}", [MilestoneController::class, "destroy"])->name("it-initiatives.milestones.destroy");
    Route::post("/it-initiatives/{project}/implementation-status", [ITInitiativeController::class, "storeImplementationStatus"])->name("it-initiatives.implementation-status.store");

    Route::put("/implementation-status/{id}", [ITInitiativeController::class, "updateImplementationStatus"])->name("it-initiatives.implementation-status.update");
    Route::delete("/implementation-status/{id}", [ITInitiativeController::class, "destroyImplementationStatus"])->name("it-initiatives.implementation-status.destroy");
    Route::put("/it-initiatives/{project}/project-status-history/{history}", [ITInitiativeController::class, "updateProjectStatusHistory"])->name("it-initiatives.project-status-history.update");
    Route::delete(
        "/it-initiatives/{project}/project-status-history/{history}",
        [ITInitiativeController::class, "destroyProjectStatusHistory"],
    )->name("it-initiatives.project-status-history.destroy");
    Route::put("/it-initiatives/{project}/mapping", [ITInitiativeController::class, "updateMapping"])->name("it-initiatives.mapping.update");
});
