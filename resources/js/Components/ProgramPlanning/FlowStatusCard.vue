<template>
    <div class="rounded-lg border border-slate-100 p-3 dark:border-white/5">
        <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">{{ title }}</h2>
        </div>

        <div class="mt-3">
            <div class="grid" :style="gridStyle">
                <div
                    v-for="(step, index) in steps"
                    :key="step.key"
                    class="relative flex justify-center"
                >
                    <span
                        class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold"
                        :class="step.circleClass"
                    >
                        {{ step.count }}
                    </span>
                    <span
                        v-if="index < steps.length - 1"
                        class="absolute left-1/2 top-1/2 ml-[1rem] h-0.5 w-[calc(100%_-_2rem)] -translate-y-1/2 rounded-full"
                        :class="step.lineClass"
                    ></span>
                </div>
            </div>

            <div class="mt-2 grid gap-1 text-center" :style="gridStyle">
                <div v-for="step in steps" :key="`label-${step.key}`">
                    <p class="text-[10px] font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    steps: {
        type: Array,
        default: () => [],
    },
});

const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${Math.max(props.steps.length, 1)}, minmax(0, 1fr))`,
}));
</script>
