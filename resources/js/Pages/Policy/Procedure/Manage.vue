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

                    <div class="mt-10 flex items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide uppercase">
                            {{ activeSection.label }}
                        </h3>
                        <!-- Auto-save status indicator -->
                        <span class="text-[11px] flex items-center gap-1.5 select-none print:hidden">
                            <span v-if="saveStatuses[activeSection.id] === 'saving'" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan otomatis...
                            </span>
                            <span v-else-if="saveStatuses[activeSection.id] === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Tersimpan
                            </span>
                            <span v-else-if="saveStatuses[activeSection.id] === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                                Gagal menyimpan
                            </span>
                            <span v-else class="text-slate-400 dark:text-slate-500">
                                Auto-save aktif
                            </span>
                        </span>
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
                <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
                    <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

                    <div class="mt-10 flex items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                            IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                        </h3>
                        <div class="flex items-center gap-3">
                            <!-- Auto-save status indicator -->
                            <span class="text-[11px] flex items-center gap-1.5 select-none print:hidden mr-2">
                                <span v-if="hasSavingActor" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan otomatis...
                                </span>
                                <span v-else-if="hasSavedActor" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span v-else-if="hasErrorActor" class="text-rose-600 dark:text-rose-400 font-bold">
                                    Gagal menyimpan
                                </span>
                                <span v-else class="text-slate-400 dark:text-slate-500">
                                    Auto-save aktif
                                </span>
                            </span>

                            <button
                                @click="addActorDirectly"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 print:hidden"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Aktor
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
                        <table class="w-full border-collapse text-left text-[11px]">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-3 w-20 text-center">No</th>
                                    <th class="px-6 py-3">Fungsi / Unit Organisasi / Jabatan</th>
                                    <th class="px-6 py-3">Jabatan</th>
                                    <th class="px-6 py-3 w-24 text-center print:hidden">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                <tr v-if="actors.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada data aktor terkait.</td>
                                </tr>
                                <tr v-for="(actor, index) in actors" :key="actor.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5">
                                    <td class="px-6 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                                    <td class="px-6 py-2 align-middle">
                                        <input
                                            v-if="actorLocal[actor.id]"
                                            v-model="actorLocal[actor.id].name"
                                            @input="handleActorInput(actor)"
                                            @blur="saveActor(actor)"
                                            type="text"
                                            class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10"
                                            placeholder="Nama Aktor / Jabatan"
                                        />
                                    </td>
                                    <td class="px-6 py-2 align-middle">
                                        <select
                                            v-if="actorLocal[actor.id]"
                                            v-model="actorLocal[actor.id].organization_id"
                                            @change="saveActor(actor)"
                                            class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10"
                                        >
                                            <option value="" disabled>-- Pilih Organisasi --</option>
                                            <option v-for="org in filteredOrganizations" :key="org.id" :value="org.id">
                                                {{ org.jabatan }} ({{ org.name }})
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-2 align-middle text-center print:hidden">
                                        <button
                                            @click="deleteActorDirectly(actor)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <!-- Tab: PROSEDUR -->
            <template v-if="activeSection?.id === 'prosedur'">
                <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
                    <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

                    <div class="mt-10 flex items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                            V. PROSEDUR
                        </h3>
                        <div class="flex items-center gap-3">
                            <!-- Auto-save status indicator -->
                            <span class="text-[11px] flex items-center gap-1.5 select-none print:hidden mr-2">
                                <span v-if="hasSavingProsedur" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan otomatis...
                                </span>
                                <span v-else-if="hasSavedProsedur" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span v-else-if="hasErrorProsedur" class="text-rose-600 dark:text-rose-400 font-bold">
                                    Gagal menyimpan
                                </span>
                                <span v-else class="text-slate-400 dark:text-slate-500">
                                    Auto-save aktif
                                </span>
                            </span>

                            <button
                                @click="addCategoryDirectly"
                                class="inline-flex items-center gap-2 rounded-xl border border-[#821f44] bg-transparent px-3 py-1.5 text-xs font-bold text-[#821f44] hover:bg-[#821f44]/5 transition-all focus:ring-2 focus:ring-[#821f44]/20 active:scale-95 dark:border-[#a83262] dark:text-[#a83262] print:hidden"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Kategori
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 space-y-8 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100">
                        <div v-if="categories.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">Belum ada data SOP untuk regulasi ini.</div>
                        <div v-for="cat in categories" :key="cat.id" class="space-y-4">
                            <!-- Category Title (Editable) -->
                            <div class="flex items-center justify-between gap-4 group/cat">
                                <div class="flex-1">
                                    <input
                                        v-if="categoryLocal[cat.id]"
                                        v-model="categoryLocal[cat.id].tipe"
                                        @input="handleCategoryInput(cat)"
                                        @blur="saveCategory(cat)"
                                        class="font-bold text-slate-950 dark:text-white bg-transparent border border-transparent rounded hover:border-slate-300 dark:hover:border-white/10 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 focus:bg-white dark:focus:bg-[#1e1e1e] text-sm md:text-base w-full px-2 py-1"
                                        placeholder="Nama Kategori (Contoh: A. Penyusunan RSTI)"
                                    />
                                </div>
                                <button
                                    @click="deleteCategoryDirectly(cat)"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition-colors ml-4 opacity-0 group-hover/cat:opacity-100 focus:opacity-100 print:hidden animate-fade-in-up"
                                >
                                    Hapus Kategori
                                </button>
                            </div>

                            <!-- SOP List -->
                            <div class="space-y-3">
                                <div v-for="(item, index) in getSopsForCategory(cat.id)" :key="item.id" class="flex gap-3 items-start group/sop">
                                    <span class="font-bold min-w-[20px] text-right select-none pt-2 text-[15px] text-slate-950 dark:text-white">{{ index + 1 }}.</span>
                                    <div class="flex-1">
                                        <textarea
                                            v-if="sopLocal[item.id]"
                                            v-model="sopLocal[item.id].description"
                                            @input="handleSopInput(item)"
                                            @blur="saveSop(item)"
                                            rows="2"
                                            class="w-full bg-transparent px-3 py-2 text-justify font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 border border-transparent rounded hover:border-slate-300 dark:hover:border-white/10 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 focus:bg-white dark:focus:bg-[#1e1e1e]"
                                            placeholder="Tulis deskripsi aktivitas SOP..."
                                        ></textarea>
                                    </div>
                                    <button
                                        @click="deleteSopDirectly(item)"
                                        class="text-xs text-rose-600 hover:text-rose-800 transition-colors pt-2 opacity-0 group-hover/sop:opacity-100 focus:opacity-100 print:hidden"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                <div v-if="getSopsForCategory(cat.id).length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">Belum ada data SOP untuk kategori ini.</div>
                            </div>

                            <!-- Add SOP under this category -->
                            <div class="pl-8">
                                <button
                                    @click="addSopDirectly(cat.id)"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors mt-1 print:hidden"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah SOP
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Diagram Alir (Flowchart) Loop by Category exactly like Index.vue but editable -->
                    <div class="mt-12 pt-10 border-t border-slate-200 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">VI. DIAGRAM ALIR</h3>
                        <div class="mt-6 space-y-6">
                            <div v-if="categories.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400">Belum ada data diagram untuk regulasi ini.</div>
                            <div v-for="cat in categories" :key="cat.id" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                                <div class="border-b border-slate-200 px-5 py-3 dark:border-white/10">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">{{ cat.tipe }}</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <div class="min-w-[1200px] p-4">
                                        <!-- Pass locked category array and flow-type to match Index.vue but keep editable -->
                                        <FlowChart :actors="actors" :sops="flowChartSops" :flow-type="String(cat.id)" :categories="[cat]" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Tab: PENGATURAN SECTION DOKUMEN -->
            <template v-if="activeSection?.id === 'manage_sections'">
                <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
                    <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

                    <div class="mt-10 flex items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                            PENGATURAN SECTION DOKUMEN
                        </h3>
                        <div class="flex items-center gap-3">
                            <!-- Auto-save status indicator -->
                            <span class="text-[11px] flex items-center gap-1.5 select-none print:hidden mr-2">
                                <span v-if="hasSavingSection" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan otomatis...
                                </span>
                                <span v-else-if="hasSavedSection" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span v-else-if="hasErrorSection" class="text-rose-600 dark:text-rose-400 font-bold">
                                    Gagal menyimpan
                                </span>
                                <span v-else class="text-slate-400 dark:text-slate-500">
                                    Auto-save aktif
                                </span>
                            </span>

                            <button
                                @click="addSectionDirectly"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 print:hidden"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Section
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
                        <table class="w-full border-collapse text-left text-[11px]">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th class="px-6 py-3 w-20 text-center">No</th>
                                    <th class="px-6 py-3">Nama Section</th>
                                    <th class="px-6 py-3 w-28 text-center">Urutan</th>
                                    <th class="px-6 py-3 w-24 text-center print:hidden">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                <tr v-if="tkoSections.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada section.</td>
                                </tr>
                                <tr v-for="(sec, index) in tkoSections" :key="sec.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5">
                                    <td class="px-6 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                                    <td class="px-6 py-2 align-middle">
                                        <input
                                            v-if="sectionLocal[sec.id]"
                                            v-model="sectionLocal[sec.id].name"
                                            @input="handleSectionInput(sec)"
                                            @blur="saveSection(sec)"
                                            type="text"
                                            class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10 font-bold"
                                            placeholder="Nama Section (Contoh: TUJUAN)"
                                        />
                                    </td>
                                    <td class="px-6 py-2 align-middle text-center">
                                        <input
                                            v-if="sectionLocal[sec.id]"
                                            v-model="sectionLocal[sec.id].order"
                                            @input="handleSectionInput(sec)"
                                            @blur="saveSection(sec)"
                                            type="number"
                                            min="1"
                                            class="w-20 bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10 text-center"
                                        />
                                    </td>
                                    <td class="px-6 py-2 align-middle text-center print:hidden">
                                        <button
                                            @click="deleteSectionDirectly(sec)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-white/5 p-4 rounded-xl">
                        <p class="font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Pengaturan Section:</p>
                        <ul class="list-disc pl-4 space-y-1">
                            <li>Secara default, bab **IV (Fungsi Terkait)** diisi otomatis oleh data Aktor di Urutan **4**.</li>
                            <li>Bab **V (Prosedur)** diisi otomatis oleh data SOP & Diagram di Urutan **5**.</li>
                            <li>Anda bebas mengubah nama dan urutan section lainnya (seperti Tujuan, Ruang Lingkup, Lampiran, dll).</li>
                            <li>Perubahan nama section atau urutan section akan secara otomatis memperbarui tata letak halaman dan menu navigasi.</li>
                        </ul>
                    </div>
                </div>
            </template>
        </div>
    </UserLayout>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import UserLayout from '@/Layouts/UserLayout.vue';
