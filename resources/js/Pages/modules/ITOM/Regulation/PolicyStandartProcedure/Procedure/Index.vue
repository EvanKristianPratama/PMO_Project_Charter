<template>
    <ModulLayout title="Procedure">
        <div class="animate-fade-in-up space-y-6 print:m-0 print:p-0">
            <!-- Navigation Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
                <!-- Back Button -->
                <Link
                    :href="route('itom.policy.regulation.index')"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#171717] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </Link>

                <!-- Fast Document Switcher & View controls -->
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        @click="isSidebarVisible = !isSidebarVisible"
                        class="inline-flex items-center gap-1.5 px-3 py-2.5 border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171717] rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 shadow-sm transition active:scale-95 cursor-pointer"
                    >
                        <span
                            class="w-1.5 h-1.5 rounded-full"
                            :class="
                                isSidebarVisible
                                    ? 'bg-emerald-500'
                                    : 'bg-slate-300 dark:bg-zinc-700'
                            "
                        ></span>
                        Navigasi Pane
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilih Dokumen:</span>
                        <div class="relative">
                            <select
                                :value="selectedRegulationId"
                                @change="handleFastDocumentSwitch($event.target.value)"
                                class="appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-8 py-2.5 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[240px] max-w-[320px] truncate"
                            >
                                <option
                                    v-for="reg in regulations"
                                    :key="reg.id"
                                    :value="reg.id"
                                >
                                    [{{ reg.tipe }}] {{ reg.judul }}
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Word-style Navigation & Document Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Sidebar: MS Word Style Navigation Pane -->
                <NavigationPane
                    v-if="isSidebarVisible"
                    :actors="actors"
                    :sop="sop"
                    :categories="categories"
                    :tko-sections="tkoSections"
                    :all-sections="allSections"
                    v-model:active-tab="activeTab"
                    v-model:active-sub-id="activeSubId"
                />

                <!-- Main Document View -->
                <main
                    :class="
                        isSidebarVisible
                            ? 'lg:col-span-8 xl:col-span-9'
                            : 'lg:col-span-12'
                    "
                    class="space-y-6 w-full print:lg:col-span-12"
                >
                    <!-- A4 Document Page Preview (Pure Word Style Document, NO watermark) -->
                    <div
                        class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0"
                    >
                        <!-- Formal Pertamina Document Grid Header -->
                        <PertaminaDocumentHeader :activeRegulation="activeRegulation" class="flex-1" />

                        <!-- Literal Document Content in Serif style for premium Word document look -->
                        <div class="mt-8 space-y-12 text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 font-serif">
                            <!-- Loop through all Sections in order -->
                            <div v-for="section in allSections" :key="section.id" :id="'section-' + section.id" class="space-y-4 scroll-mt-24">
                                
                                <!-- 1. Render TKO Content -->
                                <template v-if="section.type === 'tko'">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                        {{ section.label }}
                                    </h3>
                                    <div class="space-y-2.5">
                                        <template v-if="section.content">
                                            <div v-if="isHtmlContent(section.content)" v-html="section.content" class="prose dark:prose-invert max-w-none text-justify whitespace-normal text-slate-800 dark:text-slate-200"></div>
                                            <template v-else>
                                                <div 
                                                    v-for="(line, idx) in parseContentLines(section.content)" 
                                                    :key="idx"
                                                    :class="[
                                                        line.type === 'list' ? 'flex gap-2 items-start' : '',
                                                    ]"
                                                    :style="line.indent > 0 ? { paddingLeft: `${line.indent * 2.25}rem` } : {}"
                                                >
                                                    <template v-if="line.type === 'list'">
                                                        <span class="font-bold select-none text-right min-w-[1.75rem] text-slate-950 dark:text-white font-sans">{{ line.marker }}</span>
                                                        <span class="flex-1 text-justify whitespace-pre-wrap">{{ line.text }}</span>
                                                    </template>
                                                    <template v-else-if="line.type === 'empty'">
                                                        <div class="h-3"></div>
                                                    </template>
                                                    <template v-else>
                                                        <span class="flex-1 text-justify whitespace-pre-wrap">{{ line.text }}</span>
                                                    </template>
                                                </div>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <div class="text-slate-400 dark:text-slate-500 italic">Belum ada konten.</div>
                                        </template>
                                    </div>
                                </template>

                                <!-- 1b. Render Glossary (DefinitionTable) -->
                                <template v-else-if="section.type === 'glossary'">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                        {{ section.label }}
                                    </h3>
                                    <div class="mt-4 font-sans text-xs">
                                        <ManageGlossary
                                            :definitions="definitions"
                                            :regulations="regulations"
                                            :hide-regulation-filter="true"
                                            :readonly="true"
                                            :active-regulation-id="activeRegulation?.id"
                                        />
                                    </div>
                                </template>

                                <!-- 1c. Render Reference Documents (ManageRefrence) -->
                                <template v-else-if="section.type === 'reference'">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                        {{ section.label }}
                                    </h3>
                                    <div class="mt-4 font-sans text-xs">
                                        <ManageRefrence
                                            :related-regulations="relatedRegulations"
                                            :regulations="regulations"
                                            :readonly="true"
                                            :active-regulation-id="activeRegulation?.id"
                                        />
                                    </div>
                                </template>

                                <!-- 2. Render Fungsi (Actors) -->
                                <template v-else-if="section.id === 'fungsi'">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                        {{ section.label }}
                                    </h3>
                                    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10 mt-4 font-sans text-xs">
                                        <table class="w-full border-collapse text-left text-[11px]">
                                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                                <tr class="divide-x divide-slate-200 dark:divide-white/10">
                                                    <th class="px-1 py-3 w-10 text-center border-b border-slate-200 dark:border-white/10">No</th>
                                                    <th class="px-1 py-3 border-b border-slate-200 dark:border-white/10">Fungsi / Unit Organisasi / Jabatan</th>
                                                    <th class="px-1 py-3 border-b border-slate-200 dark:border-white/10">Mapping Master</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                                <tr v-if="actors.length === 0">
                                                    <td colspan="3" class="px-1 py-8 text-center text-slate-400">Belum ada data aktor terkait.</td>
                                                </tr>
                                                <tr v-for="(actor, index) in actors" :key="actor.id" :id="'actor-row-' + actor.id" class="divide-x divide-slate-200 dark:divide-white/10 scroll-mt-24">
                                                    <td class="px-1 py-3 text-center font-medium">{{ index + 1 }}</td>
                                                    <td class="px-1 py-3 font-semibold text-slate-900 dark:text-white">
                                                        <div class="flex items-center gap-2">
                                                            <span class="line-clamp-2 whitespace-normal break-words">{{ actor.name }}</span>
                                                            <span 
                                                                v-if="actor.tipe" 
                                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider capitalize border"
                                                                :class="{
                                                                    'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-800/30': actor.tipe === 'fungsi',
                                                                    'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800/30': actor.tipe === 'organisasi',
                                                                    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800/30': actor.tipe === 'jabatan'
                                                                }"
                                                            >
                                                                {{ actor.tipe }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <!-- Kolom Mapping Master -->
                                                    <td class="px-1 py-3 text-slate-700 dark:text-slate-300 font-sans">
                                                        <template v-if="actor.tipe === 'jabatan'">
                                                            <span v-if="actor.organization && actor.organization.jabatan" class="line-clamp-2 whitespace-normal break-words">
                                                                {{ actor.organization.jabatan }} ({{ actor.organization.name || '-' }} - {{ actor.organization.code || '-' }})
                                                            </span>
                                                            <span v-else>-</span>
                                                        </template>
                                                        <template v-else-if="actor.tipe === 'fungsi'">
                                                            <div v-if="actor.functions && actor.functions.length > 0" class="space-y-1">
                                                                <div v-for="f in actor.functions" :key="f.id" class="line-clamp-2 whitespace-normal break-words">
                                                                    {{ f.name }}
                                                                </div>
                                                            </div>
                                                            <span v-else>-</span>
                                                        </template>
                                                        <template v-else-if="actor.tipe === 'organisasi'">
                                                            <div v-if="actor.organizations && actor.organizations.length > 0" class="space-y-1">
                                                                <div v-for="o in actor.organizations" :key="o.id" class="line-clamp-2 whitespace-normal break-words">
                                                                    {{ o.jabatan }}{{ o.name || o.code ? ` (${o.name || '-'}${o.code ? ` - ${o.code}` : ''})` : '' }}
                                                                </div>
                                                            </div>
                                                            <span v-else>-</span>
                                                        </template>
                                                        <span v-else>-</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>

                                <!-- 3. Render Prosedur -->
                                <template v-else-if="section.id === 'prosedur'">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                        {{ section.label }}
                                    </h3>
                                    <div class="mt-4 space-y-8 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 text-justify">
                                        <div v-if="categories.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">Belum ada data SOP untuk regulasi ini.</div>
                                        <div v-for="cat in categories" :key="cat.id" :id="'sop-cat-' + cat.id" class="space-y-3 scroll-mt-24">
                                            <h4 :id="'category-row-' + cat.id" class="font-bold text-slate-950 dark:text-white font-sans text-sm scroll-mt-24">{{ cat.tipe }}</h4>
                                            <div class="space-y-3">
                                                <div v-for="(item, index) in getSopsForCategory(cat.id)" :key="item.id" :id="'sop-row-' + item.id" class="flex gap-3 items-start pl-2 scroll-mt-24">
                                                    <span class="font-bold min-w-[20px] text-right select-none font-sans text-sm">{{ index + 1 }}.</span>
                                                    <p class="whitespace-pre-line text-justify m-0 p-0">{{ item.description }}</p>
                                                </div>
                                                <div v-if="getSopsForCategory(cat.id).length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">Belum ada data SOP untuk kategori ini.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Diagram Alir included under Prosedur section -->
                                    <div class="mt-12 pt-10 border-t border-slate-200 dark:border-white/10 print:break-inside-avoid">
                                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                            VI. DIAGRAM ALIR
                                        </h3>
                                        <div class="mt-6 space-y-6">
                                            <div v-if="categories.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">Belum ada data diagram untuk regulasi ini.</div>
                                            <div v-for="cat in categories" :key="cat.id" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                                                <div class="border-b border-slate-200 px-5 py-3 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200 font-sans">{{ cat.tipe }}</h3>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <div class="min-w-[1200px] p-4">
                                                        <FlowChart :actors="actors" :sops="flowChartSops" readonly :flow-type="String(cat.id)" :categories="categories" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Floating Action Buttons (Fixed Bottom Right) -->
        <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-4 print:hidden">
            <!-- Scroll to Top Button -->
            <button @click="scrollToTop" title="Kembali ke Atas"
                class="group flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white/40 shadow-2xl backdrop-blur-md transition-all hover:bg-white hover:text-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-[#1a1a1a] active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                </svg>
            </button>

            <!-- Print Button -->
            <button @click="printDocument" title="Cetak PDF"
                class="group flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white/40 shadow-2xl backdrop-blur-md transition-all hover:bg-white hover:text-[#821f44] dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-[#1a1a1a] active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
                </svg>
            </button>

            <!-- Go to Management CRUD page -->
            <Link :href="route('itom.policy.regulation.procedure.manage', activeRegulation ? { regulation_id: activeRegulation.id } : {})" title="Editor"
                class="group flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44]/80 text-white shadow-2xl shadow-[#821f44]/30 backdrop-blur-md transition-all hover:bg-[#821f44] hover:shadow-[#821f44]/40 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                    stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </Link>
        </div>
    </ModulLayout>
</template>

<script setup>
import { computed, ref, watch, onMounted, nextTick } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import NavigationPane from '@/Components/modules/ITOM/Regulation/Procedure/NavigationPane.vue';
import FlowChart from '@/Components/modules/ITOM/Regulation/Procedure/FlowChart.vue';
import PertaminaDocumentHeader from '@/Components/modules/ITOM/Regulation/PertaminaDocumentHeader.vue';
import ManageGlossary from '@/Components/modules/ITOM/Regulation/Procedure/ManageGlossary.vue';
import ManageRefrence from '@/Components/modules/ITOM/Regulation/Procedure/ManageRefrence.vue';

const props = defineProps({
    actors: { type: Array, default: () => [] },
    sop: { type: Array, default: () => [] },
    flowChartSops: { type: Array, default: () => [] },
    regulations: { type: Array, default: () => [] },
    organizations: { type: Array, default: () => [] },
    selectedRegulationId: { type: Number, default: null },
    categories: { type: Array, default: () => [] },
    tkoSections: { type: Array, default: () => [] },
    definitions: { type: Array, default: () => [] },
    relatedRegulations: { type: Array, default: () => [] },
});

const allSections = computed(() => {
    const list = [];
    const romanNumerals = {
        1: 'I',
        2: 'II',
        3: 'III',
        4: 'IV',
        5: 'V',
        6: 'VI',
        7: 'VII',
        8: 'VIII',
        9: 'IX',
        10: 'X'
    };

    // Filter out "Pengertian" section from tkoSections, but keep it for reference
    const pengertianSec = (props.tkoSections || []).find(s => s.name.trim().toLowerCase() === 'pengertian');

    // 1. TKO Sections before Fungsi
    const tkoBefore = (props.tkoSections || []).filter(s => s.order < 4 && s.name.trim().toLowerCase() !== 'pengertian');
    tkoBefore.forEach(s => {
        const isReference = s.name.trim().toLowerCase() === 'referensi';
        list.push({
            id: isReference ? 'reference' : `tko_${s.id}`,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: isReference ? 'reference' : 'tko',
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

    // 4. TKO Sections after Prosedur
    const tkoAfter = (props.tkoSections || []).filter(s => s.order > 5 && s.name.trim().toLowerCase() !== 'pengertian');
    tkoAfter.forEach(s => {
        const isReference = s.name.trim().toLowerCase() === 'referensi';
        list.push({
            id: isReference ? 'reference' : `tko_${s.id}`,
            order: s.order,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            labelShort: romanNumerals[s.order] || s.order,
            type: isReference ? 'reference' : 'tko',
            name: s.name,
            content: s.contents?.[0]?.content || ''
        });
    });

    // 5. Glossary / Pengertian (virtual section)
    const glossaryOrder = pengertianSec ? pengertianSec.order : 9;
    list.push({
        id: 'glossary',
        order: glossaryOrder,
        label: pengertianSec
            ? `${romanNumerals[glossaryOrder] || glossaryOrder}. ${pengertianSec.name.trim().toUpperCase()}`
            : `${romanNumerals[glossaryOrder] || glossaryOrder}. PENGERTIAN`,
        labelShort: romanNumerals[glossaryOrder] || glossaryOrder,
        type: 'glossary',
    });

    return list.sort((a, b) => a.order - b.order);
});

const selectedRegulationId = ref(props.selectedRegulationId);
watch(() => props.selectedRegulationId, (newId) => {
    selectedRegulationId.value = newId;
});

function handleFastDocumentSwitch(regId) {
    const selectedReg = props.regulations.find(r => r.id === Number(regId));
    if (!selectedReg) return;

    const targetRoute = String(selectedReg.tipe || '').toLowerCase() === 'procedure'
        ? 'itom.policy.regulation.procedure.index'
        : 'itom.policy.general.index';

    router.visit(route(targetRoute, { regulation_id: regId }));
}

function getSopsForCategory(categoryId) {
    if (!props.sop) return [];
    return props.sop.filter(s => Number(s.category_id) === Number(categoryId));
}

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || !props.regulations.length) return props.regulations[0] || null;
    return props.regulations.find(r => r.id === selectedRegulationId.value) || props.regulations[0] || null;
});

const isSidebarVisible = ref(true);
const activeTab = ref(allSections.value[0]?.id || 'fungsi');
const activeSubId = ref(null);

watch(activeTab, (newTab) => {
    nextTick(() => {
        if (!activeSubId.value) {
            scrollToSection(newTab);
        }
    });
});

function scrollToSection(sectionId) {
    nextTick(() => {
        const el = document.getElementById(`section-${sectionId}`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            el.classList.add("bg-blue-500/10", "dark:bg-blue-500/20");
            setTimeout(() => {
                el.classList.remove("bg-blue-500/10", "dark:bg-blue-500/20");
            }, 3000);
        }
    });
}

function parseContentLines(content) {
    if (!content) return [];
    const lines = String(content).replace(/\r\n/g, '\n').split('\n');
    const parsed = [];
    
    // Pattern to capture leading spaces, marker, separator, and text content
    const listPattern = /^(\s*)([0-9]+|[a-zA-Z]|[ivxIVX]+)([\.\)\-])\s+(.*)$/;
    const bulletPattern = /^(\s*)([\-\•\*\+])\s+(.*)$/;
    
    let currentListIndent = 0;
    
    lines.forEach((line) => {
        if (!line.trim()) {
            parsed.push({ type: 'empty', text: '', indent: 0 });
            currentListIndent = 0; // reset indentation level on blank lines
            return;
        }
        
        // 1. Check list pattern (e.g. 1. or a. or I.)
        const listMatch = line.match(listPattern);
        if (listMatch) {
            const [_, spaces, num, separator, text] = listMatch;
            let listIndent = Math.floor(spaces.length / 2);
            
            // Auto-indent alphabetical/roman sub-lists if in a list context
            const isSubList = /^[a-zA-Z]$/.test(num) || /^[ivxIVX]+$/.test(num);
            if (listIndent === 0 && currentListIndent > 0 && isSubList) {
                listIndent = currentListIndent;
            }
            
            parsed.push({
                type: 'list',
                marker: `${num}${separator}`,
                text: text,
                indent: listIndent
            });
            
            currentListIndent = listIndent + 1;
            return;
        }
        
        // 2. Check bullet pattern (e.g. - or * or •)
        const bulletMatch = line.match(bulletPattern);
        if (bulletMatch) {
            const [_, spaces, bullet, text] = bulletMatch;
            let bulletIndent = Math.floor(spaces.length / 2);
            
            if (bulletIndent === 0 && currentListIndent > 0) {
                bulletIndent = currentListIndent;
            }
            
            parsed.push({
                type: 'list',
                marker: bullet,
                text: text,
                indent: bulletIndent
            });
            
            currentListIndent = bulletIndent + 1;
            return;
        }
        
        // 3. Check for manually indented continuation lines
        const spaceMatch = line.match(/^(\s+)(.*)$/);
        if (spaceMatch) {
            const [_, spaces, text] = spaceMatch;
            const indentLevel = Math.min(Math.floor(spaces.length / 2), 6);
            parsed.push({
                type: 'text',
                text: text,
                indent: indentLevel > 0 ? indentLevel : 1
            });
            currentListIndent = indentLevel;
            return;
        }
        
        // 4. Default plain text line inherits active list context indentation
        parsed.push({
            type: 'text',
            text: line,
            indent: currentListIndent
        });
    });
    return parsed;
}

function printDocument() {
    setTimeout(() => {
        window.print();
    }, 100);
}

function isHtmlContent(content) {
    if (!content) return false;
    const trimmed = String(content).trim();
    return /<[a-z][\s\S]*>/i.test(trimmed);
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

onMounted(() => {
    scrollToHash();
});

function scrollToHash() {
    nextTick(() => {
        const hash = window.location.hash;
        if (hash) {
            const element = document.querySelector(hash);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
}
</script>

<style scoped>
.animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

/* Custom nested list styles for v-html content area */
:deep(.prose ol) {
    list-style-type: none !important;
    counter-reset: level1-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.prose ol > li) {
    counter-increment: level1-counter !important;
    position: relative !important;
}
:deep(.prose ol > li::before) {
    content: counter(level1-counter) ". " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
    font-weight: inherit !important;
}

:deep(.prose ol ol) {
    counter-reset: level2-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.prose ol ol > li) {
    counter-increment: level2-counter !important;
}
:deep(.prose ol ol > li::before) {
    content: counter(level2-counter, lower-alpha) ". " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
}

:deep(.prose ol ol ol) {
    counter-reset: level3-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.prose ol ol ol > li) {
    counter-increment: level3-counter !important;
}
:deep(.prose ol ol ol > li::before) {
    content: counter(level3-counter) ") " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
}

:deep(.prose ol ol ol ol) {
    counter-reset: level4-counter !important;
    padding-left: 1.75rem !important;
}
:deep(.prose ol ol ol ol > li) {
    counter-increment: level4-counter !important;
}
:deep(.prose ol ol ol ol > li::before) {
    content: counter(level4-counter, lower-alpha) ") " !important;
    position: absolute !important;
    left: -1.5rem !important;
    width: 1.25rem !important;
    text-align: right !important;
}
</style>