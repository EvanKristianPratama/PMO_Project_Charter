<template>
    <UserLayout title="Architecture - Organization Structure">
        <div class="animate-fade-in-up space-y-4">
            <!-- Capsule Navigation Menu -->
            <div class="flex items-center gap-1.5 rounded-xl bg-slate-100 p-1 dark:bg-white/5 w-fit">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    class="rounded-lg px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                    :class="activeTab === tab.key
                        ? 'bg-white text-blue-600 shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Component Views -->
            <div class="mt-4">
                <CompanyStructure
                    v-if="activeTab === 'company'"
                    :companies="companies"
                    :groub-options="groubOptions"
                />

                <BoardOfDirector
                    v-else-if="activeTab === 'bod'"
                    :companies="companies"
                    :bods="bods"
                />

                <StructuralOrganizationalTable
                    v-else-if="activeTab === 'organization'"
                    :organization-structure-rows="organizationStructureRows"
                    :groub-options="groubOptions"
                    :companies="companies"
                    :bods="bods"
                    :sk-organizations="skOrganizations"
                />

                <FunctionalOrganization
                    v-else-if="activeTab === 'functional'"
                    :functional-organizations="functionalOrganizations"
                    :sk-organizations="skOrganizations"
                    :companies="companies"
                    :bods="bods"
                    :functions="functions"
                />

                <SkStructure
                    v-else-if="activeTab === 'sk'"
                    :sk-organizations="skOrganizations"
                />

            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import UserLayout from '@/Layouts/UserLayout.vue';
import CompanyStructure from '@/Components/Architecture/Organization/CompanyStructure.vue';
import BoardOfDirector from '@/Components/Architecture/Organization/BoardOfDirector.vue';
import StructuralOrganizationalTable from '@/Components/Architecture/Organization/StructuralOrganization/StructuralOrganizationalTable.vue';
import SkStructure from '@/Components/Architecture/Organization/SkStructure.vue';
import FunctionalOrganization from '@/Components/Architecture/Organization/FunctionalOrganization/FunctionalOrganizationTable.vue';

defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
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
    skOrganizations: {
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

const tabs = [
    { key: 'company', label: 'Company' },
    { key: 'bod', label: 'BoD' },
    { key: 'organization', label: 'Structural Organization'},
    { key: 'functional', label: 'Functional Organization'},
    { key: 'sk', label: 'SK' },
];

const activeTab = ref('company');
</script>