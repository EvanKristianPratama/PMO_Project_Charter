<template>
    <UserLayout title="Dokumen Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">

            <!-- Capsule Page Navigation Menu -->
            <div class="flex flex-wrap items-center gap-1.5 rounded-xl bg-slate-100 p-1 dark:bg-white/5 w-fit print:hidden">
                <button
                    @click="activeTab = 'proses-bisnis'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'proses-bisnis'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    Business Process
                </button>
                <button
                    @click="activeTab = 'proses-bisnis-v2'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'proses-bisnis-v2'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    Business Process v2
                </button>
                <button
                    @click="activeTab = 'function'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'function'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    Function
                </button>
                <button
                    @click="activeTab = 'kpi'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'kpi'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    KPI
                </button>
                <button
                    @click="activeTab = 'apqc'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'apqc'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    APQC
                </button>
                <button
                    @click="activeTab = 'regulation-map'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeTab === 'regulation-map'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    Regulation Mapping Function
                </button>
            </div>

            <!-- Table Component -->
            <ProsesBisnisTable
                v-if="activeTab === 'proses-bisnis'"
                :proses-bisnis="prosesBisnis"
                :organizations="organizations"
            />
            <BusinessProcessV2
                v-else-if="activeTab === 'proses-bisnis-v2'"
                :proses-bisnis-v2="prosesBisnisV2"
                :company-options="companyOptions"
                :kpi-list="kpiList"
            />
            <FunctionTable
                v-else-if="activeTab === 'function'"
                :functions="functions"
                :company-options="companyOptions"
                :regulations="regulations"
            />
            <APQCTable
                v-else-if="activeTab === 'apqc'"
                :apqc-list="apqcList"
            />
            <KpiTable
                v-else-if="activeTab === 'kpi'"
                :kpi-list="kpiList"
            />
            <RegulationMap
                v-else-if="activeTab === 'regulation-map'"
                :functions="functions"
                :regulations="regulations"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ProsesBisnisTable from '@/Components/Architecture/ProsesBisnis/ProsesBisnisTable.vue';
import BusinessProcessV2 from '@/Components/Architecture/ProsesBisnis/BusinessProcessV2/BusinessProcessV2.vue';
import FunctionTable from '@/Components/Architecture/ProsesBisnis/Function/FunctionTable.vue';
import APQCTable from '@/Components/Architecture/ProsesBisnis/APQC/APQCTable.vue';
import KpiTable from '@/Components/Architecture/ProsesBisnis/Kpi/KpiTable.vue';
import RegulationMap from '@/Components/Architecture/ProsesBisnis/RegulationMap/RegulationMap.vue';

defineProps({
    prosesBisnis: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        required: true,
    },
    functions: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    apqcList: {
        type: Array,
        default: () => [],
    },
    prosesBisnisV2: {
        type: Array,
        default: () => [],
    },
    kpiList: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref('proses-bisnis');
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
