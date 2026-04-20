<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';

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

const route = useRouteHelper();

const initiativeSummaryHref = (initiative) => {
    const initiativeId = Number(initiative?.id ?? 0);
    return initiativeId > 0
        ? route('program-planning.program-definition.digital-initiatives.summary.index', initiativeId)
        : null;
};

const initiativeSummaryTitle = (initiative) => {
    const label = String(initiative?.label ?? initiative?.name ?? initiative?.code ?? 'initiative').trim();
    return `Lihat capsule summary untuk ${label}`;
};



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
const selectedSource = ref('');

const organizationOptions = computed(() => {
    const orgs = new Set();
    props.items.forEach(ini => {
        if (ini.business_unit && ini.business_unit !== '-') {
            orgs.add(ini.business_unit);
        }
    });
    return Array.from(orgs).sort();
});

const sourceOptions = computed(() => {
    const sourceMap = new Map();
    props.items.forEach(ini => {
        const id = ini.source;
        let name = ini.source_name;

        // Fallback labels based on IDs provided by user
        if (!name) {
            if (id == 3) name = 'Baseline RSTI 2025-2029';
            else if (id == 4) name = 'New Initiatives 2026';
        }

        if (id !== undefined && id !== null && name) {
            if (!sourceMap.has(id)) {
                sourceMap.set(id, name);
            }
        }
    });

    // Urutan yang diinginkan sesuai instruksi: Baseline dulu, baru New Initiatives
    const desiredOrder = ['Baseline RSTI 2025-2029', 'New Initiatives 2026'];
    
    return Array.from(sourceMap.entries())
        .map(([id, name]) => ({ value: id, label: name }))
        .sort((a, b) => {
            const indexA = desiredOrder.indexOf(a.label);
            const indexB = desiredOrder.indexOf(b.label);
            if (indexA !== -1 && indexB !== -1) return indexA - indexB;
            if (indexA !== -1) return -1;
            if (indexB !== -1) return 1;
            return a.label.localeCompare(b.label);
        });
});

