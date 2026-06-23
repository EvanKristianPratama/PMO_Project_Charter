<template>
    <!-- ROOT: render semua root companies (parent_id null) -->
    <template v-if="isRoot">
        <div v-if="!companies || companies.length === 0"
            class="rounded-md border border-dashed border-slate-200 py-8 text-center text-xs text-slate-500 dark:border-white/10 dark:text-slate-400">
            Data company tidak ditemukan.
        </div>

        <div v-else class="px-4 py-6 overflow-x-auto">
            <div class="flex flex-row justify-center items-start gap-8 flex-wrap min-w-max">
                <CompanyThreeView
                    v-for="rootCompany in rootCompanies"
                    :key="rootCompany.id"
                    :node="rootCompany"
                    :all-companies="companies"
                    :is-root="false"
                    :depth="0"
                />
            </div>
        </div>
    </template>

    <!-- NODE: render satu kotak company + children-nya -->
    <div v-else class="relative flex flex-col items-center min-w-0 shrink-0">
        <!-- Sibling connectors -->
        <div v-if="depth >= 1 && (!isFirstChild || !isLastChild)"
            class="absolute top-0 h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-3' : '',
                isLastChild ? '-left-3 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-3 -right-3' : '',
            ]" aria-hidden="true" />

        <!-- Garis horizontal masuk dari kiri untuk sideways first child -->
        <div
            v-if="isSideways && isFirstChild"
            class="absolute top-0 h-px bg-slate-300 dark:bg-white/20"
            style="left: 88px; right: 50%;"
            aria-hidden="true"
        />

        <!-- Garis vertikal dari atas ke kotak (untuk depth >= 1) -->
        <div
            v-if="depth >= 1"
            class="w-px bg-slate-300 dark:bg-white/20"
            style="height: 16px;"
            aria-hidden="true"
        />

        <!-- Kotak node -->
        <div
            v-if="node.isVirtualGroup"
            class="relative flex flex-col items-center justify-center rounded-xl border border-dashed border-indigo-400 bg-indigo-50/50 px-3 py-1.5 text-center leading-tight shadow-sm transition duration-200 cursor-default dark:bg-indigo-950/20 dark:border-indigo-500/40"
            :title="nodeTitle"
        >
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 mb-1">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125V17.25c0 .621-.504 1.125-1.125 1.125h-6A1.125 1.125 0 0 1 2.25 17.25V7.125z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 7.125c0-.621.504-1.125 1.125-1.125h6c.621 0 1.125.504 1.125 1.125V17.25c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125V7.125z" />
                </svg>
            </span>
            <span class="block max-w-[110px] break-words whitespace-normal text-[8px] font-extrabold uppercase text-indigo-700 dark:text-indigo-300 tracking-wider">
                {{ node.name }}
            </span>
            <span
                v-if="hasChildren"
                @click.stop="toggleExpand"
                class="absolute -bottom-2 left-1/2 -translate-x-1/2 flex items-center justify-center w-4 h-4 rounded-full text-[8px] font-bold leading-none transition-colors duration-200 cursor-pointer z-10 shadow"
                :class="isExpanded
                    ? 'bg-indigo-600 text-white'
                    : 'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-300'"
            >
                {{ isExpanded ? '−' : '+' }}
            </span>
        </div>

        <div
            v-else
            class="relative flex flex-col items-center justify-center rounded border px-3 py-2 text-center leading-tight shadow-sm transition duration-200 cursor-default"
            :class="nodeBoxClass"
            :title="nodeTitle"
        >
            <!-- Ikon company -->
            <span class="flex h-5 w-5 items-center justify-center rounded mb-1" :class="iconBgClass">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </span>

            <!-- Nama company -->
            <span class="block max-w-full break-words whitespace-normal text-[9px] font-bold">
                {{ node.name }}
            </span>

            <!-- Singkatan -->
            <span v-if="node.singkatan" class="block max-w-full break-words whitespace-normal text-[8px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                ({{ node.singkatan }})
            </span>


            <!-- Organization -->
            <span v-if="node.organization" class="block max-w-full break-words whitespace-normal text-[8px] font-normal mt-0.5 opacity-70">
                {{ node.organization }}
            </span>

            <!-- Expand/collapse indicator -->
            <span
                v-if="hasChildren"
                @click.stop="toggleExpand"
                class="absolute -bottom-2 left-1/2 -translate-x-1/2 flex items-center justify-center w-4 h-4 rounded-full text-[8px] font-bold leading-none transition-colors duration-200 cursor-pointer z-10 shadow"
                :class="isExpanded
                    ? 'bg-blue-500 text-white'
                    : 'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-300'"
            >
                {{ isExpanded ? '−' : '+' }}
            </span>
        </div>

        <!-- Children -->
        <template v-if="hasChildren && isExpanded">
            <div class="relative mt-4 w-full flex flex-col items-center gap-8">
                <!-- Garis vertikal turun dari parent box ke Row 1 -->
                <div
                    class="absolute left-1/2 -translate-x-1/2 w-px bg-slate-300 dark:bg-white/20"
                    style="top: -8px; height: 8px;"
                    aria-hidden="true"
                />

                <!-- Garis horizontal bypass ke kiri (menghubungkan trunk ke line vertical kiri) -->
                <div
                    v-if="hasSidewaysRows"
                    class="absolute h-px bg-slate-300 dark:bg-white/20"
                    style="left: -32px; right: 50%; top: -8px;"
                    aria-hidden="true"
                />

                <!-- Render each row -->
                <div
                    v-for="(row, rowIdx) in childrenRows"
                    :key="row.level"
                    :class="[
                        'relative flex flex-row justify-center items-start gap-6 flex-wrap w-full',
                        row.level > 1 && !node.isVirtualGroup ? '-ml-60 justify-start' : ''
                    ]"
                >
                    <!-- Vertical bypass line segment -->
                    <template v-if="hasSidewaysRows">
                        <!-- If it's not the last row, draw from top of this row to top of next row -->
                        <div 
                            v-if="rowIdx < childrenRows.length - 1"
                            class="absolute bg-slate-300 dark:bg-white/20 w-px"
                            :style="{
                                left: '-32px',
                                top: rowIdx === 0 ? '-8px' : '0px',
                                bottom: '-32px'
                            }"
                            aria-hidden="true"
                        />
                        <!-- If it is the only row and it's sideways, draw the 8px link to top bypass connector -->
                        <div 
                            v-else-if="rowIdx === 0 && row.level > 1"
                            class="absolute bg-slate-300 dark:bg-white/20 w-px"
                            style="left: -32px; top: -8px; bottom: 100%;"
                            aria-hidden="true"
                        />
                    </template>

                    <!-- Render nodes inside the row -->
                    <CompanyThreeView
                        v-for="(child, idx) in row.nodes"
                        :key="child.id"
                        :node="child"
                        :all-companies="allCompanies"
                        :is-root="false"
                        :depth="depth + 1"
                        :is-first-child="idx === 0"
                        :is-last-child="idx === row.nodes.length - 1"
                        :is-sideways="!node.isVirtualGroup && row.level > 1"
                    />
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

