<template>
    <UserLayout title="Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Regulation & Policy</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Proses Bisnis</h1>
                    </div>
                </div>
            </section>

            <!-- Main Content -->
            <ProsesBisnis
                :proses-bisnis="prosesBisnis"
                :organizations="organizations"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import ProsesBisnis from '@/Components/Regulation/ProsesBisnis/ProsesBisnis.vue';

defineProps({
    prosesBisnis: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
