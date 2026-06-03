<template>
    <UserLayout title="Libary PPT">
        <div class="space-y-6 animate-fade-in-up">
            <header class="rounded-xl bg-gradient-to-r from-red-600 to-rose-700 p-6 shadow-md dark:from-[#8C1D13] dark:to-[#A42518] dark:border dark:border-white/10">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-black text-white tracking-tight">Presentation <span class="font-bold">Libary</span></h1>
                        <p class="text-red-100/80 text-sm mt-1">Kelola dan lihat dokumen presentasi (PPT/PPTX)</p>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <!-- Sidebar: List of PPTs -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden flex flex-col h-full max-h-[800px]">
                        <div class="p-4 border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex items-center justify-between">
                            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <FolderIcon class="w-5 h-5 text-red-600" />
                                Available Files
                            </h2>
                            <span class="px-2 py-0.5 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded text-xs font-bold">{{ pptFiles.length }}</span>
                        </div>
                        
                        <div class="p-4">
                            <div class="relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="Search files..." 
                                    class="w-full pl-9 pr-4 py-2 bg-slate-100 dark:bg-white/5 border-none rounded-lg text-sm focus:ring-2 focus:ring-red-600 transition-all"
                                />
                            </div>
                        </div>

                        <ul class="divide-y divide-slate-100 dark:divide-white/5 overflow-y-auto flex-1">
                            <li v-for="file in filteredFiles" :key="file.name" 
                                @click="selectPpt(file)"
                                class="p-4 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer transition-colors relative group"
                                :class="{'bg-red-50 dark:bg-red-900/10': selectedPpt && selectedPpt.name === file.name}"
                            >
                                <div v-if="selectedPpt && selectedPpt.name === file.name" class="absolute left-0 top-0 bottom-0 w-1 bg-red-600"></div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded group-hover:scale-110 transition-transform">
                                        <PresentationChartBarIcon class="w-5 h-5 text-red-600" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate" :class="{'text-red-600 dark:text-red-500': selectedPpt && selectedPpt.name === file.name}">
                                            {{ file.name }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 uppercase font-medium tracking-wider">
                                            {{ file.name.split('.').pop() }}
                                        </p>
                                    </div>
                                    <ChevronRightIcon class="w-4 h-4 text-slate-300 group-hover:text-red-600 transition-colors" />
                                </div>
                            </li>
                            
                            <li v-if="filteredFiles.length === 0" class="p-12 text-center">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <InboxIcon class="w-8 h-8 text-slate-300" />
                                </div>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No matching files</p>
                            </li>
                        </ul>
                        
                        <div class="p-4 bg-slate-50 dark:bg-white/5 border-t border-slate-200 dark:border-white/10">
                            <div class="p-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-100 dark:border-red-900/20">
                                <p class="text-[10px] text-red-700 dark:text-red-400 leading-relaxed">
                                    <strong>Note:</strong> Unggah file ke <code>storage/app/public/ppt</code> untuk menambah daftar ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content: Viewer -->
                <div class="lg:col-span-3">
                    <div v-if="selectedPpt" class="space-y-6 animate-fade-in">
                        <LibaryDetail :ppt="selectedPpt" />
                    </div>
                    
                    <div v-else class="flex flex-col items-center justify-center min-h-[600px] bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm text-center p-12">
                        <div class="relative mb-8">
                            <div class="absolute inset-0 bg-red-600 blur-3xl opacity-10 rounded-full animate-pulse"></div>
                            <div class="relative w-32 h-32 bg-slate-50 dark:bg-white/5 rounded-3xl flex items-center justify-center border border-slate-100 dark:border-white/10">
                                <PresentationChartBarIcon class="w-16 h-16 text-slate-200 dark:text-slate-800" />
                            </div>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">Belum Ada File Terpilih</h2>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                            Pilih salah satu file presentasi dari daftar di samping untuk melihat detail dan pratinjau rendering.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import LibaryDetail from '@/Components/Libary/Libary.vue';
import { 
    PresentationChartBarIcon, 
    ChevronRightIcon, 
    FolderIcon,
    MagnifyingGlassIcon,
    InboxIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    pptFiles: {
        type: Array,
        default: () => []
    },
    selectedPpt: {
        type: Object,
        default: null
    }
});

const searchQuery = ref('');

const filteredFiles = computed(() => {
    if (!searchQuery.value) return props.pptFiles;
    const query = searchQuery.value.toLowerCase();
    return props.pptFiles.filter(file => file.name.toLowerCase().includes(query));
});

const selectPpt = (file) => {
    router.visit(route('libary.show', { filename: file.name }), {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
