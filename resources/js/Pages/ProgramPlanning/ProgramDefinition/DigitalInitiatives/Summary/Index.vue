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

                    <label for="initiative-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih
                        Initiative</label>
                    <select id="initiative-nav" v-model="selectedInitiativeId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                        <option value="" disabled>-- Pilih Initiative --</option>
                        <option v-for="option in (initiativeOptions ?? [])" :key="`initiative-opt-${option.id}`"
                            :value="String(option.id)">
                            {{ formatInitiativeLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5">
                        <button v-for="tab in ['Planning', 'Implementation', 'Evaluation']" :key="tab"
                            @click="activeTab = tab" class="rounded-md px-3 py-1 text-xs font-semibold transition-all"
                            :class="activeTab === tab ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'">
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
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Planning</h2>
                    </div>

                    <!-- Status History Table -->
                    <div
                        class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                        <div
                            class="bg-slate-50 px-4 py-2 border-b border-slate-200 dark:bg-white/5 dark:border-white/10">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                Status History</h3>
                        </div>
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead
                                class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400 border-b border-slate-200 dark:border-white/10">
                                <tr>
                                    <th class="px-4 py-2 w-40">Tanggal</th>
                                    <th class="px-4 py-2 w-48">Status</th>
                                    <th class="px-4 py-2">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                <tr v-for="status in (initiativeMaster.status_history ?? [])" :key="status.id"
                                    class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ formatDate(status.tanggal) }}</td>
                                    <td class="px-4 py-2">
                                        <span
                                            :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-tight capitalize shadow-sm transition-all', getStatusClass(status.status)]">
                                            {{ status.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-slate-500">{{ status.notes || '-' }}</td>
                                </tr>
                                <tr v-if="!initiativeMaster.status_history?.length">
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-500 italic">No status
                                        history available.</td>
                                </tr>
                            </tbody>
                        </table>
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
                    <div
                        class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Roadmap Not Available
                        </h3>
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

                <!-- Compendium not available message -->
                <div v-else class="space-y-1 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-[#3b5e96]"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Compendium</h2>
                    </div>
                    <div
                        class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Compendium Not Available
                        </h3>
                    </div>
                </div>

                <!-- 4. Appendix Charter Document -->
                <div v-if="appendixData" class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
                    </div>
                    <AppendixCharterDocument :initiative="computedAppendixData" :editable="false"
                        :coe-options="coeOptions" :theme-options="themeOptions"
                        :organization-options="organizationOptions" />
                </div>

                <!-- Appendix not available message -->
                <div v-else class="space-y-1 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
                    </div>
                    <div
                        class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Appendix Not Available
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Implementation Tab Content -->
            <div v-if="activeTab === 'Implementation'" class="space-y-6">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Implementation</h2>
                </div>



                <!-- Digital Initiative Header -->
                <DigitalInitiativeHeader v-if="unifiedInitiative" :initiative="unifiedInitiative"
                    class="overflow-hidden" />

                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status Implementation</h2>
                </div>

                <div
                    class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Start</th>
                                <th class="px-4 py-3">End</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Current PIC</th>
                                <th class="px-4 py-3">Review Status</th>
                                <th class="px-4 py-3">Status Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-for="impl in statusImplementations" :key="impl.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">{{ impl.start || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.end || '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ impl.year || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.pic || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
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

                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
                </div>

                <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear"
                    :end-year="roadmapEndYear" />
            </div>

            <!-- Evaluation Tab Content -->
            <div v-if="activeTab === 'Evaluation'" class="space-y-6">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Evaluation</h2>
                </div>
                <!-- Digital Initiative Evaluation Details -->
                <DigitalInitiativeEvaluation v-if="unifiedInitiative" :initiative="unifiedInitiative"
                    class="overflow-hidden" />

                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status Implementation's Review</h2>
                </div>

                <div v-if="roadmapDuration || (computedAppendixData && computedAppendixData.urgency_expected !== '-')"
                    class="flex justify-start">
                    <div class="score-panel w-full max-w-4xl overflow-hidden border border-[#3b82f6] flex flex-col">
                        <div v-if="roadmapDuration" 
                            class="flex border-b border-[#3b82f6] cursor-pointer hover:bg-slate-50 group"
                            @click="isRoadmapExpanded = !isRoadmapExpanded"
                        >
                            <div class="flex flex-1 border-r border-[#3b82f6]">
                                <div
                                    class="bar-sub-mini flex items-center shrink-0 min-w-[130px] justify-center border-r border-[#3b82f6] group-hover:bg-[#255b8a]">
                                    Project Duration</div>
                                <div class="panel-body-mini flex items-center flex-1 justify-start px-4 text-left">
                                    <div class="flex items-center justify-between w-full">
                                        <span>{{ roadmapDuration }}</span>
                                        <svg class="h-4 w-4 text-[#3b82f6] transition-transform duration-200" 
                                            :class="{ 'rotate-180': isRoadmapExpanded }"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-1">
                                <div class="panel-body-mini flex-1 p-0 overflow-hidden relative min-h-[32px]">
                                    <!-- Mini Roadmap Grid -->
                                    <div class="absolute inset-0 flex">
                                        <div v-for="year in roadmapYears" :key="year"
                                            class="flex-1 border-r border-slate-100 last:border-0 flex flex-col">
                                            <div
                                                class="h-[18px] bg-slate-50 flex items-center justify-center text-[10px] font-bold text-slate-500 border-b border-slate-100 leading-none uppercase tracking-wider">
                                                {{ year }}
                                            </div>
                                            <div class="flex-1"></div>
                                        </div>
                                    </div>
                                    <!-- Roadmap Bar -->
                                    <div class="absolute inset-x-0 bottom-2 flex items-center px-1">
                                        <div class="relative w-full h-1.5">
                                            <div class="absolute h-full bg-[#1e4f8f] rounded-sm shadow-sm"
                                                :style="roadmapBarStyle" :title="roadmapDuration"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expanded Roadmap Content -->
                        <div v-if="isRoadmapExpanded" class="bg-white border-b border-[#3b82f6] animate-fade-in overflow-hidden">
                            <DigitalRoadmapSummary 
                                :items="roadmapItems" 
                                :start-year="roadmapStartYear" 
                                :end-year="roadmapEndYear" 
                            />
                        </div>

                        <div v-if="computedAppendixData && computedAppendixData.urgency_expected !== '-'"
                            class="flex last:border-0">
                            <div
                                class="bar-sub-mini flex items-center shrink-0 min-w-[130px] justify-center border-r border-[#3b82f6]">
                                Expected Go Live</div>
                            <div class="panel-body-mini flex items-center flex-1 justify-start px-4 text-left">
                                {{ computedAppendixData.urgency_expected }}
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Start</th>
                                <th class="px-4 py-3">End</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Current PIC</th>
                                <th class="px-4 py-3">Review Status</th>
                                <th class="px-4 py-3">Status Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-for="impl in statusImplementations" :key="impl.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">{{ impl.start || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.end || '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ impl.year || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.pic || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
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
                
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-red-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Summary Review</h2>
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
import DigitalInitiativeHeader from '@/Components/DigitalInitiative/DigitalInitiativeHeader.vue';
import DigitalInitiativeEvaluation from '@/Components/DigitalInitiative/DigitalInitiativeEvaluation.vue';
import CompendiumCharterDocument from '@/Components/Compendium/CompendiumCharterDocument.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';
import DigitalRoadmapComponent from '@/Components/Roadmap/Digital/DigitalRoadmapComponent.vue';
import DigitalRoadmapSummary from '@/Components/Roadmap/Digital/DigitalRoadmapSummary.vue';

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

const isRoadmapExpanded = ref(false);

const goBack = () => {
    window.history.back();
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(date);
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

const unifiedInitiative = computed(() => ({
    ...props.initiativeMaster,
    appendix_data: props.appendixData,
    project_charter: props.projectCharter,
}));

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

const getStatusClass = (status) => {
    const s = String(status || '').toLowerCase();
    if (s.includes('draft')) return 'bg-slate-100 text-slate-600 ring-1 ring-slate-300';
    if (s.includes('propose')) return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
    if (s.includes('review')) return 'bg-amber-100 text-amber-700 ring-1 ring-amber-300';
    if (s.includes('baseline')) return 'bg-purple-100 text-purple-700 ring-1 ring-purple-300';
    if (s.includes('approve')) return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
    if (s.includes('postpone')) return 'bg-rose-100 text-rose-700 ring-1 ring-rose-300';
    return 'bg-slate-100 text-slate-500 ring-1 ring-slate-200';
};

const roadmapDuration = computed(() => {
    if (!props.roadmapItems || props.roadmapItems.length === 0) return null;

    const startYears = props.roadmapItems.map(item => Number(item.startYear)).filter(y => y > 0);
    const endYears = props.roadmapItems.map(item => Number(item.endYear)).filter(y => y > 0);

    if (startYears.length === 0 || endYears.length === 0) return null;

    const minStartYear = Math.min(...startYears);
    const maxEndYear = Math.max(...endYears);

    // Get Q for min start year
    const startQs = props.roadmapItems
        .filter(item => Number(item.startYear) === minStartYear)
        .map(item => {
            const m = String(item.startQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 1;
        });
    const minStartQ = Math.min(...startQs);

    // Get Q for max end year
    const endQs = props.roadmapItems
        .filter(item => Number(item.endYear) === maxEndYear)
        .map(item => {
            const m = String(item.endQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 4;
        });
    const maxEndQ = Math.max(...endQs);

    const years = maxEndYear - minStartYear + 1;
    const yearLabel = years > 1 ? `${years} Years` : `${years} Year`;

    if (minStartYear === maxEndYear) {
        return `${yearLabel} - (Q${minStartQ} - Q${maxEndQ} ${minStartYear})`;
    } else {
        return `${yearLabel} - (Q${minStartQ} ${minStartYear} - Q${maxEndQ} ${maxEndYear})`;
    }
});

const roadmapYears = computed(() => {
    const start = props.roadmapStartYear || 2024;
    const end = props.roadmapEndYear || 2029;
    const list = [];
    for (let y = start; y <= end; y++) {
        list.push(y);
    }
    return list;
});

const roadmapBarStyle = computed(() => {
    if (!props.roadmapItems || props.roadmapItems.length === 0) return { width: '0%', left: '0%' };

    const startYears = props.roadmapItems.map(item => Number(item.startYear)).filter(y => y > 0);
    const endYears = props.roadmapItems.map(item => Number(item.endYear)).filter(y => y > 0);

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;

    if (startYears.length === 0 || endYears.length === 0) return { width: '0%', left: '0%' };

    const minStartYear = Math.max(minGlobalYear, Math.min(...startYears));
    const maxEndYear = Math.min(maxGlobalYear, Math.max(...endYears));

    const startQs = props.roadmapItems
        .filter(item => Number(item.startYear) === minStartYear)
        .map(item => {
            const m = String(item.startQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 1;
        });
    const minStartQ = startQs.length ? Math.min(...startQs) : 1;

    const endQs = props.roadmapItems
        .filter(item => Number(item.endYear) === maxEndYear)
        .map(item => {
            const m = String(item.endQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 4;
        });
    const maxEndQ = endQs.length ? Math.max(...endQs) : 4;

    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;

    const startQuarterIndex = (minStartYear - minGlobalYear) * 4 + (minStartQ - 1);
    const endQuarterIndex = (maxEndYear - minGlobalYear) * 4 + (maxEndQ - 1);

    const left = (startQuarterIndex / totalQuarters) * 100;
    const width = ((endQuarterIndex - startQuarterIndex + 1) / totalQuarters) * 100;

    return {
        left: `${left}%`,
        width: `${width}%`
    };
});
</script>

<style scoped>
.score-panel {
    display: flex;
    background: #fff;
}

.bar-sub-mini {
    background: #2e6ea2;
    color: #fff;
    padding: 3px 12px;
    font-size: 11px;
    line-height: 1.2;
    text-align: center;
    min-height: 32px;
}

.panel-body-mini {
    padding: 3px 12px;
    background: #fff;
    min-height: 32px;
    display: flex;
    align-items: center;
    font-size: 12px;
    color: #0f172a;
}
</style>