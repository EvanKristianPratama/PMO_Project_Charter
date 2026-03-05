import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
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
        const items = [
            {
                label: 'Program Planning',
                href: '/dashboard-monitoring',
                icon: Squares2X2Icon,
                active: (url) =>
                    url.startsWith('/dashboard-monitoring')
                    || url.startsWith('/program-planning')
                    || url.startsWith('/strategic-pillars'),
            },
            // {
            //     label: 'RSTI Sub Holding',
            //     href: '/program-planning/rsti-sub-holding',
            //     icon: BuildingOffice2Icon,
            //     active: (url) => url.startsWith('/program-planning/rsti-sub-holding'),
            // },
            {
                label: 'Strategic Pillars',
                href: '/strategic-pillars',
                icon: FlagIcon,
                active: (url) => url.startsWith('/strategic-pillars'),
            },
            {
                label: 'Digital Initiative Definition',
                href: '/program-planning/program-definition/digital-initiatives',
                icon: DocumentTextIcon,
                active: (url) =>
                    url.startsWith('/program-planning/program-definition/digital-initiatives')
                    || url === '/program-planning/program-definition',
            },
            {
                label: 'IT Initiative Definition',
                href: '/program-planning/program-definition/it-initiatives',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-planning/program-definition/it-initiatives'),
            },
            // {
            //     label: 'Initiative Relation',
            //     href: '/program-planning/initiative-relation',
            //     icon: TableCellsIcon,
            //     active: (url) => url.startsWith('/program-planning/initiative-relation'),
            // },
            {
                label: 'Program Evaluation',
                href: '/program-evalution/review',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-evalution'),
                children: [
                    {
                        label: 'Review PC',
                        href: '/program-evalution/review',
                        icon: ClipboardDocumentCheckIcon,
                        active: (url) => url.startsWith('/program-evalution/review'),
                    },
                ],
            },
            {
                label: 'Program Implementation',
                href: '/dashboard',
                icon: ChartBarIcon,
                active: (url) =>
                    url.startsWith('/program-implementation')
                    || url === '/dashboard'
                    || url.startsWith('/digital-initiatives')
                    || url.startsWith('/it-initiatives')
                    || url.startsWith('/roadmap'),
            },
            {
                label: 'Digital Initiatives',
                href: '/digital-initiatives',
                icon: FolderIcon,
                active: (url) => url.startsWith('/digital-initiatives'),
            },
            {
                label: 'IT Initiatives',
                href: '/it-initiatives',
                icon: FolderIcon,
                active: (url) => url.startsWith('/it-initiatives') || url.startsWith('/roadmap'),
            },
            // {
            //     label: 'Initiatives Relations',
            //     href: '/program-implementation/initiative-relation',
            //     icon: TableCellsIcon,
            //     active: (url) => url.startsWith('/program-implementation/initiative-relation'),
            // },
            {
                label: 'RKAP',
                href: '/program-implementation/budgeting',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-implementation/budgeting'),
            },
            {
                label: 'Architecture',
                href: '/architecture',
                icon: CubeIcon,
                active: (url) => url.startsWith('/architecture'),
            },
            {
                label: 'Resources Management',
                href: '/resources-management',
                icon: CubeIcon,
                active: (url) => url.startsWith('/resources-management'),
            },
            {
                label: 'Policy',
                href: '/policy',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/policy'),
            },
            {
                label: 'Master Data',
                href: '/master-data',
                icon: TableCellsIcon,
                active: (url) => url.startsWith('/master-data'),
            },

            {
                label: 'Company Profile',
                href: '/companies',
                icon: BuildingOffice2Icon,
                active: (url) => url.startsWith('/companies'),
            },
        ];

        if (isAdmin.value) {
            items.push({
                label: 'Admin',
                href: '/admin/dashboard',
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
