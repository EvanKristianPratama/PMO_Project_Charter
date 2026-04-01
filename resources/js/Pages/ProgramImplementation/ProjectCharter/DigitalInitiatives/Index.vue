<template>
    <UserLayout title="Digital Initiatives">
        <div class="animate-fade-in">
            <div
                class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-slate-900 dark:text-white"
                    >
                        Digital Initiatives
                    </h2>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                            :class="
                                tableMode === TABLE_MODE.FLOW || tableMode === TABLE_MODE.MASTER
                                    ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0b5c9d]'
                                    : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            "
                            @click="showAllProjectCharter"
                        >
                            Project Charter
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                            :class="
                                tableMode === TABLE_MODE.ROADMAP
                                    ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0b5c9d]'
                                    : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            "
                            @click="showRoadmapView"
                        >
                            Roadmap Project Charter
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                            :class="
                                tableMode === TABLE_MODE.IMPLEMENTATION
                                    ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0b5c9d]'
                                    : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            "
                            @click="showImplementationView"
                        >
                            Status Implementation
                        </button>
                    </div>
                </div>
            </div>


            <section
                v-if="tableMode !== TABLE_MODE.ROADMAP && tableMode !== TABLE_MODE.IMPLEMENTATION"
                class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3"
            >
                <div class="flex h-full flex-col gap-3">
                    <article
                        class="relative flex min-h-[140px] flex-1 cursor-pointer flex-col justify-center rounded-2xl border bg-[#1C75BC] border-[#1C75BC] p-4 shadow-[0_4px_16px_rgba(28,117,188,0.3)]"
                        role="button"
                        tabindex="0"
                        @click="showMasterDigitalInitiatives"
                        @keydown.enter.prevent="showMasterDigitalInitiatives"
                        @keydown.space.prevent="showMasterDigitalInitiatives"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-[0.08em] text-white"
                            style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3)"
                        >
                            Total Digital Inisiatif Disetujui
                        </p>
                        <p
                            class="mt-2 flex items-center justify-between text-3xl font-bold text-white"
                            style="text-shadow: 0 2px 6px rgba(0, 0, 0, 0.35)"
                        >
                            <span>{{ totalDigitalApproved }}</span>
                        </p>
                    </article>
                </div>

                <article
                    class="flex flex-col justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-[0_4px_16px_rgba(0,0,0,0.05)] dark:border-white/10 dark:bg-[#171717] lg:col-span-2 space-y-4"
                >
                    <div>
                        <div
                            class="mb-2 flex items-center justify-between gap-2"
                        >
                            <h2
                                class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                Digital Initiatives Implementation Timelines
                                Status (Project Charter)
                            </h2>
                        </div>

                        <div>
                            <div class="grid" :style="gridStyle(digitalSteps)">
                                <div
                                    v-for="(step, index) in digitalSteps"
                                    :key="`step-${step.key}`"
                                    class="relative flex justify-center cursor-pointer group"
                                    @click="handleFlowFilter(step.statusId)"
                                >
                                    <span
                                        class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold transition-all group-hover:ring-2 group-hover:ring-offset-1 group-hover:ring-slate-300"
                                        :class="[
                                            step.circleClass,
                                            activeFlowFilter === step.statusId
                                                ? 'ring-2 ring-offset-2 ring-blue-500 shadow-md transform scale-110'
                                                : '',
                                        ]"
                                    >
                                        {{ step.count }}
                                    </span>
                                    <span
                                        v-if="index < digitalSteps.length - 1"
                                        class="absolute left-1/2 top-1/2 ml-[0.75rem] h-0.5 w-[calc(100%_-_1.5rem)] -translate-y-1/2 rounded-full"
                                        :class="step.lineClass"
                                    ></span>
                                </div>
                            </div>

                            <div
                                class="mt-2 grid gap-1 text-center"
                                :style="gridStyle(digitalSteps)"
                            >
                                <div
                                    v-for="step in digitalSteps"
                                    :key="`label-${step.key}`"
                                >
                                    <p
                                        class="text-[9px] font-semibold text-slate-700 dark:text-slate-200"
                                    >
                                        {{ step.label }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <FlowStatusTable
                v-if="hasTableSelection && tableMode === TABLE_MODE.FLOW"
                :items="filteredItemsList"
                :status-options="statusOptions"
                :active-flow-filter="activeFlowFilter"
            />

            <MasterInitiativeTable
                v-else-if="hasTableSelection && tableMode === TABLE_MODE.MASTER"
                :items="mstInitiativesList"
                :initiative-items="allDigitalInitiatives"
            />

            <section
                v-else-if="hasTableSelection && tableMode === TABLE_MODE.ROADMAP"
                class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/15 dark:bg-[#171717]"
            >
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Feature Roadmap for Digital Initiatives coming soon.
                </p>
            </section>

            <section
                v-else-if="hasTableSelection && tableMode === TABLE_MODE.IMPLEMENTATION"
                class="space-y-4"
            >
                <StatusImplementationTable
                    :items="implementationStatusItems"
                    :organization-options="implementationOrganizationOptions"
                    :initiative-options="implementationInitiativeOptions"
                />
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { statusFlowClassByIndex } from "@/Composables/initiativeStatus";
import { useFlowFilter } from "@/Composables/useFlowFilter";
import FlowStatusTable from "@/Components/DigitalInitiative/FlowStatusTable.vue";
import MasterInitiativeTable from "@/Components/DigitalInitiative/MasterInitiativeTable.vue";
import StatusImplementationTable from "@/Components/DigitalInitiative/StatusImplementationTable.vue";

const props = defineProps({
    initiatives: {
        type: Array,
        default: () => [],
    },
    mstDigitalInitiatives: {
        type: Array,
        default: () => [],
    },
    implementationStatuses: {
        type: Array,
        default: () => [],
    },
    implementationOrganizations: {
        type: Array,
        default: () => [],
    },
    implementationInitiatives: {
        type: Array,
        default: () => [],
    },

    statusOptions: {
        type: Array,
        default: () => [],
    },
    completedStatusId: {
        type: Number,
        default: 5,
    },
    totalDigitalInitiatives: {
        type: Number,
        default: 0,
    },
    totalDigitalApproved: {
        type: Number,
        default: 0,
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
});

const asList = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (value && typeof value === "object") {
        return Object.values(value);
    }

    return [];
};

const TABLE_MODE = {
    FLOW: "flow",
    MASTER: "master",
    ROADMAP: "roadmap",
    IMPLEMENTATION: "implementation",
};

const tableMode = ref(TABLE_MODE.FLOW);
const hasTableSelection = ref(false);
const showAllCharter = ref(false);

const showAllProjectCharter = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.FLOW;
    activeFlowFilter.value = null;
    showAllCharter.value = true;
};

const showMasterDigitalInitiatives = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.MASTER;
    activeFlowFilter.value = null;
};

