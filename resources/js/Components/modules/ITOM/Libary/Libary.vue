<template>
    <div class="bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded text-red-600">
                    <PresentationChartBarIcon class="w-6 h-6" />
                </div>
                <h2 class="font-bold text-lg text-slate-900 dark:text-white truncate">{{ ppt.name }}</h2>
            </div>
            <div class="flex gap-2 shrink-0">
                <a :href="ppt.url" download class="px-4 py-2 text-sm font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Download
                </a>
                <button @click="openExternal" class="px-4 py-2 text-sm font-semibold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-200 rounded-lg hover:bg-slate-200 dark:hover:bg-white/20 transition-all flex items-center gap-2">
                    <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                    Open Raw
                </button>
            </div>
        </div>
        
        <div class="p-0 bg-slate-50 dark:bg-black/20 min-h-[500px] flex items-center justify-center relative">
            <!-- Iframe for rendering -->
            <div v-if="viewingHtml" class="w-full h-[700px] relative">
                <iframe 
                    :src="route('itom.libary.render', { filename: ppt.name })" 
                    class="w-full h-full border-none"
                    @load="iframeLoaded = true"
                ></iframe>
                <div v-if="!iframeLoaded" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-black/50 backdrop-blur-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 border-4 border-red-600 border-t-transparent rounded-full animate-spin mb-3"></div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Rendering with PHPPresentation...</p>
                    </div>
                </div>
            </div>
            
            <div v-else class="text-center p-12 w-full max-w-2xl mx-auto">
                <div class="p-8 bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-xl border border-slate-200 dark:border-white/5">
                    <div class="w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <PresentationChartBarIcon class="w-12 h-12 text-red-600" />
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Presentation Preview</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                        Anda dapat melihat pratinjau file ini sebagai HTML yang dirender oleh library PHPPresentation atau mengunduhnya langsung.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button @click="viewingHtml = true" class="flex items-center justify-center gap-2 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all active:scale-95 shadow-lg shadow-red-600/20">
                            <EyeIcon class="w-5 h-5" />
                            Render HTML
                        </button>
                        <a :href="ppt.url" download class="flex items-center justify-center gap-2 py-3 bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-white/20 transition-all active:scale-95">
                            <ArrowDownTrayIcon class="w-5 h-5" />
                            Download PPT
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { 
    PresentationChartBarIcon, 
    ArrowDownTrayIcon, 
    ArrowTopRightOnSquareIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    ppt: {
        type: Object,
        required: true
    }
});

const viewingHtml = ref(false);
const iframeLoaded = ref(false);

watch(() => props.ppt.name, () => {
    viewingHtml.value = false;
    iframeLoaded.value = false;
});

const openExternal = () => {
    window.open(route('itom.libary.render', { filename: props.ppt.name }), '_blank');
};
</script>
