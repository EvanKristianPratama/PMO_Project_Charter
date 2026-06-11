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
                                @click="saveStructuredDocument"
                                :disabled="isSavingTko"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                                Save
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
                <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
                    <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

                    <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                            IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                        </h3>
                        <div class="flex items-center gap-3 print:hidden">
                            <!-- Save status indicator -->
                            <span class="text-[11px] flex items-center gap-1.5 select-none">
                                <span v-if="saveActorsStatus === 'saving'" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else-if="saveActorsStatus === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span v-else-if="saveActorsStatus === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                                    Gagal menyimpan
                                </span>
                                <span v-else-if="modifiedActors.size > 0" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
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
                                @click="saveAllActors"
                                :disabled="isSavingActors || modifiedActors.size === 0"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                                Simpan Manual
                            </button>

                            <button
                                @click="addActorDirectly"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 dark:border-white/10 bg-transparent px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-white/5 active:scale-95"
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
                                            @input="handleActorChange(actor.id)"
                                            type="text"
                                            class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10"
                                            placeholder="Nama Aktor / Jabatan"
                                        />
                                    </td>
                                    <td class="px-6 py-2 align-middle">
                                        <select
                                            v-if="actorLocal[actor.id]"
                                            v-model="actorLocal[actor.id].organization_id"
                                            @change="handleActorChange(actor.id)"
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

                    <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                            V. PROSEDUR
                        </h3>
                        <div class="flex items-center gap-3 print:hidden">
                            <!-- Save status indicator -->
                            <span class="text-[11px] flex items-center gap-1.5 select-none">
                                <span v-if="saveProsedurStatus === 'saving'" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else-if="saveProsedurStatus === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span v-else-if="saveProsedurStatus === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                                    Gagal menyimpan
                                </span>
                                <span v-else-if="modifiedCategories.size > 0 || modifiedSops.size > 0" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
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
                                @click="saveAllProsedur"
                                :disabled="isSavingProsedur || (modifiedCategories.size === 0 && modifiedSops.size === 0)"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                                Simpan Manual
                            </button>

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
                                        @input="handleCategoryChange(cat.id)"
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
                                            @input="handleSopChange(item.id)"
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
                            <!-- Save status indicator -->
                            <span class="text-[11px] flex items-center gap-1.5 select-none print:hidden mr-2">
                                <span v-if="saveSectionsStatus === 'saving'" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else-if="saveSectionsStatus === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span v-else-if="saveSectionsStatus === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                                    Gagal menyimpan
                                </span>
                                <span v-else-if="modifiedSections.size > 0" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
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
                                @click="saveAllSections"
                                :disabled="isSavingSections || modifiedSections.size === 0"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50 print:hidden mr-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                                Simpan Manual
                            </button>

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
                                            @input="handleSectionChange(sec.id)"
                                            type="text"
                                            class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10 font-bold"
                                            placeholder="Nama Section (Contoh: TUJUAN)"
                                        />
                                    </td>
                                    <td class="px-6 py-2 align-middle text-center">
                                        <input
                                            v-if="sectionLocal[sec.id]"
                                            v-model="sectionLocal[sec.id].order"
                                            @input="handleSectionChange(sec.id)"
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
        label: 'DOKUMEN TKO',
        labelShort: 'DOKUMEN TKO',
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
const hasUnsavedChanges = ref(false);

const modifiedActors = ref(new Set());
const modifiedCategories = ref(new Set());
const modifiedSops = ref(new Set());
const modifiedSections = ref(new Set());

const isSavingActors = ref(false);
const saveActorsStatus = ref(null);

const isSavingProsedur = ref(false);
const saveProsedurStatus = ref(null);

const isSavingSections = ref(false);
const saveSectionsStatus = ref(null);

// TKO Document save state (dedicated flags to avoid reactive lookup issues)
const isSavingTko = ref(false);
const saveTkoStatus = ref(null);

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
        editor.setData(getMergedHtml());
        isEditorLoaded.value = true;

        // Set unsaved changes flag on data change
        editor.model.document.on('change:data', () => {
            hasUnsavedChanges.value = true;
        });
    })
    .catch(error => {
        console.error('Gagal menginisialisasi CKEditor:', error);
    });
}

