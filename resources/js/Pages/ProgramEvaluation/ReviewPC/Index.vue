<template>
    <UserLayout title="Program Evaluation">
        <div class="space-y-6 animate-fade-in-up">
            <section>
                <ReviewPCTable
                    :reviews="trsReviewPCs"
                    @count-change="onCountChange"
                />
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import ReviewPCTable from '@/Components/ReviewPC/ReviewPCTable.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    trsReviewPCs: {
        type: Array,
        default: () => [],
    },
});

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
