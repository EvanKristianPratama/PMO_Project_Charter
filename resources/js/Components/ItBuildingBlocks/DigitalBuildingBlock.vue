<script setup>
import { computed, ref } from 'vue';

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
const selectedOrganization = ref('');
const selectedCoe = ref('');
const selectedStatus = ref('');

const organizationOptions = computed(() => {
    const orgs = new Set();
    props.items.forEach(ini => {
        if (ini.business_unit && ini.business_unit !== '-') {
            orgs.add(ini.business_unit);
        }
    });
    return Array.from(orgs).sort();
});

const displayGroups = computed(() => {
    const coeMap = new Map();
    const UNIDENTIFIED = 'CoE Not Identified';

    // 1. Grouping dan Filter
    props.items.forEach(initiative => {
        const isDigitalInitiative = Number(initiative.tipe_initiative) === 1;
        const matchesOrg = !selectedOrganization.value || initiative.business_unit === selectedOrganization.value;
        const coeName = normalizeCoeName(initiative.coe_name || initiative.coe?.name);
        const matchesCoe = !selectedCoe.value || coeName === selectedCoe.value;
        const implStatus = normalizeStatusLabel(initiative.implementation_status);
        const matchesStatus = !selectedStatus.value || implStatus === selectedStatus.value;

        if (isDigitalInitiative && matchesOrg && matchesStatus && matchesCoe) {
            if (!coeMap.has(coeName)) {
                coeMap.set(coeName, []);
            }
            coeMap.get(coeName).push(initiative);
        }
    });

    // 2. Ambil semua CoE yang ditemukan di data
    const foundCoeNames = Array.from(coeMap.keys());

    // 3. Map ke format tampilan dan sort initiatives di dalam tiap group
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

    // 4. Sort urutan group (baris CoE) berdasarkan kode initiative terkecil di dalamnya
    coeGroups.sort((a, b) => {
        if (a.name === UNIDENTIFIED && b.name !== UNIDENTIFIED) return 1;
        if (b.name === UNIDENTIFIED && a.name !== UNIDENTIFIED) return -1;

        const codeA = a.initiatives.length > 0 ? String(a.initiatives[0]?.code ?? '') : '';
        const codeB = b.initiatives.length > 0 ? String(b.initiatives[0]?.code ?? '') : '';
        
        // Handle empty codes to be placed last or evaluated properly
        if (codeA === '' && codeB !== '') return 1;
        if (codeB === '' && codeA !== '') return -1;

        return codeA.localeCompare(codeB, undefined, { numeric: true, sensitivity: 'base' });
    });

    return coeGroups;
});

const buildInitiativeColumns = (initiatives = [], columnCount = initiativeColumnCount.value) => {
    if (!initiatives.length) return { items: [], rowCount: 0 };
    return { 
        items: initiatives, // Sudah diurutkan di displayGroups
        rowCount: Math.ceil(initiatives.length / Number(columnCount)) 
    };
};


const getStatusColorClass = (status) => {
    const s = normalizeStatusLabel(status);
    if (s === 'DF') return 'status-color-df';
    if (s === 'Done') return 'status-color-done';
    if (s === 'DT 2026') return 'status-color-dt2026';
    if (s === 'ITSBP') return 'status-color-itsbp';
    if (s === 'On Review') return 'status-color-onreview';
    if (s === 'SH') return 'status-color-sh';
    return '';
};

const statusDesiredOrder = ['DF', 'Done', 'DT 2026', 'ITSBP', 'On Review', 'SH'];

const statusLegend = computed(() => {
    const stats = {};
    statusDesiredOrder.forEach(label => {
        stats[label] = 0;
    });

    displayGroups.value.forEach((group) => {
        group.initiatives.forEach((initiative) => {
            const label = normalizeStatusLabel(initiative.implementation_status);
            if (label && stats.hasOwnProperty(label)) {
                stats[label]++;
            }
        });
    });

    return statusDesiredOrder.map((label) => ({
        label,
        class: getStatusColorClass(label),
        count: stats[label],
    }));
});
</script>

