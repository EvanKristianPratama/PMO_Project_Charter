<template>
    <ModulLayout title="CMS">
        <div class="space-y-6 animate-fade-in-up">
            <!-- Header Section -->
            <div class="bg-white dark:bg-[#171717] rounded-2xl border border-slate-200 dark:border-white/10 p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                        <FolderIcon class="w-7 h-7 text-[#821f44]" />
                        Content Manegement System
                    </h1>
                </div>
            </div>

            <!-- Content Area -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 items-start">
                
                <!-- Sidebar: List of Files -->
                <div class="lg:col-span-1 space-y-4">
                    
                    <!-- Search Input -->
                    <div class="relative bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm p-3">
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Cari file..." 
                            class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg focus:outline-none focus:border-[#821f44] dark:focus:border-[#821f44] text-slate-900 dark:text-white placeholder-slate-400"
                        />
                        <MagnifyingGlassIcon class="w-4 h-4 absolute left-6 top-[22px] text-slate-400" />
                    </div>

                    <!-- Category Tab Switches -->
                    <div class="flex rounded-xl bg-slate-100 p-1 dark:bg-white/5 w-full">
                        <button 
                            @click="activeTab = 'all'" 
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
                            :class="activeTab === 'all' 
                                ? 'bg-white dark:bg-[#2c2c2c] text-slate-900 dark:text-white shadow-sm' 
                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        >
                            Semua
                        </button>
                        <button 
                            @click="activeTab = 'ppt'" 
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
                            :class="activeTab === 'ppt' 
                                ? 'bg-white dark:bg-[#2c2c2c] text-[#821f44] dark:text-[#db588c] shadow-sm' 
                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        >
                            PPT
                        </button>
                        <button 
                            @click="activeTab = 'pdf'" 
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
                            :class="activeTab === 'pdf' 
                                ? 'bg-white dark:bg-[#2c2c2c] text-blue-600 dark:text-blue-400 shadow-sm' 
                                : 'text-slate-500 dark:text-slate-400 hover:text-[#821f44] dark:hover:text-white'"
                        >
                            PDF
                        </button>
                    </div>

                    <!-- File List Card -->
                    <div class="bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden flex flex-col max-h-[600px]">
                        <div class="p-4 border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex items-center justify-between">
                            <h2 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 text-sm">
                                <FolderIcon class="w-4 h-4 text-[#821f44]" />
                                File Tersedia
                            </h2>
                            <span class="px-2 py-0.5 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded text-xs font-bold">
                                {{ filteredFiles.length }}
                            </span>
                        </div>  

                        <ul class="divide-y divide-slate-100 dark:divide-white/5 overflow-y-auto flex-1">
                            <li v-for="file in filteredFiles" :key="file.uuid" 
                                @click="selectFile(file)"
                                class="p-4 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer transition-all relative group flex items-center justify-between"
                                :class="{'bg-[#821f44]/5 dark:bg-[#821f44]/10': selectedDocument && selectedDocument.uuid === file.uuid}"
                            >
                                <div v-if="selectedDocument && selectedDocument.uuid === file.uuid" class="absolute left-0 top-0 bottom-0 w-1 bg-[#821f44]"></div>
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="p-2 rounded transition-transform group-hover:scale-110 shrink-0"
                                        :class="isPdf(file) 
                                            ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                                            : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'"
                                    >
                                        <DocumentIcon v-if="isPdf(file)" class="w-4 h-4" />
                                        <PresentationChartBarIcon v-else class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate" :class="{'text-[#821f44] dark:text-[#db588c]': selectedDocument && selectedDocument.uuid === file.uuid}">
                                            {{ file.name }}
                                        </p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium uppercase">
                                            {{ file.extension }} • {{ formatSize(file.size) }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity ml-2 shrink-0">
                                    <ChevronRightIcon class="w-4 h-4 text-slate-300 group-hover:text-[#821f44] transition-colors" />
                                </div>
                            </li>
                            
                            <li v-if="filteredFiles.length === 0" class="p-12 text-center">
                                <div class="w-12 h-12 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <InboxIcon class="w-6 h-6 text-slate-300" />
                                </div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Tidak ada file yang cocok</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content: File Viewer / Detail -->
                <div class="lg:col-span-3">
                    <div v-if="selectedDocument" class="space-y-4 animate-fade-in">
                        <!-- Viewer Header Info -->
                        <div class="bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 p-4 shadow-sm flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="p-2.5 rounded shrink-0"
                                    :class="isPdf(selectedDocument) 
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'"
                                >
                                    <DocumentIcon v-if="isPdf(selectedDocument)" class="w-6 h-6" />
                                    <PresentationChartBarIcon v-else class="w-6 h-6" />
                                </div>
                                <div class="min-w-0">
                                    <h2 class="font-bold text-base text-slate-900 dark:text-white truncate">{{ selectedDocument.name }}</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Ukuran: {{ formatSize(selectedDocument.size) }} • Tipe: {{ selectedDocument.extension.toUpperCase() }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0">
                                <a 
                                    :href="route('itom.libary.document.download', { uuid: selectedDocument.uuid })" 
                                    class="px-3 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-slate-800 dark:text-white rounded-lg transition-all flex items-center gap-1.5"
                                >
                                    <ArrowDownTrayIcon class="w-4 h-4" />
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Viewer Component Selection -->
                        <div v-if="isPdf(selectedDocument)" class="bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden p-2">
                            <iframe 
                                :src="selectedDocument.url" 
                                class="w-full h-[650px] rounded-lg border border-slate-100 dark:border-white/5" 
                                type="application/pdf"
                            ></iframe>
                        </div>
                        <div v-else class="space-y-6">
                            <!-- PPT Slides rendering using LibaryClientViewer component -->
                            <LibaryClientViewer :ppt="selectedDocument" :key="selectedDocument.uuid" />
                        </div>
                    </div>
                    
                    <!-- Selection Empty State -->
                    <div v-else class="flex flex-col items-center justify-center min-h-[550px] bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm text-center p-12">
                        <div class="relative mb-6">
                            <div class="absolute inset-0 bg-[#821f44] blur-3xl opacity-10 rounded-full animate-pulse"></div>
                            <div class="relative w-28 h-28 bg-slate-50 dark:bg-white/5 rounded-3xl flex items-center justify-center border border-slate-100 dark:border-white/10">
                                <FolderOpenIcon class="w-14 h-14 text-slate-300 dark:text-slate-700" />
                            </div>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">Belum Ada File Terpilih</h2>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed text-sm">
                            Pilih salah satu file dari daftar di samping untuk melihat pratinjau dokumen regulasi PDF atau slide PowerPoint.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import ModulLayout from '@/Layouts/ModulLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import LibaryClientViewer from '@/Components/modules/ITOM/Libary/LibaryClientViewer.vue';
import { 
    PresentationChartBarIcon, 
    ChevronRightIcon, 
    FolderIcon,
    FolderOpenIcon,
    MagnifyingGlassIcon,
    InboxIcon,
    ArrowDownTrayIcon,
    DocumentIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    documents: {
        type: Array,
        default: () => []
    },
    selectedDocument: {
        type: Object,
        default: null
    }
});

const searchQuery = ref('');
const activeTab = ref('all'); // 'all', 'ppt', 'pdf'

const isPdf = (file) => {
    return file && (file.extension === 'pdf' || file.mime_type === 'application/pdf');
};

const formatSize = (bytes) => {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const filteredFiles = computed(() => {
    let list = props.documents;
    
    // Filter by tab
    if (activeTab.value === 'ppt') {
        list = list.filter(f => ['ppt', 'pptx'].includes(f.extension.toLowerCase()));
    } else if (activeTab.value === 'pdf') {
        list = list.filter(f => f.extension.toLowerCase() === 'pdf');
    }

    // Filter by query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        list = list.filter(file => file.name.toLowerCase().includes(query));
    }

    return list;
});

const selectFile = (file) => {
    router.visit(route('itom.policy.CMS.show', { uuid: file.uuid }), {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out;
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
