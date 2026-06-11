<template>
    <UserLayout title="Kelola Procedure">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header (sama untuk manage & view) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">
                                {{ activeRegulation?.judul || 'Belum ada regulasi aktif' }}
                            </p>
                            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                                Kelola Procedure
                            </h1>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <Link
                                :href="route('policy.procedure.index', activeRegulation ? { regulation_id: activeRegulation.id } : {})"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                </svg>
                                Lihat Dokumen
                            </Link>
                        </div>
                    </div>
                    <!-- Tab Switcher (persis seperti GuidanceChapterNavigation) -->
                    <ProcedureSectionNavigation v-model="activeTab" :sections="allSections" />
                </div>
            </section>

            <!-- Tab: TKO Content -->
            <template v-if="activeSection?.type === 'tko'">
                <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
                    <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

                    <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide uppercase">
                            {{ activeSection.label }}
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
                                @click="saveTkoContent()"
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

            <!-- Tab: FUNGSI -->
            <template v-if="activeSection?.id === 'fungsi'">
                <FungsiEditor
                    ref="fungsiEditorRef"
                    :actors="actors"
                    :organizations="organizations"
                    :activeRegulation="activeRegulation"
                />
            </template>

            <!-- Tab: PROSEDUR -->
            <template v-if="activeSection?.id === 'prosedur'">
                <ProsedurEditor
                    ref="prosedurEditorRef"
                    :categories="categories"
                    :sop="sop"
                    :flowChartSops="flowChartSops"
                    :actors="actors"
                    :activeRegulation="activeRegulation"
                />
            </template>

            <!-- Tab: KELOLA SECTION -->
            <template v-if="activeSection?.id === 'manage_sections'">
                <SectionEditor
                    ref="sectionEditorRef"
                    :tkoSections="tkoSections"
                    :activeRegulation="activeRegulation"
                />
            </template>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted, nextTick, shallowRef } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import UserLayout from '@/Layouts/UserLayout.vue';
import FlowChart from '@/Components/Procedure/FlowChart.vue';
import FungsiEditor from '@/Components/Procedure/FungsiEditor.vue';
import ProsedurEditor from '@/Components/Procedure/ProsedurEditor.vue';
import SectionEditor from '@/Components/Procedure/SectionEditor.vue';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';
import ProcedureSectionNavigation from '@/Components/Regulation/ProcedureSectionNavigation.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
    flowChartSops: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tkoSections: {
        type: Array,
        default: () => [],
    },
});

const allSections = computed(() => {
    const list = [];
    const romanNumerals = {
        1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X'
    };

    // 1. TKO Sections before Fungsi (order < 4)
    const tkoBefore = (props.tkoSections || []).filter(s => s.order < 4);
    tkoBefore.forEach(s => {
        list.push({
            id: `tko_${s.id}`,
            section_id: s.id,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: 'tko',
            name: s.name,
            content: s.contents?.[0]?.content || ''
        });
    });

    // 2. Fungsi (order 4)
    list.push({
        id: 'fungsi',
        order: 4,
        label: 'IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT',
        labelShort: 'IV',
        type: 'fungsi'
    });

    // 3. Prosedur (order 5)
    list.push({
        id: 'prosedur',
        order: 5,
        label: 'V. PROSEDUR',
        labelShort: 'V',
        type: 'prosedur'
    });

    // 4. TKO Sections after Prosedur (order > 5)
    const tkoAfter = (props.tkoSections || []).filter(s => s.order > 5);
    tkoAfter.forEach(s => {
        list.push({
            id: `tko_${s.id}`,
            section_id: s.id,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: 'tko',
            name: s.name,
            content: s.contents?.[0]?.content || ''
        });
    });

    // 5. Manage Sections settings tab at the end
    list.push({
        id: 'manage_sections',
        order: 100,
        label: '⚙️ KELOLA SECTION',
        labelShort: '⚙️',
        type: 'manage_sections'
    });

    return list.sort((a, b) => a.order - b.order);
});

const activeTab = ref(allSections.value[0]?.id || 'fungsi');

const activeSection = computed(() => {
    return allSections.value.find(s => s.id === activeTab.value) || null;
});

const selectedRegulationId = ref(props.selectedRegulationId);
watch(() => props.selectedRegulationId, (newId) => {
    selectedRegulationId.value = newId;
});

const isManagePage = computed(() => true);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find((r) => r.id === selectedRegulationId.value) || props.regulations[0] || null;
});

