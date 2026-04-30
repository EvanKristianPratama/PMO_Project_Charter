<script setup>
const props = defineProps({
    review: {
        type: Object,
        required: true,
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
});
</script>

<template>
    <section id="review-content" class="space-y-0">
        <div class="overflow-hidden border border-[#1661ad] bg-white dark:bg-[#171717]">
            <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                Kesimpulan / Hasil Review
            </div>
            <article class="border border-[#9ec6e7] bg-white px-5 py-4 dark:border-sky-600/40 dark:bg-[#171717]">
                <h2 v-if="!editable" class="text-xl font-extrabold leading-tight text-slate-900 dark:text-white">
                    {{ review.kesimpulan || '-' }}
                </h2>
                <input v-else v-model="form.kesimpulan" type="text" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xl font-extrabold text-slate-900 dark:border-white/10 dark:bg-[#101826] dark:text-white focus:border-[#1C75BC] focus:outline-none" placeholder="Kesimpulan" />
                <p v-if="!editable" class="mt-2 text-xs font-medium text-slate-700 dark:text-slate-200">
                    {{ review.detail_kesimpulan || '-' }}
                </p>
                <textarea v-else v-model="form.detail_kesimpulan" class="mt-2 w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs font-medium text-slate-700 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none" rows="2" placeholder="Detail kesimpulan..."></textarea>
                <ul
                    v-if="!editable && penjelasanItems.length"
                    class="mt-3 list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200"
                >
                    <li v-for="(item, index) in penjelasanItems" :key="`penjelasan-${index}`">{{ item }}</li>
                </ul>
                <p v-else-if="!editable" class="mt-3 whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                    {{ review.penjelasan || '-' }}
                </p>
                <textarea v-else v-model="form.penjelasan" class="mt-3 w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[80px]" rows="3" placeholder="Satu poin per baris..."></textarea>
            </article>
        </div>
    </section>

    <section id="review-info" class="space-y-0">
        <div class="overflow-hidden border border-[#1661ad]">
            <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                Informasi Proyek
            </div>
            <div class="grid grid-cols-1 gap-0 lg:grid-cols-3">
                <article class="border border-[#b7daf1] bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                    <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                        Why
                    </header>
                    <div class="px-3 py-3">
                        <ul v-if="!editable && whyItems.length" class="list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                            <li v-for="(item, index) in whyItems" :key="`why-${index}`">{{ item }}</li>
                        </ul>
                        <p v-else-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                            {{ review.why || '-' }}
                        </p>
                        <textarea v-else v-model="form.why" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Satu poin per baris..."></textarea>
                    </div>
                </article>

                <article class="border border-[#b7daf1] bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                    <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                        What
                    </header>
                    <div class="px-3 py-3">
                        <p v-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                            {{ review.what || '-' }}
                        </p>
                        <textarea v-else v-model="form.what" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="What..."></textarea>
                    </div>
                </article>

                <article class="border border-[#b7daf1] bg-white dark:border-sky-700/40 dark:bg-[#171717]">
                    <header class="bg-[#a8d0ed] px-3 py-1.5 text-[12px] font-bold text-slate-900 dark:bg-sky-900/40 dark:text-slate-100">
                        How
                    </header>
                    <div class="px-3 py-3">
                        <template v-if="!editable">
                            <p v-if="howParsed.intro" class="mb-2 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                {{ howParsed.intro }}
                            </p>
                            <ol v-if="howParsed.steps.length" class="list-decimal space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                                <li v-for="(item, index) in howParsed.steps" :key="`how-${index}`">{{ item }}</li>
                            </ol>
                            <p v-else class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                                {{ review.how || '-' }}
                            </p>
                        </template>
                        <textarea v-else v-model="form.how" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Satu poin per baris..."></textarea>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="space-y-0">
        <div class="overflow-hidden border border-[#1661ad]">
            <div class="bg-[#1661ad] px-4 py-1.5 text-[14px] font-bold text-white">
                Perubahan Project Charter
            </div>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-0 lg:divide-x-2 lg:divide-[#1661ad]">
                <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                    <header class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">1</span>
                        <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Project Profile</h3>
                    </header>
                    <ul v-if="!editable && projectProfileItems.length" class="list-disc space-y-1 pl-5 text-xs leading-snug text-slate-800 dark:text-slate-200">
                        <li v-for="(item, index) in projectProfileItems" :key="`profile-${index}`">{{ item }}</li>
                    </ul>
                    <p v-else-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                        {{ review.project_profile || '-' }}
                    </p>
                    <textarea v-else v-model="form.project_profile" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Satu poin per baris..."></textarea>
                </article>

                <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                    <header class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">2</span>
                        <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Key Milestone</h3>
                    </header>
                    <p v-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                        {{ review.key_milestone || '-' }}
                    </p>
                    <textarea v-else v-model="form.key_milestone" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Key Milestone..."></textarea>
                </article>

                <article class="px-4 py-2 bg-white dark:bg-[#171717]">
                    <header class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#1661ad] text-sm font-bold text-white">3</span>
                        <h3 class="text-[12px] font-bold text-slate-900 dark:text-white">Risk &amp; Impact Value</h3>
                    </header>
                    <p v-if="!editable" class="whitespace-pre-line text-xs leading-snug text-slate-800 dark:text-slate-200">
                        {{ review.risk_impact || '-' }}
                    </p>
                    <textarea v-else v-model="form.risk_impact" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-white/10 dark:bg-[#101826] dark:text-slate-200 focus:border-[#1C75BC] focus:outline-none min-h-[120px]" rows="5" placeholder="Risk & Impact Value..."></textarea>
                </article>
            </div>
        </div>
    </section>
</template>
