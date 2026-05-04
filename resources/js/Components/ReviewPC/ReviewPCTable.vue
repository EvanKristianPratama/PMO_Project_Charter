<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div class="mb-3 flex flex-wrap items-center gap-x-4 gap-y-2 dark:border-white/5">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Implementation Status:</span>
                <div
                    v-for="status in statusLegend"
                    :key="`status-legend-${status.label}`"
                    class="flex items-center gap-1.5 select-none"
                >
                    <span
                        class="h-3 w-3 rounded-sm shadow-sm legend-swatch"
                        :class="status.class"
                    ></span>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        {{ status.label }} <span class="text-slate-400 dark:text-slate-500 font-medium">({{ status.count }})</span>
                    </span>
                </div>
                <div v-if="totalStatusCount > 0" class="flex items-center gap-1.5 border-l border-slate-300 pl-4 ml-1 dark:border-white/10">
                    <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">
                        Total <span class="text-slate-500 dark:text-slate-400 font-medium">({{ totalStatusCount }})</span>
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label for="coe-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">CoE</label>
                <select
                    id="coe-filter"
                    v-model="selectedCoe"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All CoE</option>
                    <option
                        v-for="coe in coeOptions"
                        :key="`coe-filter-${coe}`"
                        :value="coe"
                    >
                        {{ coe }}
                    </option>
                </select>
                <label for="month-filter" class="text-xs font-medium text-slate-700 dark:text-slate-200">Bulan</label>
                <select
                    id="month-filter"
                    v-model="selectedMonth"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All Bulan</option>
                    <option
                        v-for="period in monthOptions"
                        :key="`month-filter-${period.value}`"
                        :value="period.value"
                    >
                        {{ period.label }}
                    </option>
                </select>
                <label for="initiative" class="text-xs font-medium text-slate-700 dark:text-slate-200">Initiative</label>
                <select
                    v-model="selectedInitiativeId"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200 md:w-72"
                >
                    <option value="all">All Initiative</option>
                    <option
                        v-for="initiative in initiativeOptions"
                        :key="initiative.id"
                        :value="String(initiative.id)"
                    >
                        {{ initiative.code }} - {{ initiative.name }}
                    </option>
                </select>
            </div>
        </div>

        <div>
            <table class="w-full min-w-[760px] border-collapse text-xs">
                <thead class="bg-slate-100 dark:bg-white/5">
                    <tr>
                        <th class="border border-slate-300 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            IT Building Blocks
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            No
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            IT Initiatives
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            Kesimpulan
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!groupedFilteredReviews.length">
                        <td colspan="5" class="border border-slate-300 px-3 py-6 text-center text-xs text-slate-500 dark:border-white/20 dark:text-slate-400">
                            Data review belum tersedia.
                        </td>
                    </tr>
                    <tr
                        v-for="review in groupedFilteredReviews"
                        :key="review.id ?? `initiative-${review.initiative_id}`"
                        class="cursor-pointer transition"
                        :class="Number(selectedReviewId) === Number(review.id)
                            ? 'bg-sky-50 dark:bg-sky-900/20'
                            : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                        @click="selectReview(review)"
                    >
                        <td
                            v-if="review.showCoeCell"
                            :rowspan="review.coeRowspan"
                            class="primary-cell border border-slate-300 px-2 py-2 text-center dark:border-white/20"
                            :class="getCoeColorClass(review.latest_coe_name)"
                        >
                            <div class="primary-cell__content">
                                <span class="text-[10px] font-bold leading-tight">
                                    {{ displayText(review.latest_coe_name) }}
                                </span>
                                <span class="coe-count-capsule">
                                    {{ review.coeRowspan }}
                                </span>
                            </div>
                        </td>
                        <td class="border border-slate-300 px-2 py-2 text-center font-medium text-slate-800 dark:border-white/20 dark:text-slate-200">
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                :class="statusCapsuleClass(review.latest_review_progress_status)"
                            >
                                {{ review.initiative?.code ?? review.initiative_id ?? '-' }}
                            </span>
                        </td>
                        <td class="border border-slate-300 px-3 py-2 text-slate-800 dark:border-white/20 dark:text-slate-200">
                            <p class="font-medium">{{ review.initiative?.name ?? '-' }}</p>
                        </td>
                        <td class="border border-slate-300 px-3 py-2 text-slate-700 dark:border-white/20 dark:text-slate-300">
                            <p class="whitespace-pre-line break-words leading-snug">
                                {{ displayText(review.review_pc_conclusion) }}
                            </p>
                        </td>
                        <td class="border border-slate-300 px-3 py-2 text-slate-600 dark:border-white/20 dark:text-slate-400">
                            <Link
                                v-if="review.id"
                                :href="route('program-evaluation.show', review.id)"
                                class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200 dark:hover:border-sky-500/60 dark:hover:bg-sky-900/30 dark:hover:text-sky-200"
                                @click.stop
                            >
                                Detail
                            </Link>
                            <span v-else class="text-xs italic text-slate-400">
                                Belum ada review
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    reviews: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['select', 'count-change']);

