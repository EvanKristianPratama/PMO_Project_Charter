<template>
    <div v-if="initiative" class="rounded-lg border overflow-hidden" :class="[
        borderColorClass,
        backgroundColorClass,
    ]">
        <!-- Initiative Details Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-fixed border-collapse text-xs">
                <colgroup>
                    <col class="w-[6%]">
                    <col class="w-[22%]">
                    <col class="w-[44%]">
                    <col class="w-[12%]">
                    <col class="w-[6%]">
                    <col class="w-[10%]">
                </colgroup>
                <thead>
                    <tr :class="theadBackgroundClass">
                        <th :class="theadCellClass">Code</th>
                        <th :class="theadCellClass">Name</th>
                        <th :class="theadCellClass">Description</th>
                        <th :class="theadCellClass">{{ statusLabel }}</th>
                        <th :class="theadCellClass">Type</th>
                        <th :class="theadCellClass">Business Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :class="tbodyRowHoverClass">
                        <td :class="tbodyCellClass">{{ initiative.code || '-' }}</td>
                        <td :class="tbodyCellClass">{{ initiative.name || '-' }}</td>
                        <td :class="tbodyCellClass">{{ initiative.description || '-' }}</td>
                        <td :class="tbodyCellClass">
                            <span class="inline-block rounded px-2 py-0.5 text-[10px] font-bold shadow-sm whitespace-nowrap" :style="getStatusBadgeStyle(initiative.status)">
                                {{ getStatusLabel(initiative.status) }}
                            </span>
                        </td>
                        <td :class="tbodyCellClass">{{ formatInitiativeType(initiative.tipe_initiative) }}</td>
                        <td :class="tbodyCellClass">{{ initiative.organization?.name || initiative.business_unit_name ||
                            initiative.business_unit || '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Relations Section -->
        <div :class="['border-t', borderTopColorClass]">
            <div
                :class="['px-3 py-2', relationsHeaderBackgroundClass, 'text-[11px] font-semibold uppercase tracking-wide', relationsHeaderTextClass]">
                {{ relationsTitle }}
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-collapse text-[11px]">
                    <colgroup>
                        <col class="w-[10%]">
                        <col class="w-[6%]">
                        <col class="w-[10%]">
                        <col class="w-[40%]">
                        <col class="w-[5%]">
                    </colgroup>
                    <thead>
                        <tr :class="theadBackgroundClass">
                            <th :class="theadCellClass">{{ columnALabel }}</th>
                            <th :class="theadCellClass">Tipe Relasi</th>
                            <th :class="theadCellClass">{{ columnBLabel }}</th>
                            <th :class="theadCellClass">Justifikasi</th>
                            <th :class="theadCellClass + ' text-center'">Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!relations.length" :class="tbodyRowHoverClass">
                            <td :class="['border px-2 py-2 text-center', borderColorClass, relationTextColorClass]">
                                Belum ada relasi
                            </td>
                        </tr>
                        <tr v-for="relation in relations" :key="relation.id" :class="tbodyRowHoverClass">
                            <td :class="tbodyCellClass">{{ relation.predecessorLabel }}</td>
                            <td :class="tbodyCellClass">{{ formatTipeRelasi(relation.type_relation) }}</td>
                            <td :class="tbodyCellClass">{{ relation.successorLabel }}</td>
                            <td :class="tbodyCellClass">{{ relation.justifikasi }}</td>
                            <td :class="tbodyCellClass + ' text-center'">{{ relation.modelRelasi }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    initiative: {
        type: Object,
        default: null,
    },
    relations: {
        type: Array,
        default: () => [],
    },
    variant: {
        type: String,
        enum: ['emerald', 'blue'],
        default: 'emerald',
    },
    statusLabel: {
        type: String,
        default: 'Source',
    },
    relationsTitle: {
        type: String,
        default: 'Initiatives Relations Existing',
    },
    columnALabel: {
        type: String,
        default: 'Initiative A',
    },
    columnBLabel: {
        type: String,
        default: 'Initiative B',
    },
});

const borderColorClass = computed(() => {
    return props.variant === 'emerald'
        ? 'border-emerald-200 dark:border-emerald-900/30'
        : 'border-blue-200 dark:border-blue-900/30';
});

const backgroundColorClass = computed(() => {
    return props.variant === 'emerald'
        ? 'bg-emerald-50 dark:bg-emerald-900/10'
        : 'bg-blue-50 dark:bg-blue-900/10';
});

const theadBackgroundClass = computed(() => {
    return props.variant === 'emerald'
        ? 'bg-emerald-100/70 dark:bg-emerald-900/30'
        : 'bg-blue-100/70 dark:bg-blue-900/30';
});

const theadCellClass = computed(() => {
    const borderClass = props.variant === 'emerald'
        ? 'border-emerald-200 dark:border-emerald-900/30'
        : 'border-blue-200 dark:border-blue-900/30';
    const textClass = props.variant === 'emerald'
        ? 'text-emerald-900 dark:text-emerald-200'
        : 'text-blue-900 dark:text-blue-200';
    return `border ${borderClass} px-2 py-1 text-left font-semibold ${textClass} whitespace-nowrap`;
});

const tbodyCellClass = computed(() => {
    const borderClass = props.variant === 'emerald'
        ? 'border-emerald-200 dark:border-emerald-900/30'
        : 'border-blue-200 dark:border-blue-900/30';
    const textClass = props.variant === 'emerald'
        ? 'text-emerald-900 dark:text-emerald-100'
        : 'text-blue-900 dark:text-blue-100';
    return `border ${borderClass} px-2 py-1 ${textClass} whitespace-normal break-words`;
});

const tbodyRowHoverClass = computed(() => {
    return props.variant === 'emerald'
        ? 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/15'
        : 'hover:bg-blue-50/50 dark:hover:bg-blue-900/15';
});

const borderTopColorClass = computed(() => {
    return props.variant === 'emerald'
        ? 'border-emerald-200 dark:border-emerald-900/30'
        : 'border-blue-200 dark:border-blue-900/30';
});

const relationsHeaderBackgroundClass = computed(() => {
    return props.variant === 'emerald'
        ? 'bg-emerald-100/70 dark:bg-emerald-900/30'
        : 'bg-blue-100/70 dark:bg-blue-900/30';
});

const relationsHeaderTextClass = computed(() => {
    return props.variant === 'emerald'
        ? 'text-emerald-900 dark:text-emerald-200'
        : 'text-blue-900 dark:text-blue-200';
});

const relationTextColorClass = computed(() => {
    return props.variant === 'emerald'
        ? 'text-emerald-700 dark:text-emerald-200'
        : 'text-blue-700 dark:text-blue-200';
});

const IT_STATUS_PALETTE = {
    'On Track': { backgroundColor: '#ecfdf5', color: '#064e3b', borderColor: '#059669' },
    'Done': { backgroundColor: '#eff6ff', color: '#1e3a8a', borderColor: '#2563eb' },
    'At Risk': { backgroundColor: '#fffbeb', color: '#78350f', borderColor: '#d97706' },
    'Delayed': { backgroundColor: '#fff1f2', color: '#881337', borderColor: '#e11d48' },
    'Not Signed': { backgroundColor: '#fff1f2', color: '#881337', borderColor: '#e11d48' },
    'Not Started': { backgroundColor: '#f8fafc', color: '#0f172a', borderColor: '#475569' },
};

const normalizeItStatus = (rawStatus) => {
    const normalized = String(rawStatus ?? '').trim().toLowerCase();

    if (!normalized || normalized === '-' || normalized === 'null') {
        return 'Not Started';
    }

    if (normalized.includes('on track')) return 'On Track';
    if (normalized.includes('at risk')) return 'At Risk';
    if (normalized.includes('not signed')) return 'Not Signed';
    if (normalized.includes('not started')) return 'Not Started';
    if (normalized.includes('done') || normalized.includes('completed')) return 'Done';
    if (normalized.includes('delayed')) return 'Delayed';

    if (normalized === '4' || normalized === 'approved') return 'On Track';
    if (normalized === '3' || normalized === 'review' || normalized === 'propose') return 'At Risk';
    if (normalized === '2' || normalized === 'draft' || normalized === 'drafting' || normalized === '0' || normalized === '1') return 'Not Started';

    return 'Not Started';
};

const getStatusBadgeStyle = (status) => {
    const normalized = normalizeItStatus(status);
    const palette = IT_STATUS_PALETTE[normalized] || IT_STATUS_PALETTE['Not Started'];
    return {
        backgroundColor: palette.backgroundColor,
        color: palette.color,
        borderColor: palette.borderColor,
        borderWidth: '1px',
        borderStyle: 'solid',
    };
};

const getStatusLabel = (status) => {
    return normalizeItStatus(status);
};

const formatInitiativeType = (typeValue) => {
    const typeMap = {
        1: 'Digital',
        2: 'IT',
    };
    return typeMap[typeValue] || '-';
};

const formatTipeRelasi = (tipeRelasi) => {
    const relasiMap = {
        1: 'Predecessor',
        2: 'Successor',
    };
    return relasiMap[tipeRelasi] || '-';
};
</script>
