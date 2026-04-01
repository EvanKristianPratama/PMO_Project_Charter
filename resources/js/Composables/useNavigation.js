import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { routeHelper } from '@/Composables/useRouteHelper';
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
} from '@heroicons/vue/24/outline';

export function useNavigation() {
    const page = usePage();
    const authUser = computed(() => page.props.auth?.user || {});
    const appRole = computed(() => String(authUser.value?.app_role || 'user').toLowerCase());
    const isAdmin = computed(() => appRole.value === 'admin');

    const navItems = computed(() => {
        const programPlanningChildren = [
            {
                label: 'Strategic Pillars',
                href: routeHelper('strategic-pillars.index'),
                icon: FlagIcon,
                active: (url) => url.startsWith('/strategic-pillars'),
            },
            {
                label: 'Digital Initiative Definition',
                href: routeHelper('program-planning.program-definition.digital-initiatives'),
                icon: DocumentTextIcon,
                active: (url) =>
                    url.startsWith('/program-planning/program-definition/digital-initiatives')
                    || url === '/program-planning/program-definition',
            },
            {
                label: 'IT Initiative Definition',
                href: routeHelper('program-planning.program-definition.it-initiatives'),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-planning/program-definition/it-initiatives'),
            },
            {
                label: 'Initiative Relation',
                href: routeHelper('initiative-relations.index'),
                icon: TableCellsIcon,
                active: (url) => url.startsWith('/program-planning/initiative-relation'),
            },
        ];

        const programEvaluationChildren = [
            {
                label: 'Review PC',
                href: routeHelper('program-evaluation.index'),
                icon: ClipboardDocumentCheckIcon,
                active: (url) =>
                    url.startsWith('/program-evalution/review')
                    && !url.startsWith('/program-evalution/review-timeline'),
            },
            {
                label: 'Review Timeline',
                href: routeHelper('program-evaluation.review-timeline'),
                icon: ClipboardDocumentCheckIcon,
                active: (url) => url.startsWith('/program-evalution/review-timeline'),
            },
        ];

        const programImplementationChildren = [
            {
                label: 'Digital Initiatives',
                href: routeHelper('digital-initiatives.index'),
                icon: FolderIcon,
                active: (url) => url.startsWith('/digital-initiatives'),
                children: [
                    {
                        label: 'Status Implementation',
                        href: routeHelper('digital-initiatives.index', { tableMode: 'implementation' }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) =>
                            url.startsWith('/digital-initiatives')
                            && url.includes('tableMode=implementation'),
                    },
                ],
            },
            {
                label: 'IT Initiatives',
                href: routeHelper('it-initiatives.index'),
                icon: FolderIcon,
                active: (url) => url.startsWith('/it-initiatives') || url.startsWith('/roadmap'),
                children: [
                    {
                        label: 'Roadmap Project Charter',
                        href: routeHelper('it-initiatives.index', { tableMode: 'roadmap' }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) => url.startsWith('/roadmap') && !url.startsWith('/roadmap/status-implementation'),
                    },
                    {
                        label: 'Status Implementation',
                        href: routeHelper('it-initiatives.index', { tableMode: 'implementation' }),
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) => url.includes('tableMode=implementation'),
                    },
                ],
            },
            {
                label: 'IT Building Blocks',
                href: routeHelper('program-implementation.it-building-blocks.index'),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-implementation/it-building-blocks'),
            },
            {
                label: 'RKAP',
                href: routeHelper('program-implementation.budgeting'),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-implementation/budgeting'),
            },
        ];

        const architectureChildren = [
            {
                label: 'Business Capability',
                href: routeHelper('master-data.business-capabilities.index'),
                icon: CubeIcon,
                active: (url) => url.startsWith('/master-data/business-capabilities'),
            },
            {
                label: 'Organization Structure',
                href: routeHelper('architecture.organization-structure'),
                icon: BuildingOffice2Icon,
                active: (url) => url.startsWith('/architecture/organization-structure'),
            },
            {
                label: 'Informatic System',
                href: routeHelper('architecture.informatic-system'),
                icon: BuildingOffice2Icon,
                active: (url) => url.startsWith('/architecture/informatic-system'),
            },
        ];

        const items = [
            {
                label: 'Program Planning',
                href: routeHelper('dashboard-monitoring'),
                icon: Squares2X2Icon,
                active: (url) =>
                    url.startsWith('/dashboard-monitoring')
                    || url.startsWith('/program-planning')
                    || url.startsWith('/strategic-pillars'),
                children: programPlanningChildren,
            },
            {
                label: 'Program Evaluation',
                href: routeHelper('program-evaluation.index'),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-evalution'),
                children: programEvaluationChildren,
            },
            {
                label: 'Program Implementation',
                href: routeHelper('dashboard'),
                icon: ChartBarIcon,
                active: (url) =>
                    url.startsWith('/program-implementation')
                    || url === '/dashboard'
                    || url.startsWith('/digital-initiatives')
                    || url.startsWith('/it-initiatives')
                    || url.startsWith('/roadmap'),
                children: programImplementationChildren,
            },
            {
                label: 'Architecture',
                href: routeHelper('architecture.index'),
                icon: CubeIcon,
                active: (url) => url.startsWith('/architecture') || url.startsWith('/master-data/business-capabilities'),
                children: architectureChildren,
            },
            {
                label: 'Service Portofolio',
                href: routeHelper('service-portofolio.index'),
                icon: CubeIcon,
                active: (url) => url.startsWith('/service-portofolio'),
            },
            {
                label: 'Resources Management',
                href: routeHelper('resources-management.index'),
                icon: CubeIcon,
                active: (url) => url.startsWith('/resources-management'),
            },
            {
                label: 'Policy',
                href: routeHelper('policy.index'),
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/policy'),
            },
            {
                label: 'Master Data',
                href: routeHelper('master-data.index'),
                icon: TableCellsIcon,
                active: (url) => url.startsWith('/master-data'),
            },
        ];

        if (isAdmin.value) {
            items.push({
                label: 'Admin',
                href: routeHelper('admin.dashboard'),
                icon: ShieldCheckIcon,
                active: (url) => url.startsWith('/admin'),
            });
        }

        return items;
    });

    return {
        navItems,
        isAdmin,
    };
}
