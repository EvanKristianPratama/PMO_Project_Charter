<script setup>
import { computed } from 'vue';

const props = defineProps({
    review: {
        type: Object,
        default: () => ({}),
    },
    reviews: {
        type: Array,
        default: () => [],
    },
    penjelasanItems: {
        type: Array,
        default: () => [],
    },
    whyItems: {
        type: Array,
        default: () => [],
    },
    howParsed: {
        type: Object,
        default: () => ({ intro: '', steps: [] }),
    },
    projectProfileItems: {
        type: Array,
        default: () => [],
    },
    editable: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
        default: () => ({}),
    },
    statusImplementation: {
        type: Object,
        default: null,
    },
});

const monthOptions = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const reviewEntries = computed(() => {
    if (Array.isArray(props.reviews) && props.reviews.length > 0) {
        return props.reviews;
    }

    return props.review ? [props.review] : [];
});

const displayReview = computed(() => {
    if (!props.editable && Array.isArray(props.reviews) && props.reviews.length > 0) {
        return props.reviews[0] ?? {};
    }

    return props.review ?? {};
});

const getReviewItems = (reviewItem) => ({
    penjelasanItems: splitToItems(reviewItem?.penjelasan),
    whyItems: splitToItems(reviewItem?.why),
    projectProfileItems: splitToItems(reviewItem?.project_profile),
    howParsed: parseHow(reviewItem?.how),
});

const formatPeriod = (reviewItem) => {
    return [reviewItem?.month, reviewItem?.year].filter(Boolean).join(' ') || '-';
};

const statusImplementationLabel = computed(() => {
    return String(props.statusImplementation?.review_status ?? '').trim();
});

const normalizeStatus = (value) => String(value ?? '').trim().toLowerCase();

const statusCapsuleClass = (status) => {
    const normalized = normalizeStatus(status);
    if (normalized === 'on track') return 'bg-emerald-500 text-white ring-1 ring-emerald-600';
    if (normalized === 'at risk') return 'bg-amber-500 text-white ring-1 ring-amber-600';
    if (normalized === 'not signed') return 'bg-rose-500 text-white ring-1 ring-rose-600';
    if (normalized === 'not started') return 'bg-blue-500 text-white ring-1 ring-blue-600';
    if (normalized === 'done') return 'bg-slate-500 text-white ring-1 ring-slate-600';
    return 'bg-slate-100 text-slate-700 ring-1 ring-slate-300';
};

const displayReviewItems = computed(() => {
    if (!props.editable && Array.isArray(props.reviews) && props.reviews.length > 0) {
        return getReviewItems(displayReview.value);
    }

    return {
        penjelasanItems: props.penjelasanItems,
        whyItems: props.whyItems,
        projectProfileItems: props.projectProfileItems,
        howParsed: props.howParsed,
    };
});

const splitToItems = (value) => {
    const text = normalizeText(value);
    if (!text) return [];

    let items = text
        .split(/\r?\n/)
        .map((line) => line.trim())
        .filter(Boolean);

    if (items.length === 1 && text.includes(' - ')) {
        items = text.split(/\s+-\s+/).map((line) => line.trim()).filter(Boolean);
    }

    return items.map((item) => item.replace(/^[-*]\s*/, '').trim()).filter(Boolean);
};

const parseHow = (value) => {
    const text = normalizeText(value);
    if (!text) {
        return { intro: '', steps: [] };
    }

    const stepMatches = Array.from(text.matchAll(/(?:^|[;\n])\s*\d+\.\s*([^;\n]+)/g));
    if (stepMatches.length) {
        const firstIndex = stepMatches[0].index ?? 0;
        const intro = text.slice(0, firstIndex).trim();
        const steps = stepMatches.map((match) => match[1].trim()).filter(Boolean);
        return { intro, steps };
    }

    return { intro: '', steps: splitToItems(text) };
};

const normalizeText = (value) => String(value ?? '').replace(/\u200B/g, '').trim();
</script>

