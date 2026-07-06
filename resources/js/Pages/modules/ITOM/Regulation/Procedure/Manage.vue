<template>
    <ModulLayout title="Kelola Procedure">
        <div class="animate-fade-in-up space-y-6">
            <!-- Unified Page Header -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="flex flex-col gap-3 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Bagian Kiri: Judul Konten -->
                    <div class="flex-1 min-w-0">
                        <h2 v-if="isHeaderVisible" class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2 flex-wrap">
                            <svg class="h-4 w-4 text-[#821f44]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Kelola Procedure
                            <span class="text-slate-400 dark:text-slate-500 font-normal">| {{ activeRegulation?.judul || 'Belum ada regulasi aktif' }}</span>
                        </h2>
                        <h2 v-else class="text-[11px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#821f44]/85" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Kelola Procedure
                        </h2>
                    </div>

                    <!-- Bagian Kanan: Aksi & Pengaturan Tampilan -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- View Settings Controls (Header and Navigation Pane toggles) -->
                        <div class="flex items-center gap-1.5 border-r border-slate-200 dark:border-white/10 pr-3 mr-1">
                            <button
                                @click="isHeaderVisible = !isHeaderVisible"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.25 border border-slate-200 dark:border-white/10 bg-transparent rounded-lg text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition active:scale-95 cursor-pointer"
                            >
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse-slow" :class="isHeaderVisible ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-zinc-700'"></span>
                                Header
                            </button>
                            <button
                                @click="isSidebarVisible = !isSidebarVisible"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.25 border border-slate-200 dark:border-white/10 bg-transparent rounded-lg text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition active:scale-95 cursor-pointer"
                            >
                                <span class="w-1.5 h-1.5 rounded-full animate-pulse-slow" :class="isSidebarVisible ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-zinc-700'"></span>
                                Navigasi Pane
                            </button>
                        </div>

                        <!-- Action Button (Tampil hanya jika isHeaderVisible true) -->
                        <div v-if="isHeaderVisible" class="flex shrink-0 gap-1">
                            <Link
                                :href="route('itom.regulation.procedure.index', activeRegulation ? { regulation_id: activeRegulation.id } : {})"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#9c2552]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                </svg>
                                Lihat Dokumen
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Word-style Navigation & Editor Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Sidebar: MS Word Style Navigation Pane -->
                <aside v-if="isSidebarVisible" class="lg:col-span-4 xl:col-span-3 bg-white dark:bg-[#171717] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden lg:sticky lg:top-32 z-10 print:hidden">
                    <div class="flex flex-col h-[580px] max-h-[calc(100vh-14rem)]">
                        <!-- Search Bar -->
                        <div class="p-3 border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#1b1b1b]">
                            <div class="relative flex items-center">
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search document"
                                    class="w-full pl-3 pr-10 py-1.5 text-xs bg-white dark:bg-[#121212] border border-slate-300 dark:border-white/10 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-900 dark:text-white"
                                />
                                <div class="absolute right-0 flex items-center pr-2.5 space-x-1 text-slate-400 dark:text-slate-500">
                                    <svg class="w-3.5 h-3.5 cursor-pointer hover:text-slate-600 dark:hover:text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span class="w-[1px] h-3 bg-slate-300 dark:bg-white/10"></span>
                                    <svg class="w-2.5 h-2.5 cursor-pointer hover:text-slate-600 dark:hover:text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs (Headings, Pages, Results) -->
                        <div class="flex border-b border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-[#1b1b1b]">
                            <button
                                v-for="tabName in ['Headings', 'Pages', 'Results']"
                                :key="tabName"
                                @click="activeNavTab = tabName.toLowerCase()"
                                class="flex-1 py-2 text-center text-[11px] font-semibold border-b-2 transition-all relative"
                                :class="[
                                    activeNavTab === tabName.toLowerCase()
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold'
                                        : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'
                                ]"
                            >
                                {{ tabName }}
                                <span 
                                    v-if="tabName === 'Results' && searchMatches.length > 0"
                                    class="absolute top-1 right-2 px-1 text-[8px] bg-blue-500 text-white rounded-full scale-90"
                                >
                                    {{ searchMatches.length }}
                                </span>
                            </button>
                        </div>

                        <!-- Tab Content Scroll Area -->
                        <div class="flex-1 overflow-y-auto p-2 space-y-1">
                            <!-- 1. HEADINGS TAB -->
                            <div v-show="activeNavTab === 'headings'" class="space-y-0.5">
                                <div v-for="node in filteredHeadingTree" :key="node.id" class="text-xs">
                                    <!-- Node Row -->
                                    <div 
                                        class="flex items-center py-1.5 px-2 rounded cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group select-none"
                                        :class="[
                                            activeTab === node.targetTab && !activeSubId
                                                ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                                                : 'text-slate-700 dark:text-slate-300'
                                        ]"
                                        :style="{ paddingLeft: `${node.level * 10 + 4}px` }"
                                        @click="handleNodeClick(node)"
                                    >
                                        <!-- Triangle Toggle -->
                                        <span 
                                            @click.stop="toggleNodeExpand(node.id)"
                                            class="w-4 h-4 flex items-center justify-center mr-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded cursor-pointer"
                                        >
                                            <template v-if="node.children && node.children.length > 0">
                                                <svg v-if="expandedNodes[node.id]" class="w-2.5 h-2.5 text-slate-500 dark:text-slate-400 fill-current" viewBox="0 0 24 24">
                                                    <path d="M7 10l5 5 5-5z" />
                                                </svg>
                                                <svg v-else class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 fill-current" viewBox="0 0 24 24">
                                                    <path d="M10 17l5-5-5-5z" />
                                                </svg>
                                            </template>
                                            <template v-else>
                                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover:bg-slate-400"></span>
                                            </template>
                                        </span>
                                        <span class="truncate flex-1 font-medium" :title="node.label">{{ node.label }}</span>
                                    </div>

                                    <!-- Children List -->
                                    <div v-if="node.children && node.children.length > 0 && expandedNodes[node.id]" class="mt-0.5">
                                        <div v-for="child in node.children" :key="child.id">
                                            <!-- Child Row -->
                                            <div 
                                                class="flex items-center py-1.5 px-2 rounded cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group select-none"
                                                :class="[
                                                    activeTab === child.targetTab && activeSubId === child.targetId
                                                        ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                                                        : 'text-slate-600 dark:text-slate-400'
                                                ]"
                                                :style="{ paddingLeft: `${child.level * 10 + 8}px` }"
                                                @click="handleNodeClick(child)"
                                            >
                                                <span 
                                                    @click.stop="toggleNodeExpand(child.id)"
                                                    class="w-4 h-4 flex items-center justify-center mr-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded cursor-pointer"
                                                >
                                                    <template v-if="child.children && child.children.length > 0">
                                                        <svg v-if="expandedNodes[child.id]" class="w-2.5 h-2.5 text-slate-500 dark:text-slate-400 fill-current" viewBox="0 0 24 24">
                                                            <path d="M7 10l5 5 5-5z" />
                                                        </svg>
                                                        <svg v-else class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 fill-current" viewBox="0 0 24 24">
                                                            <path d="M10 17l5-5-5-5z" />
                                                        </svg>
                                                    </template>
                                                    <template v-else>
                                                        <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover:bg-slate-400"></span>
                                                    </template>
                                                </span>
                                                <span class="truncate flex-1 text-[11px]" :title="child.label">{{ child.label }}</span>
                                            </div>

                                            <!-- Grandchildren List (SOPs) -->
                                            <div v-if="child.children && child.children.length > 0 && expandedNodes[child.id]" class="mt-0.5">
                                                <div 
                                                    v-for="gchild in child.children" 
                                                    :key="gchild.id"
                                                    class="flex items-center py-1.5 px-2 rounded cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group select-none"
                                                    :class="[
                                                        activeTab === gchild.targetTab && activeSubId === gchild.targetId
                                                            ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                                                            : 'text-slate-500 dark:text-slate-500'
                                                    ]"
                                                    :style="{ paddingLeft: `${gchild.level * 10 + 12}px` }"
                                                    @click="handleNodeClick(gchild)"
                                                >
                                                    <span class="w-4 h-4 flex items-center justify-center mr-1 text-slate-400">
                                                        <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover:bg-slate-400"></span>
                                                    </span>
                                                    <span class="truncate flex-1 text-[10px]" :title="gchild.label">{{ gchild.label }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. PAGES TAB -->
                            <div v-show="activeNavTab === 'pages'" class="grid grid-cols-2 gap-2 p-1">
                                <button
                                    v-for="(sec, idx) in allSections"
                                    :key="sec.id"
                                    @click="activeTab = sec.id"
                                    class="flex flex-col items-center justify-between p-2 h-24 border rounded-lg text-center transition-all bg-slate-50/50 dark:bg-[#1a1a1a]"
                                    :class="[
                                        activeTab === sec.id
                                            ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-50/10'
                                            : 'border-slate-200 dark:border-white/5 hover:border-slate-300 dark:hover:border-white/10'
                                    ]"
                                >
                                    <div class="flex-1 flex items-center justify-center overflow-hidden">
                                        <span class="text-[9px] font-medium text-slate-700 dark:text-slate-300 line-clamp-3 leading-tight">
                                            {{ sec.labelShort || sec.label }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[8px] text-slate-400 font-semibold uppercase tracking-wider">
                                        Page {{ idx + 1 }}
                                    </div>
                                </button>
                            </div>

                            <!-- 3. RESULTS TAB -->
                            <div v-show="activeNavTab === 'results'" class="space-y-1">
                                <div v-if="searchQuery.trim() === ''" class="text-center py-8 text-slate-400 dark:text-slate-500 text-[11px]">
                                    Masukkan kata kunci untuk mencari dokumen.
                                </div>
                                <div v-else-if="searchMatches.length === 0" class="text-center py-8 text-slate-400 dark:text-slate-500 text-[11px]">
                                    Tidak ada hasil untuk "{{ searchQuery }}".
                                </div>
                                <div v-else class="space-y-1.5">
                                    <button
                                        v-for="(match, idx) in searchMatches"
                                        :key="idx"
                                        @click="goToMatch(match)"
                                        class="w-full text-left p-2 hover:bg-slate-100 dark:hover:bg-white/5 rounded border border-slate-100 dark:border-white/5 transition-colors block"
                                    >
                                        <div class="text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider truncate">
                                            {{ match.sectionName }}
                                        </div>
                                        <div class="text-[11px] text-slate-700 dark:text-slate-300 font-semibold truncate mt-0.5">
                                            {{ match.title }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 italic mt-0.5 line-clamp-2 leading-relaxed" v-html="highlightText(match.preview, searchQuery)">
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Active Editor Content -->
                <main :class="isSidebarVisible ? 'lg:col-span-8 xl:col-span-9' : 'lg:col-span-12'" class="space-y-6">
                    <!-- Tab: TKO Content -->
                    <template v-if="activeSection?.type === 'tko'">
                        <ManageSection
                            ref="manageSectionRef"
                            :activeSection="activeSection"
                            :activeRegulation="activeRegulation"
                        />
                    </template>

                    <!-- Tab: FUNGSI -->
                    <template v-if="activeSection?.id === 'fungsi'">
                        <ManageFunction
                            ref="fungsiEditorRef"
                            :actors="actors"
                            :organizations="organizations"
                            :activeRegulation="activeRegulation"
                            :functions="functions"
                        />
                    </template>

                    <!-- Tab: PROSEDUR -->
                    <template v-if="activeSection?.id === 'prosedur'">
                        <ManageActivity
                            ref="prosedurEditorRef"
                            :categories="categories"
                            :sop="sop"
                            :flowChartSops="flowChartSops"
                            :actors="actors"
                            :activeRegulation="activeRegulation"
                            :activeCategoryId="activeCategoryId"
                            @select-category="id => activeSubId = id ? `category_${id}` : null"
                        />
                    </template>

                    <!-- Tab: KELOLA SECTION -->
                    <template v-if="activeSection?.id === 'manage_sections'">
                        <SectionEditor
                            ref="sectionEditorRef"
                            :tkoSections="tkoSections"
                            :activeRegulation="activeRegulation"
                        />
                    </template>
                </main>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import SectionEditor from '@/Components/modules/ITOM/Regulation/Procedure/SectionEditor.vue';
import ManageSection from '@/Components/modules/ITOM/ITOperatingModel/Regulation/Procedure/ManageSection.vue';
import ManageActivity from '@/Components/modules/ITOM/ITOperatingModel/Regulation/Procedure/ManageActivity.vue';
import ManageFunction from '@/Components/modules/ITOM/ITOperatingModel/Regulation/Procedure/ManageFunction.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
    flowChartSops: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tkoSections: {
        type: Array,
        default: () => [],
    },
    functions: {
        type: Array,
        default: () => [],
    },
});

