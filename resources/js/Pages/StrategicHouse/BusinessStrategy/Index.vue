<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="animate-fade-in-up">
            <BusinessStrategy
                :page="page"
                :summary="summary"
                :header-goals="headerGoals"
                :enabler-goals="enablerGoals"
                :groups="groups"
                :strategy-columns="strategyColumns"
                :organization-options="organizationOptions"
                :status-periods="statusPeriods"
            />
        </div>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import BusinessStrategy from '@/Components/StrategicHouse/BusinessStrategy/BusinessStrategy.vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    headerGoals: {
        type: Object,
        default: () => ({}),
    },
    enablerGoals: {
        type: Array,
        default: () => [],
    },
    groups: {
        type: Array,
        default: () => [],
    },
    strategyColumns: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
    statusPeriods: {
        type: Array,
        default: () => [],
    },
});

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (
    props.embedded
        ? {}
        : { title: props.page?.title ?? 'Business Strategy' }
));
</script>