import FlowChart from '@/Components/Procedure/FlowChart.vue';
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

    // 1. TKO Document Editor Tab
    list.push({
        id: 'tko_document',
        order: 1,
        label: '📝 DOKUMEN TKO',
        labelShort: '📝',
        type: 'tko'
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

    // 4. Manage Sections settings tab at the end
    list.push({
        id: 'manage_sections',
        order: 100,
        label: '⚙️ KELOLA SECTION',
        labelShort: '⚙️',
        type: 'manage_sections'
    });

    return list.sort((a, b) => a.order - b.order);
});

const activeTab = ref(allSections.value[0]?.id || 'tko_document');

const activeSection = computed(() => {
    return allSections.value.find(s => s.id === activeTab.value) || null;
});

const selectedRegulationId = ref(props.selectedRegulationId);
watch(() => props.selectedRegulationId, (newId) => {
    selectedRegulationId.value = newId;
});

const isManagePage = computed(() => true);

function getSopsForCategory(categoryId) {
    if (!props.sop) return [];
    return props.sop.filter((s) => Number(s.category_id) === Number(categoryId));
}

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find((r) => r.id === selectedRegulationId.value) || props.regulations[0] || null;
});

const filteredOrganizations = computed(() => {
    return props.organizations.filter((org) => org.jabatan && String(org.jabatan).trim() !== '');
});

