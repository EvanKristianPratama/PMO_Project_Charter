<script setup>
import { Link } from "@inertiajs/vue3";
import { useRouteHelper } from "@/Composables/useRouteHelper";
import UserLayout from "@/Layouts/UserLayout.vue";
import ItInitiativeRoadmapContent from "@/Components/StrategicHouse/RoadMap/ItInitiativeRoadmapContent.vue";

const route = useRouteHelper();

defineProps({
    groups: { type: Array, default: () => [] },
    startYear: { type: Number, default: 2025 },
    endYear: { type: Number, default: 2029 },
    totalCount: { type: Number, default: 0 },
    milestoneTypeOptions: { type: Array, default: () => [] },
});
</script>

<template>
    <UserLayout title="IT Strategic Initiative Roadmap">
        <div class="space-y-5">
            <section
                class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717]"
            >
                <a
                    :href="route('strategic-house.index')"
                    class="text-sm font-medium text-[#0B2A8A] hover:underline dark:text-[#53BDE6]"
                >
                    ← Kembali ke Strategic House
                </a>
                <Link
                    :href="route('strategic-house.roadmap-summary.index')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#0B2A8A] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#102f95]"
                >
                    📊 Roadmap Summary
                </Link>
            </section>

            <ItInitiativeRoadmapContent
                :groups="groups"
                :start-year="startYear"
                :end-year="endYear"
                :total-count="totalCount"
                :milestone-type-options="milestoneTypeOptions"
            />
        </div>
    </UserLayout>
</template>
