<template>
    <ConfirmationModal
        :show="show"
        :title="isEditing ? 'Edit Status Implementation' : 'Add Status Implementation'"
        :message="isEditing ? 'Perbarui data status implementation yang dipilih.' : 'Tambahkan data status implementation baru.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        max-width="2xl"
        :loading="form.processing"
        @close="closeModal"
        @confirm="submit"
        @after-leave="resetModalState"
    >
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                    Initiative
                </label>
                <select
                    v-model="form.initiative_id"
                    class="field-input"
                >
                    <option value="">Pilih Initiative</option>
                    <option
                        v-for="initiative in filteredInitiatives"
                        :key="`modal-initiative-${initiative.value}`"
                        :value="initiative.value"
                    >
                        {{ initiative.label }}
                    </option>
                </select>
                <p
                    v-if="form.errors.initiative_id"
                    class="text-xs text-rose-600 dark:text-rose-400"
                >
                    {{ form.errors.initiative_id }}
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-[120px_150px_150px_100px]">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                        Review Status
                    </label>
                    <input
                        v-model="form.review_status"
                        list="digital-review-status-options"
                        type="text"
                        maxlength="11"
                        placeholder="Contoh: ITSBP"
                        class="field-input"
                    />
                    <datalist id="digital-review-status-options">
                        <option
                            v-for="option in reviewStatusSuggestions"
                            :key="`review-status-${option}`"
                            :value="option"
                        />
                    </datalist>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">
                        Maksimal 11 karakter.
                    </p>
                    <p
                        v-if="form.errors.review_status"
                        class="text-xs text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.review_status }}
                    </p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                        Start Month
                    </label>
                    <select
                        v-model="form.start_month"
                        class="field-input"
                    >
                        <option value="">Pilih Bulan</option>
                        <option
                            v-for="month in monthOptions"
                            :key="`start-month-${month.value}`"
                            :value="month.value"
                        >
                            {{ month.label }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.start_month"
                        class="text-xs text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.start_month }}
                    </p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                        End Month
                    </label>
                    <select
                        v-model="form.end_month"
                        class="field-input"
                    >
                        <option value="">Pilih Bulan</option>
                        <option
                            v-for="month in monthOptions"
                            :key="`end-month-${month.value}`"
                            :value="month.value"
                        >
                            {{ month.label }}
                        </option>
                    </select>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">
                        Opsional, boleh dikosongkan.
                    </p>
                    <p
                        v-if="form.errors.end_month"
                        class="text-xs text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.end_month }}
                    </p>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                        Year
                    </label>
                    <input
                        v-model="form.year"
                        type="number"
                        min="2000"
                        max="2099"
                        placeholder="2026"
                        class="field-input"
                    />
                    <p
                        v-if="form.errors.year"
                        class="text-xs text-rose-600 dark:text-rose-400"
                    >
                        {{ form.errors.year }}
                    </p>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                    Updated Status
                </label>
                <textarea
                    v-model="form.status_updated"
                    rows="5"
                    placeholder="Masukkan update status implementation."
                    class="field-input min-h-[120px] resize-y py-2.5"
                ></textarea>
                <p
                    v-if="form.errors.status_updated"
                    class="text-xs text-rose-600 dark:text-rose-400"
                >
                    {{ form.errors.status_updated }}
                </p>
            </div>
        </div>
    </ConfirmationModal>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    statusData: {
        type: Object,
        default: null,
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    defaultInitiativeId: {
        type: [String, Number],
        default: '',
    },
    defaultMonth: {
        type: [String, Number],
        default: '',
    },
    defaultYear: {
        type: [String, Number],
        default: '',
    },
});

const emit = defineEmits(['close']);

const route = useRouteHelper();
const currentYear = new Date().getFullYear();

const form = useForm({
    initiative_id: '',
    review_status: '',
    start_month: '',
    end_month: '',
    year: String(currentYear),
    status_updated: '',
});

const monthOptions = [
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' },
];

const isEditing = computed(() => props.statusData !== null);