async function saveStructuredDocument() {
    if (!editorInstance.value) {
        console.warn('Editor belum siap, tidak dapat menyimpan.');
        return;
    }

    if (!activeRegulation.value?.id) {
        console.warn('Tidak ada regulasi aktif.');
        return;
    }

    const htmlData = editorInstance.value.getData();
    const parsedSections = parseMergedHtml(htmlData);

    if (parsedSections.length === 0) {
        console.warn('Tidak ada section untuk disimpan.');
        return;
    }

    isSavingTko.value = true;
    saveTkoStatus.value = null;
    hasUnsavedChanges.value = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        await axios.post(
            route('policy.procedure.tko-content.save-structured'),
            {
                regulation_id: activeRegulation.value.id,
                sections: parsedSections,
            },
            {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            }
        );

        saveTkoStatus.value = 'saved';
        hasUnsavedChanges.value = false;
        setTimeout(() => {
            saveTkoStatus.value = null;
        }, 3000);
    } catch (error) {
        console.error('Gagal menyimpan dokumen TKO:', error);
        saveTkoStatus.value = 'error';
        hasUnsavedChanges.value = true;
    } finally {
        isSavingTko.value = false;
    }
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
            content: sec.contentElements.join('\n'),
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

    if (oldTab === 'tko_document' && hasUnsavedChanges.value) {
        hasUnsaved = true;
        titleText = 'Ada perubahan dokumen TKO yang belum disimpan.';
        saveFn = () => saveStructuredDocument();
        clearFn = () => { hasUnsavedChanges.value = false; saveTkoStatus.value = null; };
    } else if (oldTab === 'fungsi' && modifiedActors.value.size > 0) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data aktor yang belum disimpan.';
        saveFn = saveAllActors;
        clearFn = () => { modifiedActors.value.clear(); };
    } else if (oldTab === 'prosedur' && (modifiedCategories.value.size > 0 || modifiedSops.value.size > 0)) {
        hasUnsaved = true;
        titleText = 'Ada perubahan data prosedur (kategori/SOP) yang belum disimpan.';
        saveFn = saveAllProsedur;
        clearFn = () => { modifiedCategories.value.clear(); modifiedSops.value.clear(); };
    } else if (oldTab === 'manage_sections' && modifiedSections.value.size > 0) {
        hasUnsaved = true;
        titleText = 'Ada perubahan pengaturan section yang belum disimpan.';
        saveFn = saveAllSections;
        clearFn = () => { modifiedSections.value.clear(); };
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
        hasUnsavedChanges.value = false;
    }
});

function handleBeforeUnload(e) {
    if (hasUnsavedChanges.value || 
        modifiedActors.value.size > 0 || 
        modifiedCategories.value.size > 0 || 
        modifiedSops.value.size > 0 || 
        modifiedSections.value.size > 0) {
        e.preventDefault();
        e.returnValue = 'Ada perubahan yang belum disimpan.';
        return e.returnValue;
    }
}

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
    if (activeTab.value === 'tko_document') {
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

// Actor Actions
function addActorDirectly() {
    const defaultOrgId = filteredOrganizations.value[0]?.id || '';
    if (!defaultOrgId) {
        Swal.fire({ title: 'Error', text: 'Tidak ada organisasi terkait yang valid.', icon: 'error' });
        return;
    }
    
    router.post(route('policy.procedure.actor.store'), {
        name: 'Aktor Baru',
        organization_id: defaultOrgId,
        regulation_id: activeRegulation.value?.id || '',
    }, { preserveScroll: true });
}

function handleActorChange(actorId) {
    modifiedActors.value.add(actorId);
}

async function saveAllActors() {
    if (modifiedActors.value.size === 0) return;
    isSavingActors.value = true;
    saveActorsStatus.value = 'saving';

    try {
        const promises = [];
        for (const actorId of modifiedActors.value) {
            const actorData = actorLocal.value[actorId];
            promises.push(
                axios.put(route('policy.procedure.actor.update', actorId), {
                    name: actorData.name,
                    organization_id: actorData.organization_id,
                    regulation_id: activeRegulation.value?.id || '',
                })
            );
        }
        await Promise.all(promises);
        modifiedActors.value.clear();
        saveActorsStatus.value = 'saved';
        router.reload({ preserveScroll: true });
        setTimeout(() => { saveActorsStatus.value = null; }, 3000);
    } catch (error) {
        console.error('Gagal menyimpan aktor:', error);
        saveActorsStatus.value = 'error';
    } finally {
        isSavingActors.value = false;
    }
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
            router.delete(route('policy.procedure.actor.destroy', actor.id), { preserveScroll: true });
        }
    });
}

