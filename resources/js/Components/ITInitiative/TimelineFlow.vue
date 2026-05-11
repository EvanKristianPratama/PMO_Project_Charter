<template>
    <article class="h-full min-h-[220px] rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-[0_4px_16px_rgba(0,0,0,0.05)] dark:border-white/10 dark:bg-[#171717]">
        <div class="mb-2 flex items-center justify-between gap-2">
            <h2 class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                IT Initiatives Planning Timeline Status
            </h2>
        </div>

        <div>
            <div class="grid" :style="gridStyle">
                <div
                    v-for="(step, index) in steps"
                    :key="`scope-step-${step.key}`"
                    class="relative flex cursor-pointer justify-center"
                    @click="emit('select', step.statusName)"
                >
                    <span
                        class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold transition-transform hover:scale-110"
                        :class="[step.circleClass, activeStatus === step.statusName ? 'ring-2 ring-offset-2 ring-blue-400 dark:ring-offset-[#171717]' : '']"
                    >
                        {{ step.count }}
                    </span>
                    <span
                        v-if="index < steps.length - 1"
                        class="absolute left-1/2 top-1/2 ml-[0.75rem] h-0.5 w-[calc(100%_-_1.5rem)] -translate-y-1/2 rounded-full"
                        :class="step.lineClass"
                    ></span>
                </div>
            </div>

            <div class="mt-2 grid gap-1 text-center" :style="gridStyle">
                <div v-for="step in steps" :key="`scope-label-${step.key}`">
                    <p class="text-[9px] font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                </div>
            </div>
        </div>
    </article>
</template>

<script setup>
import { computed } from 'vue';
import { statusFlowClassByIndex } from '@/Composables/initiativeStatus';

const props = defineProps({
    statusOptions: {
        type: Array,
        default: () => [],
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
    activeStatus: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['select']);

const scopeStatusOrder = ['drafting', 'propose', 'review', 'approved'];
const normalizeStatusName = (value) => String(value ?? '').trim().toLowerCase();

const scopeStatusOptions = computed(() => {
    const sourceOptions = Array.isArray(props.statusOptions) ? props.statusOptions : [];
    const mappedStatusByName = new Map();

    sourceOptions.forEach((status) => {
        const candidateNames = [normalizeStatusName(status?.name), normalizeStatusName(status?.label)];

        if (candidateNames.includes('baseline')) {
            return;
        }

        candidateNames.forEach((candidateName) => {
            if (scopeStatusOrder.includes(candidateName) && !mappedStatusByName.has(candidateName)) {
                mappedStatusByName.set(candidateName, status);
            }
        });
    });

    const fallbackStatusOptions = [
        { id: 1, name: 'drafting', label: 'Drafting' },
        { id: 2, name: 'propose', label: 'Propose' },
        { id: 3, name: 'review', label: 'Review' },
        { id: 4, name: 'approved', label: 'Approved' },
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

const steps = computed(() => {
    const counts = props.statusCounts || {};

    return scopeStatusOptions.value.map((status, index) => {
        const flowClass = statusFlowClassByIndex(index);
        const key = status.name;

        return {
            key,
            label: status.label,
            statusName: status.name,
            count: Number(counts?.[key] ?? 0),
            circleClass: flowClass.circleClass,
            lineClass: flowClass.lineClass,
        };
    });
});

const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${Math.max(steps.value.length, 1)}, minmax(0, 1fr))`,
}));
</script>
