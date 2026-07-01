<template>
    <ModulLayout title="Master Data - Business Capability">
        <div class="animate-fade-in-up space-y-3">
            <section class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Business Capability Maps</h1>
                </div>
            </section>

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
