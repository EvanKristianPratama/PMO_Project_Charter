<template>
    <UserLayout title="Digital Initiatives">
        <div class="animate-fade-in">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Digital Initiatives</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Manage digital initiative entries</p>
                </div>
                <Link
                    href="/digital-initiatives/create"
                    class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900"
                >
                    + New Initiative
                </Link>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 dark:border-white/5 dark:bg-[#1a1a1a] sm:flex-row">
                <div class="relative flex-1">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by no, use case, or owner..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-10 pr-4 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:placeholder-slate-500"
                        @input="debouncedSearch"
                    />
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select
                    v-model="filters.type"
                    class="rounded-lg border-slate-300 bg-white text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-200"
                    @change="applyFilters"
                >
                    <option value="">All Types</option>
                    <option value="strategic">Strategic</option>
                    <option value="operational">Operational</option>
                    <option value="tactical">Tactical</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-white/5 dark:bg-white/5">
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">No</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">Type</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">Use Case</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">Project Owner</th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700 dark:text-slate-300">Urgency</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr
                                v-for="item in initiatives.data"
                                :key="item.id"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.02]"
                            >
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 dark:text-white">{{ item.no }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                        :class="typeBadgeClass(item.type)"
                                    >
                                        {{ item.type }}
                                    </span>
                                </td>
                                <td class="max-w-xs truncate px-4 py-3 text-slate-600 dark:text-slate-400">{{ item.useCase || '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-400">{{ item.projectOwner || '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                        :class="urgencyBadgeClass(item.urgency)"
                                    >
                                        {{ item.urgency || '-' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="`/digital-initiatives/${item.id}`"
                                            class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-indigo-400"
                                            title="View"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="`/digital-initiatives/${item.id}/edit`"
                                            class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-amber-600 dark:text-slate-500 dark:hover:bg-white/5 dark:hover:text-amber-400"
                                            title="Edit"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </Link>
                                        <button
                                            type="button"
                                            class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:text-slate-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                                            title="Delete"
                                            @click="confirmDelete(item)"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="initiatives.data.length === 0"
                class="mt-6 rounded-xl border border-slate-200 bg-white py-12 text-center dark:border-white/5 dark:bg-[#1a1a1a]"
            >
                <p class="text-slate-500 dark:text-slate-400">No digital initiatives found.</p>
                <Link href="/digital-initiatives/create" class="mt-2 inline-block font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                    Create your first initiative
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="initiatives.links && initiatives.links.length > 3" class="mt-6 flex justify-center">
                <div class="flex gap-1">
                    <Link
                        v-for="(link, k) in initiatives.links"
                        :key="k"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="rounded border border-slate-200 bg-white px-3 py-1 text-sm text-slate-700 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        :class="{'border-indigo-600 bg-indigo-600 text-white': link.active, 'pointer-events-none opacity-50': !link.url}"
                    />
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    initiatives: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
});

const typeBadgeClass = (type) => {
    const t = String(type || '').toLowerCase();
    if (t === 'strategic') return 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400';
    if (t === 'operational') return 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400';
    if (t === 'tactical') return 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

const urgencyBadgeClass = (urgency) => {
    const u = String(urgency || '').toLowerCase();
    if (u === 'high' || u === 'tinggi') return 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400';
    if (u === 'medium' || u === 'sedang') return 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
    if (u === 'low' || u === 'rendah') return 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400';
    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

const confirmDelete = (item) => {
    if (confirm(`Are you sure you want to delete initiative "${item.no}"?`)) {
        router.delete(`/digital-initiatives/${item.id}`);
    }
};

let timeout = null;
const debouncedSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 300);
};

const applyFilters = () => {
    router.get('/digital-initiatives', filters.value, { preserveState: true, replace: true });
};
</script>
