<template>
    <UserLayout :title="title">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ title }}</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ description }}</p>
            </section>

            <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm dark:border-white/15 dark:bg-[#171717]">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Template halaman kosong</p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Halaman ini disiapkan sebagai placeholder untuk modul {{ title.toLowerCase() }}.
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

defineProps({
    title: {
        type: String,
        default: 'Placeholder',
    },
    description: {
        type: String,
        default: 'Template halaman kosong.',
    },
});
</script>