const normalizedInitiatives = computed(() => {
    return (Array.isArray(props.initiativeOptions) ? props.initiativeOptions : [])
        .map((initiative) => {
            const id = Number(initiative?.id);
            const code = String(initiative?.code ?? '').trim();
            const name = String(initiative?.name ?? '').trim();

            if (!Number.isInteger(id) || id <= 0 || name === '') {
                return null;
            }

            return {
                value: String(id),
                label: code !== '' ? `${code} - ${name}` : name,
            };
        })
        .filter(Boolean);
});

const filteredInitiatives = computed(() => normalizedInitiatives.value);

const reviewStatusSuggestions = computed(() => {
    return [
        props.statusData?.review_status,
        'ITSBP',
        'On Track',
        'At Risk',
        'Not Signed',
    ].filter((value, index, values) => {
        const normalizedValue = String(value ?? '').trim();

        return normalizedValue !== '' && values.findIndex(
            (item) => String(item ?? '').trim().toLowerCase() === normalizedValue.toLowerCase(),
        ) === index;
    });
});

const normalizeStringValue = (value) => {
    const normalizedValue = String(value ?? '').trim();

    return normalizedValue !== '' ? normalizedValue : '';
};

const applyFormDefaults = () => {
    if (isEditing.value) {
        form.initiative_id = normalizeStringValue(props.statusData?.initiative_id);
        form.review_status = normalizeStringValue(props.statusData?.review_status);
        form.start_month = normalizeStringValue(props.statusData?.period_start_month);
        form.end_month = normalizeStringValue(props.statusData?.period_end_month);
        form.year = normalizeStringValue(props.statusData?.period_year) || String(currentYear);
        form.status_updated = normalizeStringValue(props.statusData?.updated_status);
        form.clearErrors();

        return;
    }

    form.initiative_id = normalizeStringValue(props.defaultInitiativeId);
    form.review_status = '';
    form.start_month = normalizeStringValue(props.defaultMonth);
    form.end_month = '';
    form.year = normalizeStringValue(props.defaultYear) || String(currentYear);
    form.status_updated = '';
    form.clearErrors();
};

const closeModal = () => {
    emit('close');
};

const resetModalState = () => {
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (form.processing) {
        return;
    }

    const submissionForm = form.transform((data) => ({
        ...data,
        review_status: normalizeStringValue(data.review_status),
        end_month: normalizeStringValue(data.end_month) || null,
        status_updated: normalizeStringValue(data.status_updated),
    }));

    const requestOptions = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            Swal.fire({
                title: 'Berhasil!',
                text: isEditing.value 
                    ? 'Status implementation berhasil diperbarui.' 
                    : 'Status implementation berhasil ditambahkan.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
    };

    if (isEditing.value) {
        submissionForm.put(
            route('digital-initiatives.implementation-status.update', props.statusData.id),
            requestOptions,
        );

        return;
    }

    submissionForm.post(route('digital-initiatives.implementation-status.store'), requestOptions);
};

watch(
    () => props.show,
    (show) => {
        if (show) {
            applyFormDefaults();
        }
    },
    { immediate: true },
);

watch(
    () => form.initiative_id,
    (initiativeId) => {
        const currentInitiativeStillValid = filteredInitiatives.value.some(
            (initiative) => initiative.value === normalizeStringValue(initiativeId),
        );

        if (!currentInitiativeStillValid) {
            form.initiative_id = '';
        }
    },
);
</script>

<style scoped>
.field-input {
    width: 100%;
    border-radius: 0.75rem;
    border: 1px solid rgb(203 213 225);
    background: #ffffff;
    padding: 0.625rem 0.875rem;
    font-size: 12px;
    color: rgb(51 65 85);
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.field-input:focus {
    border-color: #1c75bc;
    box-shadow: 0 0 0 4px rgba(28, 117, 188, 0.14);
}

.dark .field-input {
    border-color: rgba(255, 255, 255, 0.1);
    background: #101826;
    color: rgb(226 232 240);
}
</style>
