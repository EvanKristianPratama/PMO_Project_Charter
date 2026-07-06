<template>
    <!-- Jika node bertipe sub_holding, skip kotak dan langsung render children-nya -->
    <template v-if="node?.type === 'sub_holding'">
        <UpstreamThreeView
            v-for="(child, index) in node.children"
            :key="child.organization_id"
            :node="child"
            :depth="depth"
            :is-first-child="index === 0"
            :is-last-child="index === node.children.length - 1"
            :force-expand-state="forceExpandState"
        />
    </template>

    <!-- Render normal untuk semua tipe node lainnya -->
    <div v-else class="relative min-w-0" :class="depth < 4 ? 'shrink-0' : 'w-full'">
        <!-- Horizontal line (for grid-based sibling connectors at depth 1 through 3) -->
        <div v-if="depth >= 1 && depth <= 3 && (!isFirstChild || !isLastChild)"
            class="absolute top-[-8px] h-px bg-slate-300 dark:bg-white/20" :class="[
                isFirstChild ? 'left-1/2 -right-1' : '',
                isLastChild ? '-left-1 right-1/2' : '',
                !isFirstChild && !isLastChild ? '-left-1 -right-1' : '',
            ]" aria-hidden="true" />
        <!-- Vertical line going up (for grid-based sibling connectors at depth 1 through 3) -->
        <div v-if="depth >= 1 && depth <= 3"
            class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
            aria-hidden="true" />

        <div class="flex w-full min-w-0" :class="depth >= 4 ? 'flex-row items-start' : 'flex-col items-center'">
            <!-- For depth >= 4: vertical command line connector (left side) -->
            <div v-if="depth >= 4" class="flex flex-col items-start shrink-0" style="width: 24px;">
                <!-- Horizontal line turning right to node -->
                <div class="self-stretch border-b border-l border-slate-300 dark:border-white/20 rounded-bl-md"
                    style="height: 12px;" aria-hidden="true"></div>
            </div>

            <!-- Node content + children wrapper -->
            <div class="flex flex-col min-w-0" :class="depth >= 4 ? 'flex-1' : 'items-center w-full'">
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
                        {{ node.jabatan || node.organization_name }}
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

                <!-- Upstream CEO Custom Children Rendering -->
                <template v-if="isUpstreamCeo && hasChildren && isExpanded">
                    <!-- Support Staff Grid (Teal boxes with center vertical line & branch lines) -->
                    <div v-if="upstreamSupportStaff.length > 0" class="flex flex-row justify-center gap-16 my-6 relative w-full">
                        <!-- Central vertical line connecting CEO to Directors -->
                        <div class="absolute left-1/2 top-[-24px] bottom-[-24px] w-px bg-slate-300 dark:bg-white/20 -translate-x-1/2" aria-hidden="true"></div>

                        <!-- Left Column (Corsec, VP Legal, Chief Audit) -->
                        <div class="flex flex-col gap-3 items-end z-10">
                            <div v-for="staff in leftStaff" :key="staff.organization_id" 
                                class="relative flex flex-col items-center justify-center rounded border border-slate-300 bg-white dark:bg-[#1a1a1a] dark:border-white/10 px-3 py-1.5 text-center font-semibold leading-tight shadow-sm text-[8px] text-slate-900 dark:text-slate-100 w-auto max-w-[12rem] min-h-[1.5rem]">
                                {{ staff.jabatan || staff.organization_name }}
                                <!-- Horizontal branch to center line -->
                                <div class="absolute right-[-32px] top-1/2 -translate-y-1/2 w-[32px] h-px bg-slate-300 dark:bg-white/20" aria-hidden="true"></div>
                            </div>
                        </div>
                        <!-- Right Column (VP Innovation, VP HSSE) -->
                        <div class="flex flex-col gap-3 items-start z-10">
                            <div v-for="staff in rightStaff" :key="staff.organization_id" 
                                class="relative flex flex-col items-center justify-center rounded border border-slate-300 bg-white dark:bg-[#1a1a1a] dark:border-white/10 px-3 py-1.5 text-center font-semibold leading-tight shadow-sm text-[8px] text-slate-900 dark:text-slate-100 w-auto max-w-[12rem] min-h-[1.5rem]">
                                {{ staff.jabatan || staff.organization_name }}
                                <!-- Horizontal branch to center line -->
                                <div class="absolute left-[-32px] top-1/2 -translate-y-1/2 w-[32px] h-px bg-slate-300 dark:bg-white/20" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Directors Row (Horizontal Flex) -->
                    <div class="flex flex-row justify-center items-start gap-x-4 gap-y-3 w-full min-w-0">
                        <UpstreamThreeView v-for="(child, index) in upstreamDirectors" :key="child.organization_id" :node="child"
                            :depth="depth + 1" :is-first-child="index === 0"
                            :is-last-child="index === upstreamDirectors.length - 1"
                            :force-expand-state="forceExpandState" />
                    </div>
                </template>

                <!-- Children of Directors (Vertical Column Stack) -->
                <template v-else-if="isUpstreamDirector && hasChildren && isExpanded">
                    <div class="relative mt-3 w-full pl-[50%]">
                        <!-- Vertical line down from center of director -->
                        <div class="absolute w-px bg-slate-300 dark:bg-white/20 left-1/2"
                            :style="{ top: '-12px', height: '100%' }"
                            aria-hidden="true"></div>

                        <div class="flex flex-col gap-2">
                            <UpstreamThreeView v-for="(child, index) in node.children" :key="child.organization_id" :node="child"
                                :depth="depth + 1" :is-first-child="index === 0"
                                :is-last-child="index === node.children.length - 1"
                                :force-expand-state="forceExpandState" />
                        </div>
                    </div>
                </template>

                <!-- Children: depth < 4 (Holding & Subholding) use horizontal flex layout for their children -->
                <template v-else-if="hasChildren && isExpanded && depth < 4">
                    <div class="relative mt-2 w-full min-w-0 pt-2">
                        <div class="absolute left-1/2 top-[-8px] h-[8px] w-px -translate-x-1/2 bg-slate-300 dark:bg-white/20"
                            aria-hidden="true" />

                        <div class="flex flex-row justify-center items-start gap-x-2 gap-y-3 w-full min-w-0">
                            <UpstreamThreeView v-for="(child, index) in node.children" :key="child.organization_id" :node="child"
                                :depth="depth + 1" :is-first-child="index === 0"
                                :is-last-child="index === node.children.length - 1"
                                :force-expand-state="forceExpandState" />
                        </div>
                    </div>
                </template>

                <!-- Children: depth >= 4 (VPs and below) use vertical command line layout -->
                <div v-if="hasChildren && isExpanded && depth >= 4" class="relative mt-3 ml-10">
                    <!-- Continuous vertical line for all children on the left -->
                    <div class="absolute w-px bg-slate-300 dark:bg-white/20 left-0"
                        :style="{ top: '-12px', height: '100%' }"
                        aria-hidden="true"></div>

                    <div class="flex flex-col gap-2">
                        <UpstreamThreeView v-for="(child, index) in node.children" :key="child.organization_id" :node="child"
                            :depth="depth + 1" :is-first-child="index === 0"
                            :is-last-child="index === node.children.length - 1"
                            :force-expand-state="forceExpandState" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import { computed, ref, watch } from 'vue';
