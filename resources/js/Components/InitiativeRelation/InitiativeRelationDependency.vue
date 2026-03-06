<template>
    <section
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div
            class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div
                class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <label for="initiative-type-filter" class="text-[11px]">Filter Type</label>
                    <select id="initiative-type-filter" v-model="selectedType"
                        class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                        <option value="all">All</option>
                        <option value="digital">Digital</option>
                        <option value="it">IT</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label for="model-relasi-filter" class="text-[11px]">Model Relasi</label>
                    <select id="model-relasi-filter" v-model="selectedModelRelasi"
                        class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                        <option value="all">All</option>
                        <option v-for="option in modelRelasiOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label for="initiative-filter" class="text-[11px]">Initiative</label>
                    <select id="initiative-filter" v-model="selectedInitiative"
                        :disabled="displayMode === 'all'"
                        class="w-48 max-w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                        <option value="all">All</option>
                        <option v-for="option in filteredInitiativeOptions" :key="option.id" :value="option.id">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label for="relation-view-mode" class="text-[11px]">Mode</label>
                    <select id="relation-view-mode" v-model="displayMode"
                        class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                        <option value="per-code">Per Code</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <colgroup>
                    <col style="width: 16%;">
                    <col style="width: 5%;">
                    <col style="width: 16%;">
                    <col style="width: 30%;">
                    <col style="width: 5%;">
                    <col style="width: 5%;">
                </colgroup>
                <tbody>
                    <template v-if="displayMode === 'per-code'">
                        <tr v-if="!filteredInitiatives.length">
                            <td colspan="6"
                                class="border border-slate-200 px-4 py-6 text-center text-sm text-slate-500 dark:border-white/10 dark:text-slate-400">
                                Belum ada data initiative.
                            </td>
                        </tr>
                        <template v-for="initiative in filteredInitiatives" :key="initiative.id">
                            <tr class="bg-slate-100 dark:bg-slate-900/30">
                                <td colspan="6"
                                    class="border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:text-slate-200">
                                    <span class="text-slate-600 dark:text-slate-400">Code:</span>
                                    {{ initiative.code ?? initiative.id ?? '-' }}
                                    <span class="mx-2 text-slate-400">|</span>
                                    <span class="text-slate-600 dark:text-slate-400">Initiative:</span>
                                    {{ initiative.name ?? '-' }}
                                </td>
                            </tr>
                            <tr class="bg-slate-50 dark:bg-white/5">
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Initiative A
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Tipe Relasi
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Initiative B
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Justifikasi
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Sumber
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Action
                                </th>
                            </tr>
                            <tr v-for="(relation, index) in relationRowsByInitiative(initiative)" :key="`${initiative.id}-${index}`"
                                class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                                <td
                                    class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    <span v-if="relation"
                                        class="inline-block break-words rounded px-2 py-0.5"
                                        :style="relationInitiativeChipStyle(relation.predecessor_id)">
                                        {{ relation.predecessor }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>

                                <td
                                    class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    <span
                                        v-if="relation"
                                        class="inline-block rounded px-2 py-0.5"
                                        :style="relationTypeChipStyle(relation)"
                                    >
                                        {{ getRelationPositionLabel(relation.type_relation) }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    <span
                                        v-if="relation"
                                        class="inline-block break-words rounded px-2 py-0.5"
                                        :style="relationInitiativeChipStyle(relation.successor_id)"
                                    >
                                        {{ relation.successor }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-ellipsis overflow-hidden">
                                    {{ relation?.justifikasi ?? '-' }}
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    {{ relation?.model_relasi ?? '-' }}
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-center align-middle dark:border-white/10 overflow-hidden">
                                    <button
                                        v-if="relation"
                                        type="button"
                                        class="relative z-10 cursor-pointer rounded-full border border-blue-300 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                        @click="handleEditRelation(initiative, relation)"
                                    >
                                        Edit
                                    </button>
                                    <span v-else class="text-slate-300 dark:text-slate-600">-</span>
                                </td>
                            </tr>
                            <tr v-if="!relationRowsByInitiative(initiative).length"
                                class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                                <td colspan="6"
                                    class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-400 dark:border-white/10 dark:text-slate-500">
                                    Tidak ada relasi
                                </td>
                            </tr>

                            <tr>
                                <td colspan="6"
                                    class="border border-slate-200 bg-slate-50/70 px-4 py-4 dark:border-white/10 dark:bg-[#141414]">
                                    <div class="rounded-xl border border-slate-200 bg-white dark:border-white/10 dark:bg-[#101010]">
                                        <div
                                            class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
                                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                                Diagram Relasi Predecessor -> Successor
                                            </h3>
                                            <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <span class="h-0.5 w-6 rounded bg-emerald-600 dark:bg-emerald-400"></span>
                                                    Garis Predecessor
                                                </span>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <span class="h-0.5 w-6 rounded bg-blue-600 dark:bg-blue-400"></span>
                                                    Garis Successor
                                                </span>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <div v-if="!relationRowsByInitiative(initiative).length"
                                                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                                                Belum ada relasi untuk divisualisasikan.
                                            </div>
                                            <div
                                                v-else
                                                class="rounded-lg border border-slate-200 dark:border-white/10"
                                                :style="{ height: `${relationGraphByInitiative(initiative).height}px` }"
                                            >
                                                <VueFlow
                                                    class="initiative-relation-flow"
                                                    :nodes="relationGraphByInitiative(initiative).nodes"
                                                    :edges="relationGraphByInitiative(initiative).edges"
                                                    :fit-view-on-init="true"
                                                    :nodes-draggable="false"
                                                    :nodes-connectable="false"
                                                    :elements-selectable="false"
                                                    :zoom-on-double-click="false"
                                                    :min-zoom="0.35"
                                                    :max-zoom="1.5"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template v-else>
                        <tr class="bg-slate-100 dark:bg-slate-900/30">
                            <td colspan="6"
                                class="border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:text-slate-200">
                                Mode All - Semua Kode Initiative
                                <span class="mx-2 text-slate-400">|</span>
                                Total Relasi: {{ allRelationRows.length }}
                            </td>
                        </tr>
                        <tr class="bg-slate-50 dark:bg-white/5">
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Initiative A
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Tipe Relasi
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Initiative B
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Justifikasi
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Sumber
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Action
                            </th>
                        </tr>
                        <tr v-if="!allRelationRows.length">
                            <td colspan="6"
                                class="border border-slate-200 px-4 py-6 text-center text-sm text-slate-500 dark:border-white/10 dark:text-slate-400">
                                Tidak ada relasi untuk filter saat ini.
                            </td>
                        </tr>
                        <tr
                            v-for="(relation, index) in allRelationRows"
                            :key="`all-${relationRowKey(relation)}-${index}`"
                            class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                        >
                            <td
                                class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                <span
                                    class="inline-block break-words rounded px-2 py-0.5"
                                    :style="relationInitiativeChipStyle(relation.predecessor_id)"
                                >
                                    {{ relation.predecessor }}
                                </span>
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                <span
                                    class="inline-block rounded px-2 py-0.5"
                                    :style="relationTypeChipStyle(relation)"
                                >
                                    {{ getRelationPositionLabel(relation.type_relation) }}
                                </span>
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                <span
                                    class="inline-block break-words rounded px-2 py-0.5"
                                    :style="relationInitiativeChipStyle(relation.successor_id)"
                                >
                                    {{ relation.successor }}
                                </span>
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-ellipsis overflow-hidden">
                                {{ relation?.justifikasi ?? '-' }}
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                {{ relation?.model_relasi ?? '-' }}
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-center align-middle dark:border-white/10 overflow-hidden">
                                <button
                                    type="button"
                                    class="relative z-10 cursor-pointer rounded-full border border-blue-300 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                    @click="handleEditRelation(initiativeFromRelation(relation), relation)"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6"
                                class="border border-slate-200 bg-slate-50/70 px-4 py-4 dark:border-white/10 dark:bg-[#141414]">
                                <div class="rounded-xl border border-slate-200 bg-white dark:border-white/10 dark:bg-[#101010]">
                                    <div
                                        class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
                                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            Diagram Relasi Predecessor -> Successor (All)
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="h-0.5 w-6 rounded bg-emerald-600 dark:bg-emerald-400"></span>
                                                Garis Predecessor
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="h-0.5 w-6 rounded bg-blue-600 dark:bg-blue-400"></span>
                                                Garis Successor
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div v-if="!allRelationRows.length"
                                            class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                                            Belum ada relasi untuk divisualisasikan.
                                        </div>
                                        <div
                                            v-else
                                            class="rounded-lg border border-slate-200 dark:border-white/10"
                                            :style="{ height: `${relationGraphAll.height}px` }"
                                        >
                                            <VueFlow
                                                class="initiative-relation-flow"
                                                :nodes="relationGraphAll.nodes"
                                                :edges="relationGraphAll.edges"
                                                :fit-view-on-init="true"
                                                :nodes-draggable="false"
                                                :nodes-connectable="false"
                                                :elements-selectable="false"
                                                :zoom-on-double-click="false"
                                                :min-zoom="0.3"
                                                :max-zoom="1.6"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { MarkerType, Position, VueFlow } from '@vue-flow/core';
import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';

const props = defineProps({
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
    modelRelationOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['edit-relation']);

const selectedType = ref('all');
const selectedModelRelasi = ref('all');
const selectedInitiative = ref('all');
const displayMode = ref('per-code');

watch(displayMode, (mode) => {
    if (mode === 'all') {
        selectedType.value = 'all';
        selectedInitiative.value = 'all';
    }
});

const handleEditRelation = (initiative, relation) => {
    emit('edit-relation', {
        initiative,
        relation,
    });
};

const getRelationPositionLabel = (typeRelation) => {
    const key = typeRelation != null ? Number(typeRelation) : null;
    const positionConfig = {
        1: 'Predecessor',
        2: 'Successor',
    };
    return positionConfig[key] ?? 'Unknown';
};

const modelRelasiOptions = computed(() => {
    if (!props.modelRelationOptions?.length) {
        return [];
    }

    return props.modelRelationOptions.filter((option) => option);
});

const initiativeOptions = computed(() => {
    return props.mstInitiatives.map((initiative) => {
        const code = initiative.code ?? initiative.id ?? '-';
        const name = initiative.name ?? '-';
        const coeName = initiative?.coe?.name ?? initiative?.coe_name ?? '';
        const baseLabel = `${code} - ${name}`;
        const label = coeName ? `${baseLabel} (CoE: ${coeName})` : baseLabel;

        return {
            id: initiative.id,
            code,
            name,
            label,
            type: initiative.tipe_initiative != null ? Number(initiative.tipe_initiative) : null,
        };
    });
});

const filteredInitiativeOptions = computed(() => {
    if (selectedType.value === 'digital') {
        return initiativeOptions.value.filter((initiative) => initiative.type === 1);
    }
    if (selectedType.value === 'it') {
        return initiativeOptions.value.filter((initiative) => initiative.type === 2);
    }
    return initiativeOptions.value;
});

const filteredInitiatives = computed(() => {
    let initiatives = props.mstInitiatives;

    if (selectedType.value !== 'all') {
        const expectedType = selectedType.value === 'digital' ? 1 : 2;
        initiatives = initiatives.filter((initiative) => Number(initiative.tipe_initiative) === expectedType);
    }

    if (selectedInitiative.value !== 'all') {
        initiatives = initiatives.filter((initiative) => (
            Number(initiative.id) === Number(selectedInitiative.value)
        ));
    }

    // Filter by model relasi if selected
    if (selectedModelRelasi.value !== 'all') {
        initiatives = initiatives.filter((initiative) => extractRelations(initiative).length > 0);
    }

    return initiatives;
});

const initiativeById = computed(() => {
    const map = new Map();
    props.mstInitiatives.forEach((initiative) => {
        map.set(Number(initiative.id), initiative);
    });
    return map;
});

const findInitiativeById = (initiativeId) => initiativeById.value.get(Number(initiativeId)) ?? null;

const formatInitiative = (initiative, fallbackCode) => {
    const code = initiative?.code ?? initiative?.id ?? fallbackCode ?? '-';
    const name = initiative?.name;
    if (!name) {
        return code;
    }
    return `${code} - ${name}`;
};

const toNumericId = (value) => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : null;
};

const getRelationDirection = (relation) => {
    const typeRelation = relation?.type_relation != null ? Number(relation.type_relation) : null;
    const rowId = toNumericId(relation?.initiative_code_row);
    const columnId = toNumericId(relation?.initiative_code_column);

    if (typeRelation === 2) {
        return {
            predecessorId: columnId,
            successorId: rowId,
        };
    }

    return {
        predecessorId: rowId,
        successorId: columnId,
    };
};

const initiativeNodeId = (initiativeId) => `initiative-${initiativeId}`;

const STATUS_BLOCK_STYLE_MAP = {
    drafting: {
        borderColor: '#D97706',
        backgroundColor: '#F59E0B',
        color: '#ffffff',
    },
    propose: {
        borderColor: '#0EA5E9',
        backgroundColor: '#0284C7',
        color: '#ffffff',
    },
    review: {
        borderColor: '#DC2626',
        backgroundColor: '#F87171',
        color: '#ffffff',
    },
    approved: {
        borderColor: '#22C55E',
        backgroundColor: '#BBF7D0',
        color: '#0f5132',
    },
    postpone: {
        borderColor: '#EAB308',
        backgroundColor: '#FEF08A',
        color: '#7a6000',
    },
};

const STATUS_ALIAS_MAP = {
    draft: 'drafting',
    approve: 'approved',
    aproved: 'approved',
};

const VALID_STATUS_KEYS = new Set(Object.keys(STATUS_BLOCK_STYLE_MAP));

const normalizeStatus = (value) => String(value ?? '').trim().toLowerCase();

const resolveInitiativeStatusKey = (initiative) => {
    const raw = normalizeStatus(
        initiative?.latest_status?.status
        ?? initiative?.latestStatus?.status
        ?? initiative?.status
        ?? 'drafting',
    );
    const canonical = STATUS_ALIAS_MAP[raw] ?? raw;

    return VALID_STATUS_KEYS.has(canonical) ? canonical : 'drafting';
};

const resolveInitiativeBlockStyle = (initiative, isCurrent = false) => {
    const statusKey = resolveInitiativeStatusKey(initiative);
    const palette = STATUS_BLOCK_STYLE_MAP[statusKey] ?? STATUS_BLOCK_STYLE_MAP.propose;

    return {
        borderColor: palette.borderColor,
        backgroundColor: palette.backgroundColor,
        color: palette.color,
        borderWidth: '1px',
        borderStyle: 'solid',
        borderRadius: '0.5rem',
        fontSize: '10px',
        fontWeight: '600',
        lineHeight: '1.25',
        padding: '6px 8px',
        boxShadow: isCurrent
            ? '0 0 0 2px rgba(15, 23, 42, 0.18), 0 4px 12px rgba(15, 23, 42, 0.25)'
            : '0 2px 6px rgba(15, 23, 42, 0.16)',
    };
};

const relationInitiativeChipStyle = (initiativeId) => {
    const initiative = findInitiativeById(initiativeId);
    const palette = STATUS_BLOCK_STYLE_MAP[resolveInitiativeStatusKey(initiative)] ?? STATUS_BLOCK_STYLE_MAP.drafting;

    return {
        borderColor: palette.borderColor,
        backgroundColor: palette.backgroundColor,
        color: palette.color,
        borderWidth: '1px',
        borderStyle: 'solid',
        fontSize: '0.95em',
        fontWeight: 600,
    };
};

const relationTypeChipStyle = (relation) => {
    const relationType = relation?.type_relation != null ? Number(relation.type_relation) : null;
    if (relationType === 1) {
        return {
            borderColor: '#059669',
            backgroundColor: '#d1fae5',
            color: '#065f46',
            borderWidth: '1px',
            borderStyle: 'solid',
            fontSize: '0.95em',
            fontWeight: 600,
        };
    }
    if (relationType === 2) {
        return {
            borderColor: '#2563eb',
            backgroundColor: '#dbeafe',
            color: '#1e40af',
            borderWidth: '1px',
            borderStyle: 'solid',
            fontSize: '0.95em',
            fontWeight: 600,
        };
    }

    return {
        borderColor: '#64748b',
        backgroundColor: '#f1f5f9',
        color: '#334155',
        borderWidth: '1px',
        borderStyle: 'solid',
        fontSize: '0.95em',
        fontWeight: 600,
    };
};

const relationLineColor = (typeRelation) => {
    const relationType = typeRelation != null ? Number(typeRelation) : null;
    if (relationType === 1) return '#059669';
    if (relationType === 2) return '#2563eb';
    return '#64748b';
};

const extractRelations = (initiative) => {
    const initiativeRelationsRow = initiative?.initiativeRelationsRow
        ?? initiative?.initiative_relations_row
        ?? [];
    const initiativeRelationsColumn = initiative?.initiativeRelationsColumn
        ?? initiative?.initiative_relations_column
        ?? [];

    const justifikasiValue = (relation) => relation.justifikasi ?? relation.description ?? '-';
    const shouldIncludeRelation = (relation) => {
        if (selectedModelRelasi.value === 'all') {
            return true;
        }

        return relation?.model_relasi === selectedModelRelasi.value;
    };
    const rows = [];
    const seen = new Set();

    const relationKey = (relation) => {
        if (relation?.id) {
            return `id-${relation.id}`;
        }

        return `row-${relation?.initiative_code_row}-col-${relation?.initiative_code_column}`;
    };

    initiativeRelationsRow.forEach((relation) => {
        if (!shouldIncludeRelation(relation)) {
            return;
        }

        const key = relationKey(relation);
        if (seen.has(key)) {
            return;
        }
        seen.add(key);

        const { predecessorId, successorId } = getRelationDirection(relation);

        rows.push({
            id: relation.id,
            predecessor: formatInitiative(findInitiativeById(predecessorId), predecessorId),
            successor: formatInitiative(findInitiativeById(successorId), successorId),
            predecessor_id: predecessorId,
            successor_id: successorId,
            justifikasi: justifikasiValue(relation),
            model_relasi: relation.model_relasi ?? '-',
            type_relation: relation.type_relation != null ? Number(relation.type_relation) : null,
        });
    });

    initiativeRelationsColumn.forEach((relation) => {
        if (!shouldIncludeRelation(relation)) {
            return;
        }

        const key = relationKey(relation);
        if (seen.has(key)) {
            return;
        }
        seen.add(key);

        const { predecessorId, successorId } = getRelationDirection(relation);

        rows.push({
            id: relation.id,
            predecessor: formatInitiative(findInitiativeById(predecessorId), predecessorId),
            successor: formatInitiative(findInitiativeById(successorId), successorId),
            predecessor_id: predecessorId,
            successor_id: successorId,
            justifikasi: justifikasiValue(relation),
            model_relasi: relation.model_relasi ?? '-',
            type_relation: relation.type_relation != null ? Number(relation.type_relation) : null,
        });
    });

    return rows;
};

const relationsByInitiativeId = computed(() => {
    const map = new Map();
    props.mstInitiatives.forEach((initiative) => {
        map.set(Number(initiative.id), extractRelations(initiative));
    });
    return map;
});

const relationRowsByInitiative = (initiative) => (
    relationsByInitiativeId.value.get(Number(initiative?.id)) ?? []
);

const relationRowKey = (relation) => {
    if (relation?.id != null) {
        return `id-${relation.id}`;
    }

    return [
        relation?.predecessor_id ?? '-',
        relation?.successor_id ?? '-',
        relation?.type_relation ?? '-',
        relation?.model_relasi ?? '-',
    ].join('|');
};

const allRelationRows = computed(() => {
    const rows = [];
    const seen = new Set();

    filteredInitiatives.value.forEach((initiative) => {
        relationRowsByInitiative(initiative).forEach((relation) => {
            const key = relationRowKey(relation);
            if (seen.has(key)) {
                return;
            }
            seen.add(key);
            rows.push(relation);
        });
    });

    return rows.sort((left, right) => {
        const leftPred = Number(left?.predecessor_id ?? 0);
        const rightPred = Number(right?.predecessor_id ?? 0);
        if (leftPred !== rightPred) {
            return leftPred - rightPred;
        }

        const leftSucc = Number(left?.successor_id ?? 0);
        const rightSucc = Number(right?.successor_id ?? 0);
        if (leftSucc !== rightSucc) {
            return leftSucc - rightSucc;
        }

        return Number(left?.id ?? 0) - Number(right?.id ?? 0);
    });
});

const initiativeFromRelation = (relation) => (
    findInitiativeById(relation?.predecessor_id) ?? findInitiativeById(relation?.successor_id) ?? null
);

const buildGraphForInitiative = (initiative, relations) => {
    const currentId = toNumericId(initiative?.id);
    if (currentId == null) {
        return { nodes: [], edges: [], height: 260 };
    }

    const predecessors = [];
    const successors = [];
    const predecessorSet = new Set();
    const successorSet = new Set();

    relations.forEach((relation) => {
        const predecessorId = toNumericId(relation?.predecessor_id);
        const successorId = toNumericId(relation?.successor_id);

        if (
            successorId === currentId
            && predecessorId != null
            && predecessorId !== currentId
            && !predecessorSet.has(predecessorId)
        ) {
            predecessorSet.add(predecessorId);
            predecessors.push(predecessorId);
        }

        if (
            predecessorId === currentId
            && successorId != null
            && successorId !== currentId
            && !successorSet.has(successorId)
        ) {
            successorSet.add(successorId);
            successors.push(successorId);
        }
    });

    const rowGap = 92;
    const rowCount = Math.max(predecessors.length, successors.length, 1);
    const centerY = 36 + ((rowCount - 1) * rowGap) / 2;
    const startYByCount = (count) => centerY - ((Math.max(count, 1) - 1) * rowGap) / 2;
    const diagramHeight = Math.max(260, Math.min(560, rowCount * rowGap + 120));

    const nodes = [
        {
            id: initiativeNodeId(currentId),
            position: { x: 360, y: centerY },
            data: { label: formatInitiative(initiative, currentId) },
            class: 'initiative-node-block initiative-node-block--focus',
            sourcePosition: Position.Right,
            targetPosition: Position.Left,
            draggable: false,
            selectable: false,
            style: resolveInitiativeBlockStyle(initiative, true),
        },
    ];

    const predecessorStartY = startYByCount(predecessors.length);
    predecessors.forEach((initiativeId, index) => {
        const linkedInitiative = findInitiativeById(initiativeId);
        nodes.push({
            id: initiativeNodeId(initiativeId),
            position: { x: 30, y: predecessorStartY + (index * rowGap) },
            data: { label: formatInitiative(linkedInitiative, initiativeId) },
            class: 'initiative-node-block',
            sourcePosition: Position.Right,
            targetPosition: Position.Right,
            draggable: false,
            selectable: false,
            style: resolveInitiativeBlockStyle(linkedInitiative),
        });
    });

    const successorStartY = startYByCount(successors.length);
    successors.forEach((initiativeId, index) => {
        const linkedInitiative = findInitiativeById(initiativeId);
        nodes.push({
            id: initiativeNodeId(initiativeId),
            position: { x: 690, y: successorStartY + (index * rowGap) },
            data: { label: formatInitiative(linkedInitiative, initiativeId) },
            class: 'initiative-node-block',
            sourcePosition: Position.Left,
            targetPosition: Position.Left,
            draggable: false,
            selectable: false,
            style: resolveInitiativeBlockStyle(linkedInitiative),
        });
    });

    const edges = relations
        .map((relation, index) => {
            const predecessorId = toNumericId(relation?.predecessor_id);
            const successorId = toNumericId(relation?.successor_id);

            if (predecessorId == null || successorId == null) {
                return null;
            }

            const lineColor = relationLineColor(relation?.type_relation);

            return {
                id: relation?.id != null ? `relation-${relation.id}` : `relation-${currentId}-${index}`,
                source: initiativeNodeId(predecessorId),
                target: initiativeNodeId(successorId),
                markerEnd: {
                    type: MarkerType.ArrowClosed,
                    color: lineColor,
                },
                type: 'smoothstep',
                style: {
                    stroke: lineColor,
                    strokeWidth: 2.1,
                },
                label: relation?.model_relasi && relation.model_relasi !== '-' ? relation.model_relasi : undefined,
                labelStyle: {
                    fill: '#475569',
                    fontSize: 11,
                    fontWeight: 600,
                },
                labelBgStyle: {
                    fill: '#ffffff',
                    fillOpacity: 0.95,
                },
                labelBgPadding: [4, 2],
                selectable: false,
                focusable: false,
            };
        })
        .filter(Boolean);

    return {
        nodes,
        edges,
        height: diagramHeight,
    };
};

const buildGraphForAll = (relations) => {
    if (!relations.length) {
        return { nodes: [], edges: [], height: 320 };
    }

    const nodeIds = new Set();
    const adjacency = new Map();
    const indegree = new Map();

    const registerNode = (idValue) => {
        const nodeId = toNumericId(idValue);
        if (nodeId == null || nodeIds.has(nodeId)) {
            return;
        }
        nodeIds.add(nodeId);
        adjacency.set(nodeId, []);
        indegree.set(nodeId, 0);
    };

    relations.forEach((relation) => {
        registerNode(relation?.predecessor_id);
        registerNode(relation?.successor_id);
    });

    relations.forEach((relation) => {
        const sourceId = toNumericId(relation?.predecessor_id);
        const targetId = toNumericId(relation?.successor_id);
        if (sourceId == null || targetId == null || sourceId === targetId) {
            return;
        }

        adjacency.get(sourceId)?.push(targetId);
        indegree.set(targetId, (indegree.get(targetId) ?? 0) + 1);
    });

    const levelByNode = new Map();
    const queue = [];
    indegree.forEach((value, key) => {
        if (value === 0) {
            queue.push(key);
            levelByNode.set(key, 0);
        }
    });

    const pendingIndegree = new Map(indegree);
    while (queue.length) {
        const current = queue.shift();
        const currentLevel = levelByNode.get(current) ?? 0;
        const targets = adjacency.get(current) ?? [];

        targets.forEach((target) => {
            const nextLevel = Math.max(levelByNode.get(target) ?? 0, currentLevel + 1);
            levelByNode.set(target, nextLevel);

            const remaining = (pendingIndegree.get(target) ?? 0) - 1;
            pendingIndegree.set(target, remaining);
            if (remaining === 0) {
                queue.push(target);
            }
        });
    }

    let fallbackLevel = 0;
    nodeIds.forEach((nodeId) => {
        if (!levelByNode.has(nodeId)) {
            levelByNode.set(nodeId, fallbackLevel);
            fallbackLevel += 1;
        }
    });

    const maxLevel = Math.max(...levelByNode.values(), 0);
    const columns = Array.from({ length: maxLevel + 1 }, () => []);
    nodeIds.forEach((nodeId) => {
        const level = levelByNode.get(nodeId) ?? 0;
        columns[level].push(nodeId);
    });

    columns.forEach((column) => {
        column.sort((left, right) => {
            const leftInitiative = findInitiativeById(left);
            const rightInitiative = findInitiativeById(right);
            const leftCode = Number(leftInitiative?.code ?? left);
            const rightCode = Number(rightInitiative?.code ?? right);
            if (Number.isFinite(leftCode) && Number.isFinite(rightCode) && leftCode !== rightCode) {
                return leftCode - rightCode;
            }
            return left - right;
        });
    });

    const colGap = 330;
    const rowGap = 92;
    const baseX = 30;
    const baseY = 30;
    const maxRows = columns.reduce((max, column) => Math.max(max, column.length), 1);

    const nodes = [];
    columns.forEach((column, level) => {
        column.forEach((nodeId, rowIndex) => {
            const initiative = findInitiativeById(nodeId);
            nodes.push({
                id: initiativeNodeId(nodeId),
                position: {
                    x: baseX + (level * colGap),
                    y: baseY + (rowIndex * rowGap),
                },
                data: {
                    label: formatInitiative(initiative, nodeId),
                },
                class: 'initiative-node-block',
                sourcePosition: Position.Right,
                targetPosition: Position.Left,
                draggable: false,
                selectable: false,
                style: resolveInitiativeBlockStyle(initiative),
            });
        });
    });

    const edges = relations
        .map((relation, index) => {
            const predecessorId = toNumericId(relation?.predecessor_id);
            const successorId = toNumericId(relation?.successor_id);
            if (predecessorId == null || successorId == null) {
                return null;
            }

            const lineColor = relationLineColor(relation?.type_relation);

            return {
                id: relation?.id != null ? `all-relation-${relation.id}` : `all-relation-${index}`,
                source: initiativeNodeId(predecessorId),
                target: initiativeNodeId(successorId),
                markerEnd: {
                    type: MarkerType.ArrowClosed,
                    color: lineColor,
                },
                type: 'smoothstep',
                style: {
                    stroke: lineColor,
                    strokeWidth: 2.1,
                },
                label: relation?.model_relasi && relation.model_relasi !== '-' ? relation.model_relasi : undefined,
                labelStyle: {
                    fill: '#475569',
                    fontSize: 11,
                    fontWeight: 600,
                },
                labelBgStyle: {
                    fill: '#ffffff',
                    fillOpacity: 0.95,
                },
                labelBgPadding: [4, 2],
                selectable: false,
                focusable: false,
            };
        })
        .filter(Boolean);

    return {
        nodes,
        edges,
        height: Math.max(320, Math.min(900, maxRows * rowGap + 140)),
    };
};

const relationGraphByInitiativeId = computed(() => {
    const map = new Map();
    props.mstInitiatives.forEach((initiative) => {
        const relations = relationRowsByInitiative(initiative);
        map.set(Number(initiative?.id), buildGraphForInitiative(initiative, relations));
    });
    return map;
});

const relationGraphByInitiative = (initiative) => (
    relationGraphByInitiativeId.value.get(Number(initiative?.id)) ?? { nodes: [], edges: [], height: 260 }
);

const relationGraphAll = computed(() => buildGraphForAll(allRelationRows.value));

</script>

<style scoped>
:deep(.initiative-relation-flow.vue-flow) {
    background:
        radial-gradient(circle at 1px 1px, rgba(148, 163, 184, 0.3) 1px, transparent 0);
    background-size: 16px 16px;
}

:deep(.initiative-relation-flow .vue-flow__node) {
    width: 240px;
    white-space: normal;
    text-align: center;
}

:deep(.initiative-relation-flow .vue-flow__node.initiative-node-block) {
    font-family: inherit;
}

:deep(.initiative-relation-flow .vue-flow__node.initiative-node-block--focus) {
    transform: translateZ(0);
}

:deep(.initiative-relation-flow .vue-flow__handle) {
    width: 8px;
    height: 8px;
    opacity: 0;
    pointer-events: none;
    border: 0;
    background: transparent;
}

:deep(.initiative-relation-flow .vue-flow__edge-path) {
    stroke-linecap: round;
    stroke-linejoin: round;
}

:deep(.dark .initiative-relation-flow.vue-flow) {
    background:
        radial-gradient(circle at 1px 1px, rgba(100, 116, 139, 0.35) 1px, transparent 0);
    background-size: 16px 16px;
}
</style>
