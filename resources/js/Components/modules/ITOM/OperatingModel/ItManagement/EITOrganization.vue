<template>
    <template v-if="isRoot">
        <section
            class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="border-b border-slate-200 px-3 py-2 dark:border-white/10 flex items-center gap-2">
                <h2 class="text-xs font-semibold uppercase text-slate-800 dark:text-white">Organisasi Fungsi TI
                </h2>

                <div class="flex items-center gap-1.5 ml-4 border-l border-slate-200 dark:border-white/10 pl-4">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-[9px] text-slate-500 dark:text-slate-400 font-medium select-none">
                        <input
                            type="checkbox"
                            v-model="showPejabatLocal"
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5 dark:border-white/10 dark:bg-[#1a1a1a]"
                        />
                        Pejabat
                    </label>
                </div>

                <div class="flex items-center gap-1.5 ml-4">
                    <select
                        :value="maxExpandDepthLocal !== null ? maxExpandDepthLocal : ''"
                        @change="handleDepthChange"
                        class="rounded border border-slate-300 bg-white px-2 py-0.5 text-[9px] text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white font-medium"
                    >
                        <option value="">-- Level --</option>
                        <option value="0">Level 0 (Hanya Root)</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                        <option value="4">Level 4</option>
                        <option value="5">Level 5</option>
                        <option value="99">Expand Semua</option>
                    </select>
                </div>
            </div>

            <div class="px-2 py-3">
                <div v-if="organizationTree.length === 0"
                    class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
                    Data organisasi tidak ditemukan.
                </div>

                <div v-else class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-3">
                    <ThreeView v-for="item in organizationTree" :key="item.organization_id" :node="item" :is-root="false"
                        :depth="0"
                        :max-expand-depth="maxExpandDepthLocal"
                        :show-pejabat="showPejabatLocal" />
                </div>
            </div>
        </section>
    </template>

    <!-- Non-root: semua depth gunakan branching horizontal -->

    <div v-else class="relative min-w-0 shrink-0">
        <!-- Horizontal line antar-sibling (hanya area branch horizontal, skip fungsi) -->
        <div v-if="!isWakilStack && depth >= 1 && depth <= 7 && !isVerticalLayoutChild && (!isFirstChild || !isLastChild) && !(node?.role_function && node.role_function.toLowerCase() === 'fungsi')"
            class="absolute top-[-8px] h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-1' : '',
                isLastChild ? '-left-1 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-1 -right-1' : '',
            ]" aria-hidden="true" />
        <!-- Vertical line dari atas ke node (kecuali fungsi) -->
        <div v-if="depth >= 1 && depth <= 7 && !isVerticalLayoutChild && !(node?.role_function && node.role_function.toLowerCase() === 'fungsi')"
            class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
            aria-hidden="true" />

        <div class="flex w-full min-w-0 flex-col items-center">
            <!-- Node content + children wrapper -->
            <div class="flex flex-col min-w-0 items-center w-full">
                <!-- The node box -->
                <div
                    class="relative flex flex-col items-center justify-center rounded border px-1 text-center font-semibold leading-tight shadow-sm transition duration-200"
                    :class="[
                        nodeSizeClass,
                        nodeToneClass,
                        isClickable ? 'cursor-pointer hover:border-blue-400 dark:hover:border-blue-400 hover:shadow-md hover:ring-1 hover:ring-blue-200 dark:hover:ring-blue-800' : 'cursor-default',
                    ]"
                    :title="nodeTitle"
                    @click="handleNodeClick"
                >
                    <span class="block max-w-full break-words whitespace-normal">
                        {{ node.organization_name }}
                    </span>
                    <!-- Alias -->
                    <span v-if="node.alias" class="block max-w-full break-words whitespace-normal text-[7px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                        ({{ node.alias }})
                    </span>
                    <!-- Pejabat -->
                    <span v-if="showPejabat && node.pejabat" class="block max-w-full break-words whitespace-normal text-[7px] font-normal mt-0.5 opacity-75">
                        {{ node.pejabat }}
                    </span>

                    <!-- Expand/collapse indicator for nodes with children at depth 5 -->
                    <span v-if="hasChildren && depth === 5"
                        class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 flex items-center justify-center w-3 h-3 rounded-full text-[6px] font-bold leading-none transition-colors duration-200"
                        :class="isExpanded
                            ? 'bg-blue-500 text-white'
                            : 'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-300'"
                        aria-hidden="true"
                    >
                        {{ isExpanded ? '−' : '+' }}
                    </span>
                </div>

                <!-- Fungsi nodes: branch ke kanan dari garis vertikal connector (role_function = 'fungsi') -->
                <template v-if="combinedFungsi.length > 0 && isExpanded">
                    <div
                        v-for="fungsi in combinedFungsi"
                        :key="'fungsi-' + fungsi.organization_id"
                        class="relative"
                        style="width: 0; overflow: visible;"
                    >
                        <!-- Garis vertikal komando utama -->
                        <div
                            class="absolute top-0 bottom-0 bg-slate-300 dark:bg-white/20"
                            style="left: 50%; transform: translateX(-50%); width: 1px;"
                            aria-hidden="true"
                        />
                        <!-- Garis horizontal + node fungsi -->
                        <div class="flex items-center py-1" style="white-space: nowrap;">
                            <div class="bg-slate-300 dark:bg-white/20 flex-shrink-0" style="width: 60px; height: 1px;" aria-hidden="true" />
                            <ThreeView
                                :node="fungsi"
                                :is-root="false"
                                :depth="depth + 1"
                                :max-expand-depth="maxExpandDepth"
                                :show-pejabat="showPejabat"
                            />
                        </div>
                    </div>
                </template>

                <!-- Children (vertical wakil stack) -->
                <template v-if="combinedWakil.length > 0 && isExpanded">
                    <div class="w-px bg-slate-300 dark:bg-white/20" style="height: 8px;" aria-hidden="true" />
                    <ThreeView
                        :node="wakilChildToRender"
                        :extra-children="remainingChildrenToPushDown"
                        :is-wakil-stack="true"
                        :is-root="false"
                        :depth="depth + 1"
                        :max-expand-depth="maxExpandDepth"
                        :show-pejabat="showPejabat"
                    />
                </template>

                <!-- Children: default horizontal branching (hanya jika tidak ada wakil) -->
                <div v-if="combinedWakil.length === 0 && hasChildren && isExpanded && !shouldRenderChildrenVertical" class="relative mt-2 w-full min-w-0 pt-2">
                    <div class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
                        aria-hidden="true" />

                    <div class="flex flex-row justify-center items-start gap-x-2 gap-y-3 w-full min-w-0 flex-wrap">
                        <ThreeView v-for="(child, index) in combinedNormal" :key="child.organization_id" :node="child"
                            :is-root="false" :depth="depth + 1" :is-first-child="index === 0"
                            :is-last-child="index === combinedNormal.length - 1"
                            :max-expand-depth="maxExpandDepth"
                            :show-pejabat="showPejabat" />
                    </div>
                </div>

                <!-- Children: level paling akhir disusun vertikal (hanya jika tidak ada wakil) -->
                <div v-if="combinedWakil.length === 0 && hasChildren && isExpanded && shouldRenderChildrenVertical" class="relative mt-3 w-full">
                    <div class="absolute left-1/2 top-[-8px] h-full w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
                        aria-hidden="true" />

                    <div class="relative flex flex-col items-center gap-y-2 pt-1">
                        <ThreeView v-for="(child, index) in combinedNormal" :key="child.organization_id" :node="child"
                            :is-root="false" :depth="depth + 1" :is-first-child="index === 0"
                            :is-last-child="index === combinedNormal.length - 1" :is-vertical-layout-child="true"
                            :max-expand-depth="maxExpandDepth"
                            :show-pejabat="showPejabat" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

