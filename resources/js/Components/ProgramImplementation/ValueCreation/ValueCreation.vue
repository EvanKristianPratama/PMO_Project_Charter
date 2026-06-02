<template>
    <div class="space-y-4">
        <div
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900"
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
                    :key="`value-implementation-status-${status.value}`"
                    type="button"
                    class="flex items-center gap-1.5 transition-opacity"
                    :class="{ 'opacity-40': selectedImplementationStatus !== 'all' && selectedImplementationStatus !== status.value }"
                    :title="`Filter: ${status.label}`"
                    @click="toggleImplementationStatusFilter(status.value)"
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
                            :key="`value-project-filter-${option}`"
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
                            :key="`value-implementation-status-filter-${option.value}`"
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
                            :key="`value-version-filter-${option.value}`"
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
                            v-for="option in periodOptions"
                            :key="`value-period-filter-${option}`"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
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
                                Periode Status
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
                            :key="ini.row_key || ini.id"
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
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-start gap-2">
                                        <span
                                            class="inline-flex shrink-0 items-center justify-center rounded px-1.5 py-0.5 text-[9px] font-bold ring-1 ring-inset"
                                            :class="projectCodeStatusClass(getProjectStatusEntry(ini, selectedPeriod).status)"
                                        >
                                            {{ ini.code }}
                                        </span>
                                        <span
                                            class="text-xs font-medium text-slate-700 dark:text-slate-200"
                                        >
                                            {{ ini.name }}
                                        </span>
                                    </div>
                                    <span
                                        v-if="ini.version_status_label"
                                        class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="versionStatusClass(ini.version_status)"
                                    >
                                        {{ ini.version_status_label }}
                                    </span>
                                </div>
                            </td>

                            <!-- Periode Status -->
                            <td class="px-6 py-4 align-top border-r border-slate-200 dark:border-white/5">
                                <div class="text-xs text-slate-700 dark:text-slate-200">
                                    {{ getProjectStatusEntry(ini, selectedPeriod).period || "-" }}
                                </div>
                            </td>

                            <!-- Value and Creation (Impact Value) -->
                            <td class="px-6 py-4 align-top">
                                <div class="text-xs text-slate-700 dark:text-slate-200 whitespace-pre-line">
                                    {{ ini.impact_value || "-" }}
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredInitiatives.length === 0">
                            <td
                                colspan="4"
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
import { computed, ref } from "vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    versionLegend: {
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

const statusClasses = {
    "On Track": "bg-emerald-100 text-emerald-800 ring-emerald-300 dark:bg-emerald-500/20 dark:text-emerald-300 dark:ring-emerald-500/30",
    "At Risk": "bg-amber-100 text-amber-800 ring-amber-300 dark:bg-amber-500/20 dark:text-amber-300 dark:ring-amber-500/30",
    "Not Signed": "bg-rose-100 text-rose-800 ring-rose-300 dark:bg-rose-500/20 dark:text-rose-300 dark:ring-rose-500/30",
    "Not Started": "bg-blue-100 text-blue-800 ring-blue-300 dark:bg-blue-500/20 dark:text-blue-300 dark:ring-blue-500/30",
    "Done": "bg-slate-100 text-slate-700 ring-slate-300 dark:bg-white/10 dark:text-slate-300 dark:ring-white/15",
};

const versionStatusClasses = {
    4: "bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300 dark:bg-emerald-500/20 dark:text-emerald-300 dark:ring-emerald-500/30",
    5: "bg-purple-100 text-purple-700 ring-1 ring-purple-300 dark:bg-purple-500/20 dark:text-purple-300 dark:ring-purple-500/30",
};

const versionStatusClass = (status) => {
    const key = Number(status);

    return versionStatusClasses[key] ?? "bg-blue-100 text-blue-700 ring-1 ring-blue-300";
};

const implementationStatusClasses = {
    "On Track": "bg-emerald-500",
    "At Risk": "bg-amber-500",
    "Not Signed": "bg-rose-500",
    "Not Started": "bg-blue-500",
    "Done": "bg-slate-500",
};

