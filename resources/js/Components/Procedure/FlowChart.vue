<template>
    <section class="space-y-4">
        <div v-if="rows.length === 0" class="rounded-2xl border border-slate-200 bg-white px-6 py-8 text-sm text-slate-400 shadow-sm dark:border-white/10 dark:bg-[#171717] dark:text-slate-500">
            Procedure Not Available
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="min-w-[1100px] overflow-x-auto">
                <div class="grid grid-cols-[52px_minmax(420px,2fr)_repeat(4,minmax(130px,0.7fr))] border-b border-slate-300 bg-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/10 dark:text-slate-200">
                    <div class="flex items-center justify-center border-r border-slate-300 px-3 py-4 text-center dark:border-white/10">No</div>
                    <div class="flex items-center justify-center border-r border-slate-300 px-4 py-4 text-center dark:border-white/10">Deskripsi Aktivitas</div>
                    <div v-for="role in roleColumns" :key="role.actorId" class="flex items-center justify-center border-r border-slate-300 px-3 py-4 text-center last:border-r-0 dark:border-white/10">
                        {{ role.label }}
                    </div>
                </div>

                <div class="relative divide-y divide-slate-300 dark:divide-white/10" ref="flowchartContainer">
                    <!-- SVG Overlay for Connector Lines -->
                    <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full">
                        <defs>
                            <marker
                                id="arrowhead"
                                markerWidth="8"
                                markerHeight="8"
                                refX="7"
                                refY="4"
                                orient="auto"
                            >
                                <path d="M 0 1 L 7 4 L 0 7 z" class="fill-sky-700 dark:fill-sky-400" />
                            </marker>
                        </defs>
                        <path
                            v-for="(path, idx) in paths"
                            :key="idx"
                            :d="path.d"
                            :marker-end="path.hasArrow ? 'url(#arrowhead)' : undefined"
                            stroke-width="2.5"
                            stroke-linejoin="round"
                            stroke-linecap="round"
                            class="stroke-sky-700 dark:stroke-sky-400 fill-none"
                        />
                    </svg>

                    <div
                        v-for="(row, index) in rows"
                        :key="row.id ?? index"
                        class="grid grid-cols-[52px_minmax(420px,2fr)_repeat(4,minmax(130px,0.7fr))] text-[11px] text-slate-700 dark:text-slate-300"
                        :style="{ minHeight: `${rowHeight}px` }"
                    >
                        <div class="flex items-center justify-center border-r border-slate-300 px-3 py-4 text-center font-medium text-slate-500 dark:border-white/10 dark:text-slate-400">
                            {{ index + 1 }}
                        </div>

                        <div class="flex items-center border-r border-slate-300 px-4 py-4 leading-snug whitespace-pre-line text-slate-900 dark:border-white/10 dark:text-white">
                            {{ row.description }}
                        </div>

                        <div class="col-span-4 grid grid-cols-4">
                            <div
                                v-for="(role, roleIndex) in roleColumns"
                                :key="role.actorId"
                                class="relative border-r border-slate-300 dark:border-white/10 last:border-r-0"
                                :style="{ minHeight: `${rowHeight}px` }"
                            >
                                <div
                                    v-if="index === 0 && roleIndex === 0"
                                    data-flow-id="start"
                                    class="absolute left-1/2 top-2 z-20 -translate-x-1/2 rounded-full border border-sky-500 bg-sky-200 px-3 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-800 shadow-sm dark:border-sky-300 dark:bg-sky-300"
                                >
                                    Start
                                </div>

                                <div
                                    v-if="index === rows.length - 1 && roleIndex === 0"
                                    data-flow-id="end"
                                    class="absolute left-1/2 bottom-2 z-20 -translate-x-1/2 rounded-full border border-sky-500 bg-sky-200 px-3 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-800 shadow-sm dark:border-sky-300 dark:bg-sky-300"
                                >
                                    End
                                </div>

                                <div v-if="hasMapping(row, role.actorId)" class="absolute inset-0 z-10 flex items-center justify-center">
                                    <div
                                        :data-flow-id="`box-${index}-${roleIndex}`"
                                        class="flex h-12 w-16 items-center justify-center border border-sky-700 bg-white text-[11px] font-bold text-slate-900 shadow-sm dark:bg-[#171717] dark:text-white"
                                    >
                                        {{ index + 1 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, onMounted, onUpdated, onBeforeUnmount } from 'vue';

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    sops: {
        type: Array,
        default: () => [],
    },
});

const roleDefinitions = [
    { actorId: 6, fallbackLabel: 'FUNGSI ITSP' },
    { actorId: 2, fallbackLabel: 'DIREKTUR TEKNIS' },
    { actorId: 9, fallbackLabel: 'FUNGSI TERKAIT IT LAINNYA' },
    { actorId: 12, fallbackLabel: 'DIREKTUR UTAMA' },
];

const rowHeight = 140;

const roleColumns = computed(() => {
    return roleDefinitions.map((definition) => {
        const actor = props.actors.find((item) => Number(item.id) === definition.actorId) ?? null;

        return {
            ...definition,
            label: actor?.name || definition.fallbackLabel,
        };
    });
});

