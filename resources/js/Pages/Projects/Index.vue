<template>
    <UserLayout title="Projects">
        <div class="animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Projects</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Manage projects and charters</p>
                </div>
                <Link
                    href="/projects/create"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    + New Project
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3 mb-6 bg-white dark:bg-[#1a1a1a] p-4 rounded-xl border border-slate-200 dark:border-white/5">
                <div class="flex-1 relative">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by name or code..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-indigo-500 focus:border-indigo-500"
                        @input="debouncedSearch"
                    />
                    <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select v-model="filters.status" @change="applyFilters" class="rounded-lg border-slate-300 dark:border-white/10 bg-white dark:bg-[#131313] text-slate-700 dark:text-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="project in projects.data" :key="project.id" class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-white/10 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-white/10">
                                {{ project.code }}
                            </span>
                            <StatusBadge :status="project.status" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1 line-clamp-1">{{ project.name }}</h3>
                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-4">
                            <UserAvatar v-if="project.owner" :user="project.owner" class="w-6 h-6 text-[10px]" />
                            <span>{{ project.owner?.name || 'Unassigned' }}</span>
                        </div>
                        <div class="flex gap-2 mt-4 pt-4 border-t border-slate-100 dark:border-white/5">
                            <Link :href="`/projects/${project.id}`" class="flex-1 text-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 py-1.5 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors">
                                View Charter
                            </Link>
                            <Link :href="`/projects/${project.id}/edit`" class="p-1.5 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-if="projects.data.length === 0" class="text-center py-12 bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 mt-6">
                <p class="text-slate-500 dark:text-slate-400">No projects found.</p>
                <Link href="/projects/create" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline mt-2 inline-block">Create your first project</Link>
            </div>

            <!-- Pagination -->
             <div v-if="projects.links && projects.links.length > 3" class="mt-6 flex justify-center">
                 <div class="flex gap-1">
                    <Link
                        v-for="(link, k) in projects.links"
                        :key="k"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 rounded bg-white dark:bg-[#1a1a1a] border border-slate-200 dark:border-white/10 text-sm text-slate-700 dark:text-slate-300"
                        :class="{'bg-indigo-600 text-white border-indigo-600': link.active, 'opacity-50 pointer-events-none': !link.url}"
                    />
                 </div>
             </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import UserAvatar from '@/Components/UserAvatar.vue';

const props = defineProps({
    projects: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

let timeout = null;
const debouncedSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 300);
};

const applyFilters = () => {
    router.get('/projects', filters.value, { preserveState: true, replace: true });
};
</script>
