<template>
    <ModulLayout :title="layoutTitle">
        <div class="space-y-6">
            <Deferred :data="['policies', 'objectives', 'roles', 'responsibles', 'regulations']">
                <template #fallback>
                    <TableSkeleton />
                </template>

                <!-- Regulation Tabs Switcher -->
                <div class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center -mt-5">
                    <div class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto">
                        <button
                            v-for="reg in regulations"
                            :key="reg.id"
                            type="button"
                            class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                            :class="
                                reg.id === selectedRegulationId
                                    ? 'bg-white text-[#0b2545] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400'
                                    : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                            "
                            @click="switchRegulation(reg.id)"
                        >
                            {{ toTitleCase(reg.judul) }}
                        </button>
                    </div>
                </div>

                <!-- Word-style Navigation & Document Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-6">
                    <!-- Sidebar: MS Word Style Navigation Pane -->
                    <NavigationPane
                        :all-sections="chapters"
                        :active-tab="activeChapter"
                        @update:active-tab="switchChapter"
                    />

                    <!-- Main Document View -->
                    <main class="lg:col-span-8 xl:col-span-9 space-y-6 w-full">
                        <!-- Content Area -->
                        <div class="animate-fade-in-up">
                            <component
                                :is="activeComponent"
                                v-bind="componentProps"
                            />
                        </div>
                    </main>
                </div>
            </Deferred>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Deferred } from '@inertiajs/vue3';
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
    regulations: {
        type: Array,
        default: () => [],
    },
    selectedRegulationId: {
        type: Number,
        required: true,
    },
    activeChapter: {
        type: String,
        required: true,
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

function toTitleCase(str) {
    if (!str) return '';
    return str.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.slice(1).toLowerCase());
}

const activeReg = computed(() => {
    if (!props.regulations) return null;
    return props.regulations.find(r => r.id === props.selectedRegulationId) || props.regulations[0] || null;
});

const layoutTitle = computed(() => {
    const regTitle = activeReg.value ? activeReg.value.judul : 'Policy';
    return `Operating Model - Policy: ${regTitle}`;
});
const activeChapter = ref(props.activeChapter || 'bab1');

watch(() => props.activeChapter, (newVal) => {
    if (newVal) {
        activeChapter.value = newVal;
    }
});
const chapters = [
    { id: 'bab1', label: 'Bab I: Pendahuluan', icon: BookOpenIcon },
    { id: 'bab2', label: 'Bab II: Kebijakan', icon: ShieldCheckIcon },
    { id: 'bab3', label: 'Bab III: Tanggung Jawab', icon: UsersIcon },
    { id: 'bab4', label: 'Bab IV: Penutup', icon: FlagIcon },
];

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

const componentProps = computed(() => {
    return {
        policies: props.policies,
        objectives: props.objectives,
        roles: props.roles,
        responsibles: props.responsibles,
        regulations: props.regulations,
        selectedRegulationId: props.selectedRegulationId,
        readonly: true,
    };
});

function switchRegulation(regId) {
    router.visit(route('itom.operating-model.policy.index', {
        regulation_id: regId,
        chapter: activeChapter.value
    }), {
        preserveScroll: true,
        preserveState: false,
    });
}

function switchChapter(chapterKey) {
    activeChapter.value = chapterKey;
}
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

/* Hide scrollbar for segmented switcher navigation */
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
