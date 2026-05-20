<template>
    <UserLayout title="Architecture - Organization Structure">
        <div class="animate-fade-in-up space-y-4">
            <OrganizationStructureFilters
                :selected-groub-name="selectedGroubName"
                :groub-names="groubNames"
                :view-mode="viewMode"
                @update:selected-groub-name="selectedGroubName = $event"
                @update:view-mode="viewMode = $event"
            />

            <!-- Table View -->
            <OrganizationStructureTable
                v-if="viewMode === 'table'"
                :organization-structure-rows="filteredOrganizationStructureRows"
                :groub-options="groubOptions"
            />

            <!-- Tree View -->
            <ThreeView
                v-if="viewMode === 'tree'"
                :organization-structure-rows="filteredOrganizationStructureRows"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import OrganizationStructureFilters from '@/Pages/Architecture/OrganizationStructure/Partials/OrganizationStructureFilters.vue';
import OrganizationStructureTable from '@/Pages/Architecture/OrganizationStructure/Partials/OrganizationStructureTable.vue';
import ThreeView from '@/Components/Architecture/Organization/ThreeView.vue';

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
    groubOptions: {
        type: Array,
        default: () => [],
    },
});

const selectedGroubName = ref('');
const viewMode = ref('table'); // 'table' or 'tree'

const groubNames = computed(() => {
    return [...new Set(
        props.organizationStructureRows
            .map((organizationStructureRow) => organizationStructureRow.groub_name)
            .filter(Boolean),
    )].sort((left, right) => left.localeCompare(right));
});

const matchesGroubFilter = (groubName) => {
    if (!selectedGroubName.value) {
        return true;
    }

    return (groubName ?? '') === selectedGroubName.value;
};

const filteredOrganizationStructureRows = computed(() => {
    return props.organizationStructureRows.filter((organizationStructureRow) => {
        return matchesGroubFilter(organizationStructureRow.groub_name);
    });
});
</script>