<template>
    <UserLayout title="Review Document">
        <div class="animate-fade-in">
            <!-- Initiative Type Switcher (Sub Capsule Menu) -->
            <div class="mb-4">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                        class="inline-flex items-center rounded-full border px-4 py-1.5 text-xs font-semibold transition-all"
                        :class="activeTab === 'Digital'
                            ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                            : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'"
                        @click="activeTab = 'Digital'">
                        Digital Initiative
                    </button>
                    <button type="button"
                        class="inline-flex items-center rounded-full border px-4 py-1.5 text-xs font-semibold transition-all"
                        :class="activeTab === 'IT'
                            ? 'border-[#1C75BC] bg-[#1C75BC] text-white hover:bg-[#0f63b5]'
                            : 'border-[#1C75BC]/45 bg-[#1C75BC]/10 text-[#1C75BC] hover:bg-[#1C75BC]/20 dark:text-[#7FC0F2]'"
                        @click="activeTab = 'IT'">
                        IT Initiative
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <div v-if="activeTab === 'IT'">
                    <ITDocumentTable :projects="it_projects" />
                </div>
                <div v-else-if="activeTab === 'Digital'">
                    <DigitalDocumentTable :projects="digital_projects" />
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useRouteHelper } from "@/Composables/useRouteHelper";
import UserLayout from '@/Layouts/UserLayout.vue';
import ITDocumentTable from '@/Components/ProgramEvaluation/ReviewDocument/ITInitiative/ITDocumentTable.vue';
import DigitalDocumentTable from '@/Components/ProgramEvaluation/ReviewDocument/DigitalInitiative/DigitalDocumentTable.vue';

const props = defineProps({
    it_projects: {
        type: Array,
        required: true,
    },
    digital_projects: {
        type: Array,
        required: true,
    },
});

const route = useRouteHelper();
const activeTab = ref('IT'); // 'IT' or 'Digital'
</script>
