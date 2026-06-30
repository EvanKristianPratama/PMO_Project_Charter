<template>
    <ModulLayout title="Operating Model - IT Management">
        <div class="animate-fade-in-up space-y-2">
            <!-- Diagram Card -->
            <div class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50">
                <div class="overflow-hidden">
                    <EITOrganization :organization-structure-rows="organizationStructureRows" />
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import EITOrganization from '@/Components/OperatingModel/EITOrganization.vue';

defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
});
</script>