// ---------------------------------------------------
// LOCAL EDITING STATES & CKEDITOR SETUP
// ---------------------------------------------------
const isEditorLoaded = ref(false);
const editorContainer = ref(null);
const editorInstance = ref(null);
let autoSaveTimer = null;

const tkoInputs = ref({});
const actorLocal = ref({});
const categoryLocal = ref({});
const sopLocal = ref({});
const sectionLocal = ref({});

const saveStatuses = ref({});

const tkoDebounceTimers = {};
const actorDebounceTimers = {};
const categoryDebounceTimers = {};
const sopDebounceTimers = {};
const sectionDebounceTimers = {};

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

function getMergedHtml() {
    const romanNumerals = {
        1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X'
    };

    let html = '';
    
    // Merge global sections in order (we sort by section.order)
    const orderedTkoSections = [...(props.tkoSections || [])].sort((a, b) => a.order - b.order);
    
    orderedTkoSections.forEach((s) => {
        const roman = romanNumerals[s.order] || s.order;
        const headingText = `${roman}. ${s.name.toUpperCase()}`;
        const content = s.contents?.[0]?.content || '';
        
        html += `<h1>${headingText}</h1>`;
        if (content) {
            if (content.trim().startsWith('<') || content.includes('</')) {
                html += content;
            } else {
                // Convert plain text newlines to paragraphs
                const lines = content.replace(/\r\n/g, '\n').split('\n');
                lines.forEach(line => {
                    if (line.trim()) {
                        html += `<p>${line}</p>`;
                    }
                });
            }
        } else {
            html += `<p></p>`;
        }
    });

    return html;
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
        editor.setData(getMergedHtml());
        isEditorLoaded.value = true;

        // Auto-save on data change
        editor.model.document.on('change:data', () => {
            triggerAutoSave();
        });
    })
    .catch(error => {
        console.error('Gagal menginisialisasi CKEditor:', error);
    });
}

