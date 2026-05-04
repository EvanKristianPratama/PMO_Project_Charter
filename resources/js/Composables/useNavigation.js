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
    DocumentTextIcon,
    TableCellsIcon,
    ClipboardDocumentCheckIcon,
    CircleStackIcon,
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
                href: safeRoute("business-strategy.index"),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith("/strategic-house") && url.includes("business-strategy"),
            },
            {
                label: "Digital Initiative Definition",
                href: safeRoute(
                    "program-planning.program-definition.digital-initiatives",
                ),
                icon: DocumentTextIcon,
                active: (url) =>
                    url.startsWith(
                        "/program-planning/program-definition/digital-initiatives",
                    ) || url === "/program-planning/program-definition",
            },
            {
                label: "IT Initiative Definition",
                href: safeRoute(
                    "program-planning.program-definition.it-initiatives",
                ),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-planning/program-definition/it-initiatives'),
            },
        ];

        const programEvaluationChildren = [
            {
                label: "Review PC",
                href: safeRoute("program-evaluation.index"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    url.startsWith("/program-evalution/review") &&
                    !url.startsWith("/program-evalution/review-timeline") &&
                    !url.startsWith("/program-evalution/review-dashboard"),
            },
            {
                label: "Review Timeline",
                href: safeRoute("program-evaluation.review-timeline"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    url.startsWith("/program-evalution/review-timeline"),
            },
            {
                label: "Review Dashboard",
                href: safeRoute("program-evaluation.review-dashboard"),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    url.startsWith("/program-evalution/review-dashboard"),
            },
        ];

        const programImplementationChildren = [
            {
                label: "Digital Initiatives",
                href: safeRoute("digital-initiatives.index"),
                icon: FolderIcon,
                active: (url) => url.startsWith("/digital-initiatives"),
                children: [
                    {
                        label: "Status Implementation",
                        href: safeRoute("digital-initiatives.index", {
                            tableMode: "implementation",
                        }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            url.startsWith("/digital-initiatives") &&
                            url.includes("tableMode=implementation"),
                    },
                ],
            },
            {
                label: "IT Initiatives",
                href: safeRoute("it-initiatives.index"),
                icon: FolderIcon,
                active: (url) =>
                    url.startsWith("/it-initiatives") ||
                    url.startsWith("/roadmap"),
                children: [
                    {
                        label: "Roadmap Project Charter",
                        href: safeRoute("roadmap.index"),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            url.startsWith("/roadmap") &&
                            !url.startsWith("/roadmap/status-implementation"),
                    },
                    {
                        label: "Status Implementation",
                        href: safeRoute("it-initiatives.index", {
                            tableMode: "implementation",
                        }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            url.includes("tableMode=implementation"),
                    },
                ],
            },

            {
                label: "Resource Management",
                href: safeRoute(
                    "program-implementation.resources-management.index",
                ),
                icon: CircleStackIcon,
                active: (url) =>
                    url.startsWith(
                        "/program-implementation/resources-management",
                    ),
            },
        ];

        const architectureChildren = [
            {
                label: "Business Capability",
                href: safeRoute("master-data.business-capabilities.index"),
                icon: CubeIcon,
                active: (url) =>
                    url.startsWith("/master-data/business-capabilities"),
            },
            {
                label: "Organization Structure",
                href: safeRoute("architecture.organization-structure"),
                icon: BuildingOffice2Icon,
                active: (url) =>
                    url.startsWith("/architecture/organization-structure"),
            },
            {
                label: "Informatic System",
                href: safeRoute("architecture.informatic-system"),
                icon: BuildingOffice2Icon,
                active: (url) =>
                    url.startsWith("/architecture/informatic-system"),
            },
        ];

        const items = [
            {
                label: "Strategic House",
                href: safeRoute("strategic-house.index"),
                icon: Squares2X2Icon,
                active: (url) =>
                    url.startsWith("/strategic-house") ||
                    url.startsWith("/strategic-pillars"),
                children: strategicHouseChildren,
            },
            {
                label: "Program Planning",
                href: safeRoute("program-planning"),
                icon: FlagIcon,
                active: (url) =>
                    url.startsWith("/program-planning"),
                children: programPlanningChildren,
            },
            {
                label: "Program Evaluation",
                href: safeRoute("program-evaluation.index"),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith("/program-evalution"),
                children: programEvaluationChildren,
            },
            {
                label: "Program Implementation",
                href: safeRoute("program-implementation.index"),
                icon: ChartBarIcon,
                active: (url) =>
                    url.startsWith("/program-implementation") ||
                    url === "/dashboard" ||
                    url.startsWith("/digital-initiatives") ||
                    url.startsWith("/it-initiatives") ||
                    url.startsWith("/roadmap"),
                children: programImplementationChildren,
            },
            {
                label: "Architecture",
                href: safeRoute("architecture.index"),
                icon: CubeIcon,
                active: (url) =>
                    url.startsWith("/architecture") ||
                    url.startsWith("/master-data/business-capabilities"),
                children: architectureChildren,
            },
            {
                label: "Service Portofolio",
                href: safeRoute("service-portofolio.index"),
                icon: CubeIcon,
                active: (url) => url.startsWith("/service-portofolio"),
            },
            {
                label: "Policy",
                href: safeRoute("policy.index"),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith("/policy"),
            },
            {
                label: "Master Data",
                href: safeRoute("master-data.index"),
                icon: TableCellsIcon,
                active: (url) => url.startsWith("/master-data"),
            },
        ];

        if (isAdmin.value) {
            items.push({
                label: "Admin",
                href: safeRoute("admin.dashboard"),
                icon: ShieldCheckIcon,
                active: (url) => url.startsWith("/admin"),
            });
        }

        return items;
    });

    return {
        navItems,
        isAdmin,
    };
}
