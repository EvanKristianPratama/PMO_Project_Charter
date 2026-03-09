<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">{{ title }}</h2>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400">
            <div class="flex items-center gap-1.5">
                <label class="text-[10px]">Tipe</label>
                <select
                    v-model="filterTipe"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option value="1">Digital</option>
                    <option value="2">IT</option>
                </select>
            </div>
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
                <label class="text-[10px]">Holding</label>
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
                    v-model="filterStatus"
                    class="rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="">All</option>
                    <option v-for="opt in statusOptions" :key="opt" :value="opt">{{ opt }}</option>
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
                v-if="filterTipe || filterCoe || filterGroub || filterOrganization || filterStatus || filterSumber"
                type="button"
                class="ml-auto rounded border border-slate-200 px-2 py-0.5 text-[10px] font-semibold normal-case tracking-normal text-slate-500 shadow-sm transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5"
                @click="resetFilters"
            >
                Reset
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                <colgroup>
                    <col class="w-[3%]">
                    <col class="w-[5%]">
                    <col class="w-[11%]">
                    <col class="w-[13%]">
                    <col class="w-[17%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[8%]">
                    <col class="w-[11%]">
                </colgroup>
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Centre of Excellence (CoE)</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Inisiatif</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Deskripsi</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Holding / Sub Holding</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Organisasi</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Timeline</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sumber</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                    <tr v-for="(item, index) in filteredItems" :key="`mst-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ item.code ?? '-' }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ coeName(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <p class="font-medium break-words">{{ item.name ?? '-' }}</p>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ normalizeText(item.description) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ groubName(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">{{ organizationName(item) }}</td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span v-if="latestStatusText(item) !== '-'" class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClass(latestStatusText(item))">
                                {{ latestStatusText(item) }}
                            </span>
                            <span v-else>-</span>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            {{ statusDateText(item) }}
                        </td>
                        <td class="px-3 py-3 text-[10px] font-medium">
                            <div class="flex flex-col items-start gap-1">
                                <Link
                                    :href="editHref(item)"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30 transition-colors cursor-pointer"
                                    title="Edit Initiative"
                                >
                                    Edit
                                </Link>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium" :class="tipeBadgeClass(item.tipe_initiative)">
                                {{ tipeLabel(item.tipe_initiative) }}
                            </span>
                        </td>
                    </tr>

                    <tr v-if="filteredItems.length === 0">
                        <td colspan="11" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                            Tidak ada data initiative.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: 'Initiative Status Table',
    },
    /** Optional: pre-filter by tipe_initiative (1=Digital, 2=IT). Empty = show all */
    initialTipeFilter: {
        type: String,
        default: '',
    },
});

const normalizeText = (value) => {
    const text = String(value ?? '').trim();
    return text.length > 0 ? text : '-';
};

const coeName = (item) => normalizeText(item?.coe?.name ?? item?.coe_name);
const organizationName = (item) => normalizeText(item?.organization?.name ?? item?.business_unit_name);
const groubName = (item) => normalizeText(item?.organization?.groub?.name);

const latestStatusText = (item) => {
    return normalizeText(item?.project_status_label ?? 'Not Start');
};

const formatDate = (value) => {
    const normalized = String(value ?? '').trim();
    if (!normalized) {
        return '-';
    }

    const date = new Date(normalized);
    if (Number.isNaN(date.getTime())) {
        return normalized;
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const statusDateText = (item) => {
    return formatDate(item?.project_status_date);
};

const tipeLabel = (tipe) => {
    return Number(tipe) === 1 ? 'Digital' : Number(tipe) === 2 ? 'IT' : '-';
};

const tipeBadgeClass = (tipe) => {
    return Number(tipe) === 1
        ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300'
        : Number(tipe) === 2
            ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300'
            : 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};

const statusBadgeClass = (status) => {
    const key = String(status ?? '').trim().toLowerCase();
    const map = {
        'not start': 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
        drafting:  'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
        propose:   'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        review:    'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        baseline:  'bg-lime-100 text-lime-700 dark:bg-lime-500/20 dark:text-lime-300',
        approved:  'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        postpone:  'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
    };
    return map[key] ?? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300';
};

const editHref = (item) => `/master-data/master-initiatives/${item.id}/edit`;

// ── Filters ──
const filterTipe = ref(props.initialTipeFilter);
const filterCoe = ref('');
const filterGroub = ref('');
const filterOrganization = ref('');
const filterStatus = ref('');
const filterSumber = ref('');

const resetFilters = () => {
    filterTipe.value = '';
    filterCoe.value = '';
    filterGroub.value = '';
    filterOrganization.value = '';
    filterStatus.value = '';
    filterSumber.value = '';
};

const uniqueSorted = (arr) => [...new Set(arr.filter((v) => v && v !== '-'))].sort();

const coeOptions = computed(() => uniqueSorted(props.items.map((item) => coeName(item))));
const groubOptions = computed(() => uniqueSorted(props.items.map((item) => groubName(item))));
const organizationOptions = computed(() => uniqueSorted(props.items.map((item) => organizationName(item))));
const statusOptions = computed(() => uniqueSorted(props.items.map((item) => latestStatusText(item))));
const sumberOptions = computed(() => uniqueSorted(props.items.map((item) => tipeLabel(item.tipe_initiative))));

const filteredItems = computed(() => {
    return props.items.filter((item) => {
        if (filterTipe.value && String(item.tipe_initiative) !== filterTipe.value) return false;
        if (filterCoe.value && coeName(item) !== filterCoe.value) return false;
        if (filterGroub.value && groubName(item) !== filterGroub.value) return false;
        if (filterOrganization.value && organizationName(item) !== filterOrganization.value) return false;
        if (filterStatus.value && latestStatusText(item) !== filterStatus.value) return false;
        if (filterSumber.value && tipeLabel(item.tipe_initiative) !== filterSumber.value) return false;
        return true;
    });
});
</script>
