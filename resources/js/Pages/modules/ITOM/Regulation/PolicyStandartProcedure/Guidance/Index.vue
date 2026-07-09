<template>
    <ModulLayout :title="layoutTitle">
        <div class="space-y-6">
            <Deferred :data="['policies', 'objectives', 'roles', 'responsibles', 'regulations']">
                <template #fallback>
                    <TableSkeleton />
                </template>

                <!-- Navigation Top Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
                    <!-- Back Button -->
                    <Link
                        :href="route('itom.policy.regulation.index')"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali
                    </Link>

                    <!-- Right: Document Switcher + Navigasi Pane Toggle -->
                    <div class="flex items-center gap-3">
                        <!-- Navigasi Pane Toggle -->
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

                        <!-- Fast Document Switcher -->
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilih Dokumen:</span>
                            <div class="relative">
                                <select
                                    :value="selectedRegulationId"
                                    @change="handleFastDocumentSwitch($event.target.value)"
                                    class="appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-8 py-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[240px] max-w-[320px] truncate"
                                >
                                    <option
                                        v-for="reg in regulations"
                                        :key="reg.id"
                                        :value="reg.id"
                                    >
                                        [{{ reg.tipe }}] {{ reg.judul }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Word-style Navigation & Document Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-6">
                    <!-- Sidebar: MS Word Style Navigation Pane -->
                    <NavigationPane
                        v-if="isSidebarVisible"
                        :all-sections="chapters"
                        :active-tab="activeChapter"
                        @update:active-tab="switchChapter"
                    />

                    <!-- Main Document View -->
                    <main :class="isSidebarVisible ? 'lg:col-span-8 xl:col-span-9' : 'lg:col-span-12'" class="space-y-6 w-full">
                        <!-- Content Area -->
                        <div class="animate-fade-in-up">
                            <component :is="activeComponent" v-bind="$props" />
                        </div>
                    </main>
                </div>
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, Deferred } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import NavigationPane from '@/Components/modules/ITOM/Regulation/Procedure/NavigationPane.vue';
import Introduction from '@/Components/modules/ITOM/Regulation/Policy/Introduction.vue';
import General from '@/Components/modules/ITOM/Regulation/Policy/General.vue';
import Role from '@/Components/modules/ITOM/Regulation/Policy/Role.vue';
import Closing from '@/Components/modules/ITOM/Regulation/Policy/Closing.vue';
import { 
    BookOpenIcon, 
    ShieldCheckIcon, 
    UsersIcon, 
    FlagIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    // Combined props from all Policy Guidance pages
    regulations: {
        type: Array,
        default: () => [],
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
    policies: {
        type: Array,
        default: () => [],
    },
    objectives: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
    responsibles: {
        type: Array,
        default: () => [],
    },
});

const isSidebarVisible = ref(true);

const activeChapter = ref(
    route().current('itom.policy.guidance.introduction') ? 'bab1' :
    route().current('itom.policy.general.index') ? 'bab2' :
    route().current('itom.policy.roles.index') ? 'bab3' :
    route().current('itom.policy.guidance.closing') ? 'bab4' : 'bab1'
);

const chapters = [
    { id: 'bab1', label: 'Bab I: Pendahuluan', icon: BookOpenIcon },
    { id: 'bab2', label: 'Bab II: Kebijakan', icon: ShieldCheckIcon },
    { id: 'bab3', label: 'Bab III: Tanggung Jawab', icon: UsersIcon },
    { id: 'bab4', label: 'Bab IV: Penutup', icon: FlagIcon },
];

function handleFastDocumentSwitch(regId) {
    const selectedReg = props.regulations.find(r => r.id === Number(regId));
    if (!selectedReg) return;

    const targetRoute = String(selectedReg.tipe || '').toLowerCase() === 'procedure'
        ? 'itom.policy.regulation.procedure.index'
        : 'itom.policy.general.index';

    router.visit(route(targetRoute, { regulation_id: regId }));
}

function switchChapter(chapterKey) {
    activeChapter.value = chapterKey;
}

const activeComponent = computed(() => {
    switch (activeChapter.value) {
        case 'bab1':
            return Introduction;
        case 'bab2':
            return General;
        case 'bab3':
            return Role;
        case 'bab4':
            return Closing;
        default:
            return Introduction;
    }
});

const activeReg = computed(() => {
    if (!props.regulations) return null;
    return props.regulations.find(r => r.id === props.selectedRegulationId) || props.regulations[0] || null;
});

const layoutTitle = computed(() => {
    const regTitle = activeReg.value ? activeReg.value.judul : 'Policy Guidance';
    return `Policy Guidance: ${regTitle}`;
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
