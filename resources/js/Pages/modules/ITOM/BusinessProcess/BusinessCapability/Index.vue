<template>
    <ModulLayout title="Master Data - Business Capability">
        <div class="animate-fade-in-up space-y-3">
            <BusinessCapabilityTable :business-capabilities="businessCapabilities" />
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import BusinessCapabilityTable from '@/Components/modules/ITOM/BusinessArchitecture/BusinessCapability/BusinessCapabilityTable.vue';

defineProps({
    businessCapabilities: { type: Array, default: () => [] },
});
</script>
