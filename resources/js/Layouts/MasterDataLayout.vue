<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    title: {
        type: String,
        default: 'Master Data',
    },
});

const route = useRouteHelper();
const page = usePage();
const currentUrl = computed(() => page.url || '');

const crudNav = [
    { label: 'Master Data Center', href: route('master-data.index'), icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
    { label: 'Master Initiative', href: route('master-data.mst-initiatives.index'), icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Business Capability', href: route('itom.business-process.business-capability.index'), icon: 'M4 6h16M4 12h16M4 18h16' },
    { label: 'Scope Charter', href: route('master-data.scope-charter.index'), icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { label: 'Project Charter', href: route('master-data.project-charter.index'), icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { label: 'PIC Project', href: route('master-data.pic-projects.index'), icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { label: 'Log Aktivitas', href: route('master-data.activity-log.index'), icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
    { label: 'Sinkronisasi Data', href: route('sync.index'), icon: 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z M12 12v6m0 0l-3-3m3 3l3-3' },
];

const isActive = (href) => {
    return currentUrl.value === href || currentUrl.value.startsWith(href);
};
</script>

<template>
    <UserLayout :title="title">
        <div class="animate-fade-in-up space-y-4">
            <!-- Main grid -->
            <section class="grid grid-cols-1 gap-4 lg:grid-cols-[240px_minmax(0,1fr)]">
                <!-- Sidebar -->
                <aside class="space-y-4">
                    <div class="sticky top-20 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Navigasi CRUD</p>

                        <div class="mt-3 space-y-1">
                            <Link v-for="nav in crudNav" :key="nav.href" :href="nav.href"
                                class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-xs font-semibold transition-colors"
                                :class="isActive(nav.href) 
                                    ? 'bg-[#1C75BC]/10 text-[#1C75BC] dark:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                                    : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5'"
                            >
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="nav.icon" />
                                </svg>
                                <span>{{ nav.label }}</span>
                            </Link>
                        </div>
                    </div>
                </aside>

                <!-- Content -->
                <div class="min-w-0 flex-1 space-y-4">
                    <slot />
                </div>
            </section>
        </div>
    </UserLayout>
</template>
