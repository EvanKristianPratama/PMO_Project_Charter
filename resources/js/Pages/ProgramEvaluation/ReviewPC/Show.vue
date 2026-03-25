<template>
    <UserLayout title="Detail Review PC">
        <div class="animate-fade-in-up">
            <section
                class="mx-auto mb-3 w-full max-w-[1200px] rounded-2xl border border-slate-200 bg-white shadow-sm print:hidden dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-2 px-3 py-2.5">
                    <Link :href="route('program-evaluation.index')"
                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>
                    <span class="text-slate-300 dark:text-slate-600">|</span>
                    <label for="review" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih
                        Initiative</label>
                    <select v-if="reviewOptions.length" v-model="selectedReviewId"
                        class="max-w-xs rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                        <option v-for="option in reviewOptions" :key="`review-opt-${option.id}`"
                            :value="String(option.id)">
                            {{ formatReviewLabel(option) }}
                        </option>
                    </select>
                    <div class="ml-auto flex items-center gap-1.5">
                        <button type="button" class="rounded-md px-2.5 py-1 text-[10px] font-semibold"
                            :class="activeNav === 'project-charter' ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 dark:text-slate-400'"
                            @click="setActiveNav('project-charter')">
                            Project Charter
                        </button>
                        <button type="button" class="rounded-md px-2.5 py-1 text-[10px] font-semibold"
                            :class="activeNav === 'initiative-relation' ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 dark:text-slate-400'"
                            @click="setActiveNav('initiative-relation')">
                            Initiative Relation
                        </button>
                        <button type="button" class="rounded-md px-2.5 py-1 text-[10px] font-semibold"
                            :class="activeNav === 'roadmap' ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 dark:text-slate-400'"
                            @click="setActiveNav('roadmap')">
                            Roadmap
                        </button>
                        <button type="button" class="rounded-md px-2.5 py-1 text-[10px] font-semibold"
                            :class="activeNav === 'review' ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 dark:text-slate-400'"
                            @click="setActiveNav('review')">
                            Review
                        </button>
                    </div>
                    <Link v-if="activeNav === 'review'" :href="route('program-evaluation.index', { edit: trsReviewPC.id })"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                        Edit
                    </Link>
                    <button type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        @click="printPage">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 9V4h12v5M6 17h12v3H6v-3Zm-2-2h16a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2Z" />
                        </svg>
                        Print
                    </button>
                </div>
            </section>
            <div id="review"
                class="mx-auto w-full max-w-[1200px] space-y-0 border border-slate-300 bg-[#e9e9e9] p-0 text-slate-900 shadow-sm dark:border-white/20 dark:bg-[#d7d7d7]">
                <header class="flex flex-wrap items-center gap-3 px-4 pt-3 pb-3">
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ headerTitle }}
                    </h1>
                    <span v-if="currentStatus"
                        :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold capitalize transition', currentStatus.class]">
                        {{ currentStatus.label }}
                    </span>
                </header>

                <section v-if="activeNav === 'review'" id="review" class="space-y-0">
                    <div class="px-3 py-3">
                        <StatusImplementationTable :projects="mappedProjects" />
                        <div v-if="allRoadmapVersions.length > 0" class="mt-3 flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10">
                            <template v-for="(roadmapProject, idx) in allRoadmapVersions" :key="`rm-rev-${roadmapProject.roadmap_key}`">
                                <ProjectRoadmapSummary 
                                    :project="roadmapProject" 
                                    :sequence="idx + 1"
                                    :expanded="isRoadmapExpanded(roadmapProject.roadmap_key)"
                                    :display-version-label="roadmapProject.roadmap_version_label"
                                    @toggle="toggleRoadmapExpand(roadmapProject.roadmap_key)"
                                />
                                <div v-if="isRoadmapExpanded(roadmapProject.roadmap_key)" class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5">
                                    <ProjectRoadmap
                                        :project="roadmapProject"
                                        :form="{ objectives: roadmapProject.charter?.objectives ?? '', duration: roadmapProject.charter?.duration ?? '' }"
                                        :selected-roadmap-version-id="roadmapProject.roadmap_version_label"
                                        :sequence="idx + 1"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
                    <ReviewContent :review="review" :penjelasan-items="penjelasanItems" :why-items="whyItems"
                        :how-parsed="howParsed" :project-profile-items="projectProfileItems" />
                </section>

                <section v-if="activeNav === 'project-charter'" id="project-charter" class="space-y-0">
                    <!-- Filter Status Timeline -->
                    <div v-if="mappedProjects.length > 0"
                        class="flex flex-wrap items-center gap-2 px-3 py-2 bg-white border-b border-slate-100 dark:bg-[#171717] dark:border-white/10">
                        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Filter
                            Status:</span>
                        <button v-for="opt in statusTimelineFilterOptions" :key="opt.value" type="button"
                            @click="pcStatusFilter = opt.value" :class="[
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold capitalize transition',
                                pcStatusFilter === opt.value
                                    ? opt.activeClass
                                    : 'bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-white/10 dark:text-slate-400 dark:hover:bg-white/20'
                            ]">
                            {{ opt.label }}
                        </button>
                    </div>

                    <div v-if="filteredProjectCharterGroups.length > 0" class="space-y-4">
                        <div v-for="group in filteredProjectCharterGroups" :key="group.project.id"
                            class="border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                            <div class="px-3 py-3">
                                <StatusImplementationTable :projects="[group.project]" />
                                <div class="mt-3 flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10">
                                    <template v-for="(roadmapProject, idx) in roadmapVersionsForGroup(group)" :key="`rm-pc-${roadmapProject.roadmap_key}`">
                                        <ProjectRoadmapSummary 
                                            :project="roadmapProject" 
                                            :sequence="idx + 1"
                                            :expanded="isRoadmapExpanded(roadmapProject.roadmap_key)"
                                            :display-version-label="roadmapProject.roadmap_version_label"
                                            @toggle="toggleRoadmapExpand(roadmapProject.roadmap_key)"
                                        />
                                        <div v-if="isRoadmapExpanded(roadmapProject.roadmap_key)" class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5">
                                            <ProjectRoadmap
                                                :project="roadmapProject"
                                                :form="{ objectives: roadmapProject.charter?.objectives ?? '', duration: roadmapProject.charter?.duration ?? '' }"
                                                :selected-roadmap-version-id="roadmapProject.roadmap_version_label"
                                                :sequence="idx + 1"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="space-y-3 pb-3">
                                <template v-for="(charter, charterIndex) in group.charters"
                                    :key="charter?.id ?? `charter-${group.project.id}-${charterIndex}`">
                                    <ItCharterDocument :it-initiative="group.project"
                                        :form="charterFormFor(group.project, charter)"
                                        :status-timeline="getProjectReviewStatus(group.project, charter)"
                                        :editable="false" />
                                </template>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="mappedProjects.length > 0"
                        class="border border-slate-200 bg-white px-4 py-6 text-center text-xs text-slate-500 dark:border-white/10 dark:bg-[#171717] dark:text-slate-400">
                        Tidak ada project charter dengan status <strong>{{ pcStatusFilter || 'dipilih' }}</strong>.
                    </div>
                    <div v-else
                        class="border border-slate-200 bg-white px-4 py-6 text-center text-xs text-slate-500 dark:border-white/10 dark:bg-[#171717] dark:text-slate-400">
                        Data project charter belum tersedia untuk initiative ini.
                    </div>
                </section>

                <section v-if="activeNav === 'initiative-relation'" id="initiative-relation" class="space-y-0">
                    <div v-if="trsReviewPC.initiative"
                        class="overflow-hidden border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717] p-6">
                        <div class="px-3 py-3">
                            <StatusImplementationTable :projects="mappedProjects" />
                            <div v-if="allRoadmapVersions.length > 0" class="mt-3 flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10">
                                <template v-for="(roadmapProject, idx) in allRoadmapVersions" :key="`rm-rel-${roadmapProject.roadmap_key}`">
                                    <ProjectRoadmapSummary 
                                        :project="roadmapProject" 
                                        :sequence="idx + 1"
                                        :expanded="isRoadmapExpanded(roadmapProject.roadmap_key)"
                                        :display-version-label="roadmapProject.roadmap_version_label"
                                        @toggle="toggleRoadmapExpand(roadmapProject.roadmap_key)"
                                    />
                                    <div v-if="isRoadmapExpanded(roadmapProject.roadmap_key)" class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5">
                                        <ProjectRoadmap
                                            :project="roadmapProject"
                                            :form="{ objectives: roadmapProject.charter?.objectives ?? '', duration: roadmapProject.charter?.duration ?? '' }"
                                            :selected-roadmap-version-id="roadmapProject.roadmap_version_label"
                                            :sequence="idx + 1"
                                        />
                                    </div>
                                </template>
                            </div>
                        </div>
                        <InitiativeDetailsWithRelations :initiative="trsReviewPC.initiative"
                            :relations="initiativeRelations" variant="emerald" status-label="Status"
                            relations-title="Initiative Relations" column-a-label="Predecessor"
                            column-b-label="Successor" />
                    </div>
                    <div v-else
                        class="border border-slate-200 bg-white px-4 py-6 text-center text-xs text-slate-500 dark:border-white/10 dark:bg-[#171717] dark:text-slate-400">
                        Data initiative relation belum tersedia untuk initiative ini.
                    </div>
                </section>

                <section v-if="activeNav === 'roadmap'" id="roadmap" class="print:hidden">
                    <div class="px-3 py-3">
                        <StatusImplementationTable :projects="mappedProjects" />
                        <div v-if="allRoadmapVersions.length > 0" class="mt-3 flex flex-col overflow-hidden rounded-xl border border-[#d0dce8] shadow-sm dark:border-white/10">
                            <template v-for="(roadmapProject, idx) in allRoadmapVersions" :key="`rm-road-${roadmapProject.roadmap_key}`">
                                <ProjectRoadmapSummary 
                                    :project="roadmapProject" 
                                    :sequence="idx + 1"
                                    :expanded="isRoadmapExpanded(roadmapProject.roadmap_key)"
                                    :display-version-label="roadmapProject.roadmap_version_label"
                                    @toggle="toggleRoadmapExpand(roadmapProject.roadmap_key)"
                                />
                                <div v-if="isRoadmapExpanded(roadmapProject.roadmap_key)" class="border-b border-[#d0dce8] bg-slate-50/50 p-4 dark:border-white/10 dark:bg-white/5">
                                    <ProjectRoadmap
                                        :project="roadmapProject"
                                        :form="{ objectives: roadmapProject.charter?.objectives ?? '', duration: roadmapProject.charter?.duration ?? '' }"
                                        :selected-roadmap-version-id="roadmapProject.roadmap_version_label"
                                        :sequence="idx + 1"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, onMounted, ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import ItCharterDocument from '@/Components/ProjectCharter/ItCharterDocument.vue';
