<script setup>
import { computed, ref, onMounted, watch } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
});



const normalizeCoeName = (rawName) => {
    let name = String(rawName ?? '').trim();
    if (!name || name === '-' || name.toUpperCase() === 'NO COE') return 'CoE Not Identified';
    
    const upper = name.toUpperCase();
    if (upper === 'IOT') return 'IoT';
    if (upper.includes('CLOUD') || upper.includes('COMPUTING') || name === 'Advance Cloud') return 'Advance Cloud';
    if (upper === 'RPA') return 'RPA';
    if (upper.includes('ROBOT') || name === 'Robotics') return 'Robotics';
    if (upper.includes('ANALYTICS') || name === 'AI / Adv. Analytics') return 'AI / Adv. Analytics';
    
    return name;
};

const normalizeStatusLabel = (rawStatus) => {
    const s = String(rawStatus ?? '').trim();
    if (!s) return null;
    if (s === 'DF') return 'DF';
    if (s === 'Done') return 'Done';
    if (s === 'DT 2026') return 'DT 2026';
    if (s === 'ITSBP') return 'ITSBP';
    if (s === 'On Review') return 'On Review';
    if (s === 'SH') return 'SH';
    return s;
};

const DEFAULT_INITIATIVE_COLUMN_COUNT = 6;
const initiativeColumnOptions = [3, 4, 5, 6];
const initiativeColumnCount = ref(DEFAULT_INITIATIVE_COLUMN_COUNT);
const showBusinessUnit = ref(false);
const showStatusColors = ref(true);
const showInitiativeCode = ref(true);
const selectedStatus = ref('');
const selectedPeriod = ref('latest');

const monthsOrder = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const availablePeriods = computed(() => {
    const periodSet = new Set();
    props.items.forEach(initiative => {
        if (initiative.statuses && Array.isArray(initiative.statuses)) {
            initiative.statuses.forEach(s => {
                if (s.month && s.year) {
                    periodSet.add(`${s.month}-${s.year}`);
                }
            });
        }
    });
    
    const list = Array.from(periodSet).map(period => {
        const [month, year] = period.split('-');
        return { 
            label: `${month} ${year}`, 
            value: period, 
            month, 
            year: parseInt(year) 
        };
    }).sort((a, b) => {
        if (a.year !== b.year) return b.year - a.year;
        return monthsOrder.indexOf(b.month) - monthsOrder.indexOf(a.month);
    });

    return [{ label: 'Latest Status Update', value: 'latest' }, ...list];
});

// Set default period to 'latest'
onMounted(() => {
    selectedPeriod.value = 'latest';
});

const getInitiativeStatusByPeriod = (initiative, periodValue) => {
    if (periodValue === 'latest') {
        return initiative.implementation_status;
    }
    if (!initiative.statuses || !Array.isArray(initiative.statuses)) {
        return null;
    }
    const found = initiative.statuses.find(s => `${s.month}-${s.year}` === periodValue);
    return found ? found.status : null;
};

const getStatusColorClass = (status) => {
    const normalized = String(status ?? '').trim().toLowerCase();
    if (normalized === 'on track') return 'status-color-ontrack';
    if (normalized === 'done' || normalized === 'completed') return 'status-color-done';
    if (normalized === 'at risk') return 'status-color-atrisk';
    if (normalized === 'delayed') return 'status-color-delayed';
    if (normalized === 'not started') return 'status-color-notstarted';
    return '';
};

const statusDesiredOrder = ['On Track', 'Delayed', 'At Risk', 'Completed', 'Done', 'Not Started'];

const statusLegend = computed(() => {
    const stats = {};
    statusDesiredOrder.forEach(label => {
        stats[label] = 0;
    });
    stats['Other'] = 0;

    // Kalkulasi status berdasarkan seluruh inisiatif di periode yang dipilih (tanpa filter status)
    props.items.forEach((initiative) => {
        if (Number(initiative.tipe_initiative) !== 2) return;
        
        const status = getInitiativeStatusByPeriod(initiative, selectedPeriod.value);
        if (!status) return;

        const label = statusDesiredOrder.find(
            s => s.toLowerCase() === String(status).trim().toLowerCase()
        ) || 'Other';
        
        stats[label]++;
    });

    const legend = statusDesiredOrder.map((label) => ({
        label,
        class: getStatusColorClass(label),
        count: stats[label],
    })).filter(item => item.count > 0);

    if (stats['Other'] > 0) {
        legend.push({
            label: 'Other',
            class: '',
            count: stats['Other']
        });
    }

    return legend;
});

