<template>
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <!-- Groub Filter -->
            <div class="flex-1">
                <label class="space-y-1.5">
                    <select
                        :value="selectedGroubName"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 dark:border-white/10 dark:bg-[#111111] dark:text-slate-200"
                        @change="$emit('update:selected-groub-name', $event.target.value)"
                    >
                        <option value="">Semua Group</option>
                        <option v-for="groubName in groubNames" :key="groubName" :value="groubName">
                            {{ groubName }}
                        </option>
                    </select>
                </label>
            </div>

            <!-- View Mode Toggle & Reset Button -->
            <div class="flex items-end gap-2">
                <div class="flex gap-2">
                    <button
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                            viewMode === 'table'
                                ? 'bg-blue-500 text-white shadow-md hover:bg-blue-600'
                                : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600',
                        ]"
                        @click="$emit('update:view-mode', 'table')"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h18v2H3v-2z" />
                        </svg>
                        Table
                    </button>
                    <button
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                            viewMode === 'tree'
                                ? 'bg-blue-500 text-white shadow-md hover:bg-blue-600'
                                : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600',
                        ]"
                        @click="$emit('update:view-mode', 'tree')"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                        </svg>
                        Tree
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    selectedGroubName: {
        type: String,
        default: '',
    },
    groubNames: {
        type: Array,
        default: () => [],
    },
    viewMode: {
        type: String,
        default: 'table',
    },
});

defineEmits([
    'update:selected-groub-name',
    'update:view-mode',
]);
</script>
