<template>
    <ModulLayout title="Mapping COBIT Khusus">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#152c5b]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#152c5b] dark:text-[#6084c9]">{{ activeRegulation?.judul }}</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Dokumen</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('policy.general.index', { regulation_id: props.selectedRegulationId || activeRegulation?.id })"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke Dokumen
                        </Link>
                        <Link
                            :href="route('policy.specific.manage', { regulation_id: props.selectedRegulationId || activeRegulation?.id })"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#1e40af] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#1e40af]/25 transition-all hover:bg-[#1e3a8a] hover:shadow-[#1e40af]/40 focus:ring-2 focus:ring-[#1e40af]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                            Kembali ke Daftar
                        </Link>
                    </div>
                </div>
            </section>


            <!-- Floating Alerts / Feedback at Bottom-Left -->
            <div class="fixed bottom-6 left-6 z-[9999] max-w-sm space-y-3 pointer-events-none">
                <transition name="fade">
                    <div v-if="localSuccess" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-emerald-200 bg-white p-4 text-sm text-[#065f46] shadow-2xl backdrop-blur-sm dark:border-emerald-500/30 dark:bg-[#1a1a1a] dark:text-emerald-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.807-9.617a.9.9 0 111.386 1.134l-4.5 5.5a.9.9 0 01-1.302.08l-2.5-2.5a.9.9 0 011.272-1.272l1.782 1.782 3.862-4.724z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localSuccess }}</span>
                    </div>
                </transition>

                <transition name="fade">
                    <div v-if="localError" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-rose-200 bg-white p-4 text-sm text-[#991b1b] shadow-2xl backdrop-blur-sm dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localError }}</span>
                    </div>
                </transition>
            </div>

            <!-- Objectives List -->
            <div class="space-y-8">
                <!-- If empty -->
                <div v-if="objectives.length === 0 && !isCreatingObjective" class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-400 mx-auto mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Belum ada Kebijakan Khusus</p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Klik tombol di atas untuk membuat Kebijakan Khusus dan Butir Kebijakan pertama Anda.
                    </p>
                    <button 
                        @click="startCreateObjective"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-[#152c5b] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#1e3f84] transition"
                    >
                        Buat Sekarang
                    </button>
                </div>

                
                <!-- View Toggle -->
                <div v-if="objectives.length > 0" class="flex justify-end mb-4">
                    <div class="inline-flex rounded-lg bg-slate-100 p-1 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                        <button 
                            @click="viewMode = 'table'" 
                            :class="viewMode === 'table' ? 'bg-white text-[#152c5b] shadow-sm font-semibold dark:bg-black/40 dark:text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'" 
                            class="px-4 py-1.5 rounded-md text-xs transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125m-9.75 0v1.5c0 .621.504 1.125 1.125 1.125m18.375-3.75v1.5c0 .621-.504 1.125-1.125 1.125m0-3.75v-1.5c0-.621-.504-1.125-1.125-1.125m-18.375 3.75V11.25m0 0V9.75m0 1.5c0 .621.504 1.125 1.125 1.125" /></svg>
                                Tampilan Tabel
                            </div>
                        </button>
                        <button 
                            @click="viewMode = 'edit'" 
                            :class="viewMode === 'edit' ? 'bg-white text-[#152c5b] shadow-sm font-semibold dark:bg-black/40 dark:text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'" 
                            class="px-4 py-1.5 rounded-md text-xs transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Mode Edit/Kelola
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Table View Mode -->
                <div v-if="viewMode === 'table' && objectives.length > 0" class="bg-white border border-slate-300 dark:bg-black/10 dark:border-white/10 text-sm overflow-hidden mb-8 shadow-sm">
                    <table class="w-full text-left border-collapse border border-slate-300">
                        <thead>
                            <tr class="bg-slate-200 dark:bg-white/10 text-center">
                                <th class="border border-slate-300 dark:border-white/20 p-3 font-bold w-1/2 uppercase text-[13px] text-slate-800 dark:text-slate-100">Kebijakan Khusus</th>
                                <th class="border border-slate-300 dark:border-white/20 p-3 font-bold w-1/2 uppercase text-[13px] text-slate-800 dark:text-slate-100">GAMO COBIT 2019</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="group in groupedObjectives" :key="group.domain">
                                <!-- Domain Row -->
                                <tr class="bg-slate-100 dark:bg-white/5 font-bold">
                                    <td class="border border-slate-300 dark:border-white/20 p-3 text-slate-900 dark:text-white text-[13px]">
                                        {{ group.metadata.letter }}. {{ group.domain }}
                                    </td>
                                    <td class="border border-slate-300 dark:border-white/20 p-3 text-slate-900 dark:text-white text-[13px]">
                                        {{ group.metadata.cobitName }}
                                    </td>
                                </tr>
                                <!-- Objectives Rows -->
                                <tr v-for="(obj, index) in group.objectives" :key="obj.objective_id" class="hover:bg-slate-50/50 dark:hover:bg-white/5">
                                    <td class="border border-slate-300 dark:border-white/20 p-3 pl-6 text-slate-700 dark:text-slate-300 align-top text-[13px]">
                                        {{ index + 1 }}. {{ obj.objective }}
                                    </td>
                                    <td class="border border-slate-300 dark:border-white/20 p-3 text-slate-700 dark:text-slate-300 align-top text-[13px]">
                                        <div v-if="obj.cobit_mappings && obj.cobit_mappings.length > 0">
                                            <div v-for="(map, mIdx) in obj.cobit_mappings" :key="map.id" :class="{'mt-2 pt-2 border-t border-slate-200 dark:border-white/10': mIdx > 0}">
                                                <span class="font-semibold">{{ map.cobit_objective }}</span>
                                                <span v-if="getCobitName(map.cobit_objective)">
                                                    - {{ getCobitName(map.cobit_objective) }}
                                                </span>
                                                <span v-else-if="parseCobitJson(map.description) && parseCobitJson(map.description).description">
                                                    - {{ parseCobitJson(map.description).description.split('.')[0] }}
                                                </span>
                                            </div>
                                        </div>
                                        <div v-else class="text-slate-400 text-[11px] italic">
                                            -
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Loop through actual objectives -->
                <div v-show="viewMode === 'edit'">
                <div v-for="obj in objectives" :key="obj.objective_id" class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative group mb-8">
                    <div class="lg:col-span-8 relative bg-white rounded-2xl border border-slate-200 shadow-md hover:shadow-xl transition-all duration-300 dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden flex flex-col h-full">
                    <!-- Mode Input / Edit Mode for Objective -->
                    <div v-if="editingObjectiveId === obj.objective_id">
                        <form @submit.prevent="submitUpdateObjective(obj.objective_id)">
                            <!-- Burgundy Header Band -->
                            <div class="bg-[#152c5b] p-5 text-white">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs font-semibold tracking-wider opacity-90 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span>Kebijakan (Bahasa Indonesia):</span>
                                    </div>
                                </div>

                                <!-- Input Regulasi di Edit Mode -->
                                <div class="mb-4 space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Pilih Regulasi:</label>
                                    <select 
                                        v-model="editObjectiveForm.regulation_id" 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                    >
                                        <option value="" class="text-slate-900">-- Pilih Regulasi --</option>
                                        <option v-for="reg in regulations" :key="reg.id" :value="reg.id" class="text-slate-900">
                                            {{ reg.judul }}
                                        </option>
                                    </select>
                                    <div v-if="editObjectiveForm.errors.regulation_id" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editObjectiveForm.errors.regulation_id }}</div>
                                </div>

                                <!-- Input Domain Manual di Edit Mode -->
                                <div class="mb-4 space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Nama Kebijakan (Domain):</label>
                                    <input 
                                        type="text" 
                                        v-model="editObjectiveForm.domain" 
                                        placeholder="Nama Kebijakan Manual..." 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                    />
                                    <div v-if="editObjectiveForm.errors.domain" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editObjectiveForm.errors.domain }}</div>
                                </div>

                                <!-- Input ID Kebijakan Editable di Edit Mode -->
                                <div class="mb-4 space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">ID Kebijakan:</label>
                                    <input 
                                        type="text" 
                                        v-model="editObjectiveForm.objective_id" 
                                        placeholder="Contoh: EDM01..." 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40 font-mono"
                                        required
                                    />
                                    <div v-if="editObjectiveForm.errors.objective_id" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editObjectiveForm.errors.objective_id }}</div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Kebijakan Khusus:</label>
                                    <input 
                                        type="text" 
                                        v-model="editObjectiveForm.objective" 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        required
                                    />
                                    <div v-if="editObjectiveForm.errors.objective" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editObjectiveForm.errors.objective }}</div>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="p-6 space-y-4">
                                <!-- Description -->
                                <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                    <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                        Deskripsi
                                    </div>
                                    <div class="p-4">
                                        <textarea 
                                            v-model="editObjectiveForm.objective_description" 
                                            rows="3" 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#152c5b]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Purpose -->
                                <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                    <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                        Tujuan
                                    </div>
                                    <div class="p-4">
                                        <textarea 
                                            v-model="editObjectiveForm.objective_purpose" 
                                            rows="3" 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#152c5b]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Row -->
                            <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                                <button 
                                    type="button"
                                    @click="deleteObjective(obj)"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-800 transition dark:text-rose-400 dark:hover:text-rose-300"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Hapus Kebijakan Khusus
                                </button>
                                
                                <div class="flex gap-3">
                                    <button 
                                        type="button" 
                                        @click="cancelEditObjective"
                                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        class="rounded-xl bg-[#152c5b] px-4 py-2 text-xs font-bold text-white shadow-md shadow-[#152c5b]/20 transition hover:bg-[#1e3f84] disabled:opacity-60"
                                        :disabled="editObjectiveForm.processing"
                                    >
                                        <span v-if="editObjectiveForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Mode COBIT / Preview Mode for Objective -->
                    <div v-else>
                        <!-- Burgundy Header Band -->
                        <div class="bg-[#152c5b] p-5 text-white flex flex-col gap-1 transition-all duration-300">
                            <!-- Quick floating options visible on card group hover -->
                            <div class="absolute right-4 top-4 opacity-80 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                                <button 
                                    @click="startEditObjective(obj)"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 border border-white/10 hover:bg-white/30 text-white backdrop-blur-md shadow transition-all duration-150 active:scale-90"
                                    title="Edit Kebijakan Khusus"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteObjective(obj)"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/80 hover:bg-rose-500 text-white backdrop-blur-md shadow transition-all duration-150 active:scale-90"
                                    title="Hapus Kebijakan Khusus"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex flex-wrap items-center justify-between text-xs font-semibold tracking-wider opacity-90 pr-20">
                                <div class="flex items-center gap-2">
                                    <span class="bg-white/20 px-2 py-0.5 rounded uppercase font-bold">{{ obj.domain || getDomainName(obj.objective_id) }}</span>
                                </div>
                            </div>
                            
                            <h2 class="mt-2 text-lg sm:text-xl font-bold tracking-tight pr-10">
                                Kebijakan Khusus: <span class="font-mono text-yellow-300">{{ obj.objective_id }}</span> — {{ obj.objective }}
                            </h2>
                        </div>

                        <!-- Card Content in Preview Mode -->
                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Description Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10 shadow-sm bg-white dark:bg-[#1f1f1f]">
                                <div class="bg-slate-50 px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#152c5b]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Deskripsi
                                </div>
                                <div class="p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ obj.objective_description || 'Belum ada deskripsi.' }}
                                </div>
                            </div>

                            <!-- Purpose Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10 shadow-sm bg-white dark:bg-[#1f1f1f]">
                                <div class="bg-slate-50 px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#152c5b]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.982-2.01c.784-.176 1.485-.645 1.947-1.3l2.898-4.078a1.534 1.534 0 00-.472-2.186l-4.502-2.602a1.534 1.534 0 00-2.122.441L12.83 13.8a4.117 4.117 0 01-3.017 2.104z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 10l1.25-2 .5-2-2.25 1-1.5 2-1 2-1 2.5" />
                                    </svg>
                                    Tujuan
                                </div>
                                <div class="p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ obj.objective_purpose || 'Belum ada deskripsi tujuan.' }}
                                </div>
                            </div>

                            <!-- Butir Kebijakan Section (mst_practice) -->
                            <div class="border border-[#152c5b]/25 rounded-lg overflow-hidden dark:border-[#6084c9]/30 shadow-sm">
                                <div class="bg-[#152c5b]/5 px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-800 dark:bg-white/5 dark:text-slate-300 border-b border-[#152c5b]/25 dark:border-white/10 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-[#152c5b] dark:text-[#93c5fd]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                        Butir Kebijakan (mst_practice)
                                    </div>
                                    <button 
                                        @click="startCreatePractice(obj.objective_id)"
                                        class="inline-flex items-center gap-1 rounded bg-[#152c5b] px-2.5 py-1 text-[10px] font-bold text-white hover:bg-[#1e3f84] transition active:scale-95 shadow-sm shadow-[#152c5b]/20"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Tambah Butir Kebijakan
                                    </button>
                                </div>

                                <!-- Practice Editor Form (Inline row inside the card) -->
                                <div v-if="activePracticeObjectiveId === obj.objective_id" class="bg-slate-50 p-4 border-b border-dashed border-[#152c5b]/20 dark:bg-[#262626]">
                                    <h4 class="text-xs font-bold text-[#152c5b] dark:text-[#6084c9] mb-3 flex items-center gap-1.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#152c5b] animate-pulse"></span>
                                        {{ editingPracticeId ? 'Edit Butir Kebijakan' : 'Tambah Butir Kebijakan Baru' }}
                                    </h4>
                                    
                                    <form @submit.prevent="submitPracticeForm" class="space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div class="space-y-1 sm:col-span-1">
                                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">ID Butir Kebijakan:</label>
                                                <input 
                                                    type="text" 
                                                    v-model="practiceForm.practice_id" 
                                                    placeholder="Contoh: EDM01.01" 
                                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#152c5b]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                                    required
                                                />
                                                <div v-if="practiceForm.errors.practice_id" class="text-[10px] text-red-500 font-medium font-mono">{{ practiceForm.errors.practice_id }}</div>
                                            </div>
                                            <div class="space-y-1 sm:col-span-2">
                                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Butir Kebijakan:</label>
                                                <input 
                                                    type="text" 
                                                    v-model="practiceForm.practice_name" 
                                                    placeholder="Nama Butir Kebijakan..." 
                                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#152c5b]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                                />
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi Butir Kebijakan:</label>
                                            <textarea 
                                                v-model="practiceForm.practice_description" 
                                                rows="2" 
                                                placeholder="Detail aktivitas atau deskripsi butir kebijakan..."
                                                class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg p-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#152c5b]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                            ></textarea>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-2">
                                            <button 
                                                type="button" 
                                                @click="cancelPracticeForm"
                                                class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                                            >
                                                Batal
                                            </button>
                                            <button 
                                                type="submit" 
                                                class="rounded-lg bg-[#152c5b] px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#1e3f84] disabled:opacity-60"
                                                :disabled="practiceForm.processing"
                                            >
                                                <span v-if="practiceForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                                Simpan Butir Kebijakan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Practice Rows Table -->
                                <div class="divide-y divide-slate-100 dark:divide-white/5">
                                    <!-- No practices yet -->
                                    <div v-if="!obj.practices || obj.practices.length === 0" class="p-4 text-center text-xs text-slate-400 dark:text-slate-500">
                                        Belum ada Butir Kebijakan. Klik "+ Tambah Butir Kebijakan" untuk memasukkan data.
                                    </div>

                                    <!-- Loop practices -->
                                    <div 
                                        v-for="prac in obj.practices" 
                                        :key="prac.practice_id"
                                        class="p-4 hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 flex flex-col sm:flex-row gap-3 sm:items-start group/row"
                                    >
                                        <!-- ID Column -->
                                        <div class="shrink-0 flex items-center gap-2">
                                            <span class="inline-block font-mono text-xs font-bold bg-[#152c5b]/10 text-[#152c5b] px-2 py-0.5 rounded border border-[#152c5b]/10 dark:bg-[#6084c9]/20 dark:text-[#93c5fd]">
                                                {{ prac.practice_id }}
                                            </span>
                                        </div>

                                        <!-- Name & Desc Column -->
                                        <div class="flex-1 space-y-1">
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                                {{ prac.practice_name || 'Tanpa Nama Butir Kebijakan' }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                                                {{ prac.practice_description }}
                                            </div>
                                        </div>

                                        <!-- Row Actions (Edit / Delete) -->
                                        <div class="shrink-0 flex gap-2 self-end sm:self-start opacity-80 group-hover/row:opacity-100 transition-opacity duration-150">
                                            <button 
                                                @click="startEditPractice(obj.objective_id, prac)"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#152c5b] hover:bg-[#152c5b]/5 hover:border-[#152c5b]/20 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-[#93c5fd] dark:hover:bg-[#93c5fd]/10"
                                                title="Edit Butir Kebijakan"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="deletePractice(prac)"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-rose-400"
                                                title="Hapus Butir Kebijakan"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Right Panel (Mapping COBIT 2019) -->
                <div class="lg:col-span-4 bg-white dark:bg-[#1a1a1a] flex flex-col h-full border border-slate-200 dark:border-white/10 shadow-md rounded-2xl overflow-hidden transition-all duration-300">
                    <!-- Mapping Section -->
                        <div class="flex flex-col h-full bg-slate-50 dark:bg-black/20">
                            <div class="flex-grow overflow-y-auto">
                                <!-- Header -->
                                <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200 p-4 flex items-center justify-between dark:bg-[#111111]/80 dark:border-white/10">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-[#1e40af]/10 p-1.5 rounded-lg text-[#1e40af] dark:bg-[#3b82f6]/20 dark:text-[#3b82f6]">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                                        </div>
                                        <h3 class="font-bold text-sm text-slate-800 dark:text-white leading-tight">
                                            Mapping COBIT 2019
                                        </h3>
                                    </div>
                                    <button 
                                        @click="startCreateMapping(obj.objective_id)"
                                        v-if="activeMappingObjectiveId !== obj.objective_id"
                                        class="text-[10px] font-bold bg-[#1e40af] text-white px-2.5 py-1.5 rounded-lg hover:bg-[#1e3a8a] transition shadow-sm"
                                    >
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Mapping Editor Form -->
                                <div v-if="activeMappingObjectiveId === obj.objective_id" class="bg-slate-50 p-4 border-b border-dashed border-[#1e40af]/20 dark:bg-[#262626]">
                                    <div class="mb-3 flex items-center gap-2">
                                        <div class="h-1.5 w-1.5 rounded-full bg-[#1e40af] dark:bg-[#3b82f6]"></div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ editingMappingId ? 'Edit Mapping COBIT' : 'Tambah Mapping COBIT' }}
                                        </h4>
                                    </div>
                                    <form @submit.prevent="submitMappingForm" class="space-y-3">
                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pilih COBIT Objective</label>
                                            <select 
                                                v-model="mappingForm.cobit_objective" 
                                                @change="handleCobitObjectiveChange"
                                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#1e40af] dark:bg-black/20 dark:border-white/20 dark:text-white"
                                                required
                                            >
                                                <option value="" disabled>-- Pilih COBIT Objective --</option>
                                                <option v-for="cObj in cobitObjectivesList" :key="cObj.id" :value="cObj.id">
                                                    {{ cObj.id }} - {{ cObj.name }}
                                                </option>
                                            </select>
                                        </div>
                                        
                                        <div v-if="mappingForm.description">
                                            <div v-if="parseCobitJson(mappingForm.description)" class="space-y-2 mt-2">
                                                <div class="bg-white rounded-lg p-3 border border-slate-100 shadow-sm dark:bg-[#262626] dark:border-white/5">
                                                    <h5 class="text-[10px] font-bold text-slate-400 uppercase mb-1">Deskripsi</h5>
                                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">{{ parseCobitJson(mappingForm.description).description }}</p>
                                                </div>
                                                <div class="bg-white rounded-lg p-3 border border-slate-100 shadow-sm dark:bg-[#262626] dark:border-white/5">
                                                    <h5 class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tujuan</h5>
                                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">{{ parseCobitJson(mappingForm.description).purpose }}</p>
                                                </div>
                                                <div v-if="isFetchingPractices" class="mt-3 flex items-center justify-center p-4"><span class="inline-block w-4 h-4 border-2 border-[#1e40af] border-t-transparent rounded-full animate-spin"></span><span class="ml-2 text-[10px] font-bold text-slate-500 uppercase">Memuat Practice...</span></div>
                                                <!-- Practices Section -->
                                                <div v-else-if="parseCobitJson(mappingForm.description).practices && parseCobitJson(mappingForm.description).practices.length > 0" class="mt-3 bg-white rounded-lg p-3 border border-slate-100 shadow-sm dark:bg-[#262626] dark:border-white/5">
                                                    <h5 class="text-[10px] font-bold text-slate-400 uppercase mb-2">Butir Kebijakan (COBIT Practices)</h5>
                                                    <div class="space-y-2">
                                                        <div v-for="prac in parseCobitJson(mappingForm.description).practices" :key="prac.id" class="flex gap-2">
                                                            <div class="shrink-0 pt-0.5">
                                                                <span class="inline-block font-mono text-[10px] font-bold bg-[#1e40af]/10 text-[#1e40af] px-1.5 py-0.5 rounded border border-[#1e40af]/10 dark:bg-[#3b82f6]/20 dark:text-[#60a5fa]">{{ prac.id }}</span>
                                                            </div>
                                                            <div class="text-[11px] text-slate-600 dark:text-slate-300 leading-snug">
                                                                <span class="font-bold text-slate-700 dark:text-slate-200 block mb-0.5">{{ prac.name }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="space-y-1 mt-2">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi (Manual)</label>
                                                <textarea 
                                                    v-model="mappingForm.description" 
                                                    rows="3" 
                                                    class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#1e40af] dark:bg-black/20 dark:border-white/20 dark:text-white"
                                                ></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end gap-2 pt-2">
                                            <button 
                                                type="button" 
                                                @click="cancelMappingForm"
                                                class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition dark:bg-transparent dark:border-white/20 dark:text-slate-300 dark:hover:bg-white/5"
                                            >Batal</button>
                                            <button 
                                                type="submit" 
                                                :disabled="mappingForm.processing || isFetchingPractices"
                                                class="px-3 py-1.5 text-xs font-bold text-white bg-[#1e40af] rounded-lg shadow-sm hover:bg-[#1e3a8a] transition disabled:opacity-50 flex items-center"
                                            >
                                                <span v-if="mappingForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                                Simpan Mapping
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Mapping Rows Table -->
                                <div class="p-4 space-y-3">
                                    <div v-if="!obj.cobit_mappings || obj.cobit_mappings.length === 0" class="p-4 text-center text-xs text-slate-400 dark:text-slate-500 border border-dashed border-slate-300 rounded-xl dark:border-white/10">
                                        Belum ada data mapping. Klik "+ Tambah" di atas.
                                    </div>
                                    
                                    <div 
                                        v-for="map in obj.cobit_mappings" 
                                        :key="map.id"
                                        class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden dark:bg-[#1a1a1a] dark:border-white/10 relative group/map transition-all hover:border-[#1e40af]/30 dark:hover:border-[#3b82f6]/30"
                                    >
                                        <div class="bg-blue-50/50 p-2.5 border-b border-slate-100 flex justify-between items-center dark:bg-blue-900/10 dark:border-white/5">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-[#1e40af] dark:text-[#3b82f6] text-xs">
                                                    {{ map.cobit_objective || 'Manual' }}
                                                </span>
                                            </div>
                                            <div class="flex gap-1 opacity-0 group-hover/map:opacity-100 transition-opacity">
                                                <button 
                                                    @click="startEditMapping(obj.objective_id, map)"
                                                    class="p-1 text-slate-400 hover:text-[#1e40af] bg-white rounded border border-slate-200 hover:border-[#1e40af]/30 transition dark:bg-black/20 dark:border-white/10 dark:hover:text-[#3b82f6]"
                                                    title="Edit Mapping"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" /></svg>
                                                </button>
                                                <button 
                                                    @click="deleteMapping(map)"
                                                    class="p-1 text-slate-400 hover:text-rose-600 bg-white rounded border border-slate-200 hover:border-rose-200 transition dark:bg-black/20 dark:border-white/10 dark:hover:text-rose-400"
                                                    title="Hapus Mapping"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.05l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.05a.75.75 0 10-1.5-.05l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <div v-if="parseCobitJson(map.description)" class="space-y-2">
                                                <div>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase block mb-0.5">Deskripsi:</span>
                                                    <p class="text-[11px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ parseCobitJson(map.description).description }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase block mb-0.5">Tujuan:</span>
                                                    <p class="text-[11px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ parseCobitJson(map.description).purpose }}</p>
                                                </div>
                                                <div v-if="parseCobitJson(map.description).practices && parseCobitJson(map.description).practices.length > 0" class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5">
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase block mb-2">Butir Kebijakan (COBIT Practices):</span>
                                                    <div class="space-y-2">
                                                        <div v-for="prac in parseCobitJson(map.description).practices" :key="prac.id" class="flex gap-2">
                                                            <div class="shrink-0 pt-0.5">
                                                                <span class="inline-block font-mono text-[9px] font-bold bg-[#1e40af]/10 text-[#1e40af] px-1.5 py-0.5 rounded border border-[#1e40af]/10 dark:bg-[#3b82f6]/20 dark:text-[#60a5fa]">{{ prac.id }}</span>
                                                            </div>
                                                            <div class="text-[11px] text-slate-700 dark:text-slate-300 leading-snug font-medium">
                                                                {{ prac.name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else-if="map.description" class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                                                {{ map.description }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Loop Item Wrapper -->
                </div> <!-- End viewMode === 'edit' -->
            </div>

            <!-- Floating Navigation Controls on the Right Side -->
            <div class="fixed right-6 bottom-24 z-[9999] flex flex-col gap-3 pointer-events-none">
                <!-- Add Policy Shortcut Button -->
                <button 
                    @click="startCreateObjective" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#152c5b] text-white shadow-lg border border-[#152c5b]/20 transition-all hover:bg-[#1e3f84] active:scale-90 hover:shadow-xl"
                    title="Tambah Kebijakan Khusus"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>

                <!-- Scroll to Top Button -->
                <button 
                    @click="scrollToTop" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Kembali ke Atas"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                    </svg>
                </button>

                <!-- Scroll to Bottom Button -->
                <button 
                    @click="scrollToBottom" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Ke Paling Bawah"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                    </svg>
                </button>

                <!-- Go to Home Button -->
                <Link 
                    :href="route('dashboard')" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Kembali ke Beranda"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </Link>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { usePage, useForm, router, Link } from '@inertiajs/vue3';
import axios from 'axios';
import ModulLayout from '@/Layouts/ModulLayout.vue';

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function scrollToBottom() {
    window.scrollTo({
        top: document.documentElement.scrollHeight,
        behavior: 'smooth'
    });
}

const props = defineProps({
    objectives: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: [Number, String],
        default: null,
    },
    selectedObjectiveId: {
        type: [Number, String],
        default: null,
    }
});

const isMobile = ref(false);
const showMobileSidebar = ref(false);

const checkMobile = () => {
    isMobile.value = window.innerWidth < 768;
    if (!isMobile.value) {
        showMobileSidebar.value = false;
    }
};

onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
    fetchCobitObjectives();
    
    // Automatically open accordion if redirected with selectedObjectiveId
    if (props.selectedObjectiveId) {
        mappingToggles.value[props.selectedObjectiveId] = true;
    }
});

const activeRegulation = computed(() => {
    if (!props.selectedRegulationId) return props.regulations[0] || null;
    return props.regulations.find(r => r.id === props.selectedRegulationId) || null;
});


// COBIT MAPPING: FORM STATE & TOGGLE STATE
const mappingToggles = ref({}); // Track toggle state per objective_id. Default is false
const activeMappingObjectiveId = ref(null);
const editingMappingId = ref(null);

function toggleMappingPanel(objectiveId) {
    mappingToggles.value[objectiveId] = !mappingToggles.value[objectiveId];
}

const mappingForm = useForm({
    objective_id: '',
    cobit_objective: '',
    description: '',
});

function startCreateMapping(objectiveId) {
    activeMappingObjectiveId.value = objectiveId;
    editingMappingId.value = null;
    mappingForm.reset();
    mappingForm.objective_id = objectiveId;
}

function startEditMapping(objectiveId, map) {
    activeMappingObjectiveId.value = objectiveId;
    editingMappingId.value = map.id;
    mappingForm.objective_id = objectiveId;
    mappingForm.cobit_objective = map.cobit_objective || map.cobit_objective_id; // Support both properties
    mappingForm.description = map.description;
}

function cancelMappingForm() {
    activeMappingObjectiveId.value = null;
    editingMappingId.value = null;
    mappingForm.reset();
}

function submitMappingForm() {
    if (editingMappingId.value) {
        mappingForm.put(route('policy.cobit-mapping.update', editingMappingId.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => cancelMappingForm(),
        });
    } else {
        mappingForm.post(route('policy.cobit-mapping.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => cancelMappingForm(),
        });
    }
}

function deleteMapping(map) {
    if (confirm('Yakin ingin menghapus mapping ini?')) {
        router.delete(route('policy.cobit-mapping.destroy', map.id), {
            preserveScroll: true,
        });
    }
}

const parseCobitJson = (jsonStr) => {
    if (!jsonStr) return null;
    try {
        const obj = JSON.parse(jsonStr);
        if (obj && (obj.description || obj.purpose || (obj.practices && obj.practices.length > 0))) {
            return {
                description: obj.description || '',
                purpose: obj.purpose || '',
                practices: obj.practices || []
            };
        }
        return null;
    } catch(e) {
        return null;
    }
};

const cobitObjectivesList = ref([]);

const viewMode = ref('table');

const domainMapping = {
    'Evaluasi, Arahkan, dan Pantau': { letter: 'A', cobitName: 'EDM (Evaluate, Direct and Monitor)' },
    'Penyelarasan, Perencanaan, dan Pengaturan': { letter: 'B', cobitName: 'APO (Align, Plan and Organize)' },
    'Pengembangan, Akuisisi dan Implementasi': { letter: 'C', cobitName: 'BAI (Build, Acquire and Implement)' },
    'Penyerahan, Pemberian Layanan dan Dukungan': { letter: 'D', cobitName: 'DSS (Deliver, Service and Support)' },
    'Memantau, Mengevaluasi dan Menilai': { letter: 'E', cobitName: 'MEA (Monitor, Evaluate and Assess)' }
};

const groupedObjectives = computed(() => {
    const groups = {};
    const domainOrder = [
        'Evaluasi, Arahkan, dan Pantau',
        'Penyelarasan, Perencanaan, dan Pengaturan',
        'Pengembangan, Akuisisi dan Implementasi',
        'Penyerahan, Pemberian Layanan dan Dukungan',
        'Memantau, Mengevaluasi dan Menilai'
    ];

    props.objectives.forEach(obj => {
        const d = obj.domain || 'Lainnya';
        if (!groups[d]) {
            groups[d] = [];
        }
        groups[d].push(obj);
    });

    return Object.keys(groups)
        .sort((a, b) => {
            const idxA = domainOrder.indexOf(a);
            const idxB = domainOrder.indexOf(b);
            if (idxA === -1 && idxB === -1) return a.localeCompare(b);
            if (idxA === -1) return 1;
            if (idxB === -1) return -1;
            return idxA - idxB;
        })
        .map(domain => {
            return {
                domain: domain,
                metadata: domainMapping[domain] || { letter: '-', cobitName: domain },
                objectives: groups[domain]
            };
        });
});

const getCobitName = (cobitObjectiveId) => {
    const found = cobitObjectivesList.value.find(c => c.id === cobitObjectiveId);
    return found ? found.name : '';
};

const isFetchingPractices = ref(false);

const fetchCobitObjectives = async () => {
    try {
        const res = await axios.get('https://cobit2019.divusi.co.id/api/cobit/overview');
        if (res.data && res.data.success) {
            cobitObjectivesList.value = res.data.data
                .filter(item => !item.objective_id.includes('.') && /^[A-Z]{3}\d{2}$/.test(item.objective_id))
                .map(item => ({
                    id: item.objective_id,
                    name: item.objective,
                    description: item.description,
                    purpose: item.purpose
                }));
        }
    } catch (e) {
        console.error("Gagal memuat API COBIT Overview:", e);
    }
};

const handleCobitObjectiveChange = async (event) => {
    const selectedValue = event ? event.target.value : mappingForm.cobit_objective;
    mappingForm.cobit_objective = selectedValue; // sync explicitly just in case

    const selected = cobitObjectivesList.value.find(c => c.id === selectedValue);
    if (selected) {
        isFetchingPractices.value = true;
        try {
            const res = await axios.get(`https://cobit2019.divusi.co.id/api/cobit/roles-matrix?objective_id=${selected.id}`);
            let practices = [];
            if (res.data && res.data.success && res.data.matrix) {
                practices = res.data.matrix.map(m => ({
                    id: String(m.practice_id).replace(/"/g, ''),
                    name: String(m.practice_name).replace(/"/g, ''),
                    description: String(m.practice_description).replace(/"/g, '')
                }));
            }
            mappingForm.description = JSON.stringify({
                description: selected.description || '',
                purpose: selected.purpose || '',
                practices: practices
            });
        } catch (e) {
            console.error("Gagal memuat API COBIT Roles Matrix:", e);
            // Fallback tanpa practices
            mappingForm.description = JSON.stringify({
                description: selected.description || '',
                purpose: selected.purpose || '',
                practices: []
            });
        } finally {
            isFetchingPractices.value = false;
        }
    } else {
        mappingForm.description = ''; // Reset jika tidak ada yang dipilih
    }
};

onMounted(() => {
    fetchCobitObjectives();
});

const page = usePage();

// Success and Error local states to handle notification flashes
const localSuccess = ref(page.props.flash?.success || null);
const localError = ref(page.props.flash?.error || null);

// Watch for inertia flash prop updates
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            localSuccess.value = flash.success;
            setTimeout(() => { localSuccess.value = null; }, 5000);
        }
        if (flash?.error) {
            localError.value = flash.error;
            setTimeout(() => { localError.value = null; }, 5000);
        }
    },
    { deep: true, immediate: true }
);


// Helper to determine Domain name by Objective ID prefix (English fallback)
function getDomainName(id) {
    if (!id) return 'Governance / Management Domain';
    const cleanId = id.trim().toUpperCase();
    if (cleanId.startsWith('EDM')) return 'Evaluate, Direct and Monitor';
    if (cleanId.startsWith('APO')) return 'Align, Plan and Organize';
    if (cleanId.startsWith('BAI')) return 'Build, Acquire and Implement';
    if (cleanId.startsWith('DSS')) return 'Deliver, Service and Support';
    if (cleanId.startsWith('MEA')) return 'Monitor, Evaluate and Assess';
    return 'COBIT Governance Objective';
}

// Indonesian auto-suggestion mapping helper
function getDomainNameInIndonesian(id) {
    if (!id) return '';
    const cleanId = id.trim().toUpperCase();
    if (cleanId.startsWith('EDM')) return 'Evaluasi, Arahan dan Pemantauan (EDM)';
    if (cleanId.startsWith('APO')) return 'Penyelarasan, Perencanaan dan Pengorganisasian (APO)';
    if (cleanId.startsWith('BAI')) return 'Pembangunan, Perolehan dan Penerapan (BAI)';
    if (cleanId.startsWith('DSS')) return 'Pemberian Layanan, Operasional dan Dukungan (DSS)';
    if (cleanId.startsWith('MEA')) return 'Pemantauan, Evaluasi dan Pengukuran (MEA)';
    return '';
}

// ---------------------------------------------------
// OBJECTIVE: CREATE STATE
// ---------------------------------------------------
const isCreatingObjective = ref(false);

const objectiveForm = useForm({
    objective_id: '',
    regulation_id: activeRegulation.value?.id || '',
    domain: '',
    objective: '',
    objective_description: '',
    objective_purpose: '',
});

// Watch activeRegulation changes to update forms' regulation_id
watch(
    () => activeRegulation.value,
    (reg) => {
        if (reg) {
            objectiveForm.regulation_id = reg.id;
        }
    },
    { immediate: true }
);

function startCreateObjective() {
    isCreatingObjective.value = true;
    objectiveForm.reset();
    objectiveForm.regulation_id = activeRegulation.value?.id || '';
    objectiveForm.clearErrors();
    // Scroll smoothly to top form
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ... (cancel logic)

// ---------------------------------------------------
// OBJECTIVE: EDIT & DELETE STATE
// ---------------------------------------------------
const editingObjectiveId = ref(null);
const editObjectiveForm = useForm({
    objective_id: '',
    regulation_id: '',
    domain: '',
    objective: '',
    objective_description: '',
    objective_purpose: '',
});

function startEditObjective(obj) {
    editingObjectiveId.value = obj.objective_id;
    editObjectiveForm.objective_id = obj.objective_id;
    editObjectiveForm.regulation_id = obj.regulation_id || null;
    editObjectiveForm.domain = obj.domain || '';
    editObjectiveForm.objective = obj.objective;
    editObjectiveForm.objective_description = obj.objective_description || '';
    editObjectiveForm.objective_purpose = obj.objective_purpose || '';
    editObjectiveForm.defaults();
    editObjectiveForm.clearErrors();
}

function cancelEditObjective() {
    editingObjectiveId.value = null;
    editObjectiveForm.reset();
    editObjectiveForm.clearErrors();
}

function submitUpdateObjective(id) {
    editObjectiveForm.put(route('policy.objective.update', { objective: id }), {
        preserveScroll: true,
        onSuccess: () => {
            editingObjectiveId.value = null;
            localSuccess.value = 'Kebijakan Khusus berhasil diperbarui.';
        },
        onError: () => {
            localError.value = 'Gagal menyimpan perubahan Kebijakan Khusus.';
        }
    });
}

function deleteObjective(obj) {
    if (confirm(`Apakah Anda yakin ingin menghapus Kebijakan Khusus "${obj.objective_id} - ${obj.objective}" beserta seluruh Butir Kebijakan di dalamnya?`)) {
        router.delete(route('policy.objective.destroy', obj.objective_id), {
            preserveScroll: true,
            onSuccess: () => {
                localSuccess.value = 'Kebijakan Khusus berhasil dihapus.';
            },
            onError: () => {
                localError.value = 'Gagal menghapus Kebijakan Khusus.';
            }
        });
    }
}

// ---------------------------------------------------
// PRACTICE: FORM STATE (ADD & EDIT INLINE)
// ---------------------------------------------------
const activePracticeObjectiveId = ref(null);
const editingPracticeId = ref(null); // stores practice_id when editing, null when creating

const practiceForm = useForm({
    practice_id: '',
    objective_id: '',
    practice_name: '',
    practice_description: '',
});

function startCreatePractice(objId) {
    activePracticeObjectiveId.value = objId;
    editingPracticeId.value = null;
    
    // Automatically suggest practice ID pattern
    const objIdPrefix = objId.trim();
    // Count existing practices for this obj to suggest next index
    const matchedObj = props.objectives.find(o => o.objective_id === objId);
    let nextIndex = 1;
    if (matchedObj && matchedObj.practices) {
        nextIndex = matchedObj.practices.length + 1;
    }
    const suggestedIndex = nextIndex < 10 ? `0${nextIndex}` : nextIndex;

    practiceForm.reset();
    practiceForm.objective_id = objId;
    practiceForm.practice_id = `${objIdPrefix}.${suggestedIndex}`;
    practiceForm.clearErrors();
}

function startEditPractice(objId, practice) {
    activePracticeObjectiveId.value = objId;
    editingPracticeId.value = practice.practice_id;
    
    practiceForm.objective_id = objId;
    practiceForm.practice_id = practice.practice_id;
    practiceForm.practice_name = practice.practice_name || '';
    practiceForm.practice_description = practice.practice_description || '';
    practiceForm.clearErrors();
}

function cancelPracticeForm() {
    activePracticeObjectiveId.value = null;
    editingPracticeId.value = null;
    practiceForm.reset();
    practiceForm.clearErrors();
}

function submitPracticeForm() {
    if (editingPracticeId.value) {
        // Update Action
        practiceForm.put(route('policy.practice.update', { practice: editingPracticeId.value }), {
            preserveScroll: true,
            onSuccess: () => {
                cancelPracticeForm();
            },
            onError: () => {
                localError.value = 'Gagal memperbarui Butir Kebijakan.';
            }
        });
    } else {
        // Store Action
        practiceForm.post(route('policy.practice.store'), {
            preserveScroll: true,
            onSuccess: () => {
                cancelPracticeForm();
            },
            onError: () => {
                localError.value = 'Mohon periksa kesalahan input pada Butir Kebijakan.';
            }
        });
    }
}

function deletePractice(practice) {
    if (confirm(`Apakah Anda yakin ingin menghapus Butir Kebijakan "${practice.practice_id} - ${practice.practice_name || ''}"?`)) {
        router.delete(route('policy.practice.destroy', practice.practice_id), {
            preserveScroll: true,
            onSuccess: () => {
                localSuccess.value = 'Butir Kebijakan berhasil dihapus.';
            },
            onError: () => {
                localError.value = 'Gagal menghapus Butir Kebijakan.';
            }
        });
    }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease-out;
    max-height: 1000px;
}
.slide-down-enter-from,
.slide-down-leave-to {
    max-height: 0;
    opacity: 0;
    padding-top: 0;
    padding-bottom: 0;
    margin-top: 0;
    margin-bottom: 0;
}
</style>
