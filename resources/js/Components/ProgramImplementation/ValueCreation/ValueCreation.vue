<template>
    <div class="space-y-4">
        <div
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <h1 class="mt-4 mb-4 text-center text-lg font-bold">
                    Impact and Value for Pertamina
                </h1>
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr
                            class="border-b border-slate-200 bg-slate-50/50 dark:border-white/5 dark:bg-white/5"
                        >
                            <th
                                class="w-[160px] px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                IT Architecture Building Block
                            </th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                List of IT Initiatives
                            </th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Impact and Value
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                        <tr
                            v-for="ini in initiativesWithRowspan"
                            :key="ini.id"
                            class="group transition-colors hover:bg-slate-50/50 dark:hover:bg-white/5"
                        >
                            <!-- IT Architecture Building Block -->
                            <td
                                v-if="ini.rowspan > 0"
                                :rowspan="ini.rowspan"
                                class="w-[160px] border-r border-slate-200 px-6 py-4 align-top dark:border-white/5"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold text-slate-700 dark:text-slate-200"
                                    >
                                        {{ ini.coe_name || "-" }}
                                    </span>
                                </div>
                            </td>

                            <!-- List of Initiative -->
                            <td class="px-6 py-4 align-top border-r border-slate-200 dark:border-white/5">
                                <div class="flex items-start gap-2">
                                    <span
                                        class="inline-flex shrink-0 items-center justify-center rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400"
                                    >
                                        {{ ini.code }}
                                    </span>
                                    <span
                                        class="text-xs font-medium text-slate-700 dark:text-slate-200"
                                    >
                                        {{ ini.name }}
                                    </span>
                                </div>
                            </td>

                            <!-- Value and Creation (Impact Value) -->
                            <td class="px-6 py-4 align-top">
                                <div class="text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line">
                                    {{ ini.impact_value || "-" }}
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!items.length">
                            <td
                                colspan="3"
                                class="px-6 py-12 text-center"
                            >
                                <div
                                    class="flex flex-col items-center justify-center space-y-2"
                                >
                                    <p
                                        class="text-sm italic text-slate-500 dark:text-slate-400"
                                    >
                                        No data available
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const coeOrder = [
    "User Interface and Experience",
    "Integration and Automation",
    "Business Application System",
    "Infrastructure",
    "Data and Analytics",
    "Cybersecurity",
    "People, Process and Technology",
    "Overall Architecture",
];

const initiativesWithRowspan = computed(() => {
    const initiatives = props.items;
    const result = [];

    // Sort by coe_name using the predefined coeOrder, then by code
    const sorted = [...initiatives].sort((a, b) => {
        const coeA = String(a.coe_name || "Unassigned");
        const coeB = String(b.coe_name || "Unassigned");

        if (coeA !== coeB) {
            const indexA = coeOrder.indexOf(coeA);
            const indexB = coeOrder.indexOf(coeB);

            // If both are in coeOrder, sort by index
            if (indexA !== -1 && indexB !== -1) {
                return indexA - indexB;
            }

            // If only one is in coeOrder, it comes first
            if (indexA !== -1) return -1;
            if (indexB !== -1) return 1;

            // If neither is in coeOrder, sort alphabetically
            return coeA.localeCompare(coeB);
        }

        const codeA = String(a.code || "");
        const codeB = String(b.code || "");
        return codeA.localeCompare(codeB);
    });

    for (let i = 0; i < sorted.length; i++) {
        const currentIni = sorted[i];
        const currentCoe = String(currentIni.coe_name || "Unassigned");

        if (i === 0 || String(sorted[i - 1].coe_name || "Unassigned") !== currentCoe) {
            let rowspan = 1;
            for (let j = i + 1; j < sorted.length; j++) {
                if (String(sorted[j].coe_name || "Unassigned") === currentCoe) {
                    rowspan++;
                } else {
                    break;
                }
            }
            result.push({ ...currentIni, rowspan });
        } else {
            result.push({ ...currentIni, rowspan: 0 });
        }
    }
    return result;
});
</script>
