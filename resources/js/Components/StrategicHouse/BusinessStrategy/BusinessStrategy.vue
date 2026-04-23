<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
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
    headerGoals: {
        type: Object,
        default: () => ({}),
    },
    enablerGoals: {
        type: Array,
        default: () => [],
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
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();
const pageContext = usePage();

const flash = computed(() => pageContext.props.flash ?? {});
const selectedOrganization = ref('');
const selectedPeriod = ref(null);
const isEditMode = ref(false);
const showInitiatives = ref(true);
const showEnablers = ref(true);
const showLastUpdatePeriod = ref(true);
const showStrategyData = ref(true);
const selectedSource = ref('');

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

const bulletIndentedStrategyColumns = new Set(['maximazing_value', 'expand', 'low_carbon']);
const lowCarbonColumn = computed(() => orderedStrategyColumns.value.find((column) => column.key === 'low_carbon') ?? null);
const legacyColumns = computed(() => orderedStrategyColumns.value.filter((column) => column.key !== 'low_carbon'));
const visibleEnablerGoals = computed(() => (showEnablers.value ? [...(props.enablerGoals || [])] : []));
const strategyFormFields = computed(() => orderedStrategyColumns.value);
const allRows = computed(() => normalizedGroups.value.flatMap((group) => group.rows || []));
const legacyGoalTitle = computed(() => String(props.headerGoals?.legacy?.title ?? 'Maximize Legacy Business'));
const lowCarbonGoalTitle = computed(() => String(props.headerGoals?.low_carbon?.title ?? 'Build Low Carbon Business'));

const shouldIndentStrategyColumn = (columnKey) => bulletIndentedStrategyColumns.has(String(columnKey ?? ''));
const isBulletLine = (line) => String(line ?? '').trimStart().startsWith('\u2022');
const splitStrategyValueLines = (value) => String(value ?? '').split(/\r?\n/);

const mergeRowsByBusinessUnit = (rows) => {
    const mergedRows = [];
    let currentBusinessUnitKey = null;
    let currentChunk = [];

    const flushChunk = () => {
        if (!currentChunk.length) {
            return;
        }

        currentChunk.forEach((row, index) => {
            mergedRows.push({
                ...row,
                show_business_unit: index === 0,
                business_unit_row_count: currentChunk.length,
                is_last_business_unit_row: index === currentChunk.length - 1,
            });
        });

        currentChunk = [];
    };

    (rows || []).forEach((row) => {
        const businessUnitKey = `${row.group_key || 'unknown'}::${row.business_unit_id || row.business_unit || row.id}`;

        if (currentBusinessUnitKey !== null && businessUnitKey !== currentBusinessUnitKey) {
            flushChunk();
        }

        currentBusinessUnitKey = businessUnitKey;
        currentChunk.push(row);
    });

    flushChunk();

    return mergedRows;
};

const normalizeStatusLabel = (rawStatus) => {
    const value = String(rawStatus ?? '').trim();

    if (!value) {
        return null;
    }

    if (value === 'DF') return 'DF';
    if (value === 'Done') return 'Done';
    if (value === 'DT 2026') return 'DT 2026';
    if (value === 'ITSBP') return 'ITSBP';
    if (value === 'On Progress' || value === 'On Progres') return 'On Progress';
    if (value === 'On Review') return 'On Review';
    if (value === 'SH') return 'SH';

    return value;
};

const getStatusColorClass = (status) => {
    const normalizedStatus = normalizeStatusLabel(status);

    if (normalizedStatus === 'DF') return 'status-color-df';
    if (normalizedStatus === 'Done') return 'status-color-done';
    if (normalizedStatus === 'DT 2026') return 'status-color-dt2026';
    if (normalizedStatus === 'ITSBP') return 'status-color-itsbp';
    if (normalizedStatus === 'On Review') return 'status-color-onreview';
    if (normalizedStatus === 'On Progress') return 'status-color-onprogress';
    if (normalizedStatus === 'SH') return 'status-color-sh';

    return '';
};

const monthsOrder = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const formatPeriodLabel = (period) => {
    const start = String(period?.start ?? '').trim();
    const end = String(period?.end ?? '').trim();
    const year = String(period?.year ?? '').trim();
    const fallbackLabel = String(period?.label ?? '').trim();

    if (start && end && year) {
        return start === end ? `${start} ${year}` : `${start} - ${end} ${year}`;
    }

    if (start && year) {
        return `${start} ${year}`;
    }

    if (year) {
        return year;
    }

    return fallbackLabel || null;
};

const getInitiativeStatus = (initiative) => {
    if (!selectedPeriod.value) {
        return initiative?.implementation_status ?? null;
    }

    const found = (initiative?.statuses || []).find((status) => (
        status.start === selectedPeriod.value.start
        && status.end === selectedPeriod.value.end
        && status.year === selectedPeriod.value.year
    ));

    return found ? found.status : null;
};

const getInitiativePeriodLabel = (initiative) => {
    if (selectedPeriod.value) {
        return formatPeriodLabel(selectedPeriod.value);
    }

    if (!initiative?.statuses?.length) {
        return null;
    }

    const latestStatus = [...initiative.statuses].sort((left, right) => {
        if (left.year !== right.year) {
            return Number(right.year ?? 0) - Number(left.year ?? 0);
        }

        return monthsOrder.indexOf(String(right.start ?? ''))
            - monthsOrder.indexOf(String(left.start ?? ''));
    })[0];

    return formatPeriodLabel(latestStatus);
};

const initiativeSummaryHref = (initiative) => {
    const initiativeId = Number(initiative?.id ?? 0);
    const initiativeType = Number(initiative?.tipe_initiative ?? 0);

    return initiativeId > 0 && initiativeType === 1
        ? route('program-planning.program-definition.digital-initiatives.summary.index', initiativeId)
        : null;
};

const initiativeSummaryTitle = (initiative) => {
    const label = String(initiative?.code ?? initiative?.name ?? 'initiative').trim();
    return `Lihat capsule summary untuk ${label}`;
};

const initiativeBoxesGridStyle = (initiatives = []) => ({
    '--initiative-column-count': Math.min(Math.max((initiatives || []).length, 1), 2),
});

const getBusinessUnitRowspan = (row) => {
    if (!showStrategyData.value && !isEditMode.value) {
        return showInitiatives.value ? 1 : 0;
    }
    const rowCount = Number(row?.business_unit_row_count ?? 1);
    return showInitiatives.value ? rowCount + 1 : rowCount;
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

const statusDesiredOrder = ['DF', 'Done', 'DT 2026', 'ITSBP', 'On Progress', 'On Review', 'SH'];

const sourceOptions = computed(() => {
    const sourceMap = new Map();
    const initiatives = allRows.value.flatMap((row) => {
        const strat = Object.values(row.initiatives_by_key || {}).flatMap(items => items || []);
        const enabler = Object.values(row.enabler_initiatives_by_key || {}).flatMap(items => items || []);
        return [...strat, ...enabler];
    });

    initiatives.forEach(ini => {
        const id = ini.source;
        let name = ini.source_name;
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

const getVisibleInitiatives = (initiatives = []) => {
    return (initiatives || []).filter((initiative) => {
        const matchesPeriod = !selectedPeriod.value || getInitiativeStatus(initiative) !== null;
        const matchesSource = !selectedSource.value || initiative.source == selectedSource.value;
        return matchesPeriod && matchesSource;
    });
};

const filteredInitiatives = computed(() => {
    const initiatives = filteredGroups.value.flatMap((group) => (
        (group.rows || []).flatMap((row) => (
            Object.values(row.initiatives_by_key || {}).flatMap((items) => getVisibleInitiatives(items || []))
        ))
    ));

    return Array.from(
        new Map(initiatives.map((initiative) => [initiative.id, initiative])).values()
    );
});

const availableStatusOptions = computed(() => {
    const statusSet = new Set();

    filteredInitiatives.value.forEach((initiative) => {
        const label = normalizeStatusLabel(getInitiativeStatus(initiative));
        if (label) {
            statusSet.add(label);
        }
    });

    return Array.from(statusSet).sort((left, right) => {
        const leftIndex = statusDesiredOrder.indexOf(left);
        const rightIndex = statusDesiredOrder.indexOf(right);

        if (leftIndex !== -1 && rightIndex !== -1) return leftIndex - rightIndex;
        if (leftIndex !== -1) return -1;
        if (rightIndex !== -1) return 1;

        return left.localeCompare(right);
    });
});

const statusLegend = computed(() => {
    const stats = {};
    availableStatusOptions.value.forEach((label) => {
        stats[label] = 0;
    });

    filteredInitiatives.value.forEach((initiative) => {
        const label = normalizeStatusLabel(getInitiativeStatus(initiative));
        if (label && Object.prototype.hasOwnProperty.call(stats, label)) {
            stats[label] += 1;
        }
    });

    return availableStatusOptions.value.map((label) => ({
        label,
        class: getStatusColorClass(label),
        count: stats[label],
    })).filter((item) => item.count > 0);
});

const totalFilteredInitiatives = computed(() => filteredInitiatives.value.length);

const modalTitle = computed(() => 'Add Strategy');
const modalMessage = computed(() => 'Pilih business unit lalu isi arah strategy yang ingin ditambahkan.');
const modalConfirmText = computed(() => 'Add Strategy');

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

const deleteStrategy = (row) => {
    if (!row?.id || strategyProcessing.value) {
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Hapus Strategy?',
        text: `Business strategy untuk ${row.business_unit ?? 'business unit ini'} akan dihapus.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        strategyProcessing.value = true;

        router.delete(route('strategic-house.business-strategy.destroy', row.id), {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                strategyProcessing.value = false;
            },
            onSuccess: (page) => {
                showSuccessAlert(
                    page?.props?.flash?.success ?? 'Business strategy berhasil dihapus.',
                );
            },
        });
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
        <div class="strategy-toolbar">
            <div class="strategy-toolbar__actions">
                <button v-if="!isEditMode" type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:bg-white dark:text-slate-900"
                    @click="openAddStrategy">
                    Add Strategy
                </button>
                <button v-if="!isEditMode" type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                    @click="startEditMode">
                    Edit Strategy
                </button>
                <template v-else>
                    <button type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        @click="cancelEditMode">
                        Batal
                    </button>
                    <button type="button" :disabled="strategyProcessing"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-slate-900"
                        @click="saveEditMode">
                        {{ strategyProcessing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </template>
            </div>

            <div v-if="filteredGroups.length > 0" class="space-y-2.5">
                <div v-if="statusLegend.length > 0 || (statusPeriods && statusPeriods.length > 0)" class="strategy-legend">
                    <div class="flex items-center gap-1.5">
                        <span class="status-legend__title">Implementation Status:</span>
                        <select
                            v-if="statusPeriods && statusPeriods.length > 0"
                            v-model="selectedPeriod"
                            class="status-period-select"
                        >
                            <option :value="null">All (Latest)</option>
                            <option v-for="period in statusPeriods" :key="period.label" :value="period">
                                {{ period.label }}
                            </option>
                        </select>
                        <span v-else class="status-legend__title">(Latest):</span>
                    </div>
                    <div v-for="status in statusLegend" :key="`status-legend-${status.label}`"
                        class="flex items-center gap-1.5">
                        <span class="legend-swatch" :class="status.class"></span>
                        <span class="status-legend__label">
                            {{ status.label }}
                            <span class="status-legend__count">({{ status.count }})</span>
                        </span>
                    </div>
                    <div class="status-legend__total">
                        Total Digital Initiatives
                        <span class="status-legend__count">({{ totalFilteredInitiatives }})</span>
                    </div>
                </div>

                <div class="flex items-center justify-start">
                    <div class="initiative-view-switch">
                        <select v-model="selectedOrganization" class="initiative-view-select">
                            <option value="">All Business Unit</option>
                            <option v-for="organization in filterOrganizationOptions" :key="organization.value"
                                :value="organization.value">
                                {{ organization.label }}
                            </option>
                        </select>

                        <select v-model="selectedSource" class="initiative-view-select">
                            <option value="">All Initiatives</option>
                            <option v-for="source in sourceOptions" :key="source.value" :value="source.value">
                                {{ source.label }}
                            </option>
                        </select>

                        <button type="button" class="view-toggle-btn"
                            :class="{ 'view-toggle-btn--active': showStrategyData }"
                            @click="showStrategyData = !showStrategyData">
                            {{ showStrategyData ? 'Hide Strategy Data' : 'Show Strategy Data' }}
                        </button>

                        <button type="button" class="view-toggle-btn"
                            :class="{ 'view-toggle-btn--active': showInitiatives }"
                            @click="showInitiatives = !showInitiatives">
                            {{ showInitiatives ? 'Hide Initiative' : 'Show Initiative' }}
                        </button>

                        <button v-if="enablerGoals.length > 0" type="button" class="view-toggle-btn"
                            :class="{ 'view-toggle-btn--active': showEnablers }"
                            @click="showEnablers = !showEnablers">
                            {{ showEnablers ? 'Hide Enabler' : 'Show Enabler' }}
                        </button>

                        <button type="button" class="view-toggle-btn"
                            :class="{ 'view-toggle-btn--active': showLastUpdatePeriod }"
                            @click="showLastUpdatePeriod = !showLastUpdatePeriod">
                            {{ showLastUpdatePeriod ? 'Hide Periode' : 'Show Periode' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <template v-if="filteredGroups.length > 0">
            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <h1 class="mb-4 mt-4 text-center text-lg font-bold">
                        Dual Growth Business Strategy 2025 -2029
                    </h1>

                    <table class="strategy-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="head-cell head-cell--business-unit"></th>
                                <th v-if="isEditMode" rowspan="2" class="head-cell head-cell--action">
                                    <div class="strategy-head-card strategy-head-card--action">
                                        <span class="strategy-head-card__title">Action</span>
                                    </div>
                                </th>
                                <th v-if="legacyColumns.length" :colspan="legacyColumns.length" class="head-cell">
                                    <div class="strategy-head-card strategy-head-card--legacy">
                                        <span class="strategy-head-card__title">{{ legacyGoalTitle }}</span>
                                    </div>
                                </th>
                                <th v-if="lowCarbonColumn" rowspan="2" class="head-cell head-cell--carbon">
                                    <div class="strategy-head-card strategy-head-card--carbon">
                                        <span class="strategy-head-card__title">{{ lowCarbonGoalTitle }}</span>
                                    </div>
                                </th>
                                <th
                                    v-for="enabler in visibleEnablerGoals"
                                    :key="`enabler-head-${enabler.key}`"
                                    rowspan="2"
                                    class="head-cell head-cell--enabler"
                                >
                                    <div class="strategy-head-card strategy-head-card--enabler">
                                        <span class="strategy-head-card__title">{{ enabler.title }}</span>
                                    </div>
                                </th>
                            </tr>
                            <tr v-if="legacyColumns.length">
                                <th v-for="column in legacyColumns" :key="column.key" class="head-cell">
                                    <div class="strategy-head-card strategy-head-card--legacy-child">
                                        <span>{{ column.label }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <template v-for="group in filteredGroups" :key="group.key">
                                <template v-for="row in group.rows" :key="row.id">
                                    <tr v-if="showStrategyData || isEditMode">
                                        <td v-if="row.show_business_unit" :rowspan="getBusinessUnitRowspan(row)"
                                            class="primary-cell">
                                            <div class="primary-cell__content">
                                                <div class="primary-logo-wrapper" v-if="row.business_unit_logo">
                                                    <img :src="row.business_unit_logo" :alt="`${row.business_unit} logo`"
                                                        class="primary-business-unit-logo">
                                                </div>
                                                <div class="primary-label-wrapper">
                                                    <span class="primary-business-unit-name">{{ row.business_unit }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td v-if="isEditMode" class="action-cell">
                                            <button type="button" class="primary-cell__delete"
                                                :disabled="strategyProcessing" title="Hapus business strategy"
                                                @click="deleteStrategy(row)">
                                                Hapus
                                            </button>
                                        </td>

                                        <td v-for="column in orderedStrategyColumns" :key="`${row.id}-${column.key}`"
                                            class="strategy-cell" :class="{ 'strategy-cell--editing': isEditMode }">
                                            <template v-if="showStrategyData || isEditMode">
                                                <textarea v-if="isEditMode" v-model="editableRows[row.id][column.key]" rows="1"
                                                    class="strategy-cell__textarea"
                                                    :placeholder="`Isi ${column.label.toLowerCase()}...`" />
                                                <p v-else-if="row.values?.[column.key]" class="strategy-cell__value">
                                                    <span
                                                        v-for="(line, lineIndex) in splitStrategyValueLines(row.values[column.key])"
                                                        :key="`${row.id}-${column.key}-line-${lineIndex}`"
                                                        class="strategy-cell__line"
                                                        :class="{
                                                            'strategy-cell__line--bullet': shouldIndentStrategyColumn(column.key) && isBulletLine(line),
                                                        }"
                                                    >
                                                        {{ line }}
                                                    </span>
                                                </p>
                                                <p v-else class="strategy-cell__empty">
                                                    Not Available
                                                </p>
                                            </template>
                                        </td>

                                        <td
                                            v-for="enabler in row.show_business_unit ? visibleEnablerGoals : []"
                                            :key="`enabler-${row.id}-${enabler.key}`"
                                            :rowspan="getBusinessUnitRowspan(row)"
                                            class="initiative-row__cell enabler-cell"
                                        >
                                            <div class="initiative-row__content">
                                                <div
                                                    v-if="getVisibleInitiatives((row.enabler_initiatives_by_key || {})[enabler.key] || []).length"
                                                    class="initiative-row__boxes initiative-row__boxes--vertical"
                                                >
                                                    <component :is="initiativeSummaryHref(initiative) ? Link : 'div'"
                                                        v-for="initiative in getVisibleInitiatives((row.enabler_initiatives_by_key || {})[enabler.key] || [])"
                                                        :key="`enabler-initiative-${row.id}-${enabler.key}-${initiative.id}`"
                                                        :href="initiativeSummaryHref(initiative)"
                                                        :title="initiativeSummaryTitle(initiative)" class="initiative-box"
                                                        :class="[
                                                            { 'initiative-box--no-code': !initiative.code },
                                                            { 'initiative-box--clickable': initiativeSummaryHref(initiative) },
                                                        ]">
                                                        <span v-if="initiative.code" class="initiative-box__code"
                                                            :class="getStatusColorClass(getInitiativeStatus(initiative))">
                                                            {{ initiative.code }}
                                                        </span>
                                                        <span class="initiative-box__name"
                                                            :class="{ 'initiative-box__name--full': !initiative.code }">
                                                            <span class="initiative-box__label-text">
                                                                {{ initiative.name }}
                                                            </span>
                                                            <span v-if="showLastUpdatePeriod && getInitiativePeriodLabel(initiative)"
                                                                class="initiative-box__period">
                                                                {{ getInitiativePeriodLabel(initiative) }}
                                                            </span>
                                                        </span>
                                                    </component>
                                                </div>

                                                <p v-else class="initiative-row__empty">
                                                    -
                                                </p>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-if="showInitiatives && row.is_last_business_unit_row" class="initiative-row">
                                        <td v-if="!(showStrategyData || isEditMode)" :rowspan="1"
                                            class="primary-cell">
                                            <div class="primary-cell__content">
                                                <div class="primary-logo-wrapper" v-if="row.business_unit_logo">
                                                    <img :src="row.business_unit_logo" :alt="`${row.business_unit} logo`"
                                                        class="primary-business-unit-logo">
                                                </div>
                                                <div class="primary-label-wrapper">
                                                    <span class="primary-business-unit-name">{{ row.business_unit }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td v-if="isEditMode" class="initiative-row__action-spacer"></td>
                                        <td v-for="column in orderedStrategyColumns" :key="`initiative-${row.id}-${column.key}`"
                                            class="initiative-row__cell">
                                            <div class="initiative-row__content">
                                                <div v-if="getVisibleInitiatives((row.initiatives_by_key || {})[column.key] || []).length"
                                                    class="initiative-row__boxes"
                                                    :style="initiativeBoxesGridStyle(getVisibleInitiatives((row.initiatives_by_key || {})[column.key] || []))">
                                                    <component :is="initiativeSummaryHref(initiative) ? Link : 'div'"
                                                        v-for="initiative in getVisibleInitiatives((row.initiatives_by_key || {})[column.key] || [])"
                                                        :key="`initiative-${row.id}-${column.key}-${initiative.id}`"
                                                        :href="initiativeSummaryHref(initiative)"
                                                        :title="initiativeSummaryTitle(initiative)" class="initiative-box"
                                                        :class="[
                                                            { 'initiative-box--no-code': !initiative.code },
                                                            { 'initiative-box--clickable': initiativeSummaryHref(initiative) },
                                                        ]">
                                                        <span v-if="initiative.code" class="initiative-box__code"
                                                            :class="getStatusColorClass(getInitiativeStatus(initiative))">
                                                            {{ initiative.code }}
                                                        </span>
                                                        <span class="initiative-box__name"
                                                            :class="{ 'initiative-box__name--full': !initiative.code }">
                                                            <span class="initiative-box__label-text">
                                                                {{ initiative.name }}
                                                            </span>
                                                            <span
                                                                v-if="showLastUpdatePeriod && getInitiativePeriodLabel(initiative)"
                                                                class="initiative-box__period">
                                                                {{ getInitiativePeriodLabel(initiative) }}
                                                            </span>
                                                        </span>
                                                    </component>
                                                </div>

                                                <p v-else class="initiative-row__empty">
                                                    -
                                                </p>
                                            </div>
                                        </td>
                                        
                                        <td
                                            v-if="!(showStrategyData || isEditMode)"
                                            v-for="enabler in visibleEnablerGoals"
                                            :key="`enabler-hidden-${row.id}-${enabler.key}`"
                                            :rowspan="1"
                                            class="initiative-row__cell enabler-cell"
                                        >
                                            <div class="initiative-row__content">
                                                <div
                                                    v-if="getVisibleInitiatives((row.enabler_initiatives_by_key || {})[enabler.key] || []).length"
                                                    class="initiative-row__boxes initiative-row__boxes--vertical"
                                                >
                                                    <component :is="initiativeSummaryHref(initiative) ? Link : 'div'"
                                                        v-for="initiative in getVisibleInitiatives((row.enabler_initiatives_by_key || {})[enabler.key] || [])"
                                                        :key="`enabler-initiative-hidden-${row.id}-${enabler.key}-${initiative.id}`"
                                                        :href="initiativeSummaryHref(initiative)"
                                                        :title="initiativeSummaryTitle(initiative)" class="initiative-box"
                                                        :class="[
                                                            { 'initiative-box--no-code': !initiative.code },
                                                            { 'initiative-box--clickable': initiativeSummaryHref(initiative) },
                                                        ]">
                                                        <span v-if="initiative.code" class="initiative-box__code"
                                                            :class="getStatusColorClass(getInitiativeStatus(initiative))">
                                                            {{ initiative.code }}
                                                        </span>
                                                        <span class="initiative-box__name"
                                                            :class="{ 'initiative-box__name--full': !initiative.code }">
                                                            <span class="initiative-box__label-text">
                                                                {{ initiative.name }}
                                                            </span>
                                                            <span v-if="showLastUpdatePeriod && getInitiativePeriodLabel(initiative)"
                                                                class="initiative-box__period">
                                                                {{ getInitiativePeriodLabel(initiative) }}
                                                            </span>
                                                        </span>
                                                    </component>
                                                </div>

                                                <p v-else class="initiative-row__empty">
                                                    -
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </template>

        <section v-else
            class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Business Strategy Not Available
            </p>
        </section>

        <ConfirmationModal :show="isModalVisible" :title="modalTitle" :message="modalMessage" type="info"
            :loading="strategyProcessing" :confirm-text="modalConfirmText" cancel-text="Batal" max-width="2xl"
            @close="closeModal" @confirm="submitStrategy" @after-leave="handleModalAfterLeave">
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Business Unit</label>
                    <select v-model="strategyForm.business_unit" class="edit-select">
                        <option value="">- Pilih Business Unit -</option>
                        <option v-for="organization in organizationOptions"
                            :key="`strategy-business-unit-${organization.value}`" :value="organization.value">
                            {{ organization.label }}
                        </option>
                    </select>
                </div>

                <div v-for="field in strategyFormFields" :key="`strategy-field-${field.key}`" class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">
                        {{ field.label }}
                    </label>
                    <p v-if="field.description" class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ field.description }}
                    </p>
                    <textarea v-model="strategyForm[field.key]" class="edit-textarea" rows="4"
                        :placeholder="`Isi ${field.label.toLowerCase()}...`" />
                </div>

                <p v-if="modalError"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700">
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

.head-cell--enabler {
    width: 120px;
}

.head-cell--action {
    width: 92px;
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

.strategy-head-card--enabler {
    min-height: 64px;
    border-color: #b9c9dd;
    background: linear-gradient(180deg, #edf3fb 0%, #dfe9f7 100%);
    color: #28476b;
    font-size: 13px;
    font-weight: 800;
}

.strategy-head-card--action {
    min-height: 102px;
    border-color: #cbd5e1;
    background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
    color: #334155;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.04em;
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
    align-items: center;
    gap: 10px;
    padding: 10px;
    height: 100%;
    justify-content: center;
    color: #1e293b;
    text-align: center;
}

.primary-logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.primary-business-unit-logo {
    display: block;
    max-width: 120px;
    max-height: 42px;
    width: auto;
    height: auto;
    object-fit: contain;
}

.primary-label-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
}

.primary-business-unit-name {
    font-size: 12px;
    font-weight: 800;
    line-height: 1.2;
}

.primary-cell__delete {
    border: 1px solid #fecaca;
    border-radius: 999px;
    background: #fff1f2;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 700;
    color: #b91c1c;
    transition: all 0.15s ease;
}

.primary-cell__delete:hover {
    border-color: #fca5a5;
    background: #ffe4e6;
}

.primary-cell__delete:disabled {
    cursor: not-allowed;
    opacity: 0.6;
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

.action-cell {
    width: 92px;
    padding: 8px;
    background: #f8fbff;
    text-align: center;
    vertical-align: middle !important;
}

.initiative-row__action-spacer {
    width: 92px;
    padding: 0;
    background: #f8fbff;
}

.initiative-row__cell {
    padding: 8px;
    background: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
}

.enabler-cell {
    min-width: 120px;
}

.initiative-row__content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.initiative-row__header {
    display: flex;
    align-items: center;
    gap: 8px;
}

.initiative-row__title {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #475569;
}

.initiative-row__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 999px;
    background: rgba(15, 111, 183, 0.08);
    border: 1px solid rgba(15, 111, 183, 0.18);
    font-size: 10px;
    font-weight: 800;
    color: #0f6fb7;
}

.initiative-row__boxes {
    display: grid;
    width: 100%;
    grid-template-columns: repeat(var(--initiative-column-count, 2), minmax(0, 1fr));
    grid-auto-flow: row;
    gap: 8px;
    align-items: stretch;
}

.initiative-row__boxes--vertical {
    grid-template-columns: 1fr;
}

.initiative-row__empty {
    margin: 0;
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    font-style: italic;
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
    padding: 2px 4px;
    font-weight: 700;
    letter-spacing: 0.01em;
    white-space: nowrap;
    min-width: 28px;
    width: 28px;
    flex-shrink: 0;
}

.initiative-box__name {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    max-width: none;
    padding: 2px 16px 2px 5px;
    word-break: break-word;
}

.initiative-box__name--full {
    grid-column: 1 / -1;
    padding-left: 5px;
}

.initiative-box__label-text {
    line-height: 1.1;
}

.initiative-box__period {
    font-size: 7.5px;
    font-weight: 700;
    font-style: italic;
    color: #64748b;
    margin-top: 1px;
}

.strategy-cell__value {
    font-size: 12px;
    font-weight: 400;
    line-height: 1.45;
    color: #1f2937;
    margin: 0;
    word-break: break-word;
}

.strategy-cell__line {
    display: block;
    white-space: break-spaces;
    tab-size: 4;
}

.strategy-cell__line--bullet {
    padding-left: 14px;
    text-indent: -14px;
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
    font-weight: 400;
    line-height: 1.5;
    color: #1f2937;
    white-space: break-spaces;
    tab-size: 4;
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
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
}

.strategy-toolbar__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}

.strategy-legend {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px 16px;
}

.initiative-view-switch {
    display: flex;
    border-radius: 12px;
    background: transparent;
    width: 100%;
    flex-wrap: nowrap;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding: 2px 10px 2px 2px;
    scrollbar-width: none;
}

.initiative-view-switch::-webkit-scrollbar {
    display: none;
}

.initiative-view-select,
.edit-select,
.edit-textarea,
.status-period-select {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    transition: all 0.15s ease;
}

.initiative-view-select,
.edit-select,
.status-period-select {
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

.status-period-select {
    height: auto;
    padding-top: 2px;
    padding-bottom: 2px;
    font-size: 10px;
    color: #2563eb;
    background-color: #f8fafc;
    background-size: 12px;
}

.view-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
    transition: all 0.15s ease;
    cursor: pointer;
}

.view-toggle-btn:hover {
    border-color: #0f6fb7;
    background: #f8fafc;
    color: #0f6fb7;
}

.view-toggle-btn--active {
    border-color: #0f6fb7;
    background: #0f6fb7;
    color: #ffffff;
}

.view-toggle-btn--active:hover {
    background: #0d5ea1;
    border-color: #0d5ea1;
    color: #ffffff;
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
.edit-textarea:hover,
.status-period-select:hover {
    border-color: #0f6fb7;
    color: #0f6fb7;
}

.initiative-view-select:focus,
.edit-select:focus,
.edit-textarea:focus,
.status-period-select:focus {
    outline: none;
    border-color: #0f6fb7;
    box-shadow: 0 0 0 3px rgba(15, 111, 183, 0.1);
}

.status-legend__title {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #94a3b8;
}

.status-legend__label {
    font-size: 10px;
    font-weight: 600;
    color: #64748b;
}

.status-legend__count {
    font-weight: 500;
    color: #94a3b8;
}

.status-legend__total {
    margin-left: 4px;
    padding-left: 16px;
    border-left: 1px solid #cbd5e1;
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
}

.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12);
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

:deep(.dark) .strategy-head-card--enabler {
    border-color: #334155;
    background: linear-gradient(180deg, #1f2d40 0%, #182435 100%);
    color: #dbeafe;
}

:deep(.dark) .strategy-head-card--action {
    border-color: #334155;
    background: #1e293b;
    color: #e2e8f0;
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

:deep(.dark) .action-cell {
    background: rgba(15, 23, 42, 0.55);
}

:deep(.dark) .strategy-legend {
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(23, 23, 23, 0.9);
}

:deep(.dark) .initiative-row__action-spacer,
:deep(.dark) .initiative-row__cell {
    background: rgba(15, 23, 42, 0.55);
}

:deep(.dark) .initiative-row__title {
    color: #cbd5e1;
}

:deep(.dark) .initiative-row__count {
    background: rgba(96, 165, 250, 0.12);
    border-color: rgba(96, 165, 250, 0.28);
    color: #93c5fd;
}

:deep(.dark) .initiative-row__empty {
    color: #94a3b8;
}

:deep(.dark) .initiative-box {
    background: rgba(15, 23, 42, 0.82);
    color: #e2e8f0;
}

:deep(.dark) .initiative-box__code {
    background: rgba(255, 255, 255, 0.08);
    border-right-color: rgba(255, 255, 255, 0.08);
}

:deep(.dark) .initiative-box__period {
    color: #94a3b8;
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

:deep(.dark) .primary-cell__delete {
    border-color: rgba(248, 113, 113, 0.35);
    background: rgba(127, 29, 29, 0.25);
    color: #fecaca;
}

:deep(.dark) .initiative-view-select,
:deep(.dark) .initiative-view-toggle,
:deep(.dark) .view-toggle-btn,
:deep(.dark) .status-period-select,
:deep(.dark) .edit-select,
:deep(.dark) .edit-textarea {
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(15, 23, 42, 0.55);
    color: #cbd5e1;
}

:deep(.dark) .view-toggle-btn:hover {
    border-color: rgba(96, 165, 250, 0.4);
    background: rgba(30, 41, 59, 0.85);
    color: #93c5fd;
}

:deep(.dark) .view-toggle-btn--active {
    border-color: #2563eb;
    background: #1d4ed8;
    color: #ffffff;
}

:deep(.dark) .view-toggle-btn--active:hover {
    border-color: #2563eb;
    background: #2563eb;
    color: #ffffff;
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

:deep(.dark) .status-legend__title {
    color: #64748b;
}

:deep(.dark) .status-legend__label {
    color: #cbd5e1;
}

:deep(.dark) .status-legend__count {
    color: #94a3b8;
}

:deep(.dark) .status-legend__total {
    border-left-color: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
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

.status-color-onprogress {
    background-color: #2563eb !important;
    color: #ffffff !important;
    border-color: #1d4ed8 !important;
}

.status-color-sh {
    background-color: #ef4444 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}
</style>
