<template>
    <ModulLayout title="Operating Model - Frameworks">
        <div class="animate-fade-in-up space-y-2 -mx-4 sm:-mx-6 lg:-mx-8 -mt-5">
            <div
                class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto"
                >
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        type="button"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            activeTab === tab.key
                                ? 'bg-white text-[#0b2545] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                        @click="activeTab = tab.key"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <div v-if="activeTab === 'framework'" class="px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-4 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-150 dark:border-zinc-800 pb-3 mb-4">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-zinc-100 flex items-center gap-2">
                            <Squares2X2Icon class="h-5 w-5 text-[#0b2545] dark:text-blue-450" />
                            Operating Model Framework
                        </h2>
                    </div>
                    <div class="flex justify-center overflow-hidden rounded-lg bg-slate-50 dark:bg-zinc-950 p-2 border border-slate-100 dark:border-zinc-800/50">
                        <img 
                            src="/frameworks/framework.jpeg" 
                            alt="Operating Model Framework" 
                            class="max-w-full max-h-[750px] object-contain"
                        />
                    </div>
                </div>
            </div>

            <CobitComponentIndex v-else-if="activeTab === 'cobit'" class="px-4 sm:px-6 lg:px-8" embedded />

            <div v-else-if="activeTab === 'itops'" class="px-4 sm:px-6 lg:px-8">
                <ItOpsIndex embedded />
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref } from "vue";
import ModulLayout from "@/Layouts/ModulLayout.vue";
import CobitComponentIndex from "./CobitComponent/Index.vue";
import ItOpsIndex from "./ItOps/Index.vue";
import { Squares2X2Icon } from "@heroicons/vue/24/outline";

const activeTab = ref("framework");

const tabs = [
    {
        key: "framework",
        label: "Framework",
    },
    {
        key: "itops",
        label: "IT Ops",
    },
    {
        key: "cobit",
        label: "COBIT 2019",
    },
];
</script>
