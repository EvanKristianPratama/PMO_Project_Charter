<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const route = useRouteHelper();
const emit = defineEmits(['cancel-add-support']);

const props = defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    editable: {
        type: Boolean,
        default: false,
    },
    digitalOptions: {
        type: Array,
        default: () => [],
    },
    itOptions: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const EMPTY_NOTE_LABEL = 'Belum ada catatan dukungan.';
const BUSINESS_UNIT_STORAGE_KEY = 'initiative-support.show-business-unit';

const addForm = reactive({
    digital_ids: [],
    it_ids: [],
    notes: '',
});

const resolveInitialBusinessUnitVisibility = () => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.localStorage.getItem(BUSINESS_UNIT_STORAGE_KEY) === 'true';
};

const saveProcessing = ref(false);
const deleteProcessing = ref(false);
const selectedNote = ref('');
const selectedCoE = ref('');
const selectedOrg = ref('');
const selectedSource = ref('');
const showStatusColors = ref(false);
const showLastUpdatePeriod = ref(false);
const selectedStatus = ref('');
const selectedPeriod = ref(null);
const showBusinessUnit = ref(resolveInitialBusinessUnitVisibility());
const activeModal = ref('');
const isModalVisible = ref(false);
const modalError = ref('');
const shouldExitEditOnCancel = ref(false);
const deleteTarget = reactive({
    label: '',
    mappingIds: [],
    digitalCount: 0,
    itCount: 0,
});

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

const getInitiativeStatus = (initiative) => {
    if (!selectedPeriod.value) {
        return initiative?.implementation_status;
    }

    const found = (initiative?.statuses || []).find(s =>
        s.start === selectedPeriod.value.start &&
        s.end === selectedPeriod.value.end &&
        s.year === selectedPeriod.value.year
    );

    return found ? found.status : null;
};

const monthsOrder = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const formatPeriodLabel = (period) => {
    const start = String(period?.start ?? '').trim();
    const end = String(period?.end ?? '').trim();
    const year = String(period?.year ?? '').trim();
    const fallbackLabel = String(period?.label ?? '').trim();

    if (start && end && year) {
        return start === end
            ? `${start} ${year}`
            : `${start} - ${end} ${year}`;
    }

    if (start && year) {
        return `${start} ${year}`;
    }

    if (year) {
        return year;
    }

    return fallbackLabel || null;
};

