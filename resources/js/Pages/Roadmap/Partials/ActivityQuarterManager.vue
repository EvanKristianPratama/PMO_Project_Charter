<template>
    <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Roadmap Activity & Quarter</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Tambahkan activity dan rentang quarter (Q1-Q4) untuk project terpilih.
            </p>
        </div>

        <form v-if="project" class="grid grid-cols-1 gap-3 lg:grid-cols-12" @submit.prevent="addMilestone">
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

            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Section</label>
                <select
                    v-model="milestoneForm.type"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                >
                    <option v-for="option in milestoneTypeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
                <p v-if="milestoneForm.errors.type" class="mt-1 text-xs text-red-500">{{ milestoneForm.errors.type }}</p>
            </div>

            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Year</label>
                <select
                    v-model="milestoneRange.startYear"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                >
                    <option v-for="year in yearOptions" :key="`start-${year}`" :value="String(year)">{{ year }}</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Q</label>
                <select
                    v-model="milestoneRange.startQuarter"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                >
                    <option v-for="quarter in quarterOptions" :key="`start-q-${quarter}`" :value="String(quarter)">Q{{ quarter }}</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Year</label>
                <select
                    v-model="milestoneRange.endYear"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                >
                    <option v-for="year in yearOptions" :key="`end-${year}`" :value="String(year)">{{ year }}</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Q</label>
                <select
                    v-model="milestoneRange.endQuarter"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                >
                    <option v-for="quarter in quarterOptions" :key="`end-q-${quarter}`" :value="String(quarter)">Q{{ quarter }}</option>
                </select>
            </div>

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

            <div class="lg:col-span-12 flex items-center justify-end">
                <button
                    type="submit"
                    :disabled="milestoneForm.processing"
                    class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:opacity-70"
                >
                    <svg v-if="milestoneForm.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4Zm2 5.29A7.95 7.95 0 0 1 4 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65Z"></path>
                    </svg>
                    Add Activity
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
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Start</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">End</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600 dark:text-slate-300">Output</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-600 dark:text-slate-300">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-for="item in roadmapMilestones" :key="item.id">
                        <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ item.title }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ milestoneTypeLabel(item.type) }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ quarterLabel(item.start_date) }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ quarterLabel(item.end_date) }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ item.output || '-' }}</td>
                        <td class="px-3 py-2 text-right">
                            <button
                                type="button"
                                class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                @click="removeMilestone(item.id)"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="roadmapMilestones.length === 0">
                        <td colspan="6" class="px-3 py-4 text-center text-slate-500 dark:text-slate-400">
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
});

const quarterOptions = [1, 2, 3, 4];
const milestoneTypeOptions = [
    { value: 'assessment_design', label: 'Assessment & Design' },
    { value: 'rollout', label: 'Rollout' },
];
const currentYear = new Date().getFullYear();

const milestoneForm = useForm({
    title: '',
    output: '',
    type: 'assessment_design',
    start_date: '',
    end_date: '',
});

const milestoneRange = ref({
    startYear: String(currentYear),
    startQuarter: '1',
    endYear: String(currentYear),
    endQuarter: '1',
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
        startQuarter: '1',
        endYear: baseYear,
        endQuarter: '1',
    };
};

watch(
    () => props.project?.id,
    () => {
        milestoneForm.reset();
        milestoneForm.clearErrors();
        milestoneForm.type = 'assessment_design';
        resetMilestoneRange();
    },
    { immediate: true }
);

const roadmapMilestones = computed(() => {
    return [...(props.project?.milestones || [])].sort((left, right) => {
        const leftOrder = Number(left.order || 0);
        const rightOrder = Number(right.order || 0);

        if (leftOrder !== rightOrder) {
            return leftOrder - rightOrder;
        }

        return String(left.start_date || '').localeCompare(String(right.start_date || ''));
    });
});

const quarterLabel = (value) => {
    const date = parseDate(value);

    if (!date) {
        return '-';
    }

    const quarter = Math.floor(date.getUTCMonth() / 3) + 1;

    return `Q${quarter} ${date.getUTCFullYear()}`;
};

const milestoneTypeLabel = (type) => String(type || '').toLowerCase().includes('rollout')
    ? 'Rollout'
    : 'Assessment & Design';

const quarterStartDate = (year, quarter) => {
    const month = (quarter * 3) - 2;

    return `${year}-${String(month).padStart(2, '0')}-01`;
};

const quarterEndDate = (year, quarter) => {
    const month = quarter * 3;
    const day = new Date(year, month, 0).getDate();

    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
};

const addMilestone = () => {
    if (!props.project?.id) {
        return;
    }

    milestoneForm.clearErrors();

    const startYear = Number(milestoneRange.value.startYear);
    const startQuarter = Number(milestoneRange.value.startQuarter);
    const endYear = Number(milestoneRange.value.endYear);
    const endQuarter = Number(milestoneRange.value.endQuarter);

    milestoneForm.start_date = quarterStartDate(startYear, startQuarter);
    milestoneForm.end_date = quarterEndDate(endYear, endQuarter);

    if (new Date(milestoneForm.end_date).getTime() < new Date(milestoneForm.start_date).getTime()) {
        milestoneForm.setError('end_date', 'End quarter harus lebih besar atau sama dengan start quarter.');
        return;
    }

    milestoneForm.post(`/projects/${props.project.id}/milestones`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            milestoneForm.reset();
            milestoneForm.type = 'assessment_design';
            resetMilestoneRange();
        },
    });
};

const removeMilestone = (milestoneId) => {
    if (!props.project?.id) {
        return;
    }

    if (!window.confirm('Delete this roadmap activity?')) {
        return;
    }

    router.delete(`/projects/${props.project.id}/milestones/${milestoneId}`, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
