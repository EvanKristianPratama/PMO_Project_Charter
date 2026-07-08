<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use Modules\ITOM\Controllers\BusinessProcess\BusinessCapability\BusinessCapabilityController;
use Modules\ITOM\Controllers\Organization\Company\CompanyController;
use Modules\ITOM\Controllers\Organization\BOD\BodController;
use Modules\ITOM\Controllers\Organization\StructuralOrganization\StructuralOrganizationController;
use Modules\ITOM\Controllers\Organization\FunctionalOrganization\FunctionalOrganizationController;

use Modules\ITOM\Controllers\BusinessProcess\BusinessProcess\BusinessProcessController;
use Modules\ITOM\Controllers\BusinessProcess\APQC\ApqcController;
use Modules\ITOM\Controllers\BusinessProcess\Kpi\KpiController;
use Modules\ITOM\Controllers\BusinessProcess\Function\FunctionController;
use Modules\ITOM\Controllers\BusinessProcess\RegulationMapping\RegulationMappingController;
use Modules\ITOM\Controllers\Organization\SDM\SdmController as MainResourceManagementController;
use Modules\ITOM\Controllers\OperatingModel\ItGovarnence\ItGovarnenceController;
use Modules\ITOM\Controllers\OperatingModel\ItManagement\ItManagementController;
use Modules\ITOM\Controllers\OperatingModel\Model\ModelController;
use Modules\ITOM\Controllers\OperatingModel\Framework\FrameworkController;
use Modules\ITOM\Controllers\Regulation\COBIT\CobitComponentController;
use Modules\ITOM\Controllers\OperatingModel\RaciAnalysis\RaciAnalysisController;
use Modules\ITOM\Controllers\RaciAnalysis\CobitInformationFlow\CobitInformationFlowController;
use Modules\ITOM\Controllers\RaciAnalysis\TktiInformationFlow\TktiInformationFlowController;
use Modules\ITOM\Controllers\Regulation\GeneralPolicyController;
use Modules\ITOM\Controllers\Regulation\PolicyController;
use Modules\ITOM\Controllers\Regulation\RoleController;
use Modules\ITOM\Controllers\Regulation\PolicyStandartProcedureController;
use Modules\ITOM\Controllers\Regulation\ProcedureController;
use Modules\ITOM\Controllers\Regulation\ResponsibleController;
use Modules\ITOM\Controllers\Regulation\CMS\CMSController;
use Modules\ITOM\Controllers\Regulation\SK\SKController;
use Modules\ITOM\Controllers\BpmnWorkflowController;
use Modules\ITOM\Controllers\LibaryController;

