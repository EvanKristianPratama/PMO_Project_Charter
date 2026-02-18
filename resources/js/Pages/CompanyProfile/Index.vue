<template>
    <UserLayout title="Company Profile">
        <div class="animate-fade-in">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Company Profile</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Corporate organizational structure with
                        groups and business units</p>
                </div>
            </div>
            <div class="py-5">
                <div v-if="companies && companies.length" class="space-y-12">
                    <div v-for="company in companies" :key="company.id">

                        <div class="flex items-center gap-3 mb-6 group">
                            <div
                                class="flex-none w-8 h-8 rounded-lg border border-slate-300 dark:border-slate-700 flex items-center justify-center bg-white dark:bg-slate-900 shadow-sm group-hover:border-indigo-500 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-4 h-4 text-indigo-600">
                                    <path
                                        d="M4.5 3.75a.75.75 0 00-1.5 0v16.5a.75.75 0 001.5 0V3.75zM19.5 3.75a.75.75 0 00-1.5 0v16.5a.75.75 0 001.5 0V3.75zM6.75 6a.75.75 0 01.75-.75h9a.75.75 0 01.75.75v12a.75.75 0 01-.75.75h-9a.75.75 0 01-.75-.75V6z" />
                                </svg>
                            </div>
                            <h2
                                class="text-base font-black text-slate-800 dark:text-slate-100 tracking-tight uppercase">
                                {{ company.name }}</h2>
                            <div
                                class="h-px flex-grow bg-gradient-to-r from-slate-300 to-transparent dark:from-slate-800">
                            </div>
                        </div>

                        <div v-if="company.groups && company.groups.length"
                            class="flex flex-wrap gap-x-12 gap-y-12 ml-4 w-full">

                            <div v-for="group in company.groups" :key="group.id" class="flex flex-col flex-none w-50">

                                <h3
                                    class="relative text-[10px] font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2 mb-3 uppercase tracking-[0.15em]">
                                    <span
                                        class="relative z-10 w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_6px_rgba(99,102,241,0.5)]"></span>
                                    {{ group.name }}
                                </h3>

                                <div v-if="group.organization && group.organization.length"
                                    class="relative ml-[3px] pl-5 overflow-hidden -mt-4 pt-4">

                                    <div
                                        class="absolute left-0 top-0 bottom-[1.3rem] w-0.5 bg-slate-300 dark:bg-slate-800">
                                    </div>

                                    <div v-for="org in group.organization" :key="org.id"
                                        class="relative flex items-center group/item py-1.5">

                                        <div
                                            class="absolute -left-5 bottom-1/2 h-[1000px] w-0.5 bg-indigo-500 opacity-0 transition-opacity duration-200 group-hover/item:opacity-100">
                                        </div>

                                        <div
                                            class="absolute -left-5 top-1/2 w-5 h-0.5 bg-slate-300 dark:bg-slate-800 group-hover/item:bg-indigo-500 transition-colors">
                                        </div>

                                        <div
                                            class="z-10 flex items-center justify-between w-full p-2 px-3 rounded-md border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900/50 shadow-sm hover:border-indigo-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-default">
                                            <div class="flex items-center gap-2 overflow-hidden">
                                                <span
                                                    class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover/item:text-indigo-700 dark:group-hover/item:text-white transition-colors truncate">
                                                    {{ org.name }}
                                                </span>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3 text-slate-400 group-hover/item:text-indigo-500 transition-colors shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="ml-6 py-1 text-[10px] text-slate-400 italic">
                                    No business unit
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else
                    class="text-center py-20 border border-dashed border-slate-300 dark:border-slate-800 rounded-2xl">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Data Tidak Tersedia</p>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    companies: Array
})
</script>