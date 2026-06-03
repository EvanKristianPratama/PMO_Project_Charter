<template>
    <UserLayout title="Policy - EIT Organization Structure">
        <div class="animate-fade-in-up space-y-6">
            <!-- Diagram Card -->
            <div class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-white/10 dark:bg-[#0f0f0f]">
                <div class="min-w-[768px] overflow-hidden">
                    <EITOrganization :organization-structure-rows="organizationStructureRows" />
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import EITOrganization from '@/Components/ITOperatingModel/Organization/EITOrganization.vue';

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
});
</script>
