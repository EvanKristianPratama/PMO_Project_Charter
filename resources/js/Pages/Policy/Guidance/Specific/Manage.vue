<template>
    <UserLayout title="Kelola Kebijakan Khusus">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
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
                        <button
                            @click="startCreateObjective"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Kebijakan Khusus
                        </button>
                    </div>
                </div>
            </section>

            <!-- Regulation Switcher Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-200 dark:border-white/10">
                <span class="text-xs font-black uppercase tracking-wider text-[#821f44] dark:text-pink-400">Pilih Regulasi / Pedoman:</span>
                <select 
                    :value="props.selectedRegulationId || activeRegulation?.id" 
                    @change="onRegulationChange"
                    class="text-xs font-semibold bg-white border border-slate-300 rounded-lg px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:border-white/10 dark:text-white shadow-sm cursor-pointer min-w-[300px]"
                >
                    <option v-for="reg in regulations" :key="reg.id" :value="reg.id">
                        {{ reg.judul }}
                    </option>
                </select>
            </div>

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

            <!-- Inline Objective Creator (Shown when isCreatingObjective is true) -->
            <transition name="slide-down">
                <div v-if="isCreatingObjective" class="max-w-4xl mx-auto overflow-hidden rounded-2xl border-2 border-dashed border-[#821f44]/30 bg-slate-50/50 p-6 dark:bg-white/5">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-white/10 mb-4">
                        <h3 class="text-base font-bold text-[#821f44] dark:text-[#a83262] flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#821f44] animate-ping"></span>
                            Buat Kebijakan Khusus Baru
                        </h3>
                        <button @click="cancelCreateObjective" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- COBIT style Card in Create Mode -->
                    <form @submit.prevent="submitCreateObjective" class="space-y-4 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden dark:border-white/15 dark:bg-[#1a1a1a]">
                        <!-- COBIT Burgundy Header Band -->
                        <div class="bg-[#821f44] p-5 text-white">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs font-semibold tracking-wider opacity-90 mb-3">
                                <div class="flex items-center gap-2">
                                    <span>Kebijakan (Bahasa Indonesia):</span>
                                </div>
                            </div>

                            <div class="mb-4 space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Pilih Regulasi:</label>
                                <select 
                                    v-model="objectiveForm.regulation_id" 
                                    class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                >
                                    <option value="" class="text-slate-900">-- Pilih Regulasi --</option>
                                    <option v-for="reg in regulations" :key="reg.id" :value="reg.id" class="text-slate-900">
                                        {{ reg.judul }}
                                    </option>
                                </select>
                                <div v-if="objectiveForm.errors.regulation_id" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ objectiveForm.errors.regulation_id }}</div>
                            </div>

                            <!-- Input Domain Manual -->
                            <div class="mb-4 space-y-1">
                                <input 
                                    type="text" 
                                    v-model="objectiveForm.domain" 
                                    placeholder="Masukkan nama kebijakan manual (e.g. Evaluasi, Arahan dan Pemantauan)" 
                                    class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                />
                                <div v-if="objectiveForm.errors.domain" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ objectiveForm.errors.domain }}</div>
                            </div>
                            
                            <div class="space-y-3">
                                <label class="block text-xs font-bold uppercase tracking-wider text-white/80">Kebijakan Khusus:</label>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <input 
                                        type="text" 
                                        v-model="objectiveForm.objective_id" 
                                        placeholder="Contoh: EDM01" 
                                        class="w-full sm:w-1/4 bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        required
                                    />
                                    <input 
                                        type="text" 
                                        v-model="objectiveForm.objective" 
                                        placeholder="Nama Kebijakan Khusus (e.g. Ensured Governance Framework Setting...)" 
                                        class="w-full sm:w-3/4 bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        required
                                    />
                                </div>
                                <div v-if="objectiveForm.errors.objective_id" class="text-xs text-red-200 font-medium font-mono bg-red-900/30 p-2 rounded">{{ objectiveForm.errors.objective_id }}</div>
                                <div v-if="objectiveForm.errors.objective" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ objectiveForm.errors.objective }}</div>
                            </div>
                        </div>

                        <!-- Card Content in Form Mode -->
                        <div class="p-6 space-y-4">
                            <!-- Description Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                    Deskripsi
                                </div>
                                <div class="p-4">
                                    <textarea 
                                        v-model="objectiveForm.objective_description" 
                                        rows="3" 
                                        placeholder="Masukkan deskripsi lengkap kebijakan khusus di sini..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Purpose Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                    Tujuan
                                </div>
                                <div class="p-4">
                                    <textarea 
                                        v-model="objectiveForm.objective_purpose" 
                                        rows="3" 
                                        placeholder="Masukkan tujuan dari kebijakan khusus di sini..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Lock Notice for practices -->
                            <div class="bg-amber-50 text-amber-800 border border-amber-200 rounded-lg p-4 text-xs dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-300 flex items-start gap-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <div>
                                    <span class="font-bold">What You See Is What You Get:</span> 
                                    Isi nama dan informasi Kebijakan Khusus utama terlebih dahulu. Setelah Anda menyimpannya ke database, Anda dapat menambahkan baris <strong>Butir Kebijakan</strong> yang berkaitan dengannya.
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                            <button 
                                type="button" 
                                @click="cancelCreateObjective"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                :disabled="objectiveForm.processing"
                            >
                                <span v-if="objectiveForm.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                Simpan Kebijakan Khusus
                            </button>
                        </div>
                    </form>
                </div>
            </transition>

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
                        class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#9c2552] transition"
                    >
                        Buat Sekarang
                    </button>
                </div>

                <!-- Loop through actual objectives -->
                <div 
                    v-for="obj in objectives" 
                    :key="obj.objective_id"
                    class="group relative bg-white rounded-2xl border border-slate-200 shadow-md hover:shadow-xl transition-all duration-300 dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden"
                >
                    <!-- Mode Input / Edit Mode for Objective -->
                    <div v-if="editingObjectiveId === obj.objective_id">
                        <form @submit.prevent="submitUpdateObjective(obj.objective_id)">
                            <!-- Burgundy Header Band -->
                            <div class="bg-[#821f44] p-5 text-white">
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
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                        class="rounded-xl bg-[#821f44] px-4 py-2 text-xs font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
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
                        <div class="bg-[#821f44] p-5 text-white flex flex-col gap-1 transition-all duration-300">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44]">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44]">
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
                            <div class="border border-[#821f44]/25 rounded-lg overflow-hidden dark:border-[#a83262]/30 shadow-sm">
                                <div class="bg-[#821f44]/5 px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-800 dark:bg-white/5 dark:text-slate-300 border-b border-[#821f44]/25 dark:border-white/10 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-[#821f44] dark:text-[#db588c]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                        Butir Kebijakan (mst_practice)
                                    </div>
                                    <button 
                                        @click="startCreatePractice(obj.objective_id)"
                                        class="inline-flex items-center gap-1 rounded bg-[#821f44] px-2.5 py-1 text-[10px] font-bold text-white hover:bg-[#9c2552] transition active:scale-95 shadow-sm shadow-[#821f44]/20"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Tambah Butir Kebijakan
                                    </button>
                                </div>

                                <!-- Practice Editor Form (Inline row inside the card) -->
                                <div v-if="activePracticeObjectiveId === obj.objective_id" class="bg-slate-50 p-4 border-b border-dashed border-[#821f44]/20 dark:bg-[#262626]">
                                    <h4 class="text-xs font-bold text-[#821f44] dark:text-[#a83262] mb-3 flex items-center gap-1.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#821f44] animate-pulse"></span>
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
                                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                                />
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi Butir Kebijakan:</label>
                                            <textarea 
                                                v-model="practiceForm.practice_description" 
                                                rows="2" 
                                                placeholder="Detail aktivitas atau deskripsi butir kebijakan..."
                                                class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg p-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                                class="rounded-lg bg-[#821f44] px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#9c2552] disabled:opacity-60"
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
                                            <span class="inline-block font-mono text-xs font-bold bg-[#821f44]/10 text-[#821f44] px-2 py-0.5 rounded border border-[#821f44]/10 dark:bg-[#a83262]/20 dark:text-[#db588c]">
                                                {{ prac.practice_id }}
                                            </span>
                                        </div>

                                        <!-- Name & Desc Column -->
                                        <div class="flex-1 space-y-1">
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                                {{ prac.practice_name || 'Tanpa Nama Butir Kebijakan' }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                                                {{ prac.practice_description || 'Belum ada deskripsi detail.' }}
                                            </div>
                                        </div>

                                        <!-- Row Actions (Edit / Delete) -->
                                        <div class="shrink-0 flex gap-2 self-end sm:self-start opacity-80 group-hover/row:opacity-100 transition-opacity duration-150">
                                            <button 
                                                @click="startEditPractice(obj.objective_id, prac)"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#821f44] hover:bg-[#821f44]/5 hover:border-[#821f44]/20 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-[#db588c] dark:hover:bg-[#db588c]/10"
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
            </div>

            <!-- Floating Navigation Controls on the Right Side -->
            <div class="fixed right-6 bottom-24 z-[9999] flex flex-col gap-3 pointer-events-none">
                <!-- Add Policy Shortcut Button -->
                <button 
                    @click="startCreateObjective" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-lg border border-[#821f44]/20 transition-all hover:bg-[#9c2552] active:scale-90 hover:shadow-xl"
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
    </UserLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { usePage, useForm, router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

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
        default: null
    }
});

const activeRegulation = computed(() => {
    if (!props.selectedRegulationId) return props.regulations[0] || null;
    return props.regulations.find(r => r.id === props.selectedRegulationId) || null;
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

function onRegulationChange(event) {
    const regId = event.target.value;
    router.visit(route('policy.specific.manage', { regulation_id: regId }));
}

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
