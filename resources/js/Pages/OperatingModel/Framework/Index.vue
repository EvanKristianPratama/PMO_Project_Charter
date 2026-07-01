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

            <CobitComponentIndex v-if="activeTab === 'cobit'" class="px-4 sm:px-6 lg:px-8" embedded />

            <div v-else class="px-4 sm:px-6 lg:px-8">
                <ItOpsIndex v-if="activeTab === 'itops'" embedded />
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref } from "vue";
import ModulLayout from "@/Layouts/ModulLayout.vue";
import CobitComponentIndex from "./CobitComponent/Index.vue";
import ItOpsIndex from "./ItOps/Index.vue";

const activeTab = ref("cobit");

const tabs = [
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