const displayGroups = computed(() => {
    const coeMap = new Map();
    const UNIDENTIFIED = 'CoE Not Identified';

    props.items.forEach(initiative => {
        const isDigitalInitiative = Number(initiative.tipe_initiative) === 1;
        
        // 1. Filter Organisasi
        const matchesOrg = !selectedOrganization.value || initiative.business_unit === selectedOrganization.value;
        
        // 2. Filter CoE
        const coeName = normalizeCoeName(initiative.coe_name || initiative.coe?.name);
        const matchesCoe = !selectedCoe.value || coeName === selectedCoe.value;
        
        // 3. Filter Implementation Status (Warna)
        const implStatus = normalizeStatusLabel(initiative.implementation_status);
        const matchesStatus = !selectedStatus.value || implStatus === selectedStatus.value;

        // 4. Filter Source (Berdasarkan ID source)
        const matchesSource = !selectedSource.value || initiative.source == selectedSource.value;

        if (isDigitalInitiative && matchesOrg && matchesStatus && matchesCoe && matchesSource) {
            if (!coeMap.has(coeName)) {
                coeMap.set(coeName, []);
            }
            coeMap.get(coeName).push(initiative);
        }
    });

    // 2. Ambil semua CoE yang ditemukan di data
    const foundCoeNames = Array.from(coeMap.keys());

// 3. Map ke format tampilan dan split holding/sub-holding dan sort initiatives di dalam tiap group
const coeGroups = foundCoeNames.map(name => {
    const initiatives = coeMap.get(name) || [];

    // Sorting function
    const sortByCode = (a, b) => {
        const codeA = String(a?.code ?? '');
        const codeB = String(b?.code ?? '');
        return codeA.localeCompare(codeB, undefined, { numeric: true, sensitivity: 'base' });
    };

    // Split by groub_id: 2 is Sub Holding, others (1 or null) are Holding (default grouping)
    const holdingInitiatives = initiatives.filter(i => i.groub_id !== 2).sort(sortByCode);
    const subHoldingInitiatives = initiatives.filter(i => i.groub_id === 2).sort(sortByCode);

    return {
        name,
        initiatives: initiatives.sort(sortByCode),
        holdingInitiatives,
        subHoldingInitiatives,
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


const coeLegend = computed(() => {
    const desiredOrder = [
        'IoT',
        'Advance Cloud',
        'RPA',
        'Robotics',
        'AI / Adv. Analytics',
        'CoE Not Identified',
    ];

    const stats = {};
    desiredOrder.forEach(name => {
        stats[name] = 0;
    });

    displayGroups.value.forEach((group) => {
        const name = group.name;
        if (stats.hasOwnProperty(name)) {
            stats[name] += group.total;
        } else {
            stats['CoE Not Identified'] += group.total;
        }
    });

    return desiredOrder.map((name, index) => ({
        id: index + 1,
        name: name,
        count: stats[name],
    }));
});

const totalOverallInitiatives = computed(() => {
    return displayGroups.value.reduce((sum, group) => sum + group.total, 0);
});

const getCoeColorClass = (coeName) => {
    const name = normalizeCoeName(coeName);

    if (name === 'IoT') return 'coe-color-blue';
    if (name === 'Advance Cloud') return 'coe-color-emerald';
    if (name === 'RPA') return 'coe-color-amber';
    if (name === 'Robotics') return 'coe-color-purple';
    if (name === 'AI / Adv. Analytics') return 'coe-color-rose';
    if (name === 'CoE Not Identified') return 'coe-color-none';

    return 'coe-color-none';
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
            <!-- Legend & Overall Total -->
            <div class="space-y-2.5">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div v-for="coe in coeLegend" :key="`coe-legend-${coe.id}`" class="flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-sm shadow-sm legend-swatch"
                            :class="getCoeColorClass(coe.name)"></span>
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
                            {{ coe.name }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ coe.count
                                }})</span>
                        </span>
                    </div>

                    <!-- Total Overall -->
                    <div class="flex items-center gap-1.5 border-l border-slate-300 pl-4 ml-1 dark:border-white/10">
                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">
                            Total Digital Initiatives <span class="text-slate-500 dark:text-slate-400 font-medium">({{
                                totalOverallInitiatives }})</span>
                        </span>
                    </div>
                </div>

                <!-- Status Implementation Legend -->
                <div v-if="showStatusColors"
                    class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 border-t border-slate-100 dark:border-white/5">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Implementation
                        Status (November - Desember 2025):</span>
                    <div v-for="status in statusLegend" :key="`status-legend-${status.label}`"
                        class="flex items-center gap-1.5 cursor-pointer select-none transition-opacity"
                        :class="{ 'opacity-40': selectedStatus && selectedStatus !== status.label }"
                        @click="selectedStatus = selectedStatus === status.label ? '' : status.label"
                        :title="`Filter: ${status.label}`">
                        <span class="h-3 w-3 rounded-sm shadow-sm legend-swatch" :class="status.class"></span>
                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                            {{ status.label }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{
                                status.count }})</span>
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

                    <select v-model="selectedSource" class="initiative-view-select mr-2">
                        <option value="">All Initiatives</option>
                        <option v-for="source in sourceOptions" :key="source.value" :value="source.value">{{ source.label }}</option>
                    </select>

                    <select v-model="selectedCoe" class="initiative-view-select mr-2">
                        <option value="">Semua CoE</option>
                        <option
                            v-for="coe in ['IoT', 'Advance Cloud', 'RPA', 'Robotics', 'AI / Adv. Analytics', 'CoE Not Identified']"
                            :key="coe" :value="coe">{{ coe }}</option>
                    </select>

                    <select v-model="selectedStatus" class="initiative-view-select mr-2">
                        <option value="">Semua Status</option>
                        <option v-for="st in ['DF', 'Done', 'DT 2026', 'ITSBP', 'On Review', 'SH']" :key="st"
                            :value="st">{{ st }}</option>
                    </select>

                    <button type="button" class="bu-toggle-btn" :class="{ 'bu-toggle-btn--active': showBusinessUnit }"
                        title="Tampilkan/Sembunyikan Business Unit" @click="showBusinessUnit = !showBusinessUnit">
                        <svg v-if="showBusinessUnit" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                        <span>Business Unit</span>
                    </button>

                    <button type="button" class="bu-toggle-btn" :class="{ 'bu-toggle-btn--active': showStatusColors }"
                        title="Tampilkan/Sembunyikan Warna Status Implementasi"
                        @click="showStatusColors = !showStatusColors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        <span>Status Impl.</span>
                    </button>

                    <button type="button" class="bu-toggle-btn" :class="{ 'bu-toggle-btn--active': showInitiativeCode }"
                        title="Tampilkan/Sembunyikan Code Initiative" @click="showInitiativeCode = !showInitiativeCode">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span>Code</span>
                    </button>

                    <select v-model="initiativeColumnCount" class="initiative-view-select">
                        <option v-for="option in initiativeColumnOptions" :key="option" :value="option">{{ option }}
                            Kolom</option>
                    </select>
                </div>
            </div>

            <!-- Table Matrix -->
            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <h1 class="text-center text-lg font-bold mt-4 mb-4">Digital Initiative based on Center Of Excellence
                    </h1>
                    <table class="itb-table min-w-full border-collapse"
                        :class="`itb-table--${initiativeColumnCount}-cols`">
                        <thead>
                            <tr>
                                <th rowspan="2" class="top-head top-head-left"
                                    style="width: 15%; vertical-align: middle;">CoE</th>
                                <th colspan="2" class="top-head top-head-right" style="width: 85%;">Digital Initiatives
                                </th>
                            </tr>
                            <tr>
                                <th class="top-head top-head-center border-t-0"
                                    style="width: 42.5%; background-color: #0d5ea1;">Holding</th>
                                <th class="top-head top-head-center border-t-0"
                                    style="width: 42.5%; background-color: #0d5ea1;">Sub Holding</th>
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

                                <!-- Holding Cell -->
                                <td class="initiatives-cell border-r border-[#c7d2de]">
                                    <div class="initiatives-grid" :style="{
                                        '--initiative-column-count': Math.max(1, Math.floor(initiativeColumnCount / 2)),
                                        '--row-count': buildInitiativeColumns(group.holdingInitiatives, Math.max(1, Math.floor(initiativeColumnCount / 2))).rowCount
                                    }">
                                        <component :is="initiativeSummaryHref(initiative) ? Link : 'div'"
                                            v-for="initiative in buildInitiativeColumns(group.holdingInitiatives, Math.max(1, Math.floor(initiativeColumnCount / 2))).items"
                                            :key="initiative.id" 
                                            :href="initiativeSummaryHref(initiative)"
                                            :title="initiativeSummaryTitle(initiative)"
                                            class="initiative-box group" :class="[
                                                getCoeColorClass(group.name),
                                                { 'initiative-box--no-code': !showInitiativeCode || !initiative.code },
                                                { 'initiative-box--clickable': initiativeSummaryHref(initiative) }
                                            ]">
                                            <div class="absolute top-full left-1/2 z-50 mt-1 hidden -translate-x-1/2 w-max max-w-[250px] sm:max-w-xs md:max-w-sm bg-white border border-slate-800 shadow-sm px-1.5 py-1 text-left text-[9px] italic text-slate-800 group-hover:block pointer-events-none whitespace-normal break-words dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                                                {{ initiative.description || initiative.name }}
                                            </div>
                                            <span v-if="showInitiativeCode && initiative.code"
                                                class="initiative-box__code"
                                                :class="showStatusColors ? getStatusColorClass(initiative.implementation_status) : ''">
                                                {{ initiative.code }}
                                            </span>
                                            <span class="initiative-box__name"
                                                :class="{ 'initiative-box__name--full': !showInitiativeCode || !initiative.code }">
                                                <span class="initiative-box__label-text">{{ initiative.name }}</span>
                                                <span v-if="showBusinessUnit" class="initiative-box__bu">{{
                                                    initiative.business_unit }}</span>
                                            </span>
                                        </component>
                                    </div>
                                </td>

                                <!-- Sub Holding Cell -->
                                <td class="initiatives-cell">
                                    <div class="initiatives-grid" :style="{
                                        '--initiative-column-count': Math.max(1, Math.ceil(initiativeColumnCount / 2)),
                                        '--row-count': buildInitiativeColumns(group.subHoldingInitiatives, Math.max(1, Math.ceil(initiativeColumnCount / 2))).rowCount
                                    }">
                                        <component :is="initiativeSummaryHref(initiative) ? Link : 'div'"
                                            v-for="initiative in buildInitiativeColumns(group.subHoldingInitiatives, Math.max(1, Math.ceil(initiativeColumnCount / 2))).items"
                                            :key="initiative.id" 
                                            :href="initiativeSummaryHref(initiative)"
                                            :title="initiativeSummaryTitle(initiative)"
                                            class="initiative-box group" :class="[
                                                getCoeColorClass(group.name),
                                                { 'initiative-box--no-code': !showInitiativeCode || !initiative.code },
                                                { 'initiative-box--clickable': initiativeSummaryHref(initiative) }
                                            ]">
                                            <div class="absolute top-full left-1/2 z-50 mt-1 hidden -translate-x-1/2 w-max max-w-[250px] sm:max-w-xs md:max-w-sm bg-white border border-slate-800 shadow-sm px-1.5 py-1 text-left text-[9px] italic text-slate-800 group-hover:block pointer-events-none whitespace-normal break-words dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                                                {{ initiative.description || initiative.name }}
                                            </div>
                                            <span v-if="showInitiativeCode && initiative.code"
                                                class="initiative-box__code"
                                                :class="showStatusColors ? getStatusColorClass(initiative.implementation_status) : ''">
                                                {{ initiative.code }}
                                            </span>
                                            <span class="initiative-box__name"
                                                :class="{ 'initiative-box__name--full': !showInitiativeCode || !initiative.code }">
                                                <span class="initiative-box__label-text">{{ initiative.name }}</span>
                                                <span v-if="showBusinessUnit" class="initiative-box__bu">{{
                                                    initiative.business_unit }}</span>
                                            </span>
                                        </component>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section v-else
            class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Tidak ada data inisiatif untuk kriteria
                ini.</p>
        </section>
    </div>
