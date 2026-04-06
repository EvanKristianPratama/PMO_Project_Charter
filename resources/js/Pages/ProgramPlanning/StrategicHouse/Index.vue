<template>
    <UserLayout :title="page.title">
        <div class="space-y-6 animate-fade-in">
            <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#171717] sm:p-6">
                <div class="text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-600 dark:text-sky-300">Strategic House</p>
                    <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">{{ page.headline }}</h1>

                    <div class="mt-5 flex flex-wrap justify-center gap-2">
                        <span
                            v-for="band in focusBands"
                            :key="band.id"
                            class="inline-flex items-center rounded-full bg-sky-100 px-4 py-2 text-xs font-semibold text-sky-700 shadow-sm dark:bg-sky-500/15 dark:text-sky-200"
                        >
                            {{ band.label }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[30px] bg-gradient-to-b from-[#2a6fc2] to-[#17478d] px-5 py-8 text-center text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.15)] [clip-path:polygon(8%_0%,92%_0%,100%_100%,0%_100%)] sm:px-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-100/90">{{ page.visionTitle }}</p>
                    <p class="mx-auto mt-4 max-w-5xl text-sm leading-7 text-white/90 sm:text-base">
                        {{ page.visionText }}
                    </p>
                </div>

                <div class="mt-5 rounded-[28px] bg-[#184f96] px-4 py-5 text-white shadow-sm sm:px-5">
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ summary.total_initiatives }}</p>
                        <p class="mt-1 text-sm font-medium text-white/85">{{ page.initiativeLabel }}</p>
                    </div>

                    <div class="mt-5 grid gap-3 md:grid-cols-5">
                        <article
                            v-for="card in technologyCards"
                            :key="card.name"
                            :class="technologyToneClass(card.tone)"
                            class="rounded-2xl border px-4 py-4 shadow-sm transition-transform hover:-translate-y-0.5"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-sm font-semibold">{{ card.display_name }}</h2>
                                <span class="rounded-full bg-white/80 px-2.5 py-1 text-[11px] font-bold text-slate-900">
                                    {{ card.initiatives_count }}
                                </span>
                            </div>

                            <div class="mt-3 space-y-1.5">
                                <p
                                    v-for="initiative in card.initiatives_preview"
                                    :key="initiative.id"
                                    class="rounded-xl bg-white/70 px-3 py-2 text-[11px] font-medium text-slate-700"
                                >
                                    {{ initiative.label }}
                                </p>

                                <p v-if="card.is_empty" class="rounded-xl bg-white/10 px-3 py-3 text-[11px] text-white/80">
                                    Belum ada initiative yang terhubung ke CoE ini.
                                </p>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="mt-5 rounded-[30px] bg-[#ddebff] p-4 shadow-inner dark:bg-[#12253f] sm:p-5">
                    <div class="rounded-3xl bg-white/75 px-5 py-4 text-center shadow-sm dark:bg-white/5">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sky-700 dark:text-sky-300">
                            {{ page.grandStrategyTitle }}
                        </p>
                        <p class="mx-auto mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                            {{ page.grandStrategyText }}
                        </p>

                        <div v-if="architectureCard" class="mt-4 inline-flex max-w-full flex-wrap items-center justify-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white dark:bg-sky-500/20 dark:text-sky-100">
                            <span>{{ architectureCard.display_name }}</span>
                            <span class="rounded-full bg-white/20 px-2 py-0.5">{{ architectureCard.initiatives_count }} linked initiatives</span>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 xl:grid-cols-6">
                        <article
                            v-for="card in strategyCards"
                            :key="card.name"
                            :class="strategyToneClass(card.tone)"
                            class="rounded-[26px] border px-4 py-5 text-white shadow-sm"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-lg font-semibold leading-6">{{ card.display_name }}</h2>
                                <span class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-bold text-white">
                                    {{ card.initiatives_count }}
                                </span>
                            </div>

                            <div class="mt-4 space-y-2">
                                <p
                                    v-for="initiative in card.initiatives_preview"
                                    :key="initiative.id"
                                    class="rounded-2xl bg-white/10 px-3 py-2 text-[11px] leading-5 text-white/90"
                                >
                                    {{ initiative.label }}
                                </p>
                                <p v-if="card.is_empty" class="rounded-2xl bg-white/10 px-3 py-3 text-[11px] leading-5 text-white/75">
                                    Capability CoE sudah tersedia, tetapi belum ada initiative digital yang dimapping ke area ini.
                                </p>
                            </div>

                            <div v-if="card.status_breakdown.length > 0" class="mt-4 flex flex-wrap gap-1.5">
                                <span
                                    v-for="status in card.status_breakdown"
                                    :key="`${card.name}-${status.key}`"
                                    class="rounded-full bg-white/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-wider text-white/85"
                                >
                                    {{ status.label }} {{ status.count }}
                                </span>
                            </div>
                        </article>
                    </div>

                    <div
                        v-if="foundationCard"
                        class="mt-5 rounded-[24px] bg-gradient-to-r from-[#1b4f93] via-[#215da8] to-[#1b4f93] px-5 py-4 text-white shadow-sm"
                    >
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-lg font-semibold">{{ foundationCard.display_name }}</p>
                                <p class="mt-1 text-sm text-white/80">
                                    Memungkinkan pelaksanaan efektif dari seluruh digital dan IT initiative sebagai lapisan fondasi.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                                    {{ foundationCard.initiatives_count }} mapped initiatives
                                </span>
                                <span
                                    v-for="status in foundationCard.status_breakdown"
                                    :key="`foundation-${status.key}`"
                                    class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold"
                                >
                                    {{ status.label }} {{ status.count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    summary: {
        type: Object,
        default: () => ({}),
    },
    focusBands: {
        type: Array,
        default: () => [],
    },
    technologyCards: {
        type: Array,
        default: () => [],
    },
    strategyCards: {
        type: Array,
        default: () => [],
    },
    foundationCard: {
        type: Object,
        default: null,
    },
    architectureCard: {
        type: Object,
        default: null,
    },
    tbcCard: {
        type: Object,
        default: null,
    },
    unassignedInitiatives: {
        type: Array,
        default: () => [],
    },
});