function triggerAutoSave() {
    saveStatuses.value['tko_document'] = 'saving';
    if (autoSaveTimer) clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        saveStructuredDocument();
    }, 1500);
}

function saveStructuredDocument() {
    if (!editorInstance.value) return;

    const htmlData = editorInstance.value.getData();
    const parsedSections = parseMergedHtml(htmlData);

    saveStatuses.value['tko_document'] = 'saving';

    axios.post(route('policy.procedure.tko-content.save-structured'), {
        regulation_id: activeRegulation.value?.id || '',
        sections: parsedSections
    })
    .then(() => {
        saveStatuses.value['tko_document'] = 'saved';
        setTimeout(() => {
            if (saveStatuses.value['tko_document'] === 'saved') {
                saveStatuses.value['tko_document'] = null;
            }
        }, 3000);
    })
    .catch(error => {
        console.error('Gagal menyimpan dokumen secara otomatis:', error);
        saveStatuses.value['tko_document'] = 'error';
    });
}

function parseMergedHtml(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const children = Array.from(doc.body.children);

    const parsedSections = [];
    let currentSection = null;

    children.forEach(child => {
        if (child.tagName === 'H1') {
            if (currentSection) {
                parsedSections.push(currentSection);
            }

            const rawTitle = child.textContent || '';
            // Strip Roman numeral prefix (e.g. "I. TUJUAN" -> "TUJUAN")
            const cleanTitle = rawTitle.replace(/^([ivxIVX\d]+[\.\)\-\s]+)+/, '').trim();

            currentSection = {
                name: cleanTitle || 'Section Tanpa Nama',
                contentElements: []
            };
        } else {
            if (!currentSection) {
                currentSection = {
                    name: 'TUJUAN',
                    contentElements: []
                };
            }
            currentSection.contentElements.push(child.outerHTML);
        }
    });

    if (currentSection) {
        parsedSections.push(currentSection);
    }

    // Map and assign orders (excluding 4 and 5 for Fungsi & Prosedur)
    return parsedSections.map((sec, index) => {
        let order = index + 1;
        if (order >= 4) {
            order += 2; // skip 4 and 5
        }
        return {
            name: sec.name,
            order: order,
            content: sec.contentElements.join('\n')
        };
    });
}

