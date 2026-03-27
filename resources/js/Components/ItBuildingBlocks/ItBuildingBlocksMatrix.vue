<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const route = useRouteHelper();
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
});

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

const displayGroups = computed(() => {
    return props.groups
        .map((group) => {
            const secondaryGroups = (group?.secondary_groups ?? [])
                .map((secondaryGroup) => {
                    const initiatives = (secondaryGroup?.initiatives ?? []).filter((initiative) => {
                        return !activeRemovalKeys.value.has(
                            createRemovalKey(group?.primary_id, secondaryGroup?.secondary_id, initiative?.initiative_id),
                        );
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

const hasGroups = computed(() => displayGroups.value.length > 0);
const hasPendingInitiativeRemovals = computed(() => pendingInitiativeRemovals.value.length > 0);

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

const initiativeOptionLabel = (initiative) => {
    const code = String(initiative?.code ?? '').trim();
    const name = String(initiative?.name ?? '').trim();

    if (code !== '' && name !== '') {
        return `[${code}] ${name}`;
    }

    return name || code || '-';
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

    router.post(route('program-implementation.it-building-blocks.store'), {
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

    router.delete(route('program-implementation.it-building-blocks.primary.destroy', {
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

    router.delete(route('program-implementation.it-building-blocks.secondary.destroy', {
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

    router.post(route('program-implementation.it-building-blocks.initiative.bulk-destroy'), {
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

        <section
            v-if="hasGroups"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
        >
            <div class="overflow-x-auto">
                <table class="itb-table min-w-full border-collapse">
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
                            <th class="sub-head sub-head-initiative">
                                Initiative Boxes
                            </th>
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
                                        <span>{{ group.primary }}</span>

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
                                        <span>{{ secondaryGroup.secondary }}</span>

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
                                    <div class="initiatives-grid">
                                        <div
                                            v-for="initiative in secondaryGroup.initiatives"
                                            :key="`initiative-${initiative.map_key}`"
                                            class="initiative-box"
                                            :title="initiativeOptionLabel(initiative)"
                                        >
                                            <span class="initiative-box__label">
                                                {{ initiative.name }}
                                            </span>

                                            <button
                                                v-if="editable && isValidId(group.primary_id) && isValidId(secondaryGroup.secondary_id) && isValidId(initiative.initiative_id)"
                                                type="button"
                                                class="initiative-box__remove"
                                                @click="openDeleteInitiativeModal(group, secondaryGroup, initiative)"
                                            >
                                                x
                                            </button>
                                        </div>
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
                Belum ada data IT Building Block.
            </p>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                {{ editable ? 'Klik tombol Tambah Mapping untuk menambahkan mapping pertama melalui modal.' : 'Masuk ke mode edit lalu gunakan tombol Tambah Mapping untuk mulai menambahkan data.' }}
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
    width: 10%;
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
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.initiative-box {
    position: relative;
    display: inline-flex;
    min-height: 28px;
    max-width: 210px;
    align-items: center;
    border: 1px solid #4b5563;
    background: #ffffff;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.25;
    color: #1f2937;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
}

.initiative-box__label {
    padding-right: 18px;
}

.initiative-box__remove {
    position: absolute;
    top: 3px;
    right: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    padding: 0;
    font-size: 10px;
    font-weight: 700;
    color: #dc2626;
    cursor: pointer;
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

@media (max-width: 1024px) {
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
        max-width: 160px;
        font-size: 10px;
    }
}
</style>
