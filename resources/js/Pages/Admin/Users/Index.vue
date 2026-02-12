<template>
    <AdminLayout title="User Management">
        <div class="space-y-6 animate-fade-in">
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-indigo-500/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-cyan-400/10 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-400">Admin Control</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">User Management</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola role, approval, dan akses user secara terpusat.</p>
                    </div>
                    <button
                        type="button"
                        @click="applyFilters"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-300 dark:hover:border-white/20 dark:hover:bg-white/5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644h4.992m0 0a2.25 2.25 0 104.5 0m-4.5 0a2.25 2.25 0 014.5 0m0 0h8.523m-16.5-10.3h8.523m0 0a2.25 2.25 0 104.5 0m-4.5 0a2.25 2.25 0 014.5 0" />
                        </svg>
                        Refresh Data
                    </button>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="card in statCards"
                    :key="card.key"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ card.label }}</p>
                            <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ card.value }}</p>
                        </div>
                        <span :class="card.iconWrapClass">
                            <svg v-if="card.key === 'total'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5V6A2.25 2.25 0 0015.75 3.75h-7.5A2.25 2.25 0 006 6v1.5m12 0h-1.5m1.5 0v8.25A2.25 2.25 0 0115.75 18h-7.5A2.25 2.25 0 016 15.75V7.5m12 0H6m3.75 3h4.5" />
                            </svg>
                            <svg v-else-if="card.key === 'active'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else-if="card.key === 'pending'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2.25m5.25-2.25a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.13a9.377 9.377 0 002.625.37 9.337 9.337 0 004.121-.95 4.125 4.125 0 00-7.533-2.49M15 19.13v-.01c0-1.11-.29-2.16-.79-3.06M15 19.13a12.3 12.3 0 01-6.38 1.87c-2.33 0-4.51-.64-6.37-1.77l-.01-.11a6.38 6.38 0 0111.97-3.06M12 6.38a3.38 3.38 0 11-6.75 0 3.38 3.38 0 016.75 0zm8.25 2.25a2.63 2.63 0 11-5.25 0 2.63 2.63 0 015.25 0z" />
                            </svg>
                        </span>
                    </div>
                </article>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="relative md:col-span-6">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0a7.5 7.5 0 10-10.6 0 7.5 7.5 0 0010.6 0z" />
                        </svg>
                        <input
                            v-model="localFilters.search"
                            type="text"
                            placeholder="Cari nama atau email..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200 dark:placeholder:text-slate-500"
                            @input="debouncedSearch"
                        />
                    </div>

                    <select
                        v-model="localFilters.status"
                        @change="applyFilters"
                        class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">Semua Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <select
                        v-model="localFilters.role"
                        @change="applyFilters"
                        class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">Semua Role</option>
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>

                    <button
                        type="button"
                        @click="resetFilters"
                        class="md:col-span-2 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.02 4.98A9 9 0 105.98 16.02M8.25 8.25h7.5v7.5" />
                        </svg>
                        Reset
                    </button>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-white/10">
                        <thead class="bg-slate-50/70 dark:bg-[#1e1e1e]">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">User</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Status</th>
                                <th class="hidden px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 lg:table-cell">Terdaftar</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            <tr v-for="user in users.data" :key="user.id" class="transition hover:bg-slate-50/70 dark:hover:bg-white/5">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <UserAvatar :user="user" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-900 dark:text-white">{{ user.name }}</p>
                                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-lg bg-indigo-50 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">{{ getUserRole(user) }}</span>
                                        <select
                                            :value="getUserRole(user)"
                                            @change="updateUser(user, { role: $event.target.value })"
                                            class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                                        >
                                            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                                        </select>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <StatusBadge :status="user.status" />
                                </td>

                                <td class="hidden px-5 py-4 text-slate-500 dark:text-slate-400 lg:table-cell">
                                    {{ formatDate(user.created_at) }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            v-if="user.status !== 'approved'"
                                            type="button"
                                            @click="updateUser(user, { status: 'approved' })"
                                            class="rounded-lg border border-emerald-200 bg-emerald-50 p-1.5 text-emerald-600 transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20"
                                            title="Approve"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </button>

                                        <button
                                            v-if="user.status !== 'rejected'"
                                            type="button"
                                            @click="updateUser(user, { status: 'rejected' })"
                                            class="rounded-lg border border-rose-200 bg-rose-50 p-1.5 text-rose-600 transition hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20"
                                            title="Reject"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click="deleteUser(user)"
                                            class="rounded-lg border border-slate-200 bg-white p-1.5 text-slate-500 transition hover:border-rose-300 hover:text-rose-600 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400 dark:hover:border-rose-400/40 dark:hover:text-rose-300"
                                            title="Delete"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.35 9m-4.78 0L9.26 9m9.97-3.21c.34.05.68.11 1.02.17m-1.02-.17L18.16 19.67a2.25 2.25 0 0 1-2.25 2.08H8.09a2.25 2.25 0 0 1-2.25-2.08L4.77 5.79m14.46 0a48.1 48.1 0 0 0-3.48-.4m-12 .57c.34-.06.68-.12 1.02-.17m0 0c1.16-.2 2.32-.33 3.48-.4m7.5 0V4.5c0-1.18-.91-2.16-2.09-2.2a52 52 0 0 0-3.32 0c-1.18.04-2.09 1.02-2.09 2.2v.92m7.5 0a48.7 48.7 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!users.data?.length">
                                <td colspan="5" class="px-5 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.13a9.38 9.38 0 002.63.37 9.34 9.34 0 004.12-.95 4.13 4.13 0 00-7.53-2.49M15 19.13v-.01c0-1.11-.29-2.16-.79-3.06M15 19.13a12.3 12.3 0 01-6.38 1.87c-2.33 0-4.51-.64-6.37-1.77l-.01-.11a6.38 6.38 0 0111.97-3.06M12 6.38a3.38 3.38 0 11-6.75 0 3.38 3.38 0 016.75 0zm8.25 2.25a2.63 2.63 0 11-5.25 0 2.63 2.63 0 015.25 0z" />
                                            </svg>
                                        </span>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Belum ada user ditemukan</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ubah filter pencarian atau tunggu user baru terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.links?.length > 3" class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan {{ users.from }}–{{ users.to }} dari {{ users.total }} user
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :preserve-scroll="true"
                            :class="[
                                'rounded-lg px-3 py-1.5 text-xs transition-colors',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : link.url
                                        ? 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5'
                                        : 'cursor-not-allowed text-slate-300 dark:text-slate-600'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import UserAvatar from '@/Components/UserAvatar.vue';

const props = defineProps({
    users: { type: Object, required: true },
    roles: { type: Array, required: true },
    stats: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const localFilters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || 'all',
    role: props.filters.role || 'all',
});

const statCards = computed(() => [
    {
        key: 'total',
        label: 'Total Users',
        value: props.stats.total,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-300',
    },
    {
        key: 'active',
        label: 'Approved',
        value: props.stats.active,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
    },
    {
        key: 'pending',
        label: 'Pending',
        value: props.stats.pending,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
    },
    {
        key: 'admin',
        label: 'Admin',
        value: props.stats.admin,
        iconWrapClass: 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
    },
]);

function getUserRole(user) {
    return user.roles?.[0]?.name || user.role || 'Viewer';
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

let searchTimer = null;

function debouncedSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 300);
}

function applyFilters() {
    router.get('/admin/users', {
        search: localFilters.search || undefined,
        status: localFilters.status !== 'all' ? localFilters.status : undefined,
        role: localFilters.role !== 'all' ? localFilters.role : undefined,
    }, { preserveState: true, replace: true });
}

function resetFilters() {
    localFilters.search = '';
    localFilters.status = 'all';
    localFilters.role = 'all';
    router.get('/admin/users', {}, { preserveState: true, replace: true });
}

function updateUser(user, data) {
    router.put(`/admin/users/${user.id}`, data, { preserveScroll: true });
}

function deleteUser(user) {
    if (confirm(`Hapus user ${user.name}?`)) {
        router.delete(`/admin/users/${user.id}`, { preserveScroll: true });
    }
}
</script>
