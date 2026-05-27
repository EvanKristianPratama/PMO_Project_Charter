<template>
    <UserLayout title="Review Analysis">
        <div class="space-y-6 animate-fade-in-up">

            <!-- <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="card in summaryCards"
                    :key="card.label"
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
                >
                    <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
                        {{ card.label }}
                    </div>
                    <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">
                        {{ card.value }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ card.description }}
                    </div>
                </div>
            </section> -->

            <ActorMatrixTable :rows="rows" />

            <footer class="mt-12 mb-8 flex justify-center border-t border-slate-100 pt-8 dark:border-white/5">
                <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                    TBD dengan IEDCC
                </div>
            </footer>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import ActorMatrixTable from '@/Components/ProgramEvaluation/ReviewAnalysis/ActorMatrixTable.vue';
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
