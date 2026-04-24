<script setup>
import { computed, ref } from "vue";
import { EyeIcon, EyeSlashIcon } from "@heroicons/vue/24/outline";
import ItInitiativeRoadmapContent from "@/Components/StrategicHouse/RoadMap/ItInitiativeRoadmapContent.vue";

const props = defineProps({
    itGroups: { type: Array, default: () => [] },
    digitalGroups: { type: Array, default: () => [] },
    startYear: { type: Number, default: 2024 },
    endYear: { type: Number, default: 2029 },
    itTotalCount: { type: Number, default: 0 },
    digitalTotalCount: { type: Number, default: 0 },
    milestoneTypeOptions: { type: Array, default: () => [] },
});

const years = computed(() => {
    const start = Number(props.startYear) || 2024;
    const end = Number(props.endYear) || start;
    const safeEnd = end >= start ? end : start;

    return Array.from({ length: safeEnd - start + 1 }, (_, i) => start + i);
});

const quarterCount = computed(() => Math.max(years.value.length * 4, 1));
const showFilters = ref(false);
</script>

<template>
    <div class="all-roadmap space-y-4">
        <div class="flex justify-end">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-[#0B2A8A] bg-white px-3 py-1.5 text-[10px] font-bold tracking-wider text-[#0B2A8A] transition hover:bg-[#0B2A8A] hover:text-white dark:border-[#53BDE6] dark:bg-transparent dark:text-[#53BDE6] dark:hover:bg-[#53BDE6] dark:hover:text-[#171717]"
                :title="showFilters ? 'Hide Filters' : 'Show Filters'"
                @click="showFilters = !showFilters"
            >
                <EyeIcon v-if="showFilters" class="h-3.5 w-3.5" />
                <EyeSlashIcon v-else class="h-3.5 w-3.5" />
                {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
            </button>
        </div>

        <section class="all-roadmap__section">
            <ItInitiativeRoadmapContent
                :groups="digitalGroups"
                :start-year="startYear"
                :end-year="endYear"
                :total-count="digitalTotalCount"
                group-header-label="COE"
                initiative-header-label="Digital Initiatives"
                :show-organization-filter="true"
                :show-controls="true"
                :show-filters="showFilters"
                controls-placement="bottom"
                :show-legend="true"
                :show-roadmap-legend="false"
                :show-table-header="false"
                empty-text="Belum ada data roadmap Digital Initiative."
            />
        </section>

        <div class="middle-timeline" :style="{ '--qcount': quarterCount }">
            <table class="middle-timeline__table">
                <colgroup>
                    <col class="col-coe" />
                    <col class="col-initiative" />
                    <col class="col-duration" />
                    <col
                        v-for="(_, i) in Array.from({ length: quarterCount })"
                        :key="`mid-q-${i}`"
                        class="col-quarter"
                    />
                </colgroup>
                <thead>
                    <tr>
                        <th colspan="3" class="middle-timeline__title">ALL TIMELINE</th>
                        <th
                            v-for="year in years"
                            :key="`mid-year-${year}`"
                            colspan="4"
                            class="middle-timeline__year"
                        >
                            {{ year }}
                        </th>
                    </tr>
                </thead>
            </table>
        </div>

        <section class="all-roadmap__section">
            <ItInitiativeRoadmapContent
                :groups="itGroups"
                :start-year="startYear"
                :end-year="endYear"
                :total-count="itTotalCount"
                :milestone-type-options="milestoneTypeOptions"
                :show-controls="true"
                :show-filters="showFilters"
                controls-placement="top"
                :show-legend="true"
                :show-roadmap-legend="false"
                :show-table-header="false"
                empty-text="Belum ada data roadmap IT Strategic Initiative."
            />
        </section>
    </div>
</template>

<style scoped>
.middle-timeline {
    border: 1px solid #c9d2dd;
    border-radius: 12px;
    overflow-x: auto;
    overflow-y: hidden;
    background: #f8fbff;
}

.middle-timeline__table {
    width: 100%;
    min-width: 820px;
    table-layout: fixed;
    border-collapse: collapse;
}

.middle-timeline__table th {
    padding: 10px 8px;
    border: 1px solid #dbe5ef;
}

.middle-timeline__title {
    background: #326eb2;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
}

.middle-timeline__year {
    background: #326eb2;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    text-align: center;
}

.col-coe {
    width: 11%;
}

.col-initiative {
    width: 23%;
}

.col-duration {
    width: 10%;
}

.col-quarter {
    width: calc(40% / var(--qcount));
}
</style>
