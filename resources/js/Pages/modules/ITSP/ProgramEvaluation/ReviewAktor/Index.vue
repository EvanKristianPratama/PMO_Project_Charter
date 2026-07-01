<template>
    <UserLayout title="Review Analysis">
        <div class="space-y-6 animate-fade-in-up">
            <ActorMatrixTable :rows="rows" />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import ActorMatrixTable from '@/Components/modules/ITSP/ProgramEvaluation/ReviewAktor/ActorMatrixTable.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({
            totalOrganizations: 0,
            totalInitiatives: 0,
            totalSponsorAssignments: 0,
            totalOwnerAssignments: 0,
            totalLeaderAssignments: 0,
            totalCrossFunctionAssignments: 0,
        }),
    },
});

const summaryCards = computed(() => [
    {
        label: 'Total Actor',
        value: props.summary.totalOrganizations ?? 0,
        description: 'Jumlah organisasi/jabatan yang tampil pada matriks.',
    },
    {
        label: 'Initiative',
        value: props.summary.totalInitiatives ?? 0,
        description: 'Total inisiatif unik yang terpetakan ke organisasi.',
    },
    {
        label: 'Project Sponsor',
        value: props.summary.totalSponsorAssignments ?? 0,
        description: 'Total keterlibatan organisasi sebagai sponsor.',
    },
    {
        label: 'Project Owner',
        value: props.summary.totalOwnerAssignments ?? 0,
        description: 'Total keterlibatan organisasi sebagai owner.',
    },
    {
        label: 'Project Leader',
        value: props.summary.totalLeaderAssignments ?? 0,
        description: 'Total keterlibatan organisasi sebagai leader.',
    },
    {
        label: 'Cross Function',
        value: props.summary.totalCrossFunctionAssignments ?? 0,
        description: 'Total keterlibatan organisasi lintas fungsi.',
    },
]);
</script>
