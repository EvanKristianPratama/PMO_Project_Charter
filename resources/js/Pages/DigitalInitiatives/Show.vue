<template>
    <UserLayout :title="`Digital Initiative - ${initiative.no}`">
        <div class="space-y-4 print:space-y-0">
            <section class="print:hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            href="/digital-initiatives"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                            </svg>
                            Back
                        </Link>

                        <h1 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ initiative.no }}
                        </h1>

                        <span class="text-xs text-slate-500 dark:text-slate-400">Digital Initiative</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            :href="`/digital-initiatives/${initiative.id}/edit`"
                            class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700"
                        >
                            Edit Charter
                        </Link>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                            @click="printCharter"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 17h12v3H6v-3Zm-2-2h16a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2Z" />
                            </svg>
                            Print
                        </button>

                        <button
                            type="button"
                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/10"
                            @click="confirmDelete"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </section>

            <main class="print:m-0 print:p-0">
                <DigitalCharterDocument :initiative="initiative" />
            </main>
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import DigitalCharterDocument from './Partials/DigitalCharterDocument.vue';

const props = defineProps({
    initiative: Object,
});

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete initiative "${props.initiative.no}"?`)) {
        router.delete(`/digital-initiatives/${props.initiative.id}`);
    }
};

const printCharter = () => {
    window.print();
};
</script>
