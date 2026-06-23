<template>
    <UserLayout title="Architecture - Organization Structure">
        <div class="animate-fade-in-up space-y-4">
            <!-- Flash Message Alerts -->
            <div v-if="flash.success" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 shadow-sm animate-fade-in">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 shadow-sm animate-fade-in">
                {{ flash.error }}
            </div>

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

                <OrganizationalStructureTable
                    v-else-if="activeTab === 'organization'"
                    :organization-structure-rows="organizationStructureRows"
                    :groub-options="groubOptions"
                    :companies="companies"
                    :bods="bods"
                />
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CompanyStructure from '@/Components/Architecture/Organization/CompanyStructure.vue';
import BoardOfDirector from '@/Components/Architecture/Organization/BoardOfDirector.vue';
import OrganizationalStructureTable from '@/Components/Architecture/Organization/OrganizationalStructureTable.vue';

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
});

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const tabs = [
    { key: 'company', label: 'Company' },
    { key: 'bod', label: 'BoD' },
    { key: 'organization', label: 'Organization Structure' },
];

const activeTab = ref('company');
</script>