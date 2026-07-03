<template>
    <ModulLayout title="Operating Model - Model Structure">
        <div class="animate-fade-in-up space-y-2 -mx-4 sm:-mx-6 lg:-mx-8 -mt-5">
            <div class="px-4 sm:px-6 lg:px-8">
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
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from "@/Layouts/ModulLayout.vue";
import { Squares2X2Icon } from "@heroicons/vue/24/outline";
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
