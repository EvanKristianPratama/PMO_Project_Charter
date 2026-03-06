<template>
    <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Roadmap Activity & Quarter</h2>
            <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                Versi aktif: {{ displayVersionLabel }}
            </p>
        </div>

        <form v-if="project" class="grid grid-cols-1 gap-3 lg:grid-cols-12" @submit.prevent="submitForm">
            <div class="lg:col-span-4">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Activity</label>
                <input
                    v-model="milestoneForm.title"
                    type="text"
                    placeholder="Contoh: Rollout pilot"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                />
                <p v-if="milestoneForm.errors.title" class="mt-1 text-xs text-red-500">{{ milestoneForm.errors.title }}</p>
            </div>

            <div class="lg:col-span-3">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Section</label>
                <input
                    v-model="milestoneForm.type"
                    type="text"
                    placeholder="Contoh: Assessment and Planning"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                />
                <p v-if="milestoneForm.errors.type" class="mt-1 text-xs text-red-500">{{ milestoneForm.errors.type }}</p>
            </div>

            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Tipe Timeline</label>
                <select
                    v-model.number="milestoneForm.milestone_type"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                >
                    <option
                        v-for="option in milestoneTypeOptionsDisplay"
                        :key="`milestone-type-${option.value}`"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
                <p v-if="milestoneForm.errors.milestone_type" class="mt-1 text-xs text-red-500">{{ milestoneForm.errors.milestone_type }}</p>
            </div>

            <div class="lg:col-span-12">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                    <input
                        v-model="useQuarterRange"
                        type="checkbox"
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-white/20"
                    />
                    Set quarter range (optional)
                </label>
            </div>

            <template v-if="useQuarterRange">
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Year</label>
                    <select
                        v-model="milestoneRange.startYear"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="">Select year</option>
                        <option v-for="year in yearOptions" :key="`start-${year}`" :value="String(year)">{{ year }}</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Q</label>
                    <select
                        v-model="milestoneRange.startQuarter"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="">Q</option>
                        <option v-for="quarter in quarterOptions" :key="`start-q-${quarter}`" :value="String(quarter)">Q{{ quarter }}</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Year</label>
                    <select
                        v-model="milestoneRange.endYear"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="">Select year</option>
                        <option v-for="year in yearOptions" :key="`end-${year}`" :value="String(year)">{{ year }}</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Q</label>
                    <select
                        v-model="milestoneRange.endQuarter"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="">Q</option>
                        <option v-for="quarter in quarterOptions" :key="`end-q-${quarter}`" :value="String(quarter)">Q{{ quarter }}</option>
                    </select>
                </div>
            </template>

            <div class="lg:col-span-12">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Output</label>
                <textarea
                    v-model="milestoneForm.output"
                    rows="3"
                    placeholder="Contoh: Compliance report confirming adherence..."
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                ></textarea>
                <p v-if="milestoneForm.errors.output" class="mt-1 text-xs text-red-500">{{ milestoneForm.errors.output }}</p>
            </div>

            <div class="lg:col-span-12 flex items-center justify-end gap-2">
                <button
                    v-if="editingMilestoneId"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-transparent dark:text-slate-300 dark:hover:bg-white/5"
                    @click="cancelEdit"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="milestoneForm.processing"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition disabled:opacity-70',
                        editingMilestoneId
                            ? 'bg-indigo-600 hover:bg-indigo-700'
                            : 'bg-teal-600 hover:bg-teal-700',
                    ]"
                >
                    <svg v-if="milestoneForm.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4Zm2 5.29A7.95 7.95 0 0 1 4 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65Z"></path>
                    </svg>
                    {{ editingMilestoneId ? 'Update Activity' : 'Add Activity' }}
                </button>
            </div>
        </form>

        <div v-else class="rounded-lg border border-dashed border-slate-300 px-3 py-4 text-sm text-slate-500 dark:border-white/15 dark:text-slate-400">
            Pilih project dulu untuk menambahkan activity roadmap.
        </div>

        <p v-if="milestoneForm.errors.start_date" class="mt-2 text-xs text-red-500">{{ milestoneForm.errors.start_date }}</p>
        <p v-if="milestoneForm.errors.end_date" class="mt-1 text-xs text-red-500">{{ milestoneForm.errors.end_date }}</p>

        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 dark:border-white/10">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-[#101826]">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Activity</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Section</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Tipe Timeline</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Start</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">End</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Output</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-600 dark:text-slate-300">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-for="item in roadmapMilestones" :key="item.id" :class="{ 'bg-indigo-50/50 dark:bg-indigo-500/5': editingMilestoneId === item.id }">
                        <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ item.title }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ item.type || '-' }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ milestoneTypeLabel(item.milestone_type ?? item.type) }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ quarterLabel(item.start_date) }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ quarterLabel(item.end_date) }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ item.output || '-' }}</td>
                        <td class="px-3 py-2 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button
                                    type="button"
                                    class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10"
                                    @click="startEdit(item)"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                    @click="removeMilestone(item.id)"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="roadmapMilestones.length === 0">
                        <td colspan="7" class="px-3 py-4 text-center text-slate-500 dark:text-slate-400">
                            Belum ada activity roadmap. Tambahkan activity pertama untuk memplot Q1-Q4.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    selectedRoadmapVersionId: {
        type: [Number, String],
        default: null,
    },
    milestoneTypeOptions: {
        type: Array,
        default: () => [],
    },
});

