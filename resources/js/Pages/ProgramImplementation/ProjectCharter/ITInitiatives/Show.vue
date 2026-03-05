<template>
    <UserLayout :title="`IT Initiative - ${itInitiative.name}`">
        <div class="space-y-4 print:space-y-0">
            <!-- Top Bar -->
            <section
                class="print:hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-2 px-3 py-2.5">
                    <Link href="/it-initiatives"
                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>

                    <span class="text-slate-300 dark:text-slate-600">|</span>

                    <select v-model="selectedProjectId"
                        class="max-w-xs rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100 lg:max-w-md">
                        <option v-for="option in projectSelectOptions" :key="`bar-${option.id}`"
                            :value="String(option.id)">
                            {{ projectOptionLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-1.5">
                        <button v-for="tab in tabs" :key="tab.key" type="button"
                            class="rounded-md px-2.5 py-1 text-[10px] font-semibold"
                            :class="activeTab === tab.key ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 dark:text-slate-400'"
                            @click="toggleTab(tab.key)">
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Detail panel (Status Implementation) -->
                <div v-if="activeTab === 'detail'" class="border-t border-slate-100 dark:border-white/5">
                    <div class="px-3 py-3">
                        <StatusImplementationTable :projects="relatedProjects" />
                    </div>
                </div>
            </section>

            <!-- Project Charter Section -->
            <template v-if="activeTab === 'charter'">
                <section
                    class="print:hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <div class="w-full lg:max-w-sm">
                            <label
                                class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Charter Version
                            </label>
                            <div class="flex items-center gap-2">
                                <select v-model="selectedCharterId" :disabled="charterVersions.length === 0 || isEditing"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-slate-100 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:disabled:bg-white/5">
                                    <option v-if="charterVersions.length === 0" value="">No saved version yet</option>
                                    <option v-for="charter in charterVersions" :key="charter.id"
                                        :value="String(charter.id)">
                                        {{ charterOptionLabel(charter) }}
                                    </option>
                                </select>
                                <button type="button"
                                    class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                                    :disabled="!resolvedRoadmapPcId"
                                    @click="goToRoadmap">
                                    View Roadmap
                                </button>
                                <button type="button"
                                    class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                                    :disabled="!resolvedRoadmapPcId"
                                    @click="goToRoadmap">
                                    Add Roadmap
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- New Version button: always visible when not editing -->
                            <button v-if="!isEditing" type="button"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-white/10 dark:text-slate-300"
                                @click="startNewVersion">
                                New Version
                            </button>

                            <!-- Edit Charter button: only when not already editing -->
                            <button v-if="!isEditing && charterVersions.length > 0" type="button"
                                class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white"
                                @click="startEdit">
                                Edit Charter
                            </button>

                            <!-- Editing controls -->
                            <template v-if="isEditing">
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    {{ isNewVersion ? 'New Version' : 'Edit Charter' }}
                                </span>
                                <button type="button"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-white/10 dark:text-slate-300"
                                    @click="cancelEdit">
                                    Cancel
                                </button>
                                <button type="button" :disabled="form.processing"
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-70"
                                    @click="saveCharter">
                                    <svg v-if="form.processing" class="h-3 w-3 animate-spin text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4Zm2 5.29A7.95 7.95 0 0 1 4 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65Z">
                                        </path>
                                    </svg>
                                    {{ isNewVersion ? 'Save as New Version' : 'Save Charter' }}
                                </button>
                            </template>

                            <button type="button"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-white/10 dark:text-slate-300"
                                @click="printCharter">
                                Print
                            </button>
                        </div>
                    </div>

                    <!-- Edit fields: only show when editing -->
                    <div v-if="isEditing" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Version Label -->
                        <div>
                            <label
                                class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Version
                                Label</label>
                            <input v-model="form.version_label" type="text"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                placeholder="e.g., v2 - reviewed" />
                            <p v-if="form.errors.version_label" class="mt-1 text-xs text-red-500">{{
                                form.errors.version_label }}</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label
                                class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</label>
                            <select v-model="form.status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                                <option value="">-- Pilih Status --</option>
                                <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</p>
                        </div>
                    </div>

                </section>

                <main class="print:m-0 print:p-0">
                    <CharterDocument :it-initiative="editableItInitiative" :form="form" :status-timeline="form.status"
                        :editable="isEditing" />
                </main>
            </template>

            <!-- Roadmap Section -->
            <section v-if="activeTab === 'roadmap'" class="print:hidden">
                <ProjectRoadmap :project="itInitiative"
                    :form="{ objectives: itInitiative.charter?.objectives ?? '', duration: itInitiative.charter?.duration ?? '' }"
                    :sequence="1" :year-start="2025" :year-end="2029" />
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CharterDocument from '@/Components/ProjectCharter/ItCharterDocument.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';
import StatusImplementationTable from '@/Components/ITInitiative/StatusImplementationTable.vue';

const props = defineProps({
    itInitiative: Object,
    relatedProjects: {
        type: Array,
        default: () => [],
    },
    projectOptions: {
        type: Array,
        default: () => [],
    },
});
const page = usePage();

// --- Status styling helper ---
const currentStatus = computed(() => {
    const val = Number(props.itInitiative?.status);
    switch (val) {
        case 1: return { label: 'Draft', class: 'bg-slate-100 text-slate-600 ring-1 ring-slate-300' };
        case 2: return { label: 'Propose', class: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300' };
        case 3: return { label: 'Review', class: 'bg-amber-100 text-amber-700 ring-1 ring-amber-300' };
        case 4: return { label: 'Approved', class: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300' };
        case 5: return { label: 'Baseline', class: 'bg-purple-100 text-purple-700 ring-1 ring-purple-300' };
        default: return null;
    }
});

// --- Status options for the charter status dropdown ---
const statusOptions = [
    { value: 1, label: 'Draft' },
    { value: 2, label: 'Propose' },
    { value: 3, label: 'Review' },
    { value: 4, label: 'Approved' },
    { value: 5, label: 'Baseline' },
];

// --- Tabs ---
const tabs = [
    { key: 'detail', label: 'Status Implementation' },
    { key: 'charter', label: 'Project Charter' },
    { key: 'roadmap', label: 'Roadmap' },
];
const parseTabFromUrl = () => {
    const query = String(page.url ?? '').split('?')[1] ?? '';
    const params = new URLSearchParams(query);
    const tab = String(params.get('tab') ?? '').trim().toLowerCase();

    if (['detail', 'scope', 'scope-charter', 'scope_charter'].includes(tab)) {
        return 'detail';
    }

    if (['charter', 'project-charter', 'project_charter'].includes(tab)) {
        return 'charter';
    }

    if (tab === 'roadmap') {
        return 'roadmap';
    }

    return 'charter';
};

const activeTab = ref(parseTabFromUrl());
const toggleTab = (key) => {
    activeTab.value = activeTab.value === key ? null : key;
};

// --- Project selector ---
const selectedProjectId = ref(String(props.itInitiative?.id ?? ''));

const projectSelectOptions = computed(() => {
    const source = Array.isArray(props.projectOptions) ? props.projectOptions : [];
    if (source.length > 0) return source;
    return [{
        id: Number(props.itInitiative?.id ?? 0),
        code: props.itInitiative?.code,
        name: props.itInitiative?.name,
        category: props.itInitiative?.charter?.category,
    }];
});

const projectOptionLabel = (option) => {
    const code = String(option?.code ?? '-').trim() || '-';
    const category = String(option?.category ?? '-').trim() || '-';
    const name = String(option?.name ?? '-').trim() || '-';
    return `${code} | ${category} | ${name}`;
};

watch(selectedProjectId, (nextValue, previousValue) => {
    if (!nextValue || nextValue === previousValue) return;
    const selectedId = Number(nextValue);
    if (!Number.isFinite(selectedId) || selectedId <= 0 || selectedId === Number(props.itInitiative?.id)) return;

    const payload = activeTab.value ? { tab: activeTab.value } : {};

    router.get(`/it-initiatives/${selectedId}`, payload, { preserveScroll: true });
});

watch(
    () => props.itInitiative?.id,
    (id) => { selectedProjectId.value = String(id ?? ''); }
);

// --- Charter ---
const CHARTER_FIELDS = [
    'category', 'duration', 'background', 'objectives', 'scope',
    'impact_value', 'key_personnel', 'key_items', 'budget',
    'risks_identified', 'risk_mitigation',
];

const mapCharterToForm = (charter = null, project = null) => {
    const payload = {
        version_label: charter?.version_label ?? '',
        owner: charter?.owner ?? '',
        status: charter?.status ?? '',
        tgl_dokumen: charter?.tgl_dokumen ?? '',
    };
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
const isEditing = ref(false);
// true = creating a new version, false = editing existing charter in-place
const isNewVersion = ref(false);
const form = useForm(mapCharterToForm(defaultCharter.value, props.itInitiative));

const selectedCharter = computed(() => {
    if (!selectedCharterId.value) return null;
    return charterVersions.value.find((c) => String(c.id) === String(selectedCharterId.value)) || null;
});

const resolvedRoadmapPcId = computed(() => {
    const selectedId = Number(selectedCharter.value?.id ?? 0);
    if (selectedId > 0) {
        return selectedId;
    }

    const fallbackId = Number(defaultCharter.value?.id ?? 0);
    return fallbackId > 0 ? fallbackId : null;
});

const goToRoadmap = () => {
    if (!resolvedRoadmapPcId.value) return;

    router.get('/roadmap/edit', {
        pc_id: resolvedRoadmapPcId.value,
    }, {
        preserveScroll: true,
    });
};

// A reactive proxy of itInitiative that reflects the in-form owner when editing
const editableItInitiative = computed(() => {
    if (!isEditing.value) return props.itInitiative;
    return {
        ...props.itInitiative,
        owner: form.owner ?? props.itInitiative?.charter?.owner ?? '',
    };
});

const setFormValues = (charter, project = null) => {
    const values = mapCharterToForm(charter, project ?? props.itInitiative);
    form.defaults(values);
    form.reset();
    form.clearErrors();
};

watch(() => selectedCharterId.value, () => {
    if (isEditing.value) return;
    setFormValues(selectedCharter.value ?? defaultCharter.value);
});

watch(() => charterVersions.value[0]?.id ?? null, (latestId) => {
    if (!latestId) return;
    const hasSelected = charterVersions.value.some((c) => String(c.id) === String(selectedCharterId.value));
    if (hasSelected) return;
    selectedCharterId.value = String(latestId);
    if (!isEditing.value) setFormValues(charterVersions.value[0]);
});

// Edit Charter: update the selected version in-place
const startEdit = () => {
    setFormValues(selectedCharter.value ?? defaultCharter.value, props.itInitiative);
    isNewVersion.value = false;
    isEditing.value = true;
};

// New Version: create a new charter record based on the selected version
const startNewVersion = () => {
    const base = selectedCharter.value ?? defaultCharter.value;
    const values = mapCharterToForm(base, props.itInitiative);
    values.version_label = '';
    form.defaults(values);
    form.reset();
    form.clearErrors();
    isNewVersion.value = true;
    isEditing.value = true;
};

const saveCharter = () => {
    if (isNewVersion.value) {
        // POST: create new charter version
        form.post(`/it-initiatives/${props.itInitiative.id}/charter`, {
            preserveScroll: true,
            onSuccess: () => {
                isEditing.value = false;
                isNewVersion.value = false;
            },
        });
    } else {
        // PUT: update the existing selected charter in-place
        const charterId = selectedCharterId.value;
        form.put(`/it-initiatives/${props.itInitiative.id}/charter/${charterId}`, {
            preserveScroll: true,
            onSuccess: () => {
                isEditing.value = false;
                isNewVersion.value = false;
            },
        });
    }
};

const cancelEdit = () => {
    isEditing.value = false;
    isNewVersion.value = false;
    setFormValues(selectedCharter.value ?? defaultCharter.value);
};

const printCharter = () => window.print();

const charterOptionLabel = (charter) => {
    return charter.resolved_version_label;
};
</script>
