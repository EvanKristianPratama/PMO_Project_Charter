<template>
    <UserLayout title="Edit IT Initiative">
        <div class="mx-auto max-w-[1860px] animate-fade-in space-y-6">
            <div>
                <Link
                    href="/it-initiatives"
                    class="mb-2 flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Edit IT Initiative</h2>
            </div>

            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Project Charter IT Initiatives</h3>
                </div>

                <div class="overflow-x-hidden">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[4%]">
                            <col class="w-[6%]">
                            <col class="w-[12%]">
                            <col class="w-[15%]">
                            <col class="w-[9%]">
                            <col class="w-[10%]">
                            <col class="w-[10%]">
                            <col class="w-[24%]">
                            <col class="w-[10%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Versi</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">IT Arsitektur Building Blok</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Project Charter</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Bulan / Tahun</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Review</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Implementasi</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr class="bg-slate-50/50 dark:bg-white/[0.03]">
                                <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ nextVersionNumber }}</td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.code" type="text" class="table-input" placeholder="PRJ-001">
                                    <p v-if="form.errors.code" class="mt-1 text-[10px] text-rose-600">{{ form.errors.code }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.charter_category" type="text" class="table-input" placeholder="Contoh: Integration and Automation">
                                    <p v-if="form.errors.charter_category" class="mt-1 text-[10px] text-rose-600">{{ form.errors.charter_category }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.name" type="text" class="table-input" placeholder="Nama inisiatif">
                                    <p v-if="form.errors.name" class="mt-1 text-[10px] text-rose-600">{{ form.errors.name }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <select v-model="form.status" class="table-input">
                                        <option v-for="statusOption in statusOptions" :key="statusOption.id" :value="statusOption.id">
                                            {{ statusOption.label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.status" class="mt-1 text-[10px] text-rose-600">{{ form.errors.status }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="historyForm.month_year" type="month" class="table-input">
                                    <p v-if="historyForm.errors.month_year" class="mt-1 text-[10px] text-rose-600">{{ historyForm.errors.month_year }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <select v-model="historyForm.review_status" class="table-input">
                                        <option v-for="reviewStatusOption in reviewStatusOptions" :key="`new-${reviewStatusOption}`" :value="reviewStatusOption">
                                            {{ reviewStatusOption }}
                                        </option>
                                    </select>
                                    <p v-if="historyForm.errors.review_status" class="mt-1 text-[10px] text-rose-600">{{ historyForm.errors.review_status }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <textarea
                                        v-model="historyForm.status"
                                        rows="3"
                                        class="table-input table-textarea"
                                        placeholder="Contoh: Mulai Tahun 2026"
                                    />
                                    <p v-if="historyForm.errors.status" class="mt-1 text-[10px] text-rose-600">{{ historyForm.errors.status }}</p>
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium align-top">
                                    <div class="flex flex-col items-start gap-1">
                                        <button
                                            type="button"
                                            :disabled="form.processing"
                                            class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[9px] font-semibold text-sky-700 transition-colors hover:bg-sky-200 disabled:opacity-50 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                            @click="submitProject"
                                        >
                                            Save Data Utama
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="historyForm.processing"
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-semibold text-emerald-700 transition-colors hover:bg-emerald-200 disabled:opacity-50 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30"
                                            @click="submitHistory"
                                        >
                                            Tambah Status
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr
                                v-for="(log, index) in implementationLogs"
                                :key="`history-${log.id}`"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5"
                            >
                                <td class="px-3 py-3 text-[11px] font-medium text-slate-600 dark:text-slate-400">{{ historyVersionNumber(index) }}</td>
                                <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ form.code || '-' }}</td>
                                <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                        {{ form.charter_category || '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-[11px] text-slate-700 dark:text-slate-200">
                                    <span class="font-medium break-words">{{ form.name || '-' }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClassById(form.status)">
                                        {{ selectedStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="getHistoryDraft(log).month_year" type="month" class="table-input">
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <select v-model="getHistoryDraft(log).review_status" class="table-input">
                                        <option v-for="reviewStatusOption in reviewStatusOptions" :key="`${log.id}-${reviewStatusOption}`" :value="reviewStatusOption">
                                            {{ reviewStatusOption }}
                                        </option>
                                    </select>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <textarea
                                        v-model="getHistoryDraft(log).status"
                                        rows="3"
                                        class="table-input table-textarea"
                                    />
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium align-top">
                                    <div class="flex flex-col items-start gap-1">
                                        <button
                                            type="button"
                                            :disabled="getHistoryDraft(log).processing"
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-700 transition-colors hover:bg-amber-200 disabled:opacity-50 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                                            @click="updateHistory(log.id)"
                                        >
                                            Update
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="getHistoryDraft(log).processing"
                                            class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-semibold text-rose-700 transition-colors hover:bg-rose-200 disabled:opacity-50 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30"
                                            @click="openDeleteModal(log.id)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                    <p v-if="getHistoryDraft(log).error" class="mt-1 text-[10px] text-rose-600">
                                        {{ getHistoryDraft(log).error }}
                                    </p>
                                </td>
                            </tr>

                            <tr v-if="implementationLogs.length === 0">
                                <td colspan="9" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Belum ada status implementasi. Gunakan baris pertama untuk menambahkan data.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <!-- Mapping IT Definition (Planning) -->
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Mapping IT Definition (Planning)</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Terpilih: {{ mappingForm.initiative_ids.length }}</span>
                            <button
                                type="button"
                                :disabled="mappingForm.processing"
                                class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[9px] font-semibold text-sky-700 disabled:opacity-50 dark:bg-sky-500/20 dark:text-sky-300"
                                @click="saveMapping"
                            >
                                Save Mapping
                            </button>
                        </div>
                    </div>
                </div>

                <div class="max-h-72 overflow-y-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[5%]">
                            <col class="w-[15%]">
                            <col class="w-[35%]">
                            <col class="w-[45%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"></th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Inisiatif</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr
                                v-for="initiative in planningDefinitions"
                                :key="`mapping-it-${initiative.id}`"
                                class="cursor-pointer"
                                @click="toggleMapping(initiative.id)"
                            >
                                <td class="px-3 py-2 text-center">
                                    <input
                                        v-model="mappingForm.initiative_ids"
                                        type="checkbox"
                                        :value="Number(initiative.id)"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-white/20"
                                        @click.stop
                                    />
                                </td>
                                <td class="px-3 py-2 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ initiative.code || '-' }}</td>
                                <td class="px-3 py-2 text-[11px] font-medium text-slate-700 dark:text-slate-200">{{ initiative.name || '-' }}</td>
                                <td class="px-3 py-2 text-[11px] text-slate-500 dark:text-slate-400">{{ initiative.description || '-' }}</td>
                            </tr>
                            <tr v-if="planningDefinitions.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Belum ada data IT Definition (tipe 2) di mst_initiative.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <StatusModal
                :show="showDeleteModal"
                mode="confirm"
                title="Hapus Status Implementasi"
                :message="deleteModalMessage"
                confirm-text="Ya, Hapus"
                cancel-text="Batal"
                confirm-tone="danger"
                @close="closeDeleteModal"
                @confirm="confirmDeleteHistory"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import StatusModal from '@/Components/StatusModal.vue';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const props = defineProps({
    itInitiative: {
        type: Object,
        required: true,
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    defaultStatusId: {
        type: Number,
        default: 1,
    },
    planningItDefinitions: {
        type: Array,
        default: () => [],
    },
    mappedInitiativeIds: {
        type: Array,
        default: () => [],
    },
});

const statusOptions = props.statusOptions.length > 0
    ? props.statusOptions
    : [{ id: 1, label: 'Drafting' }];

const reviewStatusOptions = ['On Track', 'At Risk', 'Not Started', 'Not Signed'];

const resolvedDefaultStatusId = statusOptions.some((statusOption) => Number(statusOption.id) === Number(props.defaultStatusId))
    ? props.defaultStatusId
    : statusOptions[0].id;

const currentMonthYear = () => new Date().toISOString().slice(0, 7);

const form = useForm({
    code: props.itInitiative.code ?? '',
    name: props.itInitiative.name ?? '',
    owner_name: props.itInitiative.owner_name ?? '',
    status: props.itInitiative.status ?? resolvedDefaultStatusId,
    charter_category: props.itInitiative.charter?.category ?? '',
});

const historyForm = useForm({
    status: '',
    review_status: 'Not Started',
    month_year: currentMonthYear(),
});

const implementationLogs = computed(() => {
    const source = props.itInitiative?.pc_status_implementations ?? props.itInitiative?.pcStatusImplementations ?? [];

    return Array.isArray(source) ? source : [];
});

const nextVersionNumber = computed(() => implementationLogs.value.length + 1);

const selectedStatusLabel = computed(() => statusLabelFromOptions(form.status, statusOptions));

const historyDrafts = reactive({});
const showDeleteModal = ref(false);
const pendingDeleteId = ref(null);

const toMonthInput = (rawDate) => {
    const normalized = String(rawDate ?? '').trim();
    const match = normalized.match(/^(\d{4})-(\d{2})/);

    if (match) {
        return `${match[1]}-${match[2]}`;
    }

    return currentMonthYear();
};

const getHistoryDraft = (log) => {
    if (!historyDrafts[log.id]) {
        historyDrafts[log.id] = {
            month_year: toMonthInput(log.date),
            review_status: log.review_status || 'Not Started',
            status: log.status || '',
            processing: false,
            error: '',
        };
    }

    return historyDrafts[log.id];
};

const historyVersionNumber = (index) => {
    return Math.max(implementationLogs.value.length - index, 1);
};

const submitProject = () => {
    form.put(`/it-initiatives/${props.itInitiative.id}`, {
        preserveScroll: true,
    });
};

const submitHistory = () => {
    historyForm.post(`/it-initiatives/${props.itInitiative.id}/implementation-status`, {
        preserveScroll: true,
        onSuccess: () => {
            historyForm.reset('status');
            historyForm.review_status = 'Not Started';
            historyForm.month_year = currentMonthYear();
        },
    });
};

const updateHistory = (id) => {
    const draft = historyDrafts[id];
    if (!draft) return;

    draft.processing = true;
    draft.error = '';

    router.put(`/implementation-status/${id}`, {
        month_year: draft.month_year,
        review_status: draft.review_status,
        status: draft.status,
    }, {
        preserveScroll: true,
        onError: (errors) => {
            draft.error = errors.month_year || errors.review_status || errors.status || 'Gagal memperbarui status implementasi.';
        },
        onFinish: () => {
            draft.processing = false;
        },
    });
};

const deleteModalMessage = computed(() => {
    if (!pendingDeleteId.value) {
        return 'Apakah Anda yakin ingin menghapus status implementasi ini?';
    }

    const selectedLog = implementationLogs.value.find((log) => Number(log.id) === Number(pendingDeleteId.value));
    if (!selectedLog) {
        return 'Apakah Anda yakin ingin menghapus status implementasi ini?';
    }

    const monthYear = toMonthInput(selectedLog.date);
    return `Data status implementasi periode ${monthYear} akan dihapus permanen. Lanjutkan?`;
});

const openDeleteModal = (id) => {
    pendingDeleteId.value = id;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    pendingDeleteId.value = null;
};

// --- Mapping ---
const planningDefinitions = Array.isArray(props.planningItDefinitions) ? props.planningItDefinitions : [];

const mappingForm = useForm({
    initiative_ids: Array.isArray(props.mappedInitiativeIds)
        ? props.mappedInitiativeIds.map((id) => Number(id))
        : [],
});

const toggleMapping = (id) => {
    const numId = Number(id);
    const idx = mappingForm.initiative_ids.indexOf(numId);
    if (idx >= 0) {
        mappingForm.initiative_ids.splice(idx, 1);
    } else {
        mappingForm.initiative_ids.push(numId);
    }
};

const saveMapping = () => {
    mappingForm.put(`/it-initiatives/${props.itInitiative.id}/mapping`, {
        preserveScroll: true,
    });
};

const confirmDeleteHistory = () => {
    if (!pendingDeleteId.value) {
        closeDeleteModal();
        return;
    }

    const deletedId = pendingDeleteId.value;
    showDeleteModal.value = false;

    router.delete(`/implementation-status/${deletedId}`, {
        preserveScroll: true,
        onFinish: () => {
            pendingDeleteId.value = null;
        },
    });
};
</script>

<style scoped>
.table-input {
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid rgb(203 213 225);
    background-color: rgb(255 255 255);
    padding: 0.25rem 0.5rem;
    font-size: 11px;
    color: rgb(15 23 42);
}

.table-input:focus {
    border-color: rgb(99 102 241);
    outline: none;
    box-shadow: 0 0 0 1px rgb(99 102 241 / 0.35);
}

.table-textarea {
    min-height: 64px;
    resize: vertical;
    line-height: 1.35;
}

:global(.dark) .table-input {
    border-color: rgb(255 255 255 / 0.1);
    background-color: rgb(19 19 19);
    color: rgb(226 232 240);
}
</style>