const technologyToneClass = (tone) => {
    const classes = {
        sky: 'border-sky-200 bg-gradient-to-b from-[#eff7ff] to-[#dbeeff] text-slate-900',
        indigo: 'border-indigo-200 bg-gradient-to-b from-[#eef2ff] to-[#dde7ff] text-slate-900',
        cyan: 'border-cyan-200 bg-gradient-to-b from-[#ecfeff] to-[#d7f7fb] text-slate-900',
        slate: 'border-slate-200 bg-gradient-to-b from-[#f8fafc] to-[#e9eef5] text-slate-900',
        amber: 'border-amber-200 bg-gradient-to-b from-[#fff8e8] to-[#ffedbf] text-slate-900',
    };

    return classes[tone] ?? 'border-slate-200 bg-white text-slate-900';
};

const strategyToneClass = (tone) => {
    const classes = {
        sky: 'border-sky-300 bg-gradient-to-b from-[#2d72bd] to-[#1e5aa0]',
        blue: 'border-blue-300 bg-gradient-to-b from-[#2964ad] to-[#184d8d]',
        indigo: 'border-indigo-300 bg-gradient-to-b from-[#3158a7] to-[#243f88]',
        cyan: 'border-cyan-300 bg-gradient-to-b from-[#2b6f98] to-[#1a5579]',
        emerald: 'border-emerald-300 bg-gradient-to-b from-[#2b7288] to-[#1d5c72]',
        slate: 'border-slate-300 bg-gradient-to-b from-[#36506f] to-[#24384f]',
    };

    return classes[tone] ?? 'border-slate-300 bg-gradient-to-b from-[#31527f] to-[#22395a]';
};
</script>
