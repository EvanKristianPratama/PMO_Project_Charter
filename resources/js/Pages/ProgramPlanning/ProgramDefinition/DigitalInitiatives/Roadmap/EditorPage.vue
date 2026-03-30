<template>
    <UserLayout :title="pageTitle">
        <div class="space-y-5">
            <div class="flex w-fit flex-wrap items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.master.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Digital Initiatives List
                </Link>
                <div class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400">
                    Roadmap
                </div>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.compendium.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Compendium List
                </Link>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.appendix.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Appendix List
                </Link>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.mapping.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Mapping
                </Link>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <Link
                            :href="route('program-planning.program-definition.digital-initiatives.roadmap.index')"
                            class="mb-2 inline-flex text-sm font-medium text-[#0B2A8A] hover:underline dark:text-[#53BDE6]"
                        >
                            Kembali
                        </Link>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ heading }}</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ description }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Link
                            :href="route('program-planning.program-definition.digital-initiatives.roadmap.index')"
                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-transparent dark:text-slate-200 dark:hover:bg-white/5"
                        >
                            Lihat Roadmap
                        </Link>
                        <Link
                            v-if="isEditMode"
                            :href="route('program-planning.program-definition.digital-initiatives.roadmap.create')"
                            class="inline-flex items-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                        >
                            Tambah Milestone
                        </Link>
                        <Link
                            v-else
                            :href="route('program-planning.program-definition.digital-initiatives.roadmap.edit')"
                            class="inline-flex items-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                        >
                            Buka Proses Edit
                        </Link>
                    </div>
                </div>

                <div v-if="isEditMode" class="grid max-w-4xl gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                            Filter Initiative
                        </label>
                        <select
                            v-model="selectedInitiativeIdLocal"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            @change="handleEditInitiativeChange"
                        >
                            <option value="">-- Pilih Initiative --</option>
                            <option
                                v-for="option in initiativeOptionsWithMilestones"
                                :key="`edit-initiative-${option.id}`"
                                :value="String(option.id)"
                            >
                                {{ initiativeLabel(option) }}
                            </option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                            Pilih Milestone
                        </label>
                        <select
                            v-model="selectedMilestoneIdLocal"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            :disabled="editableMilestoneOptions.length === 0"
                            @change="handleEditMilestoneChange"
                        >
                            <option v-if="editableMilestoneOptions.length === 0" value="">-- Tidak ada milestone --</option>
                            <option
                                v-for="milestone in editableMilestoneOptions"
                                :key="`edit-milestone-${milestone.id}`"
                                :value="String(milestone.id)"
                            >
                                {{ milestoneOptionLabel(milestone) }}
                            </option>
                        </select>
                    </div>
                </div>
            </section>

            <div
                v-if="flash.success"
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300"
            >
                {{ flash.success }}
            </div>

            <div
                v-if="flash.error"
                class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300"
            >
                {{ flash.error }}
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ formTitle }}</h2>
                    <button
                        v-if="isEditMode && selectedMilestone"
                        type="button"
                        class="inline-flex items-center rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
                        @click="openDeleteModal"
                    >
                        Delete Milestone
                    </button>
                </div>

                <form class="space-y-5" @submit.prevent="submitForm">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                Initiative
                            </label>
                            <select
                                v-model="form.initiative_id"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            >
                                <option value="">Pilih initiative</option>
                                <option
                                    v-for="option in normalizedInitiativeOptions"
                                    :key="`form-initiative-${option.id}`"
                                    :value="String(option.id)"
                                >
                                    {{ initiativeLabel(option) }}
                                </option>
                            </select>
                            <p v-if="form.errors.initiative_id" class="mt-1 text-xs text-rose-500">{{ form.errors.initiative_id }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                Activity
                            </label>
                            <input
                                v-model="form.activity"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                                placeholder="Input activity milestone..."
                            >
                            <p v-if="form.errors.activity" class="mt-1 text-xs text-rose-500">{{ form.errors.activity }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Year</label>
                            <select v-model="form.startYear" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                                <option v-for="year in yearOptions" :key="`start-year-${year}`" :value="String(year)">{{ year }}</option>
                            </select>
                            <p v-if="form.errors.startYear" class="mt-1 text-xs text-rose-500">{{ form.errors.startYear }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Quarter</label>
                            <select v-model="form.startQ" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                                <option v-for="quarter in quarterOptions" :key="`start-quarter-${quarter}`" :value="quarter">{{ quarter }}</option>
                            </select>
                            <p v-if="form.errors.startQ" class="mt-1 text-xs text-rose-500">{{ form.errors.startQ }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Year</label>
                            <select v-model="form.endYear" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                                <option v-for="year in yearOptions" :key="`end-year-${year}`" :value="String(year)">{{ year }}</option>
                            </select>
                            <p v-if="form.errors.endYear" class="mt-1 text-xs text-rose-500">{{ form.errors.endYear }}</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Quarter</label>
                            <select v-model="form.endQ" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                                <option v-for="quarter in quarterOptions" :key="`end-quarter-${quarter}`" :value="quarter">{{ quarter }}</option>
                            </select>
                            <p v-if="form.errors.endQ" class="mt-1 text-xs text-rose-500">{{ form.errors.endQ }}</p>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4 dark:border-white/10">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="resetForm"
                        >
                            Reset
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing || (isEditMode && !selectedMilestone)"
                            class="rounded-lg bg-[#0B2A8A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#102f95] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Menyimpan...' : submitLabel }}
                        </button>
                    </div>
                </form>
            </section>

            <section v-if="previewMilestones.length > 0" class="space-y-5">
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Preview Roadmap</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Timeline mengikuti initiative yang sedang Anda kerjakan.</p>
                        </div>
                    </div>

                    <DigitalRoadmapComponent
                        :data="previewMilestones"
                        :start-year-range="previewStartYear"
                        :end-year-range="previewEndYear"
                    />
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Milestone Table</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                            <thead class="bg-slate-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">No</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Activity</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Year</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Start Q</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Year</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">End Q</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/10 dark:bg-[#171717]">
                                <tr
                                    v-for="(milestone, index) in previewMilestones"
                                    :key="`preview-row-${milestone.id}`"
                                    :class="[
                                        selectedMilestone && milestone.id === selectedMilestone.id ? 'bg-blue-50/70 dark:bg-blue-500/10' : 'hover:bg-slate-50 dark:hover:bg-white/5',
                                        isEditMode ? 'cursor-pointer' : '',
                                    ]"
                                    @click="selectMilestone(milestone)"
                                >
                                    <td class="px-4 py-3 text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ milestone.activity || '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ milestone.startYear || '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ milestone.startQ || '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ milestone.endYear || '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ milestone.endQ || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

            <section
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    <template v-if="isEditMode">Pilih milestone untuk mulai update roadmap.</template>
                    <template v-else>Isi form di atas untuk menambahkan milestone baru.</template>
                </p>
            </section>

            <ConfirmationModal
                :show="showDeleteModal"
                title="Hapus Master Milestone"
                :message="deleteMessage"
                confirm-text="Ya, Hapus"
                cancel-text="Batal"
                type="danger"
                :loading="deleteProcessing"
                @close="closeDeleteModal"
                @confirm="confirmDelete"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DigitalRoadmapComponent from '@/Components/Roadmap/Digital/DigitalRoadmapComponent.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    pageMode: { type: String, default: 'create' },
    milestones: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    startYearRange: { type: Number, default: 2024 },
    endYearRange: { type: Number, default: 2029 },
    selectedInitiativeId: { type: Number, default: null },
    selectedMilestoneId: { type: Number, default: null },
});

const route = useRouteHelper();
const page = usePage();

const isEditMode = computed(() => props.pageMode === 'edit');
const flash = computed(() => page.props.flash ?? {});
const currentYear = new Date().getFullYear();
const quarterOptions = ['Q1', 'Q2', 'Q3', 'Q4'];

const toNumber = (value, fallback = 0) => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : fallback;
};

