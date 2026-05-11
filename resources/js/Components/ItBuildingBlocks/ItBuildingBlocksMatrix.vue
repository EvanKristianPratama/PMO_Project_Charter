<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const route = useRouteHelper();

const initiativeSummaryHref = (initiative) => {
    const initiativeId = Number(initiative?.initiative_id ?? initiative?.id ?? 0);
    return initiativeId > 0
        ? route('program-planning.program-definition.digital-initiatives.summary.index', initiativeId)
        : null;
};

const initiativeProjectCharterHref = (initiative) => {
    const projectId = Number(initiative?.mapped_project_id ?? initiative?.id ?? 0);
    return projectId > 0
        ? route('it-initiatives.show', { project: projectId, tab: 'charter' })
        : null;
};

const initiativeLinkHref = (initiative) => {
    return initiativeProjectCharterHref(initiative) || initiativeSummaryHref(initiative);
};

const initiativeLinkTitle = (initiative) => {
    const label = String(initiative?.label ?? initiative?.name ?? initiative?.code ?? 'initiative').trim();
    if (initiativeProjectCharterHref(initiative)) {
        return `Lihat project charter IT untuk ${label}`;
    }
    return `Lihat capsule summary untuk ${label}`;
};
const emit = defineEmits(['cancel-add-mapping']);