const implementationStatusClass = (status) => {
    const normalized = normalizeStatus(status);

    return implementationStatusClasses[normalized] ?? "bg-slate-400";
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

    return projectCodeStatusClasses[normalized]
        ?? "bg-slate-100 text-slate-700 ring-slate-300 dark:bg-white/10 dark:text-slate-200 dark:ring-white/15";
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

const statusBadgeClass = (status) => {
    const normalized = canonicalStatus(status);
    const matchedKey = Object.keys(statusClasses).find(
        (label) => normalizeStatusKey(label) === normalizeStatusKey(normalized),
    );

    return (
        (matchedKey && statusClasses[matchedKey]) ??
        "bg-slate-100 text-slate-600 ring-slate-300 dark:bg-white/10 dark:text-slate-300 dark:ring-white/15"
    );
};

const rows = computed(() =>
    Array.isArray(props.items) ? props.items : [],
);
const selectedProjectName = ref("all");
const selectedImplementationStatus = ref("all");
const selectedVersion = ref("all");
const selectedPeriod = ref("all");

const projectNameOptions = computed(() =>
    uniquePreserveOrder(rows.value.map((item) => item?.name)),
);

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

const getProjectStatusEntry = (initiative, period) => {
    const logs = Array.isArray(initiative.status_logs) ? initiative.status_logs : [];

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
            status: initiative.latest_pc_status,
            month: initiative.latest_pc_month,
            year: initiative.latest_pc_year,
            period: `${initiative.latest_pc_month} ${initiative.latest_pc_year}`,
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

const baseFilteredInitiatives = computed(() => {
    return rows.value.filter((item) => {
        if (selectedProjectName.value !== "all" && String(item?.name ?? "") !== selectedProjectName.value) {
            return false;
        }

        return true;
    });
});

const statusFilterOptions = [
    { value: "On Track", label: "On Track" },
    { value: "At Risk", label: "At Risk" },
    { value: "Not Signed", label: "Not Signed" },
    { value: "Not Started", label: "Not Started" },
    { value: "Done", label: "Done" },
];

const toggleImplementationStatusFilter = (statusValue) => {
    const nextValue = String(statusValue ?? "");

    selectedImplementationStatus.value =
        selectedImplementationStatus.value === nextValue ? "all" : nextValue;
};

const versionFilterOptions = computed(() =>
    versionLegendItems.value.map((status) => ({
        value: status.value,
        label: status.label,
    })),
);

const versionLegendItems = computed(() => {
    const sourceVersions = Array.isArray(props.versionLegend) ? props.versionLegend : [];
    const seen = new Set();

    const items = sourceVersions
        .map((item) => ({
            value: String(item?.value ?? "").trim(),
            label: String(item?.label ?? "").trim(),
        }))
        .filter((item) => item.value && item.label)
        .filter((item) => {
            if (seen.has(item.value)) {
                return false;
            }

            seen.add(item.value);
            return true;
        });

    const fallbackItems = items.length > 0
        ? items
        : uniquePreserveOrder(
            rows.value.map((initiative) => String(initiative?.version_status ?? "").trim()),
        ).map((value) => ({
            value,
            label: value === "5" ? "Baseline" : value === "4" ? "Approved" : value,
        }));

    const counts = Object.fromEntries(
        fallbackItems.map((item) => [String(item.value ?? "").trim(), 0]),
    );

    baseFilteredInitiatives.value.forEach((initiative) => {
        const key = String(initiative.version_status ?? "").trim();
        if (!key || !(key in counts)) {
            return;
        }
        counts[key] += 1;
    });

    return fallbackItems.map((item) => ({
        label: item.label,
        value: item.value,
        class: "bg-slate-100 text-slate-700 ring-slate-300 dark:bg-white/10 dark:text-slate-200 dark:ring-white/15",
        count: counts[String(item.value ?? "").trim()] ?? 0,
    }));
});

const statusLegendItems = computed(() => {
    const counts = Object.fromEntries(
        statusFilterOptions.map((item) => [String(item.value ?? "").trim(), 0]),
    );

    const seenProjects = new Set();

    baseFilteredInitiatives.value.forEach((item) => {
        const projectKey = String(item?.code || item?.name || "").trim();
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

    return statusFilterOptions.map((item) => ({
        ...item,
        count: counts[String(item.value ?? "").trim()] ?? 0,
        class: implementationStatusClass(item.value),
    }));
});

const totalProjectsCount = computed(() => {
    return statusLegendItems.value.reduce((acc, item) => acc + item.count, 0);
});

const matchesSelectedStatus = (initiative) => {
    if (selectedImplementationStatus.value === "all") {
        return true;
    }

    const statusEntry = getProjectStatusEntry(initiative, selectedPeriod.value);
    if (!statusEntry) return false;

    return normalizeStatus(statusEntry.status) === selectedImplementationStatus.value;
};

const matchesSelectedVersion = (initiative) => {
    if (selectedVersion.value === "all") {
        return true;
    }

    return String(initiative.version_status ?? "").trim() === String(selectedVersion.value ?? "").trim();
};

const matchesSelectedPeriod = (initiative) => {
    if (selectedPeriod.value === "all") {
        return true;
    }

    const statusEntry = getProjectStatusEntry(initiative, selectedPeriod.value);
    return !!statusEntry;
};

const filteredInitiatives = computed(() =>
    baseFilteredInitiatives.value.filter(
        (initiative) =>
            matchesSelectedStatus(initiative) &&
            matchesSelectedVersion(initiative) &&
            matchesSelectedPeriod(initiative),
    ),
);

const initiativesWithRowspan = computed(() => {
    const initiatives = filteredInitiatives.value;
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
        if (codeA !== codeB) {
            return codeA.localeCompare(codeB);
        }

        const versionA = String(a.version_status ?? "").trim();
        const versionB = String(b.version_status ?? "").trim();

        if (versionA !== versionB) {
            return versionB.localeCompare(versionA, undefined, {
                numeric: true,
                sensitivity: "base",
            });
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