const allSections = computed(() => {
    const list = [];
    const romanNumerals = {
        1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X'
    };

    // 1. TKO Sections before Fungsi (order < 4)
    const tkoBefore = (props.tkoSections || []).filter(s => s.order < 4);
    tkoBefore.forEach(s => {
        list.push({
            id: `tko_${s.id}`,
            section_id: s.id,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: 'tko',
            name: s.name,
            content: s.contents?.[0]?.content || ''
        });
    });

    // 2. Fungsi (order 4)
    list.push({
        id: 'fungsi',
        order: 4,
        label: 'IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT',
        labelShort: 'IV',
        type: 'fungsi'
    });

    // 3. Prosedur (order 5)
    list.push({
        id: 'prosedur',
        order: 5,
        label: 'V. PROSEDUR',
        labelShort: 'V',
        type: 'prosedur'
    });

    // 4. TKO Sections after Prosedur (order > 5)
    const tkoAfter = (props.tkoSections || []).filter(s => s.order > 5);
    tkoAfter.forEach(s => {
        list.push({
            id: `tko_${s.id}`,
            section_id: s.id,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: 'tko',
            name: s.name,
            content: s.contents?.[0]?.content || ''
        });
    });

    // 5. Manage Sections settings tab at the end
    list.push({
        id: 'manage_sections',
        order: 100,
        label: '⚙️ KELOLA SECTION',
        labelShort: '⚙️',
        type: 'manage_sections'
    });

    return list.sort((a, b) => a.order - b.order);
});

