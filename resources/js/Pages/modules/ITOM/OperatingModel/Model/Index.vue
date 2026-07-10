<template>
    <ModulLayout title="Operating Model - Model Structure">
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

            <div v-if="activeTab === 'model'" class="px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-4 shadow-sm">
                    <div class="flex justify-center overflow-hidden rounded-lg bg-slate-50 dark:bg-zinc-950 p-2 border border-slate-100 dark:border-zinc-800/50">
                        <img 
                            src="/frameworks/framework.jpeg" 
                            alt="Operating Model Structure" 
                            class="max-w-full max-h-[750px] object-contain"
                        />
                    </div>
                </div>
            </div>

            <PlaceholderIndex v-else-if="activeTab === 'framework'" title="Operating Model - Framework" description="Halaman framework untuk modul operating model." />
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref } from "vue";
import ModulLayout from "@/Layouts/ModulLayout.vue";
import PlaceholderIndex from "@/Pages/Placeholder/Index.vue";
import { Squares2X2Icon } from "@heroicons/vue/24/outline";

const activeTab = ref("model");

const tabs = [
    {
        key: "model",
        label: "Model",
    },
    {
        key: "framework",
        label: "Framework",
    },
];
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
