<template>
    <ModulLayout title="Edit Digital Initiative">
        <div class="mx-auto max-w-[1860px] animate-fade-in space-y-6">
            <div>
                <Link
                    :href="route('itsp.digital-initiatives.index')"
                    class="mb-2 flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </Link>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Digital Initiative</h2>
            </div>

            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Data Utama Project Charter</h3>
                </div>

                <div class="overflow-x-hidden">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[10%]">
                            <col class="w-[16%]">
                            <col class="w-[22%]">
                            <col class="w-[14%]">
                            <col class="w-[12%]">
                            <col class="w-[16%]">
                            <col class="w-[10%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Code</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Arsitektur Digital</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Daftar Inisiatif</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Project Charter</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Status</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notes</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr class="bg-slate-50/50 dark:bg-white/[0.03]">
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.code" type="text" class="table-input" placeholder="DI-001">
                                    <p v-if="form.errors.code" class="mt-1 text-[10px] text-rose-600">{{ form.errors.code }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.charter_category" type="text" class="table-input" placeholder="Contoh: Infrastructure">
                                    <p v-if="form.errors.charter_category" class="mt-1 text-[10px] text-rose-600">{{ form.errors.charter_category }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.name" type="text" class="table-input" placeholder="Nama inisiatif">
                                    <p v-if="form.errors.name" class="mt-1 text-[10px] text-rose-600">{{ form.errors.name }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <div class="space-y-2">
                                        <select v-model="form.status" class="table-input">
                                            <option v-for="statusOption in statusOptions" :key="statusOption.id" :value="statusOption.id">
                                                {{ statusOption.label }}
                                            </option>
                                        </select>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusBadgeClassById(form.status)">
                                            {{ selectedStatusLabel }}
                                        </span>
                                    </div>
                                    <p v-if="form.errors.status" class="mt-1 text-[10px] text-rose-600">{{ form.errors.status }}</p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input v-model="form.project_status_changed_at" type="date" class="table-input">
                                    <p class="mt-1 text-[10px] text-slate-400 dark:text-slate-500">
                                        Wajib diisi saat status project charter berubah.
                                    </p>
                                    <p v-if="form.errors.project_status_changed_at" class="mt-1 text-[10px] text-rose-600">
                                        {{ form.errors.project_status_changed_at }}
                                    </p>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <textarea
                                        v-model="form.project_status_notes"
                                        rows="3"
                                        class="table-input table-textarea"
                                        placeholder="Catatan perubahan status"
                                    />
                                    <p v-if="form.errors.project_status_notes" class="mt-1 text-[10px] text-rose-600">
                                        {{ form.errors.project_status_notes }}
                                    </p>
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium align-top">
                                    <button
                                        type="button"
                                        :disabled="form.processing"
                                        class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[9px] font-semibold text-sky-700 transition-colors hover:bg-sky-200 disabled:opacity-50 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                        @click="submitProject"
                                    >
                                        Save Data Utama
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <!-- Status History Table (similar to IT Initiatives) -->
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Project Status History</h3>
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ projectStatusHistories.length }} perubahan</span>
                    </div>
                </div>

                <div class="overflow-x-hidden">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[8%]">
                            <col class="w-[12%]">
                            <col class="w-[12%]">
                            <col class="w-[28%]">
                            <col class="w-[12%]">
                            <col class="w-[14%]">
                            <col class="w-[14%]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Versi</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notes</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Versi Charter</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tercatat Pada</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr
                                v-for="entry in sortedProjectStatusHistories"
                                :key="`project-status-history-${entry.id}`"
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5"
                            >
                                <td class="px-3 py-3 text-[11px] font-medium text-slate-700 dark:text-slate-200">v{{ historyVersion(entry) }}</td>
                                <td class="px-3 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium capitalize"
                                        :class="statusBadgeClassById(entry.status)"
                                    >
                                        {{ statusLabelFromOptions(entry.status, statusOptions) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <input
                                        v-if="getProjectStatusHistoryDraft(entry).editing"
                                        v-model="getProjectStatusHistoryDraft(entry).tanggal"
                                        type="date"
                                        class="table-input"
                                    >
                                    <span v-else class="text-[11px] text-slate-700 dark:text-slate-300">{{ formatDateOnly(historyTanggal(entry)) }}</span>
                                </td>
                                <td class="px-3 py-3 align-top">
                                    <template v-if="getProjectStatusHistoryDraft(entry).editing">
                                        <textarea
                                            v-model="getProjectStatusHistoryDraft(entry).notes"
                                            rows="3"
                                            class="table-input table-textarea"
                                        />
                                        <p class="mt-1 text-[10px] text-slate-400 dark:text-slate-500">
                                            Hanya tanggal dan notes yang bisa diubah.
                                        </p>
                                    </template>
                                    <span v-else class="text-[11px] text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ entry.notes || '-' }}</span>
                                    <p v-if="getProjectStatusHistoryDraft(entry).error" class="mt-1 text-[10px] text-rose-600">
                                        {{ getProjectStatusHistoryDraft(entry).error }}
                                    </p>
                                </td>
                                <td class="px-3 py-3 text-[11px] text-slate-600 dark:text-slate-300">{{ historyCharterLabel(entry) }}</td>
                                <td class="px-3 py-3 text-[11px] text-slate-500 dark:text-slate-400">{{ formatDateTime(entry.updated_at ?? entry.updatedAt ?? entry.created_at ?? entry.createdAt) }}</td>
                                <td class="px-3 py-3 text-[10px] font-medium align-top">
                                    <div class="flex flex-col items-start gap-1">
                                        <Link
                                            v-if="historyCharterId(entry)"
                                            :href="route('itsp.digital-initiatives.show', { digital_initiative: props.initiative.id, tab: 'charter' })"
                                            class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-[9px] font-semibold text-indigo-700 transition-colors hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:hover:bg-indigo-500/30"
                                        >
                                            Project Charter
                                        </Link>
                                        <template v-if="getProjectStatusHistoryDraft(entry).editing">
                                            <button
                                                type="button"
                                                :disabled="getProjectStatusHistoryDraft(entry).processing"
                                                class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[9px] font-semibold text-sky-700 transition-colors hover:bg-sky-200 disabled:opacity-50 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30"
                                                @click="updateProjectStatusHistory(entry.id)"
                                            >
                                                Update
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-semibold text-slate-600 transition-colors hover:bg-slate-200 dark:bg-white/10 dark:text-slate-300 dark:hover:bg-white/15"
                                                @click="cancelEditing(entry)"
                                            >
                                                Cancel
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-700 transition-colors hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                                                @click="toggleEditing(entry)"
                                            >
                                                Edit
                                            </button>
                                        </template>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-semibold text-rose-700 transition-colors hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-300 dark:hover:bg-rose-500/30"
                                            @click="confirmDeleteHistory(entry.id)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </ModulLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import { statusBadgeClassById, statusLabelFromOptions } from '@/Composables/initiativeStatus';

const route = useRouteHelper();

const props = defineProps({
    initiative: {
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
});

const statusOptions = props.statusOptions.length > 0
    ? props.statusOptions
    : [{ id: 1, label: 'Drafting' }];

const resolvedDefaultStatusId = statusOptions.some((statusOption) => statusOption.id === props.defaultStatusId)
    ? props.defaultStatusId
    : statusOptions[0].id;

const form = useForm({
    code: props.initiative.code ?? '',
    name: props.initiative.name ?? '',
    status: props.initiative.status ?? resolvedDefaultStatusId,
    charter_category: props.initiative.charter?.category ?? '',
    project_status_changed_at: '',
    project_status_notes: '',
});

const projectStatusHistories = computed(() => {
    const source = props.initiative?.project_status_histories ?? props.initiative?.projectStatusHistories ?? [];
    return Array.isArray(source) ? source : [];
});

const sortedProjectStatusHistories = computed(() => {
    return [...projectStatusHistories.value].sort((a, b) => {
        const versionA = Number(a.version ?? 0);
        const versionB = Number(b.version ?? 0);
        if (versionB !== versionA) return versionB - versionA;

        const dateA = new Date(a.tanggal ?? 0);
        const dateB = new Date(b.tanggal ?? 0);
        return dateB - dateA;
    });
});

const selectedStatusLabel = computed(() => statusLabelFromOptions(form.status, statusOptions));

const projectStatusHistoryDrafts = reactive({});

const formatDateOnly = (rawValue) => {
    if (!rawValue) return '-';
    const date = new Date(rawValue);
    if (Number.isNaN(date.getTime())) return String(rawValue);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatDateTime = (rawValue) => {
    if (!rawValue) return '-';
    const date = new Date(rawValue);
    if (Number.isNaN(date.getTime())) return String(rawValue);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const historyVersion = (entry) => entry?.version ?? '-';
const historyTanggal = (entry) => entry?.tanggal ?? null;
const historyCharter = (entry) => entry?.project_charter ?? entry?.projectCharter ?? null;
const historyCharterId = (entry) => historyCharter(entry)?.id ?? null;
const historyCharterLabel = (entry) => {
    const charter = historyCharter(entry);
    return charter ? (charter.version_label || formatDateOnly(charter.tgl_dokumen)) : '-';
};

const getProjectStatusHistoryDraft = (entry) => {
    if (!projectStatusHistoryDrafts[entry.id]) {
        projectStatusHistoryDrafts[entry.id] = {
            tanggal: entry.tanggal ?? '',
            notes: entry.notes ?? '',
            processing: false,
            error: '',
            editing: false,
        };
    }
    return projectStatusHistoryDrafts[entry.id];
};

const toggleEditing = (entry) => {
    getProjectStatusHistoryDraft(entry).editing = true;
};

const cancelEditing = (entry) => {
    const draft = getProjectStatusHistoryDraft(entry);
    draft.editing = false;
    draft.tanggal = entry.tanggal ?? '';
    draft.notes = entry.notes ?? '';
};

const submitProject = () => {
    form.put(route('itsp.digital-initiatives.update', props.initiative.id), {
        preserveScroll: true,
    });
};

const updateProjectStatusHistory = (id) => {
    const draft = projectStatusHistoryDrafts[id];
    draft.processing = true;
    router.put(route('itsp.digital-initiatives.project-status-history.update', [props.initiative.id, id]), {
        tanggal: draft.tanggal,
        notes: draft.notes,
    }, {
        preserveScroll: true,
        onSuccess: () => { draft.editing = false; },
        onFinish: () => { draft.processing = false; },
    });
};

const confirmDeleteHistory = (id) => {
    if (confirm('Are you sure you want to delete this status history?')) {
        router.delete(route('itsp.digital-initiatives.project-status-history.destroy', [props.initiative.id, id]), {
            preserveScroll: true,
        });
    }
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
