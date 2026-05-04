<template>
    <article class="overflow-hidden rounded-2xl border border-slate-900 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Filter Section -->
        <div class="border-b border-slate-900 px-5 py-4 dark:border-white/10">
            <!-- Legend Status -->
            <div class="mb-3 flex flex-wrap items-center gap-x-4 gap-y-2 dark:border-white/5">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Select Status Summary:</span>
                <div
                    v-for="status in statusLegend"
                    :key="`status-legend-${status.label}`"
                    class="flex cursor-pointer items-center gap-1.5 select-none transition-opacity"
                    :class="{ 'opacity-40': selectedStatus && selectedStatus !== status.label }"
                    :title="`Filter: ${status.label}`"
                    @click="toggleStatusFilter(status.label)"
                >
                    <span
                        class="h-3 w-3 rounded-sm shadow-sm legend-swatch"
                        :class="status.class"
                    ></span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        {{ status.label }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ status.count }})</span>
                    </span>
                </div>
                <div v-if="totalCount > 0" class="flex items-center gap-1.5 border-l border-slate-900 pl-4 ml-1 dark:border-white/10">
                    <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">
                        Total <span class="text-slate-500 dark:text-slate-400 font-medium">({{ totalCount }})</span>
                    </span>
                </div>
            </div>

            <!-- Dropdown Filters Row 1 -->
            <div class="review-filter-switch mb-3">
                <label for="bb-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">IT Building Blocks</label>
                <select
                    id="bb-filter"
                    v-model="selectedBuildingBlock"
                    class="review-filter-select dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All IT Building Blocks</option>
                    <option
                        v-for="bb in buildingBlockOptions"
                        :key="`bb-filter-${bb}`"
                        :value="bb"
                    >
                        {{ bb }}
                    </option>
                </select>

                <label for="period-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">Periode</label>
                <select
                    id="period-filter"
                    v-model="selectedPeriod"
                    class="review-filter-select dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All Month</option>
                    <option
                        v-for="period in periodOptions"
                        :key="`period-filter-${period}`"
                        :value="period"
                    >
                        {{ period }}
                    </option>
                </select>

                <label for="owner-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">Project Owner</label>
                <select
                    id="owner-filter"
                    v-model="selectedProjectOwner"
                    class="review-filter-select dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All Project Owner</option>
                    <option
                        v-for="owner in ownerOptions"
                        :key="`owner-filter-${owner}`"
                        :value="owner"
                    >
                        {{ owner }}
                    </option>
                </select>

                <label for="leader-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">Project Leader</label>
                <select
                    id="leader-filter"
                    v-model="selectedProjectLeader"
                    class="review-filter-select dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All Project Leader</option>
                    <option
                        v-for="leader in leaderOptions"
                        :key="`leader-filter-${leader}`"
                        :value="leader"
                    >
                        {{ leader }}
                    </option>
                </select>
            </div>

            <!-- Dropdown Filters Row 2 -->
            <div class="review-filter-switch">
                <label for="initiative-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">IT Initiatives</label>
                <select
                    id="initiative-filter"
                    v-model="selectedInitiativeId"
                    class="review-filter-select review-filter-select--wide dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200 mr-4"
                >
                    <option value="all">All IT Initiatives</option>
                    <option
                        v-for="initiative in initiativeOptions"
                        :key="`init-filter-${initiative.id}`"
                        :value="String(initiative.id)"
                    >
                        {{ initiative.name }}
                    </option>
                </select>

                <label for="sort-duration" class="text-xs font-medium text-slate-700 dark:text-slate-200">Sort Duration</label>
                <select
                    id="sort-duration"
                    v-model="sortDuration"
                    class="review-filter-select dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="none">Default (No)</option>
                    <option value="asc">Duration (Shortest)</option>
                    <option value="desc">Duration (Longest)</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-full border-collapse text-xs">
                <thead class="bg-slate-100 dark:bg-white/5">
                    <tr>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">IT BUILDING BLOCKS</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">NO</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">IT INITIATIVES</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">PROJECT OWNER</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">PROJECT LEADER</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">PC BASELINE</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">PC APPROVED</th>
                        <th class="border border-slate-900 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">APPROVAL DURATION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in groupedItems"
                        :key="`review-dashboard-row-${row.initiative_id}`"
                        class="hover:bg-slate-50 dark:hover:bg-white/5 transition"
                    >
                        <td
                            v-if="row.showTypeCell"
                            :rowspan="row.typeRowspan"
                            class="primary-cell border border-slate-900 px-2 py-2 text-center dark:border-white/20"
                            :class="getBuildingBlockColorClass(row.building_block_type)"
                        >
                            <div class="primary-cell__content">
                                <span class="text-[10px] font-bold leading-tight">
                                    {{ row.building_block_type || '-' }}
                                </span>
                                <span class="coe-count-capsule">
                                    {{ row.typeRowspan }}
                                </span>
                            </div>
                        </td>
                        <td class="border border-slate-900 px-2 py-2 text-center font-medium text-slate-800 dark:border-white/20 dark:text-slate-200">
                             <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold text-white shadow-sm"
                                :class="statusCapsuleClass(row.latest_review_status)"
                            >
                                {{ row.no }}
                            </span>
                        </td>
                        <td class="border border-slate-900 px-3 py-2 text-slate-800 dark:border-white/20 dark:text-slate-200">
                            <p class="font-medium whitespace-pre-line break-words leading-snug">
                                {{ row.initiative_name }}
                            </p>
                        </td>
                        <td class="border border-slate-900 px-3 py-2 text-center text-[10px] font-semibold text-slate-600 dark:border-white/20 dark:text-slate-300">
                            {{ row.project_owner || '-' }}
                        </td>
                        <td class="border border-slate-900 px-3 py-2 text-center text-[10px] font-semibold text-slate-600 dark:border-white/20 dark:text-slate-300">
                            {{ row.project_leader || '-' }}
                        </td>
                        <td class="border border-slate-900 px-3 py-2 text-center text-[10px] text-slate-600 dark:border-white/20 dark:text-slate-300">
                            {{ row.baseline_date || '-' }}
                        </td>
                        <td class="border border-slate-900 px-3 py-2 text-center text-[10px] text-slate-600 dark:border-white/20 dark:text-slate-300">
                            {{ row.approve_date || '-' }}
                        </td>
                        <td class="border border-slate-900 px-3 py-2 text-center font-bold text-slate-900 dark:border-white/20 dark:text-white">
                            {{ row.process_month || '-' }}
                        </td>
                    </tr>
                    <tr v-if="groupedItems.length === 0">
                        <td colspan="8" class="border border-slate-900 px-3 py-6 text-center text-xs text-slate-500 dark:border-white/20 dark:text-slate-400">
                            Belum ada data initiatives timeline.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

