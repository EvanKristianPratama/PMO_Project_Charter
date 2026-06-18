<template>
    <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
        <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

        <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide uppercase">
                {{ activeSection?.label }}
            </h3>
            <div class="flex items-center gap-3 print:hidden">
                <!-- Save status indicator -->
                <span class="text-[11px] flex items-center gap-1.5 select-none">
                    <span v-if="isSavingTko" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                        <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else-if="saveTkoStatus === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Tersimpan
                    </span>
                    <span v-else-if="saveTkoStatus === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                        Gagal menyimpan
                    </span>
                    <span v-else-if="hasUnsavedChanges" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Belum disimpan
                    </span>
                    <span v-else class="text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-emerald-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tersimpan
                    </span>
                </span>

                <!-- Manual Save Button -->
                <button
                    @click="saveAll"
                    :disabled="isSavingTko"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    Simpan
                </button>
            </div>
        </div>

        <div class="mt-6 relative">
            <div v-if="!isEditorLoaded" class="flex flex-col items-center justify-center py-20 space-y-4">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-[#821f44] border-t-transparent"></div>
                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 animate-pulse">Memuat editor dokumen...</span>
            </div>
            <div v-show="isEditorLoaded" class="prose dark:prose-invert max-w-none text-slate-900 dark:text-white">
                <div ref="editorContainer" class="min-h-[500px] bg-white dark:bg-[#1a1a1a]"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, shallowRef, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';

const props = defineProps({
    activeSection: {
        type: Object,
        default: null
    },
    activeRegulation: {
        type: Object,
        default: null
    }
});

const isEditorLoaded = ref(false);
const editorContainer = ref(null);
const editorInstance = shallowRef(null);
const hasUnsavedChanges = ref(false);

const isSavingTko = ref(false);
const saveTkoStatus = ref(null);

function loadCKEditor() {
    return new Promise((resolve, reject) => {
        if (window.ClassicEditor) {
            resolve(window.ClassicEditor);
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js';
        script.onload = () => {
            resolve(window.ClassicEditor);
        };
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

function getActiveSectionHtml() {
    if (!props.activeSection) return '';
    return props.activeSection.content || '';
}

function initEditor() {
    if (editorInstance.value) {
        editorInstance.value.destroy().then(() => {
            editorInstance.value = null;
            createEditorInstance();
        });
    } else {
        createEditorInstance();
    }
}

function createEditorInstance() {
    if (!editorContainer.value) return;

    window.ClassicEditor.create(editorContainer.value, {
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        },
        toolbar: {
            items: [
                'heading',
                '|',
                'bold',
                'italic',
                '|',
                'bulletedList',
                'numberedList',
                'outdent',
                'indent',
                '|',
                'undo',
                'redo'
            ]
        },
        placeholder: 'Tulis dokumen TKO di sini...',
    })
    .then(editor => {
        editorInstance.value = editor;
        editor.setData(getActiveSectionHtml());
        isEditorLoaded.value = true;
        hasUnsavedChanges.value = false;

        editor.model.document.on('change:data', () => {
            hasUnsavedChanges.value = true;
        });
    })
    .catch(error => {
        console.error('Gagal menginisialisasi CKEditor:', error);
    });
}

async function saveAll() {
    if (!props.activeSection?.section_id) {
        console.warn('saveAll: No valid active section ID found.');
        return;
    }

    const secDbId = props.activeSection.section_id;

    if (!editorInstance.value) {
        console.warn('Editor belum siap, tidak dapat menyimpan.');
        return;
    }

    if (!props.activeRegulation?.id) {
        console.warn('Tidak ada regulasi aktif.');
        return;
    }

    const htmlContent = editorInstance.value.getData();

    isSavingTko.value = true;
    saveTkoStatus.value = 'saving';

    return new Promise((resolve, reject) => {
        router.post(
            route('policy.procedure.tko-content.store'),
            {
                regulation_id: props.activeRegulation.id,
                section_id: secDbId,
                content: htmlContent,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    saveTkoStatus.value = 'saved';
                    hasUnsavedChanges.value = false;
                    setTimeout(() => {
                        saveTkoStatus.value = null;
                    }, 3000);
                    isSavingTko.value = false;
                    resolve();
                },
                onError: (err) => {
                    console.error('Gagal menyimpan dokumen TKO:', err);
                    saveTkoStatus.value = 'error';
                    isSavingTko.value = false;
                    reject(err);
                }
            }
        );
    });
}

watch(() => props.activeSection?.id, (newId) => {
    if (editorInstance.value && newId) {
        editorInstance.value.setData(getActiveSectionHtml());
        hasUnsavedChanges.value = false;
    }
});

watch(() => props.activeRegulation?.id, () => {
    if (editorInstance.value) {
        editorInstance.value.setData(getActiveSectionHtml());
        hasUnsavedChanges.value = false;
    }
});

onMounted(() => {
    loadCKEditor().then(() => {
        initEditor();
    });
});

onUnmounted(() => {
    if (editorInstance.value) {
        editorInstance.value.destroy();
    }
});

defineExpose({
    hasUnsavedChanges,
    saveAll
});
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* CKEditor 5 Theme Overrides for Light & Dark Mode */
:deep(.ck-editor__editable_inline) {
    min-height: 500px;
    padding: 2rem !important;
    font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
    font-size: 15px;
    line-height: 1.8;
}

:deep(.ck.ck-editor__main > .ck-editor__editable) {
    border-bottom-left-radius: 0.75rem !important;
    border-bottom-right-radius: 0.75rem !important;
    border-color: #cbd5e1 !important;
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
}

:deep(.ck.ck-toolbar) {
    border-top-left-radius: 0.75rem !important;
    border-top-right-radius: 0.75rem !important;
    border-color: #cbd5e1 !important;
    background-color: #f8fafc !important;
    padding: 0.5rem !important;
}

/* Dark mode overrides */
.dark :deep(.ck.ck-editor__main > .ck-editor__editable) {
    background-color: #171717 !important;
    color: #f3f4f6 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.dark :deep(.ck.ck-toolbar) {
    background-color: #262626 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.dark :deep(.ck.ck-toolbar .ck-button) {
    color: #d1d5db !important;
}

.dark :deep(.ck.ck-toolbar .ck-button:hover) {
    background-color: #374151 !important;
    color: #ffffff !important;
}

.dark :deep(.ck.ck-toolbar .ck-button.ck-on) {
    background-color: #4b5563 !important;
    color: #ffffff !important;
}

.dark :deep(.ck.ck-dropdown__panel) {
    background-color: #1f2937 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.dark :deep(.ck.ck-list) {
    background-color: #1f2937 !important;
}

.dark :deep(.ck.ck-list__item .ck-button:hover) {
    background-color: #374151 !important;
    color: #ffffff !important;
}

.dark :deep(.ck.ck-list__item .ck-button.ck-on) {
    background-color: #4b5563 !important;
    color: #ffffff !important;
}

/* Custom nested list styles inside editor content area */
:deep(.ck-content ol) {
    list-style-type: none !important;
    counter-reset: level1-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.ck-content ol > li) {
    counter-increment: level1-counter !important;
    position: relative !important;
}
:deep(.ck-content ol > li::before) {
    content: counter(level1-counter) ". " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
    font-weight: inherit !important;
}

:deep(.ck-content ol ol) {
    counter-reset: level2-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.ck-content ol ol > li) {
    counter-increment: level2-counter !important;
}
:deep(.ck-content ol ol > li::before) {
    content: counter(level2-counter, lower-alpha) ". " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
}

:deep(.ck-content ol ol ol) {
    counter-reset: level3-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.ck-content ol ol ol > li) {
    counter-increment: level3-counter !important;
}
:deep(.ck-content ol ol ol > li::before) {
    content: counter(level3-counter) ") " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
}

:deep(.ck-content ol ol ol ol) {
    counter-reset: level4-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.ck-content ol ol ol ol > li) {
    counter-increment: level4-counter !important;
}
:deep(.ck-content ol ol ol ol > li::before) {
    content: counter(level4-counter, lower-alpha) ") " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
}
</style>
