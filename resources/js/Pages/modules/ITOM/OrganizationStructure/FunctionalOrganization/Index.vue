<template>
    <ModulLayout title="Business Process - Organization Structure">
        <div class="animate-fade-in-up space-y-4">
            <div class="mt-4">
                <Deferred :data="['companies', 'bods', 'regulations', 'functionalOrganizations', 'functions']">
                    <template #fallback>
                        <TableSkeleton />
                    </template>
                    <FunctionalOrganization
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
import { watch } from 'vue';
import { usePage, Deferred } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import FunctionalOrganization from '@/Components/modules/ITOM/Organization/FunctionalOrganization/FunctionalOrganizationTable.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';

defineProps({
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
