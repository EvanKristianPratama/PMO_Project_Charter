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
                </div>
            </div>

            <section class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
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
                :items="filteredItems"
                :status-options="statusOptions"
                :active-flow-filter="activeFlowFilter"
            />

            <MasterInitiativeTable
                v-if="hasTableSelection && tableMode === TABLE_MODE.MASTER"
                :items="mstInitiativesList"
                :initiative-items="allDigitalInitiatives"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { statusFlowClassByIndex } from "@/Composables/initiativeStatus";
import { useFlowFilter } from "@/Composables/useFlowFilter";
import FlowStatusTable from "@/Components/DigitalInitiative/FlowStatusTable.vue";
import MasterInitiativeTable from "@/Components/DigitalInitiative/MasterInitiativeTable.vue";

const props = defineProps({
    initiatives: Object,
    mstDigitalInitiatives: {
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
};

const tableMode = ref(TABLE_MODE.FLOW);
const hasTableSelection = ref(false);

const showMasterDigitalInitiatives = () => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.MASTER;
    activeFlowFilter.value = null;
};

const handleFlowFilter = (statusId) => {
    hasTableSelection.value = true;
    tableMode.value = TABLE_MODE.FLOW;
    toggleFilter(statusId);
};

const { activeFlowFilter, filteredItems, toggleFilter } = useFlowFilter(
    () => asList(props.initiatives),
    (item) => item.status,
);

const mstInitiativesList = computed(() => {
    return asList(props.mstDigitalInitiatives);
});

const allDigitalInitiatives = computed(() => {
    return asList(props.initiatives);
});

const statusOptions = computed(() => {
    if (props.statusOptions.length > 0) {
        return props.statusOptions;
    }

    return [
        { id: 1, name: "drafting", label: "Drafting" },
        { id: 2, name: "propose", label: "Propose" },
        { id: 3, name: "review", label: "Review" },
        { id: 4, name: "approved", label: "Approved" },
        { id: 5, name: "baseline", label: "Baseline" },
    ];
});

const scopeStatusOrder = ["drafting", "propose", "review", "approved"];
const normalizeStatusName = (value) =>
    String(value ?? "")
        .trim()
        .toLowerCase();

const scopeStatusOptions = computed(() => {
    const sourceOptions = Array.isArray(statusOptions.value)
        ? statusOptions.value
        : [];
    const mappedStatusByName = new Map();

    sourceOptions.forEach((status) => {
        const candidateNames = [
            normalizeStatusName(status?.name),
            normalizeStatusName(status?.label),
        ];

        if (candidateNames.includes("baseline")) {
            return;
        }

        candidateNames.forEach((candidateName) => {
            if (
                scopeStatusOrder.includes(candidateName) &&
                !mappedStatusByName.has(candidateName)
            ) {
                mappedStatusByName.set(candidateName, status);
            }
        });
    });

    const fallbackStatusOptions = [
        { id: 1, name: "drafting", label: "Drafting" },
        { id: 2, name: "propose", label: "Propose" },
        { id: 3, name: "review", label: "Review" },
        { id: 4, name: "approved", label: "Approved" },
    ];

    return scopeStatusOrder.map((statusName, index) => {
        const matchedStatus = mappedStatusByName.get(statusName);
        const fallbackStatus = fallbackStatusOptions[index];

        return {
            id: Number(matchedStatus?.id ?? fallbackStatus.id),
            name: statusName,
            label: fallbackStatus.label,
        };
    });
});

const countInitiativesByStatus = (statusId) => {
    return asList(props.initiatives).filter(
        (item) => Number(item.status) === Number(statusId),
    ).length;
};

const digitalSteps = computed(() => {
    return scopeStatusOptions.value.map((status, index) => {
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
</script>