const activeTab = ref(allSections.value[0]?.id || 'fungsi');

const activeSection = computed(() => {
    return allSections.value.find(s => s.id === activeTab.value) || null;
});

const selectedRegulationId = ref(props.selectedRegulationId);
watch(() => props.selectedRegulationId, (newId) => {
    selectedRegulationId.value = newId;
});

const isManagePage = computed(() => true);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find((r) => r.id === selectedRegulationId.value) || props.regulations[0] || null;
});

const activeCategoryId = computed(() => {
    if (!activeSubId.value) return null;
    const subStr = String(activeSubId.value);
    if (subStr.startsWith('category_')) {
        return Number(subStr.replace('category_', ''));
    }
    if (subStr.startsWith('sop_')) {
        const sopId = Number(subStr.replace('sop_', ''));
        const found = (props.sop || []).find(s => s.id === sopId);
        return found ? Number(found.category_id) : null;
    }
    const num = Number(activeSubId.value);
    if (!isNaN(num)) {
        const foundSop = (props.sop || []).find(s => s.id === num);
        if (foundSop) return Number(foundSop.category_id);
        const foundCat = (props.categories || []).find(c => c.id === num);
        if (foundCat) return num;
    }
    return null;
});

// ---------------------------------------------------
// WORD-STYLE NAVIGATION PANE STATE & METHODS
// ---------------------------------------------------
const isHeaderVisible = ref(true);
const isSidebarVisible = ref(true);
const activeNavTab = ref('headings');
const searchQuery = ref('');
const activeSubId = ref(null);
const expandedNodes = ref({});

