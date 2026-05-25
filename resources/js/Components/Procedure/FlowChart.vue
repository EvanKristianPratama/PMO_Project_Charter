<template>
    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 px-1">
            <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white text-xs font-semibold shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <button
                    v-for="category in flowCategories"
                    :key="category.value"
                    type="button"
                    class="px-3 py-2 transition-colors"
                    :class="activeFlowType === category.value
                        ? 'bg-[#821f44] text-white'
                        : 'text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-white/5'"
                    @click="setActiveFlowType(category.value)"
                >
                    {{ category.shortLabel }}
                </button>
            </div>

            <button
                @click="openAddDiagramModal"
                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Mapping
            </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="border-b border-slate-200 px-5 py-3 dark:border-white/10">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                    Diagram Alir {{ activeCategory?.label }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                        <tr>
                            <th scope="col" class="w-16 px-5 py-3 text-center">No</th>
                            <th scope="col" class="px-5 py-3">Aktivitas SOP</th>
                            <th scope="col" class="px-5 py-3">Aktor</th>
                            <th scope="col" class="w-32 px-5 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr v-if="activeDiagramMappings.length === 0">
                            <td colspan="4" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                Belum ada mapping diagram untuk kategori ini.
                            </td>
                        </tr>
                        <tr
                            v-for="(mapping, index) in activeDiagramMappings"
                            :key="mapping.id ?? `${mapping.sop_id}-${mapping.actor_id}-${mapping.tipe}-${index}`"
                            class="hover:bg-slate-50/50 dark:hover:bg-white/5"
                        >
                            <td class="px-5 py-2 text-center font-medium">
                                {{ index + 1 }}
                            </td>
                            <td class="px-5 py-2 text-slate-900 dark:text-white">
                                <div class="line-clamp-2 leading-snug">
                                    {{ mapping.sopDescription }}
                                </div>
                            </td>
                            <td class="px-5 py-2 text-slate-700 dark:text-slate-300">
                                {{ mapping.actorName }}
                            </td>
                            <td class="px-5 py-2 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button
                                        type="button"
                                        class="text-[9px] font-bold uppercase tracking-wider text-blue-600 transition-colors hover:text-blue-800 disabled:cursor-not-allowed disabled:text-slate-400"
                                        :disabled="!mapping.id"
                                        @click="openEditDiagramModal(mapping)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800 disabled:cursor-not-allowed disabled:text-slate-400"
                                        :disabled="!mapping.id"
                                        @click="openDeleteDiagramModal(mapping)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="rows.length === 0" class="rounded-2xl border border-slate-200 bg-white px-6 py-8 text-sm text-slate-400 shadow-sm dark:border-white/10 dark:bg-[#171717] dark:text-slate-500">
            Procedure Not Available
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <div class="overflow-x-auto">
                <div :style="{ minWidth: chartMinWidth }">
                    <div
                        class="grid border-b border-slate-300 bg-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:border-white/10 dark:bg-white/10 dark:text-slate-200"
                        :style="chartGridStyle"
                    >
                        <div class="flex items-center justify-center border-r border-slate-300 px-3 py-4 text-center dark:border-white/10">No</div>
                        <div class="flex items-center justify-center border-r border-slate-300 px-4 py-4 text-center dark:border-white/10">Deskripsi Aktivitas</div>
                        <div class="grid" :style="roleGridStyle">
                            <div v-for="role in roleColumns" :key="role.actorId" class="flex items-center justify-center border-r border-slate-300 px-3 py-4 text-center last:border-r-0 dark:border-white/10">
                                {{ role.label }}
                            </div>
                        </div>
                    </div>

                    <div class="relative divide-y divide-slate-300 dark:divide-white/10" ref="flowchartContainer">
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
                                class="fill-none stroke-sky-700 dark:stroke-sky-400"
                            />
                        </svg>

                        <div
                            v-for="(row, index) in rows"
                            :key="row.id ?? index"
                            class="grid text-[11px] text-slate-700 dark:text-slate-300"
                            :style="{ ...chartGridStyle, minHeight: `${rowHeight}px` }"
                        >
                            <div class="flex items-center justify-center border-r border-slate-300 px-3 py-4 text-center font-medium text-slate-500 dark:border-white/10 dark:text-slate-400">
                                {{ index + 1 }}
                            </div>

                            <div class="flex items-center whitespace-pre-line border-r border-slate-300 px-4 py-4 leading-snug text-slate-900 dark:border-white/10 dark:text-white">
                                {{ row.description }}
                            </div>

                            <div class="grid" :style="roleGridStyle">
                                <div
                                    v-for="(role, roleIndex) in roleColumns"
                                    :key="role.actorId"
                                    class="relative border-r border-slate-300 last:border-r-0 dark:border-white/10"
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
                                        class="absolute bottom-2 left-1/2 z-20 -translate-x-1/2 rounded-full border border-sky-500 bg-sky-200 px-3 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-800 shadow-sm dark:border-sky-300 dark:bg-sky-300"
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
        </div>

        <ConfirmationModal
            :show="isDiagramModalOpen"
            :title="editingDiagramId ? 'Edit Mapping Diagram' : 'Tambah Mapping Diagram'"
            :message="editingDiagramId ? 'Silakan sesuaikan kategori, aktivitas SOP, dan aktor.' : 'Pilih kategori diagram, aktivitas SOP, dan aktor yang menjalankan aktivitas.'"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            max-width="lg"
            :loading="diagramForm.processing"
            @close="closeDiagramModal"
            @confirm="submitDiagramForm"
        >
            <div class="mt-4 space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori Diagram</label>
                        <select
                            v-model="diagramForm.tipe"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                            @change="handleDiagramTypeChange"
                        >
                            <option v-for="category in flowCategories" :key="category.value" :value="category.value">
                                {{ category.label }}
                            </option>
                        </select>
                        <p v-if="diagramForm.errors.tipe" class="mt-0.5 text-xs text-rose-500">{{ diagramForm.errors.tipe }}</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktor</label>
                        <select
                            v-model="diagramForm.actor_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                        >
                            <option value="" disabled>-- Pilih Aktor --</option>
                            <option v-for="actor in actors" :key="actor.id" :value="actor.id">
                                {{ actor.name }}
                            </option>
                        </select>
                        <p v-if="diagramForm.errors.actor_id" class="mt-0.5 text-xs text-rose-500">{{ diagramForm.errors.actor_id }}</p>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Aktivitas SOP</label>
                    <select
                        v-model="diagramForm.sop_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                    >
                        <option value="" disabled>-- Pilih Aktivitas SOP --</option>
                        <option v-for="sop in diagramSopOptions" :key="sop.id" :value="sop.id">
                            {{ shortText(sop.description, 110) }}
                        </option>
                    </select>
                    <p v-if="diagramForm.errors.sop_id" class="mt-0.5 text-xs text-rose-500">{{ diagramForm.errors.sop_id }}</p>
                </div>
            </div>
        </ConfirmationModal>

        <ConfirmationModal
            :show="isDeleteDiagramModalOpen"
            title="Hapus Mapping Diagram"
            :message="`Apakah Anda yakin ingin menghapus mapping '${selectedDiagramMapping?.actorName || '-'}' dari SOP '${shortText(selectedDiagramMapping?.sopDescription, 80)}'?`"
            confirm-text="Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="diagramForm.processing"
            @close="isDeleteDiagramModalOpen = false"
            @confirm="submitDeleteDiagram"
        />
    </section>
</template>

<script setup>
import { computed, ref, onMounted, onUpdated, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Swal from 'sweetalert2';

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

const flowCategories = [
    { value: 'A', shortLabel: 'A. Penyusunan', label: 'A. Penyusunan RSTI' },
    { value: 'B', shortLabel: 'B. Reviu Berkala', label: 'B. Reviu & Pembaruan Berkala RSTI' },
];

const roleDefinitions = [
    { actorId: 6, fallbackLabel: 'FUNGSI ITSP' },
    { actorId: 2, fallbackLabel: 'DIREKTUR TEKNIS' },
    { actorId: 9, fallbackLabel: 'FUNGSI TERKAIT IT LAINNYA' },
    { actorId: 12, fallbackLabel: 'DIREKTUR UTAMA' },
];

const rowHeight = 140;
const activeFlowType = ref('A');

const activeCategory = computed(() => {
    return flowCategories.find((category) => category.value === activeFlowType.value) ?? flowCategories[0];
});

const sopsForActiveType = computed(() => {
    return (props.sops || []).filter((item) => item?.tipe === activeFlowType.value);
});

const rows = computed(() => sopsForActiveType.value);

const activeDiagramMappings = computed(() => {
    return (props.sops || [])
        .flatMap((sop) => getMapList(sop).map((mapping) => normalizeMapping(mapping, sop)))
        .filter((mapping) => mapping.flowType === activeFlowType.value)
        .sort((a, b) => {
            if (Number(a.sop_id) !== Number(b.sop_id)) return Number(a.sop_id) - Number(b.sop_id);
            return String(a.actorName).localeCompare(String(b.actorName));
        });
});

const mappedActorIds = computed(() => {
    return activeDiagramMappings.value
        .map((mapping) => Number(mapping.actor_id))
        .filter((actorId) => Number.isFinite(actorId));
});

const roleColumns = computed(() => {
    const ids = [];
    const addId = (actorId) => {
        const id = Number(actorId);
        if (Number.isFinite(id) && !ids.includes(id)) ids.push(id);
    };

    roleDefinitions.forEach((definition) => addId(definition.actorId));
    mappedActorIds.value.forEach(addId);

    return ids.map((actorId) => {
        const actor = props.actors.find((item) => Number(item.id) === actorId) ?? null;
        const fallback = roleDefinitions.find((definition) => Number(definition.actorId) === actorId);

        return {
            actorId,
            label: actor?.name || fallback?.fallbackLabel || `AKTOR ${actorId}`,
        };
    });
});

const roleCount = computed(() => Math.max(roleColumns.value.length, 1));
const chartMinWidth = computed(() => `${560 + (roleCount.value * 130)}px`);
const chartGridStyle = computed(() => ({
    gridTemplateColumns: `52px minmax(420px, 2fr) minmax(${roleCount.value * 130}px, ${roleCount.value}fr)`,
}));
const roleGridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${roleCount.value}, minmax(130px, 1fr))`,
}));

const isDiagramModalOpen = ref(false);
const isDeleteDiagramModalOpen = ref(false);
const editingDiagramId = ref(null);
const selectedDiagramMapping = ref(null);

const diagramForm = useForm({
    tipe: 'A',
    sop_id: '',
    actor_id: '',
});

const diagramSopOptions = computed(() => {
    return (props.sops || []).filter((item) => item?.tipe === diagramForm.tipe);
});

function getMapList(row) {
    return row?.mapActorSops || row?.map_actor_sops || [];
}

function normalizeMapping(mapping, sop) {
    const actor = mapping?.actor || props.actors.find((item) => Number(item.id) === Number(mapping?.actor_id)) || null;
    const flowType = sop?.tipe || (['A', 'B'].includes(mapping?.tipe) ? mapping.tipe : 'A');

    return {
        ...mapping,
        tipe: flowType,
        flowType,
        sop_id: mapping?.sop_id || sop?.id || '',
        actor_id: mapping?.actor_id || '',
        sopDescription: sop?.description || '-',
        actorName: actor?.name || '-',
    };
}

function mappingType(mapping, row) {
    return row?.tipe || mapping?.flowType || 'A';
}

function hasMapping(row, actorId) {
    return getMapList(row).some((mapping) => {
        return Number(mapping.actor_id) === Number(actorId) && mappingType(mapping, row) === activeFlowType.value;
    });
}

function mappedRoleIndexes(row) {
    return roleColumns.value
        .map((role, index) => (hasMapping(row, role.actorId) ? index : null))
        .filter((index) => index !== null);
}

function setActiveFlowType(tipe) {
    activeFlowType.value = tipe;
    setTimeout(updatePaths, 0);
}

function openAddDiagramModal() {
    editingDiagramId.value = null;
    diagramForm.reset();
    diagramForm.clearErrors();
    diagramForm.tipe = activeFlowType.value;
    diagramForm.sop_id = '';
    diagramForm.actor_id = '';
    isDiagramModalOpen.value = true;
}

function openEditDiagramModal(mapping) {
    if (!mapping?.id) return;
    editingDiagramId.value = mapping.id;
    diagramForm.tipe = mapping.flowType || activeFlowType.value;
    diagramForm.sop_id = mapping.sop_id || '';
    diagramForm.actor_id = mapping.actor_id || '';
    diagramForm.clearErrors();
    isDiagramModalOpen.value = true;
}

function openDeleteDiagramModal(mapping) {
    if (!mapping?.id) return;
    selectedDiagramMapping.value = mapping;
    isDeleteDiagramModalOpen.value = true;
}

function closeDiagramModal() {
    isDiagramModalOpen.value = false;
    editingDiagramId.value = null;
    diagramForm.reset();
}

function handleDiagramTypeChange() {
    const selectedSopExists = diagramSopOptions.value.some((item) => Number(item.id) === Number(diagramForm.sop_id));
    if (!selectedSopExists) {
        diagramForm.sop_id = '';
    }
}

function submitDiagramForm() {
    const savedType = diagramForm.tipe;

    if (editingDiagramId.value) {
        diagramForm.put(route('policy.procedure.diagram.update', editingDiagramId.value), {
            preserveScroll: true,
            onSuccess: () => {
                activeFlowType.value = savedType;
                closeDiagramModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Mapping diagram berhasil diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    } else {
        diagramForm.post(route('policy.procedure.diagram.store'), {
            preserveScroll: true,
            onSuccess: () => {
                activeFlowType.value = savedType;
                closeDiagramModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Mapping diagram berhasil ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    }
}

function submitDeleteDiagram() {
    if (!selectedDiagramMapping.value?.id) return;

    diagramForm.delete(route('policy.procedure.diagram.destroy', selectedDiagramMapping.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteDiagramModalOpen.value = false;
            selectedDiagramMapping.value = null;
            Swal.fire({
                title: 'Dihapus!',
                text: 'Mapping diagram berhasil dihapus.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
    });
}

function shortText(text, length = 90) {
    if (!text) return '-';
    const normalized = String(text).replace(/\s+/g, ' ').trim();
    return normalized.length > length ? `${normalized.slice(0, length)}...` : normalized;
}

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
            const maxSrcBottom = Math.max(...srcCoordsList.map(item => item.coords.bottom));
            const minTgtTop = Math.min(...tgtCoordsList.map(item => item.coords.top));
            const yMid = (maxSrcBottom + minTgtTop) / 2;

            srcCoordsList.forEach(item => {
                newPaths.push({
                    d: `M ${item.coords.centerX} ${item.coords.bottom} L ${item.coords.centerX} ${yMid}`,
                    hasArrow: false,
                });
            });

            tgtCoordsList.forEach(item => {
                newPaths.push({
                    d: `M ${item.coords.centerX} ${yMid} L ${item.coords.centerX} ${item.coords.top}`,
                    hasArrow: true,
                });
            });

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
    setTimeout(updatePaths, 150);

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
