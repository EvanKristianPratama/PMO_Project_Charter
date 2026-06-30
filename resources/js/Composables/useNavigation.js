import { computed } from "vue";
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
    const { activeModul } = useModulState();
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

        const getTab = (url) => new URLSearchParams((url || '').split('?')[1] || '').get('tab');

        const architectureChildren = [
            {
                label: "APQC",
                href: safeRoute("business-process.proses-bisnis.index", { tab: 'apqc' }),
                icon: CubeIcon,
                active: (url) => getTab(url) === 'apqc' || ((url || '').startsWith("/business-process/proses-bisnis") && !getTab(url)),
            },
            {
                label: "Business Capability",
                href: safeRoute("business-process.business-capability.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || '').startsWith("/business-process/business-capability"),
            },
            {
                label: "Business Process",
                href: safeRoute("business-process.proses-bisnis.index", { tab: 'proses-bisnis-v2' }),
                icon: CubeIcon,
                active: (url) => getTab(url) === 'proses-bisnis-v2',
            },
            {
                label: "Function",
                href: safeRoute("business-process.proses-bisnis.index", { tab: 'function' }),
                icon: CubeIcon,
                active: (url) => getTab(url) === 'function',
            },
            {
                label: "KPI",
                href: safeRoute("business-process.proses-bisnis.index", { tab: 'kpi' }),
                icon: CubeIcon,
                active: (url) => getTab(url) === 'kpi',
            },
            {
                label: "Regulation Mapping Function",
                href: safeRoute("business-process.proses-bisnis.index", { tab: 'regulation-map' }),
                icon: DocumentTextIcon,
                active: (url) => getTab(url) === 'regulation-map',
            },
            // {
            //     label: "Information System",
            //     href: safeRoute("business-process.informatic-system"),
            //     icon: BuildingOffice2Icon,
            //     active: (url) =>
            //         (url || '').startsWith("/business-process/informatic-system"),
            // },
        ];

        const organizationChildren = [
            {
                label: "Company",
                href: safeRoute("business-process.organization-structure", { tab: 'company' }),
                icon: BuildingOffice2Icon,
                active: (url) => (url || '').includes("tab=company") || ((url || '').startsWith("/business-process/organization-structure") && !(url || '').includes("tab=")),
            },
            {
                label: "BoD",
                href: safeRoute("business-process.organization-structure", { tab: 'bod' }),
                icon: UserGroupIcon,
                active: (url) => (url || '').includes("tab=bod"),
            },
            {
                label: "Structural Organization",
                href: safeRoute("business-process.organization-structure", { tab: 'organization' }),
                icon: BuildingOffice2Icon,
                active: (url) => (url || '').includes("tab=organization"),
            },
            {
                label: "Functional Organization",
                href: safeRoute("business-process.organization-structure", { tab: 'functional' }),
                icon: BuildingOffice2Icon,
                active: (url) => (url || '').includes("tab=functional"),
            },
            {
                label: "SK",
                href: safeRoute("business-process.organization-structure", { tab: 'sk' }),
                icon: DocumentTextIcon,
                active: (url) => (url || '').includes("tab=sk"),
            },
            {
                label: "SDM",
                href: safeRoute("resource-management.index"),
                icon: UserGroupIcon,
                active: (url) => (url || '').startsWith("/resource-management"),
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
                label: "Business Process",
                href: safeRoute("business-process.proses-bisnis.index"),
                icon: CubeIcon,
                active: (url) =>
                    (url || '').startsWith("/business-process") && !(url || '').startsWith("/business-process/organization-structure"),
                children: architectureChildren,
            },
            {
                label: "Organization",
                href: safeRoute("business-process.organization-structure"),
                icon: BuildingOffice2Icon,
                active: (url) => (url || '').startsWith("/business-process/organization-structure") || (url || '').startsWith("/resource-management"),
                children: organizationChildren,
            },
            {
                label: "Operating Model",
                href: safeRoute("operating-model.it-governance.index"),
                icon: Squares2X2Icon,
                active: (url) => (url || '').startsWith("/operating-model"),
                children: [
                    {
                        label: "IT Governance",
                        href: safeRoute("operating-model.it-governance.index"),
                        icon: Squares2X2Icon,
                        active: (url) => (url || '').startsWith("/operating-model/it-governance"),
                    },
                    {
                        label: "IT Management",
                        href: safeRoute("operating-model.it-management.index"),
                        icon: Squares2X2Icon,
                        active: (url) => (url || '').startsWith("/operating-model/it-management"),
                    },
                ],
            },
            {
                label: "Service Portofolio",
                href: safeRoute("service-portofolio.index"),
                icon: CubeIcon,
                active: (url) => (url || '').startsWith("/service-portofolio"),
            },
            {
                label: "Regulation",
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
                        label: "CMS",
                        href: safeRoute("policy.CMS.index"),
                        icon: FolderIcon,
                        active: (url) => (url || '').startsWith("/policy/CMS"),
                    },
                    // {
                    //     label: "BPMN",
                    //     href: safeRoute("bpmn-workflow.index"),
                    //     icon: CogIcon,
                    //     active: (url) => (url || '').startsWith("/bpmn-workflow"),
                    // },

                ],
            },
            {
                label: "RACI Analysis",
                href: safeRoute("policy.raci.index"),
                icon: DocumentTextIcon,
                active: (url) => (url || '').startsWith("/raci"),
                children: [
                    {
                        label: "RACI",
                        href: safeRoute("policy.raci.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/raci/index"),
                    },
                    {
                        label: "COBIT 2019 Information Flow",
                        href: safeRoute("raci.infoflow.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/raci/infoflow"),
                    },
                    {
                        label: "Pedoman TKTI Information Flow",
                        href: safeRoute("raci.itsp-infoflow.index"),
                        icon: DocumentTextIcon,
                        active: (url) => (url || '').startsWith("/raci/itsp-infoflow"),
                    },
                ]
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

        const itspsLabels = ['Strategic House', 'Program Planning', 'Program Evaluation', 'Program Implementation'];
        const itomLabels = ['Business Process', 'Organization', 'Operating Model', 'Service Portofolio', 'Regulation', 'RACI Analysis', 'Master Data', 'Sinkronisasi Data'];

        const isItspsActive = items.some(item =>
            itspsLabels.includes(item.label) && typeof item.active === 'function' && item.active(page.url)
        );
        const isItomActive = items.some(item =>
            itomLabels.includes(item.label) && typeof item.active === 'function' && item.active(page.url)
        );

        return items.filter(item => {
            const isItspsModul = itspsLabels.includes(item.label);
            const isItomModul = itomLabels.includes(item.label);

            if (activeModul.value === 'itsps') {
                return isItspsModul;
            }
            if (activeModul.value === 'itom') {
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