import ProjectRoadmap from '@/Components/Roadmap/ProjectRoadmap.vue';
import ProjectRoadmapSummary from '@/Components/Roadmap/ProjectRoadmapSummary.vue';
import StatusImplementationTable from '@/Components/ITInitiative/StatusImplementationTable.vue';
import ReviewContent from '@/Components/ReviewPC/ReviewContent.vue';
import InitiativeDetailsWithRelations from '@/Components/InitiativeRelation/InitiativeDetailsWithRelations.vue';

const props = defineProps({
    trsReviewPC: {
        type: Object,
        required: true,
    },
    reviewOptions: {
        type: Array,
        default: () => [],
    },
    mappedProject: {
        type: Object,
        default: null,
    },
    mappedProjects: {
        type: Array,
        default: () => [],
    },
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();

const review = computed(() => ({
    ...props.trsReviewPC,
    detail_kesimpulan: props.trsReviewPC?.detail_kesimpulan ?? props.trsReviewPC?.detail_penjelasan ?? '',
}));

const initiativeName = computed(() => review.value?.initiative?.name ?? '-');

const headerTitle = computed(() => {
    const baseName = initiativeName.value;
    switch (activeNav.value) {
        case 'roadmap':
            return `Roadmap Project Charter: ${baseName}`;
        case 'initiative-relation':
            return `Initiative Relation Project Charter: ${baseName}`;
        case 'project-charter':
            return `Project Charter: ${baseName}`;
        case 'review':
        default:
            return `Review Project Charter: ${baseName}`;
    }
});

const normalizeText = (value) => String(value ?? '').replace(/\u200B/g, '').trim();

const splitToItems = (value) => {
    const text = normalizeText(value);
    if (!text) return [];

    let items = text
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);

    if (items.length === 1 && text.includes(' - ')) {
        items = text.split(/\s+-\s+/).map((line) => line.trim()).filter(Boolean);
    }

    return items.map((item) => item.replace(/^[-*]\s*/, '').trim()).filter(Boolean);
};

const parseHow = (value) => {
    const text = normalizeText(value);
    if (!text) {
        return { intro: '', steps: [] };
    }

    const stepMatches = Array.from(text.matchAll(/(?:^|[;\n])\s*\d+\.\s*([^;\n]+)/g));
    if (stepMatches.length) {
        const firstIndex = stepMatches[0].index ?? 0;
        const intro = text.slice(0, firstIndex).trim();
        const steps = stepMatches.map((match) => match[1].trim()).filter(Boolean);
        return { intro, steps };
    }

    return { intro: '', steps: splitToItems(text) };
};

const penjelasanItems = computed(() => splitToItems(review.value?.penjelasan));
const whyItems = computed(() => splitToItems(review.value?.why));
const projectProfileItems = computed(() => splitToItems(review.value?.project_profile));
const howParsed = computed(() => parseHow(review.value?.how));

const printPage = () => {
    window.print();
};

const activeNav = ref('review');

const setActiveNav = (value) => {
    activeNav.value = value;
};

onMounted(() => {
    const hash = String(window.location.hash || '').replace('#', '');
    if (hash) {
        activeNav.value = hash;
    }
});

const selectedReviewId = computed({
    get: () => String(props.trsReviewPC?.id ?? ''),
    set: (value) => {
        if (!value || String(value) === String(props.trsReviewPC?.id)) return;
        router.visit(route('program-evaluation.show', value));
    },
});

const formatReviewLabel = (option) => {
    const code = option?.initiative?.code ?? option?.initiative_id ?? option?.id ?? '-';
    const name = option?.initiative?.name ?? 'Initiative';
    return `${code} - ${name}`;
};

// --- Roadmap Per Version Logic ---
const expandedRoadmapItems = reactive(new Set());

const isRoadmapExpanded = (roadmapKey) => {
    return expandedRoadmapItems.has(roadmapKey);
};

const toggleRoadmapExpand = (roadmapKey) => {
    if (expandedRoadmapItems.has(roadmapKey)) {
        expandedRoadmapItems.delete(roadmapKey);
    } else {
        expandedRoadmapItems.add(roadmapKey);
    }
};

const normalizeVersionLabel = (value) => {
    const raw = String(value ?? '').trim();
    const lower = raw.toLowerCase();
    if (!raw || lower === 'v') return 'v1';
    if (/^v\d+$/.test(lower)) return `v${Math.max(Number(lower.slice(1)) || 1, 1)}`;
    if (/^\d+$/.test(raw)) return `v${Math.max(Number(raw) || 1, 1)}`;
    return raw;
};

const roadmapVersionsFor = (project) => {
    if (!project) return [];

    const charters = Array.isArray(project?.charters) && project.charters.length > 0
        ? [...project.charters].sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))
        : (project?.charter ? [project.charter] : []);

    if (charters.length === 0) {
        return [{
            ...project,
            charter: null,
            milestones: [],
            roadmap_version_label: null,
            roadmap_key: `project-${project.id}-no-charter`,
        }];
    }

    return charters.map((charter, charterIndex) => {
        const versionLabelRaw = String(charter?.version_label ?? '').trim();
        const versionLabelDisplay = versionLabelRaw || `v${Math.max(charters.length - charterIndex, 1)}`;
        const versionKey = normalizeVersionLabel(versionLabelDisplay);
        const charterMilestones = Array.isArray(charter?.milestones) ? charter.milestones : [];

        const milestones = charterMilestones.map((milestone) => ({
            ...milestone,
            version: versionKey || milestone?.version || null,
        }));

        return {
            ...project,
            charters: [charter], // Tambahkan ini agar komponen summary hanya merender 1 baris bar
            charter,
            milestones,
            roadmap_version_label: versionLabelDisplay,
            roadmap_key: `project-${project.id}-charter-${charter?.id ?? charterIndex}`,
        };
    });
};

