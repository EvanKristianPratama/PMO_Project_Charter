<template>
    <!-- Sidebar: MS Word Style Navigation Pane -->
    <aside
        class="lg:col-span-3 xl:col-span-2 bg-white dark:bg-[#171717] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden lg:sticky lg:top-32 z-10 print:hidden"
    >
        <div class="flex flex-col h-[580px] max-h-[calc(100vh-14rem)]">
            <!-- Search Bar -->
            <div
                class="p-3 border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#1b1b1b]"
            >
                <div class="relative flex items-center gap-1.5">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search document"
                        class="w-full pl-3 pr-10 py-1.5 text-xs bg-white dark:bg-[#121212] border border-slate-300 dark:border-white/10 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-900 dark:text-white"
                    />
                    <div
                        class="absolute right-0 flex items-center pr-2.5 space-x-1 text-slate-400 dark:text-slate-500"
                        :class="isHeaderVisible !== undefined ? 'right-8' : 'right-0'"
                    >
                        <svg
                            class="w-3.5 h-3.5 cursor-pointer hover:text-slate-600 dark:hover:text-slate-300"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                        <span
                            class="w-[1px] h-3 bg-slate-300 dark:bg-white/10"
                        ></span>
                        <svg
                            class="w-2.5 h-2.5 cursor-pointer hover:text-slate-600 dark:hover:text-slate-300"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </div>
                    <!-- Document Header Toggle (compact) -->
                    <button
                        @click="toggleHeaderVisibility"
                        class="shrink-0 inline-flex items-center justify-center w-7 h-7 border border-slate-200 dark:border-white/10 bg-transparent rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition active:scale-95 cursor-pointer"
                        :title="effectiveHeaderVisible ? 'Hide Document Header' : 'Show Document Header'"
                    >
                        <span
                            class="w-1.5 h-1.5 rounded-full"
                            :class="
                                effectiveHeaderVisible
                                    ? 'bg-emerald-500'
                                    : 'bg-slate-300 dark:bg-zinc-700'
                            "
                        ></span>
                    </button>
                </div>
            </div>

            <!-- Related Documents Tree (Parent/Child Hierarchy) -->
            <div
                v-if="relatedRegulationTree.length > 0"
                class="border-b border-slate-200 dark:border-white/10"
            >
                <button
                    @click="isRelatedDocsExpanded = !isRelatedDocsExpanded"
                    class="w-full flex items-center justify-between px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition select-none"
                >
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Dokumen Terkait
                        <span class="rounded-full bg-slate-200 dark:bg-white/10 px-1.5 py-0 text-[9px] font-extrabold text-slate-600 dark:text-slate-300">{{ flatRegulationCount }}</span>
                    </span>
                    <svg
                        class="w-2.5 h-2.5 transition-transform duration-200"
                        :class="{ 'rotate-180': isRelatedDocsExpanded }"
                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div v-show="isRelatedDocsExpanded" class="px-2 pb-2 space-y-0.5 max-h-48 overflow-y-auto">
                    <template v-for="node in relatedRegulationTree" :key="node.id">
                        <RegTreeNode
                            :node="node"
                            :depth="0"
                            :active-regulation-id="activeRegulationId"
                            :expanded-reg-nodes="expandedRegNodes"
                            @toggle="toggleRegNode"
                            @navigate="handleRegNavigate"
                        />
                    </template>
                </div>
            </div>

            <!-- Tabs (Headings, Pages, Results) -->
            <div
                class="flex border-b border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-[#1b1b1b]"
            >
                <button
                    v-for="tabName in ['Headings', 'Pages', 'Results']"
                    :key="tabName"
                    @click="activeNavTab = tabName.toLowerCase()"
                    class="flex-1 py-2 text-center text-[11px] font-semibold border-b-2 transition-all relative"
                    :class="[
                        activeNavTab === tabName.toLowerCase()
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold'
                            : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200',
                    ]"
                >
                    {{ tabName }}
                    <span
                        v-if="
                            tabName === 'Results' && searchMatches.length > 0
                        "
                        class="absolute top-1 right-2 px-1 text-[8px] bg-blue-500 text-white rounded-full scale-90"
                    >
                        {{ searchMatches.length }}
                    </span>
                </button>
            </div>

            <!-- Tab Content Scroll Area -->
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                <!-- 1. HEADINGS TAB -->
                <div
                    v-show="activeNavTab === 'headings'"
                    class="space-y-0.5"
                >
                    <div
                        v-for="node in filteredHeadingTree"
                        :key="node.id"
                        class="text-xs"
                    >
                        <!-- Node Row -->
                        <div
                            class="flex items-center py-1.5 px-2 rounded cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group select-none"
                            :class="[
                                activeTab === node.targetTab && !activeSubId
                                    ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                                    : 'text-slate-700 dark:text-slate-300',
                            ]"
                            :style="{
                                paddingLeft: `${node.level * 10 + 4}px`,
                            }"
                            @click="handleNodeClick(node)"
                        >
                            <!-- Triangle Toggle -->
                            <span
                                @click.stop="toggleNodeExpand(node.id)"
                                class="w-4 h-4 flex items-center justify-center mr-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded cursor-pointer"
                            >
                                <template
                                    v-if="
                                        node.children &&
                                        node.children.length > 0
                                    "
                                >
                                    <svg
                                        v-if="expandedNodes[node.id]"
                                        class="w-2.5 h-2.5 text-slate-500 dark:text-slate-400 fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M7 10l5 5 5-5z" />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M10 17l5-5-5-5z" />
                                    </svg>
                                </template>
                                <template v-else>
                                    <span
                                        class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover:bg-slate-400"
                                    ></span>
                                </template>
                            </span>
                            <span
                                class="whitespace-normal flex-1 font-medium"
                                :title="node.label"
                                >{{ node.label }}</span
                            >
                        </div>

                        <!-- Children List -->
                        <div
                            v-if="
                                node.children &&
                                node.children.length > 0 &&
                                expandedNodes[node.id]
                            "
                            class="mt-0.5"
                        >
                            <div
                                v-for="child in node.children"
                                :key="child.id"
                            >
                                <!-- Child Row -->
                                <div
                                    class="flex items-center py-1.5 px-2 rounded cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group select-none"
                                    :class="[
                                        activeTab === child.targetTab &&
                                        activeSubId === child.targetId
                                            ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                                            : 'text-slate-600 dark:text-slate-400',
                                    ]"
                                    :style="{
                                        paddingLeft: `${child.level * 10 + 8}px`,
                                    }"
                                    @click="handleNodeClick(child)"
                                >
                                    <span
                                        @click.stop="toggleNodeExpand(child.id)"
                                        class="w-4 h-4 flex items-center justify-center mr-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded cursor-pointer"
                                    >
                                        <template
                                            v-if="
                                                child.children &&
                                                child.children.length > 0
                                            "
                                        >
                                            <svg
                                                v-if="expandedNodes[child.id]"
                                                class="w-2.5 h-2.5 text-slate-500 dark:text-slate-400 fill-current"
                                                viewBox="0 0 24 24"
                                            >
                                                <path d="M7 10l5 5 5-5z" />
                                            </svg>
                                            <svg
                                                v-else
                                                class="w-2.5 h-2.5 text-slate-400 dark:text-slate-500 fill-current"
                                                viewBox="0 0 24 24"
                                            >
                                                <path d="M10 17l5-5-5-5z" />
                                            </svg>
                                        </template>
                                        <template v-else>
                                            <span
                                                class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover:bg-slate-400"
                                            ></span>
                                        </template>
                                    </span>
                                    <span
                                        class="whitespace-normal flex-1 text-[11px]"
                                        :title="child.label"
                                        >{{ child.label }}</span
                                    >
                                </div>

                                <!-- Grandchildren List (SOPs) -->
                                <div
                                    v-if="
                                        child.children &&
                                        child.children.length > 0 &&
                                        expandedNodes[child.id]
                                    "
                                    class="mt-0.5"
                                >
                                    <div
                                        v-for="gchild in child.children"
                                        :key="gchild.id"
                                        class="flex items-center py-1.5 px-2 rounded cursor-pointer hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group select-none"
                                        :class="[
                                            activeTab === gchild.targetTab &&
                                            activeSubId === gchild.targetId
                                                ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                                                : 'text-slate-500 dark:text-slate-500',
                                        ]"
                                        :style="{
                                            paddingLeft: `${gchild.level * 10 + 12}px`,
                                        }"
                                        @click="handleNodeClick(gchild)"
                                    >
                                        <span
                                            class="w-4 h-4 flex items-center justify-center mr-1 text-slate-400"
                                        >
                                            <span
                                                class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover:bg-slate-400"
                                            ></span>
                                        </span>
                                        <span
                                            class="whitespace-normal flex-1 text-[10px]"
                                            :title="gchild.label"
                                            >{{ gchild.label }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PAGES TAB -->
                <div
                    v-show="activeNavTab === 'pages'"
                    class="grid grid-cols-2 gap-2 p-1"
                >
                    <button
                        v-for="(sec, idx) in allSections"
                        :key="sec.id"
                        @click="selectTab(sec.id)"
                        class="flex flex-col items-center justify-between p-2 h-24 border rounded-lg text-center transition-all bg-slate-50/50 dark:bg-[#1a1a1a]"
                        :class="[
                            activeTab === sec.id
                                ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-50/10'
                                : 'border-slate-200 dark:border-white/5 hover:border-slate-300 dark:hover:border-white/10',
                        ]"
                    >
                        <div
                            class="flex-1 flex items-center justify-center overflow-hidden"
                        >
                            <span
                                class="text-[9px] font-medium text-slate-700 dark:text-slate-300 leading-tight"
                            >
                                {{ sec.labelShort || sec.label }}
                            </span>
                        </div>
                        <div
                            class="mt-1 text-[8px] text-slate-400 font-semibold uppercase tracking-wider"
                        >
                            Page {{ idx + 1 }}
                        </div>
                    </button>
                </div>

                <!-- 3. RESULTS TAB -->
                <div
                    v-show="activeNavTab === 'results'"
                    class="space-y-1"
                >
                    <div
                        v-if="searchQuery.trim() === ''"
                        class="text-center py-8 text-slate-400 dark:text-slate-500 text-[11px]"
                    >
                        Masukkan kata kunci untuk mencari dokumen.
                    </div>
                    <div
                        v-else-if="searchMatches.length === 0"
                        class="text-center py-8 text-slate-400 dark:text-slate-500 text-[11px]"
                    >
                        Tidak ada hasil untuk "{{ searchQuery }}".
                    </div>
                    <div v-else class="space-y-1.5">
                        <button
                            v-for="(match, idx) in searchMatches"
                            :key="idx"
                            @click="goToMatch(match)"
                            class="w-full text-left p-2 hover:bg-slate-100 dark:hover:bg-white/5 rounded border border-slate-100 dark:border-white/5 transition-colors block"
                        >
                            <div
                                class="text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider whitespace-normal"
                            >
                                {{ match.sectionName }}
                            </div>
                            <div
                                class="text-[11px] text-slate-700 dark:text-slate-300 font-semibold whitespace-normal mt-0.5"
                            >
                                {{ match.title }}
                            </div>
                                <div
                                    class="text-[10px] text-slate-500 dark:text-slate-400 italic mt-0.5 leading-relaxed"
                                    v-html="highlightText(match.preview, searchQuery)"
                                ></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed, onMounted, watch, defineComponent, h } from "vue";