// Filter States
const selectedStatus = ref('');
const selectedBuildingBlock = ref('all');
const selectedPeriod = ref('all');
const selectedProjectOwner = ref('all');
const selectedProjectLeader = ref('all');
const selectedInitiativeId = ref('all');
const sortDuration = ref('none'); // 'none', 'asc', 'desc'

const statusDesiredOrder = ['On Track', 'At Risk', 'Not Signed', 'Not Started', 'Done'];

// Dropdown Options
const buildingBlockOptions = computed(() => {
    return Array.from(new Set(props.items.map(i => i.building_block_type).filter(Boolean))).sort();
});

const periodOptions = computed(() => {
    return Array.from(new Set(props.items.map(i => i.latest_review_period).filter(Boolean))).sort();
});

const ownerOptions = computed(() => {
    return Array.from(new Set(props.items.map(i => i.project_owner).filter(o => o && o !== '-'))).sort();
});

const leaderOptions = computed(() => {
    return Array.from(new Set(props.items.map(i => i.project_leader).filter(l => l && l !== '-'))).sort();
});

const initiativeOptions = computed(() => {
    const map = new Map();
    props.items.forEach(i => {
        if (!map.has(i.initiative_id)) {
            map.set(i.initiative_id, { id: i.initiative_id, name: i.initiative_name });
        }
    });
    return Array.from(map.values()).sort((a, b) => a.name.localeCompare(b.name));
});

const normalizeStatus = (value) => String(value ?? '').trim().toLowerCase();

// Filter Logic
const baseFilteredItems = computed(() => {
    return props.items.filter(item => {
        const matchesBB = selectedBuildingBlock.value === 'all' || item.building_block_type === selectedBuildingBlock.value;
        const matchesPeriod = selectedPeriod.value === 'all' || item.latest_review_period === selectedPeriod.value;
        const matchesOwner = selectedProjectOwner.value === 'all' || item.project_owner === selectedProjectOwner.value;
        const matchesLeader = selectedProjectLeader.value === 'all' || item.project_leader === selectedProjectLeader.value;
        const matchesInitiative = selectedInitiativeId.value === 'all' || String(item.initiative_id) === String(selectedInitiativeId.value);
        
        return matchesBB && matchesPeriod && matchesOwner && matchesLeader && matchesInitiative;
    });
});

const filteredItems = computed(() => {
    return baseFilteredItems.value.filter(item => {
        return !selectedStatus.value || normalizeStatus(item.latest_review_status) === normalizeStatus(selectedStatus.value);
    });
});

// Legend Logic
const statusLegend = computed(() => {
    const counts = Object.fromEntries(statusDesiredOrder.map(s => [s, 0]));
    baseFilteredItems.value.forEach(item => {
        const rawStatus = item.latest_review_status || '';
        const matched = statusDesiredOrder.find(s => normalizeStatus(s) === normalizeStatus(rawStatus));
        if (matched) counts[matched]++;
    });
    
    return statusDesiredOrder.map(label => ({
        label,
        class: statusCapsuleClass(label),
        count: counts[label]
    }));
});

const totalCount = computed(() => baseFilteredItems.value.length);

const toggleStatusFilter = (status) => {
    selectedStatus.value = selectedStatus.value === status ? '' : status;
};