const allRoadmapVersions = computed(() => {
    return props.mappedProjects.flatMap((proj) => roadmapVersionsFor(proj));
});

const roadmapProjectFrom = (project) => {
    // Keep for backward compatibility if needed, but we prefer allRoadmapVersions
    const versions = roadmapVersionsFor(project);
    return versions.length > 0 ? versions[0] : null;
};

const mappedRoadmapProject = computed(() => roadmapProjectFrom(props.mappedProject));

// ---- Per-project charter form helper ----
const charterFormFor = (proj, charterOverride = null) => {
    const charter = charterOverride ?? proj?.charter ?? {};
    return {
        version_label: charter?.version_label ?? '',
        category: charter?.category ?? '',
        duration: charter?.duration ?? '',
        owner: charter?.owner ?? '',
        tgl_dokumen: charter?.tgl_dokumen ?? '',
        background: charter?.background ?? '',
        objectives: charter?.objectives ?? '',
        scope: charter?.scope ?? '',
        impact_value: charter?.impact_value ?? '',
        key_personnel: charter?.key_personnel ?? '',
        key_items: charter?.key_items ?? '',
        budget: charter?.budget ?? '',
        risks_identified: charter?.risks_identified ?? '',
        risk_mitigation: charter?.risk_mitigation ?? '',
    };
};

