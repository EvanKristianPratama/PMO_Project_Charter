import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
    HomeIcon,
    FlagIcon,
    FolderIcon,
    ShieldCheckIcon,
    Squares2X2Icon,
    BuildingOffice2Icon,
    CubeIcon,
    DocumentTextIcon,
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
                active: (url) => url === '/dashboard',
            },
            {
                label: 'Dashboard Monitoring',
                href: '/dashboard-monitoring',
                icon: HomeIcon,
                active: (url) => url.startsWith('/dashboard-monitoring'),
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
                label: 'Asitecture',
                href: '/',
                icon: CubeIcon,
                active: (url) => url === '/',
            },
            {
                label: 'Policy',
                href: '/',
                icon: DocumentTextIcon,
                active: (url) => url === '/',
            },
            {
                label: 'Company Profile',
                href: '/companies',
                icon: BuildingOffice2Icon,
                active: (url) => url === '/companies',
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