const groupedItems = computed(() => {
    const rows = [];
    let index = 0;

    // Sorting Logic
    const sorted = [...filteredItems.value].sort((a, b) => {
        // If sortDuration is set, prioritize it
        if (sortDuration.value !== 'none') {
            const valA = a.process_month_value ?? -1;
            const valB = b.process_month_value ?? -1;
            if (valA !== valB) {
                return sortDuration.value === 'asc' ? valA - valB : valB - valA;
            }
        }

        // Default sort by 'no'
        const noA = Number(String(a.no).replace(/[^0-9]/g, '')) || 0;
        const noB = Number(String(b.no).replace(/[^0-9]/g, '')) || 0;
        
        if (noA !== noB) return noA - noB;
        
        // Secondary sort by building block
        const typeA = String(a.building_block_type || '').toLowerCase();
        const typeB = String(b.building_block_type || '').toLowerCase();
        return typeA.localeCompare(typeB);
    });

    while (index < sorted.length) {
        const currentType = sorted[index]?.building_block_type ?? '';
        let groupSize = 1;

        while (
            index + groupSize < sorted.length
            && (sorted[index + groupSize]?.building_block_type ?? '') === currentType
        ) {
            groupSize += 1;
        }

        for (let offset = 0; offset < groupSize; offset += 1) {
            rows.push({
                ...sorted[index + offset],
                showTypeCell: offset === 0,
                typeRowspan: groupSize,
            });
        }

        index += groupSize;
    }

    return rows.map((row, i) => ({
        ...row,
        sequentialNo: i + 1
    }));
});

const statusCapsuleClass = (status) => {
    const normalized = normalizeStatus(status);
    if (normalized === 'on track') return 'status-color-ontrack';
    if (normalized === 'at risk') return 'status-color-atrisk';
    if (normalized === 'not signed') return 'status-color-notsigned';
    if (normalized === 'not started') return 'status-color-notstarted';
    if (normalized === 'done') return 'status-color-done';
    return 'status-color-other';
};

const getBuildingBlockColorClass = (name) => {
    const upper = String(name ?? '').toUpperCase();
    if (upper.includes('INTERFACE') || upper.includes('UX')) return 'coe-color-blue';
    if (upper.includes('INTEGRATION') || upper.includes('AUTOMATION')) return 'coe-color-emerald';
    if (upper.includes('BUSINESS APPLICATION')) return 'coe-color-amber';
    if (upper.includes('INFRASTRUCTURE')) return 'coe-color-purple';
    if (upper.includes('DATA AND ANALYTICS')) return 'coe-color-neutral';
    if (upper.includes('CYBERSECURITY')) return 'coe-color-rose';
    if (upper.includes('PEOPLE')) return 'coe-color-slate';
    if (upper.includes('ARCHITECTURE')) return 'coe-color-indigo';

    return 'coe-color-none';
};
</script>

<style scoped>
.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}

.review-filter-switch {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    border-radius: 12px;
    background: transparent;
    padding: 2px;
}

.review-filter-select {
    appearance: none;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background-color: #ffffff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
    color: #475569;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 24px 4px 10px;
    transition: all 0.15s ease;
}

.review-filter-select:hover {
    border-color: #0f6fb7;
    color: #0f6fb7;
}

.review-filter-select:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.review-filter-select--wide {
    width: 18rem;
    max-width: 100%;
}

.status-color-ontrack {
    background-color: #10b981 !important;
    color: #ffffff !important;
    border: 1px solid #059669 !important;
}

.status-color-atrisk {
    background-color: #f59e0b !important;
    color: #ffffff !important;
    border: 1px solid #d97706 !important;
}

.status-color-notsigned {
    background-color: #f43f5e !important;
    color: #ffffff !important;
    border: 1px solid #e11d48 !important;
}

.status-color-notstarted {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
    border: 1px solid #2563eb !important;
}

.status-color-done {
    background-color: #64748b !important;
    color: #ffffff !important;
    border: 1px solid #475569 !important;
}

.status-color-other {
    background-color: #64748b !important;
    color: #ffffff !important;
    border: 1px solid #475569 !important;
}

.primary-cell {
    vertical-align: middle !important;
    min-width: 120px;
    transition: all 0.2s;
}

.primary-cell__content {
    display: flex;
    flex-direction: row;
    gap: 6px;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 6px 8px;
    text-align: center;
    font-weight: 700;
    color: #1e293b;
}

.coe-count-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    flex-shrink: 0;
    border-radius: 999px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    background: rgba(0, 0, 0, 0.1);
    padding: 0 4px;
    color: inherit;
    font-size: 9px;
    font-weight: 800;
    line-height: 1;
}

.coe-color-blue { background-color: #ffffff; color: #1e3a8a; }
.coe-color-emerald { background-color: #ffffff; color: #065f46; }
.coe-color-amber { background-color: #ffffff; color: #92400e; }
.coe-color-purple { background-color: #ffffff; color: #5b21b6; }
.coe-color-none { background-color: #ffffff; color: #334155; }
.coe-color-neutral { background-color: #ffffff; color: #334155; }
.coe-color-rose { background-color: #ffffff; color: #9f1239; }
.coe-color-slate { background-color: #ffffff; color: #1e293b; }
.coe-color-indigo { background-color: #ffffff; color: #312e81; }
</style>
