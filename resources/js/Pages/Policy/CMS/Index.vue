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
                <!-- Upload Button/Toggle -->
                <button 
                    @click="showUploadModal = true" 
                    class="px-4 py-2.5 bg-[#821f44] hover:bg-[#6b1837] text-white font-bold rounded-xl transition-all shadow-md shadow-[#821f44]/25 flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]"
                >
                    <ArrowUpTrayIcon class="w-4 h-4" />
                    Upload File Baru
                </button>
            </div>

            <!-- Upload Area (Modal / Collapsible Panel) -->
            <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div class="bg-white dark:bg-[#171717] rounded-2xl border border-slate-200 dark:border-white/10 w-full max-w-lg shadow-2xl overflow-hidden">
                    <div class="p-6 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <CloudArrowUpIcon class="w-6 h-6 text-[#821f44]" />
                            Unggah File Baru
                        </h3>
                        <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <XMarkIcon class="w-6 h-6" />
                        </button>
                    </div>
                    <form @submit.prevent="submitUpload" class="p-6 space-y-4">
                        <div 
                            @dragover.prevent="dragover = true"
                            @dragleave.prevent="dragover = false"
                            @drop.prevent="handleDrop"
                            class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all flex flex-col items-center justify-center min-h-[200px]"
                            :class="dragover 
                                ? 'border-[#821f44] bg-[#821f44]/5 dark:bg-[#821f44]/10' 
                                : 'border-slate-300 hover:border-[#821f44] dark:border-white/10 dark:hover:border-[#821f44]'"
                            @click="$refs.fileInput.click()"
                        >
                            <input 
                                type="file" 
                                ref="fileInput" 
                                class="hidden" 
                                accept=".pdf,.ppt,.pptx" 
                                @change="handleFileSelect"
                            />
                            
                            <div class="p-4 bg-slate-100 dark:bg-white/5 rounded-full mb-3 text-[#821f44] transition-transform group-hover:scale-110">
                                <ArrowUpTrayIcon class="w-8 h-8" />
                            </div>

                            <p class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                                {{ selectedFileToUpload ? selectedFileToUpload.name : 'Pilih file atau seret ke sini' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                PDF, PPT, atau PPTX (Maks. 20MB)
                            </p>
                        </div>

                        <!-- Progress Bar / Status -->
                        <div v-if="form.processing" class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-slate-600 dark:text-slate-400">
                                <span>Mengunggah file...</span>
                                <span>{{ form.progress ? form.progress.percentage : 0 }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-white/5 h-2 rounded-full overflow-hidden">
                                <div class="bg-[#821f44] h-full transition-all duration-300" :style="{ width: (form.progress ? form.progress.percentage : 0) + '%' }"></div>
                            </div>
                        </div>

                        <div v-if="form.errors.file" class="p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/20 text-red-600 dark:text-red-400 text-xs rounded-lg font-bold">
                            {{ form.errors.file }}
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-white/10">
                            <button 
                                type="button" 
                                @click="showUploadModal = false" 
                                class="px-4 py-2 border border-slate-300 dark:border-white/10 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-semibold transition-all"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                :disabled="!selectedFileToUpload || form.processing" 
                                class="px-4 py-2 bg-[#821f44] hover:bg-[#6b1837] text-white font-bold rounded-xl text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Unggah Sekarang
                            </button>
                        </div>
                    </form>
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
                                    <button 
                                        @click.stop="deleteFile(file.uuid)" 
                                        class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                        title="Hapus"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
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
                        
                        <div class="p-3 bg-slate-50 dark:bg-white/5 border-t border-slate-200 dark:border-white/10">
                            <div class="p-2.5 bg-[#821f44]/5 dark:bg-[#821f44]/10 rounded-lg border border-[#821f44]/10">
                                <p class="text-[10px] text-[#821f44] dark:text-[#db588c] leading-relaxed">
                                    <strong>Keamanan:</strong> Semua file disimpan secara aman di direktori privat dan tidak dapat diakses publik secara langsung.
                                </p>
                            </div>
                        </div>
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
                                    :href="route('libary.document.download', { uuid: selectedDocument.uuid })" 
                                    class="px-3 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-white/5 dark:hover:bg-white/10 text-slate-800 dark:text-white rounded-lg transition-all flex items-center gap-1.5"
                                >
                                    <ArrowDownTrayIcon class="w-4 h-4" />
                                    Download
                                </a>
                                <button 
                                    @click="deleteFile(selectedDocument.uuid)" 
                                    class="px-3 py-1.5 text-xs font-bold bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-600 dark:text-red-400 rounded-lg transition-all flex items-center gap-1.5"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                    Hapus
                                </button>
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
import { router, useForm } from '@inertiajs/vue3';
import LibaryClientViewer from '@/Components/Libary/LibaryClientViewer.vue';
import { 
    PresentationChartBarIcon, 
    ChevronRightIcon, 
    FolderIcon,
    FolderOpenIcon,
    MagnifyingGlassIcon,
    InboxIcon,
    ArrowUpTrayIcon,
    DocumentIcon,
    TrashIcon,
    CloudArrowUpIcon,
    XMarkIcon
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
const showUploadModal = ref(false);
const dragover = ref(false);
const selectedFileToUpload = ref(null);
const fileInput = ref(null);

const form = useForm({
    file: null
});

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
    router.visit(route('policy.CMS.show', { uuid: file.uuid }), {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteFile = (uuid) => {
    if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
        router.delete(route('policy.CMS.document.destroy', { uuid }), {
            preserveScroll: true
        });
    }
};

const handleFileSelect = (e) => {
    const files = e.target.files;
    if (files.length > 0) {
        selectedFileToUpload.value = files[0];
        form.file = files[0];
    }
};

const handleDrop = (e) => {
    dragover.value = false;
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        selectedFileToUpload.value = files[0];
        form.file = files[0];
    }
};

const submitUpload = () => {
    if (!form.file) return;
    form.post(route('policy.CMS.upload'), {
        forceFormData: true,
        onSuccess: () => {
            showUploadModal.value = false;
            selectedFileToUpload.value = null;
            form.reset();
        }
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
