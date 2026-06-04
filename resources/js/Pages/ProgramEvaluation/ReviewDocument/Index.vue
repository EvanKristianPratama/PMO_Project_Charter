<template>
    <UserLayout title="Review Document">
        <div class="space-y-6">
            <ITDocumentTable :projects="projects" />
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import ITDocumentTable from '@/Components/ProgramEvaluation/ReviewDocument/ITInitiative/ITDocumentTable.vue';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
});
</script>