<template>
    <UserLayout title="Policy - EIT Organization Structure">
        <div class="animate-fade-in-up space-y-6">
            <!-- Header Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Struktur Organisasi EIT
                        </h1>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Visualisasi struktur hirarki Enterprise IT mulai dari Holding, SVP, VP, sampai Senior.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Diagram Card -->
            <div class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-white/10 dark:bg-[#0f0f0f]">
                <div class="min-w-[768px] overflow-hidden">
                    <ThreeView :organization-structure-rows="organizationStructureRows" />
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import ThreeView from '@/Components/Architecture/Organization/ThreeView.vue';

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
});
</script>
