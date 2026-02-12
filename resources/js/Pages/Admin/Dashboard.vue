<template>
    <AdminLayout>
        <template #title>Dashboard</template>

        <div class="animate-fade-in">
            <h2 class="text-2xl font-bold text-slate-900 mb-1">Dashboard</h2>
            <p class="text-slate-500 text-sm mb-8">Selamat datang di Admin Panel PMO Portal</p>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Modul</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">5</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Projects</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">—</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Programs</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">—</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Goals</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">—</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>