</template>

<style scoped>
.itb-table {
    background: #ffffff;
    width: 100%;
    border-collapse: collapse;
}

.itb-table th,
.itb-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.top-head {
    padding: 10px 12px;
    background: #0f6fb7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    text-align: center;
    letter-spacing: 0.05em;
}

.primary-cell {
    vertical-align: middle !important;
    min-width: 120px;
    transition: all 0.2s;
}

.primary-cell__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    text-align: center;
    font-weight: 700;
    color: #1e293b;
}

.primary-label-wrapper {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}

.count-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.1);
    font-size: 9px;
    font-weight: 800;
    color: inherit;
    flex-shrink: 0;
}

.initiatives-cell {
    padding: 8px;
    background: #f8fafc;
}

.initiatives-grid {
    display: grid;
    grid-template-columns: repeat(var(--initiative-column-count, 6), minmax(0, 1fr));
    grid-auto-flow: column;
    grid-template-rows: repeat(var(--row-count, 1), minmax(min-content, 1fr));
    gap: 8px;
    align-items: stretch;
}

.initiative-box {
    position: relative;
    display: grid;
    grid-template-columns: 28px minmax(0, 1fr);
    min-height: 24px;
    width: 100%;
    align-items: stretch;
    border: 1px solid #374151;
    background: #ffffff;
    font-size: 9px;
    font-weight: 500;
    line-height: 1.1;
    color: #1f2937;
}

