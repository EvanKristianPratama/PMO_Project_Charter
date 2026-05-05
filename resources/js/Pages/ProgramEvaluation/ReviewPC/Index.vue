<template>
    <UserLayout title="Program Evaluation">
        <div class="space-y-6 animate-fade-in-up">
            <section>
                <ReviewPCTable
                    :reviews="trsReviewPCs"
                    @count-change="onCountChange"
                />
            </section>

            <StatusMatrix
                v-if="trsReviewPCs.length > 0"
                :rows="matrixRows"
                groupBy="project_owner"
                label="Project Owner"
            />

            <StatusMatrix
                v-if="trsReviewPCs.length > 0"
                :rows="matrixRows"
                groupBy="project_leader"
                label="Project Leader"
            />

            <!-- Footer Note -->
            <footer class="mt-12 mb-8 flex justify-center border-t border-slate-100 pt-8 dark:border-white/5">
                <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                    TBD dengan IEDCC
                </div>
            </footer>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import ReviewPCTable from '@/Components/ReviewPC/ReviewPCTable.vue';
import StatusMatrix from '@/Components/ProgramEvaluation/ReviewDashboard/StatusMatrix.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    trsReviewPCs: {
        type: Array,
        default: () => [],
    },
});

const matrixRows = computed(() => {
    return props.trsReviewPCs.map(r => ({
        no: r.no,
        project_owner: r.project_owner,
        project_leader: r.project_leader,
        latest_review_status: r.latest_review_status,
        initiative_name: r.initiative?.name
    }));
});

const getCircleColor = (status) => {
    const s = String(status ?? "").trim().toLowerCase();
    if (["on track", "on-track"].includes(s)) return "bg-emerald-500";
    if (["at risk", "at-risk"].includes(s)) return "bg-amber-500";
    if (["delayed"].includes(s)) return "bg-orange-600";
    if (["on progress", "on-progress", "on progres", "in progress"].includes(s)) return "bg-sky-500";
    if (["not start", "not started", "not-started"].includes(s)) return "bg-blue-500";
    if (["not signed", "not-signed"].includes(s)) return "bg-rose-500";
    if (["done", "completed"].includes(s)) return "bg-slate-500";
    return "bg-slate-400";
};

const filteredCount = ref(props.trsReviewPCs.length);

const onCountChange = (count) => {
    filteredCount.value = Number(count);
};

const summaryCounts = computed(() => {
    return props.trsReviewPCs.reduce(
        (acc, review) => {
            const type = Number(review?.initiative?.tipe_initiative);
            if (type === 1) acc.digital += 1;
            if (type === 2) acc.it += 1;
            return acc;
        },
        { digital: 0, it: 0 },
    );
});

</script>
