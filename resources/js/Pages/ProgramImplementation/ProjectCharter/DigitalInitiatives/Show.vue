<template>
    <UserLayout :title="`Digital Initiative - ${initiative.code}`">
        <div class="space-y-4 print:space-y-0">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-2 px-3 py-2.5">
                    <Link
                        :href="route('digital-initiatives.index')"
                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-slate-500 dark:text-slate-400"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>

                    <span class="text-slate-300 dark:text-slate-600">|</span>

                    <h1 class="text-xs font-bold text-slate-900 dark:text-white">
                        {{ initiative.code }}
                    </h1>

                    <div class="ml-auto flex items-center gap-1.5">
                        <button v-for="tab in tabs" :key="tab.key" type="button"
                            class="rounded-md px-2.5 py-1 text-[10px] font-semibold"
                            :class="activeTab === tab.key ? 'bg-slate-800 text-white dark:bg-slate-200 dark:text-slate-900' : 'text-slate-500 dark:text-slate-400'"
                            @click="toggleTab(tab.key)">
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Detail panel (Status Implementation) -->
                <div v-if="activeTab === 'detail'" class="border-t border-slate-100 dark:border-white/5">
                    <div class="px-3 py-3">
                        <StatusImplementationTable :projects="[initiative]" codeLabel="Progres status history" />
                    </div>
                </div>
            </section>

            <!-- Project Charter Section -->
            <template v-if="activeTab === 'charter'">
                <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200">Project Charter</h2>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                :href="route('digital-initiatives.edit', initiative.id)"
                                class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700"
                            >
                                Edit Charter
                            </Link>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                                @click="printCharter"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 17h12v3H6v-3Zm-2-2h16a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2Z" />
                                </svg>
                                Print
                            </button>

                            <button
                                type="button"
                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/10"
                                @click="confirmDelete"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </section>

                <main class="print:m-0 print:p-0">
                    <DigitalCharterDocument :initiative="initiative" />
                </main>
            </template>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import DigitalCharterDocument from './Partials/DigitalCharterDocument.vue';
import StatusImplementationTable from '@/Components/ITInitiative/ReviewStatusImplementationTable.vue';

const route = useRouteHelper();

const props = defineProps({
    initiative: Object,
});

const page = usePage();

// --- Tabs ---
const tabs = [
    { key: 'charter', label: 'Project Charter' },
    { key: 'detail', label: 'Status Implementation' },
];

const parseTabFromUrl = () => {
    const query = String(page.url ?? '').split('?')[1] ?? '';
    const params = new URLSearchParams(query);
    const tab = String(params.get('tab') ?? '').trim().toLowerCase();

    if (['detail', 'status', 'implementation'].includes(tab)) {
        return 'detail';
    }

    return 'charter';
};

const activeTab = ref(parseTabFromUrl());
const toggleTab = (key) => {
    activeTab.value = key;
};

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete initiative "${props.initiative.code}"?`)) {
        router.delete(route('digital-initiatives.destroy', props.initiative.id));
    }
};

const printCharter = () => {
    window.print();
};
</script>
