<template>
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-3">
                <label class="space-y-1.5">
                    <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Search</span>
                    <input
                        :value="search"
                        type="text"
                        placeholder="Cari company, groub, atau organization..."
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 dark:border-white/10 dark:bg-[#111111] dark:text-slate-200"
                        @input="$emit('update:search', $event.target.value)"
                    />
                </label>

                <label class="space-y-1.5">
                    <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Company</span>
                    <select
                        :value="selectedCompanyName"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 dark:border-white/10 dark:bg-[#111111] dark:text-slate-200"
                        @change="$emit('update:selected-company-name', $event.target.value)"
                    >
                        <option value="">Semua Company</option>
                        <option v-for="companyName in companyNames" :key="companyName" :value="companyName">
                            {{ companyName }}
                        </option>
                    </select>
                </label>

                <label class="space-y-1.5">
                    <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Groub</span>
                    <select
                        :value="selectedGroubName"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 dark:border-white/10 dark:bg-[#111111] dark:text-slate-200"
                        @change="$emit('update:selected-groub-name', $event.target.value)"
                    >
                        <option value="">Semua Groub</option>
                        <option v-for="groubName in groubNames" :key="groubName" :value="groubName">
                            {{ groubName }}
                        </option>
                    </select>
                </label>
            </div>

            <button
                type="button"
                class="self-start rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5 lg:self-end"
                @click="$emit('reset')"
            >
                Reset Filter
            </button>
        </div>
    </section>
</template>

<script setup>
defineProps({
    search: {
        type: String,
        default: '',
    },
    selectedCompanyName: {
        type: String,
        default: '',
    },
    selectedGroubName: {
        type: String,
        default: '',
    },
    companyNames: {
        type: Array,
        default: () => [],
    },
    groubNames: {
        type: Array,
        default: () => [],
    },
});

defineEmits([
    'update:search',
    'update:selected-company-name',
    'update:selected-groub-name',
    'reset',
]);
</script>
