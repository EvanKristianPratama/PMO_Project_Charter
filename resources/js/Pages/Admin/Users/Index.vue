<template>
    <AdminLayout>
        <template #title>User Management</template>

        <div class="animate-fade-in">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">User Management</h2>
                    <p class="text-slate-500 text-sm mt-0.5">Kelola user dan akses modul</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <StatCard label="Total" :value="stats.total" />
                <StatCard label="Aktif" :value="stats.active" color="emerald" />
                <StatCard label="Pending" :value="stats.pending" color="amber" />
                <StatCard label="Admin" :value="stats.admin" color="indigo" />
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            v-model="localFilters.search"
                            type="text"
                            placeholder="Cari nama atau email..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-shadow"
                            @input="debouncedSearch"
                        />
                    </div>
                </div>

                <select
                    v-model="localFilters.status"
                    @change="applyFilters"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                >
                    <option value="all">Semua Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>

                <select
                    v-model="localFilters.role"
                    @change="applyFilters"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                >
                    <option value="all">Semua Role</option>
                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                </select>

                <button
                    @click="resetFilters"
                    class="px-4 py-2.5 rounded-xl text-sm text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                >
                    Reset
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left py-3.5 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                                <th class="text-left py-3.5 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="text-left py-3.5 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="text-left py-3.5 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Terdaftar</th>
                                <th class="text-right py-3.5 px-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50/50 transition-colors">
                                <!-- User -->
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <UserAvatar :user="user" />
                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-900 truncate">{{ user.name }}</p>
                                            <p class="text-xs text-slate-400 truncate">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="py-3.5 px-5">
                                    <select
                                        :value="getUserRole(user)"
                                        @change="updateUser(user, { role: $event.target.value })"
                                        class="text-xs px-2 py-1 rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                                    </select>
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-5">
                                    <StatusBadge :status="user.status" />
                                </td>

                                <!-- Date -->
                                <td class="py-3.5 px-5 text-slate-500 hidden md:table-cell">
                                    {{ formatDate(user.created_at) }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            v-if="user.status !== 'approved'"
                                            @click="updateUser(user, { status: 'approved' })"
                                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors"
                                            title="Approve"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </button>
                                        <button
                                            v-if="user.status !== 'rejected'"
                                            @click="updateUser(user, { status: 'rejected' })"
                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                            title="Reject"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteUser(user)"
                                            class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-red-500 transition-colors"
                                            title="Delete"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty state -->
                            <tr v-if="!users.data?.length">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Tidak ada user ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links?.length > 3" class="flex items-center justify-between px-5 py-3 border-t border-slate-100">
                    <p class="text-xs text-slate-500">
                        Menampilkan {{ users.from }}–{{ users.to }} dari {{ users.total }}
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs transition-colors',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : link.url
                                        ? 'text-slate-600 hover:bg-slate-100'
                                        : 'text-slate-300 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                            :preserve-scroll="true"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import UserAvatar from '@/Components/UserAvatar.vue';

const props = defineProps({
    users:   { type: Object, required: true },
    roles:   { type: Array,  required: true },
    stats:   { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const localFilters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || 'all',
    role:   props.filters.role   || 'all',
});

/* ── Helpers ───────────────────────────────────── */

function getUserRole(user) {
    return user.roles?.[0]?.name || 'Viewer';
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric',
    });
}

/* ── Actions ───────────────────────────────────── */

let searchTimer = null;

function debouncedSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 300);
}

function applyFilters() {
    router.get('/admin/users', {
        search: localFilters.search || undefined,
        status: localFilters.status !== 'all' ? localFilters.status : undefined,
        role:   localFilters.role   !== 'all' ? localFilters.role   : undefined,
    }, { preserveState: true, replace: true });
}

function resetFilters() {
    localFilters.search = '';
    localFilters.status = 'all';
    localFilters.role   = 'all';
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
