<template>
    <UserLayout title="Program Definition Digital Initiatives — Appendix List">
        <div class="animate-fade-in space-y-4">
            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717] w-fit">
                <Link
                    href="/program-planning/program-definition/digital-initiatives"
                    class="group flex h-8 items-center gap-2 rounded-lg px-3 text-xs font-bold text-slate-500 transition-all hover:bg-slate-50 hover:text-[#0f63b5] dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-blue-400"
                >
                    <svg class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>

                <div class="h-4 w-px bg-slate-200 dark:bg-white/10" />

                <Link
                    href="/program-planning/program-definition/digital-initiatives"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Digital Initiatives
                </Link>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/compendium"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Compendium
                </Link>
                <div
                    class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400"
                >
                    Appendix
                </div>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Appendix List</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                        >
                            Total: {{ filteredAppendixItems.length }}
                        </span>
                        <Link
                            href="/program-planning/program-definition/digital-initiatives/appendix/create"
                            class="inline-flex items-center rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f]"
                        >
                            New Appendix
                        </Link>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 border-b border-slate-100 bg-slate-50/30 px-4 py-2.5 dark:border-white/5 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Owner:</label>
                        <select
                            v-model="filters.owner"
                            class="min-w-[140px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Project Owners</option>
                            <option v-for="owner in uniqueOwners" :key="owner" :value="owner">{{ owner }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">CoE:</label>
                        <select
                            v-model="filters.coe"
                            class="min-w-[120px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All CoE</option>
                            <option v-for="coe in uniqueCoes" :key="coe" :value="coe">{{ coe }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Value:</label>
                        <select
                            v-model="filters.value"
                            class="min-w-[90px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Value</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                            <option value="TBC">TBC</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Urgency:</label>
                        <select
                            v-model="filters.urgency"
                            class="min-w-[90px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Urgency</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                            <option value="TBC">TBC</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Compendium:</label>
                        <select
                            v-model="filters.compendium"
                            class="min-w-[140px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-8 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Compendium</option>
                            <option value="none">(No Compendium)</option>
                            <option v-for="compendium in uniqueCompendiums" :key="compendium" :value="compendium">{{ compendium }}</option>
                        </select>
                    </div>

                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="resetFilters"
                        class="ml-auto text-[10px] font-bold uppercase tracking-tighter text-rose-500 hover:text-rose-600 dark:text-rose-400"
                    >
                        Reset Filters
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[40px]">
                            <col class="w-[120px]">
                            <col class="w-[150px]">
                            <col class="w-[200px]">
                            <col class="w-[70px]">
                            <col class="w-[70px]">
                            <col class="w-[80px]">
                            <col class="w-[80px]">
                            <col class="w-[100px]">
                            <col class="w-[80px]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">RJPP</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">CoE</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Compendium</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(item, index) in filteredAppendixItems" :key="`appendix-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-3 text-center text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.project_owner ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200 font-medium">{{ item.use_case ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    {{ item.desc ?? '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.value)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.value ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.urgency)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.urgency ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ (item.rjpp ?? '-').replace(/#/g, '') }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.coe ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    {{ item.compendium }}
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium">
                                    <Link
                                        :href="`/program-planning/program-definition/digital-initiatives/appendix/${item.id}/edit`"
                                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                    >
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="filteredAppendixItems.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-xs text-slate-500 dark:text-slate-400 italic">
                                    Data tidak ditemukan berdasarkan filter yang dipilih.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    appendixItems: {
        type: Array,
        default: () => [],
    },
    totalAppendixItems: {
        type: Number,
        default: 0,
    },
    uniqueCompendiums: {
        type: Array,
        default: () => [],
    },
});

const filters = ref({
    owner: '',
    value: '',
    urgency: '',
    coe: '',
    compendium: '',
});

const uniqueOwners = computed(() => {
    const owners = props.appendixItems
        .map((item) => String(item.project_owner ?? '').trim())
        .filter((owner) => owner !== '' && owner !== '-');
    return [...new Set(owners)].sort();
});

const uniqueCoes = computed(() => {
    const coes = props.appendixItems
        .map((item) => String(item.coe ?? '').trim())
        .filter((coe) => coe !== '' && coe !== '-');
    return [...new Set(coes)].sort();
});

const filteredAppendixItems = computed(() => {
    return props.appendixItems.filter((item) => {
        const ownerVal = String(item.project_owner ?? '').trim() || '-';
        const coeVal = String(item.coe ?? '').trim() || '-';

        const matchOwner = !filters.value.owner || ownerVal === filters.value.owner;
        const matchValue = !filters.value.value || item.value === filters.value.value;
        const matchUrgency = !filters.value.urgency || item.urgency === filters.value.urgency;
        const matchCoe = !filters.value.coe || coeVal === filters.value.coe;

        let matchCompendium = true;
        if (filters.value.compendium === 'none') {
            matchCompendium = item.compendium === '-';
        } else if (filters.value.compendium) {
            matchCompendium = (item.compendium ?? '').includes(filters.value.compendium);
        }

        return matchOwner && matchValue && matchUrgency && matchCoe && matchCompendium;
    });
});

const hasActiveFilters = computed(() => {
    return !!(filters.value.owner || filters.value.value || filters.value.urgency || filters.value.coe || filters.value.compendium);
});

const resetFilters = () => {
    filters.value.owner = '';
    filters.value.value = '';
    filters.value.urgency = '';
    filters.value.coe = '';
    filters.value.compendium = '';
};

const scoreClass = (label) => {
    const l = String(label ?? '').toLowerCase();
    if (l === 'high') return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    if (l === 'medium') return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    if (l === 'low') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    return 'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300';
};
</script>
