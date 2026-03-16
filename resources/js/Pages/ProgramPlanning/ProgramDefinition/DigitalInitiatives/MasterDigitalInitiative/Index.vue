<template>
    <UserLayout title="Program Definition Digital Initiatives">
        <div class="animate-fade-in space-y-4">
            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717] w-fit">
                
                <div
                    class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400"
                >
                    Digital Initiatives List
                </div>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/compendium"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Compendium List
                </Link>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/appendix/"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Appendix List
                </Link>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/mapping"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Mapping
                </Link>
            </div>

            <!-- Hint when table is hidden -->
            <p v-if="!showTable" class="py-4 text-center text-xs text-slate-400 dark:text-slate-500">
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
