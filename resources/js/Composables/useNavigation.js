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
                href: '/dashboard',
                icon: Squares2X2Icon,
                active: (url) =>
                    url === '/dashboard'
                    || url.startsWith('/program-planning')
                    || url.startsWith('/dashboard-monitoring'),
            },
            {
                label: 'RSTI Sub Holding',
                href: '/program-planning/rsti-sub-holding',
                icon: BuildingOffice2Icon,
                active: (url) => url.startsWith('/program-planning/rsti-sub-holding'),
            },
            {
                label: 'Program Definition',
                href: '/program-planning/program-definition',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-planning/program-definition'),
            },
            {
                label: 'Dashboard Usulan',
                href: '/dashboard-monitoring',
                icon: ChartBarIcon,
                active: (url) => url.startsWith('/dashboard-monitoring'),
            },
            {
                label: 'Matrix Dependency',
                href: '/program-planning/matrix-dependency',
                icon: TableCellsIcon,
                active: (url) => url.startsWith('/program-planning/matrix-dependency'),
            },
            {
                label: 'Program Evaluation',
                href: '/program-information',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-information'),
            },
            {
                label: 'Program Implementation',
                href: '/program-implementation',
                icon: ChartBarIcon,
                active: (url) =>
                    url.startsWith('/program-implementation')
                    || url.startsWith('/strategic-pillars')
                    || url.startsWith('/digital-initiatives')
                    || url.startsWith('/it-initiatives'),
            },
            {
                label: 'Strategic Pillars',
                href: '/strategic-pillars',
                icon: FlagIcon,
                active: (url) => url.startsWith('/strategic-pillars'),
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
                active: (url) => url.startsWith('/it-initiatives'),
            },
            {
                label: 'RKAP',
                href: '/program-implementation/budgeting',
                icon: DocumentTextIcon,
                active: (url) => url.startsWith('/program-implementation/budgeting'),
            },
            {
                label: 'Matrix Dependency',
                href: '/program-implementation/matrix-dependency',
                icon: TableCellsIcon,
                active: (url) => url.startsWith('/program-implementation/matrix-dependency'),
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
