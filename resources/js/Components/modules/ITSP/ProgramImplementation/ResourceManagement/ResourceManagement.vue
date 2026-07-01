<template>
    <div class="space-y-6">
        <article
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
        >
            <div
                v-if="statusLegendItems.length > 0"
                class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-slate-200 px-4 py-3 dark:border-white/10"
            >
                <span
                    class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                >
                    Status Implementation:
                </span>

                <button
                    v-for="status in statusLegendItems"
                    :key="`resource-status-${status.value}`"
                    type="button"
                    class="flex items-center gap-1.5 transition-opacity"
                    :class="{ 'opacity-40': selectedImplementationStatus !== 'all' && selectedImplementationStatus !== status.value }"
                    :title="`Filter: ${status.label}`"
                    @click="toggleStatusFilter(status.value)"
                >
                    <span
                        class="h-3 w-3 rounded-sm shadow-sm"
                        :class="status.class"
                    ></span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        {{ status.label }}
                        <span class="font-medium text-slate-400 dark:text-slate-500">
                            ({{ status.count }})
                        </span>
                    </span>
                </button>

                <div
                    class="flex items-center gap-1.5 border-l border-slate-200 pl-4 dark:border-white/10"
                >
                    <span
                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                    >
                        Total Project:
                    </span>
                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200">
                        {{ totalProjectsCount }}
                    </span>
                </div>
            </div>

            <div
                class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:text-slate-400"
            >
                <div class="flex items-center gap-1.5">
                    <label class="text-[10px]">Project</label>
                    <select
                        v-model="selectedProjectName"
                        class="min-w-[144px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">All</option>
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
                        v-model="selectedImplementationStatus"
                        class="min-w-[124px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">All</option>
                        <option
                            v-for="option in statusFilterOptions"
                            :key="`resource-status-filter-${option.value}`"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-[10px]">Version</label>
                    <select
                        v-model="selectedVersion"
                        class="min-w-[124px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">All</option>
                        <option
                            v-for="option in versionFilterOptions"
                            :key="`resource-version-filter-${option.value}`"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-[10px]">Period</label>
                    <select
                        v-model="selectedPeriod"
                        class="min-w-[124px] rounded border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200"
                    >
                        <option value="all">All (Latest)</option>
                        <option
                            v-for="period in periodOptions"
                            :key="`resource-period-filter-${period}`"
                            :value="period"
                        >
                            {{ period }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
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
                                class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Periode Status
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
                            <td
                                v-if="item.rowspan > 0"
                                :rowspan="item.rowspan"
                                class="w-[160px] border-r border-slate-200 px-6 py-4 align-top dark:border-white/5"
                            >
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                        {{ item.coe_name || "-" }}
                                    </span>
                                </div>
                            </td>

                            <td class="border-r border-slate-200 px-6 py-4 align-top dark:border-white/5">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-start gap-2">
                                        <span
                                            class="inline-flex shrink-0 items-center justify-center rounded px-1.5 py-0.5 text-[9px] font-bold ring-1 ring-inset"
                                            :class="projectCodeStatusClass(getProjectStatusEntry(item, selectedPeriod).status)"
                                        >
                                            {{ formatProjectCode(item.code || item.project_code) }}
                                        </span>
                                        <span class="text-xs font-medium text-slate-700 dark:text-slate-200">
                                            {{ item.name || item.project_name || "-" }}
                                        </span>
                                    </div>
                                    <span
                                        v-if="item.version_status_label"
                                        class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="versionStatusClass(item.version_status)"
                                    >
                                        {{ item.version_status_label }}
                                    </span>
                                </div>
                            </td>

                            <td class="border-r border-slate-200 px-6 py-4 align-top dark:border-white/5">
                                <div class="text-xs text-slate-700 dark:text-slate-200">
                                    {{ getProjectStatusEntry(item, selectedPeriod).period || "-" }}
                                </div>
                            </td>

                            <td class="border-r border-slate-200 px-4 py-4 align-top dark:border-white/5">
                                <span class="whitespace-pre-line break-words text-xs text-slate-600 dark:text-slate-300">
                                    {{ item.budget || "-" }}
                                </span>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <span class="whitespace-pre-line break-words text-xs text-slate-600 dark:text-slate-300">
                                    {{ item.key_personnel_display || item.key_personnel || "-" }}
                                </span>
                            </td>

                            <td class="border-l border-slate-200 px-4 py-4 align-top dark:border-white/5">
                                <span class="whitespace-pre-line break-words text-xs text-slate-600 dark:text-slate-300">
                                    {{ item.impact_value || "-" }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="filteredRows.length === 0">
                            <td
                                colspan="6"
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
            project: "all",
            status: "all",
            version: "all",
        }),
    },
    filterOptions: {
        type: Object,
        default: () => ({
            statuses: [],
            versions: [],
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

const normalizeStatus = (status) => String(status ?? "").trim();
const normalizeStatusKey = (status) => normalizeStatus(status).toLowerCase();
const canonicalStatus = (status) => {
    const raw = normalizeStatus(status);
    const key = normalizeStatusKey(raw);

    if (!key) {
        return "";
    }

    if (key.includes("on track")) return "On Track";
    if (key.includes("at risk")) return "At Risk";
    if (key.includes("not signed")) return "Not Signed";
    if (key.includes("not started")) return "Not Started";
    if (key.includes("done") || key.includes("complete")) return "Done";

    return raw;
};

const implementationStatusClasses = {
    "On Track": "bg-emerald-500",
    "At Risk": "bg-amber-500",
    "Not Signed": "bg-rose-500",
    "Not Started": "bg-blue-500",
    "Done": "bg-slate-500",
};

const implementationStatusClass = (status) => {
    const normalized = canonicalStatus(status);

    return implementationStatusClasses[normalized] ?? "bg-slate-400";
};

const versionStatusClasses = {
    4: "bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300",
    5: "bg-purple-100 text-purple-700 ring-1 ring-purple-300",
};

const versionStatusClass = (status) => {
    const key = Number(status);

    return versionStatusClasses[key] ?? "bg-blue-100 text-blue-700 ring-1 ring-blue-300";
};

const projectCodeStatusClasses = {
    "On Track": "bg-emerald-500 text-white ring-emerald-500 dark:bg-emerald-500 dark:text-white dark:ring-emerald-400/60",
    "At Risk": "bg-amber-500 text-white ring-amber-500 dark:bg-amber-500 dark:text-white dark:ring-amber-400/60",
    "Not Signed": "bg-rose-500 text-white ring-rose-500 dark:bg-rose-500 dark:text-white dark:ring-rose-400/60",
    "Not Started": "bg-blue-500 text-white ring-blue-500 dark:bg-blue-500 dark:text-white dark:ring-blue-400/60",
    "Done": "bg-slate-500 text-white ring-slate-500 dark:bg-slate-500 dark:text-white dark:ring-slate-400/60",
};

const projectCodeStatusClass = (status) => {
    const normalized = normalizeStatus(status);

    return projectCodeStatusClasses[normalized] ??
        "bg-slate-100 text-slate-700 ring-slate-300 dark:bg-white/10 dark:text-slate-200 dark:ring-white/15";
};

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

const statusFilterOptions = computed(() =>
    Array.isArray(props.filterOptions?.statuses) && props.filterOptions.statuses.length > 0
        ? props.filterOptions.statuses
        : [
            { value: "On Track", label: "On Track" },
            { value: "At Risk", label: "At Risk" },
            { value: "Not Signed", label: "Not Signed" },
            { value: "Not Started", label: "Not Started" },
            { value: "Done", label: "Done" },
        ],
);

const versionFilterOptions = computed(() =>
    Array.isArray(props.filterOptions?.versions) && props.filterOptions.versions.length > 0
        ? props.filterOptions.versions
        : [
            { value: "4", label: "Approved" },
            { value: "5", label: "Baseline" },
        ],
);

const projectNameOptions = computed(() =>
    uniquePreserveOrder(rows.value.map((item) => item?.project_name)),
);

const selectedProjectName = ref(String(props.filters?.project ?? "all"));
const selectedImplementationStatus = ref(String(props.filters?.status ?? "all"));
const selectedVersion = ref(String(props.filters?.version ?? "all"));
const selectedPeriod = ref("all");

const periodOrderValue = (periodLabel) => {
    const normalized = String(periodLabel ?? "").trim();
    if (!normalized) return 0;

    const parts = normalized.split(/\s+/);
    const year = Number(parts[parts.length - 1]);
    const monthName = parts.slice(0, -1).join(" ").trim();

    const monthOrderMap = {
        Januari: 1,
        Februari: 2,
        Maret: 3,
        April: 4,
        Mei: 5,
        Juni: 6,
        Juli: 7,
        Agustus: 8,
        September: 9,
        Oktober: 10,
        November: 11,
        Desember: 12,
    };

    const monthOrder = monthOrderMap[monthName] ?? 0;

    if (!Number.isFinite(year)) {
        return monthOrder;
    }

    return year * 100 + monthOrder;
};

const periodOptions = computed(() => {
    const map = new Map();

    rows.value.forEach((row) => {
        const logs = Array.isArray(row.status_logs) ? row.status_logs : [];
        logs.forEach((log) => {
            const value = String(log.period ?? "").trim();
            if (value && !map.has(value)) {
                map.set(value, value);
            }
        });
    });

    return Array.from(map.values()).sort((a, b) => periodOrderValue(b) - periodOrderValue(a));
});

const getProjectStatusEntry = (item, period) => {
    const logs = Array.isArray(item.status_logs) ? item.status_logs : [];

    if (period === "all") {
        if (logs.length > 0) {
            let latestLog = logs[0];
            let maxOrder = periodOrderValue(latestLog.period);

            for (let i = 1; i < logs.length; i++) {
                const currentOrder = periodOrderValue(logs[i].period);
                if (currentOrder > maxOrder) {
                    maxOrder = currentOrder;
                    latestLog = logs[i];
                }
            }
            return latestLog;
        }

        return {
            status: item.latest_implementation_status,
            month: item.latest_pc_month,
            year: item.latest_pc_year,
            period:
                item.latest_pc_month && item.latest_pc_year
                    ? `${item.latest_pc_month} ${item.latest_pc_year}`
                    : null,
        };
    }

    for (let index = logs.length - 1; index >= 0; index -= 1) {
        const log = logs[index];
        if (String(log?.period ?? "").trim() === period) {
            return log;
        }
    }

    return null;
};

const baseFilteredRows = computed(() => {
    return rows.value.filter((item) => {
        if (
            selectedProjectName.value !== "all" &&
            String(item?.project_name ?? "") !== selectedProjectName.value
        ) {
            return false;
        }

        return true;
    });
});

const filteredRows = computed(() =>
    baseFilteredRows.value.filter((item) => {
        const statusEntry = getProjectStatusEntry(item, selectedPeriod.value);

        if (selectedPeriod.value !== "all" && !statusEntry) {
            return false;
        }

        if (
            selectedImplementationStatus.value !== "all" &&
            canonicalStatus(statusEntry?.status) !== selectedImplementationStatus.value
        ) {
            return false;
        }

        if (
            selectedVersion.value !== "all" &&
            String(item?.version_status ?? "") !== selectedVersion.value
        ) {
            return false;
        }

        return true;
    }),
);

const toggleStatusFilter = (statusValue) => {
    const nextValue = String(statusValue ?? "");

    selectedImplementationStatus.value =
        selectedImplementationStatus.value === nextValue ? "all" : nextValue;
};

const statusLegendItems = computed(() => {
    const counts = Object.fromEntries(
        statusFilterOptions.value.map((item) => [String(item.value ?? "").trim(), 0]),
    );

    const seenProjects = new Set();

    baseFilteredRows.value.forEach((item) => {
        const projectKey = String(item?.project_code || item?.project_name || item?.name || "").trim();
        if (!projectKey || seenProjects.has(projectKey)) {
            return;
        }

        const statusEntry = getProjectStatusEntry(item, selectedPeriod.value);
        if (!statusEntry) return;

        const key = canonicalStatus(statusEntry.status);
        if (!key || !(key in counts)) {
            return;
        }

        counts[key] += 1;
        seenProjects.add(projectKey);
    });

    return statusFilterOptions.value.map((item) => ({
        ...item,
        count: counts[String(item.value ?? "").trim()] ?? 0,
        class: implementationStatusClass(item.value),
    }));
});

const totalProjectsCount = computed(() => {
    return statusLegendItems.value.reduce((acc, item) => acc + item.count, 0);
});

const initiativesWithRowspan = computed(() => {
    const initiatives = filteredRows.value;
    const result = [];

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
        if (codeA !== codeB) {
            return codeA.localeCompare(codeB);
        }

        return Number(b.id || 0) - Number(a.id || 0);
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