const showRoadmapView = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.ROADMAP;
    activeFlowFilter.value = null;
};

const showImplementationView = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.IMPLEMENTATION;
    activeFlowFilter.value = null;
};

const handleFlowFilter = (statusId) => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.FLOW;
    showAllCharter.value = false;
    toggleFilter(statusId);
};

const FLOW_NOT_YET_ID = 0;
const FLOW_STATUS_STEPS = [
    { id: FLOW_NOT_YET_ID, name: "not_start", label: "Not Start" },
    { id: 1, name: "drafting", label: "Drafting" },
    { id: 2, name: "propose", label: "Propose" },
    { id: 3, name: "review", label: "Review" },
    { id: 5, name: "baseline", label: "Baseline" },
    { id: 4, name: "approved", label: "Approved" },
];

const normalizeProjectStatusId = (value) => {
    if (value === null || value === "" || value === undefined) {
        return FLOW_NOT_YET_ID;
    }
    const parsed = Number(value);
    return Number.isInteger(parsed) && parsed >= 0 ? parsed : FLOW_NOT_YET_ID;
};

const latestProjectStatusHistory = (item) => {
    const histories =
        item?.project_status_histories ?? item?.projectStatusHistories ?? [];
    return Array.isArray(histories) && histories.length > 0
        ? histories[0]
        : null;
};

const resolvedInitiativeStatusId = (item) => {
    // 1. Check history first (most accurate for transitioned projects)
    const historyStatus = latestProjectStatusHistory(item)?.status;
    if (
        historyStatus !== null &&
        historyStatus !== undefined &&
        historyStatus !== ""
    ) {
        return normalizeProjectStatusId(historyStatus);
    }

    // 2. Fallback to project status (for newly synced projects without history)
    if (item?.status !== null && item?.status !== undefined) {
        return normalizeProjectStatusId(item.status);
    }

    return FLOW_NOT_YET_ID;
};

const { activeFlowFilter, filteredItems, toggleFilter } = useFlowFilter(
    () => asList(props.initiatives),
    (item) => resolvedInitiativeStatusId(item),
);

const filteredItemsList = computed(() => {
    if (showAllCharter.value && activeFlowFilter.value === null) {
        return asList(props.initiatives);
    }
    return filteredItems.value;
});

const mstInitiativesList = computed(() => {
    return asList(props.mstDigitalInitiatives);
});

const allDigitalInitiatives = computed(() => {
    return asList(props.initiatives);
});

const implementationStatusItems = computed(() => {
    return asList(props.implementationStatuses);
});

const implementationOrganizationOptions = computed(() => {
    return asList(props.implementationOrganizations);
});

const implementationInitiativeOptions = computed(() => {
    return asList(props.implementationInitiatives);
});

const statusOptions = computed(() => {
    const sourceOptions = Array.isArray(props.statusOptions)
        ? props.statusOptions
        : [];
    const sourceById = new Map(
        sourceOptions
            .map((status) => [Number(status?.id), status])
            .filter(([id]) => Number.isInteger(id))
    );

    return FLOW_STATUS_STEPS.map((step) => {
        const matched = sourceById.get(step.id);

        return {
            id: step.id,
            name: step.name,
            label: step.label,
            rawName: matched?.name ?? step.name,
        };
    });
});

const countInitiativesByStatus = (statusId) => {
    return asList(props.initiatives).filter(
        (item) => resolvedInitiativeStatusId(item) === Number(statusId),
    ).length;
};

const digitalSteps = computed(() => {
    return statusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = status.name;

        return {
            key,
            statusId: status.id,
            label: status.label,
            count: countInitiativesByStatus(status.id),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
});

const gridStyle = (steps = []) => ({
    gridTemplateColumns: `repeat(${Math.max(steps.length, 1)}, minmax(0, 1fr))`,
});

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const mode = params.get("tableMode");

    if (mode && Object.values(TABLE_MODE).includes(mode)) {
        tableMode.value = mode;
        hasTableSelection.value = true;
        showAllCharter.value = mode === TABLE_MODE.FLOW;
    }
});
</script>