onMounted(() => {
    allSections.value.forEach(sec => {
        expandedNodes.value[sec.id] = true;
    });
    (props.categories || []).forEach(cat => {
        expandedNodes.value[`category_${cat.id}`] = true;
    });
    expandedNodes.value['category_uncategorized'] = true;
});

const headingTree = computed(() => {
    const list = [];
    const romanNumerals = {
        1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X'
    };

    allSections.value.forEach(sec => {
        const node = {
            id: sec.id,
            label: sec.label,
            level: 0,
            type: 'section',
            targetTab: sec.id,
            children: []
        };

        if (sec.id === 'fungsi') {
            (props.actors || []).forEach(actor => {
                node.children.push({
                    id: `actor_${actor.id}`,
                    label: actor.name,
                    level: 1,
                    type: 'actor',
                    targetTab: 'fungsi',
                    targetId: actor.id
                });
            });
        } else if (sec.id === 'prosedur') {
            const catMap = {};
            (props.categories || []).forEach(cat => {
                catMap[cat.id] = {
                    id: `category_${cat.id}`,
                    label: cat.tipe,
                    level: 1,
                    type: 'category',
                    targetTab: 'prosedur',
                    targetId: `category_${cat.id}`,
                    children: []
                };
            });

            const uncategorized = {
                id: 'category_uncategorized',
                label: 'LAIN-LAIN',
                level: 1,
                type: 'category',
                targetTab: 'prosedur',
                targetId: 'category_uncategorized',
                children: []
            };

            (props.sop || []).forEach(s => {
                const item = {
                    id: `sop_${s.id}`,
                    label: s.name || s.judul || `SOP ${s.id}`,
                    level: 2,
                    type: 'sop',
                    targetTab: 'prosedur',
                    targetId: s.id
                };
                if (s.category_id && catMap[s.category_id]) {
                    catMap[s.category_id].children.push(item);
                } else {
                    uncategorized.children.push(item);
                }
            });

            Object.values(catMap).forEach(catNode => {
                node.children.push(catNode);
            });
            if (uncategorized.children.length > 0) {
                node.children.push(uncategorized);
            }
        }

        list.push(node);
    });

    return list;
});