.initiative-box--clickable {
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.initiative-box--clickable:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 10;
}

/* Ensure Link doesn't have default <a> styles */
a.initiative-box {
    text-decoration: none;
    color: inherit;
}

.initiative-box--no-code {
    grid-template-columns: 1fr !important;
}

.initiative-box__code {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #374151;
    padding: 2px 4px;
    font-weight: 700;
    min-width: 28px;
    background: rgba(0, 0, 0, 0.03);
}

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

.initiative-box__name {
    display: flex;
    flex-direction: column;
    padding: 2px 5px;
    word-break: break-word;
    justify-content: center;
}

.initiative-box__bu {
    font-size: 7.5px;
    font-weight: 700;
    font-style: italic;
    opacity: 0.7;
}

.initiative-box__name--full {
    grid-column: 1 / -1;
}



.initiative-view-switch {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    border-radius: 12px;
    background: transparent;
    padding: 2px;
}

.initiative-view-select {
    appearance: none;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 24px 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
    transition: all 0.15s ease;
}

.initiative-view-select:hover {
    border-color: #0f6fb7;
    color: #0f6fb7;
}

.initiative-view-select:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.bu-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    transition: all 0.15s ease;
    cursor: pointer;
}

.bu-toggle-btn:hover {
    border-color: #0f6fb7;
    background: #f8fafc;
}