<template>
    <template v-if="reviewEntries.length > 1 && !editable">
        <div class="space-y-4">
            <section
                v-for="(entry, index) in reviewEntries"
                :key="entry?.id ?? `review-${index}`"
                class="space-y-0"
            >
                <div class="overflow-hidden border border-slate-900 bg-white dark:bg-[#171717]">
                    <div class="bg-[#1661ad] pl-4 text-[14px] font-bold text-white flex justify-between items-stretch">
                        <div class="py-1.5 flex items-center">
                            Kesimpulan / Hasil Review
                            <span
                                v-if="entry.initiative?.coe_name"
                                class="ml-2 inline-flex items-center rounded bg-white/20 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white ring-1 ring-white/30"
                            >
                                {{ entry.initiative.coe_name }}
                            </span>
                            <span
                                v-if="statusImplementationLabel"
                                :class="['ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold leading-none', statusCapsuleClass(statusImplementationLabel)]"
                            >
                                {{ statusImplementationLabel }}
                            </span>
                        </div>
                        <div class="flex items-stretch border-l border-white/30 text-xs font-medium">
                            <div class="flex items-center bg-[#12508f] px-3 text-[#f8f9fa] font-semibold tracking-wide">
                                Periode Status
                            </div>
                            <div class="flex items-center bg-white px-3 text-slate-900">
                                {{ formatPeriod(entry) }}
                            </div>
                        </div>
                    </div>
                    <article class="border border-slate-900 bg-white px-5 py-4 dark:border-sky-600/40 dark:bg-[#171717]">
                        <h2 class="text-xl font-extrabold leading-tight text-slate-900 dark:text-white">
                            {{ entry.kesimpulan || '-' }}
                        </h2>
                        <p class="mt-2 text-xs font-medium text-slate-700 dark:text-slate-200">
                            {{ entry.detail_kesimpulan || entry.detail_penjelasan || '-' }}
                        </p>
                        <ul
                            v-if="getReviewItems(entry).penjelasanItems.length"
                            class="mt-3 list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200"
                        >
                            <li v-for="(item, itemIndex) in getReviewItems(entry).penjelasanItems" :key="`penjelasan-${entry?.id ?? index}-${itemIndex}`">
                                {{ item }}
                            </li>
                        </ul>
                        <p v-else class="mt-3 whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                            {{ entry.penjelasan || '-' }}
                        </p>
                    </article>
                </div>

                <section class="space-y-0">
                    <div class="overflow-hidden border border-slate-900">
                        <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                            Informasi Proyek
                        </div>
                        <div class="grid grid-cols-1 gap-0 lg:grid-cols-3">
                            <article class="border border-slate-900 bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                                <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                                    Why
                                </header>
                                <div class="px-3 py-3">
                                    <ul v-if="getReviewItems(entry).whyItems.length" class="list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                        <li v-for="(item, itemIndex) in getReviewItems(entry).whyItems" :key="`why-${entry?.id ?? index}-${itemIndex}`">{{ item }}</li>
                                    </ul>
                                    <p v-else class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                        {{ entry.why || '-' }}
                                    </p>
                                </div>
                            </article>

                            <article class="border border-slate-900 bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                                <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                                    What
                                </header>
                                <div class="px-3 py-3">
                                    <p class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                        {{ entry.what || '-' }}
                                    </p>
                                </div>
                            </article>

                            <article class="border border-slate-900 bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                                <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                                    How
                                </header>
                                <div class="px-3 py-3">
                                    <template v-if="getReviewItems(entry).howParsed.intro">
                                        <p class="mb-2 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                            {{ getReviewItems(entry).howParsed.intro }}
                                        </p>
                                        <ol v-if="getReviewItems(entry).howParsed.steps.length" class="list-decimal space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                            <li v-for="(item, itemIndex) in getReviewItems(entry).howParsed.steps" :key="`how-${entry?.id ?? index}-${itemIndex}`">{{ item }}</li>
                                        </ol>
                                    </template>
                                    <p v-else class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                        {{ entry.how || '-' }}
                                    </p>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="space-y-0">
                    <div class="overflow-hidden border border-slate-900">
                        <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                            Perubahan Project Charter
                        </div>
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-0 lg:divide-x-2 lg:divide-slate-900">
                            <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                                <header class="mb-3 flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">1</span>
                                    <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Project Profile</h3>
                                </header>
                                <ul v-if="getReviewItems(entry).projectProfileItems.length" class="list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                    <li v-for="(item, itemIndex) in getReviewItems(entry).projectProfileItems" :key="`profile-${entry?.id ?? index}-${itemIndex}`">{{ item }}</li>
                                </ul>
                                <p v-else class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                    {{ entry.project_profile || '-' }}
                                </p>
                            </article>

                            <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                                <header class="mb-3 flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">2</span>
                                    <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Key Milestone</h3>
                                </header>
                                <p class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                    {{ entry.key_milestone || '-' }}
                                </p>
                            </article>

                            <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                                <header class="mb-3 flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">3</span>
                                    <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Risk &amp; Impact Value</h3>
                                </header>
                                <p class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                    {{ entry.risk_impact || '-' }}
                                </p>
                            </article>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </template>
    <template v-else>
    <section id="review-content" class="space-y-0">
        <div class="overflow-hidden border border-slate-900 bg-white dark:bg-[#171717]">
            <div class="bg-[#1661ad] pl-4 text-[14px] font-bold text-white flex justify-between items-stretch">
                <div class="py-1.5 flex items-center">
                    Kesimpulan / Hasil Review
                    <span
                        v-if="displayReview.initiative?.coe_name"
                        class="ml-2 inline-flex items-center rounded bg-white/20 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white ring-1 ring-white/30"
                    >
                        {{ displayReview.initiative.coe_name }}
                    </span>
                    <span
                        v-if="statusImplementationLabel"
                        :class="['ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold leading-none', statusCapsuleClass(statusImplementationLabel)]"
                    >
                        {{ statusImplementationLabel }}
                    </span>
                </div>
                <div class="flex items-stretch border-l border-white/30 text-xs font-medium">
                    <div class="flex items-center bg-[#12508f] px-3 text-[#f8f9fa] font-semibold tracking-wide">
                        Periode Status
                    </div>
                    <div class="flex items-center bg-white text-slate-900">
                        <template v-if="editable">
                            <select v-model="form.month" class="w-[90px] border-0 bg-transparent py-1.5 pl-2 pr-5 text-slate-900 focus:ring-0 text-xs">
                                <option value="" disabled>Bulan</option>
                                <option v-for="m in monthOptions" :key="m" :value="m">{{ m }}</option>
                            </select>
                            <input v-model="form.year" type="number" class="w-[50px] border-0 bg-transparent py-1.5 pl-0 pr-2 text-slate-900 placeholder-slate-400 focus:ring-0 text-xs" placeholder="YYYY" />
                        </template>
                        <span v-else class="px-3 py-1.5">
                            {{ formatPeriod(displayReview) }}
                        </span>
                    </div>
                </div>
            </div>
            <article class="border border-slate-900 bg-white px-5 py-4 dark:border-sky-600/40 dark:bg-[#171717]">
                <h2 v-if="!editable" class="text-xl font-extrabold leading-tight text-slate-900 dark:text-white">
                    {{ displayReview.kesimpulan || '-' }}
                </h2>
                <input v-else v-model="form.kesimpulan" type="text" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xl font-extrabold text-slate-900 dark:border-white/10 dark:bg-[#101826] dark:text-white focus:border-[#1C75BC] focus:outline-none" placeholder="Kesimpulan" />
                <p v-if="!editable" class="mt-2 text-xs font-medium text-slate-700 dark:text-slate-200">
                    {{ displayReview.detail_kesimpulan || displayReview.detail_penjelasan || '-' }}
                </p>
                <textarea v-else v-model="form.detail_kesimpulan" class="mt-2 w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs font-medium text-slate-700 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none" rows="2" placeholder="Detail kesimpulan..."></textarea>
                <ul
                    v-if="!editable && displayReviewItems.penjelasanItems.length"
                    class="mt-3 list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200"
                >
                    <li v-for="(item, index) in displayReviewItems.penjelasanItems" :key="`penjelasan-${index}`">{{ item }}</li>
                </ul>
                <p v-else-if="!editable" class="mt-3 whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                    {{ displayReview.penjelasan || '-' }}
                </p>
                <textarea v-else v-model="form.penjelasan" class="mt-3 w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[80px]" rows="3" placeholder="Satu poin per baris..."></textarea>
            </article>
        </div>
    </section>

    <section id="review-info" class="space-y-0">
        <div class="overflow-hidden border border-slate-900">
            <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                Informasi Proyek
            </div>
            <div class="grid grid-cols-1 gap-0 lg:grid-cols-3">
                <article class="border border-slate-900 bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                    <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                        Why
                    </header>
                    <div class="px-3 py-3">
                        <ul v-if="!editable && displayReviewItems.whyItems.length" class="list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                            <li v-for="(item, index) in displayReviewItems.whyItems" :key="`why-${index}`">{{ item }}</li>
                        </ul>
                        <p v-else-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                            {{ displayReview.why || '-' }}
                        </p>
                        <textarea v-else v-model="form.why" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Satu poin per baris..."></textarea>
                    </div>
                </article>

                <article class="border border-slate-900 bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                    <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                        What
                    </header>
                    <div class="px-3 py-3">
                        <p v-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                            {{ displayReview.what || '-' }}
                        </p>
                        <textarea v-else v-model="form.what" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="What..."></textarea>
                    </div>
                </article>

                <article class="border border-slate-900 bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                    <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                        How
                    </header>
                    <div class="px-3 py-3">
                        <template v-if="!editable">
                            <p v-if="displayReviewItems.howParsed.intro" class="mb-2 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                {{ displayReviewItems.howParsed.intro }}
                            </p>
                            <ol v-if="displayReviewItems.howParsed.steps.length" class="list-decimal space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                <li v-for="(item, index) in displayReviewItems.howParsed.steps" :key="`how-${index}`">{{ item }}</li>
                            </ol>
                            <p v-else class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                {{ displayReview.how || '-' }}
                            </p>
                        </template>
                        <textarea v-else v-model="form.how" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Satu poin per baris..."></textarea>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="space-y-0">
        <div class="overflow-hidden border border-slate-900">
            <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                Perubahan Project Charter
            </div>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-0 lg:divide-x-2 lg:divide-slate-900">
                <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                    <header class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">1</span>
                        <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Project Profile</h3>
                    </header>
                    <ul v-if="!editable && displayReviewItems.projectProfileItems.length" class="list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                        <li v-for="(item, index) in displayReviewItems.projectProfileItems" :key="`profile-${index}`">{{ item }}</li>
                    </ul>
                    <p v-else-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                        {{ displayReview.project_profile || '-' }}
                    </p>
                    <textarea v-else v-model="form.project_profile" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Satu poin per baris..."></textarea>
                </article>

                <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                    <header class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">2</span>
                        <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Key Milestone</h3>
                    </header>
                    <p v-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                        {{ displayReview.key_milestone || '-' }}
                    </p>
                    <textarea v-else v-model="form.key_milestone" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Key Milestone..."></textarea>
                </article>

                <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                    <header class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">3</span>
                        <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Risk &amp; Impact Value</h3>
                    </header>
                    <p v-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                        {{ displayReview.risk_impact || '-' }}
                    </p>
                    <textarea v-else v-model="form.risk_impact" class="w-full rounded border border-slate-900 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Risk & Impact Value..."></textarea>
                </article>
            </div>
        </div>
    </section>
    </template>
</template>
