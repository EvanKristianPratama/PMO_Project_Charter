<template>
    <UserLayout title="Roadmap">
        <div class="space-y-6">
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
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Digital Initiative Roadmap {{ timelineStartYear }}-{{ timelineEndYear }}</h1>
                    </div>

                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end">
                        <div class="w-full sm:w-72">
                            <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                Filter Initiative
                            </label>
                            <select
                                v-model="selectedInitiativeId"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            >
                                <option value="all">Semua Initiative</option>
                                <option
                                    v-for="option in normalizedInitiativeOptions"
                                    :key="`filter-initiative-${option.id}`"
                                    :value="String(option.id)"
                                >
                                    {{ initiativeLabel(option) }}
                                </option>
                            </select>
                        </div>

                        <div class="w-full sm:w-56">
                            <label class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                                Filter Organization
                            </label>
                            <select
                                v-model="selectedOrganization"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                            >
                                <option value="all">Semua Organization</option>
                                <option
                                    v-for="organization in organizationOptions"
                                    :key="`filter-organization-${organization}`"
                                    :value="organization"
                                >
                                    {{ organization }}
                                </option>
                            </select>
                        </div>

                        <Link
                            :href="route('program-planning.program-definition.digital-initiatives.roadmap.edit')"
                            class="inline-flex items-center justify-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                        >
                            Buka Proses Edit
                        </Link>
                        <Link
                            :href="route('program-planning.program-definition.digital-initiatives.roadmap.create')"
                            class="inline-flex items-center justify-center rounded-lg bg-[#0B2A8A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                        >
                            Add Milestone
                        </Link>
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

            <div
                v-if="usingDummyData"
                class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300"
            >
                Data roadmap saat ini masih menggunakan dummy data.
            </div>

            <section v-if="filteredMilestones.length > 0" >
                <DigitalRoadmapComponent
                    :data="filteredMilestones"
                    :start-year-range="timelineStartYear"
                    :end-year-range="timelineEndYear"
                />
            </section>

            <section
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Belum ada data milestone untuk ditampilkan atau filter belum cocok.
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DigitalRoadmapComponent from '@/Components/Roadmap/Digital/DigitalRoadmapComponent.vue';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    milestones: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    startYearRange: { type: Number, default: 2024 },
    endYearRange: { type: Number, default: 2029 },
    usingDummyData: { type: Boolean, default: false },
});

const route = useRouteHelper();
const page = usePage();

const flash = computed(() => page.props.flash ?? {});

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

const organizationOptions = computed(() =>
    (() => {
        const masterOrganizations = (Array.isArray(props.organizationOptions) ? props.organizationOptions : [])
            .map((item) => normalizeText(item?.name))
            .filter(Boolean);

        if (masterOrganizations.length > 0) {
            return [...new Set(masterOrganizations)]
                .sort((left, right) => left.localeCompare(right, undefined, { numeric: true, sensitivity: 'base' }));
        }

        return [...new Set(
            milestoneItems.value
                .map((item) => normalizeText(item.organization_name))
                .filter(Boolean),
        )].sort((left, right) => left.localeCompare(right, undefined, { numeric: true, sensitivity: 'base' }));
    })(),
);

const selectedInitiativeId = ref('all');
const selectedOrganization = ref('all');

const filteredMilestones = computed(() => milestoneItems.value.filter((item) => {
    if (selectedInitiativeId.value !== 'all' && String(item.initiative_id) !== selectedInitiativeId.value) {
        return false;
    }

    if (selectedOrganization.value !== 'all' && item.organization_name !== selectedOrganization.value) {
        return false;
    }

    return true;
}));

const timelineStartYear = computed(() => {
    const years = filteredMilestones.value.map((item) => item.startYear).filter((year) => year > 0);
    return years.length > 0 ? Math.min(toNumber(props.startYearRange, 2024), ...years) : toNumber(props.startYearRange, 2024);
});

const timelineEndYear = computed(() => {
    const years = filteredMilestones.value.map((item) => item.endYear).filter((year) => year > 0);
    return years.length > 0 ? Math.max(toNumber(props.endYearRange, 2029), ...years) : toNumber(props.endYearRange, 2029);
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

</script>
