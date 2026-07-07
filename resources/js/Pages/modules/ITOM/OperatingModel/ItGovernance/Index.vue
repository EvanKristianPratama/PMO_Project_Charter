<template>
    <ModulLayout title="Operating Model - IT Governance">
        <div class="animate-fade-in-up space-y-2 -mx-4 sm:-mx-6 lg:-mx-8 -mt-5">
            <div
                class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto"
                >
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        type="button"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            activeTab === tab.key
                                ? 'bg-white text-[#0b2545] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                        @click="activeTab = tab.key"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <!-- Steering Committee Tab -->
            <div v-if="activeTab === 'steering'" class="px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50">
                    <div class="overflow-hidden">
                        <Deferred :data="['steeringRows', 'organizationOptions']">
                            <template #fallback>
                                <TableSkeleton />
                            </template>
                            <ITSteeringComittee :steering-rows="steeringRows" :organization-options="organizationOptions" />
                        </Deferred>
                    </div>
                </div>
            </div>

            <!-- Functional Organization Tab -->
            <div v-else-if="activeTab === 'functional'" class="px-4 sm:px-6 lg:px-8">
                <Deferred :data="['companies', 'bods', 'regulations', 'functionalOrganizations', 'functions']">
                    <template #fallback>
                        <TableSkeleton />
                    </template>
                    <FunctionalOrganizationTable
                        :functional-organizations="functionalOrganizations"
                        :regulations="regulations"
                        :companies="companies"
                        :bods="bods"
                        :functions="functions"
                    />
                </Deferred>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage, Deferred } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import ITSteeringComittee from '@/Components/modules/ITOM/OperatingModel/ItGovarnance/ITSteeringComittee.vue';
import FunctionalOrganizationTable from '@/Components/modules/ITOM/Organization/FunctionalOrganization/FunctionalOrganizationTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';

const activeTab = ref('steering');

const tabs = [
    {
        key: 'steering',
        label: 'Steering Committee',
    },
    {
        key: 'functional',
        label: 'Detail IT Steering Committee',
    },
];

defineProps({
    steeringRows: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    functionalOrganizations: {
        type: Array,
        default: () => [],
    },
    functions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

watch(
    () => page.props.flash,
    (flashVal) => {
        if (flashVal?.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: flashVal.success,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 rounded-lg px-4 py-2 text-xs font-semibold shadow-sm transition',
                },
                buttonsStyling: false,
            });
        } else if (flashVal?.error) {
            Swal.fire({
                title: 'Gagal!',
                text: flashVal.error,
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 rounded-lg px-4 py-2 text-xs font-semibold shadow-sm transition',
                },
                buttonsStyling: false,
            });
        }
    },
    { deep: true, immediate: true }
);
</script>

