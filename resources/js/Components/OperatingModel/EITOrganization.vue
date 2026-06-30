<template>
    <template v-if="isRoot">
        <section
            class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="border-b border-slate-200 px-3 py-2 dark:border-white/10">
                <h2 class="text-xs font-semibold uppercase text-slate-800 dark:text-white">Struktur Organisasi EIT
                </h2>
            </div>

            <div class="px-2 py-3">
                <div v-if="organizationTree.length === 0"
                    class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
                    Data organisasi tidak ditemukan.
                </div>

                <div v-else class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-3">
                    <ThreeView v-for="item in organizationTree" :key="item.organization_id" :node="item" :is-root="false"
                        :depth="0" />
                </div>
            </div>
        </section>
    </template>

    <!-- Non-root: semua depth gunakan branching horizontal -->

    <div v-else class="relative min-w-0 shrink-0">
        <!-- Horizontal line antar-sibling -->
        <div v-if="depth >= 1 && (!isFirstChild || !isLastChild)"
            class="absolute top-[-8px] h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-1' : '',
                isLastChild ? '-left-1 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-1 -right-1' : '',
            ]" aria-hidden="true" />
        <!-- Vertical line dari atas ke node -->
        <div v-if="depth >= 1"
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

                <!-- Children: depth < 7 use horizontal flex layout for their children -->
                <div v-if="hasChildren && isExpanded && depth < 7" class="relative mt-2 w-full min-w-0 pt-2">
                    <div class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
                        aria-hidden="true" />

                    <div class="flex flex-row justify-center items-start gap-x-2 gap-y-3 w-full min-w-0 flex-wrap">
                        <ThreeView v-for="(child, index) in node.children" :key="child.organization_id" :node="child"
                            :is-root="false" :depth="depth + 1" :is-first-child="index === 0"
                            :is-last-child="index === node.children.length - 1" />
                    </div>
                </div>

                <!-- Children: depth >= 7 tetap horizontal agar node akhir tidak saling mengganggu -->
                <div v-if="hasChildren && isExpanded && depth >= 7" class="relative mt-2 min-w-max pt-2">
                    <div class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
                        aria-hidden="true" />

                    <div class="flex flex-row justify-center items-start gap-x-2 gap-y-3 min-w-max">
                        <ThreeView v-for="(child, index) in node.children" :key="child.organization_id" :node="child"
                            :is-root="false" :depth="depth + 1" :is-first-child="index === 0"
                            :is-last-child="index === node.children.length - 1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

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
});

// Depth 0-4, 6+ auto-expand, depth 5 collapsed by default (show/hide children on click)
const isExpanded = ref(props.depth !== 5);

const hasChildren = computed(() => Array.isArray(props.node?.children) && props.node.children.length > 0);

const isClickable = computed(() => hasChildren.value && props.depth === 5);

const nodeTitle = computed(() => {
    if (hasChildren.value && props.depth === 5) {
        return `${props.node?.organization_name} (Klik untuk ${isExpanded.value ? 'menyembunyikan' : 'menampilkan'} anak organisasi)`;
    }
    return props.node?.organization_name ?? '';
});

const handleNodeClick = () => {
    if (hasChildren.value && props.depth === 5) {
        isExpanded.value = !isExpanded.value;
    }
};



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
    return 'bg-white text-slate-900 border-slate-300 dark:bg-white dark:text-slate-900 dark:border-slate-300';
});

const childGridStyle = computed(() => {
    return {
        gridTemplateColumns: 'repeat(auto-fit, minmax(5rem, 1fr))',
    };
});

const toggleExpand = () => {
    if (hasChildren.value) {
        isExpanded.value = !isExpanded.value;
    }
};
</script>