import { router } from "@inertiajs/vue3";

// Recursive tree node component for regulation hierarchy
const RegTreeNode = defineComponent({
    name: 'RegTreeNode',
    props: {
        node: { type: Object, required: true },
        depth: { type: Number, default: 0 },
        activeRegulationId: { type: Number, default: null },
        expandedRegNodes: { type: Object, default: () => ({}) },
    },
    emits: ['toggle', 'navigate'],
    setup(props, { emit }) {
        const isActive = computed(() => props.node.id === props.activeRegulationId);
        const isExpanded = computed(() => !!props.expandedRegNodes[props.node.id]);
        const hasChildren = computed(() => props.node.children && props.node.children.length > 0);

        return () => {
            const children = [];

            // The row itself
            children.push(
                h('div', {
                    class: [
                        'flex items-center py-1.5 px-2 rounded cursor-pointer transition-colors group select-none text-[11px]',
                        isActive.value
                            ? 'bg-blue-50/70 text-blue-900 font-semibold dark:bg-blue-950/20 dark:text-blue-200'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5',
                    ],
                    style: { paddingLeft: `${props.depth * 12 + 6}px` },
                    onClick: () => emit('navigate', props.node.id),
                }, [
                    // Toggle icon
                    hasChildren.value
                        ? h('span', {
                            class: 'w-4 h-4 flex items-center justify-center mr-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded shrink-0',
                            onClick: (e) => { e.stopPropagation(); emit('toggle', props.node.id); },
                        }, [
                            h('svg', {
                                class: ['w-2.5 h-2.5 fill-current transition-transform duration-150', isExpanded.value ? '' : '-rotate-90'],
                                viewBox: '0 0 24 24',
                            }, [h('path', { d: 'M7 10l5 5 5-5z' })])
                        ])
                        : h('span', {
                            class: 'w-4 h-4 flex items-center justify-center mr-1 shrink-0',
                        }, [
                            h('span', { class: 'w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700' })
                        ]),
                    // Tipe badge
                    props.node.tipe
                        ? h('span', {
                            class: [
                                'shrink-0 mr-1.5 rounded px-1 py-0 text-[8px] font-bold uppercase leading-tight',
                                props.node.tipe === 'Policy'
                                    ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400'
                                    : props.node.tipe === 'Procedure'
                                        ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400'
                                        : props.node.tipe === 'Standart'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400'
                                            : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-400',
                            ],
                        }, props.node.tipe.substring(0, 3).toUpperCase())
                        : null,
                    // Label
                    h('span', {
                        class: ['whitespace-normal flex-1 leading-tight', isActive.value ? 'font-semibold' : 'font-medium'],
                        title: props.node.judul,
                    }, props.node.judul || 'Untitled'),
                ])
            );

            // Recursively render children
            if (hasChildren.value && isExpanded.value) {
                props.node.children.forEach(child => {
                    children.push(
                        h(RegTreeNode, {
                            node: child,
                            depth: props.depth + 1,
                            activeRegulationId: props.activeRegulationId,
                            expandedRegNodes: props.expandedRegNodes,
                            onToggle: (id) => emit('toggle', id),
                            onNavigate: (id) => emit('navigate', id),
                        })
                    );
                });
            }

            return h('div', null, children);
        };
    },
});

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tkoSections: {
        type: Array,
        default: () => [],
    },
    allSections: {
        type: Array,
        default: () => [],
    },
    activeTab: {
        type: String,
        required: true,
    },
    activeSubId: {
        type: [String, Number],
        default: null,
    },
    isHeaderVisible: {
        type: Boolean,
        default: undefined,
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    activeRegulationId: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(["update:activeTab", "update:activeSubId", "update:isHeaderVisible"]);

const searchQuery = ref("");
const activeNavTab = ref("headings");
const expandedNodes = ref({});

// ── Internal Header Visibility State ──
// Jika parent tidak mengirim prop isHeaderVisible (undefined), kelola secara internal
// Jika parent mengirim prop, sync dengan parent via v-model
const internalHeaderVisible = ref(true);
const effectiveHeaderVisible = computed(() => {
    if (props.isHeaderVisible !== undefined) {
        return props.isHeaderVisible;
    }
    return internalHeaderVisible.value;
});
function toggleHeaderVisibility() {
    const newVal = !effectiveHeaderVisible.value;
    if (props.isHeaderVisible !== undefined) {
        emit('update:isHeaderVisible', newVal);
    } else {
        internalHeaderVisible.value = newVal;
    }
}

// ── Related Regulations Tree State ──
const isRelatedDocsExpanded = ref(true);
const expandedRegNodes = ref({});

const initExpandedNodes = () => {
    props.allSections.forEach((sec) => {
        if (expandedNodes.value[sec.id] === undefined) {
            expandedNodes.value[sec.id] = true;
        }
    });
    props.categories.forEach((cat) => {
        if (expandedNodes.value[`category_${cat.id}`] === undefined) {
            expandedNodes.value[`category_${cat.id}`] = true;
        }
    });
    expandedNodes.value["category_uncategorized"] = true;
};

// ── Related Regulations Tree ──
// Hanya menampilkan tree dari regulasi yang terkait dengan activeRegulationId
// melalui relasi parent/child (seluruh hirarki)
const relatedRegulationTree = computed(() => {
    if (!props.activeRegulationId || props.regulations.length === 0) return [];

    // 1. Kumpulkan semua ID dalam hirarki (ancestors + descendants) dari activeRegulation
    const allRegs = props.regulations;
    const relatedIds = new Set();

    // Traverse ke atas (ancestors)
    let currentId = props.activeRegulationId;
    while (currentId) {
        relatedIds.add(currentId);
        const reg = allRegs.find(r => r.id === currentId);
        if (reg?.parent_id) {
            currentId = reg.parent_id;
        } else {
            break;
        }
    }

    // Traverse ke bawah (descendants) dari setiap node yang sudah dikumpulkan
    const collectDescendants = (parentId) => {
        allRegs.forEach(reg => {
            if (reg.parent_id === parentId && !relatedIds.has(reg.id)) {
                relatedIds.add(reg.id);
                collectDescendants(reg.id);
            }
        });
    };
    // Collect descendants dari semua node yang sudah ada di relatedIds
    [...relatedIds].forEach(id => collectDescendants(id));

    // 2. Filter hanya regulasi yang termasuk dalam hirarki
    const filteredRegs = allRegs.filter(reg => relatedIds.has(reg.id));

    // 3. Build tree dari filteredRegs
    const map = {};
    const roots = [];

    filteredRegs.forEach(reg => {
        map[reg.id] = {
            id: reg.id,
            judul: reg.judul,
            tipe: reg.tipe,
            nomor: reg.nomor,
            children: [],
        };
    });

    filteredRegs.forEach(reg => {
        const node = map[reg.id];
        if (reg.parent_id && map[reg.parent_id]) {
            map[reg.parent_id].children.push(node);
        } else {
            roots.push(node);
        }
    });

    return roots;
});

const flatRegulationCount = computed(() => {
    let count = 0;
    const countNodes = (nodes) => {
        nodes.forEach(node => {
            count++;
            if (node.children && node.children.length > 0) {
                countNodes(node.children);
            }
        });
    };
    countNodes(relatedRegulationTree.value);
    return count;
});

function toggleRegNode(id) {
    expandedRegNodes.value = {
        ...expandedRegNodes.value,
        [id]: !expandedRegNodes.value[id],
    };
}

function handleRegNavigate(id) {
    const reg = props.regulations.find(r => r.id === id);
    if (!reg) return;

    const isProcedure = String(reg.tipe || "").toLowerCase() === "procedure";
    const routeName = isProcedure
        ? "itom.policy.regulation.procedure.index"
        : "itom.policy.general.index";

    router.visit(route(routeName, { regulation_id: reg.id }));
}

const initExpandedRegNodes = () => {
    const expanded = {};
    const expandParents = (nodes) => {
        nodes.forEach(node => {
            if (node.children && node.children.length > 0) {
                expanded[node.id] = true;
                expandParents(node.children);
            }
        });
    };
    expandParents(relatedRegulationTree.value);
    expandedRegNodes.value = expanded;
};

onMounted(() => {
    initExpandedNodes();
    initExpandedRegNodes();
});

watch(
    () => [props.allSections, props.categories],
    () => {
        initExpandedNodes();
    },
    { deep: true }
);

watch(
    () => props.regulations,
    () => {
        initExpandedRegNodes();
    },
    { deep: true }
);

const headingTree = computed(() => {
    const list = [];
    props.allSections.forEach((sec) => {
        const node = {
            id: sec.id,
            label: sec.label,
            level: 0,
            type: "section",
            targetTab: sec.id,
            children: [],
        };

        if (sec.id === "fungsi") {
            (props.actors || []).forEach((actor) => {
                node.children.push({
                    id: `actor_${actor.id}`,
                    label: actor.name,
                    level: 1,
                    type: "actor",
                    targetTab: "fungsi",
                    targetId: actor.id,
                });
            });
        } else if (sec.id === "prosedur") {
            const catMap = {};
            (props.categories || []).forEach((cat) => {
                catMap[cat.id] = {
                    id: `category_${cat.id}`,
                    label: cat.tipe,
                    level: 1,
                    type: "category",
                    targetTab: "prosedur",
                    targetId: `category_${cat.id}`,
                    children: [],
                };
            });

            const uncategorized = {
                id: "category_uncategorized",
                label: "LAIN-LAIN",
                level: 1,
                type: "category",
                targetTab: "prosedur",
                targetId: "category_uncategorized",
                children: [],
            };

            const categoryCounters = {};
            (props.sop || []).forEach((s) => {
                const catId = s.category_id || "uncategorized";
                if (!categoryCounters[catId]) {
                    categoryCounters[catId] = 0;
                }
                categoryCounters[catId]++;
                const count = categoryCounters[catId];

                const item = {
                    id: `sop_${s.id}`,
                    label: s.name || s.judul || `Aktifitas ${count}`,
                    level: 2,
                    type: "sop",
                    targetTab: "prosedur",
                    targetId: s.id,
                };
                if (s.category_id && catMap[s.category_id]) {
                    catMap[s.category_id].children.push(item);
                } else {
                    uncategorized.children.push(item);
                }
            });

            Object.values(catMap).forEach((catNode) => {
                node.children.push(catNode);
            });
            if (uncategorized.children.length > 0) {
                node.children.push(uncategorized);
            }
        } else if (sec.children && sec.children.length > 0) {
            sec.children.forEach((child) => {
                node.children.push({
                    id: child.id,
                    label: child.label,
                    level: 1,
                    type: "sub-section",
                    targetTab: child.targetTab,
                    targetId: child.targetId || null,
                });
            });
        }

        list.push(node);
    });

    return list;
});

const filteredHeadingTree = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return headingTree.value;

    const filtered = [];
    headingTree.value.forEach((node) => {
        const labelMatches = node.label.toLowerCase().includes(query);
        const matchingChildren = [];

        if (node.children) {
            node.children.forEach((child) => {
                const childMatches = child.label.toLowerCase().includes(query);
                const matchingGrandchildren = [];

                if (child.children) {
                    child.children.forEach((gchild) => {
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
            matchingChildren.forEach((c) => {
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
    (props.tkoSections || []).forEach((sec) => {
        const romanNumerals = {
            1: "I",
            2: "II",
            3: "III",
            4: "IV",
            5: "V",
            6: "VI",
            7: "VII",
            8: "VIII",
            9: "IX",
            10: "X",
        };
        const sectionLabel = `${romanNumerals[sec.order] || sec.order}. ${sec.name.toUpperCase()}`;
        const isReference = sec.name.trim().toLowerCase() === 'referensi';

        if (sec.name.toLowerCase().includes(query)) {
            matches.push({
                sectionId: isReference ? 'reference' : `tko_${sec.id}`,
                sectionName: sectionLabel,
                title: sec.name,
                preview: isReference ? "Daftar referensi regulasi" : (sec.contents?.[0]?.content || "Dokumen kosong"),
                targetTab: isReference ? 'reference' : `tko_${sec.id}`,
            });
        } else if (!isReference) {
            const content = sec.contents?.[0]?.content || "";
            const idx = content.toLowerCase().indexOf(query);
            if (idx > -1) {
                const start = Math.max(0, idx - 30);
                const end = Math.min(content.length, idx + query.length + 50);
                let preview = content.substring(start, end);
                if (start > 0) preview = "..." + preview;
                if (end < content.length) preview = preview + "...";
                matches.push({
                    sectionId: `tko_${sec.id}`,
                    sectionName: sectionLabel,
                    title: sec.name,
                    preview: preview,
                    targetTab: `tko_${sec.id}`,
                });
            }
        }
    });

    // Search in Fungsi/Actors
    (props.actors || []).forEach((actor) => {
        if (actor.name.toLowerCase().includes(query)) {
            matches.push({
                sectionId: "fungsi",
                sectionName: "IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT",
                title: actor.name,
                preview: `Tipe peran: ${actor.tipe || "Fungsi"}`,
                targetTab: "fungsi",
                targetId: actor.id,
            });
        }
    });

    // Search in SOP/Prosedur
    (props.sop || []).forEach((s) => {
        const name = s.name || s.judul || "";
        if (name.toLowerCase().includes(query)) {
            matches.push({
                sectionId: "prosedur",
                sectionName: "V. PROSEDUR",
                title: name,
                preview: s.description || "Lihat langkah-langkah prosedur",
                targetTab: "prosedur",
                targetId: s.id,
            });
        }
    });

    return matches;
});

function toggleNodeExpand(nodeId) {
    expandedNodes.value[nodeId] = !expandedNodes.value[nodeId];
}

function handleNodeClick(node) {
    emit("update:activeTab", node.targetTab);
    if (node.targetId) {
        emit("update:activeSubId", node.targetId);
        scrollToElement(node.targetId);
    } else {
        emit("update:activeSubId", null);
    }
}

function scrollToElement(targetId) {
    setTimeout(() => {
        const cleanId = String(targetId).startsWith("category_")
            ? String(targetId).replace("category_", "")
            : targetId;
        const el =
            document.getElementById(`actor-row-${cleanId}`) ||
            document.getElementById(`sop-row-${cleanId}`) ||
            document.getElementById(`category-row-${cleanId}`);
        if (el) {
            el.scrollIntoView({ behavior: "smooth", block: "center" });
            el.classList.add("bg-blue-500/10", "dark:bg-blue-500/20");
            setTimeout(() => {
                el.classList.remove("bg-blue-500/10", "dark:bg-blue-500/20");
            }, 3000);
        }
    }, 400);
}

function goToMatch(match) {
    emit("update:activeTab", match.targetTab);
    if (match.targetId) {
        emit("update:activeSubId", match.targetId);
        scrollToElement(match.targetId);
    } else {
        emit("update:activeSubId", null);
    }
}

function selectTab(secId) {
    emit("update:activeTab", secId);
    emit("update:activeSubId", null);
}

function highlightText(text, query) {
    if (!text || !query) return text || "";
    const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
    const regex = new RegExp(`(${escapedQuery})`, "gi");
    return text.replace(
        regex,
        '<span class="bg-yellow-100 text-slate-900 px-0.5 rounded font-bold dark:bg-yellow-500/30 dark:text-yellow-100">$1</span>'
    );
}
</script>