defineOptions({
    name: 'ThreeView',
});

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
    node: {
        type: Object,
        default: null,
    },
    isRoot: {
        type: Boolean,
        default: true,
    },
    depth: {
        type: Number,
        default: 0,
    },
    isFirstChild: {
        type: Boolean,
        default: false,
    },
    isLastChild: {
        type: Boolean,
        default: false,
    },
    isVerticalLayoutChild: {
        type: Boolean,
        default: false,
    },
    extraChildren: {
        type: Array,
        default: () => [],
    },
    isWakilStack: {
        type: Boolean,
        default: false,
    },
    maxExpandDepth: {
        type: Number,
        default: null,
    },
    showPejabat: {
        type: Boolean,
        default: false,
    },
});

const showPejabatLocal = ref(false);

// Depth 0-4, 6+ auto-expand, depth 5 collapsed by default (show/hide children on click)
const isExpanded = ref(props.depth !== 5);

const maxExpandDepthLocal = ref(null);

const handleDepthChange = (event) => {
    const val = event.target.value;
    if (val === '') {
        maxExpandDepthLocal.value = null;
        return;
    }
    maxExpandDepthLocal.value = Number(val);
};

watch(() => props.maxExpandDepth, (newVal) => {
    if (newVal !== null && newVal !== undefined) {
        isExpanded.value = props.depth < newVal;
    } else {
        isExpanded.value = props.depth !== 5;
    }
});

