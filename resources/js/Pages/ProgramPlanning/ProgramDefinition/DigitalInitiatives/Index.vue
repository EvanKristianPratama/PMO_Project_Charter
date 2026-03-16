<template>
    <UserLayout title="Program Definition Digital Initiatives">
        <div class="animate-fade-in space-y-4">
            <section class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <SummaryCard :total="totalDigitalInitiatives" @create="showCreateModal = true"
                    @show-all="toggleShowAll" />

                <div class="lg:col-span-2">
                    <TimelineFlow :status-counts="statusCounts" :postpone-from-counts="postponeFromCounts"
                        :active-status="activeStatusFilter" @select="toggleStatusFilter" />
                </div>
            </section>

            <!-- Hint when table is hidden -->
            <p v-if="showTable" class="py-4 text-center text-xs text-slate-400 dark:text-slate-500">
                Klik card atau status timeline untuk menampilkan data
            </p>

            <!-- Table shown on click -->
            <MasterInitiativeTable v-else :items="filteredList" :initiative-items="initiativeItemsList" />
        </div>

        <CreateInitiativeModal :show="showCreateModal" :tipe-initiative="1" :coe-options="coeOptions"
            :organization-options="organizationOptions" @close="showCreateModal = false" />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import SummaryCard from '@/Components/DigitalInitiative/SummaryCard.vue';
import TimelineFlow from '@/Components/DigitalInitiative/TimelineFlow.vue';
import MasterInitiativeTable from '@/Components/DigitalInitiative/MasterInitiativeTable.vue';
import CreateInitiativeModal from '@/Components/Initiative/CreateInitiativeModal.vue';

const props = defineProps({
    totalDigitalInitiatives: { type: Number, default: 0 },
    statusOptions: { type: Array, default: () => [] },
    statusCounts: { type: Object, default: () => ({}) },
    postponeFromCounts: { type: Object, default: () => ({}) },
    masterDigitalInitiatives: { type: Array, default: () => [] },
    initiativeItems: { type: Array, default: () => [] },
    coeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
});

const showCreateModal = ref(false);
const showTable = ref(true);
const activeStatusFilter = ref(null);

const masterDigitalList = computed(() => Array.isArray(props.masterDigitalInitiatives) ? props.masterDigitalInitiatives : []);
const initiativeItemsList = computed(() => Array.isArray(props.initiativeItems) ? props.initiativeItems : []);

const normalizeStatus = (val) => String(val ?? '').trim().toLowerCase();

const filteredList = computed(() => {
    if (!activeStatusFilter.value) return masterDigitalList.value;
    const filterKey = normalizeStatus(activeStatusFilter.value);
    return masterDigitalList.value.filter((item) => {
        const latestStatus = normalizeStatus(item?.latest_status?.status ?? item?.status);
        return latestStatus === filterKey;
    });
});

const toggleStatusFilter = (statusKey) => {
    if (activeStatusFilter.value === statusKey) {
        activeStatusFilter.value = null;
        showTable.value = false;
    } else {
        activeStatusFilter.value = statusKey;
        showTable.value = true;
    }
};

const toggleShowAll = () => {
    if (showTable.value && activeStatusFilter.value === null) {
        showTable.value = false;
    } else {
        activeStatusFilter.value = null;
        showTable.value = true;
    }
};
</script>