// Category Actions
function addCategoryDirectly() {
    router.post(route('policy.procedure.category.store'), {
        regulation_id: activeRegulation.value?.id || '',
        tipe: 'Kategori Baru',
    }, { preserveScroll: true });
}

function handleCategoryChange(catId) {
    modifiedCategories.value.add(catId);
}

// SOP Actions
function addSopDirectly(categoryId) {
    router.post(route('policy.procedure.sop.store'), {
        category_id: categoryId,
        description: 'Aktivitas SOP Baru',
    }, { preserveScroll: true });
}

function handleSopChange(sopId) {
    modifiedSops.value.add(sopId);
}

async function saveAllProsedur() {
    if (modifiedCategories.value.size === 0 && modifiedSops.value.size === 0) return;
    isSavingProsedur.value = true;
    saveProsedurStatus.value = 'saving';

    try {
        const promises = [];
        for (const catId of modifiedCategories.value) {
            const catData = categoryLocal.value[catId];
            promises.push(
                axios.put(route('policy.procedure.category.update', catId), {
                    tipe: catData.tipe,
                })
            );
        }
        for (const sopId of modifiedSops.value) {
            const sopData = sopLocal.value[sopId];
            promises.push(
                axios.put(route('policy.procedure.sop.update', sopId), {
                    category_id: sopData.category_id,
                    description: sopData.description,
                })
            );
        }
        await Promise.all(promises);
        modifiedCategories.value.clear();
        modifiedSops.value.clear();
        saveProsedurStatus.value = 'saved';
        router.reload({ preserveScroll: true });
        setTimeout(() => { saveProsedurStatus.value = null; }, 3000);
    } catch (error) {
        console.error('Gagal menyimpan prosedur:', error);
        saveProsedurStatus.value = 'error';
    } finally {
        isSavingProsedur.value = false;
    }
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
            router.delete(route('policy.procedure.category.destroy', cat.id), { preserveScroll: true });
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
            router.delete(route('policy.procedure.sop.destroy', item.id), { preserveScroll: true });
        }
    });
}

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
    }, { preserveScroll: true });
}

function handleSectionChange(secId) {
    modifiedSections.value.add(secId);
}

async function saveAllSections() {
    if (modifiedSections.value.size === 0) return;
    isSavingSections.value = true;
    saveSectionsStatus.value = 'saving';

    try {
        const promises = [];
        for (const secId of modifiedSections.value) {
            const secData = sectionLocal.value[secId];
            promises.push(
                axios.put(route('policy.procedure.section.update', secId), {
                    name: secData.name,
                    order: secData.order,
                })
            );
        }
        await Promise.all(promises);
        modifiedSections.value.clear();
        saveSectionsStatus.value = 'saved';
        router.reload({ preserveScroll: true });
        setTimeout(() => { saveSectionsStatus.value = null; }, 3000);
    } catch (error) {
        console.error('Gagal menyimpan section:', error);
        saveSectionsStatus.value = 'error';
    } finally {
        isSavingSections.value = false;
    }
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
            router.delete(route('policy.procedure.section.destroy', sec.id), { preserveScroll: true });
        }
    });
}

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
