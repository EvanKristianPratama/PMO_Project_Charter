<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const props = defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    groups: {
        type: Array,
        default: () => [],
    },
    strategyColumns: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();
const pageContext = usePage();

const flash = computed(() => pageContext.props.flash ?? {});
const selectedOrganization = ref('');
const isEditMode = ref(false);

const strategyForm = reactive({
    business_unit: '',
    maximazing_value: '',
    expand: '',
    low_carbon: '',
});

const strategyProcessing = ref(false);
const isModalVisible = ref(false);
const modalError = ref('');
const editableRows = ref({});
const editableSnapshot = ref({});

const orderedStrategyColumns = computed(() => {
    const lowCarbonColumn = (props.strategyColumns || []).find(
        (column) => column.key === 'low_carbon',
    );
    const otherColumns = (props.strategyColumns || []).filter(
        (column) => column.key !== 'low_carbon',
    );

    return lowCarbonColumn ? [...otherColumns, lowCarbonColumn] : [...(props.strategyColumns || [])];
});

const lowCarbonColumn = computed(() => orderedStrategyColumns.value.find((column) => column.key === 'low_carbon') ?? null);
const legacyColumns = computed(() => orderedStrategyColumns.value.filter((column) => column.key !== 'low_carbon'));
const strategyFormFields = computed(() => orderedStrategyColumns.value);
const allRows = computed(() => normalizedGroups.value.flatMap((group) => group.rows || []));

const mergeRowsByBusinessUnit = (rows) => {
    let previousBusinessUnitKey = null;
    let previousMergedRow = null;

    return (rows || []).map((row) => {
        const businessUnitKey = `${row.group_key || 'unknown'}::${row.business_unit_id || row.business_unit || row.id}`;

        if (businessUnitKey === previousBusinessUnitKey && previousMergedRow) {
            previousMergedRow.business_unit_rowspan += 1;

            return {
                ...row,
                show_business_unit: false,
                business_unit_rowspan: 0,
            };
        }

        const mergedRow = {
            ...row,
            show_business_unit: true,
            business_unit_rowspan: 1,
        };

        previousBusinessUnitKey = businessUnitKey;
        previousMergedRow = mergedRow;

        return mergedRow;
    });
};

const normalizedGroups = computed(() => props.groups || []);
const filterOrganizationOptions = computed(() => {
    const optionMap = new Map(
        (props.organizationOptions || []).map((organization) => [
            String(organization.value),
            organization,
        ]),
    );

    return allRows.value
        .reduce((carry, row) => {
            const value = String(row.business_unit_id ?? '');

            if (!value || carry.some((item) => item.value === value)) {
                return carry;
            }

            const fallbackLabel = row.group_label
                ? `${row.group_label} - ${row.business_unit}`
                : row.business_unit;

            carry.push(
                optionMap.get(value) ?? {
                    value,
                    label: fallbackLabel,
                },
            );

            return carry;
        }, [])
        .sort((left, right) => String(left.label ?? '').localeCompare(String(right.label ?? '')));
});

const filteredGroups = computed(() => normalizedGroups.value
    .map((group) => {
        const rows = (group.rows || []).filter((row) => {
            return !selectedOrganization.value
                || String(row.business_unit_id ?? '') === String(selectedOrganization.value);
        });

        return {
            ...group,
            count: rows.length,
            rows: mergeRowsByBusinessUnit(rows),
        };
    })
    .filter((group) => group.rows.length > 0));

const modalTitle = computed(() => 'Tambah Strategy');
const modalMessage = computed(() => 'Pilih business unit lalu isi arah strategy yang ingin ditambahkan.');
const modalConfirmText = computed(() => 'Tambah Strategy');

const showSuccessAlert = (message) => {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: message,
        confirmButtonColor: '#0f6fb7',
    });
};

const buildEditableRowsState = () => {
    return allRows.value.reduce((carry, row) => {
        carry[row.id] = {
            id: Number(row.id),
            business_unit: Number(row.business_unit_id ?? 0),
            maximazing_value: String(row.values?.maximazing_value ?? ''),
            expand: String(row.values?.expand ?? ''),
            low_carbon: String(row.values?.low_carbon ?? ''),
        };

        return carry;
    }, {});
};

const cloneEditableRowsState = (state) => JSON.parse(JSON.stringify(state));

const resetEditableRows = () => {
    const nextState = buildEditableRowsState();
    editableRows.value = cloneEditableRowsState(nextState);
    editableSnapshot.value = cloneEditableRowsState(nextState);
};

const rowHasChanged = (rowId) => {
    const current = editableRows.value[rowId];
    const original = editableSnapshot.value[rowId];

    if (!current || !original) {
        return false;
    }

    return ['business_unit', 'maximazing_value', 'expand', 'low_carbon']
        .some((field) => String(current[field] ?? '') !== String(original[field] ?? ''));
};