const quarterOptions = [1, 2, 3, 4];
const currentYear = new Date().getFullYear();
const defaultMilestoneType = 1;
const dashedMilestoneTypes = new Set([2, 4]);

const milestoneTypeOptionsDisplay = computed(() => {
    return [
        { value: 1, label: 'Blok', timeline_style: 'block' },
        { value: 2, label: 'Garis', timeline_style: 'dashed' },
    ];
});

const editingMilestoneId = ref(null);

const milestoneForm = useForm({
    title: '',
    output: '',
    type: '',
    milestone_type: defaultMilestoneType,
    start_date: '',
    end_date: '',
});

const useQuarterRange = ref(false);

const milestoneRange = ref({
    startYear: '',
    startQuarter: '',
    endYear: '',
    endQuarter: '',
});

const parseDate = (value) => {
    if (!value) {
        return null;
    }

    const matched = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);

    if (matched) {
        return new Date(Date.UTC(
            Number(matched[1]),
            Number(matched[2]) - 1,
            Number(matched[3])
        ));
    }

    const parsed = new Date(value);

    return Number.isNaN(parsed.getTime()) ? null : parsed;
};

const yearOptions = computed(() => {
    const years = new Set();

    for (let year = currentYear - 1; year <= currentYear + 6; year += 1) {
        years.add(year);
    }

    const durationMatch = String(props.project?.charter?.duration || '').match(/(20\d{2}).*?(20\d{2})/);

    if (durationMatch) {
        const durationStart = Number(durationMatch[1]);
        const durationEnd = Number(durationMatch[2]);

        if (Number.isFinite(durationStart) && Number.isFinite(durationEnd) && durationEnd >= durationStart) {
            for (let year = durationStart; year <= durationEnd; year += 1) {
                years.add(year);
            }
        }
    }

    for (const item of props.project?.milestones || []) {
        const startDate = parseDate(item.start_date);
        const endDate = parseDate(item.end_date);

        if (startDate) {
            years.add(startDate.getUTCFullYear());
        }

        if (endDate) {
            years.add(endDate.getUTCFullYear());
        }
    }

    return Array.from(years).sort((a, b) => a - b);
});

const resetMilestoneRange = () => {
    const baseYear = String(yearOptions.value[0] ?? currentYear);

    milestoneRange.value = {
        startYear: baseYear,
        startQuarter: '',
        endYear: baseYear,
        endQuarter: '',
    };
};

const resetForm = () => {
    milestoneForm.reset();
    milestoneForm.clearErrors();
    milestoneForm.type = '';
    milestoneForm.milestone_type = defaultMilestoneType;
    editingMilestoneId.value = null;
    useQuarterRange.value = false;
    resetMilestoneRange();
};

const normalizeVersionLabel = (value) => {
    const raw = String(value ?? '').trim().toLowerCase();

    if (!raw || raw === 'v') {
        return 'v1';
    }

    if (/^v\d+$/.test(raw)) {
        return `v${Math.max(Number(raw.slice(1)) || 1, 1)}`;
    }

    if (/^\d+$/.test(raw)) {
        return `v${Math.max(Number(raw) || 1, 1)}`;
    }

    return raw;
};

const selectedRoadmapVersionLabel = computed(() => {
    if (props.selectedRoadmapVersionId === null || props.selectedRoadmapVersionId === undefined || String(props.selectedRoadmapVersionId).trim() === '') {
        return null;
    }

    return normalizeVersionLabel(props.selectedRoadmapVersionId);
});

const displayVersionLabel = computed(() => {
    if (selectedRoadmapVersionLabel.value) {
        return selectedRoadmapVersionLabel.value;
    }

    const fallback = normalizeVersionLabel(props.project?.charter?.version_label ?? props.project?.version_label ?? '');
    return fallback || 'v1';
});

watch(
    () => [props.project?.id, props.selectedRoadmapVersionId],
    () => resetForm(),
    { immediate: true }
);

