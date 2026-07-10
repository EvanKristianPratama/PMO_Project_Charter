<template>
    <ModulLayout title="Kelola Procedure">
        <div class="animate-fade-in-up space-y-6">
            <!-- Unified Page Header -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden"
            >
                <div
                    class="flex flex-col gap-3 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <!-- Bagian Kiri: Judul Konten -->
                    <div class="flex-1 min-w-0">
                        <h2
                            class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2 flex-wrap"
                        >
                            <svg
                                class="h-4 w-4 text-[#821f44]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                                />
                            </svg>
                            Manage Procedure
                        </h2>
                    </div>

                    <!-- Bagian Kanan: Aksi & Pengaturan Tampilan -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- View Settings Controls (Header and Navigation Pane toggles) -->
                        <div
                            class="flex items-center gap-1.5 border-r border-slate-200 dark:border-white/10 pr-3 mr-1"
                        >
                            <button
                                @click="isHeaderVisible = !isHeaderVisible"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.25 border border-slate-200 dark:border-white/10 bg-transparent rounded-lg text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition active:scale-95 cursor-pointer"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full animate-pulse-slow"
                                    :class="
                                        isHeaderVisible
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300 dark:bg-zinc-700'
                                    "
                                ></span>
                                Header
                            </button>
                            <button
                                @click="isSidebarVisible = !isSidebarVisible"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.25 border border-slate-200 dark:border-white/10 bg-transparent rounded-lg text-[11px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition active:scale-95 cursor-pointer"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full animate-pulse-slow"
                                    :class="
                                        isSidebarVisible
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300 dark:bg-zinc-700'
                                    "
                                ></span>
                                Navigasi Pane
                            </button>
                        </div>

                        <!-- Action Button -->
                        <div class="flex shrink-0 gap-1">
                            <Link
                                :href="
                                    route(
                                        'itom.policy.regulation.procedure.index',
                                        activeRegulation
                                            ? {
                                                  regulation_id:
                                                      activeRegulation.id,
                                              }
                                            : {},
                                    )
                                "
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#9c2552]"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-3 w-3"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"
                                    />
                                </svg>
                                Kembali
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Word-style Navigation & Editor Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Sidebar: MS Word Style Navigation Pane -->
                <NavigationPane
                    v-if="isSidebarVisible"
                    :actors="actors"
                    :sop="sop"
                    :categories="categories"
                    :tko-sections="tkoSections"
                    :all-sections="allSections"
                    :regulations="regulations"
                    :active-regulation-id="activeRegulation?.id"
                    v-model:active-tab="activeTab"
                    v-model:active-sub-id="activeSubId"
                />

                <!-- Active Editor Content -->
                <main
                    :class="
                        isSidebarVisible
                            ? 'lg:col-span-8 xl:col-span-9'
                            : 'lg:col-span-12'
                    "
                    class="space-y-6"
                >
                    <!-- Tab: TKO Content -->
                    <template v-if="activeSection?.type === 'tko'">
                        <ManageSection
                            ref="manageSectionRef"
                            :activeSection="activeSection"
                            :activeRegulation="activeRegulation"
                            :isHeaderVisible="isHeaderVisible"
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
                            :isHeaderVisible="isHeaderVisible"
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
                            :activeCategoryId="activeCategoryId"
                            :isHeaderVisible="isHeaderVisible"
                            @select-category="
                                (id) =>
                                    (activeSubId = id ? `category_${id}` : null)
                            "
                        />
                    </template>

                    <!-- Tab: GLOSSARY -->
                    <template v-if="activeSection?.type === 'glossary'">
                        <ManageGlossary
                            :definitions="definitions"
                            :regulations="regulations"
                            :hide-regulation-filter="true"
                            :active-regulation-id="activeRegulation?.id"
                            :all-definitions="allDefinitions"
                        />
                    </template>

                    <!-- Tab: REFERENSI -->
                    <template v-if="activeSection?.type === 'reference'">
                        <ManageRefrence
                            :relatedRegulations="relatedRegulations"
                            :regulations="regulations"
                            :active-regulation-id="activeRegulation?.id"
                        />
                    </template>

                    <!-- Tab: KELOLA SECTION -->
                    <template v-if="activeSection?.id === 'manage_sections'">
                        <SectionEditor
                            ref="sectionEditorRef"
                            :tkoSections="tkoSections"
                            :activeRegulation="activeRegulation"
                            :isHeaderVisible="isHeaderVisible"
                        />
                    </template>
                </main>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";
import ModulLayout from "@/Layouts/ModulLayout.vue";
import NavigationPane from "@/Components/modules/ITOM/Regulation/Procedure/NavigationPane.vue";
import SectionEditor from "@/Components/modules/ITOM/Regulation/Procedure/SectionEditor.vue";
import ManageSection from "@/Components/modules/ITOM/Regulation/Procedure/ManageSection.vue";
import ManageActivity from "@/Components/modules/ITOM/Regulation/Procedure/ManageActivity.vue";
import ManageFunction from "@/Components/modules/ITOM/Regulation/Procedure/ManageFunction.vue";
import ManageGlossary from "@/Components/modules/ITOM/Regulation/Procedure/ManageGlossary.vue";
import ManageRefrence from "@/Components/modules/ITOM/Regulation/Procedure/ManageRefrence.vue";
import Swal from "sweetalert2";

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
    definitions: {
        type: Array,
        default: () => [],
    },
    allDefinitions: {
        type: Array,
        default: () => [],
    },
    relatedRegulations: {
        type: Array,
        default: () => [],
    },
});

