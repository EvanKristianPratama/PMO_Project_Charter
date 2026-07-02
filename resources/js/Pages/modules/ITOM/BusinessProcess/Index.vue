<template>
    <ModulLayout title="Dokumen Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">
            <APQCTable
                v-if="activeTab === 'apqc'"
                :apqc-list="apqcList"
            />
            <BusinessProcessV2
                v-else-if="activeTab === 'proses-bisnis-v2'"
                :proses-bisnis-v2="prosesBisnisV2"
                :company-options="companyOptions"
                :kpi-list="kpiList"
                :regulations="regulations"
            />
            <FunctionTable
                v-else-if="activeTab === 'function'"
                :functions="functions"
                :company-options="companyOptions"
                :bod-options="bodOptions"
                :regulations="regulations"
            />
            <KpiTable
                v-else-if="activeTab === 'kpi'"
                :kpi-list="kpiList"
                :company-options="companyOptions"
            />
            <RegulationMap
                v-else-if="activeTab === 'regulation-map'"
                :functions="functions"
                :regulations="regulations"
            />
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import BusinessProcessV2 from '@/Components/modules/ITOM/Architecture/ProsesBisnis/BusinessProcessV2/BusinessProcessV2.vue';
import FunctionTable from '@/Components/modules/ITOM/Architecture/ProsesBisnis/Function/FunctionTable.vue';
import APQCTable from '@/Components/modules/ITOM/Architecture/ProsesBisnis/APQC/APQCTable.vue';
import KpiTable from '@/Components/modules/ITOM/Architecture/ProsesBisnis/Kpi/KpiTable.vue';
import RegulationMap from '@/Components/modules/ITOM/Architecture/ProsesBisnis/RegulationMap/RegulationMap.vue';

defineProps({
    apqcList: {
        type: Array,
        default: () => [],
    },
    functions: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    bodOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
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

const page = usePage();

const getTabFromUrl = () => {
    const url = page.url;
    if (url.includes('/business-process/proses-bisnis-v2')) return 'proses-bisnis-v2';
    if (url.includes('/business-process/function')) return 'function';
    if (url.includes('/business-process/kpi')) return 'kpi';
    if (url.includes('/business-process/regulation-mapping')) return 'regulation-map';
    if (url.includes('/business-process/apqc')) return 'apqc';

    const params = new URLSearchParams(url.split('?')[1] || '');
    return params.get('tab') || 'apqc';
};

const activeTab = ref(getTabFromUrl());

watch(() => page.url, () => {
    activeTab.value = getTabFromUrl();
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
