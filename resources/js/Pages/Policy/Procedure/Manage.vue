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
                <ManageSection
                    ref="manageSectionRef"
                    :activeSection="activeSection"
                    :activeRegulation="activeRegulation"
                />
            </template>

            <!-- Tab: FUNGSI -->
            <template v-if="activeSection?.id === 'fungsi'">
                <ManageFunction
                    ref="fungsiEditorRef"
                    :actors="actors"
                    :organizations="organizations"
                    :activeRegulation="activeRegulation"
                    :functions="functions"
                />
            </template>

            <!-- Tab: PROSEDUR -->
            <template v-if="activeSection?.id === 'prosedur'">
                <ManageActivity
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
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import SectionEditor from '@/Components/Procedure/SectionEditor.vue';
import ProcedureSectionNavigation from '@/Components/Regulation/ProcedureSectionNavigation.vue';
import ManageSection from '@/Components/ITOperatingModel/Regulation/Procedure/ManageSection.vue';
import ManageActivity from '@/Components/ITOperatingModel/Regulation/Procedure/ManageActivity.vue';
import ManageFunction from '@/Components/ITOperatingModel/Regulation/Procedure/ManageFunction.vue';
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
    functions: {
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
// LOCAL EDITING STATES
// ---------------------------------------------------
// Child component refs for tab-switch unsaved-change detection
const manageSectionRef = ref(null);
const fungsiEditorRef = ref(null);
const prosedurEditorRef = ref(null);
const sectionEditorRef = ref(null);

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

    if (oldTab && oldTab.startsWith('tko_') && manageSectionRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan dokumen TKO yang belum disimpan.';
        saveFn = () => manageSectionRef.value.saveAll();
        clearFn = () => { if (manageSectionRef.value) { manageSectionRef.value.hasUnsavedChanges.value = false; } };
    } else if (oldTab === 'fungsi' && fungsiEditorRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data aktor yang belum disimpan.';
        saveFn = () => fungsiEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (oldTab === 'prosedur' && prosedurEditorRef.value?.hasUnsavedChanges?.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data prosedur (kategori/SOP) yang belum disimpan.';
        saveFn = () => prosedurEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (oldTab === 'manage_sections' && sectionEditorRef.value?.hasUnsavedChanges?.value) {
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
    }
});

function handleBeforeUnload(e) {
    const childrenHaveUnsaved = 
        manageSectionRef.value?.hasUnsavedChanges?.value ||
        fungsiEditorRef.value?.hasUnsavedChanges?.value ||
        prosedurEditorRef.value?.hasUnsavedChanges?.value ||
        sectionEditorRef.value?.hasUnsavedChanges?.value;
    if (childrenHaveUnsaved) {
        e.preventDefault();
        e.returnValue = 'Ada perubahan yang belum disimpan.';
        return e.returnValue;
    }
}

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
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
</style>