const allSections = computed(() => {
    const list = [];
    const romanNumerals = {
        1: "I",
        2: "II",
        3: "III",
        4: "IV",
        5: "V",
        6: "VI",
        7: "VII",
        8: "VIII",
        9: "IX",
        10: "X",
    };

    // Filter out "Pengertian" section from tkoSections, but keep it for reference
    const pengertianSec = (props.tkoSections || []).find((s) => s.name.trim().toLowerCase() === "pengertian");

    // 1. TKO Sections before Fungsi (order < 4)
    const tkoBefore = (props.tkoSections || []).filter((s) => s.order < 4 && s.name.trim().toLowerCase() !== "pengertian");
    tkoBefore.forEach((s) => {
        const isReference = s.name.trim().toLowerCase() === "referensi";
        list.push({
            id: isReference ? "reference" : `tko_${s.id}`,
            section_id: s.id,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: isReference ? "reference" : "tko",
            name: s.name,
            content: s.contents?.[0]?.content || "",
        });
    });

    // 2. Fungsi (order 4)
    list.push({
        id: "fungsi",
        order: 4,
        label: "IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT",
        labelShort: "IV",
        type: "fungsi",
    });

    // 3. Prosedur (order 5)
    list.push({
        id: "prosedur",
        order: 5,
        label: "V. PROSEDUR",
        labelShort: "V",
        type: "prosedur",
    });

    // 4. TKO Sections after Prosedur (order > 5)
    const tkoAfter = (props.tkoSections || []).filter((s) => s.order > 5 && s.name.trim().toLowerCase() !== "pengertian");
    tkoAfter.forEach((s) => {
        const isReference = s.name.trim().toLowerCase() === "referensi";
        list.push({
            id: isReference ? "reference" : `tko_${s.id}`,
            section_id: s.id,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: isReference ? "reference" : "tko",
            name: s.name,
            content: s.contents?.[0]?.content || "",
        });
    });

    // 5. Glossary / Pengertian (virtual section)
    const glossaryOrder = pengertianSec ? pengertianSec.order : 9;
    list.push({
        id: "glossary",
        section_id: pengertianSec ? pengertianSec.id : null,
        order: glossaryOrder,
        label: pengertianSec
            ? `${romanNumerals[glossaryOrder] || glossaryOrder}. ${pengertianSec.name.trim().toUpperCase()}`
            : `${romanNumerals[glossaryOrder] || glossaryOrder}. PENGERTIAN`,
        labelShort: romanNumerals[glossaryOrder] || glossaryOrder,
        type: "glossary",
    });

    // 6. Manage Sections settings tab at the end
    list.push({
        id: "manage_sections",
        order: 100,
        label: "⚙️ KELOLA SECTION",
        labelShort: "⚙️",
        type: "manage_sections",
    });

    return list.sort((a, b) => a.order - b.order);
});

