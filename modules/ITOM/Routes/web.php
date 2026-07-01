<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use Modules\ITOM\Controllers\BusinessProcess\BusinessCapability\BusinessCapabilityController;
use Modules\ITOM\Controllers\BusinessProcess\OrganizationStructure\OrganizationController as BusinessProcessOrganizationStructureController;
use Modules\ITOM\Controllers\BusinessProcess\ProsesBisnis\ProsesBisnisController as BusinessProcessProsesBisnisController;
use Modules\ITOM\Controllers\ResourceManagement\ResourceManagementController as MainResourceManagementController;
use Modules\ITOM\Controllers\OperatingModel\OperatingModelController;
use Modules\ITOM\Controllers\Policy\CobitComponentController;
use Modules\ITOM\Controllers\OperatingModel\PracticeRoleController;
use Modules\ITOM\Controllers\Policy\InfoflowController;
use Modules\ITOM\Controllers\Policy\ItspInfoflowController;
use Modules\ITOM\Controllers\Policy\GeneralPolicyController;
use Modules\ITOM\Controllers\Policy\PolicyController;
use Modules\ITOM\Controllers\Policy\RoleController;
use Modules\ITOM\Controllers\Policy\RegulationController;
use Modules\ITOM\Controllers\Policy\ProcedureController;
use Modules\ITOM\Controllers\Policy\ResponsibleController;
use Modules\ITOM\Controllers\Policy\CMSController;
use Modules\ITOM\Controllers\BpmnWorkflowController;
use Modules\ITOM\Controllers\LibaryController;