const changedEditableRows = computed(() => {
    return Object.values(editableRows.value).filter((row) => rowHasChanged(row.id));
});

const hasEditableChanges = computed(() => changedEditableRows.value.length > 0);

const resetStrategyForm = (nextValues = {}) => {
    strategyForm.business_unit = nextValues.business_unit != null ? String(nextValues.business_unit) : '';
    strategyForm.maximazing_value = String(nextValues.maximazing_value ?? '');
    strategyForm.expand = String(nextValues.expand ?? '');
    strategyForm.low_carbon = String(nextValues.low_carbon ?? '');
};

const closeModal = (force = false) => {
    if (strategyProcessing.value && !force) {
        return;
    }

    isModalVisible.value = false;
};

const handleModalAfterLeave = () => {
    modalError.value = '';
    resetStrategyForm();
};

const openAddStrategyModal = () => {
    modalError.value = '';
    resetStrategyForm({
        business_unit: selectedOrganization.value || '',
    });
    isModalVisible.value = true;
};

const openAddStrategy = () => {
    openAddStrategyModal();
};

const startEditMode = () => {
    resetEditableRows();
    isEditMode.value = true;
};

const cancelEditMode = () => {
    resetEditableRows();
    isEditMode.value = false;
};

const saveEditMode = () => {
    if (!hasEditableChanges.value) {
        isEditMode.value = false;
        return;
    }

    strategyProcessing.value = true;

    router.put(route('strategic-house.business-strategy.bulk-update'), {
        rows: changedEditableRows.value.map((row) => ({
            id: Number(row.id),
            business_unit: Number(row.business_unit),
            maximazing_value: row.maximazing_value,
            expand: row.expand,
            low_carbon: row.low_carbon,
        })),
    }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            strategyProcessing.value = false;
        },
        onSuccess: (page) => {
            isEditMode.value = false;
            showSuccessAlert(
                page?.props?.flash?.success ?? 'Perubahan business strategy berhasil disimpan.',
            );
        },
    });
};

const firstErrorMessage = (errors = {}) => {
    return Object.values(errors)
        .flat()
        .map((value) => String(value ?? '').trim())
        .find((value) => value !== '');
};

const buildPayload = () => ({
    business_unit: Number(strategyForm.business_unit),
    maximazing_value: strategyForm.maximazing_value,
    expand: strategyForm.expand,
    low_carbon: strategyForm.low_carbon,
});

const submitStrategy = () => {
    if (!strategyForm.business_unit) {
        modalError.value = 'Pilih business unit terlebih dahulu.';
        return;
    }

    modalError.value = '';
    strategyProcessing.value = true;

    const requestOptions = {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            const message = firstErrorMessage(errors);

            if (message) {
                modalError.value = message;
            }
        },
        onSuccess: (page) => {
            closeModal(true);
            showSuccessAlert(
                page?.props?.flash?.success ?? 'Business strategy berhasil ditambahkan.',
            );
        },
        onFinish: () => {
            strategyProcessing.value = false;
        },
    };

    router.post(
        route('strategic-house.business-strategy.store'),
        buildPayload(),
        requestOptions,
    );
};

watch(
    () => props.groups,
    () => {
        if (!isEditMode.value) {
            resetEditableRows();
        }
    },
    { deep: true, immediate: true },
);

watch(filterOrganizationOptions, (options) => {
    if (!selectedOrganization.value) {
        return;
    }

    const selectedExists = options.some(
        (organization) => String(organization.value) === String(selectedOrganization.value),
    );

    if (!selectedExists) {
        selectedOrganization.value = '';
    }
}, { immediate: true });
</script>