const filteredHeadingTree = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return headingTree.value;

    const filtered = [];
    headingTree.value.forEach(node => {
        const labelMatches = node.label.toLowerCase().includes(query);
        const matchingChildren = [];

        if (node.children) {
            node.children.forEach(child => {
                const childMatches = child.label.toLowerCase().includes(query);
                const matchingGrandchildren = [];

                if (child.children) {
                    child.children.forEach(gchild => {
                        if (gchild.label.toLowerCase().includes(query)) {
                            matchingGrandchildren.push(gchild);
                        }
                    });
                }

                if (childMatches || matchingGrandchildren.length > 0) {
                    const clonedChild = { ...child };
                    if (matchingGrandchildren.length > 0) {
                        clonedChild.children = matchingGrandchildren;
                    }
                    matchingChildren.push(clonedChild);
                }
            });
        }

        if (labelMatches || matchingChildren.length > 0) {
            const clonedNode = { ...node };
            if (matchingChildren.length > 0) {
                clonedNode.children = matchingChildren;
            }
            expandedNodes.value[node.id] = true;
            matchingChildren.forEach(c => {
                expandedNodes.value[c.id] = true;
            });
            filtered.push(clonedNode);
        }
    });

    return filtered;
});

const searchMatches = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return [];

    const matches = [];

    // Search in TKO sections
    (props.tkoSections || []).forEach(sec => {
        const romanNumerals = {
            1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X'
        };
        const sectionLabel = `${romanNumerals[sec.order] || sec.order}. ${sec.name.toUpperCase()}`;

        if (sec.name.toLowerCase().includes(query)) {
            matches.push({
                sectionId: `tko_${sec.id}`,
                sectionName: sectionLabel,
                title: sec.name,
                preview: sec.contents?.[0]?.content || 'Dokumen kosong',
                targetTab: `tko_${sec.id}`
            });
        } else {
            const content = sec.contents?.[0]?.content || '';
            const idx = content.toLowerCase().indexOf(query);
            if (idx > -1) {
                const start = Math.max(0, idx - 30);
                const end = Math.min(content.length, idx + query.length + 50);
                let preview = content.substring(start, end);
                if (start > 0) preview = '...' + preview;
                if (end < content.length) preview = preview + '...';
                matches.push({
                    sectionId: `tko_${sec.id}`,
                    sectionName: sectionLabel,
                    title: sec.name,
                    preview: preview,
                    targetTab: `tko_${sec.id}`
                });
            }
        }
    });

    // Search in Fungsi/Actors
    (props.actors || []).forEach(actor => {
        if (actor.name.toLowerCase().includes(query)) {
            matches.push({
                sectionId: 'fungsi',
                sectionName: 'IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT',
                title: actor.name,
                preview: `Tipe peran: ${actor.tipe || 'Fungsi'}`,
                targetTab: 'fungsi',
                targetId: actor.id
            });
        }
    });

    // Search in SOP/Prosedur
    (props.sop || []).forEach(s => {
        const name = s.name || s.judul || '';
        if (name.toLowerCase().includes(query)) {
            matches.push({
                sectionId: 'prosedur',
                sectionName: 'V. PROSEDUR',
                title: name,
                preview: s.description || 'Lihat langkah-langkah prosedur',
                targetTab: 'prosedur',
                targetId: s.id
            });
        }
    });

    return matches;
});

