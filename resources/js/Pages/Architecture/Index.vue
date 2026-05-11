<template>
    <UserLayout title="Architecture">
        <div class="animate-fade-in-up space-y-3">
            <section class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Architecture</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Overview arsitektur bisnis, kapabilitas, dan struktur organisasi.
                    </p>
                </div>
            </section>

            <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm dark:border-white/15 dark:bg-[#171717]">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Halaman Architecture</p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Gunakan sub-menu di atas untuk mengakses Business Capability atau Organization Structure.
                </p>
            </section>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
</script>