Route::middleware(["approved"])->group(function () {
    Route::get(
        "/business-process",
        fn() => redirect()->route("itom.business-process.proses-bisnis.index"),
    )->name("business-process.index");
    
    Route::get("/business-process/organization-structure", [
        BusinessProcessOrganizationStructureController::class,
        "index",
    ])->name("business-process.organization-structure");
    
    Route::post("/business-process/organization-structure", [
        BusinessProcessOrganizationStructureController::class,
        "store",
    ])->name("business-process.organization-structure.store");
    
    Route::post("/business-process/organization-structure/company", [
        BusinessProcessOrganizationStructureController::class,
        "storeCompany",
    ])->name("business-process.organization-structure.company.store");
    
    Route::put("/business-process/organization-structure/company/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "updateCompany",
    ])->name("business-process.organization-structure.company.update");
    
    Route::delete("/business-process/organization-structure/company/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "destroyCompany",
    ])->name("business-process.organization-structure.company.destroy");
    
    Route::post("/business-process/organization-structure/group", [
        BusinessProcessOrganizationStructureController::class,
        "storeGroup",
    ])->name("business-process.organization-structure.group.store");
    
    Route::put("/business-process/organization-structure/group/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "updateGroup",
    ])->name("business-process.organization-structure.group.update");
    
    Route::delete("/business-process/organization-structure/group/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "destroyGroup",
    ])->name("business-process.organization-structure.group.destroy");
    
    Route::put("/business-process/organization-structure/{organization}", [
        BusinessProcessOrganizationStructureController::class,
        "update",
    ])->name("business-process.organization-structure.update");
    
    Route::delete("/business-process/organization-structure/{organization}", [
        BusinessProcessOrganizationStructureController::class,
        "destroy",
    ])->name("business-process.organization-structure.destroy");
    
    Route::post("/business-process/organization-structure/bod", [
        BusinessProcessOrganizationStructureController::class,
        "storeBod",
    ])->name("business-process.organization-structure.bod.store");
    
    Route::put("/business-process/organization-structure/bod/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "updateBod",
    ])->name("business-process.organization-structure.bod.update");
    
    Route::delete("/business-process/organization-structure/bod/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "destroyBod",
    ])->name("business-process.organization-structure.bod.destroy");
    
    Route::post("/business-process/organization-structure/sk", [
        BusinessProcessOrganizationStructureController::class,
        "storeSk",
    ])->name("business-process.organization-structure.sk.store");
    
    Route::put("/business-process/organization-structure/sk/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "updateSk",
    ])->name("business-process.organization-structure.sk.update");
    
    Route::delete("/business-process/organization-structure/sk/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "destroySk",
    ])->name("business-process.organization-structure.sk.destroy");
    
    Route::post("/business-process/organization-structure/functional", [
        BusinessProcessOrganizationStructureController::class,
        "storeFunctional",
    ])->name("business-process.organization-structure.functional.store");
    
    Route::put("/business-process/organization-structure/functional/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "updateFunctional",
    ])->name("business-process.organization-structure.functional.update");
    
    Route::delete("/business-process/organization-structure/functional/{id}", [
        BusinessProcessOrganizationStructureController::class,
        "destroyFunctional",
    ])->name("business-process.organization-structure.functional.destroy");
    
    Route::post("/business-process/organization-structure/functional/member", [
        BusinessProcessOrganizationStructureController::class,
        "storeFunctionalMember",
    ])->name("business-process.organization-structure.functional.member.store");
    
    Route::delete(
        "/business-process/organization-structure/functional/member",
        [BusinessProcessOrganizationStructureController::class, "destroyFunctionalMember"],
    )->name("business-process.organization-structure.functional.member.destroy");
    
    Route::post(
        "/business-process/organization-structure/functional/structure",
        [BusinessProcessOrganizationStructureController::class, "storeFunctionalStructure"],
    )->name("business-process.organization-structure.functional.structure.store");
    
    Route::delete(
        "/business-process/organization-structure/functional/structure",
        [BusinessProcessOrganizationStructureController::class, "destroyFunctionalStructure"],
    )->name("business-process.organization-structure.functional.structure.destroy");
    
    Route::get(
        "/business-process/informatic-system",
        fn() => Inertia::render('modules/ITOM/BusinessProcess/InformaticSystem/Index'),
    )->name("business-process.informatic-system");
    
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

    // Proses Bisnis (Business Process) CRUD under Architecture
    Route::get("/business-process/proses-bisnis", [
        BusinessProcessProsesBisnisController::class,
        "index",
    ])->name("business-process.proses-bisnis.index");
    
    Route::get("/business-process/proses-bisnis/manage", [
        BusinessProcessProsesBisnisController::class,
        "manage",
    ])->name("business-process.proses-bisnis.manage");
    
    Route::post("/business-process/proses-bisnis", [
        BusinessProcessProsesBisnisController::class,
        "store",
    ])->name("business-process.proses-bisnis.store");
    
    Route::put("/business-process/proses-bisnis/{id}", [
        BusinessProcessProsesBisnisController::class,
        "update",
    ])->name("business-process.proses-bisnis.update");
    
    Route::delete("/business-process/proses-bisnis/{id}", [
        BusinessProcessProsesBisnisController::class,
        "destroy",
    ])->name("business-process.proses-bisnis.destroy");
    
    // APQC CRUD under Architecture
    Route::post("/business-process/apqc", [
        BusinessProcessProsesBisnisController::class,
        "storeApqc",
    ])->name("business-process.apqc.store");
    
    Route::put("/business-process/apqc/{id}", [
        BusinessProcessProsesBisnisController::class,
        "updateApqc",
    ])->name("business-process.apqc.update");
    
    Route::delete("/business-process/apqc/{id}", [
        BusinessProcessProsesBisnisController::class,
        "destroyApqc",
    ])->name("business-process.apqc.destroy");

    // Proses Bisnis v2 CRUD under Architecture
    Route::post("/business-process/proses-bisnis-v2", [
        BusinessProcessProsesBisnisController::class,
        "storeProsesBisnisV2",
    ])->name("business-process.proses-bisnis-v2.store");
    
    Route::put("/business-process/proses-bisnis-v2/{id}", [
        BusinessProcessProsesBisnisController::class,
        "updateProsesBisnisV2",
    ])->name("business-process.proses-bisnis-v2.update");
    
    Route::delete("/business-process/proses-bisnis-v2/{id}", [
        BusinessProcessProsesBisnisController::class,
        "destroyProsesBisnisV2",
    ])->name("business-process.proses-bisnis-v2.destroy");

    // Function CRUD under Architecture
    Route::post("/business-process/function", [
        BusinessProcessProsesBisnisController::class,
        "storeFunction",
    ])->name("business-process.function.store");
    
    Route::put("/business-process/function/{id}", [
        BusinessProcessProsesBisnisController::class,
        "updateFunction",
    ])->name("business-process.function.update");
    
    Route::delete("/business-process/function/{id}", [
        BusinessProcessProsesBisnisController::class,
        "destroyFunction",
    ])->name("business-process.function.destroy");

    // KPI CRUD under Architecture
    Route::post("/business-process/kpi", [
        BusinessProcessProsesBisnisController::class,
        "storeKpi",
    ])->name("business-process.kpi.store");
    
    Route::put("/business-process/kpi/{id}", [
        BusinessProcessProsesBisnisController::class,
        "updateKpi",
    ])->name("business-process.kpi.update");
    
    Route::delete("/business-process/kpi/{id}", [
        BusinessProcessProsesBisnisController::class,
        "destroyKpi",
    ])->name("business-process.kpi.destroy");

    // Resource Management CRUD
    Route::prefix("/resource-management")
        ->name("resource-management.")
        ->group(function () {
            Route::get("/", [MainResourceManagementController::class, "index"])->name("index");
            Route::post("/", [MainResourceManagementController::class, "store"])->name("store");
            Route::put("/{resource}", [MainResourceManagementController::class, "update"])->name("update");
            Route::delete("/{resource}", [MainResourceManagementController::class, "destroy"])->name("destroy");
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
            Route::get("/", [OperatingModelController::class, "index"])->name("index");
            
            Route::get("/it-governance", [
                OperatingModelController::class,
                "itGovernance",
            ])->name("it-governance.index");
            
            Route::get("/it-management", [
                OperatingModelController::class,
                "itManagement",
            ])->name("it-management.index");
            
            Route::get("/framework", [
                OperatingModelController::class,
                "framework",
            ])->name("framework.index");
            
            Route::get("/cobit-component", [
                CobitComponentController::class,
                "index",
            ])->name("cobit-component.index");
            
            Route::get("/raci-analysis", [PracticeRoleController::class, "index"])->name("raci-analysis.index");
            Route::get("/raci-analysis/manage", [PracticeRoleController::class, "manage"])->name("raci-analysis.manage");
            Route::post("/raci-analysis", [PracticeRoleController::class, "update"])->name("raci-analysis.update");
            
            Route::post("/it-governance/steering", [
                OperatingModelController::class,
                "storeSteering",
            ])->name("it-governance.steering.store");
            
            Route::put("/it-governance/steering/{id}", [
                OperatingModelController::class,
                "updateSteering",
            ])->name("it-governance.steering.update");
            
            Route::delete("/it-governance/steering/{id}", [
                OperatingModelController::class,
                "destroySteering",
            ])->name("it-governance.steering.destroy");
        });

    Route::prefix("/raci")
        ->name("raci.")
        ->group(function () {
            Route::get("/", function () {
                return redirect()->route("itom.operating-model.raci-analysis.index");
            })->name("index");
            Route::get("/infoflow", [InfoflowController::class, "index"])->name("infoflow.index");

            Route::get("/itsp-infoflow", [ItspInfoflowController::class, "index"])->name("itsp-infoflow.index");
            Route::get("/itsp-infoflow/data", [ItspInfoflowController::class, "getData"])->name("itsp-infoflow.data");
            Route::post("/itsp-infoflow/save", [ItspInfoflowController::class, "saveData"])->name("itsp-infoflow.save");
            Route::get("/itsp-infoflow/manage", [ItspInfoflowController::class, "manage"])->name("itsp-infoflow.manage");
            Route::post("/itsp-infoflow/sync", [ItspInfoflowController::class, "syncFromCobit"])->name("itsp-infoflow.sync");
            Route::post("/itsp-infoflow/inputs", [ItspInfoflowController::class, "storeInput"])->name("itsp-infoflow.input.store");
            Route::put("/itsp-infoflow/inputs/{id}", [ItspInfoflowController::class, "updateInput"])->name("itsp-infoflow.input.update");
            Route::delete("/itsp-infoflow/inputs/{id}", [ItspInfoflowController::class, "destroyInput"])->name("itsp-infoflow.input.destroy");
            Route::post("/itsp-infoflow/outputs", [ItspInfoflowController::class, "storeOutput"])->name("itsp-infoflow.output.store");
            Route::put("/itsp-infoflow/outputs/{id}", [ItspInfoflowController::class, "updateOutput"])->name("itsp-infoflow.output.update");
            Route::delete("/itsp-infoflow/outputs/{id}", [ItspInfoflowController::class, "destroyOutput"])->name("itsp-infoflow.output.destroy");
        });

    // Policy CRUD (mst_general_policy, mst_objective & mst_practice)
    Route::prefix("/policy")
        ->name("policy.")
        ->group(function () {
            Route::get("/", function () {
                return redirect()->route("itom.policy.regulation.index");
            })->name("index");

            // Guidance Intro & Outro chapters (Bab I & Bab V)
            Route::get("/guidance/introduction", [GeneralPolicyController::class, "introduction"])->name("guidance.introduction");
            Route::get("/guidance/closing", [GeneralPolicyController::class, "closing"])->name("guidance.closing");

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

            // Regulasi (Regulation) CRUD
            Route::get("/regulation/{id}/preview", [RegulationController::class, "previewData"])->name("regulation.preview");
            Route::get("/regulation", [RegulationController::class, "index"])->name("regulation.index");
            Route::get("/sk", [RegulationController::class, "skIndex"])->name("sk.index");
            Route::post("/regulation", [RegulationController::class, "store"])->name("regulation.store");
            Route::put("/regulation/{id}", [RegulationController::class, "update"])->name("regulation.update");
            Route::delete("/regulation/{id}", [RegulationController::class, "destroy"])->name("regulation.destroy");

            // Prosedur (Procedure) placeholder
            Route::get("/procedure", [ProcedureController::class, "index"])->name("procedure.index");
            Route::get("/procedure/manage", [ProcedureController::class, "manage"])->name("procedure.manage");
            Route::post("/procedure/actor", [ProcedureController::class, "storeActor"])->name("procedure.actor.store");
            Route::put("/procedure/actor/{id}", [ProcedureController::class, "updateActor"])->name("procedure.actor.update");
            Route::delete("/procedure/actor/{id}", [ProcedureController::class, "destroyActor"])->name("procedure.actor.destroy");
            Route::post("/procedure/category", [ProcedureController::class, "storeCategory"])->name("procedure.category.store");
            Route::put("/procedure/category/{id}", [ProcedureController::class, "updateCategory"])->name("procedure.category.update");
            Route::delete("/procedure/category/{id}", [ProcedureController::class, "destroyCategory"])->name("procedure.category.destroy");
            Route::post("/procedure/sop", [ProcedureController::class, "storeSop"])->name("procedure.sop.store");
            Route::put("/procedure/sop/{id}", [ProcedureController::class, "updateSop"])->name("procedure.sop.update");
            Route::delete("/procedure/sop/{id}", [ProcedureController::class, "destroySop"])->name("procedure.sop.destroy");
            Route::post("/procedure/diagram", [ProcedureController::class, "storeDiagram"])->name("procedure.diagram.store");
            Route::put("/procedure/diagram/{id}", [ProcedureController::class, "updateDiagram"])->name("procedure.diagram.update");
            Route::delete("/procedure/diagram/{id}", [ProcedureController::class, "destroyDiagram"])->name("procedure.diagram.destroy");
            Route::post("/procedure/tko-content", [ProcedureController::class, "storeOrUpdateTkoContent"])->name("procedure.tko-content.store");
            Route::post("/procedure/tko-content/save-structured", [ProcedureController::class, "saveStructuredDocument"])->name("procedure.tko-content.save-structured");
            Route::post("/procedure/section", [ProcedureController::class, "storeSection"])->name("procedure.section.store");
            Route::put("/procedure/section/{id}", [ProcedureController::class, "updateSection"])->name("procedure.section.update");
            Route::delete("/procedure/section/{id}", [ProcedureController::class, "destroySection"])->name("procedure.section.destroy");

            Route::get("/organization", [OperatingModelController::class, "itGovernance"])->name("organization.index");
            Route::get("/it-management", [OperatingModelController::class, "itManagement"])->name("it-management.index");
            Route::post("/organization/steering", [OperatingModelController::class, "storeSteering"])->name("organization.steering.store");
            Route::put("/organization/steering/{id}", [OperatingModelController::class, "updateSteering"])->name("organization.steering.update");
            Route::delete("/organization/steering/{id}", [OperatingModelController::class, "destroySteering"])->name("organization.steering.destroy");

            // Matriks RACI (RACI Matrix) mapping
            Route::get("/raci", [PracticeRoleController::class, "index"])->name("raci.index");
            Route::get("/raci/manage", [PracticeRoleController::class, "manage"])->name("raci.manage");
            Route::post("/raci", [PracticeRoleController::class, "update"])->name("raci.update");

            // COBIT Component API Documentation
            Route::get("/cobit-component", [CobitComponentController::class, "index"])->name("cobit-component.index");

            // GAMO Information Flow
            Route::get("/infoflow", [InfoflowController::class, "index"])->name("infoflow.index");

            // ITSP Information Flow
            Route::get("/itsp-infoflow", [ItspInfoflowController::class, "index"])->name("itsp-infoflow.index");
            Route::get("/itsp-infoflow/data", [ItspInfoflowController::class, "getData"])->name("itsp-infoflow.data");
            Route::post("/itsp-infoflow/save", [ItspInfoflowController::class, "saveData"])->name("itsp-infoflow.save");
            Route::get("/itsp-infoflow/manage", [ItspInfoflowController::class, "manage"])->name("itsp-infoflow.manage");
            Route::post("/itsp-infoflow/sync", [ItspInfoflowController::class, "syncFromCobit"])->name("itsp-infoflow.sync");
            Route::post("/itsp-infoflow/inputs", [ItspInfoflowController::class, "storeInput"])->name("itsp-infoflow.input.store");
            Route::put("/itsp-infoflow/inputs/{id}", [ItspInfoflowController::class, "updateInput"])->name("itsp-infoflow.input.update");
            Route::delete("/itsp-infoflow/inputs/{id}", [ItspInfoflowController::class, "destroyInput"])->name("itsp-infoflow.input.destroy");
            Route::post("/itsp-infoflow/outputs", [ItspInfoflowController::class, "storeOutput"])->name("itsp-infoflow.output.store");
            Route::put("/itsp-infoflow/outputs/{id}", [ItspInfoflowController::class, "updateOutput"])->name("itsp-infoflow.output.update");
            Route::delete("/itsp-infoflow/outputs/{id}", [ItspInfoflowController::class, "destroyOutput"])->name("itsp-infoflow.output.destroy");

            // Master Responsible CRUD
            Route::get("/responsible", [ResponsibleController::class, "manage"])->name("responsible.manage");
            Route::post("/responsible", [ResponsibleController::class, "store"])->name("responsible.store");
            Route::put("/responsible/{id}", [ResponsibleController::class, "update"])->name("responsible.update");
            Route::delete("/responsible/{id}", [ResponsibleController::class, "destroy"])->name("responsible.destroy");

            // CMS Policy routes
            Route::get('/CMS', [CMSController::class, 'index'])->name('CMS.index');
            Route::post('/CMS/upload', [CMSController::class, 'upload'])->name('CMS.upload');
            Route::delete('/CMS/document/{uuid}', [CMSController::class, 'destroy'])->name('CMS.document.destroy');
            Route::get('/CMS/{uuid}', [CMSController::class, 'show'])->name('CMS.show');
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