// Keep backward-compatible charterForm for other tabs
const charterForm = computed(() => charterFormFor(props.mappedProject));

const projectChartersFor = (proj) => {
    if (Array.isArray(proj?.charters) && proj.charters.length > 0) {
        return proj.charters;
    }
    if (proj?.charter) {
        return [proj.charter];
    }
    return [];
};

// ---- Status Timeline Filter ----
const pcStatusFilter = ref('');

const statusTimelineFilterOptions = [
    { value: '', label: 'Semua', activeClass: 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' },
    { value: 1, label: 'Draft', activeClass: 'bg-slate-100 text-slate-600 ring-1 ring-slate-300' },
    { value: 2, label: 'Propose', activeClass: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300' },
    { value: 3, label: 'Review', activeClass: 'bg-amber-100 text-amber-700 ring-1 ring-amber-300' },
    { value: 5, label: 'Baseline', activeClass: 'bg-purple-100 text-purple-700 ring-1 ring-purple-300' },
    { value: 4, label: 'Approved', activeClass: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300' },
];

const getProjectReviewStatus = (proj, charter = null) => {
    // Prioritas status per versi charter, fallback ke status project bila perlu.
    const raw = charter?.status ?? proj?.status;
    if (raw === null || raw === undefined || raw === '') return null;
    return Number(raw);
};

const filteredProjectCharterGroups = computed(() => {
    const filterVal = pcStatusFilter.value;
    
    return props.mappedProjects
        .map((proj) => {
            const allCharters = projectChartersFor(proj);
            
            // Filter individual charter versions based on the selected status
            const filteredCharters = (filterVal === '' || filterVal === null)
                ? allCharters
                : allCharters.filter(c => {
                    const status = c?.status ?? proj?.status;
                    return Number(status) === Number(filterVal);
                });
            
            return {
                project: proj,
                charters: filteredCharters,
            };
        })
        .filter((group) => group.charters.length > 0);
});

const roadmapVersionsForGroup = (group) => {
    // Return only roadmap versions that match the filtered charters in the group
    const allVersions = roadmapVersionsFor(group.project);
    if (pcStatusFilter.value === '' || pcStatusFilter.value === null) return allVersions;
    
    const allowedCharterIds = new Set(group.charters.map(c => c.id));
    return allVersions.filter(v => v.charter && allowedCharterIds.has(v.charter.id));
};

const initiativeLabel = (initiative) => {
    const code = initiative?.code ?? initiative?.id ?? '-';
    const name = initiative?.name ?? '';
    const baseLabel = name ? `${code} - ${name}` : code;
    const coeName = initiative?.coe_name ?? initiative?.coe?.name ?? '';
    return coeName ? `${baseLabel} (CoE: ${coeName})` : baseLabel;
};

const findInitiativeById = (initiativeId) => {
    if (!initiativeId || !props.mstInitiatives.length) return null;
    return props.mstInitiatives.find(opt => Number(opt.id) === Number(initiativeId));
};

const buildInitiativeRelations = () => {
    const initiative = props.trsReviewPC?.initiative;
    if (!initiative) return [];

    const rows = [];
    const seen = new Set();

    // Get all relations from trsReviewPC.initiative (eager-loaded by controller)
    const allRelations = [
        ...(initiative?.initiative_relations_row ?? initiative?.initiativeRelationsRow ?? []),
        ...(initiative?.initiative_relations_column ?? initiative?.initiativeRelationsColumn ?? []),
    ];

    const relationKey = (relation) => {
        if (relation?.id) {
            return `id-${relation.id}`;
        }
        return `row-${relation?.initiative_code_row}-col-${relation?.initiative_code_column}`;
    };

    allRelations.forEach((relation) => {
        const key = relationKey(relation);
        if (seen.has(key)) {
            return;
        }
        seen.add(key);

        const rowInitiative = relation.initiative_row;
        const columnInitiative = relation.initiative_column;
        const rowFallback = relation.initiative_code_row;
        const columnFallback = relation.initiative_code_column;
        const justifikasi = relation.justifikasi ?? '-';

        rows.push({
            id: relation.id ?? `${relation.initiative_code_row}-${relation.initiative_code_column}`,
            predecessorLabel: rowInitiative ? initiativeLabel(rowInitiative) : (rowFallback ?? '-'),
            successorLabel: columnInitiative ? initiativeLabel(columnInitiative) : (columnFallback ?? '-'),
            type_relation: relation.type_relation ?? null,
            modelRelasi: relation.model_relasi ?? '-',
            justifikasi,
        });
    });

    return rows;
};

const initiativeRelations = computed(() => buildInitiativeRelations());

const currentStatus = computed(() => {
    const val = Number(props.trsReviewPC?.initiative?.status);
    switch (val) {
        case 1: return { label: 'Draft', class: 'bg-slate-100 text-slate-600 ring-1 ring-slate-300' };
        case 2: return { label: 'Propose', class: 'bg-blue-100 text-blue-700 ring-1 ring-blue-300' };
        case 3: return { label: 'Review', class: 'bg-amber-100 text-amber-700 ring-1 ring-amber-300' };
        case 4: return { label: 'Approved', class: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300' };
        case 5: return { label: 'Baseline', class: 'bg-purple-100 text-purple-700 ring-1 ring-purple-300' };
        default: return null;
    }
});
</script>