// ---------------------------------------------------
// LOCAL EDITING STATES & CKEDITOR SETUP
// ---------------------------------------------------
const isEditorLoaded = ref(false);
const editorContainer = ref(null);
const editorInstance = shallowRef(null);
const hasUnsavedChanges = ref(false);

// Child component refs for tab-switch unsaved-change detection
const fungsiEditorRef = ref(null);
const prosedurEditorRef = ref(null);
const sectionEditorRef = ref(null);

// TKO Document save state (dedicated flags to avoid reactive lookup issues)
const isSavingTko = ref(false);
const saveTkoStatus = ref(null);

const saveStatuses = ref({});

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
    if (!activeSection.value) return '';
    return activeSection.value.content || '';
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

        // Set unsaved changes flag on data change
        editor.model.document.on('change:data', () => {
            hasUnsavedChanges.value = true;
        });
    })
    .catch(error => {
        console.error('Gagal menginisialisasi CKEditor:', error);
    });
}

async function saveTkoContent(sectionTabId) {
    let targetId = null;
    if (typeof sectionTabId === 'string' && sectionTabId.startsWith('tko_')) {
        targetId = sectionTabId;
    } else if (activeTab.value && activeTab.value.startsWith('tko_')) {
        targetId = activeTab.value;
    }

    if (!targetId) {
        console.warn('saveTkoContent: No valid target section ID found.');
        return;
    }

    const secDbId = Number(targetId.replace('tko_', ''));

    if (!editorInstance.value) {
        console.warn('Editor belum siap, tidak dapat menyimpan.');
        return;
    }

    if (!activeRegulation.value?.id) {
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
                regulation_id: activeRegulation.value.id,
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

const isRevertingTab = ref(false);
watch(activeTab, (newTab, oldTab) => {
    if (isRevertingTab.value) {
        isRevertingTab.value = false;
        return;
    }

    let hasUnsaved = false;
    let titleText = '';
    let saveFn = null;
    let clearFn = null;

    if (oldTab && oldTab.startsWith('tko_') && hasUnsavedChanges.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan dokumen TKO yang belum disimpan.';
        saveFn = () => saveTkoContent(oldTab);
        clearFn = () => { hasUnsavedChanges.value = false; saveTkoStatus.value = null; };
    } else if (oldTab === 'fungsi' && fungsiEditorRef.value?.hasUnsavedChanges.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data aktor yang belum disimpan.';
        saveFn = () => fungsiEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (oldTab === 'prosedur' && prosedurEditorRef.value?.hasUnsavedChanges.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data prosedur (kategori/SOP) yang belum disimpan.';
        saveFn = () => prosedurEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (oldTab === 'manage_sections' && sectionEditorRef.value?.hasUnsavedChanges.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan pengaturan section yang belum disimpan.';
        saveFn = () => sectionEditorRef.value.saveAll();
        clearFn = () => {};
    }

    if (hasUnsaved) {
        isRevertingTab.value = true;
        activeTab.value = oldTab;

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: `${titleText} Apakah Anda ingin menyimpannya sekarang?`,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#821f44',
            denyButtonColor: '#64748b',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan',
            denyButtonText: 'Jangan Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                saveFn().then(() => {
                    clearFn();
                    activeTab.value = newTab;
                });
            } else if (result.isDenied) {
                clearFn();
                activeTab.value = newTab;
            }
        });
        return;
    }

    if (newTab && newTab.startsWith('tko_')) {
        nextTick(() => {
            if (window.ClassicEditor) {
                initEditor();
            } else {
                loadCKEditor().then(() => {
                    initEditor();
                });
            }
        });
    }
});

watch(() => selectedRegulationId.value, () => {
    if (activeTab.value && activeTab.value.startsWith('tko_') && editorInstance.value) {
        editorInstance.value.setData(getActiveSectionHtml());
        hasUnsavedChanges.value = false;
    }
});

function handleBeforeUnload(e) {
    const childrenHaveUnsaved = 
        fungsiEditorRef.value?.hasUnsavedChanges.value ||
        prosedurEditorRef.value?.hasUnsavedChanges.value ||
        sectionEditorRef.value?.hasUnsavedChanges.value;
    if (hasUnsavedChanges.value || childrenHaveUnsaved) {
        e.preventDefault();
        e.returnValue = 'Ada perubahan yang belum disimpan.';
        return e.returnValue;
    }
}

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
    if (activeTab.value && activeTab.value.startsWith('tko_')) {
        loadCKEditor().then(() => {
            initEditor();
        });
    }
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    if (editorInstance.value) {
        editorInstance.value.destroy();
    }
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