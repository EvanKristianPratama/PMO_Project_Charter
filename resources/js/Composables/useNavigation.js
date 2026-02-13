import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
    HomeIcon,
    FlagIcon,
    FolderIcon,
    ShieldCheckIcon,
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
                icon: HomeIcon,
                active: (url) => url.startsWith('/dashboard'),
            },
            {
                label: 'Strategic Pillars',
                href: '/strategic-pillars',
                icon: FlagIcon,
                active: (url) => url.startsWith('/strategic-pillars'),
            },
            {
                label: 'IT Initiatives',
                href: '/it-initiatives',
                icon: FolderIcon,
                active: (url) => url.startsWith('/it-initiatives'),
            },
            {
                label: 'Digital Initiatives',
                href: '/digital-initiatives',
                icon: FolderIcon,
                active: (url) => url.startsWith('/digital-initiatives'),
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
