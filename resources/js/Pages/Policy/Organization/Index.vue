<template>
    <ModulLayout title="Policy - Organization Structure">
        <div class="animate-fade-in-up space-y-2">
            <!-- Header & Navigation -->
            <div>
                <div class=" flex flex-wrap items-center gap-2">
                    <button type="button"
                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                        :class="activeTab === 'steering'
                            ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                            : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            " @click="activeTab = 'steering'">
                        IT Steering Committee
                    </button>
                    <button type="button"
                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                        :class="activeTab === 'eit'
                            ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                            : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'
                            " @click="activeTab = 'eit'">
                        Enterprise IT
                    </button>
                </div>
            </div>

            <!-- Diagram Card -->
            <div
                class="relative overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50 ">
                <div class=" overflow-hidden">
                    <EITOrganization v-if="activeTab === 'eit'"
                        :organization-structure-rows="organizationStructureRows" />
                    <ITSteeringComittee v-else-if="activeTab === 'steering'" :steering-rows="steeringRows" :organization-options="organizationOptions" />
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref } from 'vue';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import EITOrganization from '@/Components/ITOperatingModel/Organization/EITOrganization.vue';
import ITSteeringComittee from '@/Components/ITOperatingModel/Organization/ITSteeringComittee.vue';

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
    steeringRows: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

const activeTab = ref('eit');
</script>