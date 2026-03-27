<template>
    <UserLayout title="Digital Initiatives - Roadmap">
        <div class="animate-fade-in space-y-4">
            <div class="flex w-fit flex-wrap items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.master.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Digital Initiatives List
                </Link>
                <div class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400">
                    Roadmap
                </div>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.compendium.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Compendium List
                </Link>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.appendix.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Appendix List
                </Link>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.mapping.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 transition-all hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Mapping
                </Link>
            </div>

            <DigitalRoadmap
                :data="filteredRoadmapItems"
                :start-year="startYearRange"
                :end-year="endYearRange"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import DigitalRoadmap from '@/Components/Roadmap/Digital/DigitalRoadmapComponentA.vue';

const props = defineProps({
    roadmapItems: {
        type: Array,
        default: () => [],
    },
    availableVersions: {
        type: Array,
        default: () => [],
    },
    selectedVersion: {
        type: String,
        default: 'v1.0',
    },
    startYearRange: {
        type: Number,
        default: 2024,
    },
    endYearRange: {
        type: Number,
        default: 2029,
    },
    usingDummyData: {
        type: Boolean,
        default: false,
    },
});

const versionOptions = computed(() => {
    if (Array.isArray(props.availableVersions) && props.availableVersions.length > 0) {
        return props.availableVersions;
    }

    const versions = new Set(
        (Array.isArray(props.roadmapItems) ? props.roadmapItems : [])
            .map((item) => String(item?.version ?? '').trim())
            .filter(Boolean),
    );

    return versions.size > 0 ? Array.from(versions) : ['v1.0'];
});

const selectedVersionLocal = ref(props.selectedVersion || versionOptions.value[0] || 'v1.0');

watch(versionOptions, (nextVersions) => {
    if (!nextVersions.includes(selectedVersionLocal.value)) {
        selectedVersionLocal.value = nextVersions[0] || 'v1.0';
    }
}, { immediate: true });

const filteredRoadmapItems = computed(() => {
    const items = Array.isArray(props.roadmapItems) ? props.roadmapItems : [];

    return items.filter((item) => String(item?.version ?? 'v1.0').trim() === selectedVersionLocal.value);
});
</script>