function toggleNodeExpand(nodeId) {
    expandedNodes.value[nodeId] = !expandedNodes.value[nodeId];
}

function handleNodeClick(node) {
    activeTab.value = node.targetTab;
    if (node.targetId) {
        activeSubId.value = node.targetId;
        scrollToElement(node.targetId);
    } else {
        activeSubId.value = null;
    }
}

function scrollToElement(targetId) {
    setTimeout(() => {
        const cleanId = String(targetId).startsWith('category_') ? String(targetId).replace('category_', '') : targetId;
        const el = document.getElementById(`actor-row-${cleanId}`) || 
                   document.getElementById(`sop-row-${cleanId}`) ||
                   document.getElementById(`category-row-${cleanId}`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('bg-blue-500/10', 'dark:bg-blue-500/20');
            setTimeout(() => {
                el.classList.remove('bg-blue-500/10', 'dark:bg-blue-500/20');
            }, 3000);
        }
    }, 400);
}

function goToMatch(match) {
    activeTab.value = match.targetTab;
    if (match.targetId) {
        activeSubId.value = match.targetId;
        scrollToElement(match.targetId);
    } else {
        activeSubId.value = null;
    }
}

function highlightText(text, query) {
    if (!text || !query) return text || '';
    const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    const regex = new RegExp(`(${escapedQuery})`, 'gi');
    return text.replace(regex, '<span class="bg-yellow-100 text-slate-900 px-0.5 rounded font-bold dark:bg-yellow-500/30 dark:text-yellow-100">$1</span>');
}

// ---------------------------------------------------
// LOCAL EDITING STATES
// ---------------------------------------------------
// Child component refs for tab-switch unsaved-change detection
const manageSectionRef = ref(null);
const fungsiEditorRef = ref(null);
const prosedurEditorRef = ref(null);
const sectionEditorRef = ref(null);

const isRevertingTab = ref(false);
watch(activeTab, (newTab, oldTab) => {
    if (isRevertingTab.value) {
        isRevertingTab.value = false;
        return;
    }

    let hasUnsaved = false;
    let titleText = '';
    let saveFn = null;
    let clearFn = null;

    if (oldTab && oldTab.startsWith('tko_') && manageSectionRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan dokumen TKO yang belum disimpan.';
        saveFn = () => manageSectionRef.value.saveAll();
        clearFn = () => { if (manageSectionRef.value) { manageSectionRef.value.hasUnsavedChanges.value = false; } };
    } else if (oldTab === 'fungsi' && fungsiEditorRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data aktor yang belum disimpan.';
        saveFn = () => fungsiEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (oldTab === 'prosedur' && prosedurEditorRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data prosedur (kategori/SOP) yang belum disimpan.';
        saveFn = () => prosedurEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (oldTab === 'manage_sections' && sectionEditorRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan pengaturan section yang belum disimpan.';
        saveFn = () => sectionEditorRef.value.saveAll();
        clearFn = () => {};
    }

    if (hasUnsaved) {
        isRevertingTab.value = true;
        activeTab.value = oldTab;

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: `${titleText} Apakah Anda ingin menyimpannya sekarang?`,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#821f44',
            denyButtonColor: '#64748b',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan',
            denyButtonText: 'Jangan Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                saveFn().then(() => {
                    clearFn();
                    activeTab.value = newTab;
                });
            } else if (result.isDenied) {
                clearFn();
                activeTab.value = newTab;
            }
        });
    }
});

function handleBeforeUnload(e) {
    const childrenHaveUnsaved = 
        manageSectionRef.value?.hasUnsavedChanges?.value ||
        fungsiEditorRef.value?.hasUnsavedChanges?.value ||
        prosedurEditorRef.value?.hasUnsavedChanges?.value ||
        sectionEditorRef.value?.hasUnsavedChanges?.value;
    if (childrenHaveUnsaved) {
        e.preventDefault();
        e.returnValue = 'Ada perubahan yang belum disimpan.';
        return e.returnValue;
    }
}

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
});

</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>