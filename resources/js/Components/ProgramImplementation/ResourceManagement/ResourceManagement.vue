<template>
    <div class="space-y-6">
        <article
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
        >
            <div
                class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400"
            >
                <div class="flex items-center gap-1.5">
                    <label class="text-[10px]">Code</label>
                    <select
                        v-model="selectedCode"
                        class="min-w-[124px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">Semua</option>
                        <option
                            v-for="option in codeOptions"
                            :key="`code-filter-${option}`"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-[10px]">Project</label>
                    <select
                        v-model="selectedProjectName"
                        class="min-w-[144px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">Semua</option>
                        <option
                            v-for="option in projectNameOptions"
                            :key="`project-filter-${option}`"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-[10px]">Status</label>
                    <select
                        v-model="selectedStatus"
                        class="min-w-[124px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">Semua</option>
                        <option
                            v-for="option in statusOptions"
                            :key="`status-filter-${option.value}`"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table
                    class="w-full border-collapse text-left text-sm"
                >
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr class="border-b border-slate-200 dark:border-white/10">
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
                                class="w-[10%] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Budget
                            </th>
                            <th
                                class="w-[24%] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Key Personnel
                            </th>
                            <th
                                class="w-[30%] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Value Creation
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                        <tr
                            v-for="item in initiativesWithRowspan"
                            :key="item.row_key"
                            class="group transition-colors hover:bg-slate-50/50 dark:hover:bg-white/5"
                        >
                            <!-- IT Architecture Building Block -->
                            <td
                                v-if="item.rowspan > 0"
                                :rowspan="item.rowspan"
                                class="w-[160px] border-r border-slate-200 px-6 py-4 align-top dark:border-white/5"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold text-slate-700 dark:text-slate-200"
                                    >
                                        {{ item.coe_name || "-" }}
                                    </span>
                                </div>
                            </td>

                            <!-- List of IT Initiatives -->
                            <td class="px-6 py-4 align-top border-r border-slate-200 dark:border-white/5">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-start gap-2">
                                        <span
                                            class="inline-flex shrink-0 items-center justify-center rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400"
                                        >
                                            {{ formatProjectCode(item.code || item.project_code) }}
                                        </span>
                                        <span
                                            class="text-xs font-medium text-slate-700 dark:text-slate-200"
                                        >
                                            {{ item.name || item.project_name || "-" }}
                                        </span>
                                    </div>
                                    <span
                                        :class="statusBadgeClass(item.status_id)"
                                        class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                    >
                                        {{ item.status || "-" }}
                                    </span>
                                </div>
                            </td>

                            <!-- Budget -->
                            <td class="px-4 py-4 align-top border-r border-slate-200 dark:border-white/5">
                                <span class="text-xs text-slate-600 dark:text-slate-300 whitespace-pre-line break-words">
                                    {{ item.budget || "-" }}
                                </span>
                            </td>

                            <!-- Key Personnel -->
                            <td class="px-4 py-4 align-top">
                                <span class="text-xs text-slate-600 dark:text-slate-300 whitespace-pre-line break-words">
                                    {{ item.key_personnel_display || item.key_personnel || "-" }}
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top border-l border-slate-200 dark:border-white/5">
                                <span class="text-xs text-slate-600 dark:text-slate-300 whitespace-pre-line break-words">
                                    {{ item.impact_value || "-" }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="filteredRows.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400"
                            >
                                Project Charter Not Available
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    resourceProjects: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            status: "4",
        }),
    },
    filterOptions: {
        type: Object,
        default: () => ({
            statuses: [],
        }),
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

const uniquePreserveOrder = (values) => {
    const seen = new Set();

    return values.filter((value) => {
        const normalized = String(value ?? "").trim();

        if (!normalized || seen.has(normalized)) {
            return false;
        }

        seen.add(normalized);
        return true;
    });
};

const formatProjectCode = (value) => {
    const raw = String(value ?? "").trim();

    if (!raw) {
        return "-";
    }

    const digits = raw.match(/\d+/g)?.join("") ?? "";

    if (!digits) {
        return raw;
    }

    return String(Number(digits));
};

const rows = computed(() =>
    Array.isArray(props.resourceProjects) ? props.resourceProjects : [],
);
const statusOptions = computed(() =>
    Array.isArray(props.filterOptions?.statuses)
        ? props.filterOptions.statuses
        : [],
);
const codeOptions = computed(() =>
    uniquePreserveOrder(rows.value.map((item) => item?.project_code)),
);
const projectNameOptions = computed(() =>
    uniquePreserveOrder(rows.value.map((item) => item?.project_name)),
);
const selectedCode = ref("all");
const selectedProjectName = ref("all");
const selectedStatus = ref(String(props.filters?.status ?? "4"));

const filteredRows = computed(() => {
    return rows.value.filter((item) => {
        if (
            selectedCode.value !== "all" &&
            String(item?.project_code ?? "") !== selectedCode.value
        ) {
            return false;
        }

        if (
            selectedProjectName.value !== "all" &&
            String(item?.project_name ?? "") !== selectedProjectName.value
        ) {
            return false;
        }

        if (
            selectedStatus.value !== "all" &&
            String(item?.status_id ?? "") !== selectedStatus.value
        ) {
            return false;
        }

        return true;
    });
});

const initiativesWithRowspan = computed(() => {
    const initiatives = filteredRows.value;
    const result = [];

    // Sort by coe_name using the predefined coeOrder, then by code
    const sorted = [...initiatives].sort((a, b) => {
        const coeA = String(a.coe_name || "Unassigned");
        const coeB = String(b.coe_name || "Unassigned");

        if (coeA !== coeB) {
            const indexA = coeOrder.indexOf(coeA);
            const indexB = coeOrder.indexOf(coeB);

            if (indexA !== -1 && indexB !== -1) {
                return indexA - indexB;
            }

            if (indexA !== -1) return -1;
            if (indexB !== -1) return 1;

            return coeA.localeCompare(coeB);
        }

        const codeA = String(a.project_code || "");
        const codeB = String(b.project_code || "");
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

const statusMap = {
    1: "bg-slate-100 text-slate-600 ring-1 ring-slate-300",
    2: "bg-blue-100 text-blue-700 ring-1 ring-blue-300",
    3: "bg-amber-100 text-amber-700 ring-1 ring-amber-300",
    5: "bg-purple-100 text-purple-700 ring-1 ring-purple-300",
    4: "bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300",
};

const statusBadgeClass = (statusId) => {
    const key = Number(statusId);

    return statusMap[key] ?? "bg-blue-100 text-blue-700 ring-1 ring-blue-300";
};
</script>
