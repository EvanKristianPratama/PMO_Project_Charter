<template>
    <component :is="activeComponent" v-bind="$props" />
</template>

<script setup>
import { computed } from 'vue';
import Introduction from '@/Components/modules/ITOM/Regulation/Policy/Introduction.vue';
import General from '@/Components/modules/ITOM/Regulation/Policy/General.vue';
import Role from '@/Components/modules/ITOM/Regulation/Policy/Role.vue';
import Closing from '@/Components/modules/ITOM/Regulation/Policy/Closing.vue';

const props = defineProps({
    // Combined props from all Policy Guidance pages
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
    policies: {
        type: Array,
        default: () => [],
    },
    objectives: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
    responsibles: {
        type: Array,
        default: () => [],
    },
});

const activeComponent = computed(() => {
    const currentRoute = route().current();
    if (currentRoute === 'itom.policy.guidance.introduction') {
        return Introduction;
    } else if (currentRoute === 'itom.policy.general.index') {
        return General;
    } else if (currentRoute === 'itom.policy.roles.index') {
        return Role;
    } else if (currentRoute === 'itom.policy.guidance.closing') {
        return Closing;
    }
    // Fallback default
    return Introduction;
});
</script>
