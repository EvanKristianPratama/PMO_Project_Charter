<template>
    <UserLayout :title="`IT Initiative - ${itInitiative.name}`">
        <div class="space-y-4 print:space-y-0">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            href="/it-initiatives"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                            </svg>
                            Back
                        </Link>

                        <h1 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ itInitiative.name }}
                        </h1>

                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ itInitiative.code }}</span>
                        <StatusBadge :status="itInitiative.status" />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-if="!isCharterOpen"
                            type="button"
                            class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700"
                            @click="openCharter"
                        >
                            Open Project Charter
                        </button>

                        <button
                            v-else
                            type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="closeCharter"
                        >
                            Close Project Charter
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <span>
                        Roadmap dipisah di menu <strong>Roadmap</strong> dan otomatis mengambil data durasi/tanggal dari IT initiative yang sudah diinput.
                    </span>
                    <Link
                        :href="`/roadmap?project_id=${itInitiative.id}`"
                        class="inline-flex items-center rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200"
                    >
                        Buka Roadmap Module
                    </Link>
                </div>
            </section>

            <section
                v-if="isCharterOpen"
                class="print:hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div class="w-full lg:max-w-sm">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Charter Version
                        </label>
                        <select
                            v-model="selectedCharterId"
                            :disabled="charterVersions.length === 0 || isEditing"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:disabled:bg-white/5"
                        >
                            <option v-if="charterVersions.length === 0" value="">No saved version yet</option>
                            <option
                                v-for="charter in charterVersions"
                                :key="charter.id"
                                :value="String(charter.id)"
                            >
                                {{ charterOptionLabel(charter) }}
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="startNewVersion"
                        >
                            New Version
                        </button>

                        <button
                            v-if="!isEditing"
                            type="button"
                            class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700"
                            @click="startEdit"
                        >
                            Edit Charter
                        </button>

                        <template v-else>
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                                @click="cancelEdit"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-70"
                                @click="saveCharter"
                            >
                                <svg v-if="form.processing" class="h-3 w-3 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4Zm2 5.29A7.95 7.95 0 0 1 4 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65Z"></path>
                                </svg>
                                Save as Version
                            </button>
                        </template>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="printCharter"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 17h12v3H6v-3Zm-2-2h16a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2Z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>

                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Save akan membuat versi charter baru. Versi lama tetap tersimpan sebagai histori.
                </p>

                <div v-if="isEditing" class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Version Label
                    </label>
                    <input
                        v-model="form.version_label"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                        placeholder="e.g., v2 - reviewed"
                    />
                    <p v-if="form.errors.version_label" class="mt-1 text-xs text-red-500">{{ form.errors.version_label }}</p>
                </div>
            </section>

            <section
                v-if="!isCharterOpen"
                class="print:hidden rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Buka project charter untuk lihat atau edit dokumen.
                </p>
            </section>

            <main v-if="isCharterOpen" class="print:m-0 print:p-0">
                <CharterDocument
                    :it-initiative="itInitiative"
                    :form="form"
                    :editable="isEditing"
                />
            </main>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CharterDocument from './Partials/CharterDocument.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    itInitiative: Object,
});

const CHARTER_FIELDS = [
    'category',
    'duration',
    'background',
    'objectives',
    'scope',
    'impact_value',
    'key_personnel',
    'key_items',
    'budget',
    'risks_identified',
    'risk_mitigation',
];

const mapCharterToForm = (charter = null) => {
    const payload = { version_label: charter?.version_label ?? '' };

    for (const field of CHARTER_FIELDS) {
        payload[field] = charter?.[field] ?? '';
    }

    return payload;
};

const charterVersions = computed(() => {
    const items = Array.isArray(props.itInitiative?.charters) ? props.itInitiative.charters : [];

    return items.map((charter, index) => ({
        ...charter,
        resolved_version_label: String(charter?.version_label || '').trim() || `v${items.length - index}`,
    }));
});

const defaultCharter = computed(() => props.itInitiative?.charter ?? charterVersions.value[0] ?? null);
const selectedCharterId = ref(defaultCharter.value ? String(defaultCharter.value.id) : '');
const isCharterOpen = ref(false);
const isEditing = ref(false);

const form = useForm(mapCharterToForm(defaultCharter.value));

const selectedCharter = computed(() => {
    if (!selectedCharterId.value) {
        return null;
    }

    return charterVersions.value.find(
        (charter) => String(charter.id) === String(selectedCharterId.value)
    ) || null;
});

const setFormValues = (charter) => {
    const values = mapCharterToForm(charter);

    form.defaults(values);
    form.reset();
    form.clearErrors();
};

watch(
    () => selectedCharterId.value,
    () => {
        if (isEditing.value) {
            return;
        }

        setFormValues(selectedCharter.value ?? defaultCharter.value);
    }
);

watch(
    () => charterVersions.value[0]?.id ?? null,
    (latestId) => {
        if (!latestId) {
            return;
        }

        const hasSelectedVersion = charterVersions.value.some(
            (charter) => String(charter.id) === String(selectedCharterId.value)
        );

        if (hasSelectedVersion) {
            return;
        }

        selectedCharterId.value = String(latestId);

        if (!isEditing.value) {
            setFormValues(charterVersions.value[0]);
        }
    }
);

const openCharter = () => {
    isCharterOpen.value = true;

    if (!selectedCharterId.value && charterVersions.value[0]) {
        selectedCharterId.value = String(charterVersions.value[0].id);
    }

    if (!isEditing.value) {
        setFormValues(selectedCharter.value ?? defaultCharter.value);
    }
};

const closeCharter = () => {
    isCharterOpen.value = false;
    isEditing.value = false;
    setFormValues(selectedCharter.value ?? defaultCharter.value);
};

const startEdit = () => {
    setFormValues(selectedCharter.value ?? defaultCharter.value);
    isEditing.value = true;
};

const startNewVersion = () => {
    const base = selectedCharter.value ?? defaultCharter.value;
    const values = mapCharterToForm(base);

    values.version_label = '';
    form.defaults(values);
    form.reset();
    form.clearErrors();
    isEditing.value = true;
};

const saveCharter = () => {
    form.post(`/it-initiatives/${props.itInitiative.id}/charter`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
            selectedCharterId.value = '';
        },
    });
};

const cancelEdit = () => {
    isEditing.value = false;
    setFormValues(selectedCharter.value ?? defaultCharter.value);
};

const printCharter = () => {
    window.print();
};

const charterOptionLabel = (charter) => {
    const createdAt = charter?.created_at ? new Date(charter.created_at) : null;
    const createdLabel = createdAt && !Number.isNaN(createdAt.getTime())
        ? createdAt.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        })
        : '-';

    return `${charter.resolved_version_label} - ${createdLabel}`;
};
</script>
