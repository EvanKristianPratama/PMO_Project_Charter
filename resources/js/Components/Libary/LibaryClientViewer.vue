<template>
    <div class="bg-white dark:bg-[#171717] rounded-xl border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded text-red-600">
                    <PresentationChartBarIcon class="w-6 h-6" />
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-lg text-slate-900 dark:text-white truncate">{{ ppt.name }}</h2>
                    <p v-if="slides.length" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        {{ slides.length }} slides
                    </p>
                </div>
            </div>
            <div class="flex gap-2 shrink-0">
                <a :href="ppt.url" download class="px-4 py-2 text-sm font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    Download
                </a>
            </div>
        </div>

        <!-- Loading Status -->
        <div v-if="isLoading" class="p-8 bg-slate-50 dark:bg-black/20 min-h-[500px] flex items-center justify-center">
            <div class="text-center">
                <div class="w-12 h-12 border-4 border-red-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    {{ loadingMessage }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                    {{ renderProgress }}%
                </p>
            </div>
        </div>

        <!-- Slides Gallery -->
        <div v-else-if="slides.length > 0" class="p-6 bg-slate-50 dark:bg-black/20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="(slide, idx) in slides" 
                    :key="idx"
                    @click="selectSlide(idx)"
                    class="group cursor-pointer relative overflow-hidden rounded-lg border-2 transition-all"
                    :class="selectedSlideIndex === idx 
                        ? 'border-red-600 shadow-xl shadow-red-600/20' 
                        : 'border-slate-200 dark:border-white/10 hover:border-red-400 dark:hover:border-red-500'"
                >
                    <div class="relative aspect-video bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <img 
                            :src="slide.imageUrl"
                            :alt="`Slide ${slide.number}`"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all"></div>
                    </div>
                    <div class="p-3 bg-white dark:bg-slate-900">
                        <p class="font-semibold text-sm text-slate-900 dark:text-white">
                            Slide {{ slide.number }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Selected Slide Detail View -->
            <div v-if="selectedSlideIndex !== null && slides[selectedSlideIndex]" class="mt-8 pt-8 border-t border-slate-200 dark:border-white/10">
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                        Slide {{ slides[selectedSlideIndex].number }} Detail
                    </h3>
                    <div class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10 shadow-lg">
                        <img 
                            :src="slides[selectedSlideIndex].imageUrl"
                            :alt="`Slide ${slides[selectedSlideIndex].number} detail`"
                            class="w-full h-auto"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Error or Empty State -->
        <div v-else class="flex items-center justify-center min-h-[500px] text-center p-12 bg-slate-50 dark:bg-black/20">
            <div class="p-8 bg-white dark:bg-[#1e1e1e] rounded-2xl shadow-xl border border-slate-200 dark:border-white/5">
                <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <ExclamationTriangleIcon v-if="error" class="w-12 h-12 text-red-600" />
                    <PresentationChartBarIcon v-else class="w-12 h-12 text-slate-400" />
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                    {{ error ? 'Error Loading File' : 'No Slides' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6 leading-relaxed max-w-sm mx-auto">
                    {{ error || 'Could not extract slides from this file.' }}
                </p>
                <button 
                    v-if="error"
                    @click="loadPptFile"
                    class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all"
                >
                    Retry
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { 
    PresentationChartBarIcon, 
    ArrowDownTrayIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';
import { PptxParser } from '@/Utils/PptxParser';

const props = defineProps({
    ppt: {
        type: Object,
        required: true
    }
});

const isLoading = ref(true);
const loadingMessage = ref('Opening file...');
const renderProgress = ref(0);
const slides = ref([]);
const selectedSlideIndex = ref(null);
const error = ref(null);

onMounted(() => {
    loadPptFile();
});

const loadPptFile = async () => {
    try {
        isLoading.value = true;
        error.value = null;
        loadingMessage.value = 'Opening file...';
        renderProgress.value = 0;
        slides.value = [];

        // Get filename from ppt object
        const filename = props.ppt.name || props.ppt.filename;
        const pptName = filename.replace(/\.[^/.]+$/, ''); // Remove extension

        // First, try to load pre-converted PNG images (high quality)
        const pngImages = await tryLoadPngImages(pptName);
        if (pngImages && pngImages.length > 0) {
            loadingMessage.value = 'Loading pre-rendered slides...';
            slides.value = pngImages;
            isLoading.value = false;
            return;
        }

        // Fallback: Parse and render from PPTX XML
        loadingMessage.value = 'Fetching file...';
        const fileUrl = props.ppt.url || `/storage/${props.ppt.path}`;
        const response = await fetch(fileUrl);
        const arrayBuffer = await response.arrayBuffer();

        loadingMessage.value = 'Parsing PowerPoint...';
        renderProgress.value = 10;

        // Parse PPTX file dengan custom parser
        const parser = new PptxParser();
        const presentation = await parser.parse(arrayBuffer);

        loadingMessage.value = 'Rendering slides...';

        // Render setiap slide
        const renderedSlides = [];
        const totalSlides = presentation.slideCount || presentation.slides.length;

        for (let i = 0; i < presentation.slides.length; i++) {
            try {
                const slide = presentation.slides[i];
                
                // Create canvas untuk render slide
                const canvas = document.createElement('canvas');
                canvas.width = 960;
                canvas.height = 540;
                
                // Render slide dengan parser
                const imageUrl = parser.renderSlideToCanvas(slide, canvas, 960, 540);
                
                renderedSlides.push({
                    number: slide.number,
                    imageUrl: imageUrl,
                    slide: slide
                });
                
                renderProgress.value = Math.round(((i + 1) / totalSlides) * 100);
                loadingMessage.value = `Rendered ${i + 1} of ${totalSlides} slides...`;
                
            } catch (err) {
                console.error(`Error rendering slide ${i + 1}:`, err);
            }
        }

        slides.value = renderedSlides;

        if (slides.value.length === 0) {
            error.value = 'No slides could be rendered from this file.';
        }

        isLoading.value = false;

    } catch (err) {
        console.error('Error loading PPT file:', err);
        error.value = `Failed to load file: ${err.message}`;
        isLoading.value = false;
    }
};

/**
 * Try to load pre-converted PNG images from server
 */
const tryLoadPngImages = async (pptName) => {
    try {
        // Check if PNG images directory exists and has slides
        const response = await fetch(`/api/ppt-images/${pptName}/list`);
        
        if (!response.ok) {
            console.log('PNG images not found, will use XML parsing');
            return null;
        }

        const data = await response.json();
        if (!data.slides || data.slides.length === 0) {
            console.log('No PNG slides found');
            return null;
        }

        // Load PNG images
        const pngSlides = data.slides.map((slide, idx) => ({
            number: idx + 1,
            imageUrl: slide.url,
            isPng: true
        }));

        console.log(`Loaded ${pngSlides.length} pre-rendered PNG slides`);
        return pngSlides;

    } catch (err) {
        console.warn('Error loading PNG images:', err);
        return null;
    }
};

const selectSlide = (index) => {
    selectedSlideIndex.value = selectedSlideIndex.value === index ? null : index;
};
</script>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