defineOptions({ name: 'CompanyThreeView' });

const props = defineProps({
    /** Data semua companies (array of { id, parent_id, name, organization }) - hanya di root */
    companies: {
        type: Array,
        default: () => [],
    },
    /** Node company saat ini (saat bukan root) */
    node: {
        type: Object,
        default: null,
    },
    /** Semua companies (untuk resolve children) */
    allCompanies: {
        type: Array,
        default: () => [],
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
    isSideways: {
        type: Boolean,
        default: false,
    },
});

// Expand by default kecuali depth >= 1, tapi expand virtual groups
const isExpanded = ref(props.depth < 1 || props.node?.isVirtualGroup);

// =====================
// Root-level computed
// =====================

/** Root companies = yang tidak punya parent_id, atau parent-nya tidak ada dalam list yang dipassing (untuk support filter) */
const rootCompanies = computed(() => {
    const list = props.companies || [];
    const ids = new Set(list.map(c => c.id));
    return list.filter(c => !c.parent_id || !ids.has(Number(c.parent_id)));
});

// =====================
// Node-level computed
// =====================

/** Children langsung dari node ini (tanpa grouping, untuk helper) */
const rawChildren = computed(() => {
    if (!props.node || props.node.isVirtualGroup) return [];
    const allList = props.allCompanies.length ? props.allCompanies : props.companies;
    return allList.filter(c => Number(c.parent_id) === Number(props.node.id));
});

/** Grouped database-level children: Kelompokkan DB children berdasarkan kolom grup. */
const groupedChildren = computed(() => {
    if (!props.node || props.node.isVirtualGroup) return [];
    
    const list = rawChildren.value;
    const result = [];
    const groups = {};
    
    for (const child of list) {
        if (!child.grup) {
            // Direct child without group. Is it top-level?
            const isTop = !child.level || child.level === 1 || !list.some(other => !other.grup && other.level === child.level - 1);
            if (isTop) {
                result.push(child);
            }
        } else {
            // Under a group
            if (!groups[child.grup]) {
                groups[child.grup] = [];
            }
            groups[child.grup].push(child);
        }
    }
    
    // Add virtual group nodes
    for (const groupName in groups) {
        result.push({
            isVirtualGroup: true,
            id: `virtual-group-${props.node.id}-${groupName}`,
            name: groupName,
            parent_id: props.node.id,
            groupChildren: groups[groupName],
        });
    }
    
    return result;
});

/** List anak yang akan dirender */
const childrenToRender = computed(() => {
    if (!props.node) return [];
    
    const allList = props.allCompanies.length ? props.allCompanies : props.companies;
    
    if (props.node.isVirtualGroup) {
        // Virtual Group Node: represents group category
        // Direct children under this group node should only be the top-level ones (level 1 or level null, or no parent level in groupChildren)
        const list = props.node.groupChildren || [];
        return list.filter(c => {
            if (!c.level || c.level === 1) return true;
            const hasParentLevel = list.some(other => other.level === c.level - 1);
            return !hasParentLevel;
        });
    }
    
    // Real company node
    let children = [];
    
    // 1. Resolve virtual children based on level (same parent, same group, level = current_level + 1)
    if (props.node.level) {
        const parentId = props.node.parent_id;
        const groupName = props.node.grup;
        const currentLevel = props.node.level;
        
        // Find all companies under the same parent and same group
        const sameContextCompanies = allList.filter(c => 
            Number(c.parent_id) === Number(parentId) && 
            c.grup === groupName
        );
        
        // Filter for level + 1 children
        const levelChildren = sameContextCompanies.filter(c => Number(c.level) === Number(currentLevel) + 1);
        children = [...children, ...levelChildren];
    }
    
    // 2. Resolve database-level children (grouped by group / level)
    children = [...children, ...groupedChildren.value];
    
    return children;
});

const hasChildren = computed(() => childrenToRender.value.length > 0);

const nodeTitle = computed(() => {
    if (props.node?.isVirtualGroup) {
        return `Kategori Grup: ${props.node.name} (${childrenToRender.value.length} anak perusahaan)`;
    }
    const parts = [props.node?.name];
    if (props.node?.level) parts.push(`Level ${props.node.level}`);
    if (props.node?.grup) parts.push(props.node.grup);
    if (props.node?.organization) parts.push(props.node.organization);
    if (hasChildren.value) {
        parts.push(`(Klik ± untuk ${isExpanded.value ? 'sembunyikan' : 'tampilkan'} anak)`);
    }
    return parts.filter(Boolean).join(' · ');
});

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
};

