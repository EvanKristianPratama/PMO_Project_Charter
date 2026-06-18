import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { routeHelper } from "@/Composables/useRouteHelper";
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
} from "@heroicons/vue/24/outline";

export function useNavigation() {
    const page = usePage();
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
            return page.url || "#";
        }
    };

    const navItems = computed(() => {
        const strategicHouseChildren = [
    
        ];

        const programPlanningChildren = [
            {
                label: "Business Strategy",
                href: '/program-planning/business-strategy',
                icon: DocumentTextIcon,
                // Only mark active when under program-planning routes,
                // not when viewing Business Strategy inside Strategic House.
                active: (url) => (url || '').startsWith("/program-planning") && (url || '').includes("business-strategy"),
            },
            {
                label: "Digital Initiative Definition",
                href: safeRoute(
                    "program-planning.program-definition.digital-initiatives",
                ),
                icon: DocumentTextIcon,
                active: (url) =>
                    (url || '').startsWith(
                        "/program-planning/program-definition/digital-initiatives",
                    ) || url === "/program-planning/program-definition",
            },
            {
                label: "IT Initiative Definition",
                href: safeRoute(
                    "program-planning.program-definition.it-initiatives",
                ),
                icon: DocumentTextIcon,
                active: (url) => (url || '').startsWith('/program-planning/program-definition/it-initiatives'),
            },
        ];

        const programEvaluationChildren = [
            {
                label: "Dashboard Summary",
                href: safeRoute("program-evaluation.review-summary"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/review-summary"),
            },
            {
                label: "Review PC",
                href: safeRoute("program-evaluation.index"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/review") &&
                    !(url || '').startsWith("/program-evalution/review-timeline") &&
                    !(url || '').startsWith("/program-evalution/review-dashboard") &&
                    !(url || '').startsWith("/program-evalution/review-summary") &&
                    !(url || '').startsWith("/program-evalution/review-aktor") &&
                    !(url || '').startsWith("/program-evalution/review-document") &&
                    !(url || '').startsWith("/program-evalution/review-analysis"),
            },
            {
                label: "Review Approval",
                href: safeRoute("program-evaluation.review-dashboard"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/review-dashboard"),
            },
            {
                label: "Review Status",
                href: safeRoute("program-evaluation.review-timeline"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/review-timeline"),
            },
            {
                label: "Review Aktor",
                href: safeRoute("program-evaluation.review-aktor"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/review-aktor"),
            },
            {
                label: "Review Document",
                href: safeRoute("program-evaluation.review-document"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/review-document"),
            },
            {
                label: "Report",
                href: safeRoute("program-evaluation.report"),
                icon: DocumentTextIcon,
                active: (url) =>
                    (url || '').startsWith("/program-evalution/report"),
            },
        ];

        const programImplementationChildren = [
            {
                label: "Digital Initiatives",
                href: safeRoute("digital-initiatives.index"),
                icon: FolderIcon,
                active: (url) => (url || '').startsWith("/digital-initiatives"),
                children: [
                    {
                        label: "Status Implementation",
                        href: safeRoute("digital-initiatives.index", {
                            tableMode: "implementation",
                        }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            (url || '').startsWith("/digital-initiatives") &&
                            (url || '').includes("tableMode=implementation"),
                    },
                ],
            },
            {
                label: "IT Initiatives",
                href: safeRoute("it-initiatives.index"),
                icon: FolderIcon,
                active: (url) =>
                    (url || '').startsWith("/it-initiatives") ||
                    (url || '').startsWith("/roadmap"),
                children: [
                    {
                        label: "Dashboard Summary",
                        href: safeRoute("program-implementation.index"),
                        icon: ChartBarIcon,
                        active: (url) => url === "/program-implementation",
                    },
                    {
                        label: "Roadmap Project Charter",
                        href: safeRoute("roadmap.index"),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            (url || '').startsWith("/roadmap") &&
                            !(url || '').startsWith("/roadmap/status-implementation"),
                    },
                    {
                        label: "Status Implementation",
                        href: safeRoute("it-initiatives.index", {
                            tableMode: "implementation",
                        }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            (url || '').includes("tableMode=implementation"),
                    },
                ],
            },
        ];

        const architectureChildren = [
            {
                label: "Business Capability",
                href: safeRoute("master-data.business-capabilities.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || '').startsWith("/master-data/business-capabilities"),
            },
            {
                label: "Business Process",
                href: safeRoute("architecture.proses-bisnis.index"),
                icon: DocumentTextIcon,
                active: (url) => (url || '').startsWith("/architecture/proses-bisnis"),
            },
            {
                label: "Function",
                href: safeRoute("architecture.function.index"),
                icon: DocumentTextIcon,
                active: (url) => (url || '').startsWith("/architecture/function"),
            },
            {
                label: "Organization",
                href: safeRoute("architecture.organization-structure"),
                icon: BuildingOffice2Icon,
                active: (url) =>
                    (url || '').startsWith("/architecture/organization-structure"),
            },
            {
                label: "Information System",
                href: safeRoute("architecture.informatic-system"),
                icon: BuildingOffice2Icon,
                active: (url) =>
                    (url || '').startsWith("/architecture/informatic-system"),
            },
        ];

        const items = [
            {
                label: "Strategic House",
                href: safeRoute("strategic-house.index"),
                icon: Squares2X2Icon,
                active: (url) =>
                    (url || '').startsWith("/strategic-house") ||
                    (url || '').startsWith("/strategic-pillars"),
                children: strategicHouseChildren,
            },
            {
                label: "Program Planning",
                href: safeRoute("program-planning"),
                icon: FlagIcon,
                active: (url) =>
                    (url || '').startsWith("/program-planning"),
                children: programPlanningChildren,
            },
            {
                label: "Program Evaluation",
                href: safeRoute("program-evaluation.review-summary"),
                icon: DocumentTextIcon,
                active: (url) => (url || '').startsWith("/program-evalution"),
                children: programEvaluationChildren,
            },
            {
                label: "Program Implementation",
                href: safeRoute("program-implementation.index"),
                icon: ChartBarIcon,
                active: (url) =>
                    (url || '').startsWith("/program-implementation") ||
                    url === "/dashboard" ||
                    (url || '').startsWith("/digital-initiatives") ||
                    (url || '').startsWith("/it-initiatives") ||
                    (url || '').startsWith("/roadmap"),
                children: programImplementationChildren,
            },
            {
                label: "Architecture",
                href: safeRoute("architecture.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || '').startsWith("/architecture") ||
                    (url || '').startsWith("/master-data/business-capabilities"),
                children: architectureChildren,
            },
            {
                label: "Service Portofolio",
                href: safeRoute("service-portofolio.index"),
                icon: CubeIcon,
                active: (url) => (url || '').startsWith("/service-portofolio"),
            },
            {
                label: "IT Operating Model",
                href: safeRoute("policy.regulation.index"),
                icon: DocumentTextIcon,
                active: (url) => (url || '').startsWith("/policy") || (url || '').startsWith("/bpmn-workflow"),
                children: [
                    {
                        label: "Regulation",
                        href: safeRoute("policy.regulation.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/regulation"),
                    },
                    {
                        label: "Organization",
                        href: safeRoute("policy.organization.index"),
                        icon: BuildingOffice2Icon,
                        active: (url) => (url || '').startsWith("/policy/organization"),
                    },
                    {
                        label: "Bab I: Pendahuluan",
                        href: safeRoute("policy.guidance.introduction"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/guidance/introduction"),
                    },
                    {
                        label: "Bab II: Kebijakan",
                        href: safeRoute("policy.general.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/general") || (url || '').startsWith("/policy/specific"),
                    },
                    {
                        label: "Bab III: Tanggung Jawab",
                        href: safeRoute("policy.roles.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/roles"),
                    },
                    {
                        label: "Bab IV: Penutup",
                        href: safeRoute("policy.guidance.closing"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/guidance/closing"),
                    },
                    {
                        label: "Matriks RACI",
                        href: safeRoute("policy.raci.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/raci"),
                    },
                    {
                        label: "Information Flow",
                        href: safeRoute("policy.infoflow.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/infoflow"),
                    },
                    {
                        label: "ITSP Info Flow",
                        href: safeRoute("policy.itsp-infoflow.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/policy/itsp-infoflow"),
                    },
                    {
                        label: "BPMN",
                        href: safeRoute("bpmn-workflow.index"),
                        icon: CogIcon,
                        active: (url) => (url || '').startsWith("/bpmn-workflow"),
                    },

                ],
            },
            {
                label: "Libary",
                href: safeRoute("libary.index"),
                icon: FolderIcon,
                active: (url) => (url || '').startsWith("/libary"),
            },
            {
                label: "Master Data",
                href: safeRoute("master-data.index"),
                icon: TableCellsIcon,
                active: (url) => (url || '').startsWith("/master-data"),
            },
            {
                label: "Sinkronisasi Data",
                href: safeRoute("sync.index"),
                icon: CloudArrowDownIcon,
                active: (url) => (url || '').startsWith("/sync"),
            },
        ];

        if (isAdmin.value) {
            items.push({
                label: "Admin",
                href: safeRoute("admin.dashboard"),
                icon: ShieldCheckIcon,
                active: (url) => (url || '').startsWith("/admin"),
            });
        }

        return items;
    });

    return {
        navItems,
        isAdmin,
    };
}