const normalizeText = (value) => String(value ?? '').trim();

const normalizeQuarter = (value) => {
    const matched = normalizeText(value).toUpperCase().match(/^Q?([1-4])$/);
    return matched ? `Q${matched[1]}` : 'Q1';
};

const normalizedInitiativeOptions = computed(() =>
    (Array.isArray(props.initiativeOptions) ? props.initiativeOptions : []).map((option) => ({
        id: toNumber(option?.id, 0),
        code: normalizeText(option?.code),
        name: normalizeText(option?.name),
        organization_name: normalizeText(option?.organization_name) || '-',
    })),
);

const milestoneItems = computed(() =>
    (Array.isArray(props.milestones) ? props.milestones : []).map((item) => ({
        id: toNumber(item?.id, 0),
        initiative_id: toNumber(item?.initiative_id, 0),
        initiative_code: normalizeText(item?.initiative_code),
        initiative_name: normalizeText(item?.initiative_name),
        organization_name: normalizeText(item?.organization_name) || '-',
        activity: normalizeText(item?.activity),
        startYear: toNumber(item?.startYear, 0),
        startQ: normalizeQuarter(item?.startQ),
        endYear: toNumber(item?.endYear, 0),
        endQ: normalizeQuarter(item?.endQ),
    })),
);