const totalOverallInitiatives = computed(() => {
    return statusLegend.value.reduce((sum, item) => sum + item.count, 0);
});

const displayGroups = computed(() => {
    const coeMap = new Map();
    const UNIDENTIFIED = 'CoE Not Identified';

    props.items.forEach(initiative => {
        const isItInitiative = Number(initiative.tipe_initiative) === 2;
        const currentStatus = getInitiativeStatusByPeriod(initiative, selectedPeriod.value);
        const statusLabel = String(currentStatus ?? '').trim();
        const matchesStatus = !selectedStatus.value || statusLabel.toLowerCase() === selectedStatus.value.toLowerCase();

        if (isItInitiative && matchesStatus) {
            const coeName = normalizeCoeName(initiative.coe_name || initiative.coe?.name);
            if (!coeMap.has(coeName)) {
                coeMap.set(coeName, []);
            }
            coeMap.get(coeName).push({
                ...initiative,
                display_status: currentStatus
            });
        }
    });

    const foundCoeNames = Array.from(coeMap.keys());
    const coeGroups = foundCoeNames.map(name => {
        const initiatives = coeMap.get(name) || [];
        return {
            name,
            initiatives: initiatives.sort((a, b) => {
                const codeA = String(a?.code ?? '');
                const codeB = String(b?.code ?? '');
                return codeA.localeCompare(codeB, undefined, { numeric: true, sensitivity: 'base' });
            }),
            total: initiatives.length
        };
    });

    coeGroups.sort((a, b) => {
        if (a.name === UNIDENTIFIED && b.name !== UNIDENTIFIED) return 1;
        if (b.name === UNIDENTIFIED && a.name !== UNIDENTIFIED) return -1;
        const codeA = a.initiatives.length > 0 ? String(a.initiatives[0]?.code ?? '') : '';
        const codeB = b.initiatives.length > 0 ? String(b.initiatives[0]?.code ?? '') : '';
        if (codeA === '' && codeB !== '') return 1;
        if (codeB === '' && codeA !== '') return -1;
        return codeA.localeCompare(codeB, undefined, { numeric: true, sensitivity: 'base' });
    });

    return coeGroups;
});

const buildInitiativeColumns = (initiatives = [], columnCount = initiativeColumnCount.value) => {
    if (!initiatives.length) return { items: [], rowCount: 0 };
    return { 
        items: initiatives,
        rowCount: Math.ceil(initiatives.length / Number(columnCount)) 
    };
};

const getCoeColorClass = (coeName) => {
    const name = normalizeCoeName(coeName);
    if (name === 'IoT') return 'coe-color-blue';
    if (name === 'Advance Cloud') return 'coe-color-emerald';
    if (name === 'RPA') return 'coe-color-amber';
    if (name === 'Robotics') return 'coe-color-purple';
    if (name === 'AI / Adv. Analytics') return 'coe-color-none';
    if (name === 'CoE Not Identified') return 'coe-color-none';
    return 'coe-color-none';
};

</script>