const roadmapMilestones = computed(() => {
    let milestones = [...(props.project?.milestones || [])];
    const selectedVersionId = selectedRoadmapVersionLabel.value;

    if (selectedVersionId) {
        milestones = milestones.filter((item) => normalizeVersionLabel(item.version) === selectedVersionId);
    }

    return milestones.sort((left, right) => {
        const leftOrder = Number(left.order || 0);
        const rightOrder = Number(right.order || 0);

        if (leftOrder !== rightOrder) {
            return leftOrder - rightOrder;
        }

        return String(left.start_date || '').localeCompare(String(right.start_date || ''));
    });
});

const normalizeMilestoneType = (value) => {
    const normalized = Number(value);
    return dashedMilestoneTypes.has(normalized) ? 2 : defaultMilestoneType;
};

const milestoneTypeLabel = (value) => {
    const normalized = normalizeMilestoneType(value);
    const found = milestoneTypeOptionsDisplay.value.find((option) => option.value === normalized);

    return found ? found.label : 'Blok';
};

const quarterLabel = (value) => {
    const date = parseDate(value);

    if (!date) {
        return '-';
    }

    const quarter = Math.floor(date.getUTCMonth() / 3) + 1;

    return `Q${quarter} ${date.getUTCFullYear()}`;
};

const quarterStartDate = (year, quarter) => {
    const month = (quarter * 3) - 2;

    return `${year}-${String(month).padStart(2, '0')}-01`;
};

const quarterEndDate = (year, quarter) => {
    const month = quarter * 3;
    const day = new Date(year, month, 0).getDate();

    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
};

const extractQuarterFromDate = (value) => {
    const date = parseDate(value);

    if (!date) {
        return { year: '', quarter: '' };
    }

    return {
        year: String(date.getUTCFullYear()),
        quarter: String(Math.floor(date.getUTCMonth() / 3) + 1),
    };
};

const startEdit = (item) => {
    editingMilestoneId.value = item.id;
    milestoneForm.clearErrors();

    milestoneForm.title = item.title || '';
    milestoneForm.type = item.type || '';
    milestoneForm.milestone_type = normalizeMilestoneType(item.milestone_type ?? item.type);
    milestoneForm.output = item.output || '';
    milestoneForm.start_date = item.start_date || '';
    milestoneForm.end_date = item.end_date || '';

    if (item.start_date && item.end_date) {
        useQuarterRange.value = true;
        const start = extractQuarterFromDate(item.start_date);
        const end = extractQuarterFromDate(item.end_date);

        milestoneRange.value = {
            startYear: start.year,
            startQuarter: start.quarter,
            endYear: end.year,
            endQuarter: end.quarter,
        };
    } else {
        useQuarterRange.value = false;
        resetMilestoneRange();
    }
};

const cancelEdit = () => {
    resetForm();
};

const buildDates = () => {
    if (useQuarterRange.value) {
        const startYear = Number(milestoneRange.value.startYear);
        const startQuarter = Number(milestoneRange.value.startQuarter);
        const endYear = Number(milestoneRange.value.endYear);
        const endQuarter = Number(milestoneRange.value.endQuarter);

        if (!startYear || !startQuarter || !endYear || !endQuarter) {
            milestoneForm.setError('start_date', 'Pilih start year/quarter dan end year/quarter atau matikan opsi quarter range.');
            return false;
        }

        milestoneForm.start_date = quarterStartDate(startYear, startQuarter);
        milestoneForm.end_date = quarterEndDate(endYear, endQuarter);

        if (new Date(milestoneForm.end_date).getTime() < new Date(milestoneForm.start_date).getTime()) {
            milestoneForm.setError('end_date', 'End quarter harus lebih besar atau sama dengan start quarter.');
            return false;
        }
    } else {
        milestoneForm.start_date = null;
        milestoneForm.end_date = null;
    }

    return true;
};

const submitForm = () => {
    if (!props.project?.id) {
        return;
    }

    milestoneForm.clearErrors();
    milestoneForm.milestone_type = normalizeMilestoneType(milestoneForm.milestone_type);

    if (!buildDates()) {
        return;
    }

    if (editingMilestoneId.value) {
        milestoneForm.put(`/it-initiatives/${props.project.id}/milestones/${editingMilestoneId.value}`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => resetForm(),
        });
    } else {
        milestoneForm.post(`/it-initiatives/${props.project.id}/milestones`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => resetForm(),
        });
    }
};

const removeMilestone = (milestoneId) => {
    if (!props.project?.id) {
        return;
    }

    if (!window.confirm('Delete this roadmap activity?')) {
        return;
    }

    if (editingMilestoneId.value === milestoneId) {
        resetForm();
    }

    router.delete(`/it-initiatives/${props.project.id}/milestones/${milestoneId}`, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