const props = defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    editable: {
        type: Boolean,
        default: false,
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const selectedPeriod = ref(null);

const monthsOrder = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const normalizePeriodMonth = (value) => String(value ?? '').trim();
const normalizePeriodYear = (value) => String(value ?? '').trim();

const resolvePeriodStart = (period) => normalizePeriodMonth(period?.start ?? period?.month);
const resolvePeriodEnd = (period) => normalizePeriodMonth(period?.end);

const formatPeriodLabel = (period) => {
    const start = resolvePeriodStart(period);
    const end = resolvePeriodEnd(period);
    const year = normalizePeriodYear(period?.year);
    const fallbackLabel = String(
        period?.label
        ?? period?.latest_period_label
        ?? period?.period_label
        ?? '',
    ).trim();

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

const getInitiativeStatuses = (initiative) => {
    return Array.isArray(initiative?.statuses) ? initiative.statuses : [];
};

const periodMatchesSelection = (period, selectedPeriod) => {
    return resolvePeriodStart(period) === resolvePeriodStart(selectedPeriod)
        && resolvePeriodEnd(period) === resolvePeriodEnd(selectedPeriod)
        && normalizePeriodYear(period?.year) === normalizePeriodYear(selectedPeriod?.year);
};

const getInitiativeStatus = (initiative) => {
    if (!selectedPeriod.value) {
        return initiative.implementation_status;
    }

    const found = getInitiativeStatuses(initiative).find((status) => periodMatchesSelection(status, selectedPeriod.value));

    return found ? found.status : null;
};

const getInitiativePeriodLabel = (initiative) => {
    if (selectedPeriod.value) {
        return formatPeriodLabel(selectedPeriod.value);
    }

    const latestPeriodLabel = String(
        initiative?.latest_period_label
        ?? initiative?.period_label
        ?? '',
    ).trim();

    if (latestPeriodLabel) {
        return latestPeriodLabel;
    }

    const statuses = getInitiativeStatuses(initiative);

    if (statuses.length === 0) return null;

    const sorted = [...statuses].sort((a, b) => {
        const yearDiff = Number(b?.year ?? 0) - Number(a?.year ?? 0);

        if (yearDiff !== 0) return yearDiff;

        return monthsOrder.indexOf(resolvePeriodStart(b)) - monthsOrder.indexOf(resolvePeriodStart(a));
    });

    const latest = sorted[0];
    return formatPeriodLabel(latest);
};

const mappingForm = reactive({
    primary: '',
    secondary: '',
    initiative_ids: [],
});

const mappingProcessing = ref(false);
const deleteProcessing = ref(false);
const saveRemovalsProcessing = ref(false);
const pendingInitiativeRemovals = ref([]);
const activeModal = ref('');
const isModalVisible = ref(false);
const modalError = ref('');
const shouldExitEditOnCancel = ref(false);
const deleteTarget = reactive({
    group: null,
    secondaryGroup: null,
    initiative: null,
});

const createRemovalKey = (primaryId, secondaryId, initiativeId) => {
    return [
        Number(primaryId ?? 0),
        Number(secondaryId ?? 0),
        Number(initiativeId ?? 0),
    ].join(':');
};

const pendingInitiativeRemovalKeys = computed(() => {
    return new Set(
        pendingInitiativeRemovals.value.map((item) => {
            return createRemovalKey(item.primary_id, item.secondary_id, item.initiative_id);
        }),
    );
});

const activeRemovalKeys = computed(() => {
    return pendingInitiativeRemovalKeys.value;
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

const filteredBase = computed(() => {
    return props.groups
        .map((group) => {
            const secondaryGroups = (group?.secondary_groups ?? [])
                .map((secondaryGroup) => {
                    const initiatives = (secondaryGroup?.initiatives ?? []).filter((initiative) => {
                        const isNotRemoved = !activeRemovalKeys.value.has(
                            createRemovalKey(group?.primary_id, secondaryGroup?.secondary_id, initiative?.initiative_id),
                        );
                        
                        let matchesOrg = true;
                        if (selectedOrganization.value === 'all_holding') {
                            matchesOrg = initiative.groub_id !== 2;
                        } else if (selectedOrganization.value === 'all_subholding') {
                            matchesOrg = initiative.groub_id === 2;
                        } else if (selectedOrganization.value !== '') {
                            matchesOrg = initiative.business_unit === selectedOrganization.value;
                        }
                        
                        const coeName = normalizeCoeName(initiative.coe_name);
                        const matchesCoe = !selectedCoe.value || coeName === selectedCoe.value;

                        const matchesSource = !selectedSource.value || initiative.source == selectedSource.value;
                        
                        const periodStatus = getInitiativeStatus(initiative);
                        const matchesPeriod = !selectedPeriod.value || periodStatus !== null;

                        return isNotRemoved && matchesOrg && matchesCoe && matchesSource && matchesPeriod;
                    });

                    return {
                        ...secondaryGroup,
                        initiatives,
                    };
                })
                .filter((secondaryGroup) => secondaryGroup.initiatives.length > 0);

            return {
                ...group,
                secondary_groups: secondaryGroups,
            };
        })
        .filter((group) => group.secondary_groups.length > 0);
});

const statusLegend = computed(() => {
    const stats = {};
    statusDesiredOrder.forEach(label => {
        stats[label] = 0;
    });

    filteredBase.value.forEach((group) => {
        group.secondary_groups.forEach((secondaryGroup) => {
            secondaryGroup.initiatives.forEach((initiative) => {
                const label = normalizeStatusLabel(getInitiativeStatus(initiative));
                if (label && stats.hasOwnProperty(label)) {
                    stats[label]++;
                }
            });
        });
    });

    return statusDesiredOrder.map((label) => ({
        label,
        class: getStatusColorClass(label),
        count: stats[label],
    }));
});

const displayGroups = computed(() => {
    return filteredBase.value
        .map((group) => {
            let totalInitiativesCount = 0;
            const secondaryGroups = (group?.secondary_groups ?? [])
                .map((secondaryGroup) => {
                    const initiatives = secondaryGroup.initiatives.filter((initiative) => {
                        const periodStatus = getInitiativeStatus(initiative);
                        const implStatus = normalizeStatusLabel(periodStatus);
                        return !selectedStatus.value || implStatus === selectedStatus.value;
                    });

                    totalInitiativesCount += initiatives.length;

                    return {
                        ...secondaryGroup,
                        initiatives,
                    };
                })
                .filter((secondaryGroup) => secondaryGroup.initiatives.length > 0);

            return {
                ...group,
                secondary_groups: secondaryGroups,
                total_initiatives: totalInitiativesCount,
            };
        })
        .filter((group) => group.secondary_groups.length > 0);
});

const hasGroups = computed(() => displayGroups.value.length > 0);
const hasPendingInitiativeRemovals = computed(() => pendingInitiativeRemovals.value.length > 0);

const totalOverallInitiatives = computed(() => {
    return displayGroups.value.reduce((sum, group) => sum + (group.total_initiatives ?? 0), 0);
});

const existingMappingKeys = computed(() => {
    const keys = new Set();

    props.groups.forEach((group) => {
        const primaryId = Number(group?.primary_id ?? 0);

        (group?.secondary_groups ?? []).forEach((secondaryGroup) => {
            const secondaryId = Number(secondaryGroup?.secondary_id ?? 0);

            (secondaryGroup?.initiatives ?? []).forEach((initiative) => {
                const initiativeId = Number(initiative?.initiative_id ?? 0);

                if (primaryId > 0 && secondaryId > 0 && initiativeId > 0) {
                    keys.add(`${primaryId}:${secondaryId}:${initiativeId}`);
                }
            });
        });
    });

    return keys;
});

const canSubmitMapping = computed(() => {
    return (
        String(mappingForm.primary ?? '').trim() !== ''
        && String(mappingForm.secondary ?? '').trim() !== ''
        && mappingForm.initiative_ids.length > 0
    );
});

const selectedInitiatives = computed(() => {
    return mappingForm.initiative_ids
        .map((initiativeId) => {
            return props.initiativeOptions.find((option) => Number(option?.id) === Number(initiativeId)) ?? {
                id: Number(initiativeId),
                code: '',
                name: `Initiative ${initiativeId}`,
            };
        });
});

const isAddModal = computed(() => ['add-mapping', 'add-secondary', 'add-initiative'].includes(activeModal.value));

const modalTitle = computed(() => {
    if (activeModal.value === 'add-mapping') return 'Tambah Mapping';
    if (activeModal.value === 'add-secondary') return 'Tambah Secondary';
    if (activeModal.value === 'add-initiative') return 'Tambah Initiative';
    if (activeModal.value === 'delete-primary') return 'Hapus Primary';
    if (activeModal.value === 'delete-secondary') return 'Hapus Secondary';
    if (activeModal.value === 'delete-initiative') return 'Tandai Hapus Initiative';

    return 'Konfirmasi Aksi';
});

const modalMessage = computed(() => {
    if (activeModal.value === 'add-mapping') {
        return 'Tentukan Primary, Secondary, dan satu atau lebih Digital Initiative yang ingin dimapping.';
    }

    if (activeModal.value === 'add-secondary') {
        return `Tambahkan Secondary baru untuk Primary "${deleteTarget.group?.primary ?? '-'}" beserta initiative yang ingin dimapping.`;
    }

    if (activeModal.value === 'add-initiative') {
        return `Tambahkan satu atau lebih initiative ke Secondary "${deleteTarget.secondaryGroup?.secondary ?? '-'}".`;
    }

    if (activeModal.value === 'delete-primary') {
        return `Primary "${deleteTarget.group?.primary ?? '-'}" akan dihapus beserta seluruh Secondary dan Initiative di bawahnya.`;
    }

    if (activeModal.value === 'delete-secondary') {
        return `Secondary "${deleteTarget.secondaryGroup?.secondary ?? '-'}" akan dihapus beserta seluruh Initiative di bawahnya.`;
    }

    if (activeModal.value === 'delete-initiative') {
        return `Initiative "${deleteTarget.initiative?.name ?? '-'}" tidak langsung dihapus. Data ini akan ditandai dulu, lalu benar-benar dihapus saat Anda menekan Simpan Perubahan.`;
    }

    return 'Lanjutkan aksi ini?';
});

const modalType = computed(() => {
    return isAddModal.value ? 'info' : 'warning';
});

const modalConfirmText = computed(() => {
    if (isAddModal.value) return 'Simpan';
    if (activeModal.value === 'delete-initiative') return 'Tandai Hapus';
    return 'Ya, Lanjutkan';
});

const modalLoading = computed(() => {
    return isAddModal.value ? mappingProcessing.value : deleteProcessing.value;
});

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

const DEFAULT_INITIATIVE_COLUMN_COUNT = 6;
const initiativeColumnOptions = [3, 4, 5, 6];
const initiativeColumnCount = ref(DEFAULT_INITIATIVE_COLUMN_COUNT);
const showBusinessUnit = ref(false);
const showStatusColors = ref(false);
const showInitiativeCode = ref(false);
const showLastUpdatePeriod = ref(false);
const selectedOrganization = ref('');
const selectedCoe = ref('');
const selectedStatus = ref('');
const selectedSource = ref('');

const organizationOptions = computed(() => {
    const orgMap = new Map();
    props.groups.forEach(group => {
        (group.secondary_groups ?? []).forEach(sec => {
            (sec.initiatives ?? []).forEach(ini => {
                const org = ini.business_unit;
                if (org && org !== '-') {
                    const groupLabel = ini.groub_id === 2 ? 'Sub Holding' : 'Holding';
                    if (!orgMap.has(org)) {
                        orgMap.set(org, groupLabel);
                    }
                }
            });
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
        (group.secondary_groups ?? []).forEach(sec => {
            (sec.initiatives ?? []).forEach(ini => {
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

const buildInitiativeColumns = (initiatives = [], columnCount = initiativeColumnCount.value) => {
    const items = Array.isArray(initiatives)
        ? [...initiatives].sort((left, right) => {
            const leftHasCoe = (left?.coe_id ?? 0) > 0;
            const rightHasCoe = (right?.coe_id ?? 0) > 0;

            if (leftHasCoe && !rightHasCoe) return -1;
            if (!leftHasCoe && rightHasCoe) return 1;

            const leftCode = initiativeDisplayCode(left);
            const rightCode = initiativeDisplayCode(right);

            if (leftCode !== '' || rightCode !== '') {
                const codeCompare = leftCode.localeCompare(rightCode, undefined, { numeric: true, sensitivity: 'base' });

                if (codeCompare !== 0) {
                    return codeCompare;
                }
            }

            return initiativeDisplayName(left).localeCompare(initiativeDisplayName(right), undefined, {
                numeric: true,
                sensitivity: 'base',
            });
        })
        : [];

    if (items.length === 0) {
        return { items: [], rowCount: 0 };
    }

    const rowCount = Math.ceil(items.length / Number(columnCount));

    return {
        items,
        rowCount,
    };
};

const isValidId = (value) => {
    const numericValue = Number(value);

    return Number.isInteger(numericValue) && numericValue > 0;
};

const resetMappingForm = (nextValues = {}) => {
    mappingForm.primary = nextValues.primary ?? '';
    mappingForm.secondary = nextValues.secondary ?? '';
    mappingForm.initiative_ids = Array.isArray(nextValues.initiative_ids)
        ? nextValues.initiative_ids.map((value) => Number(value)).filter((value) => Number.isInteger(value) && value > 0)
        : [];
};

const resetDeleteTarget = () => {
    deleteTarget.group = null;
    deleteTarget.secondaryGroup = null;
    deleteTarget.initiative = null;
};

const closeModal = (force = false) => {
    if (modalLoading.value && !force) {
        return;
    }

    const shouldExitEdit = activeModal.value === 'add-mapping' && shouldExitEditOnCancel.value && !force;

    isModalVisible.value = false;

    if (shouldExitEdit) {
        emit('cancel-add-mapping');
    }
};

const handleModalAfterLeave = () => {
    if (isAddModal.value) {
        resetMappingForm();
    }

    activeModal.value = '';
    modalError.value = '';
    shouldExitEditOnCancel.value = false;
    resetDeleteTarget();
};

const openAddMappingModal = (options = {}) => {
    resetMappingForm();
    resetDeleteTarget();
    modalError.value = '';
    shouldExitEditOnCancel.value = Boolean(options.exitEditOnCancel);
    activeModal.value = 'add-mapping';
    isModalVisible.value = true;
};

const openAddSecondaryModal = (group) => {
    resetMappingForm({
        primary: isValidId(group?.primary_id) ? String(group.primary_id) : '',
    });
    deleteTarget.group = group ?? null;
    deleteTarget.secondaryGroup = null;
    deleteTarget.initiative = null;
    modalError.value = '';
    shouldExitEditOnCancel.value = false;
    activeModal.value = 'add-secondary';
    isModalVisible.value = true;
};

const openAddInitiativeModal = (group, secondaryGroup) => {
    resetMappingForm({
        primary: isValidId(group?.primary_id) ? String(group.primary_id) : '',
        secondary: isValidId(secondaryGroup?.secondary_id) ? String(secondaryGroup.secondary_id) : '',
    });
    deleteTarget.group = group ?? null;
    deleteTarget.secondaryGroup = secondaryGroup ?? null;
    deleteTarget.initiative = null;
    modalError.value = '';
    shouldExitEditOnCancel.value = false;
    activeModal.value = 'add-initiative';
    isModalVisible.value = true;
};

const firstErrorMessage = (errors = {}) => {
    return Object.values(errors)
        .flat()
        .map((value) => String(value ?? '').trim())
        .find((value) => value !== '');
};

const isInitiativeAlreadyMapped = (initiativeId) => {
    if (!isValidId(mappingForm.primary) || !isValidId(mappingForm.secondary) || !isValidId(initiativeId)) {
        return false;
    }

    return existingMappingKeys.value.has(createRemovalKey(
        mappingForm.primary,
        mappingForm.secondary,
        initiativeId,
    ));
};

const addInitiativeSelection = async (initiativeId) => {
    const numericInitiativeId = Number(initiativeId);

    if (!isValidId(mappingForm.primary)) {
        modalError.value = 'Pilih Primary terlebih dahulu.';
        return;
    }

    if (!isValidId(mappingForm.secondary)) {
        modalError.value = 'Pilih Secondary terlebih dahulu.';
        return;
    }

    if (!Number.isInteger(numericInitiativeId) || numericInitiativeId <= 0) {
        return;
    }

    if (mappingForm.initiative_ids.includes(numericInitiativeId)) {
        return;
    }

    if (isInitiativeAlreadyMapped(numericInitiativeId)) {
        modalError.value = 'Initiative ini sudah termapping pada kombinasi Primary dan Secondary yang dipilih.';
        return;
    }

    modalError.value = '';
    mappingForm.initiative_ids = [
        ...mappingForm.initiative_ids,
        numericInitiativeId,
    ];
};

const removeInitiativeSelection = (initiativeId) => {
    mappingForm.initiative_ids = mappingForm.initiative_ids.filter((value) => Number(value) !== Number(initiativeId));
};

const onInitiativeSelect = async (event) => {
    const selectedValue = event?.target?.value ?? '';

    event.target.value = '';

    await addInitiativeSelection(selectedValue);
};

watch(
    () => [mappingForm.primary, mappingForm.secondary],
    (currentValue, previousValue) => {
        if (!previousValue) {
            return;
        }

        if (currentValue[0] !== previousValue[0] || currentValue[1] !== previousValue[1]) {
            mappingForm.initiative_ids = [];
            modalError.value = '';
        }
    },
);

watch(
    () => props.editable,
    (editable) => {
        if (!editable && isModalVisible.value) {
            closeModal(true);
        }
    },
);

const submitMapping = () => {
    if (!canSubmitMapping.value) {
        modalError.value = 'Pilih Primary, Secondary, dan minimal satu Digital Initiative terlebih dahulu.';
        return;
    }

    if (mappingForm.primary === mappingForm.secondary) {
        modalError.value = 'Primary dan Secondary tidak boleh sama.';
        return;
    }

    const duplicateSelections = mappingForm.initiative_ids.filter((initiativeId) => {
        return isInitiativeAlreadyMapped(initiativeId);
    });

    if (duplicateSelections.length > 0) {
        modalError.value = 'Ada initiative yang sudah termapping pada kombinasi Primary dan Secondary ini.';
        return;
    }

    modalError.value = '';
    mappingProcessing.value = true;

    router.post(route('program-planning.it-building-blocks.store'), {
        primary: Number(mappingForm.primary),
        secondary: Number(mappingForm.secondary),
        initiative_ids: mappingForm.initiative_ids.map((value) => Number(value)),
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
            resetMappingForm();
        },
        onFinish: () => {
            mappingProcessing.value = false;
        },
    });
};

const openDeletePrimaryModal = (group) => {
    if (!isValidId(group?.primary_id)) return;

    resetDeleteTarget();
    deleteTarget.group = group;
    modalError.value = '';
    activeModal.value = 'delete-primary';
    shouldExitEditOnCancel.value = false;
    isModalVisible.value = true;
};

const openDeleteSecondaryModal = (group, secondaryGroup) => {
    if (!isValidId(group?.primary_id) || !isValidId(secondaryGroup?.secondary_id)) return;

    resetDeleteTarget();
    deleteTarget.group = group;
    deleteTarget.secondaryGroup = secondaryGroup;
    modalError.value = '';
    activeModal.value = 'delete-secondary';
    shouldExitEditOnCancel.value = false;
    isModalVisible.value = true;
};

const openDeleteInitiativeModal = (group, secondaryGroup, initiative) => {
    if (!isValidId(group?.primary_id) || !isValidId(secondaryGroup?.secondary_id) || !isValidId(initiative?.initiative_id)) {
        return;
    }

    resetDeleteTarget();
    deleteTarget.group = group;
    deleteTarget.secondaryGroup = secondaryGroup;
    deleteTarget.initiative = initiative;
    modalError.value = '';
    activeModal.value = 'delete-initiative';
    shouldExitEditOnCancel.value = false;
    isModalVisible.value = true;
};

const confirmDeletePrimary = () => {
    deleteProcessing.value = true;

    router.delete(route('program-planning.it-building-blocks.primary.destroy', {
        primary: deleteTarget.group.primary_id,
    }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            closeModal(true);
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
};

const confirmDeleteSecondary = () => {
    deleteProcessing.value = true;

    router.delete(route('program-planning.it-building-blocks.secondary.destroy', {
        primary: deleteTarget.group.primary_id,
        secondary: deleteTarget.secondaryGroup.secondary_id,
    }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            closeModal(true);
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
};

const confirmDeleteInitiative = () => {
    const removalKey = createRemovalKey(
        deleteTarget.group.primary_id,
        deleteTarget.secondaryGroup.secondary_id,
        deleteTarget.initiative.initiative_id,
    );

    if (!pendingInitiativeRemovalKeys.value.has(removalKey)) {
        pendingInitiativeRemovals.value = [
            ...pendingInitiativeRemovals.value,
            {
                primary_id: Number(deleteTarget.group.primary_id),
                secondary_id: Number(deleteTarget.secondaryGroup.secondary_id),
                initiative_id: Number(deleteTarget.initiative.initiative_id),
                initiative_name: deleteTarget.initiative.name ?? '-',
                secondary_name: deleteTarget.secondaryGroup.secondary ?? '-',
            },
        ];
    }

    closeModal(true);
};

const cancelPendingInitiativeRemovals = () => {
    pendingInitiativeRemovals.value = [];
};

const savePendingInitiativeRemovals = () => {
    if (!hasPendingInitiativeRemovals.value) {
        return;
    }

    saveRemovalsProcessing.value = true;

    router.post(route('program-planning.it-building-blocks.initiative.bulk-destroy'), {
        removals: pendingInitiativeRemovals.value.map((item) => ({
            primary: item.primary_id,
            secondary: item.secondary_id,
            initiative_id: item.initiative_id,
        })),
    }, {
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            const message = firstErrorMessage(errors);

            if (message) {
                window.alert(message);
            }
        },
        onSuccess: () => {
            pendingInitiativeRemovals.value = [];
        },
        onFinish: () => {
            saveRemovalsProcessing.value = false;
        },
    });
};

const handleModalConfirm = () => {
    if (isAddModal.value) {
        submitMapping();
        return;
    }

    if (activeModal.value === 'delete-primary') {
        confirmDeletePrimary();
        return;
    }

    if (activeModal.value === 'delete-secondary') {
        confirmDeleteSecondary();
        return;
    }

    if (activeModal.value === 'delete-initiative') {
        confirmDeleteInitiative();
    }
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

    // Inisialisasi statistik
    const stats = {};
    desiredOrder.forEach(name => {
        stats[name] = 0;
    });

    displayGroups.value.forEach((group) => {
        group.secondary_groups.forEach((secondaryGroup) => {
            secondaryGroup.initiatives.forEach((initiative) => {
                const name = normalizeCoeName(initiative.coe_name);
                
                if (stats.hasOwnProperty(name)) {
                    stats[name]++;
                } else {
                    stats['CoE Not Identified']++;
                }
            });
        });
    });

    return desiredOrder.map((name, index) => ({
        id: index + 1,
        name: name,
        count: stats[name],
    }));
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

const normalizeStatusLabel = (rawStatus) => {
    const s = String(rawStatus ?? '').trim();
    if (!s) return null;
    const normalized = s.toLowerCase();

    if (normalized === 'df') return 'DF';
    if (normalized === 'done' || normalized === 'completed') return 'Done';
    if (normalized === 'on progress' || normalized === 'on progres') return 'On Progress';
    if (normalized === 'dt 2026') return 'DT 2026';
    if (normalized === 'itsbp') return 'ITSBP';
    if (normalized === 'on review') return 'On Review';
    if (normalized === 'sh') return 'SH';

    return s;
};

const getStatusColorClass = (status) => {
    const s = normalizeStatusLabel(status);
    if (!s) return '';
    if (s === 'DF') return 'status-color-df';
    if (s === 'Done') return 'status-color-done';
    if (s === 'DT 2026') return 'status-color-dt2026';
    if (s === 'ITSBP') return 'status-color-itsbp';
    if (s === 'On Review') return 'status-color-onreview';
    if (s === 'On Progress' || s === 'On Progres') return 'status-color-onprogress';
    if (s === 'SH') return 'status-color-sh';
    return '';
};

const statusDesiredOrder = [
    'Done',
    'DF',
    'DT 2026',
    'ITSBP',
    'On Progress',
    'On Review',
    'SH',
];

defineExpose({
    openAddMappingModal,
});
</script>

<template>
    <div class="space-y-4">
        <div
            v-if="hasPendingInitiativeRemovals"
            class="flex flex-col gap-3 border border-amber-200 bg-amber-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="min-w-0">
                <p class="text-sm font-semibold text-amber-900">
                    {{ pendingInitiativeRemovals.length }} initiative ditandai untuk dihapus.
                </p>
                <p class="mt-1 text-xs text-amber-800">
                    Klik `Simpan Perubahan` untuk menerapkan penghapusan, atau `Batal` untuk mengembalikan.
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="saveRemovalsProcessing"
                    @click="savePendingInitiativeRemovals"
                >
                    {{ saveRemovalsProcessing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-medium text-amber-800 transition hover:bg-amber-100"
                    @click="cancelPendingInitiativeRemovals"
                >
                    Batal
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Row 1: Legend & Overall Total -->
            <div class="space-y-2.5">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div
                        v-for="coe in coeLegend"
                        :key="`coe-legend-${coe.id}`"
                        class="flex items-center gap-1.5 cursor-pointer select-none transition-opacity"
                        :class="{ 'opacity-40': selectedCoe && selectedCoe !== coe.name }"
                        :title="`Filter CoE: ${coe.name}`"
                        role="button"
                        tabindex="0"
                        @click="selectedCoe = selectedCoe === coe.name ? '' : coe.name"
                        @keydown.enter.prevent="selectedCoe = selectedCoe === coe.name ? '' : coe.name"
                        @keydown.space.prevent="selectedCoe = selectedCoe === coe.name ? '' : coe.name"
                    >
                        <span
                            class="h-3 w-3 rounded-sm shadow-sm legend-swatch"
                            :class="getCoeColorClass(coe.name)"
                        ></span>
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
                            {{ coe.name }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ coe.count }})</span>
                        </span>
                    </div>

                    <!-- Total Overall -->
                    <div class="flex items-center gap-1.5 border-l border-slate-300 pl-4 ml-1 dark:border-white/10">
                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">
                            Total Digital Initiatives <span class="text-slate-500 dark:text-slate-400 font-medium">({{ totalOverallInitiatives }})</span>
                        </span>
                    </div>
                </div>

                <!-- Status Implementation Legend -->
                <div
                    v-if="showStatusColors"
                    class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 border-t border-slate-100 dark:border-white/5"
                >
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Implementation Status:</span>
                        <select 
                            v-if="statusPeriods && statusPeriods.length > 0" 
                            v-model="selectedPeriod" 
                            class="text-[10px] font-bold bg-slate-50 dark:bg-slate-800/50 border-none rounded focus:ring-0 cursor-pointer text-blue-600 dark:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors py-0.5 px-1.5 h-auto leading-none"
                        >
                            <option :value="null">All (Latest)</option>
                            <option v-for="period in statusPeriods" :key="period.label" :value="period">
                                {{ period.label }}
                            </option>
                        </select>
                        <span v-else class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">(November - Desember 2025):</span>
                    </div>
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

            <!-- Row 2: Toolbar (Filters & Settings) -->
            <div class="flex items-center justify-start">
                <div class="initiative-view-switch">
                    <select
                        v-model="selectedOrganization"
                        class="initiative-view-select"
                    >
                        <option value="">All Organizations</option>
                        <option
                            v-for="org in organizationOptions"
                            :key="org.value"
                            :value="org.value"
                        >
                            {{ org.label }}
                        </option>
                    </select>

                    <select
                        v-model="selectedCoe"
                        class="initiative-view-select"
                    >
                        <option value="">All CoE</option>
                        <option
                            v-for="coe in coeLegend"
                            :key="`coe-opt-${coe.id}`"
                            :value="coe.name"
                        >
                            {{ coe.name }}
                        </option>
                    </select>

                    <select
                        v-model="selectedStatus"
                        class="initiative-view-select"
                    >
                        <option value="">All Status</option>
                        <option
                            v-for="status in statusLegend"
                            :key="`status-opt-${status.label}`"
                            :value="status.label"
                        >
                            {{ status.label }}
                        </option>
                    </select>

                    <select
                        v-model="selectedSource"
                        class="initiative-view-select"
                    >
                        <option value="">All Initiatives</option>
                        <option
                            v-for="source in sourceOptions"
                            :key="`source-opt-${source.value}`"
                            :value="source.value"
                        >
                            {{ source.label }}
                        </option>
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

                    <button
                        type="button"
                        class="bu-toggle-btn"
                        :class="{ 'bu-toggle-btn--active': showLastUpdatePeriod }"
                        title="Tampilkan/Sembunyikan Periode Update"
                        @click="showLastUpdatePeriod = !showLastUpdatePeriod"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Periode</span>
                    </button>

                    <select
                        v-model="initiativeColumnCount"
                        class="initiative-view-select"
                    >
                        <option
                            v-for="option in initiativeColumnOptions"
                            :key="`col-opt-${option}`"
                            :value="option"
                        >
                            {{ option }} Kolom
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <section
            v-if="hasGroups"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
        >
            <h1 class="text-center text-l font-bold mt-4 mb-4 ">IT Building Blocks Supporting for Digital Initiative</h1>
            <div class="overflow-x-auto">
                <table
                    class="itb-table min-w-full border-collapse"
                    :class="`itb-table--${initiativeColumnCount}-cols`"
                >
                    <thead>
                        <tr>
                            <th colspan="2" class="top-head top-head-left">
                                IT Building Blocks
                            </th>
                            <th class="top-head top-head-right">
                                Digital Initiatives
                            </th>
                        </tr>
                        <tr>
                            <th class="sub-head sub-head-primary">
                                Primary
                            </th>
                            <th class="sub-head sub-head-secondary">
                                Secondary
                            </th>
                            <th class="sub-head sub-head-initiative"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="group in displayGroups" :key="`primary-${group.primary_id}-${group.primary}`">
                            <tr
                                v-for="(secondaryGroup, secondaryIndex) in group.secondary_groups"
                                :key="`secondary-${group.primary_id}-${secondaryGroup.secondary_id}-${secondaryGroup.secondary}`"
                            >
                                <td
                                    v-if="secondaryIndex === 0"
                                    :rowspan="Math.max(group.secondary_groups.length, 1)"
                                    class="primary-cell"
                                >
                                    <div class="primary-cell__content">
                                        <div class="primary-label-wrapper">
                                            <span>{{ group.primary }}</span>
                                            <span class="count-capsule">{{ group.total_initiatives }}</span>
                                        </div>

                                        <div
                                            v-if="editable && isValidId(group.primary_id)"
                                            class="cell-actions cell-actions--primary"
                                        >
                                            <button
                                                type="button"
                                                class="cell-action-btn"
                                                @click="openAddSecondaryModal(group)"
                                            >
                                                + Secondary
                                            </button>
                                            <button
                                                type="button"
                                                class="cell-action-btn cell-action-btn--danger"
                                                :disabled="hasPendingInitiativeRemovals"
                                                @click="openDeletePrimaryModal(group)"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="secondary-cell">
                                    <div class="secondary-cell__content">
                                        <div class="secondary-label-wrapper">
                                            <span>{{ secondaryGroup.secondary }}</span>
                                            <span class="count-capsule">{{ secondaryGroup.initiatives.length }}</span>
                                        </div>

                                        <div
                                            v-if="editable && isValidId(group.primary_id) && isValidId(secondaryGroup.secondary_id)"
                                            class="cell-actions"
                                        >
                                            <button
                                                type="button"
                                                class="cell-action-btn"
                                                @click="openAddInitiativeModal(group, secondaryGroup)"
                                            >
                                                + Initiative
                                            </button>
                                            <button
                                                type="button"
                                                class="cell-action-btn cell-action-btn--danger"
                                                :disabled="hasPendingInitiativeRemovals"
                                                @click="openDeleteSecondaryModal(group, secondaryGroup)"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="initiatives-cell">
                                    <div
                                        class="initiatives-grid"
                                        :style="{
                                            '--initiative-column-count': initiativeColumnCount,
                                            '--row-count': buildInitiativeColumns(secondaryGroup.initiatives, initiativeColumnCount).rowCount
                                        }"
                                    >
                                        <component
                                            :is="initiativeLinkHref(initiative) ? Link : 'div'"
                                            v-for="initiative in buildInitiativeColumns(secondaryGroup.initiatives, initiativeColumnCount).items"
                                            :key="`initiative-${initiative.map_key}`"
                                            :href="initiativeLinkHref(initiative)"
                                            :title="initiativeLinkTitle(initiative)"
                                            class="initiative-box group"
                                            :class="[
                                                getCoeColorClass(initiative.coe_name),
                                                { 'initiative-box--no-code': !showInitiativeCode || !initiativeDisplayCode(initiative) },
                                                { 'initiative-box--clickable': initiativeLinkHref(initiative) }
                                            ]"
                                        >
                                            <template v-if="initiative">
                                                <!-- Custom Smart Tooltip -->
                                                <div class="absolute top-full left-1/2 z-50 mt-1 hidden -translate-x-1/2 w-max max-w-[250px] sm:max-w-xs md:max-w-sm bg-white border border-slate-800 shadow-sm px-1.5 py-1 text-left text-[9px] italic text-slate-800 group-hover:block pointer-events-none whitespace-normal break-words dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                                                    {{ initiative.description || initiativeOptionLabel(initiative) }}
                                                </div>                                                <span
                                                    v-if="showInitiativeCode && initiativeDisplayCode(initiative)"
                                                    class="initiative-box__code"
                                                    :class="showStatusColors ? getStatusColorClass(initiative.implementation_status) : ''"
                                                >
                                                    {{ initiativeDisplayCode(initiative) }}
                                                </span>

                                                <span
                                                    class="initiative-box__name"
                                                    :class="{ 'initiative-box__name--full': !showInitiativeCode || !initiativeDisplayCode(initiative) }"
                                                >
                                                    <span class="initiative-box__label-text">{{ initiativeDisplayName(initiative) }}</span>
                                                    <span v-if="showLastUpdatePeriod && getInitiativePeriodLabel(initiative)" class="initiative-box__period">
                                                        {{ getInitiativePeriodLabel(initiative) }}
                                                    </span>
                                                    <span v-if="showBusinessUnit" class="initiative-box__bu">
                                                        {{ initiative.business_unit }}
                                                    </span>
                                                </span>

                                                <button
                                                    v-if="editable && isValidId(group.primary_id) && isValidId(secondaryGroup.secondary_id) && isValidId(initiative.initiative_id)"
                                                    type="button"
                                                    class="initiative-box__remove"
                                                    @click.stop.prevent="openDeleteInitiativeModal(group, secondaryGroup, initiative)"
                                                >
                                                    x
                                                </button>
                                            </template>
                                        </component>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        <section
            v-else
            class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm dark:border-white/15 dark:bg-[#171717]"
        >
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Initiative Not Available
            </p>
        </section>

        <ConfirmationModal
            :show="isModalVisible"
            :title="modalTitle"
            :message="modalMessage"
            :type="modalType"
            :loading="modalLoading"
            :confirm-text="modalConfirmText"
            cancel-text="Batal"
            max-width="2xl"
            @close="closeModal"
            @confirm="handleModalConfirm"
            @after-leave="handleModalAfterLeave"
        >
            <div v-if="isAddModal" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Primary</label>
                        <select
                            v-model="mappingForm.primary"
                            class="edit-select"
                            :disabled="activeModal === 'add-secondary' || activeModal === 'add-initiative'"
                        >
                            <option value="">- Pilih Primary -</option>
                            <option
                                v-for="option in coeOptions"
                                :key="`modal-primary-option-${option.id}`"
                                :value="String(option.id)"
                            >
                                {{ option.name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Secondary</label>
                        <select
                            v-model="mappingForm.secondary"
                            class="edit-select"
                            :disabled="activeModal === 'add-initiative'"
                        >
                            <option value="">- Pilih Secondary -</option>
                            <option
                                v-for="option in coeOptions"
                                :key="`modal-secondary-option-${option.id}`"
                                :value="String(option.id)"
                            >
                                {{ option.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Digital Initiative</label>
                    <div class="space-y-2">
                        <select
                            class="edit-select"
                            @change="onInitiativeSelect"
                        >
                            <option value="">+ Pilih Digital Initiative...</option>
                            <option
                                v-for="option in initiativeOptions"
                                :key="`modal-initiative-option-${option.id}`"
                                :value="String(option.id)"
                                :disabled="mappingForm.initiative_ids.includes(Number(option.id)) || isInitiativeAlreadyMapped(option.id)"
                            >
                                {{ initiativeOptionLabel(option) }}
                            </option>
                        </select>

                        <div v-if="selectedInitiatives.length > 0" class="flex flex-wrap gap-2">
                            <span
                                v-for="initiative in selectedInitiatives"
                                :key="`modal-selected-initiative-${initiative.id}`"
                                class="initiative-tag"
                            >
                                {{ initiativeOptionLabel(initiative) }}
                                <button
                                    type="button"
                                    class="initiative-tag__remove"
                                    @click="removeInitiativeSelection(initiative.id)"
                                >
                                    x
                                </button>
                            </span>
                        </div>
                        <p v-else class="text-[11px] text-slate-500 dark:text-slate-400">
                            Tambahkan satu atau lebih initiative seperti field RJPP tagging.
                        </p>
                    </div>
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
.edit-select {
    width: 100%;
    height: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    background: #ffffff;
    padding: 0 0.875rem;
    font-size: 12px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.edit-select:focus {
    border-color: #0f6fb7;
    box-shadow: 0 0 0 4px rgba(15, 111, 183, 0.12);
}

.itb-table {
    background: #ffffff;
    width: 100%;
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

.itb-table th,
.itb-table td {
    border: 1px solid #c7d2de;
    vertical-align: top;
}

.initiative-view-switch {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 6px;
    border-radius: 12px;
    background: transparent;
    padding: 2px;
    width: 100%;
    overflow-x: auto;
    scrollbar-width: none; /* Hide scrollbar for cleaner look */
}

.initiative-view-switch::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome/Safari/Edge */
}


.bu-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
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

.bu-toggle-btn span {
    white-space: nowrap;
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

.initiative-view-select {
    appearance: none;
    flex-shrink: 0;
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

.top-head {
    padding: 10px 12px;
    background: #326eb2;
    color: #ffffff;
    font-size: 14px;
    font-weight: 800;
    line-height: 1.1;
    text-align: center;
}

.top-head-left {
    width: 20%;
}

.top-head-right {
    width: 80%;
}

.sub-head {
    padding: 6px 10px;
    background: #eef4f8;
    color: #4f6b85;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
}

.sub-head-primary {
    width: 8%;
}

.sub-head-secondary {
    width: 10%;
}

.sub-head-initiative {
    width: 80%;
}

.primary-cell {
    padding: 0;
    background: #0f6fb7;
    color: #ffffff;
    vertical-align: middle !important;
}

.primary-cell__content {
    display: flex;
    min-height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 12px;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.25;
}

.secondary-cell {
    padding: 0;
    background: linear-gradient(180deg, #78b8ea 0%, #63a9df 100%);
    color: #ffffff;
    vertical-align: middle !important;
    text-align: center;
    min-width: 140px;
}

.secondary-cell__content {
    display: flex;
    min-height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 10px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.25;
}

.primary-label-wrapper,
.secondary-label-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
}

.count-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    font-size: 10px;
    font-weight: 800;
    color: #ffffff;
    flex-shrink: 0;
}

.cell-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
}

.cell-actions--primary {
    max-width: 120px;
}

.cell-action-btn {
    border: 1px solid rgba(255, 255, 255, 0.38);
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.14);
    padding: 4px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #ffffff;
    transition: background 0.15s ease, border-color 0.15s ease;
}

.cell-action-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.52);
}

.cell-action-btn:disabled {
    cursor: not-allowed;
    opacity: 0.45;
}

.cell-action-btn--danger {
    background: rgba(190, 24, 93, 0.16);
    border-color: rgba(255, 255, 255, 0.3);
}

.cell-action-btn--danger:hover {
    background: rgba(190, 24, 93, 0.28);
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
    align-items: stretch;
    gap: 8px;
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

/* COE Color Classes - High Contrast & Deep Colors */
.coe-color-blue { background-color: #dbeafe; border-color: #1d4ed8 !important; color: #1e3a8a; }
.coe-color-emerald { background-color: #d1fae5; border-color: #047857 !important; color: #065f46; }
.coe-color-amber { background-color: #fef3c7; border-color: #b45309 !important; color: #92400e; }
.coe-color-purple { background-color: #ede9fe; border-color: #6d28d9 !important; color: #5b21b6; }
.coe-color-rose { background-color: #ffe4e6; border-color: #be123c !important; color: #9f1239; }
.coe-color-indigo { background-color: #e0e7ff; border-color: #4338ca !important; color: #3730a3; }
.coe-color-none { background-color: #f8fafc; border-color: #475569 !important; color: #334155; }

/* Legend Swatches - Solid Deep Colors */
.legend-swatch.coe-color-blue { background-color: #dbeafe !important; border: 1px solid #1d4ed8; }
.legend-swatch.coe-color-emerald { background-color: #d1fae5 !important; border: 1px solid #047857; }
.legend-swatch.coe-color-amber { background-color: #fef3c7 !important; border: 1px solid #b45309; }
.legend-swatch.coe-color-purple { background-color: #ede9fe !important; border: 1px solid #6d28d9; }
.legend-swatch.coe-color-rose { background-color: #ffe4e6 !important; border: 1px solid #be123c; }
.legend-swatch.coe-color-indigo { background-color: #e0e7ff !important; border: 1px solid #4338ca; }
.legend-swatch.coe-color-none { background-color: #f8fafc !important; border: 1px solid #475569; }

.coe-color-blue .initiative-box__code { border-right-color: #1d4ed8; background-color: rgba(29, 78, 216, 0.1); }
.coe-color-emerald .initiative-box__code { border-right-color: #047857; background-color: rgba(4, 120, 87, 0.1); }
.coe-color-amber .initiative-box__code { border-right-color: #b45309; background-color: rgba(180, 83, 9, 0.1); }
.coe-color-purple .initiative-box__code { border-right-color: #6d28d9; background-color: rgba(109, 40, 217, 0.1); }
.coe-color-rose .initiative-box__code { border-right-color: #be123c; background-color: rgba(190, 18, 60, 0.1); }
.coe-color-indigo .initiative-box__code { border-right-color: #4338ca; background-color: rgba(67, 56, 202, 0.1); }

/* Implementation Status Colors — distinct from COE colors */
.status-color-df { background-color: #0d9488 !important; color: #ffffff !important; border-color: #0f766e !important; }
.status-color-done { background-color: #65a30d !important; color: #ffffff !important; border-color: #4d7c0f !important; }
.status-color-dt2026 { background-color: #ea580c !important; color: #ffffff !important; border-color: #c2410c !important; }
.status-color-itsbp { background-color: #06b6d4 !important; color: #ffffff !important; border-color: #0891b2 !important; }
.status-color-onreview { background-color: #ca8a04 !important; color: #ffffff !important; border-color: #a16207 !important; }
.status-color-onprogress { background-color: #2563eb !important; color: #ffffff !important; border-color: #1d4ed8 !important; }
.status-color-sh { background-color: #ef4444 !important; color: #ffffff !important; border-color: #dc2626 !important; }

.initiative-box--placeholder {
    visibility: hidden;
    pointer-events: none;
}

.initiative-box__code {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #374151;
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

.initiative-box__period {
    font-size: 7.5px;
    font-weight: 700;
    font-style: italic;
    color: #64748b;
    margin-top: 1px;
}

.initiative-box__label-text {
    line-height: 1.1;
}

.initiative-box__bu {
    display: block;
    width: 100%;
    margin-top: 1px;
    font-size: 7.5px;
    font-weight: 700;
    font-style: italic;
    color: inherit;
    opacity: 0.7;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.initiative-box__name--full {
    grid-column: 1 / -1;
    padding-left: 5px;
}

.initiative-box__remove {
    position: absolute;
    top: 2px;
    right: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    padding: 0;
    font-size: 9px;
    font-weight: 700;
    color: #dc2626;
    cursor: pointer;
}

.itb-table--six-cols .top-head {
    padding: 8px 10px;
    font-size: 13px;
}

.itb-table--six-cols .top-head-left {
    width: 17%;
}

.itb-table--six-cols .top-head-right {
    width: 83%;
}

.itb-table--six-cols .sub-head {
    padding: 5px 8px;
    font-size: 11px;
}

.itb-table--six-cols .sub-head-secondary {
    width: 12%;
}

.itb-table--six-cols .sub-head-initiative {
    width: 80%;
}

.itb-table--six-cols .primary-cell__content {
    gap: 8px;
    padding: 12px 8px;
    font-size: 11px;
}

.itb-table--six-cols .secondary-cell {
    min-width: 110px;
}

.itb-table--six-cols .secondary-cell__content {
    gap: 8px;
    padding: 10px 8px;
    font-size: 10px;
}

.itb-table--six-cols .cell-action-btn {
    padding: 3px 8px;
    font-size: 9px;
}

.itb-table--six-cols .cell-actions--primary {
    max-width: 96px;
}

.itb-table--six-cols .initiatives-cell {
    padding: 6px;
}

.itb-table--six-cols .initiatives-grid,
.itb-table--six-cols .initiatives-column {
    gap: 6px;
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

:deep(.dark) .itb-toolbar {
    border-bottom-color: rgba(148, 163, 184, 0.14);
    background: linear-gradient(180deg, rgba(23, 23, 23, 0.98) 0%, rgba(15, 23, 42, 0.92) 100%);
}

:deep(.dark) .initiative-view-switch {
    border-color: rgba(148, 163, 184, 0.22);
    background: rgba(15, 23, 42, 0.9);
    box-shadow: none;
}

:deep(.dark) .initiative-view-switch__label {
    color: #cbd5e1;
}

:deep(.dark) .initiative-view-switch__options {
    background: rgba(51, 65, 85, 0.9);
}

:deep(.dark) .initiative-view-switch__button {
    color: #cbd5e1;
}

:deep(.dark) .initiative-view-switch__button:hover {
    background: rgba(148, 163, 184, 0.12);
    border-color: rgba(148, 163, 184, 0.2);
    color: #bfdbfe;
}

:deep(.dark) .coe-color-blue { background-color: #dbeafe; color: #1e3a8a; }
:deep(.dark) .coe-color-purple { background-color: #ede9fe; color: #5b21b6; }
:deep(.dark) .coe-color-rose { background-color: #ffe4e6; color: #9f1239; }
:deep(.dark) .coe-color-indigo { background-color: #e0e7ff; color: #3730a3; }
:deep(.dark) .coe-color-emerald { background-color: #d1fae5; color: #065f46; }
:deep(.dark) .coe-color-amber { background-color: #fef3c7; color: #92400e; }
:deep(.dark) .coe-color-none { background-color: #f8fafc; color: #334155; }
:deep(.dark) .initiative-box { color: inherit; }
:deep(.dark) .initiative-box__code { color: inherit; }

:deep(.dark) .coe-color-blue .initiative-box__code,
:deep(.dark) .coe-color-emerald .initiative-box__code,
:deep(.dark) .coe-color-amber .initiative-box__code,
:deep(.dark) .coe-color-purple .initiative-box__code,
:deep(.dark) .coe-color-rose .initiative-box__code,
:deep(.dark) .coe-color-indigo .initiative-box__code,
:deep(.dark) .coe-color-none .initiative-box__code {
    color: inherit;
}

@media (max-width: 1024px) {
    .itb-toolbar {
        padding: 12px;
    }

    .initiative-view-switch {
        justify-content: flex-start;
    }

    .itb-toolbar + .overflow-x-auto {
        padding-top: 10px;
    }

    .initiatives-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .top-head {
        font-size: 13px;
    }

    .sub-head {
        font-size: 11px;
    }

    .primary-cell__content {
        font-size: 11px;
    }

    .secondary-cell {
        min-width: 120px;
    }

    .secondary-cell__content {
        font-size: 10px;
    }

    .initiative-box {
        font-size: 10px;
    }

    .initiative-box__code {
        padding: 6px 9px;
    }

    .initiative-box__name {
        max-width: none;
        padding: 6px 24px 6px 10px;
    }
}

@media (max-width: 768px) {
    .initiative-view-switch {
        flex-wrap: nowrap;
        justify-content: flex-start;
        width: 100%;
    }

    .initiatives-grid {
        grid-template-columns: 1fr;
    }
}
</style>
