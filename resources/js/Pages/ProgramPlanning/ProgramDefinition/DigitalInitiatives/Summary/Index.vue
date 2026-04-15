<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <!-- Navigation & Title Header -->
            <section
                class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <button @click="goBack"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </button>

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

                    <div class="ml-auto flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5">
                        <button
                            v-for="tab in ['Planning', 'Implementation', 'Evaluation']"
                            :key="tab"
                            @click="activeTab = tab"
                            class="rounded-md px-3 py-1 text-xs font-semibold transition-all"
                            :class="activeTab === tab ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                        >
                            {{ tab }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- Planning Tab Content -->
            <div v-if="activeTab === 'Planning'" class="space-y-6">
                <!-- 1. Digital Project Charter Document -->
                <div v-if="initiativeMaster" class="space-y-4">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-[#1e4f8f]"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Scope Charter</h2>
                    </div>
                    <DigitalInitiativeCharterDocument :initiative="initiativeMaster" />
                </div>

                <!-- 2. Digital Roadmap Component -->
                <div v-if="roadmapItems && roadmapItems.length"
                    class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
                    </div>
                    <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear"
                        :end-year="roadmapEndYear" />
                </div>

                <!-- Roadmap not available message -->
                <div v-else class="space-y-1 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
                    </div>
                    <div class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Roadmap Not Available</h3>
                    </div>
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

            <!-- Implementation Tab Content -->
            <div v-if="activeTab === 'Implementation'" class="space-y-6">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">StatusImplementation</h2>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Start</th>
                                <th class="px-4 py-3">End</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Review Status</th>
                                <th class="px-4 py-3">Status Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-for="impl in statusImplementations" :key="impl.id" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">{{ impl.start || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.end || '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ impl.year || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                        {{ impl.review_status || '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ impl.status_updated || '-' }}</td>
                            </tr>
                            <tr v-if="!statusImplementations || !statusImplementations.length">
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500 italic">
                                    Status Implementation Not Available
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Evaluation Tab Content -->
            <div v-if="activeTab === 'Evaluation'" class="space-y-6">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Evaluation</h2>
                </div>
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-12 text-center dark:border-white/10 dark:bg-white/5">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-white/5">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-sm font-bold text-slate-900 dark:text-white">Belum Ada Evaluasi</h3>
                    <p class="mt-2 text-xs text-slate-500">
                        Halaman evaluasi sedang dalam pengembangan.
                    </p>
                </div>
            </div>
            </div>
            </UserLayout>
            </template>
<script setup>
import { computed, ref } from 'vue';
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
    statusImplementations: { type: Array, default: () => [] },

    // Options
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
});

const activeTab = ref('Planning');
const route = useRouteHelper();

const goBack = () => {
    window.history.back();
};

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