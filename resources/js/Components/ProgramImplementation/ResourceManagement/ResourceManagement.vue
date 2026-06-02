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
                    class="w-full min-w-[980px] divide-y divide-slate-200 text-sm dark:divide-white/10"
                >
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th
                                class="w-[16%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                Code
                            </th>
                            <th
                                class="w-[24%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                Project Name
                            </th>
                            <th
                                class="w-[16%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                Status
                            </th>
                            <th
                                class="w-[18%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                Budget
                            </th>
                            <th
                                class="w-[26%] px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"
                            >
                                Key Personnel
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        <tr v-if="filteredRows.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400"
                            >
                                Belum ada data project charter yang dapat
                                ditampilkan.
                            </td>
                        </tr>

                        <tr
                            v-for="item in filteredRows"
                            :key="item.row_key"
                            class="align-top"
                        >
                            <td
                                class="px-4 py-4 text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                {{ item.project_code || "-" }}
                            </td>
                            <td
                                class="px-4 py-4 text-sm text-slate-800 dark:text-slate-100"
                            >
                                {{ item.project_name || "-" }}
                            </td>
                            <td
                                class="px-4 py-4 text-sm text-slate-700 dark:text-slate-200"
                            >
                                <span
                                    :class="statusBadgeClass(item.status_id)"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                                >
                                    {{ item.status || "-" }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                <span class="whitespace-pre-line break-words">
                                    {{ item.budget || "-" }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                <span class="whitespace-pre-line break-words">
                                    {{ item.key_personnel || "-" }}
                                </span>
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
            type: "all",
            status: "all",
        }),
    },
    filterOptions: {
        type: Object,
        default: () => ({
            types: [],
            statuses: [],
        }),
    },
});

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

const rows = computed(() =>
    Array.isArray(props.resourceProjects) ? props.resourceProjects : [],
);
const typeOptions = computed(() =>
    Array.isArray(props.filterOptions?.types) ? props.filterOptions.types : [],
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
const selectedType = ref(String(props.filters?.type ?? "all"));
const selectedStatus = ref(String(props.filters?.status ?? "all"));

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
            selectedType.value !== "all" &&
            String(item?.project_type ?? "") !== selectedType.value
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
