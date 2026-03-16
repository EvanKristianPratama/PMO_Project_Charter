<template>
    <UserLayout title="Program Definition Digital Initiatives â€” Appendix List">
        <div class="animate-fade-in space-y-4">
            <section class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <SummaryCard
                    :total="totalDigitalInitiatives"
                    @create="showCreateModal = true"
                    @show-all="toggleShowAll"
                />

                <div class="lg:col-span-2">
                    <TimelineFlow
                        :status-counts="statusCounts"
                        :postpone-from-counts="postponeFromCounts"
                        :active-status="activeStatusFilter"
                        @select="toggleStatusFilter"
                    />
                </div>
            </section>
            
            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717] w-fit">
                <Link
                    href="/program-planning/program-definition/digital-initiatives"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Digital Initiatives List
                </Link>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/compendium"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Compendium List
                </Link>
                <div
                    class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400"
                >
                    Appendix List
                </div>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/mapping"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Mapping
                </Link>
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
                        <button
                            type="button"
                            @click="openCreateModal"
                            class="inline-flex items-center rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f]"
                        >
                            New Appendix
                        </button>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-3 border-b border-slate-100 bg-slate-50/30 px-4 py-2.5 dark:border-white/5 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Master Initiative:</label>
                        <select
                            v-model="filters.masterInitiative"
                            class="w-[125px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All</option>
                            <option value="__none__">No Master Initiative</option>
                            <option v-for="opt in initiativeOptions" :key="opt.id" :value="opt.code ? `${opt.code} - ${opt.name}` : opt.name">
                                {{ opt.code ? `${opt.code} - ` : '' }}{{ opt.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Compendium:</label>
                        <select
                            v-model="filters.compendium"
                            class="w-[125px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All</option>
                            <option value="__none__">No Compendium</option>
                            <option v-for="opt in compendiumOptions" :key="opt.id" :value="opt.label">{{ opt.label }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Appendix:</label>
                        <select
                            v-model="filters.appendix"
                            class="w-[125px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[11px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All</option>
                            <option value="__none__">None</option>
                            <option v-for="val in uniqueAppendixUseCases" :key="val" :value="val">{{ val }}</option>
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
                            <col class="w-[35px]">
                            <col class="w-[150px]">
                            <col class="w-[120px]">
                            <col class="w-[200px]">
                            <col class="w-[240px]">
                            <col class="w-[60px]">
                            <col class="w-[100px]">
                            <col class="w-[140px]">
                            <col class="w-[60px]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">PIC</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Scope Charter</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Scope Charter Description</th>
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
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.pic ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200 font-medium">{{ item.use_case_appendix ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.use_case_appendix_description ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ (item.rjpp ?? '-').replace(/#/g, '') }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.coe ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.use_case_compendium ?? '-' }}</td>
                                <td class="px-3 py-3 text-[10px] font-medium">
                                    <Link
                                        v-if="resolveDetailId(item)"
                                        :href="`/program-planning/program-definition/digital-initiatives/appendix/${resolveDetailId(item)}/edit`"
                                        class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-[9px] font-bold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                    >
                                        Show
                                    </Link>
                                    <span
                                        v-else
                                        class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[9px] font-bold text-slate-400 dark:bg-white/5 dark:text-slate-500"
                                    >
                                        -
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="filteredAppendixItems.length === 0">
                                <td colspan="9" class="px-6 py-10 text-center text-xs text-slate-500 dark:text-slate-400 italic">
                                    Data tidak ditemukan berdasarkan filter yang dipilih.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <AppendixCharterModal
            :show="isCreateModalOpen"
            :compendium="null"
            :appendix="null"
            :compendium-options="compendiumOptions"
            :initiative-options="initiativeOptions"
            :coe-options="coeOptions"
            :source-options="sourceOptions"
            :theme-options="themeOptions"
            :organization-options="organizationOptions"
            @close="closeCreateModal"
            @success="closeCreateModal"
        />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import AppendixCharterModal from '@/Components/Appendix/AppendixCharterModal.vue';

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
    compendiumOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    sourceOptions: {
        type: Array,
        default: () => [],
    },
    themeOptions: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

const isCreateModalOpen = ref(false);

const openCreateModal = () => {
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
};

const filters = ref({
    masterInitiative: '',
    compendium: '',
    appendix: '',
});

const normalizeFilterValue = (value) => {
    if (value === null || value === undefined) {
        return '';
    }
    return String(value);
};

const isAllFilter = (value) => value === '';
const isNoneFilter = (value) => value === '__none__';
const isSpecificFilter = (value) => value !== '';

const parseListValue = (value) => String(value ?? '')
    .split(',')
    .map((item) => item.trim())
    .filter((item) => item !== '' && item !== '-');

const joinListValues = (values) => {
    const list = [...new Set(values.flatMap(parseListValue))];
    return list.length ? list.join(', ') : '-';
};

const joinDistinctValues = (values) => {
    const list = [...new Set(values.map((value) => String(value ?? '').trim()).filter((val) => val !== '' && val !== '-'))];
    return list.length ? list.join(', ') : '-';
};

const formatMasterInitiativeLabel = (opt) => {
    if (!opt) return '';
    const code = String(opt.code ?? '').trim();
    const name = String(opt.name ?? '').trim();
    if (code && name) return `${code} - ${name}`;
    return name || '';
};

const masterInitiativeRows = computed(() => {
    const labels = (props.initiativeOptions ?? [])
        .map(formatMasterInitiativeLabel)
        .filter((label) => label !== '');

    const appendixMap = new Map();
    for (const item of props.appendixItems ?? []) {
        const key = String(item.master_initiative ?? '').trim();
        if (!key) continue;
        if (!appendixMap.has(key)) appendixMap.set(key, []);
        appendixMap.get(key).push(item);
    }

    const compendiumMap = new Map();
    const compendiumDescMap = new Map();
    for (const option of props.compendiumOptions ?? []) {
        const masterKey = String(option.master_initiative ?? '').trim();
        const label = String(option.label ?? '').trim();
        if (!masterKey || !label || masterKey === '-') continue;
        if (!compendiumMap.has(masterKey)) compendiumMap.set(masterKey, []);
        compendiumMap.get(masterKey).push(label);

        const desc = String(option.description ?? '').trim();
        if (desc && desc !== '-') {
            if (!compendiumDescMap.has(masterKey)) compendiumDescMap.set(masterKey, []);
            compendiumDescMap.get(masterKey).push(desc);
        }
    }

        return labels.map((label) => {
            const appendixItems = appendixMap.get(label) ?? [];
            const detailId = appendixItems.length === 1 ? appendixItems[0]?.id : null;
            const compendiumLabels = compendiumMap.get(label) ?? [];
            const compendiumDescriptions = compendiumDescMap.get(label) ?? [];
            return {
                id: `mi-${label}`,
                master_initiative: label,
                project_owner: joinDistinctValues(appendixItems.map((item) => item.project_owner)),
                pic: joinDistinctValues(appendixItems.map((item) => item.pic)),
                use_case_compendium: joinDistinctValues(compendiumLabels),
                use_case_compendium_description: joinDistinctValues(compendiumDescriptions),
                use_case_appendix: joinListValues(appendixItems.map((item) => item.use_case_appendix)),
                use_case_appendix_description: joinDistinctValues(appendixItems.map((item) => item.use_case_appendix_description)),
                rjpp: joinListValues(appendixItems.map((item) => item.rjpp)),
                coe: joinDistinctValues(appendixItems.map((item) => item.coe)),
                detail_id: detailId,
            };
        });
    });

const compendiumRows = computed(() => {
    const labels = [];
    const seen = new Set();
    for (const opt of props.compendiumOptions ?? []) {
        const label = String(opt.label ?? '').trim();
        if (!label || seen.has(label)) continue;
        seen.add(label);
        labels.push(label);
    }

    const compendiumByLabel = new Map();
    for (const opt of props.compendiumOptions ?? []) {
        const label = String(opt.label ?? '').trim();
        if (!label) continue;
        if (!compendiumByLabel.has(label)) compendiumByLabel.set(label, []);
        compendiumByLabel.get(label).push(opt);
    }

        return labels.map((label) => {
            const options = compendiumByLabel.get(label) ?? [];
            const items = (props.appendixItems ?? []).filter((item) => {
                const list = parseListValue(item.use_case_compendium);
                return list.includes(label);
            });
            const detailId = items.length === 1 ? items[0]?.id : null;
            return {
                id: `comp-${label}`,
                master_initiative: joinDistinctValues([
                    ...options.map((option) => option.master_initiative),
                    ...items.map((item) => item.master_initiative),
                ]),
                project_owner: joinDistinctValues(items.map((item) => item.project_owner)),
                pic: joinDistinctValues(items.map((item) => item.pic)),
                use_case_compendium: label,
                use_case_compendium_description: joinDistinctValues([
                    ...options.map((option) => option.description),
                    ...items.map((item) => item.use_case_compendium_description),
                ]),
                use_case_appendix: joinListValues(items.map((item) => item.use_case_appendix)),
                use_case_appendix_description: joinDistinctValues(items.map((item) => item.use_case_appendix_description)),
                rjpp: joinListValues(items.map((item) => item.rjpp)),
                coe: joinDistinctValues(items.map((item) => item.coe)),
                detail_id: detailId,
            };
        });
    });

const uniqueAppendixUseCases = computed(() => {
    const items = props.appendixItems
        .map((item) => String(item.use_case_appendix ?? '').trim())
        .filter((val) => val !== '' && val !== '-');
    return [...new Set(items)].sort();
});

const appendixRows = computed(() => {
    return uniqueAppendixUseCases.value.map((label) => {
        const items = (props.appendixItems ?? []).filter((item) => item.use_case_appendix === label);
        const detailId = items.length === 1 ? items[0]?.id : null;
        return {
            id: `app-${label}`,
            master_initiative: joinDistinctValues(items.map((item) => item.master_initiative)),
            project_owner: joinDistinctValues(items.map((item) => item.project_owner)),
            pic: joinDistinctValues(items.map((item) => item.pic)),
            use_case_compendium: joinListValues(items.map((item) => item.use_case_compendium)),
            use_case_compendium_description: joinDistinctValues(items.map((item) => item.use_case_compendium_description)),
            use_case_appendix: label,
            use_case_appendix_description: joinDistinctValues(items.map((item) => item.use_case_appendix_description)),
            rjpp: joinListValues(items.map((item) => item.rjpp)),
            coe: joinDistinctValues(items.map((item) => item.coe)),
            detail_id: detailId,
        };
    });
});

const filteredAppendixItems = computed(() => {
    const masterRaw = filters.value.masterInitiative;
    const compendiumRaw = filters.value.compendium;
    const appendixRaw = filters.value.appendix;

    const masterFilter = normalizeFilterValue(masterRaw);
    const compendiumFilter = normalizeFilterValue(compendiumRaw);
    const appendixFilter = normalizeFilterValue(appendixRaw);

    return (props.appendixItems ?? []).filter((item) => {
        const masterValue = String(item.master_initiative ?? '').trim();
        const matchMaster = isNoneFilter(masterRaw)
            ? masterValue === '' || masterValue === '-'
            : (!masterFilter || masterValue === masterFilter);
        const appendixValue = String(item.use_case_appendix ?? '').trim();
        const matchAppendix = isNoneFilter(appendixRaw)
            ? appendixValue === '' || appendixValue === '-'
            : (!appendixFilter || appendixValue === appendixFilter);

        let matchCompendium = true;
        if (isNoneFilter(compendiumRaw)) {
            matchCompendium = parseListValue(item.use_case_compendium).length === 0;
        } else if (compendiumFilter) {
            matchCompendium = parseListValue(item.use_case_compendium).includes(compendiumFilter);
        }

        return matchMaster && matchCompendium && matchAppendix;
    });
});

const hasActiveFilters = computed(() => {
    return isSpecificFilter(filters.value.masterInitiative)
        || isSpecificFilter(filters.value.compendium)
        || isSpecificFilter(filters.value.appendix);
});

const resolveDetailId = (item) => {
    const raw = item?.detail_id ?? item?.id ?? null;
    const num = Number(raw);
    return Number.isFinite(num) ? num : null;
};

const resetFilters = () => {
    filters.value.masterInitiative = '';
    filters.value.compendium = '';
    filters.value.appendix = '';
};
</script>