Route::middleware(["approved"])->group(function () {
    Route::get(
        "/business-process",
        fn() => redirect()->route("itom.business-process.apqc.index"),
    )->name("business-process.index");
    
    Route::get("/business-process/organization-structure", fn() => redirect()->route("itom.business-process.organization-structure.company.index"))
        ->name("business-process.organization-structure");

    Route::prefix("business-process/organization-structure")
        ->name("business-process.organization-structure.")
        ->group(function () {
            // Company Sub-menu
            Route::prefix("company")
                ->name("company.")
                ->controller(CompanyController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "storeCompany")->name("store");
                    Route::put("/{id}", "updateCompany")->name("update");
                    Route::delete("/{id}", "destroyCompany")->name("destroy");
                });

            // BOD Sub-menu
            Route::prefix("bod")
                ->name("bod.")
                ->controller(BodController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "storeBod")->name("store");
                    Route::put("/{id}", "updateBod")->name("update");
                    Route::delete("/{id}", "destroyBod")->name("destroy");
                });

            // Structural Organization Sub-menu
            Route::prefix("structural")
                ->name("structural.")
                ->controller(StructuralOrganizationController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "store")->name("store");
                    Route::put("/{id}", "update")->name("update");
                    Route::delete("/{id}", "destroy")->name("destroy");
                });

            // Functional Organization Sub-menu
            Route::prefix("functional")
                ->name("functional.")
                ->controller(FunctionalOrganizationController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "storeFunctional")->name("store");
                    Route::put("/{id}", "updateFunctional")->name("update");
                    Route::delete("/{id}", "destroyFunctional")->name("destroy");

                    Route::post("/member", "storeFunctionalMember")->name("member.store");
                    Route::delete("/member", "destroyFunctionalMember")->name("member.destroy");

                    Route::post("/structure", "storeFunctionalStructure")->name("structure.store");
                    Route::delete("/structure", "destroyFunctionalStructure")->name("structure.destroy");
                });



            // Group CRUD Actions
            Route::prefix("group")
                ->name("group.")
                ->controller(CompanyController::class)
                ->group(function () {
                    Route::post("/", "storeGroup")->name("store");
                    Route::put("/{id}", "updateGroup")->name("update");
                    Route::delete("/{id}", "destroyGroup")->name("destroy");
                });
        });
    

    Route::get("/business-process/business-capability", [
        BusinessCapabilityController::class,
        "index",
    ])->name("business-process.business-capability.index");
    
    Route::post("/business-process/business-capability", [
        BusinessCapabilityController::class,
        "store",
    ])->name("business-process.business-capability.store");
    
    Route::put("/business-process/business-capability/{businessCapability}", [
        BusinessCapabilityController::class,
        "update",
    ])->name("business-process.business-capability.update");
    
    Route::delete(
        "/business-process/business-capability/{businessCapability}",
        [BusinessCapabilityController::class, "destroy"],
    )->name("business-process.business-capability.destroy");


    // Business Process sub-menus route grouping
    Route::prefix("business-process")
        ->name("business-process.")
        ->group(function () {
            
            // APQC
            Route::prefix("apqc")
                ->name("apqc.")
                ->controller(ApqcController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "store")->name("store");
                    Route::put("/{id}", "update")->name("update");
                    Route::delete("/{id}", "destroy")->name("destroy");
                });

            // Business Process (Proses Bisnis v2)
            Route::prefix("business-process-v2")
                ->name("business-process-v2.")
                ->controller(BusinessProcessController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "storeBusinessProcessV2")->name("store");
                    Route::put("/{id}", "updateBusinessProcessV2")->name("update");
                    Route::delete("/{id}", "destroyBusinessProcessV2")->name("destroy");
                });

            // Function
            Route::prefix("function")
                ->name("function.")
                ->controller(FunctionController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "storeFunction")->name("store");
                    Route::put("/{id}", "updateFunction")->name("update");
                    Route::delete("/{id}", "destroyFunction")->name("destroy");
                });

            // KPI
            Route::prefix("kpi")
                ->name("kpi.")
                ->controller(KpiController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                    Route::post("/", "storeKpi")->name("store");
                    Route::put("/{id}", "updateKpi")->name("update");
                    Route::delete("/{id}", "destroyKpi")->name("destroy");
                });

            // Regulation Mapping
            Route::prefix("regulation-mapping")
                ->name("regulation-mapping.")
                ->controller(RegulationMappingController::class)
                ->group(function () {
                    Route::get("/", "index")->name("index");
                });
        });

    
    // Resource Management CRUD
    Route::prefix("resource-management")
        ->name("resource-management.")
        ->controller(MainResourceManagementController::class)
        ->group(function () {
            Route::get("/", "index")->name("index");
            Route::post("/", "store")->name("store");
            Route::put("/{id}", "update")->name("update");
            Route::delete("/{id}", "destroy")->name("destroy");
        });

    Route::get(
        "/service-portofolio",
        fn() => Inertia::render("Placeholder/Index", [
            "title" => "Service Portofolio",
        ]),
    )->name("service-portofolio.index");

    Route::prefix("/operating-model")
        ->name("operating-model.")
        ->group(function () {
            Route::get("/", [ModelController::class, "index"])->name("index");
            
            Route::get("/it-governance", [
                ItGovarnenceController::class,
                "index",
            ])->name("it-governance.index");
            
            Route::get("/it-management", [
                ItManagementController::class,
                "index",
            ])->name("it-management.index");
            
            Route::get("/it-function", [
                \Modules\ITOM\Controllers\OperatingModel\ItFunction\ItFunctionController::class,
                "index",
            ])->name("it-function.index");
            
            Route::get("/stk", [
                \Modules\ITOM\Controllers\OperatingModel\STK\StkController::class,
                "index",
            ])->name("stk.index");
            
            Route::get("/model", [
                ModelController::class,
                "index",
            ])->name("model.index");

            Route::get("/framework", [
                FrameworkController::class,
                "index",
            ])->name("framework.index");
            
            Route::get("/cobit-component", [
                CobitComponentController::class,
                "index",
            ])->name("cobit-component.index");
            
            Route::get("/raci-analysis", [RaciAnalysisController::class, "index"])->name("raci-analysis.index");
            Route::get("/raci-analysis/manage", [RaciAnalysisController::class, "manage"])->name("raci-analysis.manage");
            Route::post("/raci-analysis", [RaciAnalysisController::class, "update"])->name("raci-analysis.update");
            
            Route::post("/it-governance/steering", [
                ItGovarnenceController::class,
                "storeSteering",
            ])->name("it-governance.steering.store");
            
            Route::put("/it-governance/steering/{id}", [
                ItGovarnenceController::class,
                "updateSteering",
            ])->name("it-governance.steering.update");
            
            Route::delete("/it-governance/steering/{id}", [
                ItGovarnenceController::class,
                "destroySteering",
            ])->name("it-governance.steering.destroy");
        });

    Route::prefix("/raci")
        ->name("raci.")
        ->group(function () {
            Route::get("/", function () {
                return redirect()->route("itom.operating-model.raci-analysis.index");
            })->name("index");
            Route::get("/infoflow", [CobitInformationFlowController::class, "index"])->name("infoflow.index");

            Route::get("/itsp-infoflow", [TktiInformationFlowController::class, "index"])->name("itsp-infoflow.index");
            Route::get("/itsp-infoflow/data", [TktiInformationFlowController::class, "getData"])->name("itsp-infoflow.data");
            Route::post("/itsp-infoflow/save", [TktiInformationFlowController::class, "saveData"])->name("itsp-infoflow.save");
            Route::get("/itsp-infoflow/manage", [TktiInformationFlowController::class, "manage"])->name("itsp-infoflow.manage");
            Route::post("/itsp-infoflow/sync", [TktiInformationFlowController::class, "syncFromCobit"])->name("itsp-infoflow.sync");
            Route::post("/itsp-infoflow/inputs", [TktiInformationFlowController::class, "storeInput"])->name("itsp-infoflow.input.store");
            Route::put("/itsp-infoflow/inputs/{id}", [TktiInformationFlowController::class, "updateInput"])->name("itsp-infoflow.input.update");
            Route::delete("/itsp-infoflow/inputs/{id}", [TktiInformationFlowController::class, "destroyInput"])->name("itsp-infoflow.input.destroy");
            Route::post("/itsp-infoflow/outputs", [TktiInformationFlowController::class, "storeOutput"])->name("itsp-infoflow.output.store");
            Route::put("/itsp-infoflow/outputs/{id}", [TktiInformationFlowController::class, "updateOutput"])->name("itsp-infoflow.output.update");
            Route::delete("/itsp-infoflow/outputs/{id}", [TktiInformationFlowController::class, "destroyOutput"])->name("itsp-infoflow.output.destroy");
        });

    // Policy CRUD (mst_general_policy, mst_objective & mst_practice)
    Route::prefix("/policy")
        ->name("policy.")
        ->group(function () {
            Route::get("/", function () {
                return redirect()->route("itom.policy.regulation.index");
            })->name("index");

            // Guidance (Kebijakan) CRUD under regulation/guidance prefix
            Route::prefix("/regulation/guidance")->group(function () {
                // Guidance Intro & Outro chapters (Bab I & Bab V)
                Route::get("/introduction", [GeneralPolicyController::class, "introduction"])->name("guidance.introduction");
                Route::get("/closing", [GeneralPolicyController::class, "closing"])->name("guidance.closing");

                // Kebijakan Umum (General Policy) CRUD
                Route::get("/general", [GeneralPolicyController::class, "index"])->name("general.index");
                Route::get("/general/manage", [GeneralPolicyController::class, "manage"])->name("general.manage");
                Route::post("/general", [GeneralPolicyController::class, "store"])->name("general.store");
                Route::put("/general/{id}", [GeneralPolicyController::class, "update"])->name("general.update");
                Route::delete("/general/{id}", [GeneralPolicyController::class, "destroy"])->name("general.destroy");

                // Kebijakan Khusus (Specific Policy) CRUD
                Route::get("/specific", function () {
                    return redirect()->route("itom.policy.general.index");
                })->name("specific.index");
                Route::get("/specific/manage", [PolicyController::class, "manage"])->name("specific.manage");
                Route::get("/specific/create", [PolicyController::class, "createObjective"])->name("specific.create");
                Route::get("/specific/{objective}/edit", [PolicyController::class, "editObjective"])->name("specific.edit");
                Route::get("/specific/mapping-all", [PolicyController::class, "mappingCobit"])->name("specific.mapping");
                Route::get("/specific/mapping-analysis", [PolicyController::class, "mappingCobitAnalysis"])->name("specific.mapping.analysis");
                Route::post("/objective", [PolicyController::class, "storeObjective"])->name("objective.store");
                Route::put("/objective/{objective}", [PolicyController::class, "updateObjective"])->name("objective.update");
                Route::delete("/objective/{objective}", [PolicyController::class, "destroyObjective"])->name("objective.destroy");
                Route::post("/practice", [PolicyController::class, "storePractice"])->name("practice.store");
                Route::put("/practice/{practice}", [PolicyController::class, "updatePractice"])->name("practice.update");
                Route::delete("/practice/{practice}", [PolicyController::class, "destroyPractice"])->name("practice.destroy");

                // Mapping COBIT 2019
                Route::post("/cobit-mapping", [PolicyController::class, "storeCobitMapping"])->name("cobit-mapping.store");
                Route::put("/cobit-mapping/{id}", [PolicyController::class, "updateCobitMapping"])->name("cobit-mapping.update");
                Route::delete("/cobit-mapping/{id}", [PolicyController::class, "destroyCobitMapping"])->name("cobit-mapping.destroy");

                // Roles & Responsibilities CRUD
                Route::get("/roles", [RoleController::class, "index"])->name("roles.index");
                Route::get("/roles/manage", [RoleController::class, "manage"])->name("roles.manage");
                Route::post("/roles/role", [RoleController::class, "storeRole"])->name("roles.role.store");
                Route::put("/roles/role/{role}", [RoleController::class, "updateRole"])->name("roles.role.update");
                Route::delete("/roles/role/{role}", [RoleController::class, "destroyRole"])->name("roles.role.destroy");
                Route::post("/roles/responsibility", [RoleController::class, "storeResponsibility"])->name("roles.responsibility.store");
                Route::put("/roles/responsibility/{responsibility}", [RoleController::class, "updateResponsibility"])->name("roles.responsibility.update");
                Route::delete("/roles/responsibility/{responsibility}", [RoleController::class, "destroyResponsibility"])->name("roles.responsibility.destroy");

                // Mapped Responsibles from Master Data
                Route::post("/roles/mapped-responsible", [RoleController::class, "storeMappedResponsible"])->name("roles.mapped-responsible.store");
                Route::delete("/roles/mapped-responsible/{roleId}/{responsibleId}", [RoleController::class, "destroyMappedResponsible"])->name("roles.mapped-responsible.destroy");
                Route::post("/roles/responsible-practice", [RoleController::class, "updateResponsiblePractice"])->name("roles.responsible-practice.update");
                Route::post("/roles/objective-responsible/{objectiveId}", [RoleController::class, "updateObjectiveResponsibles"])->name("roles.objective-responsible.update");
            });

            // Regulasi (Regulation) CRUD
            Route::get("/regulation/{id}/preview", [PolicyStandartProcedureController::class, "previewData"])->name("regulation.preview");
            Route::get("/regulation", [PolicyStandartProcedureController::class, "index"])->name("regulation.index");
            Route::get("/sk", [SKController::class, "index"])->name("sk.index");
            Route::post("/regulation", [PolicyStandartProcedureController::class, "store"])->name("regulation.store");
            Route::put("/regulation/{id}", [PolicyStandartProcedureController::class, "update"])->name("regulation.update");
            Route::delete("/regulation/{id}", [PolicyStandartProcedureController::class, "destroy"])->name("regulation.destroy");

            // Prosedur (Procedure) routes moved to regulation prefix group below

            Route::get("/organization", [ItGovarnenceController::class, "index"])->name("organization.index");
            Route::get("/it-management", [ItManagementController::class, "index"])->name("it-management.index");
            Route::post("/organization/steering", [ItGovarnenceController::class, "storeSteering"])->name("organization.steering.store");
            Route::put("/organization/steering/{id}", [ItGovarnenceController::class, "updateSteering"])->name("organization.steering.update");
            Route::delete("/organization/steering/{id}", [ItGovarnenceController::class, "destroySteering"])->name("organization.steering.destroy");

            // Matriks RACI (RACI Matrix) mapping
            Route::get("/raci", [RaciAnalysisController::class, "index"])->name("raci.index");
            Route::get("/raci/manage", [RaciAnalysisController::class, "manage"])->name("raci.manage");
            Route::post("/raci", [RaciAnalysisController::class, "update"])->name("raci.update");

            // COBIT Component API Documentation
            Route::get("/cobit-component", [CobitComponentController::class, "index"])->name("cobit-component.index");

            // GAMO Information Flow
            Route::get("/infoflow", [CobitInformationFlowController::class, "index"])->name("infoflow.index");

            // ITSP Information Flow
            Route::get("/itsp-infoflow", [TktiInformationFlowController::class, "index"])->name("itsp-infoflow.index");
            Route::get("/itsp-infoflow/data", [TktiInformationFlowController::class, "getData"])->name("itsp-infoflow.data");
            Route::post("/itsp-infoflow/save", [TktiInformationFlowController::class, "saveData"])->name("itsp-infoflow.save");
            Route::get("/itsp-infoflow/manage", [TktiInformationFlowController::class, "manage"])->name("itsp-infoflow.manage");
            Route::post("/itsp-infoflow/sync", [TktiInformationFlowController::class, "syncFromCobit"])->name("itsp-infoflow.sync");
            Route::post("/itsp-infoflow/inputs", [TktiInformationFlowController::class, "storeInput"])->name("itsp-infoflow.input.store");
            Route::put("/itsp-infoflow/inputs/{id}", [TktiInformationFlowController::class, "updateInput"])->name("itsp-infoflow.input.update");
            Route::delete("/itsp-infoflow/inputs/{id}", [TktiInformationFlowController::class, "destroyInput"])->name("itsp-infoflow.input.destroy");
            Route::post("/itsp-infoflow/outputs", [TktiInformationFlowController::class, "storeOutput"])->name("itsp-infoflow.output.store");
            Route::put("/itsp-infoflow/outputs/{id}", [TktiInformationFlowController::class, "updateOutput"])->name("itsp-infoflow.output.update");
            Route::delete("/itsp-infoflow/outputs/{id}", [TktiInformationFlowController::class, "destroyOutput"])->name("itsp-infoflow.output.destroy");

            // Master Responsible CRUD
            Route::get("/responsible", [ResponsibleController::class, "manage"])->name("responsible.manage");
            Route::post("/responsible", [ResponsibleController::class, "store"])->name("responsible.store");
            Route::put("/responsible/{id}", [ResponsibleController::class, "update"])->name("responsible.update");
            Route::delete("/responsible/{id}", [ResponsibleController::class, "destroy"])->name("responsible.destroy");

            // CMS Policy routes
            Route::prefix('CMS')
                ->name('CMS.')
                ->controller(CMSController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                });

            // Definition CRUD
            Route::prefix('definition')
                ->name('definition.')
                ->controller(\Modules\ITOM\Controllers\Regulation\Definition\DefinitionController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                });

            // Prosedur (Procedure) routes (as policy.regulation.procedure)
            Route::prefix("regulation/procedure")
                ->name("regulation.procedure.")
                ->group(function () {
                    Route::get("/", [ProcedureController::class, "index"])->name("index");
                    Route::get("/manage", [ProcedureController::class, "manage"])->name("manage");
                    Route::post("/actor", [ProcedureController::class, "storeActor"])->name("actor.store");
                    Route::put("/actor/{id}", [ProcedureController::class, "updateActor"])->name("actor.update");
                    Route::delete("/actor/{id}", [ProcedureController::class, "destroyActor"])->name("actor.destroy");
                    Route::post("/category", [ProcedureController::class, "storeCategory"])->name("category.store");
                    Route::put("/category/{id}", [ProcedureController::class, "updateCategory"])->name("category.update");
                    Route::delete("/category/{id}", [ProcedureController::class, "destroyCategory"])->name("category.destroy");
                    Route::post("/sop", [ProcedureController::class, "storeSop"])->name("sop.store");
                    Route::put("/sop/{id}", [ProcedureController::class, "updateSop"])->name("sop.update");
                    Route::delete("/sop/{id}", [ProcedureController::class, "destroySop"])->name("sop.destroy");
                    Route::post("/diagram", [ProcedureController::class, "storeDiagram"])->name("diagram.store");
                    Route::put("/diagram/{id}", [ProcedureController::class, "updateDiagram"])->name("diagram.update");
                    Route::delete("/diagram/{id}", [ProcedureController::class, "destroyDiagram"])->name("diagram.destroy");
                    Route::post("/tko-content", [ProcedureController::class, "storeOrUpdateTkoContent"])->name("tko-content.store");
                    Route::post("/tko-content/save-structured", [ProcedureController::class, "saveStructuredDocument"])->name("tko-content.save-structured");
                    Route::post("/section", [ProcedureController::class, "storeSection"])->name("section.store");
                    Route::put("/section/{id}", [ProcedureController::class, "updateSection"])->name("section.update");
                    Route::delete("/section/{id}", [ProcedureController::class, "destroySection"])->name("section.destroy");
                    Route::post("/glossary/map", [ProcedureController::class, "mapGlossary"])->name("glossary.map");
                    Route::post("/glossary/unmap", [ProcedureController::class, "unmapGlossary"])->name("glossary.unmap");
                });
        });

    Route::get("/libary", [LibaryController::class, "index"])->name("libary.index");
    Route::post("/libary/upload", [LibaryController::class, "upload"])->name("libary.upload");
    Route::get("/libary/document/{uuid}/preview", [LibaryController::class, "previewFile"])->name("libary.document.preview");
    Route::get("/libary/document/{uuid}/download", [LibaryController::class, "downloadFile"])->name("libary.document.download");
    Route::delete("/libary/document/{uuid}", [LibaryController::class, "destroy"])->name("libary.document.destroy");
    Route::get("/libary/{uuid}", [LibaryController::class, "show"])->name("libary.show");

    // BPMN Workflow Controller (Proof of Concept)
    Route::prefix("/bpmn-workflow")
        ->name("bpmn-workflow.")
        ->group(function () {
            Route::get("/", [BpmnWorkflowController::class, "index"])->name("index");
            Route::post("/", [BpmnWorkflowController::class, "store"])->name("store");
            Route::delete("/{bpmnWorkflow}", [BpmnWorkflowController::class, "destroy"])->name("destroy");
            Route::post("/trigger-action", [BpmnWorkflowController::class, "triggerAction"])->name("trigger-action");
            Route::post("/{id}/sync-from-sop", [BpmnWorkflowController::class, "syncFromSop"])->name("sync-from-sop");
        });
});