const selectedCoe = ref('all');
const selectedMonth = ref('all');
const selectedInitiativeId = ref('all');
const selectedReviewId = ref(null);

const normalizedReviews = computed(() => {
    return props.reviews.map((item) => ({
        ...item,
        detail_kesimpulan: item.detail_kesimpulan ?? item.detail_penjelasan ?? '',
        latest_review_progress_status: item?.latest_review_status_implementation?.review_status ?? '',
        review_pc_conclusion: item?.kesimpulan ?? '',
        review_pc_period: buildReviewPcPeriodLabel(item),
        review_pc_period_value: buildReviewPcPeriodValue(item),
        latest_coe_name: normalizeCoeName(item?.initiative?.coe_name ?? item?.initiative?.coe?.name),
    }));
});

const buildReviewPcPeriodLabel = (review) => {
    const month = String(review?.month ?? '').trim();
    const year = String(review?.year ?? '').trim();

    if (month && year) return `${month} ${year}`;
    return month || year;
};

const buildReviewPcPeriodValue = (review) => {
    const month = String(review?.month ?? '').trim().toLowerCase();
    const year = String(review?.year ?? '').trim();

    if (month && year) return `${month}-${year}`;
    return month || year;
};

const normalizeCoeName = (rawName) => {
    let name = String(rawName ?? '').trim();
    if (!name || name === '-' || name.toUpperCase() === 'NO COE') return 'CoE Not Identified';

    const upper = name.toUpperCase();
    if (upper === 'IOT') return 'IoT';
    if (upper.includes('CLOUD') || upper.includes('COMPUTING') || name === 'Advance Cloud') return 'Advance Cloud';
    if (upper === 'RPA') return 'RPA';
    if (upper.includes('ROBOT') || name === 'Robotics') return 'Robotics';
    if (upper.includes('ANALYTICS') || name === 'AI / Adv. Analytics') return 'AI / Adv. Analytics';

    return name;
};

const getCoeColorClass = (coeName) => {
    const name = normalizeCoeName(coeName);
    if (name === 'IoT') return 'coe-color-blue';
    if (name === 'Advance Cloud') return 'coe-color-emerald';
    if (name === 'RPA') return 'coe-color-amber';
    if (name === 'Robotics') return 'coe-color-purple';
    if (name === 'AI / Adv. Analytics') return 'coe-color-neutral';
    if (name === 'CoE Not Identified') return 'coe-color-none';
    return 'coe-color-none';
};

const statusDesiredOrder = ['On Track', 'At Risk', 'Not Signed', 'Not Started'];

const normalizeStatus = (value) => String(value ?? '').trim().toLowerCase();

const statusCapsuleClass = (status) => {
    const normalized = normalizeStatus(status);
    if (normalized === 'on track') return 'status-color-ontrack';
    if (normalized === 'at risk') return 'status-color-atrisk';
    if (normalized === 'not signed') return 'status-color-notsigned';
    if (normalized === 'not started') return 'status-color-notstarted';
    return 'status-color-other';
};

const statusLegend = computed(() => {
    const counts = Object.fromEntries(statusDesiredOrder.map((status) => [status, 0]));
    let otherCount = 0;

    normalizedReviews.value.forEach((review) => {
        const rawStatus = String(review?.latest_review_progress_status ?? '').trim();
        if (!rawStatus) return;

        const matchedStatus = statusDesiredOrder.find(
            (status) => normalizeStatus(status) === normalizeStatus(rawStatus),
        );

        if (matchedStatus) {
            counts[matchedStatus] += 1;
            return;
        }

        otherCount += 1;
    });

    const legend = statusDesiredOrder.map((label) => ({
        label,
        class: statusCapsuleClass(label),
        count: counts[label],
    }));

    if (otherCount > 0) {
        legend.push({
            label: 'Other',
            class: 'status-color-other',
            count: otherCount,
        });
    }

    return legend;
});

const totalStatusCount = computed(() => {
    return statusLegend.value.reduce((sum, item) => sum + item.count, 0);
});

const initiativeOptions = computed(() => {
    const map = new Map();

    normalizedReviews.value.forEach((review) => {
        const initiative = review?.initiative;
        const id = initiative?.id ?? review?.initiative_id;
        if (!id) return;

        if (!map.has(Number(id))) {
            map.set(Number(id), {
                id: Number(id),
                code: initiative?.code ?? '-',
                name: initiative?.name ?? `Initiative ${id}`,
            });
        }
    });

    return Array.from(map.values()).sort((a, b) => {
        const codeA = Number(a.code);
        const codeB = Number(b.code);
        const codeBothNumber = !Number.isNaN(codeA) && !Number.isNaN(codeB);

        if (codeBothNumber && codeA !== codeB) return codeA - codeB;
        return String(a.name).localeCompare(String(b.name));
    });
});

const coeOptions = computed(() => {
    return Array.from(
        new Set(normalizedReviews.value.map((review) => review.latest_coe_name).filter(Boolean)),
    ).sort((a, b) => String(a).localeCompare(String(b)));
});

