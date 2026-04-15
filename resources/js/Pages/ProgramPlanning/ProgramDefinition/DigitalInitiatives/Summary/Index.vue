<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <!-- Navigation & Title Header -->
            <section
                class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <Link :href="route('program-planning.program-definition.digital-initiatives')"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>

                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10" />

                    <label for="initiative-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih Initiative</label>
                    <select
                        id="initiative-nav"
                        v-model="selectedInitiativeId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100"
                    >
                        <option value="" disabled>-- Pilih Initiative --</option>
                        <option v-for="option in (initiativeOptions ?? [])" :key="`initiative-opt-${option.id}`" :value="String(option.id)">
                            {{ formatInitiativeLabel(option) }}
                        </option>
                    </select>
                </div>
            </section>

            <!-- Error fallback if data doesn't exist -->
            <div v-if="!hasAnyData"
                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-12 text-center dark:border-white/10 dark:bg-white/5">
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 4h.01M6 20h12a2 2 0 0 0 2-2V8l-6-6H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-bold text-slate-900 dark:text-white">Kosong / Empty</h3>
                <p class="mt-2 text-xs text-slate-500">
                    Belum ada data Charter, Compendium, Appendix, maupun Roadmap untuk inisiatif ini.
                </p>
            </div>

            <!-- 1. Digital Roadmap Component -->
            <div v-if="roadmapItems && roadmapItems.length"
                class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap 2024-2029</h2>
                </div>
                <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear"
                    :end-year="roadmapEndYear" />
            </div>

            <!-- Roadmap not available message -->
            <div v-else class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap 2024-2029</h2>
                </div>
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-6 text-center dark:border-white/10 dark:bg-white/5">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900 dark:text-white">Belum Ada Roadmap</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Belum ada roadmap pada initiative terkait.
                    </p>
                </div>
            </div>

            <!-- 2. Digital Project Charter Document -->
            <div v-if="initiativeMaster" class="space-y-4">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-[#1e4f8f]"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Project Charter</h2>
                </div>
                <DigitalInitiativeCharterDocument :initiative="initiativeMaster" />
            </div>

            <!-- 3. Compendium Charter Document -->
            <div v-if="compendiumData" class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-[#3b5e96]"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Compendium</h2>
                </div>
                <CompendiumCharterDocument :form="compendiumData" :editable="false" :coe-options="coeOptions"
                    :source-options="sourceOptions" :theme-options="themeOptions" />
            </div>

            <!-- 4. Appendix Charter Document -->
            <div v-if="appendixData" class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
                </div>
                <AppendixCharterDocument :initiative="computedAppendixData" :editable="false" :coe-options="coeOptions"
                    :theme-options="themeOptions" :organization-options="organizationOptions" />
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

// Using DigitalInitiativeCharterDocument that displays the core project charter fields
import DigitalInitiativeCharterDocument from '@/Components/DigitalInitiative/DigitalInitiativeCharterDocument.vue';
import CompendiumCharterDocument from '@/Components/Compendium/CompendiumCharterDocument.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';
import DigitalRoadmapComponent from '@/Components/Roadmap/Digital/DigitalRoadmapComponent.vue';

const props = defineProps({
    initiativeMaster: { type: Object, default: () => ({}) },
    projectCharter: { type: Object, default: null },
    compendiumData: { type: Object, default: null },
    appendixData: { type: Object, default: null },
    roadmapItems: { type: Array, default: () => [] },
    roadmapStartYear: { type: Number, default: 2024 },
    roadmapEndYear: { type: Number, default: 2029 },

    // Options
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
});

const route = useRouteHelper();

const initiativeId = computed(() => Number(props.initiativeMaster?.id ?? 0));

const selectedInitiativeId = computed({
    get: () => (initiativeId.value > 0 ? String(initiativeId.value) : ''),
    set: (value) => {
        const selectedValue = String(value ?? '').trim();
        if (!selectedValue) return;
        if (initiativeId.value > 0 && selectedValue === String(initiativeId.value)) return;
        router.visit(route('program-planning.program-definition.digital-initiatives.summary.index', selectedValue));
    },
});

const formatInitiativeLabel = (option) => {
    const code = String(option?.code ?? '').replace(/#/g, '').trim();
    const name = String(option?.name ?? '').trim();
    if (code && name) return `[${code}] ${name}`;
    return name || code || `Initiative #${option?.id ?? '-'}`;
};

const pageTitle = computed(() => `Capsule Summary - ${props.initiativeMaster?.code}`);

const hasAnyData = computed(() => {
    return props.projectCharter || props.compendiumData || props.appendixData || (props.roadmapItems && props.roadmapItems.length > 0);
});

// We need to map `appendixData` props to the shape that `AppendixCharterDocument`'s `initiative` needs
// Just like in Compendium/Show.vue `appendixData` computed property.
const computedAppendixData = computed(() => {
    const a = props.appendixData;
    if (!a) return null;

    const getLabel = (val) => {
        if (val === 1) return 'High';
        if (val === 2) return 'Medium';
        if (val === 3) return 'Low';
        return '-';
    };

    let signBy = a?.sign_by ?? [];
    if (typeof signBy === 'string') {
        try { signBy = JSON.parse(signBy); } catch { signBy = signBy ? [signBy] : []; }
    }

    const themeMap = new Map((props.themeOptions ?? []).map(t => [Number(t.id), t]));
    const rjppThemes = (a?.rjpp_tagging_ids ?? []).map(id => themeMap.get(Number(id))).filter(Boolean);

    return {
        usecase: a?.usecase ?? '-',
        description: a?.description ?? '-',
        owner: a?.owner ?? '-',
        coe: a?.coe ?? '-',
        value_label: getLabel(a?.value),
        urgency_label: getLabel(a?.urgency),
        organization: a?.organization ?? '-',
        update_doc: a?.update_doc ?? '-',
        situation: a?.situation ?? '-',
        key_functionalities: a?.key_functionalities ?? '-',
        value_rationale: a?.value_rationale ?? '-',
        value_matrics: a?.value_matrics ?? '-',
        urgency_rationale: a?.urgency_rationale ?? '-',
        urgency_expected: a?.urgency_expected ?? '-',
        ease_label: getLabel(a?.ease),
        ease_rationale: a?.ease_rationale ?? '-',
        ease_detail: a?.ease_detail ?? '-',
        resource_label: getLabel(a?.resource),
        resource_rationale: a?.resource_rationale ?? '-',
        resource_detail: a?.resource_detail ?? '-',
        predecessor: a?.predecessor ?? '-',
        successor: a?.successor ?? '-',
        otherBU: a?.otherBU ?? '-',
        sign_by: signBy,
        rjppThemes,
    };
});
</script>