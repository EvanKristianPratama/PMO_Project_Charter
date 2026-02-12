<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowTopRightOnSquareIcon,
    ChevronRightIcon,
    ClockIcon,
    KeyIcon,
    ShieldCheckIcon,
    UserPlusIcon,
    UsersIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_users: 0,
            admin_users: 0,
            pending_users: 0,
            permission_roles: 0,
        }),
    },
    recentUsers: {
        type: Array,
        default: () => [],
    },
});

const cards = computed(() => [
    {
        label: 'Total Users',
        value: props.stats.total_users ?? 0,
        icon: UsersIcon,
        iconWrapClass: 'bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-200',
    },
    {
        label: 'Admin Accounts',
        value: props.stats.admin_users ?? 0,
        icon: ShieldCheckIcon,
        iconWrapClass: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
    },
    {
        label: 'Pending Approval',
        value: props.stats.pending_users ?? 0,
        icon: ClockIcon,
        iconWrapClass: 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
    },
    {
        label: 'Permission Roles',
        value: props.stats.permission_roles ?? 0,
        icon: KeyIcon,
        iconWrapClass: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
    },
]);

const quickActions = [
    {
        label: 'Kelola User',
        description: 'Atur app role dan internal role.',
        href: '/admin/users',
        icon: UsersIcon,
    },
    {
        label: 'User Pending',
        description: 'Lihat akun yang menunggu approval.',
        href: '/admin/users?status=pending',
        icon: ClockIcon,
    },
    {
        label: 'Akun Admin',
        description: 'Filter akun dengan app_role admin.',
        href: '/admin/users?app_role=admin',
        icon: ShieldCheckIcon,
    },
    {
        label: 'Role Management',
        description: 'Buat role dan atur permission.',
        href: '/admin/roles',
        icon: KeyIcon,
    },
    {
        label: 'Portal User',
        description: 'Masuk ke dashboard aplikasi utama.',
        href: '/dashboard',
        icon: ArrowTopRightOnSquareIcon,
    },
];

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value));
};

const statusClass = (status) => {
    switch (status) {
        case 'approved':
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300';
        case 'pending':
            return 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300';
        case 'rejected':
            return 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300';
        default:
            return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
    }
};

const getInitials = (name) => {
    if (!name) {
        return 'U';
    }

    return name
        .split(' ')
        .map((word) => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};
</script>

<template>
    <AdminLayout title="Admin Dashboard">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#111827]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-500 dark:text-indigo-300">Admin Workspace</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Dashboard</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ringkasan akses akun aplikasi PMO dalam satu panel.</p>
                    </div>
                    <Link
                        href="/admin/users"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        <UserPlusIcon class="h-4 w-4" />
                        Kelola Pengguna
                    </Link>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="card in cards"
                    :key="card.label"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827]"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">{{ card.label }}</p>
                            <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ card.value }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl" :class="card.iconWrapClass">
                            <component :is="card.icon" class="h-5 w-5" />
                        </span>
                    </div>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827] xl:col-span-2">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Quick Actions</h2>
                    <div class="mt-4 space-y-3">
                        <Link
                            v-for="action in quickActions"
                            :key="action.label"
                            :href="action.href"
                            class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 transition hover:border-indigo-200 hover:bg-white dark:border-white/10 dark:bg-[#0f172a] dark:hover:border-indigo-500/40 dark:hover:bg-white/5"
                        >
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                                    <component :is="action.icon" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ action.label }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ action.description }}</p>
                                </div>
                            </div>
                            <ChevronRightIcon class="h-4 w-4 text-slate-400 transition group-hover:text-indigo-500 dark:text-slate-500 dark:group-hover:text-indigo-300" />
                        </Link>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827] xl:col-span-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Recent Users</h2>
                        <Link href="/admin/users" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200">
                            Lihat semua
                        </Link>
                    </div>

                    <div v-if="recentUsers.length" class="mt-4 space-y-3">
                        <div
                            v-for="user in recentUsers"
                            :key="user.id"
                            class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-[#0f172a] sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-sm font-semibold text-white">
                                    {{ getInitials(user.name) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ user.name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ user.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="rounded-lg px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.08em]" :class="statusClass(user.status)">
                                    {{ user.status }}
                                </span>
                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-600 dark:bg-white/10 dark:text-slate-300">
                                    {{ user.app_role }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ formatDate(user.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="mt-4 rounded-xl border border-dashed border-slate-300 px-4 py-8 text-center dark:border-white/15">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Belum ada data user terbaru.</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Data akan tampil setelah user terdaftar.</p>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