const rows = computed(() => {
    return (props.sops || [])
        .filter((item) => item?.tipe === 'A')
        .map((item) => item);
});

function getMapList(row) {
    return row?.mapActorSops || row?.map_actor_sops || [];
}

function hasMapping(row, actorId) {
    return getMapList(row).some((mapping) => Number(mapping.actor_id) === Number(actorId));
}

function mappedRoleIndexes(row) {
    return roleColumns.value
        .map((role, index) => (hasMapping(row, role.actorId) ? index : null))
        .filter((index) => index !== null);
}

// SVG Connections State
const flowchartContainer = ref(null);
const paths = ref([]);
let resizeObserver = null;

function updatePaths() {
    if (!flowchartContainer.value || rows.value.length === 0) {
        paths.value = [];
        return;
    }

    const containerRect = flowchartContainer.value.getBoundingClientRect();
    const newPaths = [];

    // Helper to get relative coordinates of an element
    const getElCoords = (flowId) => {
        const el = flowchartContainer.value.querySelector(`[data-flow-id="${flowId}"]`);
        if (!el) return null;
        const rect = el.getBoundingClientRect();
        return {
            left: rect.left - containerRect.left,
            right: rect.right - containerRect.left,
            top: rect.top - containerRect.top,
            bottom: rect.bottom - containerRect.top,
            width: rect.width,
            height: rect.height,
            centerX: rect.left - containerRect.left + rect.width / 2,
            centerY: rect.top - containerRect.top + rect.height / 2,
        };
    };

    // 1. Connection from Start to Box 1
    const startCoords = getElCoords('start');
    const firstCols = mappedRoleIndexes(rows.value[0]);
    if (startCoords && firstCols.length > 0) {
        const firstCol = firstCols[0];
        const box1Coords = getElCoords(`box-0-${firstCol}`);
        if (box1Coords) {
            newPaths.push({
                d: `M ${startCoords.centerX} ${startCoords.bottom} L ${box1Coords.centerX} ${box1Coords.top}`,
                hasArrow: true,
            });
        }
    }

    // 2. Connections between rows
    for (let r = 0; r < rows.value.length - 1; r++) {
        const srcCols = mappedRoleIndexes(rows.value[r]);
        const tgtCols = mappedRoleIndexes(rows.value[r + 1]);

        const srcCoordsList = srcCols
            .map(c => ({ col: c, coords: getElCoords(`box-${r}-${c}`) }))
            .filter(item => item.coords !== null);

        const tgtCoordsList = tgtCols
            .map(c => ({ col: c, coords: getElCoords(`box-${r + 1}-${c}`) }))
            .filter(item => item.coords !== null);

        if (srcCoordsList.length > 0 && tgtCoordsList.length > 0) {
            // Find Y_mid
            const maxSrcBottom = Math.max(...srcCoordsList.map(item => item.coords.bottom));
            const minTgtTop = Math.min(...tgtCoordsList.map(item => item.coords.top));
            const yMid = (maxSrcBottom + minTgtTop) / 2;

            // Generate source vertical lines to yMid
            srcCoordsList.forEach(item => {
                newPaths.push({
                    d: `M ${item.coords.centerX} ${item.coords.bottom} L ${item.coords.centerX} ${yMid}`,
                    hasArrow: false,
                });
            });

            // Generate target vertical lines from yMid (with arrowheads)
            tgtCoordsList.forEach(item => {
                newPaths.push({
                    d: `M ${item.coords.centerX} ${yMid} L ${item.coords.centerX} ${item.coords.top}`,
                    hasArrow: true,
                });
            });

            // Generate the connecting horizontal line at yMid
            const allX = [
                ...srcCoordsList.map(item => item.coords.centerX),
                ...tgtCoordsList.map(item => item.coords.centerX)
            ];
            const minX = Math.min(...allX);
            const maxX = Math.max(...allX);
            newPaths.push({
                d: `M ${minX} ${yMid} L ${maxX} ${yMid}`,
                hasArrow: false,
            });
        }
    }

    // 3. Connection from Box 6 (last row) to End
    const lastRowIndex = rows.value.length - 1;
    const lastCols = mappedRoleIndexes(rows.value[lastRowIndex]);
    const endCoords = getElCoords('end');
    if (lastCols.length > 0 && endCoords) {
        const lastCol = lastCols[0];
        const lastBoxCoords = getElCoords(`box-${lastRowIndex}-${lastCol}`);
        if (lastBoxCoords) {
            newPaths.push({
                d: `M ${lastBoxCoords.centerX} ${lastBoxCoords.bottom} L ${endCoords.centerX} ${endCoords.top}`,
                hasArrow: true,
            });
        }
    }

    paths.value = newPaths;
}

onMounted(() => {
    updatePaths();
    setTimeout(updatePaths, 150); // Small delay to guarantee elements are in correct final positions

    window.addEventListener('resize', updatePaths);

    if (flowchartContainer.value && typeof ResizeObserver !== 'undefined') {
        resizeObserver = new ResizeObserver(() => {
            updatePaths();
        });
        resizeObserver.observe(flowchartContainer.value);
    }
});

onUpdated(() => {
    updatePaths();
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updatePaths);
    if (resizeObserver) {
        resizeObserver.disconnect();
    }
});
</script>