const monthOptions = computed(() => {
    const map = new Map();

    normalizedReviews.value.forEach((review) => {
        if (!review.review_pc_period_value) return;

        map.set(review.review_pc_period_value, {
            value: review.review_pc_period_value,
            label: review.review_pc_period,
            year: Number(review.year) || 0,
            monthOrder: getMonthOrder(review.month),
        });
    });

    return Array.from(map.values()).sort((a, b) => {
        if (a.year !== b.year) return b.year - a.year;
        if (a.monthOrder !== b.monthOrder) return b.monthOrder - a.monthOrder;
        return String(a.label).localeCompare(String(b.label));
    });
});

const getMonthOrder = (month) => {
    const normalized = String(month ?? '').trim().toLowerCase();

    return {
        januari: 1,
        februari: 2,
        maret: 3,
        april: 4,
        mei: 5,
        juni: 6,
        juli: 7,
        agustus: 8,
        september: 9,
        oktober: 10,
        november: 11,
        desember: 12,
    }[normalized] ?? 0;
};

const filteredReviews = computed(() => {
    return normalizedReviews.value.filter((review) => {
        const matchesCoe = selectedCoe.value === 'all'
            || review.latest_coe_name === selectedCoe.value;
        const matchesMonth = selectedMonth.value === 'all'
            || review.review_pc_period_value === selectedMonth.value;
        const initiativeId = review?.initiative?.id ?? review?.initiative_id;
        const matchesInitiative = selectedInitiativeId.value === 'all'
            || String(initiativeId) === String(selectedInitiativeId.value);

        return matchesCoe && matchesMonth && matchesInitiative;
    });
});

const groupedFilteredReviews = computed(() => {
    const rows = [];
    let index = 0;

    while (index < filteredReviews.value.length) {
        const currentCoe = filteredReviews.value[index]?.latest_coe_name ?? '';
        let groupSize = 1;

        while (
            index + groupSize < filteredReviews.value.length
            && (filteredReviews.value[index + groupSize]?.latest_coe_name ?? '') === currentCoe
        ) {
            groupSize += 1;
        }

        for (let offset = 0; offset < groupSize; offset += 1) {
            rows.push({
                ...filteredReviews.value[index + offset],
                showCoeCell: offset === 0,
                coeRowspan: groupSize,
            });
        }

        index += groupSize;
    }

    return rows;
});

watch(
    groupedFilteredReviews,
    (list) => {
        emit('count-change', list.length);

        if (!list.length) {
            selectedReviewId.value = null;
            emit('select', null);
            return;
        }

        const selectedStillExists = list.some((item) => Number(item.id) === Number(selectedReviewId.value));
        const selected = selectedStillExists
            ? list.find((item) => Number(item.id) === Number(selectedReviewId.value))
            : list[0];

        selectedReviewId.value = selected.id;
        emit('select', selected);
    },
    { immediate: true },
);

const selectReview = (review) => {
    selectedReviewId.value = review.id;
    emit('select', review);
};

const displayText = (value) => {
    const safe = String(value ?? '').trim();
    return safe || '-';
};

</script>

<style scoped>
.legend-swatch {
    display: block;
    width: 12px;
    height: 12px;
    min-width: 12px;
    min-height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}

.status-color-ontrack {
    background-color: #10b981 !important;
    color: #ffffff !important;
    border: 1px solid #059669 !important;
}

.status-color-atrisk {
    background-color: #f59e0b !important;
    color: #ffffff !important;
    border: 1px solid #d97706 !important;
}

.status-color-notsigned {
    background-color: #f43f5e !important;
    color: #ffffff !important;
    border: 1px solid #e11d48 !important;
}

.status-color-notstarted {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
    border: 1px solid #2563eb !important;
}

.status-color-other {
    background-color: #64748b !important;
    color: #ffffff !important;
    border: 1px solid #475569 !important;
}

.primary-cell {
    vertical-align: middle !important;
    min-width: 120px;
    transition: all 0.2s;
}

.primary-cell__content {
    display: flex;
    flex-direction: row;
    gap: 6px;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 6px 8px;
    text-align: center;
    font-weight: 700;
    color: #1e293b;
}

.coe-count-capsule {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    flex-shrink: 0;
    border-radius: 999px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    background: rgba(0, 0, 0, 0.1);
    padding: 0 4px;
    color: inherit;
    font-size: 9px;
    font-weight: 800;
    line-height: 1;
}

.coe-color-blue {
    background-color: #ffffff;
    color: #1e3a8a;
    border-color: #1d4ed8 !important;
}

.coe-color-emerald {
    background-color: #ffffff;
    color: #065f46;
    border-color: #047857 !important;
}

.coe-color-amber {
    background-color: #ffffff;
    color: #92400e;
    border-color: #b45309 !important;
}

.coe-color-purple {
    background-color: #ffffff;
    color: #5b21b6;
    border-color: #6d28d9 !important;
}

.coe-color-none {
    background-color: #ffffff;
    color: #334155;
    border-color: #475569 !important;
}

.coe-color-neutral {
    background-color: #ffffff;
    color: #334155;
    border-color: #cbd5e1 !important;
}
</style>