// =====================
// Node-level computed
// =====================

/** Wakil children: role_function === 'wakil' */
const combinedWakil = computed(() => {
    if (!props.node || !Array.isArray(props.node.children)) return [];
    const directWakil = props.node.children.filter(b => b.role_function && b.role_function.toLowerCase() === 'wakil');
    const extraWakil = (props.extraChildren || []).filter(b => b.role_function && b.role_function.toLowerCase() === 'wakil');
    return [...directWakil, ...extraWakil];
});

/** Fungsi children: role_function === 'fungsi' — branch ke kanan */
const combinedFungsi = computed(() => {
    if (!props.node || !Array.isArray(props.node.children)) return [];
    return props.node.children.filter(b => b.role_function && b.role_function.toLowerCase() === 'fungsi');
});

/** Normal children: bukan wakil, bukan fungsi */
const combinedNormal = computed(() => {
    if (!props.node || !Array.isArray(props.node.children)) return [];
    const directNormal = props.node.children.filter(b => !b.role_function || (b.role_function.toLowerCase() !== 'wakil' && b.role_function.toLowerCase() !== 'fungsi'));
    const extraNormal = (props.extraChildren || []).filter(b => !b.role_function || (b.role_function.toLowerCase() !== 'wakil' && b.role_function.toLowerCase() !== 'fungsi'));
    return [...directNormal, ...extraNormal];
});

const hasChildren = computed(() => {
    return combinedWakil.value.length > 0 || combinedNormal.value.length > 0 || combinedFungsi.value.length > 0;
});

const shouldRenderChildrenVertical = computed(() => {
    if (combinedNormal.value.length === 0) {
        return false;
    }

    return combinedNormal.value.every((child) => !Array.isArray(child?.children) || child.children.length === 0);
});

const isClickable = computed(() => hasChildren.value && props.depth === 5);

/** Wakil pertama untuk vertical stack */
const wakilChildToRender = computed(() => combinedWakil.value[0]);

/** Sisa wakil + semua normal, di-push ke bawah sebagai extraChildren */
const remainingChildrenToPushDown = computed(() => {
    return [
        ...combinedWakil.value.slice(1),
        ...combinedNormal.value,
    ];
});

const nodeTitle = computed(() => {
    if (hasChildren.value && props.depth === 5) {
        return `${props.node?.organization_name} (Klik untuk ${isExpanded.value ? 'menyembunyikan' : 'menampilkan'} anak organisasi)`;
    }
    const parts = [props.node?.organization_name ?? ''];
    if (props.node?.alias) parts[0] += ` (${props.node.alias})`;
    if (props.node?.pejabat) parts.push(props.node.pejabat);
    return parts.filter(Boolean).join(' · ');
});

