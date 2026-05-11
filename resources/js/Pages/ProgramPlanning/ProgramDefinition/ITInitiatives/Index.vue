<template>
    <UserLayout title="Program Definition IT Initiatives">
        <div class="animate-fade-in">
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Program Definition IT Initiatives
                </h2>
            </div>

            <section class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <SummaryCard :total="totalItInitiatives" @create="showCreateModal = true" @show-all="toggleShowAll" />

                <div class="lg:col-span-2">
                    <TimelineFlow
                        :status-options="statusOptions"
                        :status-counts="statusCounts"
                        :active-status="activeStatusFilter"
                        @select="toggleStatusFilter"
                    />
                </div>
            </section>

            <!-- Hint when table is hidden -->
            <p
                v-if="!showTable"
                class="py-4 text-center text-xs text-slate-400 dark:text-slate-500"
            >
                Klik card atau status timeline untuk menampilkan data
            </p>

            <!-- Table shown on click -->
            <MasterInitiativeTable v-else :items="filteredList" />
        </div>

        <CreateInitiativeModal
            :show="showCreateModal"
            :tipe-initiative="2"
            :coe-options="coeOptions"
            :organization-options="organizationOptions"
            @close="showCreateModal = false"
        />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import SummaryCard from '@/Components/ITInitiative/SummaryCard.vue';
import TimelineFlow from '@/Components/ITInitiative/TimelineFlow.vue';
import MasterInitiativeTable from '@/Components/ITInitiative/MasterInitiativeTable.vue';
import CreateInitiativeModal from '@/Components/Initiative/CreateInitiativeModal.vue';

const props = defineProps({
    totalItInitiatives: {
        type: Number,
        default: 0,
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
    masterItInitiatives: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

const showCreateModal    = ref(false);
const showTable          = ref(false);
const activeStatusFilter = ref(null);

const masterItList = computed(() => {
    return Array.isArray(props.masterItInitiatives) ? props.masterItInitiatives : [];
});

const normalizeStatus = (val) => String(val ?? '').trim().toLowerCase();

const filteredList = computed(() => {
    if (!activeStatusFilter.value) return masterItList.value;
    const filterKey = normalizeStatus(activeStatusFilter.value);
    return masterItList.value.filter((item) => {
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
