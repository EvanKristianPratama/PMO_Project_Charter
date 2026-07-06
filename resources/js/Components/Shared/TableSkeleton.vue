<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Skeleton Header -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="h-8 w-48 animate-pulse rounded-lg bg-slate-200 dark:bg-white/5"></div>
                <div class="h-8 w-40 animate-pulse rounded-lg bg-slate-200 dark:bg-white/5"></div>
                <div class="h-8 w-40 animate-pulse rounded-lg bg-slate-200 dark:bg-white/5"></div>
                <div class="h-8 w-32 animate-pulse rounded-lg bg-slate-200 dark:bg-white/5"></div>
            </div>
            <div class="h-8 w-36 animate-pulse rounded-lg bg-slate-200 dark:bg-white/5"></div>
        </div>

        <!-- Skeleton Table -->
        <div class="overflow-x-auto">
            <div class="min-w-full space-y-4">
                <!-- Header row -->
                <div class="flex border-b border-slate-100 pb-4 dark:border-white/5">
                    <div class="h-4 w-1/6 animate-pulse rounded bg-slate-200 dark:bg-white/5 mr-4"></div>
                    <div class="h-4 w-1/12 animate-pulse rounded bg-slate-200 dark:bg-white/5 mr-4"></div>
                    <div class="h-4 w-2/5 animate-pulse rounded bg-slate-200 dark:bg-white/5 mr-4"></div>
                    <div class="h-4 w-1/4 animate-pulse rounded bg-slate-200 dark:bg-white/5"></div>
                </div>
                <!-- Data rows -->
                <div v-for="i in 5" :key="i" class="flex items-center border-b border-slate-50 py-4 dark:border-white/5/30">
                    <div class="h-4 w-1/6 animate-pulse rounded bg-slate-100 dark:bg-white/5 mr-4"></div>
                    <div class="h-4 w-1/12 animate-pulse rounded bg-slate-100 dark:bg-white/5 mr-4"></div>
                    <div class="h-6 w-2/5 animate-pulse rounded bg-slate-100 dark:bg-white/5 mr-4"></div>
                    <div class="h-5 w-1/4 animate-pulse rounded bg-slate-100 dark:bg-white/5"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Component is a pure loading skeleton
</script>