const yearOptions = computed(() => {
    const start = Math.min(toNumber(props.startYearRange, 2024), currentYear - 1, 2024);
    const end = Math.max(toNumber(props.endYearRange, 2029), currentYear + 2, 2029);
    return Array.from({ length: (end - start) + 1 }, (_, index) => start + index);
});

const initiativeLabel = (item) => {
    const code = normalizeText(item?.code ?? item?.initiative_code);
    const name = normalizeText(item?.name ?? item?.initiative_name);
    const initiativeId = toNumber(item?.id ?? item?.initiative_id, 0);

    if (code && name) {
        return `[${code}] ${name}`;
    }

    if (name) {
        return name;
    }

    return initiativeId > 0 ? `Initiative #${initiativeId}` : '-';
};

const periodLabel = (year, quarter) => {
    const normalizedYear = toNumber(year, 0);
    return normalizedYear > 0 ? `${normalizedYear} - ${normalizeQuarter(quarter)}` : '-';
};

const milestoneOptionLabel = (milestone) => `${milestone.activity || '-'} | ${periodLabel(milestone.startYear, milestone.startQ)} -> ${periodLabel(milestone.endYear, milestone.endQ)}`;

const initiativeOptionsWithMilestones = computed(() => {
    const usedIds = new Set(milestoneItems.value.map((item) => item.initiative_id).filter((id) => id > 0));
    return normalizedInitiativeOptions.value.filter((option) => usedIds.has(option.id));
});

const selectedInitiativeIdLocal = ref(props.selectedInitiativeId ? String(props.selectedInitiativeId) : '');
const selectedMilestoneIdLocal = ref(props.selectedMilestoneId ? String(props.selectedMilestoneId) : '');

const editableMilestoneOptions = computed(() => {
    const initiativeId = normalizeText(selectedInitiativeIdLocal.value);
    return milestoneItems.value.filter((item) => initiativeId === '' || String(item.initiative_id) === initiativeId);
});

const selectedMilestone = computed(() =>
    milestoneItems.value.find((item) => item.id === toNumber(selectedMilestoneIdLocal.value, 0)) ?? null,
);

const resolveDefaultYear = () => {
    if (yearOptions.value.includes(currentYear)) {
        return currentYear;
    }

    return yearOptions.value[0] ?? currentYear;
};

const buildFormPayload = (milestone = null) => {
    const defaultYear = resolveDefaultYear();

    return {
        initiative_id: milestone
            ? String(milestone.initiative_id ?? '')
            : (props.selectedInitiativeId ? String(props.selectedInitiativeId) : ''),
        activity: milestone ? normalizeText(milestone.activity) : '',
        startYear: milestone ? String(toNumber(milestone.startYear, defaultYear)) : String(defaultYear),
        startQ: milestone ? normalizeQuarter(milestone.startQ) : 'Q1',
        endYear: milestone ? String(toNumber(milestone.endYear, defaultYear)) : String(defaultYear),
        endQ: milestone ? normalizeQuarter(milestone.endQ) : 'Q1',
    };
};

const form = useForm(buildFormPayload());
const showDeleteModal = ref(false);
const deleteProcessing = ref(false);

const pageTitle = computed(() => isEditMode.value ? 'Edit Roadmap' : 'Create Roadmap');
const heading = computed(() => isEditMode.value ? 'Roadmap Input & Edit' : 'Create Roadmap');
const description = computed(() => (
    isEditMode.value
        ? 'Pilih milestone yang ingin diperbarui lalu sesuaikan periodenya.'
        : 'Tambahkan master milestone baru dan cek preview roadmap di bawah.'
));
const formTitle = computed(() => isEditMode.value ? 'Edit Milestone Form' : 'Create Milestone Form');
const submitLabel = computed(() => isEditMode.value ? 'Simpan Perubahan' : 'Simpan Milestone');
const deleteMessage = computed(() => selectedMilestone.value
    ? `Milestone "${selectedMilestone.value.activity || '-'}" akan dihapus dari roadmap.`
    : 'Apakah Anda yakin ingin menghapus data ini?');

