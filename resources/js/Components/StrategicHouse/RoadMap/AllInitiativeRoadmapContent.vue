<script setup>
import { computed } from "vue";
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
</script>

<template>
    <div class="all-roadmap space-y-4">
        <section class="all-roadmap__section">
            <div class="all-roadmap__label">Digital Initiative</div>
            <ItInitiativeRoadmapContent
                :groups="digitalGroups"
                :start-year="startYear"
                :end-year="endYear"
                :total-count="digitalTotalCount"
                group-header-label="COE"
                initiative-header-label="Digital Initiatives"
                :show-controls="false"
                :show-legend="false"
                :show-table-header="false"
                empty-text="Belum ada data roadmap Digital Initiative."
            />
        </section>

        <div class="middle-timeline" :style="{ '--qcount': quarterCount }">
            <table class="middle-timeline__table">
                <colgroup>
                    <col class="col-coe" />
                    <col class="col-initiative" />
                    <col
                        v-for="(_, i) in Array.from({ length: quarterCount })"
                        :key="`mid-q-${i}`"
                        class="col-quarter"
                    />
                </colgroup>
                <thead>
                    <tr>
                        <th colspan="2" class="middle-timeline__title">ALL TIMELINE</th>
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
            <div class="all-roadmap__label">IT Initiative</div>
            <ItInitiativeRoadmapContent
                :groups="itGroups"
                :start-year="startYear"
                :end-year="endYear"
                :total-count="itTotalCount"
                :milestone-type-options="milestoneTypeOptions"
                :show-controls="false"
                :show-legend="false"
                :show-table-header="false"
                empty-text="Belum ada data roadmap IT Strategic Initiative."
            />
        </section>
    </div>
</template>

<style scoped>
.all-roadmap__section {
    border: 1px solid #d9e4ef;
    border-radius: 12px;
    background: #ffffff;
    padding: 12px;
}

.all-roadmap__label {
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #1e3a5f;
    text-transform: uppercase;
}

.middle-timeline {
    border: 1px solid #c9d2dd;
    border-radius: 12px;
    overflow: hidden;
    background: #f8fbff;
}

.middle-timeline__table {
    width: 100%;
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

.col-quarter {
    width: calc(40% / var(--qcount));
}
</style>
