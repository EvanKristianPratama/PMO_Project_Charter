<template>
    <UserLayout title="Matrix Dependency">
        <div class="space-y-6 animate-fade-in-up">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Matrix Dependency</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Program Planning: atas Digital Initiative, samping IT Initiative.
                </p>
            </section>

            <MatrixDependencyTable />
        </div>
    </UserLayout>
</template>

<script setup>
import MatrixDependencyTable from '@/Components/MatrixDependencyTable.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
</script>
