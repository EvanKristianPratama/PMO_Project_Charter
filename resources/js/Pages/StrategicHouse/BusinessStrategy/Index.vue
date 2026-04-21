<template>
    <component :is="pageContainer" v-bind="pageContainerProps">
        <div class="space-y-6 animate-fade-in-up">
            <BusinessStrategy
                :page="page"
                :summary="summary"
                :groups="groups"
                :strategy-columns="strategyColumns"
                :organization-options="organizationOptions"
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
});

const pageContainer = computed(() => (props.embedded ? 'div' : UserLayout));
const pageContainerProps = computed(() => (
    props.embedded
        ? {}
        : { title: props.page?.title ?? 'Business Strategy' }
));
</script>