const getInitiativePeriodLabel = (initiative) => {
    if (selectedPeriod.value) {
        return formatPeriodLabel(selectedPeriod.value);
    }

    if (!initiative?.statuses || initiative.statuses.length === 0) return null;

    const sorted = [...initiative.statuses].sort((a, b) => {
        if (a.year !== b.year) return b.year - a.year;
        return monthsOrder.indexOf(b.start) - monthsOrder.indexOf(a.start);
    });

    const latest = sorted[0];
    return formatPeriodLabel(latest);
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

const availableStatusOptions = computed(() => {
    const statusSet = new Set();
    props.groups.forEach(group => {
        const checkInitiative = (ini) => {
            if (!ini) return;
            const latest = normalizeStatusLabel(ini.implementation_status);
            if (latest) statusSet.add(latest);
            (ini.statuses ?? []).forEach(s => {
                const label = normalizeStatusLabel(s.status);
                if (label) statusSet.add(label);
            });
        };
        (group.digital_initiatives ?? []).forEach(checkInitiative);
        (group.it_initiatives ?? []).forEach(checkInitiative);
    });

    return Array.from(statusSet).sort((a, b) => {
        const idxA = statusDesiredOrder.indexOf(a);
        const idxB = statusDesiredOrder.indexOf(b);
        if (idxA !== -1 && idxB !== -1) return idxA - idxB;
        if (idxA !== -1) return -1;
        if (idxB !== -1) return 1;
        return a.localeCompare(b);
    });
});

const statusLegend = computed(() => {
    const uniqueDigitalIds = new Set();
    const uniqueItIds = new Set();
    const statusCounts = {};
    availableStatusOptions.value.forEach(l => statusCounts[l] = 0);

    displayGroups.value.forEach(groupRow => {
        groupRow.digitalRows.forEach(row => {
            const d = row.digital;
            if (d && !uniqueDigitalIds.has(d.id)) {
                uniqueDigitalIds.add(d.id);
                const label = normalizeStatusLabel(getInitiativeStatus(d));
                if (label && statusCounts.hasOwnProperty(label)) {
                    statusCounts[label]++;
                }
            }
        });

        (groupRow.group?.it_initiatives ?? []).forEach(it => {
            if (it && !uniqueItIds.has(it.id)) {
                uniqueItIds.add(it.id);
                const label = normalizeStatusLabel(getInitiativeStatus(it));
                if (label && statusCounts.hasOwnProperty(label)) {
                    statusCounts[label]++;
                }
            }
        });
    });

    return availableStatusOptions.value.map((label) => ({
        label,
        class: getStatusColorClass(label),
        count: statusCounts[label],
    })).filter(item => item.count > 0);
});

const displayStatusLegend = computed(() => {
    if (statusLegend.value.length > 0) return statusLegend.value;

    if (selectedPeriod.value && availableStatusOptions.value.length > 0) {
        return availableStatusOptions.value.map((label) => ({
            label,
            class: getStatusColorClass(label),
            count: 0,
        }));
    }

    return [];
});

const noteOptions = computed(() => {
    const notes = new Set();
    props.groups.forEach(group => {
        const note = (group.note ?? '').trim();
        if (note) {
            notes.add(note);
        }
    });
    return Array.from(notes).sort();
});

const getCategoryByNote = (note) => {
    const text = (note ?? '').toLowerCase();
    if (text.includes('data analystic & ai')) return 'AI/Analytics';
    if (text.includes('iot')) return 'IoT';
    if (text.includes('cloud computing')) return 'Cloud & Advanced Computing';
    return null;
};

const coeOptions = computed(() => {
    const coes = new Set();
    props.groups.forEach(group => {
        const noteCategory = getCategoryByNote(group.note);
        (group.digital_initiatives ?? []).forEach(digital => {
            const coe = normalizeTechCapability(digital?.coe_name);
            const finalCategory = coe === 'CoE Not Available' ? coe : (noteCategory || coe);
            coes.add(finalCategory);
        });
    });
    return Array.from(coes).sort();
});

const orgOptions = computed(() => {
    const orgMap = new Map();
    props.groups.forEach(group => {
        (group.digital_initiatives ?? []).forEach(digital => {
            const org = initiativeBusinessUnitLabel(digital);
            if (org !== '-') {
                const groupLabel = digital.groub_id === 2 ? 'Sub Holding' : 'Holding';
                if (!orgMap.has(org)) {
                    orgMap.set(org, groupLabel);
                }
            }
        });
    });

    const individualOptions = Array.from(orgMap.entries())
        .map(([name, group]) => ({
            value: name,
            label: `${group} - ${name}`
        }))
        .sort((a, b) => a.label.localeCompare(b.label));

    return [
        { value: 'all_holding', label: 'All Holding' },
        { value: 'all_subholding', label: 'All Sub Holding' },
        ...individualOptions
    ];
});

const sourceOptions = computed(() => {
    const sourceMap = new Map();
    props.groups.forEach(group => {
        (group.digital_initiatives ?? []).forEach(digital => {
            const id = digital.source;
            let name = digital.source_name;

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

const createMappingKey = (digitalId, itId) => {
    return `${Number(digitalId ?? 0)}:${Number(itId ?? 0)}`;
};

const firstErrorMessage = (errors = {}) => {
    return Object.values(errors)
        .flat()
        .map((value) => String(value ?? '').trim())
        .find((value) => value !== '');
};

const normalizeTechCapability = (value) => {
    if (value === null || value === undefined) return 'CoE Not Available';

    const name = String(value).trim();
    const upper = name.toUpperCase();

    if (name === '' || upper === 'NO COE' || upper === 'NULL' || upper === 'UNDEFINED' || name === '-') {
        return 'CoE Not Available';
    }

    return name;
};

const stripInitiativePrefix = (name, code) => {
    const rawName = String(name ?? '').trim();
    const rawCode = String(code ?? '').trim().replace(/#/g, '');

    if (rawName === '' || rawCode === '') {
        return rawName;
    }

    const escapedCode = rawCode.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const pattern = new RegExp(`^\\s*(\\[\\s*)?${escapedCode}(\\s*\\])?\\s*[-|.:)]?\\s*`, 'i');
    const cleanedName = rawName.replace(pattern, '').trim();

    return cleanedName !== '' ? cleanedName : rawName;
};

const initiativeDisplayCode = (initiative) => {
    return String(initiative?.code ?? '').trim().replace(/#/g, '');
};

const initiativeDisplayName = (initiative) => {
    const code = initiativeDisplayCode(initiative);
    const name = stripInitiativePrefix(initiative?.name ?? '', code);

    return name || '-';
};

const initiativeOptionLabel = (initiative) => {
    const code = initiativeDisplayCode(initiative);
    const name = initiativeDisplayName(initiative);

    if (code !== '' && name !== '-') {
        return `[${code}] ${name}`;
    }

    return name !== '-' ? name : code || '-';
};

const initiativeBusinessUnitLabel = (initiative) => {
    const businessUnit = String(initiative?.business_unit ?? '').trim();

    return businessUnit !== '' ? businessUnit : '-';
};

const existingMappingKeys = computed(() => {
    return new Set(
        props.groups.flatMap((group) => {
            return (group?.mappings ?? []).map((mapping) => {
                return createMappingKey(mapping?.digital_id, mapping?.it_id);
            });
        }),
    );
});

const displayGroups = computed(() => {
    let groups = props.groups;

    if (selectedNote.value !== '') {
        groups = groups.filter(group =>
            (group.note ?? '').trim() === selectedNote.value
        );
    }

    return groups
        .map((group, groupIndex) => {
            const noteCategory = getCategoryByNote(group.note);
            let digitalRows = (group?.digital_initiatives ?? [])
                .filter((digital) => {
                    if (!Boolean(digital?.id)) return false;
                    if (selectedPeriod.value && getInitiativeStatus(digital) === null) return false;
                    return true;
                })
                .map((digital, digitalIndex) => {
                    const coe = normalizeTechCapability(digital?.coe_name);
                    const finalCategory = coe === 'CoE Not Available' ? coe : (noteCategory || coe);

                    return {
                        key: `${group?.group_key ?? 'support-group'}:${digital.id}:${digitalIndex}`,
                        digital,
                        techCapability: finalCategory,
                        org: initiativeBusinessUnitLabel(digital),
                        isFirstRow: digitalIndex === 0,
                    };
                });
            if (selectedCoE.value !== '') {
                digitalRows = digitalRows.filter(row => row.techCapability === selectedCoE.value);
            }

            if (selectedOrg.value === 'all_holding') {
                digitalRows = digitalRows.filter(row => row.digital.groub_id !== 2);
            } else if (selectedOrg.value === 'all_subholding') {
                digitalRows = digitalRows.filter(row => row.digital.groub_id === 2);
            } else if (selectedOrg.value !== '') {
                digitalRows = digitalRows.filter(row => row.org === selectedOrg.value);
            }

            if (selectedSource.value !== '') {
                digitalRows = digitalRows.filter(row => row.digital.source == selectedSource.value);
            }

            if (selectedStatus.value !== '') {
                digitalRows = digitalRows.filter(row => {
                    const status = normalizeStatusLabel(getInitiativeStatus(row.digital));
                    return status === selectedStatus.value;
                });
            }

            let groupItInitiatives = group?.it_initiatives ?? [];
            if (selectedPeriod.value) {
                groupItInitiatives = groupItInitiatives.filter(it => getInitiativeStatus(it) !== null);
            }
            if (selectedStatus.value !== '') {
                groupItInitiatives = groupItInitiatives.filter(it => {
                    const status = normalizeStatusLabel(getInitiativeStatus(it));
                    return status === selectedStatus.value;
                });
            }

            return {
                key: group?.group_key ?? `support-group-${groupIndex}`,
                group: { ...group, it_initiatives: groupItInitiatives },
                digitalRows,
            };
        })
        .filter((group) => group.digitalRows.length > 0 || group.group.it_initiatives.length > 0)
        .sort((a, b) => {
            const aHasNotAvailable = a.digitalRows.some(row => row.techCapability === 'CoE Not Available');
            const bHasNotAvailable = b.digitalRows.some(row => row.techCapability === 'CoE Not Available');

            if (aHasNotAvailable && !bHasNotAvailable) return 1;
            if (!aHasNotAvailable && bHasNotAvailable) return -1;
            return 0;
        })
        .map((group, index, groups) => {
            const finalDigitalRows = group.digitalRows.map((row, idx) => ({
                ...row,
                isFirstRow: idx === 0,
            }));

            return {
                ...group,
                digitalRows: finalDigitalRows,
                rowSpan: finalDigitalRows.length,
                isLastGroup: index === groups.length - 1,
            };
        });
});

const coeSpanMap = computed(() => {
    const map = new Map();
    const allDigitalRows = displayGroups.value.flatMap((group) => group.digitalRows);

    let i = 0;
    while (i < allDigitalRows.length) {
        const startRow = allDigitalRows[i];
        const uniqueDigitalIds = new Set();
        let j = i;

        while (
            j < allDigitalRows.length &&
            allDigitalRows[j].techCapability === startRow.techCapability
        ) {
            if (allDigitalRows[j].digital?.id) {
                uniqueDigitalIds.add(allDigitalRows[j].digital.id);
            }
            j++;
        }

        map.set(startRow.key, {
            span: j - i,
            isLastRow: j === allDigitalRows.length,
            digitalCount: uniqueDigitalIds.size,
        });

        i = j;
    }

    return map;
});

const hasGroups = computed(() => displayGroups.value.length > 0);

const totalDigitalCount = computed(() => {
    return new Set(
        displayGroups.value.flatMap((groupRow) => {
            return groupRow.digitalRows
                .map((row) => Number(row.digital?.id))
                .filter((id) => Number.isInteger(id) && id > 0);
        }),
    ).size;
});

const totalItCount = computed(() => {
    return new Set(
        displayGroups.value.flatMap((item) => {
            return (item.group?.it_initiatives ?? [])
                .map((initiative) => Number(initiative?.id))
                .filter((id) => Number.isInteger(id) && id > 0);
        }),
    ).size;
});

const selectedDigitalInitiatives = computed(() => {
    return addForm.digital_ids
        .map((initiativeId) => {
            return props.digitalOptions.find((option) => Number(option?.id) === Number(initiativeId)) ?? {
                id: Number(initiativeId),
                code: '',
                name: `Digital Initiative ${initiativeId}`,
            };
        });
});

const selectedItInitiatives = computed(() => {
    return addForm.it_ids
        .map((initiativeId) => {
            return props.itOptions.find((option) => Number(option?.id) === Number(initiativeId)) ?? {
                id: Number(initiativeId),
                code: '',
                name: `IT Initiative ${initiativeId}`,
            };
        });
});

const totalPendingPairs = computed(() => addForm.digital_ids.length * addForm.it_ids.length);

const duplicatePendingPairs = computed(() => {
    let totalDuplicates = 0;

    addForm.digital_ids.forEach((digitalId) => {
        addForm.it_ids.forEach((itId) => {
            if (existingMappingKeys.value.has(createMappingKey(digitalId, itId))) {
                totalDuplicates += 1;
            }
        });
    });

    return totalDuplicates;
});

const newPendingPairs = computed(() => Math.max(0, totalPendingPairs.value - duplicatePendingPairs.value));

const isAddModal = computed(() => activeModal.value === 'add-support');
const modalTitle = computed(() => (activeModal.value === 'delete-group' ? 'Hapus Grup Support' : 'Tambah Initiative Support'));
const businessUnitToggleLabel = computed(() => (
    showBusinessUnit.value ? 'Business Unit' : 'Business Unit'
));
const modalMessage = computed(() => {
    if (activeModal.value === 'delete-group') {
        return 'Seluruh mapping pada grup support ini akan dihapus.';
    }

    return 'Pilih satu atau lebih Digital Initiative, tentukan IT Initiative pendukung, lalu isi catatan dukungannya bila diperlukan.';
});
const modalType = computed(() => (isAddModal.value ? 'info' : 'warning'));
const modalConfirmText = computed(() => (isAddModal.value ? 'Simpan' : 'Hapus'));
const modalLoading = computed(() => (isAddModal.value ? saveProcessing.value : deleteProcessing.value));

const resetAddForm = () => {
    addForm.digital_ids = [];
    addForm.it_ids = [];
    addForm.notes = '';
};

const resetDeleteTarget = () => {
    deleteTarget.label = '';
    deleteTarget.mappingIds = [];
    deleteTarget.digitalCount = 0;
    deleteTarget.itCount = 0;
};

const closeModal = (force = false) => {
    if (modalLoading.value && !force) {
        return;
    }

    const shouldExitEdit = activeModal.value === 'add-support' && shouldExitEditOnCancel.value && !force;
    isModalVisible.value = false;

    if (shouldExitEdit) {
        emit('cancel-add-support');
    }
};

const handleModalAfterLeave = () => {
    if (isAddModal.value) {
        resetAddForm();
    }

    activeModal.value = '';
    modalError.value = '';
    shouldExitEditOnCancel.value = false;
    resetDeleteTarget();
};

const openAddSupportModal = (options = {}) => {
    resetAddForm();
    resetDeleteTarget();
    modalError.value = '';
    shouldExitEditOnCancel.value = Boolean(options.exitEditOnCancel);
    activeModal.value = 'add-support';
    isModalVisible.value = true;
};

const openDeleteGroupModal = (group) => {
    resetDeleteTarget();
    deleteTarget.label = String(group?.note_label ?? EMPTY_NOTE_LABEL);
    deleteTarget.mappingIds = Array.isArray(group?.mapping_ids)
        ? group.mapping_ids.map((value) => Number(value)).filter((value) => Number.isInteger(value) && value > 0)
        : [];
    deleteTarget.digitalCount = Number(group?.digital_initiatives?.length ?? 0);
    deleteTarget.itCount = Number(group?.it_initiatives?.length ?? 0);
    modalError.value = '';
    shouldExitEditOnCancel.value = false;
    activeModal.value = 'delete-group';
    isModalVisible.value = true;
};

const addSelection = (field, initiativeId) => {
    const numericId = Number(initiativeId);

    if (!Number.isInteger(numericId) || numericId <= 0 || addForm[field].includes(numericId)) {
        return;
    }

    addForm[field] = [...addForm[field], numericId];
};

const removeSelection = (field, initiativeId) => {
    addForm[field] = addForm[field].filter((value) => Number(value) !== Number(initiativeId));
};

const onDigitalSelect = (event) => {
    const selectedValue = event?.target?.value ?? '';

    event.target.value = '';
    addSelection('digital_ids', selectedValue);
};

const onItSelect = (event) => {
    const selectedValue = event?.target?.value ?? '';

    event.target.value = '';
    addSelection('it_ids', selectedValue);
};

const submitSupport = () => {
    if (addForm.digital_ids.length === 0) {
        modalError.value = 'Pilih minimal satu Digital Initiative.';
        return;
    }

    if (addForm.it_ids.length === 0) {
        modalError.value = 'Pilih minimal satu IT Initiative.';
        return;
    }

    if (newPendingPairs.value === 0) {
        modalError.value = 'Semua kombinasi Digital Initiative dan IT Initiative yang dipilih sudah ada.';
        return;
    }

    modalError.value = '';
    saveProcessing.value = true;

    router.post(route('strategic-house.initiative-support.store'), {
        digital_ids: addForm.digital_ids.map((value) => Number(value)),
        it_ids: addForm.it_ids.map((value) => Number(value)),
        notes: String(addForm.notes ?? '').trim() || null,
    }, {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            const message = firstErrorMessage(errors);

            if (message) {
                modalError.value = message;
            }
        },
        onSuccess: () => {
            closeModal(true);
            resetAddForm();
        },
        onFinish: () => {
            saveProcessing.value = false;
        },
    });
};

const confirmDeleteGroup = () => {
    if (deleteTarget.mappingIds.length === 0) {
        modalError.value = 'Tidak ada mapping yang bisa dihapus pada grup ini.';
        return;
    }

    modalError.value = '';
    deleteProcessing.value = true;

    router.post(route('strategic-house.initiative-support.mappings.destroy'), {
        mapping_ids: deleteTarget.mappingIds,
    }, {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            const message = firstErrorMessage(errors);

            if (message) {
                modalError.value = message;
            }
        },
        onSuccess: () => {
            closeModal(true);
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
};

const handleModalConfirm = () => {
    if (activeModal.value === 'delete-group') {
        confirmDeleteGroup();
        return;
    }

    submitSupport();
};

const toggleBusinessUnit = () => {
    showBusinessUnit.value = !showBusinessUnit.value;
};

watch(
    () => props.editable,
    (editable) => {
        if (!editable && isModalVisible.value) {
            closeModal(true);
        }
    },
);

watch(showBusinessUnit, (value) => {
    if (typeof window === 'undefined') {
        return;
    }

    window.localStorage.setItem(BUSINESS_UNIT_STORAGE_KEY, value ? 'true' : 'false');
});

defineExpose({
    openAddSupportModal,
});
</script>

<template>
    <div class="space-y-5">
        <section class="space-y-4">
            <!-- Row 1: Metrics -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="support-metric">{{ displayGroups.length }} Tech Capability</span>
                    <span class="support-metric">{{ totalDigitalCount }} Digital Initiatives</span>
                    <span class="support-metric">{{ totalItCount }} IT Initiatives</span>
                </div>
            </div>

            <!-- Row 2: Status Implementation Legend -->
            <div v-if="showStatusColors && (displayStatusLegend.length > 0 || (statusPeriods && statusPeriods.length > 0))"
                class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1.5 border-t border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-1.5">
                    <span
                        class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Implementation Status:</span>
                    <select v-if="statusPeriods && statusPeriods.length > 0" v-model="selectedPeriod"
                        class="text-[10px] font-bold bg-slate-50 dark:bg-slate-800/50 border-none rounded focus:ring-0 cursor-pointer text-blue-600 dark:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors py-0.5 px-1.5 h-auto leading-none">
                        <option :value="null">All (Latest)</option>
                        <option v-for="period in statusPeriods" :key="period.label" :value="period">
                            {{ period.label }}
                        </option>
                    </select>
                    <span v-else class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">(Latest
                        Update):</span>
                </div>
                <div v-for="status in displayStatusLegend" :key="`status-legend-${status.label}`"
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

            <!-- Row 3: Filters & Toggles -->
            <div
                class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-white/5">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" class="bu-toggle-btn shrink-0"
                        :class="{ 'bu-toggle-btn--active': showBusinessUnit }" :title="businessUnitToggleLabel"
                        @click="toggleBusinessUnit">
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
                        <span>{{ businessUnitToggleLabel }}</span>
                    </button>

                    <button type="button" class="bu-toggle-btn shrink-0"
                        :class="{ 'bu-toggle-btn--active': showStatusColors }"
                        title="Tampilkan/Sembunyikan Warna Status Implementasi"
                        @click="showStatusColors = !showStatusColors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        <span>Status Impl.</span>
                    </button>

                    <button type="button" class="bu-toggle-btn shrink-0"
                        :class="{ 'bu-toggle-btn--active': showLastUpdatePeriod }"
                        title="Tampilkan/Sembunyikan Periode Update"
                        @click="showLastUpdatePeriod = !showLastUpdatePeriod">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Periode</span>
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <select v-model="selectedSource" class="initiative-view-select min-w-[130px] max-w-[160px]">
                        <option value="">All Initiatives</option>
                        <option v-for="source in sourceOptions" :key="source.value" :value="source.value">
                            {{ source.label }}
                        </option>
                    </select>

                    <select v-model="selectedCoE" class="initiative-view-select min-w-[130px] max-w-[160px]">
                        <option value="">All CoE</option>
                        <option v-for="coe in coeOptions" :key="`coe-opt-${coe}`" :value="coe">
                            {{ coe }}
                        </option>
                    </select>

                    <select v-model="selectedOrg" class="initiative-view-select min-w-[130px] max-w-[160px]">
                        <option value="">All Organizations</option>
                        <option v-for="org in orgOptions" :key="`org-opt-${org.value}`" :value="org.value">
                            {{ org.label }}
                        </option>
                    </select>

                    <select v-model="selectedStatus" class="initiative-view-select min-w-[130px] max-w-[160px]">
                        <option value="">All Status</option>
                        <option v-for="st in availableStatusOptions" :key="st" :value="st">{{ st }}</option>
                    </select>

                    <select v-model="selectedNote" class="initiative-view-select min-w-[130px] max-w-[200px]">
                        <option value="">All Notes</option>
                        <option v-for="note in noteOptions" :key="`note-opt-${note}`" :value="note">
                            {{ note.length > 35 ? note.substring(0, 35) + '...' : note }}
                        </option>
                    </select>
                </div>
            </div>
        </section>

        <section v-if="hasGroups"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#111827]">
            <h1 class="text-center text-l font-bold mt-6 mb-2 px-4">
                Analisis Konsolidasi Kapabilitas Technology /CoE seiring dengan arahan restrukturisasi bisnis hilir
            </h1>
            <div class="overflow-x-auto">
                <table class="support-table">
                    <thead>
                        <tr>
                            <th class="support-table__head-cell support-table__head-cell--tech">Tech Capability / CoE
                            </th>
                            <th class="support-table__head-cell support-table__head-cell--digital">Digital Initiatives
                            </th>
                            <th class="support-table__head-cell support-table__head-cell--it">Potensi Dukungan IT
                                Initiatives & Notes</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="groupRow in displayGroups" :key="groupRow.key">
                            <tr v-for="row in groupRow.digitalRows" :key="row.key" class="support-table__row">
                                <td v-if="coeSpanMap.has(row.key)" :rowspan="coeSpanMap.get(row.key).span"
                                    class="support-table__cell support-table__cell--tech"
                                    :class="{ 'support-table__cell--last-group': coeSpanMap.get(row.key).isLastRow }">
                                    <div class="tech-capability">
                                        <span class="tech-capability__label">{{ row.techCapability }}</span>
                                        <span class="tech-capability__counter">
                                            {{ coeSpanMap.get(row.key).digitalCount }}
                                        </span>
                                    </div>
                                </td>

                                <td class="support-table__cell support-table__cell--digital">
                                    <div class="digital-initiative">
                                        <span v-if="initiativeDisplayCode(row.digital)" class="support-code-badge"
                                            :class="showStatusColors ? getStatusColorClass(getInitiativeStatus(row.digital)) : ''">
                                            {{ initiativeDisplayCode(row.digital) }}
                                        </span>
                                        <div class="flex flex-col min-w-0">
                                            <span class="leading-snug text-slate-800 dark:text-slate-100">
                                                {{ initiativeDisplayName(row.digital) }}
                                            </span>
                                            <span v-if="showLastUpdatePeriod && getInitiativePeriodLabel(row.digital)" class="support-period-label">
                                                {{ getInitiativePeriodLabel(row.digital) }}
                                            </span>
                                            <span v-if="showBusinessUnit" class="support-bu-label">
                                                {{ initiativeBusinessUnitLabel(row.digital) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td v-if="row.isFirstRow" :rowspan="groupRow.rowSpan"
                                    class="support-table__cell support-table__cell--it support-table__cell--grouped"
                                    :class="{ 'support-table__cell--last-group': groupRow.isLastGroup }">
                                    <div class="support-panel">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <button
                                                v-if="editable && Array.isArray(groupRow.group?.mapping_ids) && groupRow.group.mapping_ids.length > 0"
                                                type="button" class="support-delete-btn"
                                                @click="openDeleteGroupModal(groupRow.group)">
                                                Hapus
                                            </button>
                                        </div>

                                        <div class="note-card"
                                            :class="{ 'note-card--muted': !(groupRow.group?.note ?? '').trim() }">
                                            {{ groupRow.group?.note || EMPTY_NOTE_LABEL }}
                                        </div>

                                        <ol class="support-list">
                                            <li v-for="initiative in groupRow.group?.it_initiatives ?? []"
                                                :key="`it-${groupRow.group?.group_key}-${initiative.id}`"
                                                class="support-list__item">
                                                <span v-if="initiativeDisplayCode(initiative)"
                                                    class="support-code-badge support-code-badge--it"
                                                    :class="showStatusColors ? getStatusColorClass(getInitiativeStatus(initiative)) : ''">
                                                    {{ initiativeDisplayCode(initiative) }}
                                                </span>
                                                <div class="flex flex-col min-w-0">
                                                    <span class="leading-snug text-slate-800 dark:text-slate-100">
                                                        {{ initiativeDisplayName(initiative) }}
                                                    </span>
                                                    <span v-if="showLastUpdatePeriod && getInitiativePeriodLabel(initiative)" class="support-period-label">
                                                        {{ getInitiativePeriodLabel(initiative) }}
                                                    </span>
                                                </div>
                                            </li>
                                        </ol>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        <section v-else
            class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center dark:border-white/10 dark:bg-white/[0.03]">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Initiative Not Available
            </p>
        </section>

        <ConfirmationModal :show="isModalVisible" :title="modalTitle" :message="modalMessage" :type="modalType"
            :loading="modalLoading" :confirm-text="modalConfirmText" cancel-text="Batal" max-width="2xl"
            @close="closeModal" @confirm="handleModalConfirm" @after-leave="handleModalAfterLeave">
            <div v-if="isAddModal" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Digital
                        Initiatives</label>
                    <div class="space-y-2">
                        <select class="edit-select" @change="onDigitalSelect">
                            <option value="">+ Pilih Digital Initiative...</option>
                            <option v-for="option in digitalOptions" :key="`digital-option-${option.id}`"
                                :value="String(option.id)" :disabled="addForm.digital_ids.includes(Number(option.id))">
                                {{ initiativeOptionLabel(option) }}
                            </option>
                        </select>

                        <div v-if="selectedDigitalInitiatives.length > 0" class="flex flex-wrap gap-2">
                            <span v-for="initiative in selectedDigitalInitiatives"
                                :key="`selected-digital-${initiative.id}`" class="initiative-tag">
                                {{ initiativeOptionLabel(initiative) }}
                                <button type="button" class="initiative-tag__remove"
                                    @click="removeSelection('digital_ids', initiative.id)">
                                    x
                                </button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">IT Initiatives</label>
                    <div class="space-y-2">
                        <select class="edit-select" @change="onItSelect">
                            <option value="">+ Pilih IT Initiative...</option>
                            <option v-for="option in itOptions" :key="`it-option-${option.id}`"
                                :value="String(option.id)" :disabled="addForm.it_ids.includes(Number(option.id))">
                                {{ initiativeOptionLabel(option) }}
                            </option>
                        </select>

                        <div v-if="selectedItInitiatives.length > 0" class="flex flex-wrap gap-2">
                            <span v-for="initiative in selectedItInitiatives" :key="`selected-it-${initiative.id}`"
                                class="initiative-tag">
                                {{ initiativeOptionLabel(initiative) }}
                                <button type="button" class="initiative-tag__remove"
                                    @click="removeSelection('it_ids', initiative.id)">
                                    x
                                </button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Catatan Dukungan</label>
                    <textarea v-model="addForm.notes" rows="4" class="edit-textarea"
                        placeholder="Isi catatan dukungan bila diperlukan." />
                </div>

                <p v-if="modalError"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700">
                    {{ modalError }}
                </p>
            </div>

            <div v-else class="space-y-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                    <p class="font-semibold text-slate-900">{{ deleteTarget.label }}</p>
                    <p class="mt-1">
                        Dukungan ini akan dihapus dari sistem.
                    </p>
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
.support-metric {
    display: inline-flex;
    align-items: center;
    border-radius: 9999px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    padding: 0.45rem 0.8rem;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
}

.support-table {
    width: 100%;
    min-width: 1180px;
    border-collapse: separate;
    border-spacing: 0;
}

.support-table__head-cell {
    padding: 10px 16px;
    border-bottom: 1px solid #dbe3ee;
    border-right: 1px solid #dbe3ee;
    background: #0f6fb7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    text-align: left;
}

.support-table__head-cell--notes,
.support-table__cell--notes {
    border-right: none !important;
}

.support-table__cell {
    padding: 8px 16px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    vertical-align: top;
    background: #ffffff;
}

.support-table__row:last-child .support-table__cell {
    border-bottom: none;
}

.support-table__cell--tech {
    width: 15%;
    background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
    vertical-align: middle !important;
}

.support-table__cell--digital {
    width: 35%;
}

.support-table__cell--it {
    width: 50%;
}

.support-table__cell--grouped {
    vertical-align: top;
}

.support-table__cell--last-group {
    border-bottom: none;
}

.tech-capability {
    display: flex;
    min-height: 100%;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    gap: 4px;
}

.tech-capability__label {
    font-size: 11px;
    font-weight: 800;
    line-height: 1.35;
    color: #0f172a;
}

.tech-capability__counter {
    display: inline-flex;
    align-items: center;
    border-radius: 9999px;
    background: #0f6fb7;
    padding: 2px 10px;
    font-size: 9px;
    font-weight: 800;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    white-space: nowrap;
}

:deep(.dark) .tech-capability__counter {
    background: #3b82f6;
    color: #ffffff;
}

.digital-initiative {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 12px;
    font-weight: 600;
}

.note-card {
    font-size: 13px;
    font-weight: 600;
    line-height: 1.55;
    color: #1e293b;
}

.note-card--muted {
    color: #64748b;
    font-style: italic;
}

.support-panel {
    display: flex;
    min-height: 100%;
    flex-direction: column;
    gap: 8px;
}

.support-list {
    display: grid;
    gap: 6px;
    padding-left: 18px;
}

.support-list__item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13px;
}

.support-code-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    border-radius: 9999px;
    background: #e0f2fe;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 800;
    color: #075985;
    flex-shrink: 0;
}

.support-code-badge--it {
    background: #dbeafe;
    color: #1d4ed8;
}

.support-delete-btn {
    border: 1px solid #fecaca;
    border-radius: 9999px;
    background: #fff1f2;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #be123c;
    transition: background 0.15s ease, border-color 0.15s ease;
}

.support-delete-btn:hover {
    background: #ffe4e6;
    border-color: #fda4af;
}

.bu-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 6px 10px;
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

.support-bu-label {
    display: block;
    width: 100%;
    margin-top: 2px;
    font-size: 8px;
    font-weight: 700;
    font-style: italic;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.support-period-label {
    display: block;
    width: 100%;
    margin-top: 1px;
    font-size: 8px;
    font-weight: 700;
    font-style: italic;
    color: #64748b;
    white-space: nowrap;
}

.initiative-view-select {
    appearance: none;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 6px 32px 6px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 14px;
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

.edit-select,
.edit-textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    background: #ffffff;
    font-size: 12px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.edit-select {
    height: 40px;
    padding: 0 0.875rem;
}

.edit-textarea {
    padding: 0.875rem;
    resize: vertical;
}

.edit-select:focus,
.edit-textarea:focus {
    border-color: #0f6fb7;
    box-shadow: 0 0 0 4px rgba(15, 111, 183, 0.12);
}

.initiative-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #bfdbfe;
    border-radius: 9999px;
    background: #eff6ff;
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #1e4f8f;
}

.initiative-tag__remove {
    border: none;
    background: transparent;
    padding: 0;
    font-size: 10px;
    font-weight: 700;
    color: #1e3a8a;
    cursor: pointer;
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

.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}

:deep(.dark) .support-metric {
    border-color: rgba(148, 163, 184, 0.16);
    background: rgba(255, 255, 255, 0.04);
    color: #cbd5e1;
}

:deep(.dark) .support-table__head-cell {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    border-right-color: rgba(148, 163, 184, 0.14);
    background: linear-gradient(180deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.9) 100%);
    color: #f8fafc;
}

:deep(.dark) .support-table__cell {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    border-right-color: rgba(148, 163, 184, 0.14);
    background: rgba(15, 23, 42, 0.3);
}

:deep(.dark) .support-table__cell--tech {
    background: linear-gradient(180deg, rgba(51, 65, 85, 0.85) 0%, rgba(30, 41, 59, 0.85) 100%);
}

:deep(.dark) .tech-capability__label,
:deep(.dark) .note-card {
    color: #e2e8f0;
}

:deep(.dark) .note-card--muted {
    color: #94a3b8;
}

:deep(.dark) .edit-select,
:deep(.dark) .edit-textarea {
    border-color: rgba(148, 163, 184, 0.18);
    background: rgba(15, 23, 42, 0.75);
    color: #f8fafc;
}
</style>
