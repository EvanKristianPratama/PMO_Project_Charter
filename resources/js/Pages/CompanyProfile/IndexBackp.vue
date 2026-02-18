<template>
    <UserLayout title="Company Profile">
        <div class="animate-fade-in">
            <!-- Page Header (konsisten dengan halaman lain) -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Company Profile</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">
                            Struktur organisasi perusahaan beserta grup dan unit organisasi
                        </p>
                    </div>
                </div>
            </div>

            <!-- Company List -->
            <div v-if="companies && companies.length" class="space-y-4">
                <div
                    v-for="company in companies"
                    :key="company.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/[0.06] dark:bg-[#1a1a1a]"
                >
                    <!-- Company Row (clickable) -->
                    <button
                        @click="toggleCompany(company.id)"
                        class="w-full flex items-center gap-4 px-6 py-5 text-left transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.02] focus:outline-none"
                    >
                        <!-- Icon -->
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-md shadow-indigo-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                            </svg>
                        </div>

                        <!-- Company Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">{{ company.name }}</h3>
                            <div class="mt-0.5 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-500">
                                <span class="inline-flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Holding Company
                                </span>
                                <span>•</span>
                                <span>{{ company.groups ? company.groups.length : 0 }} Grup</span>
                                <span>•</span>
                                <span>{{ getCompanyOrgCount(company) }} Unit Organisasi</span>
                            </div>
                        </div>

                        <!-- Chevron -->
                        <div class="flex-shrink-0">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-slate-400 transition-transform duration-300"
                                :class="{ 'rotate-180': expandedCompanies.has(company.id) }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </button>

                    <!-- Expanded Detail -->
                    <transition
                        @enter="onEnter"
                        @after-enter="onAfterEnter"
                        @leave="onLeave"
                        @after-leave="onAfterLeave"
                    >
                        <div v-if="expandedCompanies.has(company.id)" class="overflow-hidden">
                            <div class="border-t border-slate-300 dark:border-slate-600">
                                <!-- Groups & Organizations Table (sama dengan StrategicPillar) -->
                                <div v-if="company.groups && company.groups.length > 0" class="w-full">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-slate-100 dark:bg-slate-800 border-b-2 border-slate-300 dark:border-slate-600">
                                                <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 border-r border-slate-300 dark:border-slate-600">
                                                    Nama Grup
                                                </th>
                                                <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                    Unit Organisasi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(group, gIdx) in company.groups" :key="group.id" class="border-b border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                <td class="px-6 py-4 border-r border-slate-300 dark:border-slate-600 text-center align-middle">
                                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                        {{ group.name }}
                                                    </div>
                                                </td>
                                                <td class="p-0 align-top">
                                                    <!-- Nested Organization Table (seperti themes di StrategicPillar) -->
                                                    <div v-if="group.organization && group.organization.length > 0" class="w-full">
                                                        <table class="w-full border-collapse">
                                                            <tbody>
                                                                <tr v-for="(org, oIdx) in group.organization" :key="org.id" class="border-b border-slate-300 dark:border-slate-600 last:border-b-0">
                                                                    <td class="px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-300 text-center w-16 border-r border-slate-300 dark:border-slate-600">
                                                                        {{ oIdx + 1 }}
                                                                    </td>
                                                                    <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-200">
                                                                        {{ org.name }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div v-else class="px-6 py-4">
                                                        <span class="text-sm text-slate-400 dark:text-slate-500 italic">
                                                            Belum ada unit organisasi
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- No Groups -->
                                <div v-else class="px-6 py-10 text-center">
                                    <span class="text-sm text-slate-400 dark:text-slate-500 italic">Belum ada grup untuk perusahaan ini</span>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/[0.06]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Data Perusahaan Kosong</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">
                    Silakan tambahkan profil perusahaan Anda melalui panel manajemen.
                </p>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    companies: {
        type: Array,
        default: () => []
    }
});

const expandedCompanies = reactive(new Set());

const toggleCompany = (id) => {
    if (expandedCompanies.has(id)) {
        expandedCompanies.delete(id);
    } else {
        expandedCompanies.add(id);
    }
};

const getCompanyOrgCount = (company) => {
    if (!company.groups) return 0;
    return company.groups.reduce((sum, g) => sum + (g.organization ? g.organization.length : 0), 0);
};

// Smooth expand/collapse transitions
const onEnter = (el) => {
    el.style.height = '0';
    el.style.opacity = '0';
    el.style.transition = 'height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease';
    // Force reflow
    el.offsetHeight;
    el.style.height = el.scrollHeight + 'px';
    el.style.opacity = '1';
};

const onAfterEnter = (el) => {
    el.style.height = 'auto';
    el.style.transition = '';
};

const onLeave = (el) => {
    el.style.height = el.scrollHeight + 'px';
    el.style.transition = 'height 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease';
    // Force reflow
    el.offsetHeight;
    el.style.height = '0';
    el.style.opacity = '0';
};

const onAfterLeave = (el) => {
    el.style.height = '';
    el.style.opacity = '';
    el.style.transition = '';
};
</script>