import UpstreamThreeView from './UpstreamThreeView.vue';

defineOptions({
    name: 'UpstreamThreeView',
});

const props = defineProps({
    node: {
        type: Object,
        required: true,
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
    forceExpandState: {
        type: Boolean,
        default: null,
    },
});

// Depth 0-4, 6+ auto-expand, depth 5 collapsed by default (show/hide children on click)
const isExpanded = ref(props.depth !== 5);

watch(() => props.forceExpandState, (newVal) => {
    if (newVal !== null) {
        isExpanded.value = newVal;
    }
});

const hasChildren = computed(() => Array.isArray(props.node?.children) && props.node.children.length > 0);

const isClickable = computed(() => hasChildren.value && props.depth === 5);

const nodeTitle = computed(() => {
    if (hasChildren.value && props.depth === 5) {
        return `${props.node?.jabatan || props.node?.organization_name} (Klik untuk ${isExpanded.value ? 'menyembunyikan' : 'menampilkan'} anak organisasi)`;
    }
    return props.node?.jabatan || props.node?.organization_name || '';
});

const handleNodeClick = () => {
    if (hasChildren.value && props.depth === 5) {
        isExpanded.value = !isExpanded.value;
    }
};

const isUpstreamCeo = computed(() => {
    return props.node && (Number(props.node.organization_id) === 174 || String(props.node.code) === '01100000');
});

const isUpstreamDirector = computed(() => {
    if (!props.node) return false;
    const parentIsCeo = Number(props.node.parent_id) === 174;
    const title = String(props.node.jabatan || props.node.organization_name || '').toLowerCase();
    const hasDirectorTitle = title.includes('direktur');
    return parentIsCeo && hasDirectorTitle;
});

const upstreamSupportStaff = computed(() => {
    if (!isUpstreamCeo.value || !props.node.children) return [];
    return props.node.children.filter(child => {
        const title = String(child.jabatan || child.organization_name || '').toLowerCase();
        return !title.includes('direktur');
    });
});

const upstreamDirectors = computed(() => {
    if (!isUpstreamCeo.value || !props.node.children) return props.node?.children || [];
    return props.node.children.filter(child => {
        const title = String(child.jabatan || child.organization_name || '').toLowerCase();
        return title.includes('direktur');
    });
});

const leftStaff = computed(() => upstreamSupportStaff.value.slice(0, 3));
const rightStaff = computed(() => upstreamSupportStaff.value.slice(3));

const nodeSizeClass = computed(() => {
    if (props.node?.type === 'holding' || props.node?.type === 'sub_holding') {
        return 'h-8 w-28 text-[10px]';
    }
    return 'min-h-[1.5rem] w-auto px-3 py-1.5 text-[8px] max-w-[12rem]';
});

const nodeToneClass = computed(() => {
    return 'bg-white text-slate-900 border-slate-300 dark:bg-[#1a1a1a] dark:text-slate-100 dark:border-white/10';
});
</script>
