<template>
    <article class="h-full min-h-[220px] rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-[0_4px_16px_rgba(0,0,0,0.05)] dark:border-white/10 dark:bg-[#171717]">
        <div class="mb-4 flex items-center justify-between gap-2">
            <h2 class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                Digital Initiatives Planning Timeline Status
            </h2>
        </div>

        <!-- GitHub-style Branch Flow (SVG) -->
        <div ref="branchContainer" class="relative" :style="{ minHeight: hasAnyPostpone ? '140px' : '70px' }">
            <svg class="absolute inset-0 h-full w-full overflow-visible">
                <!-- Main branch line segments (colored per gap) -->
                <line
                    v-for="(seg, si) in mainLineSegments"
                    :key="'seg-' + si"
                    :x1="seg.x1"
                    y1="28"
                    :x2="seg.x2"
                    y2="28"
                    :stroke="seg.color"
                    stroke-width="2.5"
                    stroke-linecap="round"
                />

                <!-- Postpone branch curves (one per branch-from point) -->
                <path
                    v-for="br in postponeBranches"
                    :key="br.fromKey"
                    :d="br.curvePath"
                    fill="none"
                    stroke="#fb7185"
                    stroke-width="2.5"
                    stroke-linecap="round"
                />
            </svg>

            <!-- Main branch nodes -->
            <div class="relative flex items-start justify-between px-4">
                <div
                    v-for="step in mainSteps"
                    :key="step.key"
                    class="relative z-10 flex cursor-pointer flex-col items-center"
                    style="min-width: 56px;"
                    @click="emit('select', step.key)"
                >
                    <div class="flex h-[56px] w-[56px] items-center justify-center">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full border-[2.5px] text-[11px] font-bold shadow transition-transform hover:scale-110"
                            :class="[step.nodeClass, activeStatus === step.key ? 'ring-2 ring-offset-2 ring-blue-400 dark:ring-offset-[#171717]' : '']"
                        >
                            {{ step.count }}
                        </div>
                    </div>
                    <span class="mt-0.5 text-[10px] font-semibold" :class="step.labelClass">{{ step.label }}</span>
                </div>
            </div>

            <!-- Postpone nodes (one per branch) -->
            <div
                v-for="br in postponeBranches"
                :key="'pn-' + br.fromKey"
                class="absolute z-10 flex flex-col items-center"
                :style="{ left: `${br.endX}px`, top: '86px', transform: 'translateX(-50%)' }"
            >
                <div class="flex h-9 w-9 items-center justify-center rounded-full border-[2.5px] border-rose-400 bg-rose-500 text-[11px] font-bold text-white shadow">
                    {{ br.count }}
                </div>
                <span class="mt-0.5 whitespace-nowrap text-[10px] font-semibold text-rose-500 dark:text-rose-400">Postpone</span>
            </div>
        </div>
    </article>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
    postponeFromCounts: {
        type: Object,
        default: () => ({}),
    },
    activeStatus: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['select']);

// ── Main branch config (straight line: Drafting → Approved) ──
const mainBranch = [
    { key: 'drafting', label: 'Drafting', nodeClass: 'border-slate-400 bg-slate-500 text-white',     labelClass: 'text-slate-600 dark:text-slate-300' },
    { key: 'propose',  label: 'Propose',  nodeClass: 'border-blue-400 bg-blue-500 text-white',      labelClass: 'text-blue-600 dark:text-blue-300' },
    { key: 'review',   label: 'Review',   nodeClass: 'border-amber-400 bg-amber-500 text-white',    labelClass: 'text-amber-600 dark:text-amber-300' },
    { key: 'approved', label: 'Approved', nodeClass: 'border-emerald-400 bg-emerald-500 text-white', labelClass: 'text-emerald-600 dark:text-emerald-300' },
];

const mainSteps = computed(() => {
    const c = props.statusCounts || {};
    return mainBranch.map((s) => ({ ...s, count: Number(c[s.key] ?? 0) }));
});

// ── Postpone: one branch per "from" status ──
const hasAnyPostpone = computed(() => {
    const pfc = props.postponeFromCounts || {};
    return Object.values(pfc).some((v) => Number(v) > 0);
});

// ── SVG coordinate helpers ──
const branchContainer = ref(null);
const containerWidth  = ref(400);
const PAD = 16;

const updateWidth = () => {
    if (branchContainer.value) containerWidth.value = branchContainer.value.offsetWidth;
};

let ro = null;
onMounted(() => {
    nextTick(updateWidth);
    if (typeof ResizeObserver !== 'undefined' && branchContainer.value) {
        ro = new ResizeObserver(updateWidth);
        ro.observe(branchContainer.value);
    }
});
onUnmounted(() => ro?.disconnect());

const nodeX = (i) => {
    const gap = mainBranch.length - 1;
    return PAD + ((containerWidth.value - PAD * 2) / gap) * i;
};

// Main line segments with solid colors
const segmentColors = ['#94a3b8', '#60a5fa', '#f59e0b'];
const mainLineSegments = computed(() => {
    const segs = [];
    for (let i = 0; i < mainBranch.length - 1; i++) {
        segs.push({
            x1: nodeX(i),
            x2: nodeX(i + 1),
            color: segmentColors[i] ?? '#10b981',
        });
    }
    return segs;
});

// Build one branch descriptor per "from" status that has postponed items
const postponeBranches = computed(() => {
    const pfc = props.postponeFromCounts || {};
    const branches = [];
    const MAIN_Y = 28;
    const END_Y  = 102;

    for (const step of mainBranch) {
        const cnt = Number(pfc[step.key] ?? 0);
        if (cnt <= 0) continue;

        const fromIdx = mainBranch.findIndex((s) => s.key === step.key);
        const startX  = nodeX(fromIdx);
        const nextIdx = Math.min(fromIdx + 1, mainBranch.length - 1);
        const endX    = (nodeX(fromIdx) + nodeX(nextIdx)) / 2;

        const cp1x = startX;
        const cp1y = MAIN_Y + (END_Y - MAIN_Y) * 0.45;
        const cp2x = endX;
        const cp2y = END_Y - (END_Y - MAIN_Y) * 0.45;

        branches.push({
            fromKey:   step.key,
            count:     cnt,
            endX,
            curvePath: `M ${startX} ${MAIN_Y} C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${endX} ${END_Y}`,
        });
    }
    return branches;
});
</script>