const getLevelOfNode = (n) => {
    if (!n) return 1;
    if (n.isVirtualGroup) {
        const childrenLevels = (n.groupChildren || []).map(c => c.level).filter(Boolean);
        if (childrenLevels.length > 0) {
            return Math.min(...childrenLevels);
        }
        return 1;
    }
    return n.level || 1;
};

const childrenRows = computed(() => {
    const list = childrenToRender.value;
    const rows = {};
    
    for (const child of list) {
        const lvl = getLevelOfNode(child);
        if (!rows[lvl]) {
            rows[lvl] = [];
        }
        rows[lvl].push(child);
    }
    
    return Object.keys(rows)
        .map(Number)
        .sort((a, b) => a - b)
        .map(lvl => ({
            level: lvl,
            nodes: rows[lvl]
        }));
});

const hasSidewaysRows = computed(() => {
    if (props.node?.isVirtualGroup) return false;
    return childrenRows.value.some(row => row.level > 1);
});

/** Style box — root company lebih menonjol, child company lebih simpel */
const nodeBoxClass = computed(() => {
    if (props.depth === 0) {
        return 'min-w-[100px] max-w-[140px] bg-white text-slate-900 border-blue-300 dark:bg-[#1a1a1a] dark:text-slate-100 dark:border-blue-500/40 ring-1 ring-blue-200 dark:ring-blue-500/20';
    }
    return 'min-w-[90px] max-w-[130px] bg-white text-slate-900 border-slate-300 dark:bg-[#1a1a1a] dark:text-slate-100 dark:border-white/10';
});

/** Icon background: biru untuk root, slate untuk child */
const iconBgClass = computed(() => {
    if (props.depth === 0) {
        return 'bg-blue-500 text-white';
    }
    return 'bg-slate-200 text-slate-600 dark:bg-white/10 dark:text-slate-300';
});
</script>
