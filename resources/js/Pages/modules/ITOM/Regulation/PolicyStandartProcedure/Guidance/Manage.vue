<template>
    <ModulLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 dark:bg-[#171717] dark:border-white/10 shadow-sm">
                <Link :href="route('itom.policy.general.index', { regulation_id: selectedRegulationId })"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali ke Dokumen
                </Link>

                <button
                    @click="isSidebarVisible = !isSidebarVisible"
                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-slate-200 dark:border-white/10 bg-transparent rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition active:scale-95 cursor-pointer"
                >
                    <span
                        class="w-1.5 h-1.5 rounded-full animate-pulse"
                        :class="isSidebarVisible ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-zinc-700'"
                    ></span>
                    Navigasi Pane
                </button>
            </div>

            <!-- Workspace Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Navigation Sidebar -->
                <NavigationPane
                    v-slot:default
                    v-if="isSidebarVisible"
                    :all-sections="navigationSections"
                    v-model:active-tab="activeTab"
                    :active-sub-id="null"
                />

                <!-- Main Editor/Table Content -->
                <div :class="isSidebarVisible ? 'lg:col-span-8 xl:col-span-9' : 'lg:col-span-12'" class="space-y-6">
                    <!-- Navigation Tabs (Only visible when Navigasi Pane is hidden) -->
                    <div v-if="!isSidebarVisible" class="border-b border-slate-200 dark:border-white/10 flex gap-6 px-1">
                        <button @click="activeTab = 'general'" :class="[
                            'pb-3 text-sm font-bold border-b-2 transition-all duration-150',
                            activeTab === 'general'
                                ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        ]">
                            Kebijakan Umum
                        </button>
                        <button @click="activeTab = 'specific'" :class="[
                            'pb-3 text-sm font-bold border-b-2 transition-all duration-150',
                            activeTab === 'specific'
                                ? 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
                        ]">
                            Kebijakan Khusus
                        </button>
                    </div>

                    <!-- BAB I: Pendahuluan (hardcoded component) -->
                    <div v-if="activeTab === 'introduction'" class="animate-fade-in-up">
                        <Introduction :regulations="regulations"
                            :selected-regulation-id="selectedRegulationId" />
                    </div>

                    <!-- Deferred Loading Table/Card Wrapper (BAB II) -->
                    <Deferred v-else-if="activeTab === 'general' || activeTab === 'specific'" :data="['policies', 'regulations', 'objectives']">
                        <template #fallback>
                            <TableSkeleton />
                        </template>
                        <div v-if="activeTab === 'general'" class="animate-fade-in-up">
                            <ManageGeneral :policies="policies" :regulations="regulations"
                                :selected-regulation-id="selectedRegulationId" />
                        </div>
                        <div v-else-if="activeTab === 'specific'" class="animate-fade-in-up">
                            <ManageSpecifik :objectives="objectives" :regulations="regulations"
                                :selected-regulation-id="selectedRegulationId" />
                        </div>
                    </Deferred>

                    <!-- BAB IV: Penutup (hardcoded component) -->
                    <div v-else-if="activeTab === 'closing'" class="animate-fade-in-up">
                        <Closing :regulations="regulations"
                            :selected-regulation-id="selectedRegulationId" />
                    </div>
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Link, Deferred, router } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import NavigationPane from '@/Components/modules/ITOM/Regulation/Procedure/NavigationPane.vue';
import ManageGeneral from '@/Components/modules/ITOM/Regulation/Policy/General/ManageGeneral.vue';
import ManageSpecifik from '@/Components/modules/ITOM/Regulation/Policy/General/ManageSpecifik.vue';
import Introduction from '@/Components/modules/ITOM/Regulation/Policy/Introduction.vue';
import Closing from '@/Components/modules/ITOM/Regulation/Policy/Closing.vue';

const props = defineProps({
    policies: {
        type: Array,
        default: () => [],
    },
    objectives: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    selectedRegulationId: {
        type: [Number, String],
        default: null,
    },
    activeTab: {
        type: String,
        default: 'general',
    }
});

const activeTab = ref(props.activeTab);
const isSidebarVisible = ref(true);

const pageTitle = computed(() => {
    switch (activeTab.value) {
        case 'introduction': return 'BAB I: Pendahuluan';
        case 'general': return 'Kelola Kebijakan Umum';
        case 'specific': return 'Kelola Kebijakan Khusus';
        case 'roles': return 'Tugas, Tanggung Jawab & Wewenang';
        case 'closing': return 'Penutup';
        default: return 'Kelola Kebijakan';
    }
});

const navigationSections = [
    { id: 'introduction', label: 'BAB I. PENDAHULUAN', targetTab: 'introduction' },
    {
        id: 'bab_2_kebijakan',
        label: 'BAB II. KEBIJAKAN',
        children: [
            { id: 'general', label: 'A. Kebijakan Umum', targetTab: 'general' },
            { id: 'specific', label: 'B. Kebijakan Khusus', targetTab: 'specific' }
        ]
    },
    { id: 'roles', label: 'BAB III. TUGAS, TANGGUNG JAWAB & WEWENANG', targetTab: 'roles' },
    { id: 'closing', label: 'BAB IV. PENUTUP', targetTab: 'closing' }
];

// Handle sidebar selection redirects
watch(activeTab, (newVal) => {
    if (newVal === 'roles') {
        router.visit(route('itom.policy.roles.manage', { regulation_id: props.selectedRegulationId }));
    } else if (newVal === 'bab_2_kebijakan') {
        activeTab.value = 'general';
    }
});
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
