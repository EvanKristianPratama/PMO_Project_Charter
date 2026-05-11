<template>
    <UserLayout title="Architecture - Organization Structure">
        <div class="animate-fade-in-up space-y-4">
            <OrganizationStructureFilters
                :search="search"
                :selected-company-name="selectedCompanyName"
                :selected-groub-name="selectedGroubName"
                :company-names="companyNames"
                :groub-names="groubNames"
                @update:search="search = $event"
                @update:selected-company-name="selectedCompanyName = $event"
                @update:selected-groub-name="selectedGroubName = $event"
                @reset="resetFilters"
            />

            <OrganizationStructureTable :organization-structure-rows="filteredOrganizationStructureRows" />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import OrganizationStructureFilters from '@/Pages/Architecture/OrganizationStructure/Partials/OrganizationStructureFilters.vue';
import OrganizationStructureTable from '@/Pages/Architecture/OrganizationStructure/Partials/OrganizationStructureTable.vue';

const props = defineProps({
    organizationStructureRows: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const selectedCompanyName = ref('');
const selectedGroubName = ref('');

const normalizeText = (value) => String(value ?? '').trim().toLowerCase();

const companyNames = computed(() => {
    return [...new Set(
        props.organizationStructureRows
            .map((organizationStructureRow) => organizationStructureRow.company_name)
            .filter(Boolean),
    )].sort((left, right) => left.localeCompare(right));
});

const groubNames = computed(() => {
    return [...new Set(
        props.organizationStructureRows
            .filter((organizationStructureRow) => {
                if (!selectedCompanyName.value) {
                    return true;
                }

                return organizationStructureRow.company_name === selectedCompanyName.value;
            })
            .map((organizationStructureRow) => organizationStructureRow.groub_name)
            .filter(Boolean),
    )].sort((left, right) => left.localeCompare(right));
});

const searchKeyword = computed(() => normalizeText(search.value));

const matchesCompanyFilter = (companyName) => {
    if (!selectedCompanyName.value) {
        return true;
    }

    return (companyName ?? '') === selectedCompanyName.value;
};

const matchesGroubFilter = (groubName) => {
    if (!selectedGroubName.value) {
        return true;
    }

    return (groubName ?? '') === selectedGroubName.value;
};

const matchesSearch = (...values) => {
    if (!searchKeyword.value) {
        return true;
    }

    return values.some((value) => normalizeText(value).includes(searchKeyword.value));
};

const filteredOrganizationStructureRows = computed(() => {
    return props.organizationStructureRows.filter((organizationStructureRow) => {
        return matchesCompanyFilter(organizationStructureRow.company_name)
            && matchesGroubFilter(organizationStructureRow.groub_name)
            && matchesSearch(
                organizationStructureRow.company_name,
                organizationStructureRow.groub_name,
                organizationStructureRow.organization_name,
            );
    });
});

const resetFilters = () => {
    search.value = '';
    selectedCompanyName.value = '';
    selectedGroubName.value = '';
};

watch(selectedCompanyName, () => {
    if (selectedGroubName.value && !groubNames.value.includes(selectedGroubName.value)) {
        selectedGroubName.value = '';
    }
});
</script>
