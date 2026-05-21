<template>
    <template v-if="isRoot">
        <section
            class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="border-b border-slate-200 px-3 py-2 dark:border-white/10">
                <h2 class="text-xs font-semibold uppercase text-slate-800 dark:text-white">Struktur Organisasi Holding
                </h2>
            </div>

            <div class="px-2 py-3">
                <div v-if="holdingTree.length === 0"
                    class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
                    Data holding tidak ditemukan.
                </div>

                <div v-else class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-3">
                    <ThreeView v-for="item in holdingTree" :key="item.organization_id" :node="item" :is-root="false"
                        :depth="0" />
                </div>
            </div>
        </section>

        <section
            class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="border-b border-slate-200 px-3 py-2 dark:border-white/10">
                <h2 class="text-xs font-semibold uppercase text-slate-800 dark:text-white">Struktur Organisasi Sub
                    Holding</h2>
            </div>

            <div class="px-2 py-3">
                <div v-if="subHoldingTree.length === 0"
                    class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
                    Data sub holding tidak ditemukan.
                </div>

                <div v-else class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-3">
                    <ThreeView v-for="item in subHoldingTree" :key="item.organization_id" :node="item" :is-root="false"
                        :depth="0" />
                </div>
            </div>
        </section>
    </template>

    <div v-else class="relative w-full min-w-0">
        <!-- Horizontal line -->
        <div v-if="depth > 0 && (!isFirstChild || !isLastChild)"
            class="absolute top-[-8px] h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-1' : '',
                isLastChild ? '-left-1 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-1 -right-1' : '',
            ]" aria-hidden="true" />
        <!-- Vertical line going up -->
        <div v-if="depth > 0"
            class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
            aria-hidden="true" />

        <div class="flex w-full min-w-0 flex-col items-center">
            <div
                class="relative flex flex-col items-center justify-center rounded border px-1 text-center font-semibold leading-tight shadow-sm transition duration-200"
                :class="[
                    nodeSizeClass,
                    nodeToneClass,
                    node.pic_projects && node.pic_projects.length > 0 ? 'cursor-pointer hover:border-slate-400 dark:hover:border-white/30 hover:shadow-md' : 'cursor-default',
                ]"
                :title="
                    node.pic_projects && node.pic_projects.length > 0
                        ? `${node.organization_name} (Klik untuk ${showPics ? 'menyembunyikan' : 'menampilkan'} PIC)`
                        : node.organization_name
                "
                @click="node.pic_projects && node.pic_projects.length > 0 && (showPics = !showPics)"
            >
                <span class="block max-w-full break-words whitespace-normal">
                    {{ node.organization_name }}
                </span>
                <div v-if="node.pic_projects && node.pic_projects.length > 0 && showPics" class="mt-1 w-full border-t border-slate-200 dark:border-white/10 pt-1 text-left px-0.5">
                    <div class="text-[7px] text-slate-400 dark:text-slate-500 font-semibold mb-0.5 uppercase tracking-wider">
                        PIC:
                    </div>
                    <div class="space-y-0.5">
                        <div
                            v-for="pic in node.pic_projects"
                            :key="pic.id"
                            class="text-[7px] text-slate-500 dark:text-slate-400 font-normal break-words whitespace-normal max-w-full flex items-start"
                            :title="pic.name"
                        >
                            <span class="mr-0.5 select-none text-[6px] text-slate-400">•</span>
                            <span class="flex-1 min-w-0 leading-[1.1]">{{ pic.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="hasChildren && isExpanded" class="relative mt-2 w-full min-w-0 pt-2">
                <div class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
                    aria-hidden="true" />

                <div class="grid w-full min-w-0 gap-x-2 gap-y-3" :style="childGridStyle">
                    <ThreeView v-for="(child, index) in node.children" :key="child.organization_id" :node="child"
                        :is-root="false" :depth="depth + 1" :is-first-child="index === 0"
                        :is-last-child="index === node.children.length - 1" />
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

const isExpanded = ref(true);
const showPics = ref(false);

const normalizeCode = (value) => String(value ?? '').trim();

const compareCodes = (left, right) => {
    const leftCode = normalizeCode(left.code);
    const rightCode = normalizeCode(right.code);

    if (leftCode.length !== rightCode.length) {
        return leftCode.length - rightCode.length;
    }

    return leftCode.localeCompare(rightCode);
};

const buildCodeHierarchy = (items) => {
    if (!items.length) {
        return [];
    }

    const sorted = [...items]
        .map((item) => ({
            ...item,
            code: normalizeCode(item.code ?? item.organization_code),
            children: [],
        }))
        .sort(compareCodes);

    const nodeByCode = new Map();

    sorted.forEach((item) => {
        if (item.code) {
            nodeByCode.set(item.code, item);
        }
    });

    const roots = [];

    sorted.forEach((item) => {
        if (!item.code || item.code.length === 2) {
            roots.push(item);
            return;
        }

        const parentCode = item.code.slice(0, -2);
        const parentNode = nodeByCode.get(parentCode);

        if (parentNode) {
            parentNode.children.push(item);
        } else {
            roots.push(item);
        }
    });

    const sortTree = (nodes) => {
        return nodes
            .sort(compareCodes)
            .map((node) => ({
                ...node,
                children: node.children ? sortTree(node.children) : [],
            }));
    };

    return sortTree(roots);
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
                    children: buildCodeHierarchy(groupNode.items),
                }))
                .sort((left, right) => left.organization_name.localeCompare(right.organization_name)),
        }))
        .sort((left, right) => left.organization_name.localeCompare(right.organization_name));
};

const organizationTree = computed(() => buildFullHierarchy(props.organizationStructureRows));
const holdingTree = computed(() => {
    return organizationTree.value
        .map((companyNode) => {
            const holdingGroups = (companyNode.children ?? []).filter((groupNode) => {
                const name = String(groupNode.organization_name).toLowerCase();
                const isSubHolding = name.includes('sub') || groupNode.organization_id.endsWith('-2');
                return !isSubHolding;
            });
            return {
                ...companyNode,
                children: holdingGroups,
            };
        })
        .filter((companyNode) => companyNode.children.length > 0);
});
const subHoldingTree = computed(() => {
    return organizationTree.value
        .map((companyNode) => {
            const subHoldingGroups = (companyNode.children ?? []).filter((groupNode) => {
                const name = String(groupNode.organization_name).toLowerCase();
                const isSubHolding = name.includes('sub') || groupNode.organization_id.endsWith('-2');
                return isSubHolding;
            });
            return {
                ...companyNode,
                children: subHoldingGroups,
            };
        })
        .filter((companyNode) => companyNode.children.length > 0);
});
const hasChildren = computed(() => Array.isArray(props.node?.children) && props.node.children.length > 0);

const nodeSizeClass = computed(() => {
    if (props.node?.type === 'holding' || props.node?.type === 'sub_holding') {
        return 'h-8 w-28 text-[10px]';
    }

    if (showPics.value && props.node?.pic_projects?.length > 0) {
        return 'min-h-[3rem] w-20 text-[8px] py-1.5';
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