const handleNodeClick = () => {
    if (hasChildren.value && props.depth === 5) {
        isExpanded.value = !isExpanded.value;
    }
};

// =====================
// Root-level: build hierarchy
// =====================

const buildParentIdHierarchy = (items) => {
    if (!items.length) {
        return [];
    }

    const sorted = [...items]
        .map((item) => ({
            ...item,
            children: [],
        }))
        .sort((a, b) => {
            if (a.order !== null && b.order !== null && a.order !== undefined && b.order !== undefined) {
                return a.order - b.order;
            }
            return String(a.organization_name).localeCompare(String(b.organization_name));
        });

    const nodeById = new Map();
    sorted.forEach((item) => {
        nodeById.set(item.organization_id, item);
    });

    const roots = [];
    sorted.forEach((item) => {
        const parentId = item.parent_id;
        if (parentId) {
            const parentNode = nodeById.get(parentId);
            if (parentNode) {
                parentNode.children.push(item);
            } else {
                roots.push(item);
            }
        } else {
            roots.push(item);
        }
    });

    return roots;
};

const buildFullHierarchy = (items) => {
    if (!items.length) {
        return [];
    }

    const companyMap = new Map();

    items.forEach((item) => {
        const companyKey = item.company_id ?? item.company_name ?? 'company';
        const groupKey = item.groub_id ?? item.groub_name ?? 'group';

        if (!companyMap.has(companyKey)) {
            companyMap.set(companyKey, {
                companyName: item.company_name || 'Tanpa Holding',
                groups: new Map(),
            });
        }

        const companyNode = companyMap.get(companyKey);

        if (!companyNode.groups.has(groupKey)) {
            companyNode.groups.set(groupKey, {
                groupName: item.groub_name || 'Tanpa Sub Holding',
                items: [],
            });
        }

        companyNode.groups.get(groupKey).items.push(item);
    });

    return Array.from(companyMap.entries())
        .map(([companyKey, companyNode]) => ({
            organization_id: `company-${companyKey}`,
            organization_name: companyNode.companyName,
            type: 'holding',
            children: Array.from(companyNode.groups.entries())
                .map(([groupKey, groupNode]) => ({
                    organization_id: `group-${companyKey}-${groupKey}`,
                    organization_name: groupNode.groupName,
                    type: 'sub_holding',
                    children: buildParentIdHierarchy(groupNode.items),
                }))
                .sort((left, right) => left.organization_name.localeCompare(right.organization_name)),
        }))
        .sort((left, right) => left.organization_name.localeCompare(right.organization_name));
};

const organizationTree = computed(() => buildFullHierarchy(props.organizationStructureRows));

const nodeSizeClass = computed(() => {
    if (props.node?.type === 'holding' || props.node?.type === 'sub_holding') {
        return 'h-8 w-28 text-[10px]';
    }

    return 'min-h-[1.5rem] w-20 text-[8px] py-1';
});

const nodeToneClass = computed(() => {
    // Wakil: blue accent
    if (props.node?.role_function && props.node.role_function.toLowerCase() === 'wakil') {
        return 'bg-slate-50 text-slate-900 border-blue-400 dark:bg-slate-50 dark:text-slate-900 dark:border-blue-400';
    }
    // Fungsi: violet accent
    if (props.node?.role_function && props.node.role_function.toLowerCase() === 'fungsi') {
        return 'bg-violet-50 text-violet-900 border-violet-400 dark:bg-violet-50 dark:text-violet-900 dark:border-violet-400';
    }
    return 'bg-white text-slate-900 border-slate-300 dark:bg-white dark:text-slate-900 dark:border-slate-300';
});

const toggleExpand = () => {
    if (hasChildren.value) {
        isExpanded.value = !isExpanded.value;
    }
};
</script>
