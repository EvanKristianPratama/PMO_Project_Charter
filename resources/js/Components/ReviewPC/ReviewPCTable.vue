<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-white/10">
            <div class="flex flex-wrap items-center gap-3">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari initiative / kesimpulan..."
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200 md:w-72"
                />
                <label for="type" class="text-xs font-medium text-slate-700 dark:text-slate-200">Tipe Initiative</label>
                <select
                    v-model="selectedType"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200"
                >
                    <option value="all">All Type</option>
                    <option value="digital">Digital</option>
                    <option value="it">IT</option>
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
            <table class="w-full min-w-[760px] border-collapse text-sm">
                <thead class="bg-slate-100 dark:bg-white/5">
                    <tr>
                        <th class="border border-slate-300 bg-slate-100 px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            Code
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            Initiative
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            Kesimpulan
                        </th>
                        <th class="border border-slate-300 bg-slate-100 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-300">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!filteredReviews.length">
                        <td colspan="4" class="border border-slate-300 px-4 py-8 text-center text-sm text-slate-500 dark:border-white/20 dark:text-slate-400">
                            Data review belum tersedia.
                        </td>
                    </tr>
                    <tr
                        v-for="review in filteredReviews"
                        :key="review.id"
                        class="cursor-pointer transition"
                        :class="Number(selectedReviewId) === Number(review.id)
                            ? 'bg-sky-50 dark:bg-sky-900/20'
                            : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                        @click="selectReview(review)"
                    >
                        <td class="border border-slate-300 px-3 py-3 text-center font-medium text-slate-800 dark:border-white/20 dark:text-slate-200">
                            {{ review.initiative?.code ?? review.initiative_id ?? '-' }}
                        </td>
                        <td class="border border-slate-300 px-4 py-3 text-slate-800 dark:border-white/20 dark:text-slate-200">
                            <p class="font-medium">{{ review.initiative?.name ?? '-' }}</p>
                        </td>
                        <td class="border border-slate-300 px-4 py-3 text-slate-700 dark:border-white/20 dark:text-slate-300">
                            {{ previewText(review.kesimpulan) }}
                        </td>
                        <td class="border border-slate-300 px-4 py-3 text-slate-600 dark:border-white/20 dark:text-slate-400">
                            <Link
                                :href="route('program-evaluation.show', review.id)"
                                class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700 dark:border-white/20 dark:bg-[#1f1f1f] dark:text-slate-200 dark:hover:border-sky-500/60 dark:hover:bg-sky-900/30 dark:hover:text-sky-200"
                                @click.stop
                            >
                                Detail
                            </Link>
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

const search = ref('');
const selectedType = ref('all');
const selectedInitiativeId = ref('all');
const selectedReviewId = ref(null);

const normalizedReviews = computed(() => {
    return props.reviews.map((item) => ({
        ...item,
        detail_kesimpulan: item.detail_kesimpulan ?? item.detail_penjelasan ?? '',
    }));
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

const filteredReviews = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    return normalizedReviews.value.filter((review) => {
        const matchesType = selectedType.value === 'all'
            || (selectedType.value === 'digital' && Number(review?.initiative?.tipe_initiative) === 1)
            || (selectedType.value === 'it' && Number(review?.initiative?.tipe_initiative) === 2);
        const initiativeId = review?.initiative?.id ?? review?.initiative_id;
        const matchesInitiative = selectedInitiativeId.value === 'all'
            || String(initiativeId) === String(selectedInitiativeId.value);

        if (!matchesType || !matchesInitiative) return false;
        if (!keyword) return true;

        const searchable = [
            review?.initiative?.name,
            review?.initiative?.code,
            review?.kesimpulan,
            review?.detail_kesimpulan,
            review?.what,
            review?.why,
            review?.how,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        return searchable.includes(keyword);
    });
});

watch(
    filteredReviews,
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

const previewText = (value, max = 110) => {
    const safe = String(value ?? '').trim();
    if (!safe) return '-';
    if (safe.length <= max) return safe;
    return `${safe.slice(0, max).trim()}...`;
};

</script>