<template>
    <div class="space-y-4">
        <div
            v-if="flash.error"
            class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300"
        >
            {{ flash.error }}
        </div>

        <div class="strategy-toolbar">
            <div class="strategy-toolbar__actions">
                <button
                    v-if="!isEditMode"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                    @click="openAddStrategy"
                >
                    Tambah Strategy
                </button>
                <button
                    v-if="!isEditMode"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                    @click="startEditMode"
                >
                    Edit Strategy
                </button>
                <template v-else>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        @click="cancelEditMode"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        :disabled="strategyProcessing"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-slate-900"
                        @click="saveEditMode"
                    >
                        {{ strategyProcessing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </template>
            </div>

            <div class="initiative-view-switch">
                <select v-model="selectedOrganization" class="initiative-view-select">
                    <option value="">All Business Unit</option>
                    <option
                        v-for="organization in filterOrganizationOptions"
                        :key="organization.value"
                        :value="organization.value"
                    >
                        {{ organization.label }}
                    </option>
                </select>
            </div>
        </div>

        <section
            v-if="filteredGroups.length > 0"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
        >
            <div class="overflow-x-auto">
                <h1 class="mb-4 mt-4 text-center text-lg font-bold">
                    Dual Growth Business Strategy 2025 -2029
                </h1>

                <table class="strategy-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="head-cell head-cell--business-unit">
                                <div class="strategy-head-card strategy-head-card--business-unit">
                                    <span class="strategy-head-card__title">Business Unit</span>
                                </div>
                            </th>
                            <th
                                v-if="legacyColumns.length"
                                :colspan="legacyColumns.length"
                                class="head-cell"
                            >
                                <div class="strategy-head-card strategy-head-card--legacy">
                                    <span class="strategy-head-card__title">Maximize Legacy Business</span>
                                </div>
                            </th>
                            <th
                                v-if="lowCarbonColumn"
                                rowspan="2"
                                class="head-cell head-cell--carbon"
                            >
                                <div class="strategy-head-card strategy-head-card--carbon">
                                    <span class="strategy-head-card__title">Build Low Carbon Business</span>
                                </div>
                            </th>
                        </tr>
                        <tr v-if="legacyColumns.length">
                            <th
                                v-for="column in legacyColumns"
                                :key="column.key"
                                class="head-cell"
                            >
                                <div class="strategy-head-card strategy-head-card--legacy-child">
                                    <span>{{ column.label }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="group in filteredGroups" :key="group.key">
                            <tr v-for="row in group.rows" :key="row.id">
                                <td
                                    v-if="row.show_business_unit"
                                    :rowspan="row.business_unit_rowspan"
                                    class="primary-cell"
                                >
                                    <div class="primary-cell__content">
                                        <div class="primary-label-wrapper">
                                            <span class="text-xs">{{ row.business_unit }}</span>
                                        </div>
                                        <span class="primary-cell__meta">{{ row.group_label }}</span>
                                    </div>
                                </td>

                                <td
                                    v-for="column in orderedStrategyColumns"
                                    :key="`${row.id}-${column.key}`"
                                    class="strategy-cell"
                                    :class="{ 'strategy-cell--editing': isEditMode }"
                                >
                                    <textarea
                                        v-if="isEditMode"
                                        v-model="editableRows[row.id][column.key]"
                                        rows="1"
                                        class="strategy-cell__textarea"
                                        :placeholder="`Isi ${column.label.toLowerCase()}...`"
                                    />
                                    <p
                                        v-else-if="row.values?.[column.key]"
                                        class="strategy-cell__value"
                                    >
                                        {{ row.values[column.key] }}
                                    </p>
                                    <p
                                        v-else
                                        class="strategy-cell__empty"
                                    >
                                        Belum diisi
                                    </p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        <section
            v-else
            class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
        >
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Business Strategy Not Available
            </p>
        </section>

        <ConfirmationModal
            :show="isModalVisible"
            :title="modalTitle"
            :message="modalMessage"
            type="info"
            :loading="strategyProcessing"
            :confirm-text="modalConfirmText"
            cancel-text="Batal"
            max-width="2xl"
            @close="closeModal"
            @confirm="submitStrategy"
            @after-leave="handleModalAfterLeave"
        >
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Business Unit</label>
                    <select
                        v-model="strategyForm.business_unit"
                        class="edit-select"
                    >
                        <option value="">- Pilih Business Unit -</option>
                        <option
                            v-for="organization in organizationOptions"
                            :key="`strategy-business-unit-${organization.value}`"
                            :value="organization.value"
                        >
                            {{ organization.label }}
                        </option>
                    </select>
                </div>

                <div
                    v-for="field in strategyFormFields"
                    :key="`strategy-field-${field.key}`"
                    class="space-y-1.5"
                >
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                        {{ field.label }}
                    </label>
                    <p
                        v-if="field.description"
                        class="text-[11px] text-slate-500 dark:text-slate-400"
                    >
                        {{ field.description }}
                    </p>
                    <textarea
                        v-model="strategyForm[field.key]"
                        class="edit-textarea"
                        rows="4"
                        :placeholder="`Isi ${field.label.toLowerCase()}...`"
                    />
                </div>

                <p
                    v-if="modalError"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700"
                >
                    {{ modalError }}
                </p>
            </div>
        </ConfirmationModal>
    </div>
</template>

<style scoped>
.strategy-table {
    width: 100%;
    min-width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: auto;
    background: #ffffff;
}

.strategy-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.strategy-table thead th {
    border: 0;
    background: transparent;
    padding: 0 4px 8px;
    vertical-align: stretch;
}

.head-cell--business-unit {
    width: auto;
}

.head-cell--carbon {
    width: auto;
}

.strategy-head-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 100%;
    height: 100%;
    border: 1px solid #c5d6e8;
    border-radius: 10px;
    padding: 12px 16px;
    text-align: center;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.strategy-head-card__title {
    display: block;
    line-height: 1.2;
}

.strategy-head-card--business-unit {
    min-height: 102px;
    border-color: #0f6fb7;
    background: linear-gradient(180deg, #0f6fb7 0%, #0d5ea1 100%);
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.05em;
}

.strategy-head-card--legacy {
    min-height: 40px;
    background: #e8eff8;
    color: #1a2a3a;
    font-size: 15px;
    font-weight: 700;
}

.strategy-head-card--legacy-child {
    min-height: 34px;
    background: #e8eff8;
    color: #2a4a6a;
    font-size: 13px;
    font-weight: 700;
}

.strategy-head-card--carbon {
    min-height: 84px;
    border-color: #2f5596;
    background: linear-gradient(180deg, #3b64a8 0%, #2f5596 100%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
}

.strategy-table tbody th,
.strategy-table td {
    vertical-align: top;
}

.primary-cell {
    width: auto;
    min-width: 0;
    background: #f8fbff;
    vertical-align: middle !important;
}

.primary-cell__content {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 10px;
    height: 100%;
    justify-content: center;
    color: #1e293b;
}

.primary-label-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.primary-cell__meta {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.strategy-cell {
    width: auto;
    min-width: 0;
    padding: 8px 10px;
    background: #ffffff;
}

.strategy-cell--editing {
    padding: 6px;
    background: #f8fbff;
}

.strategy-cell__value {
    font-size: 12px;
    font-weight: 700;
    line-height: 1.45;
    color: #1f2937;
    white-space: pre-wrap;
    word-break: break-word;
    margin: 0;
}

.strategy-cell__empty {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    font-style: italic;
    margin: 0;
}

.strategy-cell__textarea {
    width: 100%;
    min-height: 34px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background: #ffffff;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.5;
    color: #1f2937;
    white-space: pre-wrap;
    resize: vertical;
    transition: all 0.15s ease;
}

.strategy-cell__textarea:hover {
    border-color: #0f6fb7;
}

.strategy-cell__textarea:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.strategy-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.strategy-toolbar__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}

.initiative-view-switch {
    display: flex;
    width: 100%;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding: 2px;
    scrollbar-width: none;
}

.initiative-view-switch::-webkit-scrollbar {
    display: none;
}

.initiative-view-select,
.edit-select,
.edit-textarea {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    transition: all 0.15s ease;
}

.initiative-view-select,
.edit-select {
    appearance: none;
    cursor: pointer;
    padding: 4px 24px 4px 10px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
}

.initiative-view-select {
    flex-shrink: 0;
}

.edit-select {
    width: 100%;
}

.edit-textarea {
    width: 100%;
    min-height: 92px;
    padding: 10px 12px;
    font-weight: 600;
    line-height: 1.5;
    white-space: pre-wrap;
    resize: vertical;
}

.initiative-view-select:hover,
.edit-select:hover,
.edit-textarea:hover {
    border-color: #0f6fb7;
    color: #0f6fb7;
}

.initiative-view-select:focus,
.edit-select:focus,
.edit-textarea:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

:deep(.dark) .strategy-table thead th {
    background: transparent;
}

:deep(.dark) .strategy-head-card--business-unit {
    border-color: #1d4ed8;
    background: linear-gradient(180deg, #1d4ed8 0%, #1e40af 100%);
}

:deep(.dark) .strategy-head-card--legacy,
:deep(.dark) .strategy-head-card--legacy-child {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

:deep(.dark) .strategy-head-card--carbon {
    border-color: #3b82f6;
    background: linear-gradient(180deg, #274a87 0%, #1f3e74 100%);
}

:deep(.dark) .primary-cell {
    background: rgba(15, 23, 42, 0.55);
}

:deep(.dark) .strategy-cell {
    background: rgba(15, 23, 42, 0.35);
}

:deep(.dark) .strategy-cell--editing {
    background: rgba(15, 23, 42, 0.55);
}

:deep(.dark) .strategy-cell__value {
    color: #e2e8f0;
}

:deep(.dark) .strategy-cell__empty {
    color: #94a3b8;
}

:deep(.dark) .primary-cell__hint {
    color: #93c5fd;
}

:deep(.dark) .initiative-view-select,
:deep(.dark) .edit-select,
:deep(.dark) .edit-textarea {
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(15, 23, 42, 0.55);
    color: #cbd5e1;
}

:deep(.dark) .strategy-cell__textarea {
    border-color: rgba(255, 255, 255, 0.12);
    background: rgba(15, 23, 42, 0.8);
    color: #e2e8f0;
}

:deep(.dark) .strategy-cell__textarea:hover {
    border-color: rgba(96, 165, 250, 0.4);
}

:deep(.dark) .strategy-cell__textarea:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.12);
}
</style>