const syncForm = (milestone = null) => {
    form.defaults(buildFormPayload(milestone));
    form.reset();
    form.clearErrors();
};

watch(() => props.selectedInitiativeId, (value) => {
    selectedInitiativeIdLocal.value = value ? String(value) : '';
}, { immediate: true });

watch(() => props.selectedMilestoneId, (value) => {
    selectedMilestoneIdLocal.value = value ? String(value) : '';
}, { immediate: true });

watch(selectedMilestone, (milestone) => {
    if (isEditMode.value) {
        syncForm(milestone);
    }
}, { immediate: true });

const previewInitiativeId = computed(() => {
    if (isEditMode.value) {
        return normalizeText(selectedInitiativeIdLocal.value) || 'all';
    }

    return normalizeText(form.initiative_id) || 'all';
});

const previewMilestones = computed(() => milestoneItems.value.filter((item) => {
    if (previewInitiativeId.value !== 'all' && String(item.initiative_id) !== previewInitiativeId.value) {
        return false;
    }

    return true;
}));

const previewStartYear = computed(() => {
    const years = previewMilestones.value.map((item) => item.startYear).filter((year) => year > 0);
    return years.length > 0 ? Math.min(toNumber(props.startYearRange, 2024), ...years) : toNumber(props.startYearRange, 2024);
});

const previewEndYear = computed(() => {
    const years = previewMilestones.value.map((item) => item.endYear).filter((year) => year > 0);
    return years.length > 0 ? Math.max(toNumber(props.endYearRange, 2029), ...years) : toNumber(props.endYearRange, 2029);
});

const resetForm = () => {
    syncForm(isEditMode.value ? selectedMilestone.value : null);
};

const handleEditInitiativeChange = () => {
    const initiativeId = normalizeText(selectedInitiativeIdLocal.value);
    const firstMilestone = editableMilestoneOptions.value[0] ?? null;

    router.get(route('program-planning.program-definition.digital-initiatives.roadmap.edit'), {
        initiative_id: initiativeId || undefined,
        milestone_id: firstMilestone?.id ?? undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const handleEditMilestoneChange = () => {
    router.get(route('program-planning.program-definition.digital-initiatives.roadmap.edit'), {
        initiative_id: normalizeText(selectedInitiativeIdLocal.value) || undefined,
        milestone_id: toNumber(selectedMilestoneIdLocal.value, 0) || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const selectMilestone = (milestone) => {
    if (!isEditMode.value || !milestone?.id) {
        return;
    }

    router.get(route('program-planning.program-definition.digital-initiatives.roadmap.edit'), {
        initiative_id: milestone.initiative_id,
        milestone_id: milestone.id,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const submitForm = () => {
    form.transform((data) => ({
        initiative_id: toNumber(data.initiative_id, 0),
        activity: normalizeText(data.activity),
        startYear: toNumber(data.startYear, resolveDefaultYear()),
        startQ: normalizeQuarter(data.startQ),
        endYear: toNumber(data.endYear, resolveDefaultYear()),
        endQ: normalizeQuarter(data.endQ),
    }));

    if (isEditMode.value) {
        if (!selectedMilestone.value?.id) {
            return;
        }

        form.put(route('program-planning.program-definition.digital-initiatives.roadmap.update', selectedMilestone.value.id), {
            preserveScroll: true,
        });
        return;
    }

    form.post(route('program-planning.program-definition.digital-initiatives.roadmap.store'), {
        preserveScroll: true,
        onSuccess: () => {
            const currentInitiativeId = normalizeText(form.initiative_id);
            syncForm(null);
            form.initiative_id = currentInitiativeId;
        },
    });
};

const openDeleteModal = () => {
    if (!selectedMilestone.value) {
        return;
    }

    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    if (deleteProcessing.value) {
        return;
    }

    showDeleteModal.value = false;
};

const confirmDelete = () => {
    if (!selectedMilestone.value?.id) {
        return;
    }

    deleteProcessing.value = true;

    router.delete(route('program-planning.program-definition.digital-initiatives.roadmap.destroy', selectedMilestone.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteProcessing.value = false;
            showDeleteModal.value = false;
        },
    });
};
</script>
