import { computed, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { routeHelper } from "@/Composables/useRouteHelper";
import { useModulState } from "@/Composables/useModulState";
import {
    FlagIcon,
    FolderIcon,
    ChartBarIcon,
    ShieldCheckIcon,
    Squares2X2Icon,
    BuildingOffice2Icon,
    CubeIcon,
    PresentationChartBarIcon,
    DocumentTextIcon,
    TableCellsIcon,
    ClipboardDocumentCheckIcon,
    CircleStackIcon,
    CloudArrowDownIcon,
    CogIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/outline";

export function useNavigation() {
    const page = usePage();
    const { activeModul, setActiveModul } = useModulState();

    // Auto-adjust activeModul filter based on current URL path
    watch(
        () => page.url,
        (url) => {
            const path = url || "";
            const isItomPath =
                path.includes("/policy") ||
                path.includes("/regulation") ||
                path.includes("/bpmn-workflow") ||
                path.includes("/operating-model") ||
                path.includes("/service-portofolio") ||
                path.includes("/raci-analysis") ||
                path.includes("/master-data") ||
                path.includes("/sync") ||
                path.includes("/itom");

            const isItspsPath =
                path.includes("/itsp") ||
                path.includes("/strategic-house") ||
                path.includes("/program-planning") ||
                path.includes("/program-evaluation") ||
                path.includes("/program-implementation");

            if (isItomPath && activeModul.value === "itsps") {
                setActiveModul("itom");
            } else if (isItspsPath && activeModul.value === "itom") {
                setActiveModul("itsps");
            }
        },
        { immediate: true }
    );
    const authUser = computed(() => page.props.auth?.user || {});
    const appRole = computed(() =>
        String(authUser.value?.app_role || "user").toLowerCase(),
    );
    const isAdmin = computed(() => appRole.value === "admin");
    const safeRoute = (...args) => {
        try {
            return routeHelper(...args);
        } catch (error) {
            console.warn(
                `[useNavigation] Failed to resolve route "${args[0]}".`,
                error,
            );
            return "#";
        }
    };

    const navItems = computed(() => {
        const strategicHouseChildren = [];

        const programPlanningChildren = [
            {
                label: "Business Strategy",
                href: "/itsp/program-planning/business-strategy",
                icon: DocumentTextIcon,
                // Only mark active when under program-planning routes,
                // not when viewing Business Strategy inside Strategic House.
                active: (url) =>
                    (url || "").includes("/program-planning") &&
                    (url || "").includes("business-strategy"),
            },
            {
                label: "Digital Initiative Definition",
                href: safeRoute(
                    "itsp.program-planning.program-definition.digital-initiatives",
                ),
                icon: DocumentTextIcon,
                active: (url) =>
                    (url || "").includes(
                        "/program-planning/program-definition/digital-initiatives",
                    ) || url === "/program-planning/program-definition",
            },
            {
                label: "IT Initiative Definition",
                href: safeRoute(
                    "itsp.program-planning.program-definition.it-initiatives",
                ),
                icon: DocumentTextIcon,
                active: (url) =>
                    (url || "").includes(
                        "/program-planning/program-definition/it-initiatives",
                    ),
            },
        ];

        const programEvaluationChildren = [
            {
                label: "Dashboard Summary",
                href: safeRoute("itsp.program-evaluation.review-summary"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || "").includes("/program-evalution/review-summary"),
            },
            {
                label: "Review PC",
                href: safeRoute("itsp.program-evaluation.index"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || "").includes("/program-evalution/review") &&
                    !(url || "").includes(
                        "/program-evalution/review-timeline",
                    ) &&
                    !(url || "").includes(
                        "/program-evalution/review-dashboard",
                    ) &&
                    !(url || "").includes(
                        "/program-evalution/review-summary",
                    ) &&
                    !(url || "").includes(
                        "/program-evalution/review-aktor",
                    ) &&
                    !(url || "").includes(
                        "/program-evalution/review-document",
                    ) &&
                    !(url || "").includes(
                        "/program-evalution/review-analysis",
                    ),
            },
            {
                label: "Review Approval",
                href: safeRoute("itsp.program-evaluation.review-dashboard"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || "").includes(
                        "/program-evalution/review-dashboard",
                    ),
            },
            {
                label: "Review Status",
                href: safeRoute("itsp.program-evaluation.review-timeline"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || "").includes(
                        "/program-evalution/review-timeline",
                    ),
            },
            {
                label: "Review Aktor",
                href: safeRoute("itsp.program-evaluation.review-aktor"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || "").includes("/program-evalution/review-aktor"),
            },
            {
                label: "Review Document",
                href: safeRoute("itsp.program-evaluation.review-document"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || "").includes(
                        "/program-evalution/review-document",
                    ),
            },
            {
                label: "Report",
                href: safeRoute("itsp.program-evaluation.report"),
                icon: DocumentTextIcon,
                active: (url) =>
                    (url || "").includes("/program-evalution/report"),
            },
        ];

        const programImplementationChildren = [
            {
                label: "Digital Initiatives",
                href: safeRoute("itsp.digital-initiatives.index"),
                icon: FolderIcon,
                active: (url) =>
                    (url || "").includes("/digital-initiatives") &&
                    !(url || "").includes("/program-planning"),
                children: [
                    {
                        label: "Status Implementation",
                        href: safeRoute("itsp.digital-initiatives.index", {
                            tableMode: "implementation",
                        }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            (url || "").includes("/digital-initiatives") &&
                            (url || "").includes("tableMode=implementation") &&
                            !(url || "").includes("/program-planning"),
                    },
                ],
            },
            {
                label: "IT Initiatives",
                href: safeRoute("itsp.it-initiatives.index"),
                icon: FolderIcon,
                active: (url) => {
                    const normalized = url || "";
                    if (normalized.includes("/program-planning") || normalized.includes("/strategic-house")) {
                        return false;
                    }
                    return (
                        normalized.includes("/it-initiatives") ||
                        normalized.includes("/roadmap")
                    );
                },
                children: [
                    {
                        label: "Dashboard Summary",
                        href: safeRoute("itsp.program-implementation.index"),
                        icon: ChartBarIcon,
                        active: (url) => url === "/program-implementation",
                    },
                    {
                        label: "Roadmap Project Charter",
                        href: safeRoute("itsp.roadmap.index"),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) => {
                            const normalized = url || "";
                            if (normalized.includes("/program-planning") || normalized.includes("/strategic-house")) {
                                return false;
                            }
                            return (
                                normalized.includes("/roadmap") &&
                                !normalized.includes(
                                    "/roadmap/status-implementation",
                                )
                            );
                        },
                    },
                    {
                        label: "Status Implementation",
                        href: safeRoute("itsp.it-initiatives.index", {
                            tableMode: "implementation",
                        }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            (url || "").includes("tableMode=implementation") &&
                            !(url || "").includes("/program-planning"),
                    },
                ],
            },
        ];

        const getTab = (url) =>
            new URLSearchParams((url || "").split("?")[1] || "").get("tab");

        const architectureChildren = [
            {
                label: "APQC",
                href: safeRoute("itom.business-process.apqc.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || "").includes("/business-process/apqc"),
            },
            {
                label: "Business Capability",
                href: safeRoute("itom.business-process.business-capability.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || "").includes(
                        "/business-process/business-capability",
                    ),
            },
            {
                label: "Business Process",
                href: safeRoute("itom.business-process.business-process-v2.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || "").includes("/business-process/business-process-v2"),
            },
            {
                label: "Function",
                href: safeRoute("itom.business-process.function.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || "").includes("/business-process/function"),
            },
            {
                label: "KPI",
                href: safeRoute("itom.business-process.kpi.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || "").includes("/business-process/kpi"),
            },
            {
                label: "Regulation Mapping Function",
                href: safeRoute("itom.business-process.regulation-mapping.index"),
                icon: DocumentTextIcon,
                active: (url) =>
                    (url || "").includes(
                        "/business-process/regulation-mapping",
                    ),
            },
        ];

        const organizationChildren = [
            {
                label: "Company",
                href: safeRoute("itom.business-process.organization-structure.company.index"),
                icon: BuildingOffice2Icon,
                active: (url) => (url || "").includes("/business-process/organization-structure/company"),
            },
            {
                label: "BoD",
                href: safeRoute("itom.business-process.organization-structure.bod.index"),
                icon: UserGroupIcon,
                active: (url) => (url || "").includes("/business-process/organization-structure/bod"),
            },
            {
                label: "Structural Organization",
                href: safeRoute("itom.business-process.organization-structure.structural.index"),
                icon: BuildingOffice2Icon,
                active: (url) => (url || "").includes("/business-process/organization-structure/structural"),
            },
            {
                label: "Functional Organization",
                href: safeRoute("itom.business-process.organization-structure.functional.index"),
                icon: BuildingOffice2Icon,
                active: (url) => (url || "").includes("/business-process/organization-structure/functional"),
            },

            {
                label: "SDM",
                href: safeRoute("itom.resource-management.index"),
                icon: UserGroupIcon,
                active: (url) => (url || "").includes("/resource-management"),
            },
        ];

        const items = [
            {
                label: "Strategic House",
                href: safeRoute("itsp.strategic-house.index"),
                icon: Squares2X2Icon,
                active: (url) =>
                    (url || "").includes("/strategic-house") ||
                    (url || "").includes("/strategic-pillars"),
                children: strategicHouseChildren,
            },
            {
                label: "Program Planning",
                href: safeRoute("itsp.program-planning"),
                icon: FlagIcon,
                active: (url) => (url || "").includes("/program-planning"),
                children: programPlanningChildren,
            },
            {
                label: "Program Evaluation",
                href: safeRoute("itsp.program-evaluation.review-summary"),
                icon: DocumentTextIcon,
                active: (url) => (url || "").includes("/program-evalution"),
                children: programEvaluationChildren,
            },
            {
                label: "Program Implementation",
                href: safeRoute("itsp.program-implementation.index"),
                icon: ChartBarIcon,
                active: (url) => {
                    const normalized = url || "";
                    if (
                        normalized.includes("/program-planning") ||
                        normalized.includes("/strategic-house")
                    ) {
                        return false;
                    }
                    return (
                        normalized.includes("/program-implementation") ||
                        normalized === "/dashboard" ||
                        normalized.includes("/digital-initiatives") ||
                        normalized.includes("/it-initiatives") ||
                        normalized.includes("/roadmap")
                    );
                },
                children: programImplementationChildren,
            },
            {
                label: "Business Process",
                href: safeRoute("itom.business-process.apqc.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || "").includes("/business-process") &&
                    !(url || "").includes(
                        "/business-process/organization-structure",
                    ),
                children: architectureChildren,
            },
            {
                label: "Organization",
                href: safeRoute("itom.business-process.organization-structure"),
                icon: BuildingOffice2Icon,
                active: (url) =>
                    (url || "").includes(
                        "/business-process/organization-structure",
                    ) || (url || "").includes("/resource-management"),
                children: organizationChildren,
            },
            {
                label: "Operating Model",
                href: safeRoute("itom.operating-model.model.index"),
                icon: Squares2X2Icon,
                active: (url) =>
                    (url || "").includes("/operating-model"),
                children: [
                    {
                        label: "Model Structure",
                        href: safeRoute("itom.operating-model.model.index"),
                        icon: Squares2X2Icon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/model",
                            ) ||
                            [
                                "/itom/operating-model",
                                "/itom/operating-model/",
                                "/operating-model",
                                "/operating-model/",
                            ].includes((url || "").split("?")[0]),
                    },
                    {
                        label: "Framework",
                        href: safeRoute("itom.operating-model.framework.index"),
                        icon: Squares2X2Icon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/framework",
                            ) ||
                            (url || "").includes(
                                "/operating-model/cobit-component",
                            ),
                    },
                    {
                        label: "Policy",
                        href: safeRoute("itom.operating-model.policy.index"),
                        icon: DocumentTextIcon,
                        active: (url) =>
                            (url || "").includes("/operating-model/policy"),
                    },
                    {
                        label: "IT Governance",
                        href: safeRoute("itom.operating-model.it-governance.index"),
                        icon: Squares2X2Icon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/it-governance",
                            ),
                    },
                    {
                        label: "IT Management",
                        href: safeRoute("itom.operating-model.it-management.index"),
                        icon: Squares2X2Icon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/it-management",
                            ),
                    },
                    {
                        label: "IT Function",
                        href: safeRoute("itom.operating-model.it-function.index"),
                        icon: Squares2X2Icon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/it-function",
                            ),
                    },
                    {
                        label: "TKO",
                        href: safeRoute("itom.operating-model.stk.index"),
                        icon: Squares2X2Icon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/stk",
                            ),
                    },
                    {
                        label: "RACI Analysis",
                        href: safeRoute("itom.operating-model.raci-analysis.index"),
                        icon: DocumentTextIcon,
                        active: (url) =>
                            (url || "").includes(
                                "/operating-model/raci-analysis",
                            ),
                        children: [],
                    },
                ],
            },
            {
                label: "Service Portofolio",
                href: safeRoute("itom.service-portofolio.index"),
                icon: CubeIcon,
                active: (url) => (url || "").includes("/service-portofolio"),
            },
            {
                label: "Regulation",
                href: safeRoute("itom.policy.regulation.index"),
                icon: DocumentTextIcon,
                active: (url) =>
                    (((url || "").includes("/policy") ||
                    (url || "").includes("/regulation") ||
                    (url || "").includes("/bpmn-workflow")) &&
                    !(url || "").includes("/business-process/regulation-mapping")) &&
                    !(url || "").includes("/operating-model/policy"),
                children: [
                    {
                        label: "Policy, Standard, Procedure",
                        href: safeRoute("itom.policy.regulation.index"),
                        icon: DocumentTextIcon,
                        active: (url) =>
                            (url || "").includes("/policy/regulation") &&
                            !(url || "").includes("/policy/regulation/procedure") &&
                            !(url || "").includes("/policy/regulation/guidance"),
                    },
                    {
                        label: "SK",
                        href: safeRoute("itom.policy.sk.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || "").includes("/policy/sk"),
                    },
                    {
                        label: "CMS",
                        href: safeRoute("itom.policy.CMS.index"),
                        icon: FolderIcon,
                        active: (url) =>
                            (url || "").includes("/policy/CMS") ||
                            (url || "").includes("/policy/regulation/procedure") ||
                            (url || "").includes("/policy/regulation/guidance") ||
                            (url || "").includes("/policy/regulation/general")
                    },
                    {
                        label: "Glossary",
                        href: safeRoute("itom.policy.definition.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || "").includes("/policy/definition"),
                    },
                    {
                        label: "COBIT 2019",
                        href: safeRoute("itom.policy.cobit-component.index"),
                        icon: DocumentTextIcon,
                        active: (url) =>
                            (url || "").includes("/policy/cobit-component"),
                    },
                    // {
                    //     label: "BPMN",
                    //     href: safeRoute("bpmn-workflow.index"),
                    //     icon: CogIcon,
                    //     active: (url) => (url || '').includes("/bpmn-workflow"),
                    // },
                ],
            },

            {
                label: "RACI Analysis",
                href: safeRoute("itom.raci.infoflow.index"),
                icon: DocumentTextIcon,
                active: (url) => (url || "").includes("/raci/") && !(url || "").includes("operating-model"),
                children: [
                    {
                        label: "COBIT 2019 Information Flow",
                        href: safeRoute("itom.raci.infoflow.index"),
                        icon: DocumentTextIcon,
                        active: (url) =>
                            (url || "").includes("/raci/infoflow"),
                    },
                    {
                        label: "Pedoman TKTI Information Flow",
                        href: safeRoute("itom.raci.itsp-infoflow.index"),
                        icon: DocumentTextIcon,
                        active: (url) =>
                            (url || "").includes("/raci/itsp-infoflow"),
                    },
                ],
            },
            {
                label: "Master Data",
                href: safeRoute("master-data.index"),
                icon: TableCellsIcon,
                active: (url) => (url || "").includes("/master-data"),
            },
            {
                label: "Sinkronisasi Data",
                href: safeRoute("sync.index"),
                icon: CloudArrowDownIcon,
                active: (url) => (url || "").includes("/sync"),
            },
        ];

        const itspsLabels = [
            "Strategic House",
            "Program Planning",
            "Program Evaluation",
            "Program Implementation",
        ];
        const itomLabels = [
            "Business Process",
            "Organization",
            "Operating Model",
            "Service Portofolio",
            "Regulation",
            "RACI Analysis",
            "Master Data",
            "Sinkronisasi Data",
        ];

        const isItspsActive = items.some(
            (item) =>
                itspsLabels.includes(item.label) &&
                typeof item.active === "function" &&
                item.active(page.url),
        );
        const isItomActive = items.some(
            (item) =>
                itomLabels.includes(item.label) &&
                typeof item.active === "function" &&
                item.active(page.url),
        );

        return items.filter((item) => {
            const isItspsModul = itspsLabels.includes(item.label);
            const isItomModul = itomLabels.includes(item.label);

            if (activeModul.value === "itsps") {
                return isItspsModul;
            }
            if (activeModul.value === "itom") {
                return isItomModul;
            }

            // activeModul is 'all'
            if (isItspsActive) {
                return isItspsModul;
            }
            if (isItomActive) {
                return isItomModul;
            }
            return true;
        });
    });

    return {
        navItems,
        isAdmin,
    };
}
