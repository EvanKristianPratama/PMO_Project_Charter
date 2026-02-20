<template>
    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717] xl:col-span-3">
            <div class="space-y-4">
                <div
                    v-for="item in flowItems"
                    :key="item.key"
                    class="rounded-lg border border-slate-100 p-3 dark:border-white/5"
                >
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">{{ item.title }}</h2>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ legend }}</span>
                    </div>

                    <div class="mt-3">
                        <div
                            class="grid"
                            :style="gridStyle(item.steps)"
                        >
                            <div
                                v-for="(step, index) in item.steps"
                                :key="`${item.key}-${step.key}`"
                                class="relative flex justify-center"
                            >
                                <span
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold"
                                    :class="step.circleClass"
                                >
                                    {{ step.count }}
                                </span>
                                <span
                                    v-if="index < item.steps.length - 1"
                                    class="absolute left-1/2 top-1/2 ml-[1rem] h-0.5 w-[calc(100%_-_2rem)] -translate-y-1/2 rounded-full"
                                    :class="step.lineClass"
                                ></span>
                            </div>
                        </div>

                        <div
                            class="mt-2 grid gap-1 text-center"
                            :style="gridStyle(item.steps)"
                        >
                            <div v-for="step in item.steps" :key="`label-${item.key}-${step.key}`">
                                <p class="text-[10px] font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    digitalSteps: {
        type: Array,
        default: () => [],
    },
    itSteps: {
        type: Array,
        default: () => [],
    },
    legend: {
        type: String,
        default: '',
    },
});

const flowItems = computed(() => [
    {
        key: 'digital',
        title: 'Scope Charter Digital Initiative Status',
        steps: props.digitalSteps,
    },
    {
        key: 'it',
        title: 'Scope Charter IT Initiative Status',
        steps: props.itSteps,
    },
]);

const gridStyle = (steps = []) => ({
    gridTemplateColumns: `repeat(${Math.max(steps.length, 1)}, minmax(0, 1fr))`,
});
</script>