.bu-toggle-btn--active {
    background: #0f6fb7;
    border-color: #0f6fb7;
    color: #ffffff;
}

.bu-toggle-btn--active:hover {
    background: #0d5ea1;
    border-color: #0d5ea1;
}

.status-color-df {
    background-color: #0d9488 !important;
    color: #ffffff !important;
    border-color: #0f766e !important;
}

.status-color-done {
    background-color: #65a30d !important;
    color: #ffffff !important;
    border-color: #4d7c0f !important;
}

.status-color-dt2026 {
    background-color: #ea580c !important;
    color: #ffffff !important;
    border-color: #c2410c !important;
}

.status-color-itsbp {
    background-color: #06b6d4 !important;
    color: #ffffff !important;
    border-color: #0891b2 !important;
}

.status-color-onreview {
    background-color: #ca8a04 !important;
    color: #ffffff !important;
    border-color: #a16207 !important;
}

.status-color-sh {
    background-color: #ef4444 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

/* COE Color Classes - High Contrast & Deep Colors */
.coe-color-blue {
    background-color: #eff6ff;
    border-color: #1d4ed8 !important;
}

.coe-color-emerald {
    background-color: #ecfdf5;
    border-color: #047857 !important;
}

.coe-color-amber {
    background-color: #fffbeb;
    border-color: #b45309 !important;
}

.coe-color-purple {
    background-color: #faf5ff;
    border-color: #6d28d9 !important;
}

.coe-color-rose {
    background-color: #fff1f2;
    border-color: #be123c !important;
}

.coe-color-indigo {
    background-color: #eef2ff;
    border-color: #4338ca !important;
}

.coe-color-none {
    background-color: #ffffff;
    border-color: #374151 !important;
}

/* Legend Swatches - Solid Deep Colors */
.legend-swatch.coe-color-blue {
    background-color: #1d4ed8 !important;
}

.legend-swatch.coe-color-emerald {
    background-color: #047857 !important;
}

.legend-swatch.coe-color-amber {
    background-color: #b45309 !important;
}

.legend-swatch.coe-color-purple {
    background-color: #6d28d9 !important;
}

.legend-swatch.coe-color-rose {
    background-color: #be123c !important;
}

.legend-swatch.coe-color-indigo {
    background-color: #4338ca !important;
}

.legend-swatch.coe-color-none {
    background-color: #374151 !important;
}

/* Dark mode overrides for CoE Colors */
:deep(.dark) .coe-color-blue {
    background-color: rgba(59, 130, 246, 0.2);
}

:deep(.dark) .coe-color-emerald {
    background-color: rgba(16, 185, 129, 0.2);
}

:deep(.dark) .coe-color-amber {
    background-color: rgba(245, 158, 11, 0.2);
}

:deep(.dark) .coe-color-purple {
    background-color: rgba(168, 85, 247, 0.2);
}

:deep(.dark) .coe-color-rose {
    background-color: rgba(244, 63, 94, 0.2);
}

:deep(.dark) .coe-color-indigo {
    background-color: rgba(99, 102, 241, 0.2);
}

.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}
</style>