const activeTab = ref(allSections.value[0]?.id || "fungsi");

const activeSection = computed(() => {
    return allSections.value.find((s) => s.id === activeTab.value) || null;
});

const selectedRegulationId = ref(props.selectedRegulationId);
watch(
    () => props.selectedRegulationId,
    (newId) => {
        selectedRegulationId.value = newId;
    },
);

const isManagePage = computed(() => true);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return (
        props.regulations.find((r) => r.id === selectedRegulationId.value) ||
        props.regulations[0] ||
        null
    );
});

const activeCategoryId = computed(() => {
    if (!activeSubId.value) return null;
    const subStr = String(activeSubId.value);
    if (subStr.startsWith("category_")) {
        return Number(subStr.replace("category_", ""));
    }
    if (subStr.startsWith("sop_")) {
        const sopId = Number(subStr.replace("sop_", ""));
        const found = (props.sop || []).find((s) => s.id === sopId);
        return found ? Number(found.category_id) : null;
    }
    const num = Number(activeSubId.value);
    if (!isNaN(num)) {
        const foundSop = (props.sop || []).find((s) => s.id === num);
        if (foundSop) return Number(foundSop.category_id);
        const foundCat = (props.categories || []).find((c) => c.id === num);
        if (foundCat) return num;
    }
    return null;
});

// ---------------------------------------------------
// WORD-STYLE NAVIGATION PANE STATE & METHODS
// ---------------------------------------------------
const isHeaderVisible = ref(true);
const isSidebarVisible = ref(true);
const activeSubId = ref(null);

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
    let titleText = "";
    let saveFn = null;
    let clearFn = null;

    if (
        oldTab &&
        oldTab.startsWith("tko_") &&
        manageSectionRef.value?.hasUnsavedChanges?.value
    ) {
        hasUnsaved = true;
        titleText = "Ada perubahan dokumen TKO yang belum disimpan.";
        saveFn = () => manageSectionRef.value.saveAll();
        clearFn = () => {
            if (manageSectionRef.value) {
                manageSectionRef.value.hasUnsavedChanges.value = false;
            }
        };
    } else if (
        oldTab === "fungsi" &&
        fungsiEditorRef.value?.hasUnsavedChanges?.value
    ) {
        hasUnsaved = true;
        titleText = "Ada perubahan data aktor yang belum disimpan.";
        saveFn = () => fungsiEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (
        oldTab === "prosedur" &&
        prosedurEditorRef.value?.hasUnsavedChanges?.value
    ) {
        hasUnsaved = true;
        titleText =
            "Ada perubahan data prosedur (kategori/SOP) yang belum disimpan.";
        saveFn = () => prosedurEditorRef.value.saveAll();
        clearFn = () => {};
    } else if (
        oldTab === "manage_sections" &&
        sectionEditorRef.value?.hasUnsavedChanges?.value
    ) {
        hasUnsaved = true;
        titleText = "Ada perubahan pengaturan section yang belum disimpan.";
        saveFn = () => sectionEditorRef.value.saveAll();
        clearFn = () => {};
    }

    if (hasUnsaved) {
        isRevertingTab.value = true;
        activeTab.value = oldTab;

        Swal.fire({
            title: "Simpan Perubahan?",
            text: `${titleText} Apakah Anda ingin menyimpannya sekarang?`,
            icon: "question",
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: "#821f44",
            denyButtonColor: "#64748b",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Simpan",
            denyButtonText: "Jangan Simpan",
            cancelButtonText: "Batal",
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
        e.returnValue = "Ada perubahan yang belum disimpan.";
        return e.returnValue;
    }
}

onMounted(() => {
    window.addEventListener("beforeunload", handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener("beforeunload", handleBeforeUnload);
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
