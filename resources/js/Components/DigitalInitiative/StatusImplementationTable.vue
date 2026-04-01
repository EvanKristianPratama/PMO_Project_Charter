<template>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-white/5 dark:bg-[#1a1a1a]">
        <div class="border-b border-slate-200 bg-slate-50/70 px-4 py-3 dark:border-white/10 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                    <div class="flex min-w-0 items-center gap-2">
                        <label
                            class="shrink-0 text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-slate-400">
                            Organization
                        </label>
                        <select v-model="selectedOrganization"
                            class="h-8 w-[250px] rounded-lg border border-slate-300 bg-white px-3 text-[10px] text-slate-700 shadow-sm focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                            <option value="all">Semua Organization</option>
                            <option v-for="organization in availableOrganizations"
                                :key="`organization-${organization.value}`" :value="organization.value">
                                {{ organization.label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex min-w-0 items-center gap-2">
                        <label
                            class="shrink-0 text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-slate-400">
                            Initiative
                        </label>
                        <select v-model="selectedInitiative"
                            class="h-8 w-[340px] rounded-lg border border-slate-300 bg-white px-3 text-[10px] text-slate-700 shadow-sm focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                            <option value="all">Semua Initiative</option>
                            <option v-for="initiative in availableInitiatives" :key="`initiative-${initiative.value}`"
                                :value="initiative.value">
                                {{ initiative.label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex min-w-0 items-center gap-2">
                        <label
                            class="shrink-0 text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:text-slate-400">
                            Bulan
                        </label>
                        <select v-model="selectedMonth"
                            class="h-8 w-[170px] rounded-lg border border-slate-300 bg-white px-3 text-[10px] text-slate-700 shadow-sm focus:border-[#1C75BC] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20 dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                            <option value="all">Semua Bulan</option>
                            <option v-for="month in availableMonths" :key="`month-${month.value}`" :value="month.value">
                                {{ month.label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex min-w-0 items-center gap-2">
                        <button type="button"
                            class="inline-flex h-8 items-center justify-center gap-1.5 rounded-lg bg-[#1C75BC] px-3 text-[10px] font-semibold text-white shadow-sm transition hover:bg-[#0f63b5] focus:outline-none focus:ring-2 focus:ring-[#1C75BC]/20"
                            @click="openAddModal">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Status
                        </button>
                    </div>
                </div>


            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-[10px] dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th
                            class="min-w-[120px] px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            BUs
                        </th>
                        <th
                            class="min-w-[72px] px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Code
                        </th>
                        <th
                            class="min-w-[180px] px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Initiative
                        </th>
                        <th
                            class="min-w-[110px] px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Review Status
                        </th>
                        <th
                            class="min-w-[220px] px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Periode Status
                        </th>
                        <th
                            class="min-w-[220px] px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Updated Status
                        </th>
                        <th
                            class="min-w-[96px] px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/10 dark:bg-[#1a1a1a]">
                    <tr v-for="item in displayItems" :key="`trs-status-implementation-${item.id}`"
                        class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                        <td v-if="item.showBusinessUnit" :rowspan="item.businessUnitRowspan"
                            class="border-r border-slate-100 bg-slate-50/50 px-4 py-4 text-center align-middle text-slate-700 dark:border-white/5 dark:bg-white/[0.02] dark:text-slate-200">
                            {{ item.business_unit || '-' }}
                        </td>
                        <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                            {{ item.code || '-' }}
                        </td>
                        <td class="whitespace-normal break-words px-4 py-4 text-slate-700 dark:text-slate-200">
                            {{ item.initiative || '-' }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-semibold"
                                :class="reviewStatusClass(item.review_status)">
                                {{ item.review_status || '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                            {{ item.periode_status || '-' }}
                        </td>
                        <td class="whitespace-normal break-words px-4 py-4 text-slate-700 dark:text-slate-200">
                            {{ item.updated_status || '-' }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button type="button"
                                class="inline-flex items-center justify-center rounded-full bg-amber-100 px-3 py-1 text-[9px] font-semibold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:hover:bg-amber-500/30"
                                @click="openEditModal(item)">
                                Edit
                            </button>
                        </td>
                    </tr>

                    <tr v-if="displayItems.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-xs text-slate-500 dark:text-slate-400">
                            {{ emptyStateText }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <StatusImplementationModal :show="isModalOpen" :status-data="editingItem"
            :initiative-options="initiativeOptions"
            :default-initiative-id="selectedInitiative !== 'all' ? selectedInitiative : ''"
            :default-month="selectedMonth !== 'all' ? selectedMonth : ''"
            :default-year="selectedYear !== 'all' ? selectedYear : ''" @close="closeModal" />
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import StatusImplementationModal from '@/Components/DigitalInitiative/StatusImplementationModal.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
});

const selectedOrganization = ref('all');
const selectedInitiative = ref('all');
const selectedMonth = ref('all');
const selectedYear = ref('all');
const isModalOpen = ref(false);
const editingItem = ref(null);

const normalizeText = (value) => String(value ?? '').trim().toLowerCase();
const normalizeYearValue = (value) => {
    const rawValue = String(value ?? '').trim();

    return rawValue !== '' ? rawValue : null;
};
const normalizeMonthNumber = (value) => {
    const parsedValue = Number(value);

    return Number.isInteger(parsedValue) && parsedValue >= 1 && parsedValue <= 12
        ? parsedValue
        : null;
};
const monthOptions = [
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' },
];

const openAddModal = () => {
    editingItem.value = null;
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingItem.value = null;
};

const normalizedItems = computed(() => {
    return (Array.isArray(props.items) ? props.items : []).map((item) => ({
        ...item,
        organization_id: item?.organization_id ? String(item.organization_id) : null,
        initiative_id_key: item?.initiative_id ? String(item.initiative_id) : null,
        period_start_month: normalizeMonthNumber(item?.period_start_month),
        period_end_month: normalizeMonthNumber(item?.period_end_month),
        period_year: normalizeYearValue(item?.period_year),
    }));
});

const periodMonthsForItem = (item) => {
    const startMonth = normalizeMonthNumber(item?.period_start_month);
    const endMonth = normalizeMonthNumber(item?.period_end_month);

    if (startMonth && endMonth) {
        const minMonth = Math.min(startMonth, endMonth);
        const maxMonth = Math.max(startMonth, endMonth);

        return Array.from({ length: maxMonth - minMonth + 1 }, (_, index) => minMonth + index);
    }

    const singleMonth = startMonth || endMonth;

    return singleMonth ? [singleMonth] : [];
};

const availableOrganizations = computed(() => {
    return (Array.isArray(props.organizationOptions) ? props.organizationOptions : [])
        .map((organization) => {
            const id = Number(organization?.id);
            const name = String(organization?.name ?? '').trim();

            if (!Number.isInteger(id) || id <= 0 || name === '') {
                return null;
            }

            return {
                value: String(id),
                label: name,
            };
        })
        .filter(Boolean);
});

const availableInitiatives = computed(() => {
    return (Array.isArray(props.initiativeOptions) ? props.initiativeOptions : [])
        .map((initiative) => {
            const id = Number(initiative?.id);
            const code = String(initiative?.code ?? '').trim();
            const name = String(initiative?.name ?? '').trim();

            if (!Number.isInteger(id) || id <= 0 || name === '') {
                return null;
            }

            return {
                value: String(id),
                label: code !== '' ? `${code} - ${name}` : name,
            };
        })
        .filter(Boolean);
});

const availableMonths = computed(() => monthOptions);

const availableYears = computed(() => {
    const years = new Set(
        normalizedItems.value
            .map((item) => item?.period_year)
            .filter(Boolean),
    );

    return Array.from(years)
        .sort((left, right) => {
            const leftNumber = Number(left);
            const rightNumber = Number(right);

            if (Number.isFinite(leftNumber) && Number.isFinite(rightNumber)) {
                return rightNumber - leftNumber;
            }

            return String(right).localeCompare(String(left));
        })
        .map((year) => ({
            value: String(year),
            label: String(year),
        }));
});

const filteredItems = computed(() => {
    const selectedMonthNumber = normalizeMonthNumber(selectedMonth.value);

    return normalizedItems.value.filter((item) => {
        if (selectedOrganization.value !== 'all' && item.organization_id !== selectedOrganization.value) {
            return false;
        }

        if (selectedInitiative.value !== 'all' && item.initiative_id_key !== selectedInitiative.value) {
            return false;
        }

        if (selectedYear.value !== 'all' && item.period_year !== selectedYear.value) {
            return false;
        }

        if (selectedMonthNumber !== null && !periodMonthsForItem(item).includes(selectedMonthNumber)) {
            return false;
        }

        return true;
    });
});

const compareText = (left, right) => {
    const normalizedLeft = normalizeText(left);
    const normalizedRight = normalizeText(right);

    if (!normalizedLeft && normalizedRight) {
        return 1;
    }

    if (normalizedLeft && !normalizedRight) {
        return -1;
    }

    return normalizedLeft.localeCompare(normalizedRight);
};

const compareDate = (left, right) => {
    const leftTime = Date.parse(left ?? '');
    const rightTime = Date.parse(right ?? '');
    const normalizedLeft = Number.isNaN(leftTime) ? Number.MAX_SAFE_INTEGER : leftTime;
    const normalizedRight = Number.isNaN(rightTime) ? Number.MAX_SAFE_INTEGER : rightTime;

    return normalizedLeft - normalizedRight;
};

const sortedItems = computed(() => {
    return [...filteredItems.value].sort((left, right) => {
        return (
            compareText(left.business_unit, right.business_unit) ||
            compareText(left.code, right.code) ||
            compareText(left.initiative, right.initiative) ||
            compareDate(left.created_at, right.created_at) ||
            Number(left.id || 0) - Number(right.id || 0)
        );
    });
});

const displayItems = computed(() => {
    return sortedItems.value.map((item, index, items) => {
        const businessUnitKey = normalizeText(item.business_unit);
        const previousBusinessUnitKey = index > 0
            ? normalizeText(items[index - 1]?.business_unit)
            : '';
        const canMergeBusinessUnit = businessUnitKey !== '';
        const showBusinessUnit = !canMergeBusinessUnit || businessUnitKey !== previousBusinessUnitKey;

        if (!showBusinessUnit) {
            return {
                ...item,
                showBusinessUnit: false,
                businessUnitRowspan: 0,
            };
        }

        let businessUnitRowspan = 1;

        if (canMergeBusinessUnit) {
            for (let nextIndex = index + 1; nextIndex < items.length; nextIndex += 1) {
                if (normalizeText(items[nextIndex]?.business_unit) !== businessUnitKey) {
                    break;
                }

                businessUnitRowspan += 1;
            }
        }

        return {
            ...item,
            showBusinessUnit: true,
            businessUnitRowspan,
        };
    });
});

const emptyStateText = computed(() => {
    if (normalizedItems.value.length === 0) {
        return 'Belum ada data status implementation.';
    }

    return 'Tidak ada data yang sesuai dengan filter.';
});

const reviewStatusClass = (value) => {
    const normalizedValue = normalizeText(value);

    if (normalizedValue.includes('track') || normalizedValue.includes('on')) {
        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300';
    }

    if (normalizedValue.includes('risk')) {
        return 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300';
    }

    if (normalizedValue.includes('delay') || normalizedValue.includes('off')) {
        return 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300';
    }

    return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
};
</script>
