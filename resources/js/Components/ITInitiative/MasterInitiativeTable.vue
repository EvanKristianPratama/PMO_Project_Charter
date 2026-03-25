<template>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
            <div class="flex items-center gap-1.5">
                <label class="text-[10px]">CoE</label>
                <select
                    v-model="filterCoe"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option v-for="opt in coeOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px]">Group</label>
                <select
                    v-model="filterGroub"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option v-for="opt in groubOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px]">Organisasi</label>
                <select
                    v-model="filterOrganization"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option v-for="opt in organizationOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px]">Status</label>
                <select
                    v-model="filterStatusTimeline"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option v-for="opt in statusTimelineOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px]">Sumber</label>
                <select
                    v-model="filterSumber"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option v-for="opt in sumberOptions" :key="opt" :value="opt">{{ opt }}</option>
                </select>
            </div>
            <button
                v-if="filterCoe || filterGroub || filterOrganization || filterStatusTimeline || filterSumber"
                type="button"
                class="ml-auto rounded border border-slate-200 px-2 py-0.5 text-[10px] font-semibold normal-case tracking-normal text-slate-500 shadow-sm transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5"
                @click="filterCoe = ''; filterGroub = ''; filterOrganization = ''; filterStatusTimeline = ''; filterSumber = ''"
            >
                Reset
            </button>
        </div>

        <div class="overflow-x-hidden">
            <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[3%]">
                    <col class="w-[4%]">
                    <col class="w-[11%]">
                    <col class="w-[11%]">
                    <col class="w-[15%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[12%]">
                    <col class="w-[12%]">
                    <col class="w-[12%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Centre of Excellence (CoE)</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Initiatives</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Holding / Sub Holding</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Timeline Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                    <tr v-for="(item, index) in filteredItems" :key="`mst-digital-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ item.code ?? '-' }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ coeName(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <p class="font-medium break-words">{{ item.name ?? '-' }}</p>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ descriptionText(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ groubName(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ organizationName(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span v-if="scStatus(item) !== '-'" class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                                {{ scStatus(item) }}
                            </span>
                            <span v-else>-</span>
                        </td>
                        <td class="px-3 py-3 text-[10px] font-medium">
                            <div class="flex flex-col items-start gap-1">
                                <Link
                                    v-if="hasLinkedInitiative(item)"
                                    :href="initiativeHref(item)"
                                    :class="actionCellClass(true)"
                                    title="View Scope Charter"
                                >
                                    Scope Charter
                                </Link>
                                <button
                                    v-else
                                    type="button"
                                    :class="actionCellClass(false)"
                                    title="Data belum ada"
                                    @click="showNoDataModal = true"
                                >
                                    Scope Charter
                                </button>
                                <Link
                                    :href="editHref(item)"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30 transition-colors cursor-pointer"
                                    title="Edit Digital Initiative"
                                >
                                    Edit
                                </Link>
                                <Link
                                    :href="item.mappedProjects?.[0]?.id ? route('it-initiatives.show', { project: item.mappedProjects[0].id, tab: 'detail' }) : route('it-initiatives.index', { tableMode: 'implementation' })"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30 transition-colors cursor-pointer"
                                    title="Status Implementation"
                                >
                                    Status Implementation
                                </Link>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-700 dark:bg-white/10 dark:text-slate-300">
                                {{ statusName(item) }}
                            </span>
                        </td>
                    </tr>

                    <tr v-if="filteredItems.length === 0">
                        <td colspan="10" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            Tidak ada data `mst_initiative` tipe 1.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <ConfirmationModal
        :show="showNoDataModal"
        type="info"
        title="Data Belum Tersedia"
        message="Data Scope Charter untuk inisiatif ini belum tersedia."
        :cancel-text="''"
        confirm-text="Tutup"
        @confirm="showNoDataModal = false"
        @close="showNoDataModal = false"
    />
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    initiativeItems: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();

const showNoDataModal = ref(false);

const normalizeText = (value) => {
    const text = String(value ?? '').trim();
    return text.length > 0 ? text : '-';
};

const coeName = (item) => normalizeText(item?.coe?.name ?? item?.coe_name ?? item?.coe_id);

const organizationName = (item) => normalizeText(item?.organization?.name ?? item?.business_unit_name ?? item?.business_unit);

const groubName = (item) => normalizeText(item?.organization?.groub?.name);

// Filter state
const filterCoe = ref('');
const filterGroub = ref('');
const filterOrganization = ref('');
const filterStatusTimeline = ref('');
const filterSumber = ref('');

const uniqueSorted = (arr) => [...new Set(arr.filter((v) => v && v !== '-'))].sort();

const coeOptions = computed(() => uniqueSorted(props.items.map((item) => coeName(item))));
const groubOptions = computed(() => uniqueSorted(props.items.map((item) => groubName(item))));
const organizationOptions = computed(() => uniqueSorted(props.items.map((item) => organizationName(item))));
const statusTimelineOptions = computed(() => uniqueSorted(props.items.map((item) => normalizeText(item?.latest_status?.status))));
const sumberOptions = computed(() => uniqueSorted(props.items.map((item) => statusName(item))));

const filteredItems = computed(() => {
    return props.items.filter((item) => {
        if (filterCoe.value && coeName(item) !== filterCoe.value) return false;
        if (filterGroub.value && groubName(item) !== filterGroub.value) return false;
        if (filterOrganization.value && organizationName(item) !== filterOrganization.value) return false;
        if (filterStatusTimeline.value && scStatus(item) !== filterStatusTimeline.value) return false;
        if (filterSumber.value && statusName(item) !== filterSumber.value) return false;
        return true;
    });
});

const organizationWithGroup = (item) => {
    const org = organizationName(item);
    const group = groubName(item);

    if (org === '-' && group === '-') {
        return '-';
    }

    if (group === '-') {
        return org;
    }

    if (org === '-') {
        return group;
    }

    return `${org} - ${group}`;
};

const statusName = (item) => normalizeText(item?.status);

const descriptionText = (item) => normalizeText(item?.description);

const scStatus = (item) => {
    return normalizeText(item?.latest_status?.status);
};

const scReviewStatus = (item) => {
    return normalizeText(item?.latest_status?.notes);
};

const normalizeKey = (value) => String(value ?? '').trim().toLowerCase();

const resolveLinkedInitiative = (item) => {
    const sourceItems = Array.isArray(props.initiativeItems) ? props.initiativeItems : [];
    const codeKey = normalizeKey(item?.code);
    const nameKey = normalizeKey(item?.name);

    return sourceItems.find((initiative) =>
        normalizeKey(initiative?.no) === codeKey ||
        normalizeKey(initiative?.useCase) === nameKey
    ) || null;
};

const hasLinkedInitiative = (item) => Boolean(resolveLinkedInitiative(item)?.id);

const initiativeHref = (item) => {
    const matchedInitiative = resolveLinkedInitiative(item);
    return matchedInitiative?.id ? route('digital-initiatives.show', matchedInitiative.id) : route('digital-initiatives.index');
};

const editHref = (item) => {
    return route('master-data.mst-initiatives.edit', item.id);
};

const actionCellClass = (isReady) => {
    if (isReady) {
        return 'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30 transition-colors cursor-pointer';
    }

    return 'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-rose-100 text-rose-800 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30 transition-colors cursor-pointer';
};
</script>