<template>
    <div class="space-y-4">
        <div v-if="displayGroups.length > 0" class="space-y-4">
            <!-- Status Implementation Legend -->
            <div class="space-y-2.5">
                <div
                    v-if="showStatusColors"
                    class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 border-slate-100 dark:border-white/5"
                >
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Implementation Status (November - Desember 2025):</span>
                    <div
                        v-for="status in statusLegend"
                        :key="`status-legend-${status.label}`"
                        class="flex items-center gap-1.5 cursor-pointer select-none transition-opacity"
                        :class="{ 'opacity-40': selectedStatus && selectedStatus !== status.label }"
                        @click="selectedStatus = selectedStatus === status.label ? '' : status.label"
                        :title="`Filter: ${status.label}`"
                    >
                        <span
                            class="h-3 w-3 rounded-sm shadow-sm"
                            :class="status.class"
                        ></span>
                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                            {{ status.label }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ status.count }})</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="flex items-center justify-start">
                <div class="initiative-view-switch">
                    <select v-model="selectedOrganization" class="initiative-view-select mr-2">
                        <option value="">Semua Organisasi</option>
                        <option v-for="org in organizationOptions" :key="org" :value="org">{{ org }}</option>
                    </select>

                    <select v-model="selectedCoe" class="initiative-view-select mr-2">
                        <option value="">Semua CoE</option>
                        <option v-for="coe in ['IoT', 'Advance Cloud', 'RPA', 'Robotics', 'AI / Adv. Analytics', 'CoE Not Identified']" :key="coe" :value="coe">{{ coe }}</option>
                    </select>

                    <select v-model="selectedStatus" class="initiative-view-select mr-2">
                        <option value="">Semua Status</option>
                        <option v-for="st in ['DF', 'Done', 'DT 2026', 'ITSBP', 'On Review', 'SH']" :key="st" :value="st">{{ st }}</option>
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

            <!-- Table Matrix -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <h1 class="text-center text-lg font-bold mt-4 mb-4">Digital Initiative based on Center Of Excellence</h1>
                    <table class="itb-table min-w-full border-collapse" :class="`itb-table--${initiativeColumnCount}-cols`">
                        <thead>
                            <tr>
                                <th class="top-head top-head-left" style="width: 15%;">CoE</th>
                                <th class="top-head top-head-right" style="width: 85%;">Digital Initiatives</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="group in displayGroups" :key="group.name">
                                <td class="primary-cell">
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
                                                { 'initiative-box--no-code': !showInitiativeCode || !initiative.code }
                                            ]"
                                        >
                                            <div class="absolute top-full left-1/2 z-50 mt-1 hidden -translate-x-1/2 w-max max-w-sm bg-white border border-slate-800 shadow-sm px-1.5 py-1 text-[9px] italic group-hover:block dark:bg-slate-800">
                                                {{ initiative.name }}
                                            </div>
                                            <span v-if="showInitiativeCode && initiative.code" class="initiative-box__code" :class="showStatusColors ? getStatusColorClass(initiative.implementation_status) : ''">
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
        </div>

        <section v-else class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tidak ada data inisiatif untuk kriteria ini.</p>
        </section>
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



.initiative-view-switch { display: inline-flex; flex-wrap: wrap; align-items: center; gap: 10px; border-radius: 12px; background: transparent; padding: 2px; }
.initiative-view-switch__label { font-size: 11px; font-weight: 700; color: #475569; white-space: nowrap; }
.initiative-view-select { appearance: none; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; padding: 4px 24px 4px 10px; font-size: 11px; font-weight: 700; color: #475569; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 6px center; background-size: 12px; transition: all 0.15s ease; }
.initiative-view-select:hover { border-color: #0f6fb7; color: #0f6fb7; }
.initiative-view-select:focus { outline: none; border-color: #0f6fb7; box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1); }
.bu-toggle-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; padding: 4px 10px; font-size: 11px; font-weight: 700; color: #475569; transition: all 0.15s ease; cursor: pointer; }
.bu-toggle-btn:hover { border-color: #0f6fb7; background: #f8fafc; }
.bu-toggle-btn--active { background: #0f6fb7; border-color: #0f6fb7; color: #ffffff; }
.bu-toggle-btn--active:hover { background: #0d5ea1; border-color: #0d5ea1; }

.status-color-df { background-color: #0d9488 !important; color: #ffffff !important; border-color: #0f766e !important; }
.status-color-done { background-color: #65a30d !important; color: #ffffff !important; border-color: #4d7c0f !important; }
.status-color-dt2026 { background-color: #ea580c !important; color: #ffffff !important; border-color: #c2410c !important; }
.status-color-itsbp { background-color: #06b6d4 !important; color: #ffffff !important; border-color: #0891b2 !important; }
.status-color-onreview { background-color: #ca8a04 !important; color: #ffffff !important; border-color: #a16207 !important; }
.status-color-sh { background-color: #ef4444 !important; color: #ffffff !important; border-color: #dc2626 !important; }
</style>