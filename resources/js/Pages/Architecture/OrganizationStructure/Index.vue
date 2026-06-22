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

            <OrganizationalStructureTable
                :organization-structure-rows="organizationStructureRows"
                :groub-options="groubOptions"
                :companies="companies"
                :bods="bods"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
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
</script>