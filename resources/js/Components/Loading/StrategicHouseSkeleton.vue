<script setup>
</script>

<template>
    <div class="animate-pulse space-y-8 pb-10">
        <!-- Roof Skeleton -->
        <div class="flex flex-col items-center gap-4">
            <div class="h-8 w-64 rounded-lg bg-slate-200 dark:bg-slate-800"></div>
            <div class="flex gap-4">
                <div class="h-24 w-48 rounded-xl bg-slate-100 dark:bg-white/5"></div>
                <div class="h-24 w-48 rounded-xl bg-slate-100 dark:bg-white/5"></div>
            </div>
        </div>

        <!-- Vision Trapezoid Skeleton -->
        <div class="mx-auto h-32 w-full max-w-4xl rounded-[40px] bg-slate-200/50 dark:bg-white/10"></div>

        <!-- Pillars/DTI Section Skeleton -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div v-for="i in 3" :key="i" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-slate-900">
                <div class="h-6 w-32 rounded bg-slate-200 dark:bg-slate-800"></div>
                <div class="space-y-3">
                    <div v-for="j in 4" :key="j" class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-white/5"></div>
                        <div class="h-4 flex-1 rounded bg-slate-100 dark:bg-white/5"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Skeleton (Business Strategy) -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-white/10 dark:bg-slate-900">
            <div class="h-12 bg-slate-50 dark:bg-white/5"></div>
            <div class="p-4 space-y-4">
                <div v-for="i in 5" :key="i" class="flex gap-4">
                    <div class="h-6 w-1/4 rounded bg-slate-100 dark:bg-white/5"></div>
                    <div class="h-6 w-1/4 rounded bg-slate-100 dark:bg-white/5"></div>
                    <div class="h-6 w-1/4 rounded bg-slate-100 dark:bg-white/5"></div>
                    <div class="h-6 w-1/4 rounded bg-slate-100 dark:bg-white/5"></div>
                </div>
            </div>
        </div>
    </div>
</template>