function initLocalStates() {
    // Actors
    props.actors.forEach(actor => {
        if (!actorLocal.value[actor.id]) {
            actorLocal.value[actor.id] = {
                name: actor.name || '',
                organization_id: actor.organization_id || ''
            };
        } else {
            if (!actorDebounceTimers[actor.id]) {
                actorLocal.value[actor.id].name = actor.name || '';
                actorLocal.value[actor.id].organization_id = actor.organization_id || '';
            }
        }
    });

    // Categories
    props.categories.forEach(cat => {
        if (!categoryLocal.value[cat.id]) {
            categoryLocal.value[cat.id] = {
                tipe: cat.tipe || ''
            };
        } else {
            if (!categoryDebounceTimers[cat.id]) {
                categoryLocal.value[cat.id].tipe = cat.tipe || '';
            }
        }
    });

    // SOPs
    props.sop.forEach(item => {
        if (!sopLocal.value[item.id]) {
            sopLocal.value[item.id] = {
                category_id: item.category_id,
                description: item.description || ''
            };
        } else {
            if (!sopDebounceTimers[item.id]) {
                sopLocal.value[item.id].category_id = item.category_id;
                sopLocal.value[item.id].description = item.description || '';
            }
        }
    });

    // Sections
    props.tkoSections.forEach(sec => {
        if (!sectionLocal.value[sec.id]) {
            sectionLocal.value[sec.id] = {
                name: sec.name || '',
                order: sec.order || 1
            };
        } else {
            if (!sectionDebounceTimers[sec.id]) {
                sectionLocal.value[sec.id].name = sec.name || '';
                sectionLocal.value[sec.id].order = sec.order || 1;
            }
        }
    });
}

// Initialize states
initLocalStates();

watch(() => [props.tkoSections, props.actors, props.categories, props.sop], () => {
    initLocalStates();
}, { deep: true });

watch(activeTab, (newTab) => {
    if (newTab === 'tko_document') {
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
    if (activeTab.value === 'tko_document' && editorInstance.value) {
        editorInstance.value.setData(getMergedHtml());
    }
});

onMounted(() => {
    if (activeTab.value === 'tko_document') {
        loadCKEditor().then(() => {
            initEditor();
        });
    }
});

onUnmounted(() => {
    if (autoSaveTimer) clearTimeout(autoSaveTimer);
    if (editorInstance.value) {
        editorInstance.value.destroy();
    }
});

// Actor Actions
function addActorDirectly() {
    const defaultOrgId = filteredOrganizations.value[0]?.id || '';
    if (!defaultOrgId) {
        Swal.fire({
            title: 'Error',
            text: 'Tidak ada organisasi terkait yang valid.',
            icon: 'error'
        });
        return;
    }
    
    router.post(route('policy.procedure.actor.store'), {
        name: 'Aktor Baru',
        organization_id: defaultOrgId,
        regulation_id: activeRegulation.value?.id || '',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Aktor baru telah ditambahkan.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }
    });
}

function handleActorInput(actor) {
    saveStatuses.value[`actor_${actor.id}`] = 'saving';
    if (actorDebounceTimers[actor.id]) clearTimeout(actorDebounceTimers[actor.id]);
    actorDebounceTimers[actor.id] = setTimeout(() => {
        saveActor(actor);
    }, 1500);
}

function saveActor(actor) {
    if (actorDebounceTimers[actor.id]) {
        clearTimeout(actorDebounceTimers[actor.id]);
        delete actorDebounceTimers[actor.id];
    }
    saveStatuses.value[`actor_${actor.id}`] = 'saving';
    
    router.put(route('policy.procedure.actor.update', actor.id), {
        name: actorLocal.value[actor.id]?.name || '',
        organization_id: actorLocal.value[actor.id]?.organization_id || '',
        regulation_id: activeRegulation.value?.id || '',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            saveStatuses.value[`actor_${actor.id}`] = 'saved';
            setTimeout(() => {
                if (saveStatuses.value[`actor_${actor.id}`] === 'saved') {
                    saveStatuses.value[`actor_${actor.id}`] = null;
                }
            }, 3000);
        },
        onError: () => {
            saveStatuses.value[`actor_${actor.id}`] = 'error';
        }
    });
}