<template>
    <div class="space-y-4">
        <div class="space-y-4">
            <!-- Legend Section (Always Visible) -->
            <div class="space-y-2.5">
                <!-- Status Implementation Legend -->
                <div v-if="showStatusColors" class="flex flex-wrap items-center gap-x-4 gap-y-2 dark:border-white/5">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Implementation Status (IT Initiative):</span>
                    <div
                        v-for="status in statusLegend"
                        :key="`status-legend-${status.label}`"
                        class="flex items-center gap-1.5 cursor-pointer select-none transition-opacity"
                        :class="{ 'opacity-40': selectedStatus && selectedStatus !== status.label }"
                        @click="selectedStatus = selectedStatus === status.label ? '' : status.label"
                        :title="`Filter: ${status.label}`"
                    >
                        <span
                            class="h-3 w-3 rounded-sm shadow-sm legend-swatch"
                            :class="status.class"
                        ></span>
                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                            {{ status.label }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ status.count }})</span>
                        </span>
                    </div>

                    <!-- Total Overall -->
                    <div v-if="totalOverallInitiatives > 0" class="flex items-center gap-1.5 border-l border-slate-300 pl-4 ml-1 dark:border-white/10">
                        <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">
                            Total IT Initiatives <span class="text-slate-500 dark:text-slate-400 font-medium">({{ totalOverallInitiatives }})</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Toolbar (Always Visible) -->
            <div class="flex items-center justify-start">
                <div class="initiative-view-switch">
                    <select v-model="selectedPeriod" class="initiative-view-select mr-2">
                        <option value="" disabled>Pilih Periode</option>
                        <option v-for="period in availablePeriods" :key="period.value" :value="period.value">{{ period.label }}</option>
                    </select>

                    <select v-model="selectedStatus" class="initiative-view-select mr-2">
                        <option value="">Semua Status</option>
                        <option v-for="st in statusDesiredOrder" :key="st" :value="st">{{ st }}</option>
                    </select>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showBusinessUnit }"
                        title="Tampilkan/Sembunyikan Business Unit"
                        @click="showBusinessUnit = !showBusinessUnit"
                    >
                        <svg v-if="showBusinessUnit" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                        <span>Business Unit</span>
                    </button>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showStatusColors }"
                        title="Tampilkan/Sembunyikan Warna Status Implementasi"
                        @click="showStatusColors = !showStatusColors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        <span>Status Impl.</span>
                    </button>

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showInitiativeCode }"
                        title="Tampilkan/Sembunyikan Code Initiative"
                        @click="showInitiativeCode = !showInitiativeCode"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span>Code</span>
                    </button>

                    <span class="initiative-view-switch__label ml-2">Tampilan kolom:</span>
                    <select v-model="initiativeColumnCount" class="initiative-view-select">
                        <option v-for="option in initiativeColumnOptions" :key="option" :value="option">{{ option }} Kolom</option>
                    </select>
                </div>
            </div>

            <!-- Table Matrix or Empty State -->
            <template v-if="displayGroups.length > 0">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <h1 class="text-center text-l font-bold mt-4 mb-4 ">IT Initiative bases on IT Building Blocks</h1>
                        <table class="itb-table min-w-full border-collapse" :class="`itb-table--${initiativeColumnCount}-cols`">
                            <thead>
                                <tr>
                                    <th class="top-head top-head-left" style="width: 15%;">IT Building Blocks</th>
                                    <th class="top-head top-head-right" style="width: 85%;">IT Initiatives</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="group in displayGroups" :key="group.name">
                                    <td class="primary-cell" :class="getCoeColorClass(group.name)">
                                        <div class="primary-cell__content">
                                            <div class="primary-label-wrapper">
                                                <span class="text-xs">{{ group.name }}</span>
                                                <span class="count-capsule">{{ group.total }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="initiatives-cell">
                                        <div class="initiatives-grid" :style="{
                                            '--initiative-column-count': initiativeColumnCount,
                                            '--row-count': buildInitiativeColumns(group.initiatives).rowCount
                                        }">
                                            <div
                                                v-for="initiative in buildInitiativeColumns(group.initiatives).items"
                                                :key="initiative.id"
                                                class="initiative-box group"
                                                :class="[
                                                    getCoeColorClass(initiative.coe_name || initiative.coe?.name),
                                                    { 'initiative-box--no-code': !showInitiativeCode || !initiative.code }
                                                ]"
                                            >
                                                <div class="absolute top-full left-1/2 z-50 mt-1 hidden -translate-x-1/2 w-max max-w-sm bg-white border border-slate-800 shadow-sm px-1.5 py-1 text-[9px] italic group-hover:block dark:bg-slate-800">
                                                    {{ initiative.name }}
                                                </div>
                                                <span v-if="showInitiativeCode && initiative.code" 
                                                    class="initiative-box__code"
                                                    :class="showStatusColors ? getStatusColorClass(initiative.display_status) : ''"
                                                >
                                                    {{ initiative.code }}
                                                </span>
                                                <span class="initiative-box__name" :class="{ 'initiative-box__name--full': !showInitiativeCode || !initiative.code }">
                                                    <span class="initiative-box__label-text">{{ initiative.name }}</span>
                                                    <span v-if="showBusinessUnit" class="initiative-box__bu">{{ initiative.business_unit }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </template>

            <section v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tidak ada data inisiatif untuk kriteria ini.</p>
            </section>
        </div>
    </div>
</template>

<style scoped>
.itb-table { background: #ffffff; width: 100%; border-collapse: collapse; }
.itb-table th, .itb-table td { border: 1px solid #c7d2de; vertical-align: top; }
.top-head { padding: 10px 12px; background: #0f6fb7; color: #ffffff; font-size: 12px; font-weight: 800; text-align: center; letter-spacing: 0.05em; }
.primary-cell { vertical-align: middle !important; min-width: 120px; transition: all 0.2s; }
.primary-cell__content { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; }
.primary-label-wrapper { display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 6px; width: 100%; }
.count-capsule { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0 4px; border-radius: 999px; background: rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.1); font-size: 9px; font-weight: 800; color: inherit; flex-shrink: 0; }
.initiatives-cell { padding: 8px; background: #f8fafc; }
.initiatives-grid { display: grid; grid-template-columns: repeat(var(--initiative-column-count, 6), minmax(0, 1fr)); grid-auto-flow: column; grid-template-rows: repeat(var(--row-count, 1), minmax(min-content, 1fr)); gap: 8px; align-items: stretch; }
.initiative-box { position: relative; display: grid; grid-template-columns: 28px minmax(0, 1fr); min-height: 24px; width: 100%; align-items: stretch; border: 1px solid #374151; background: #ffffff; font-size: 9px; font-weight: 500; line-height: 1.1; color: #1f2937; }
.initiative-box--no-code { grid-template-columns: 1fr !important; }
.initiative-box__code { display: flex; align-items: center; justify-content: center; border-right: 1px solid #374151; padding: 2px 4px; font-weight: 700; min-width: 28px; background: rgba(0,0,0,0.03); }

/* Mendukung min-width pada tampilan kolom yang lebih sedikit agar tidak terlalu sempit */
.itb-table--3-cols,
.itb-table--4-cols,
.itb-table--5-cols {
    min-width: 1080px;
}

.itb-table--6-cols {
    min-width: 100%;
    table-layout: fixed;
}

.initiative-box__name { display: flex; flex-direction: column; padding: 2px 5px; word-break: break-word; justify-content: center; }
.initiative-box__bu { font-size: 7.5px; font-weight: 700; font-style: italic; opacity: 0.7; }
.initiative-box__name--full { grid-column: 1 / -1; }

.coe-color-blue { background-color: #eff6ff; color: #1d4ed8; border-left: 4px solid #1d4ed8 !important; }
.coe-color-emerald { background-color: #ecfdf5; color: #047857; border-left: 4px solid #047857 !important; }
.coe-color-amber { background-color: #fffbeb; color: #b45309; border-left: 4px solid #b45309 !important; }
.coe-color-purple { background-color: #faf5ff; color: #6d28d9; border-left: 4px solid #6d28d9 !important; }
.coe-color-rose { background-color: #fff1f2; color: #be123c; border-left: 4px solid #be123c !important; }
.coe-color-indigo { background-color: #eef2ff; color: #4338ca; border-left: 4px solid #4338ca !important; }
.coe-color-none { background-color: #ffffff; color: #475569; }

/* Status colors consistent with IT Initiative */
.status-color-ontrack { background-color: #10b981 !important; color: #ffffff !important; border-color: #059669 !important; }
.status-color-done { background-color: #3b82f6 !important; color: #ffffff !important; border-color: #2563eb !important; }
.status-color-atrisk { background-color: #f59e0b !important; color: #ffffff !important; border-color: #d97706 !important; }
.status-color-delayed { background-color: #f43f5e !important; color: #ffffff !important; border-color: #e11d48 !important; }
.status-color-notstarted { background-color: #64748b !important; color: #ffffff !important; border-color: #475569 !important; }

.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}

.initiative-view-switch { display: inline-flex; flex-wrap: wrap; align-items: center; gap: 10px; border-radius: 12px; background: transparent; padding: 2px; }
.initiative-view-switch__label { font-size: 11px; font-weight: 700; color: #475569; white-space: nowrap; }
.initiative-view-select { appearance: none; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; padding: 4px 24px 4px 10px; font-size: 11px; font-weight: 700; color: #475569; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 6px center; background-size: 12px; transition: all 0.15s ease; }
.initiative-view-select:hover { border-color: #0f6fb7; color: #0f6fb7; }
.initiative-view-select:focus { outline: none; border-color: #0f6fb7; box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1); }
.bu-toggle-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; padding: 4px 10px; font-size: 11px; font-weight: 700; color: #475569; transition: all 0.15s ease; cursor: pointer; }
.bu-toggle-btn:hover { border-color: #0f6fb7; background: #f8fafc; }
.bu-toggle-btn--active { background: #0f6fb7; border-color: #0f6fb7; color: #ffffff; }
.bu-toggle-btn--active:hover { background: #0d5ea1; border-color: #0d5ea1; }
</style>