function deleteActorDirectly(actor) {
    Swal.fire({
        title: 'Hapus Aktor',
        text: `Apakah Anda yakin ingin menghapus aktor '${actor.name}'?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.actor.destroy', actor.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Aktor berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                    });
                }
            });
        }
    });
}

const hasSavingActor = computed(() => {
    return Object.keys(saveStatuses.value).some(k => k.startsWith('actor_') && saveStatuses.value[k] === 'saving');
});
const hasSavedActor = computed(() => {
    return Object.keys(saveStatuses.value).some(k => k.startsWith('actor_') && saveStatuses.value[k] === 'saved');
});
const hasErrorActor = computed(() => {
    return Object.keys(saveStatuses.value).some(k => k.startsWith('actor_') && saveStatuses.value[k] === 'error');
});

// Category Actions
function addCategoryDirectly() {
    router.post(route('policy.procedure.category.store'), {
        regulation_id: activeRegulation.value?.id || '',
        tipe: 'Kategori Baru',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Kategori baru telah ditambahkan.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }
    });
}

function handleCategoryInput(cat) {
    saveStatuses.value[`category_${cat.id}`] = 'saving';
    if (categoryDebounceTimers[cat.id]) clearTimeout(categoryDebounceTimers[cat.id]);
    categoryDebounceTimers[cat.id] = setTimeout(() => {
        saveCategory(cat);
    }, 1500);
}

function saveCategory(cat) {
    if (categoryDebounceTimers[cat.id]) {
        clearTimeout(categoryDebounceTimers[cat.id]);
        delete categoryDebounceTimers[cat.id];
    }
    saveStatuses.value[`category_${cat.id}`] = 'saving';
    
    router.put(route('policy.procedure.category.update', cat.id), {
        tipe: categoryLocal.value[cat.id]?.tipe || '',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            saveStatuses.value[`category_${cat.id}`] = 'saved';
            setTimeout(() => {
                if (saveStatuses.value[`category_${cat.id}`] === 'saved') {
                    saveStatuses.value[`category_${cat.id}`] = null;
                }
            }, 3000);
        },
        onError: () => {
            saveStatuses.value[`category_${cat.id}`] = 'error';
        }
    });
}

function deleteCategoryDirectly(cat) {
    Swal.fire({
        title: 'Hapus Kategori',
        text: `Apakah Anda yakin ingin menghapus kategori '${cat.tipe}' beserta seluruh SOP di dalamnya?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.category.destroy', cat.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Kategori berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                    });
                }
            });
        }
    });
}

// SOP Actions
function addSopDirectly(categoryId) {
    router.post(route('policy.procedure.sop.store'), {
        category_id: categoryId,
        description: 'Aktivitas SOP Baru',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'SOP baru telah ditambahkan.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }
    });
}

function handleSopInput(item) {
    saveStatuses.value[`sop_${item.id}`] = 'saving';
    if (sopDebounceTimers[item.id]) clearTimeout(sopDebounceTimers[item.id]);
    sopDebounceTimers[item.id] = setTimeout(() => {
        saveSop(item);
    }, 1500);
}

function saveSop(item) {
    if (sopDebounceTimers[item.id]) {
        clearTimeout(sopDebounceTimers[item.id]);
        delete sopDebounceTimers[item.id];
    }
    saveStatuses.value[`sop_${item.id}`] = 'saving';
    
    router.put(route('policy.procedure.sop.update', item.id), {
        category_id: sopLocal.value[item.id]?.category_id,
        description: sopLocal.value[item.id]?.description || '',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            saveStatuses.value[`sop_${item.id}`] = 'saved';
            setTimeout(() => {
                if (saveStatuses.value[`sop_${item.id}`] === 'saved') {
                    saveStatuses.value[`sop_${item.id}`] = null;
                }
            }, 3000);
        },
        onError: () => {
            saveStatuses.value[`sop_${item.id}`] = 'error';
        }
    });
}

function deleteSopDirectly(item) {
    Swal.fire({
        title: 'Hapus SOP',
        text: `Apakah Anda yakin ingin menghapus SOP '${shortSopDescription(item.description)}'?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.sop.destroy', item.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'SOP berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                    });
                }
            });
        }
    });
}

const hasSavingProsedur = computed(() => {
    return Object.keys(saveStatuses.value).some(k => (k.startsWith('sop_') || k.startsWith('category_')) && saveStatuses.value[k] === 'saving');
});
const hasSavedProsedur = computed(() => {
    return Object.keys(saveStatuses.value).some(k => (k.startsWith('sop_') || k.startsWith('category_')) && saveStatuses.value[k] === 'saved');
});
const hasErrorProsedur = computed(() => {
    return Object.keys(saveStatuses.value).some(k => (k.startsWith('sop_') || k.startsWith('category_')) && saveStatuses.value[k] === 'error');
});

// Section Actions
function addSectionDirectly() {
    const maxOrder = props.tkoSections.reduce((max, s) => Math.max(max, s.order || 0), 0);
    let nextOrder = maxOrder + 1;
    if (nextOrder === 4 || nextOrder === 5) {
        nextOrder = 6;
    }
    
    router.post(route('policy.procedure.section.store'), {
        name: 'Section Baru',
        order: nextOrder,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Section baru telah ditambahkan.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }
    });
}

function handleSectionInput(sec) {
    saveStatuses.value[`section_${sec.id}`] = 'saving';
    if (sectionDebounceTimers[sec.id]) clearTimeout(sectionDebounceTimers[sec.id]);
    sectionDebounceTimers[sec.id] = setTimeout(() => {
        saveSection(sec);
    }, 1500);
}

function saveSection(sec) {
    if (sectionDebounceTimers[sec.id]) {
        clearTimeout(sectionDebounceTimers[sec.id]);
        delete sectionDebounceTimers[sec.id];
    }
    saveStatuses.value[`section_${sec.id}`] = 'saving';
    
    router.put(route('policy.procedure.section.update', sec.id), {
        name: sectionLocal.value[sec.id]?.name || '',
        order: sectionLocal.value[sec.id]?.order || 1,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            saveStatuses.value[`section_${sec.id}`] = 'saved';
            setTimeout(() => {
                if (saveStatuses.value[`section_${sec.id}`] === 'saved') {
                    saveStatuses.value[`section_${sec.id}`] = null;
                }
            }, 3000);
        },
        onError: () => {
            saveStatuses.value[`section_${sec.id}`] = 'error';
        }
    });
}

function deleteSectionDirectly(sec) {
    Swal.fire({
        title: 'Hapus Section',
        text: `Apakah Anda yakin ingin menghapus section '${sec.name}' beserta seluruh konten di dalamnya?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.section.destroy', sec.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Section berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                    });
                }
            });
        }
    });
}

const hasSavingSection = computed(() => {
    return Object.keys(saveStatuses.value).some(k => k.startsWith('section_') && saveStatuses.value[k] === 'saving');
});
const hasSavedSection = computed(() => {
    return Object.keys(saveStatuses.value).some(k => k.startsWith('section_') && saveStatuses.value[k] === 'saved');
});
const hasErrorSection = computed(() => {
    return Object.keys(saveStatuses.value).some(k => k.startsWith('section_') && saveStatuses.value[k] === 'error');
});

function tightSopText(text) {
    if (!text) return '';
    return String(text)
        .replace(/\r\n/g, '\n')
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line !== '')
        .join('\n');
}

function shortSopDescription(text) {
    if (!text) return '-';
    const normalized = tightSopText(text).replace(/\n/g, ' ');
    return normalized.length > 80 ? `${normalized.slice(0, 80)}...` : normalized;
}
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
