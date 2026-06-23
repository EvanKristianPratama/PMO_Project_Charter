<template>
    <UserLayout title="Kelola Proses Bisnis">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Dashboard Arsitektur</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Proses Bisnis</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('architecture.proses-bisnis.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke Proses Bisnis
                        </Link>
                        <button
                            @click="startCreate"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Proses Bisnis
                        </button>
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

            <!-- Inline Creator Form (Shown when isCreating is true) -->
            <transition name="slide-down">
                <div v-if="isCreating" class="max-w-4xl mx-auto overflow-hidden rounded-2xl border-2 border-dashed border-[#821f44]/30 bg-slate-50/50 p-6 dark:bg-white/5">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-white/10 mb-4">
                        <h3 class="text-base font-bold text-[#821f44] dark:text-[#a83262] flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#821f44] animate-ping"></span>
                            Buat Proses Bisnis Baru
                        </h3>
                        <button @click="cancelCreate" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate" class="space-y-4 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden dark:border-white/15 dark:bg-[#1a1a1a]">
                        <!-- Burgundy Header Band -->
                        <div class="bg-[#821f44] p-5 text-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Organisasi:</label>
                                    <select 
                                        v-model="createForm.organization_id" 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        required
                                    >
                                        <option value="" disabled class="text-slate-900">-- Pilih Organisasi --</option>
                                        <option v-for="org in organizations" :key="org.id" :value="org.id" class="text-slate-900">
                                            {{ org.name }}
                                        </option>
                                    </select>
                                    <div v-if="createForm.errors.organization_id" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ createForm.errors.organization_id }}</div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">No:</label>
                                        <input 
                                            type="text" 
                                            v-model="createForm.no" 
                                            placeholder="Contoh: 1.1" 
                                            class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-white/40"
                                            required
                                        />
                                        <div v-if="createForm.errors.no" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ createForm.errors.no }}</div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Status:</label>
                                        <input 
                                            type="text" 
                                            v-model="createForm.status" 
                                            placeholder="Contoh: Aktif, Draft" 
                                            class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        />
                                        <div v-if="createForm.errors.status" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ createForm.errors.status }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-1">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Judul Proses Bisnis:</label>
                                <input 
                                    type="text" 
                                    v-model="createForm.proses_bisnis" 
                                    placeholder="Masukkan judul atau nama proses bisnis..." 
                                    class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                    required
                                />
                                <div v-if="createForm.errors.proses_bisnis" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ createForm.errors.proses_bisnis }}</div>
                            </div>
                        </div>

                        <!-- Card Content in Form Mode -->
                        <div class="p-6 space-y-4">
                            <!-- Tugas Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                    Tugas / Aktivitas
                                </div>
                                <div class="p-4">
                                    <textarea 
                                        v-model="createForm.tugas" 
                                        rows="3" 
                                        placeholder="Masukkan tugas-tugas proses bisnis di sini..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        required
                                    ></textarea>
                                    <div v-if="createForm.errors.tugas" class="text-xs text-rose-500 font-medium mt-1">{{ createForm.errors.tugas }}</div>
                                </div>
                            </div>

                            <!-- Hasil Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                    Hasil / Output
                                </div>
                                <div class="p-4">
                                    <textarea 
                                        v-model="createForm.hasil" 
                                        rows="3" 
                                        placeholder="Masukkan output atau hasil proses bisnis..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        required
                                    ></textarea>
                                    <div v-if="createForm.errors.hasil" class="text-xs text-rose-500 font-medium mt-1">{{ createForm.errors.hasil }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                            <button 
                                type="button" 
                                @click="cancelCreate"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                :disabled="createForm.processing"
                            >
                                <span v-if="createForm.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                Simpan Proses Bisnis
                            </button>
                        </div>
                    </form>
                </div>
            </transition>

            <!-- Processes List -->
            <div class="space-y-8 max-w-4xl mx-auto">
                <!-- Empty State -->
                <div v-if="prosesBisnis.length === 0 && !isCreating" class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-400 mx-auto mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Belum ada Proses Bisnis</p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Klik tombol di atas untuk membuat Proses Bisnis pertama Anda.
                    </p>
                    <button 
                        @click="startCreate"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#9c2552] transition"
                    >
                        Buat Sekarang
                    </button>
                </div>

                <!-- Loop processes -->
                <div 
                    v-for="item in props.prosesBisnis" 
                    :key="item.id"
                    :id="`item-${item.id}`"
                    class="group relative bg-white rounded-2xl border border-slate-200 shadow-md hover:shadow-xl transition-all duration-300 dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden"
                >
                    <!-- Edit Mode inside Card -->
                    <div v-if="editingId === item.id">
                        <form @submit.prevent="submitUpdate(item.id)">
                            <!-- Burgundy Header Band -->
                            <div class="bg-[#821f44] p-5 text-white">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Organisasi:</label>
                                        <select 
                                            v-model="editForm.organization_id" 
                                            class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                            required
                                        >
                                            <option value="" disabled class="text-slate-900">-- Pilih Organisasi --</option>
                                            <option v-for="org in organizations" :key="org.id" :value="org.id" class="text-slate-900">
                                                {{ org.name }}
                                            </option>
                                        </select>
                                        <div v-if="editForm.errors.organization_id" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editForm.errors.organization_id }}</div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">No:</label>
                                            <input 
                                                type="text" 
                                                v-model="editForm.no" 
                                                class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-white/40"
                                                required
                                            />
                                            <div v-if="editForm.errors.no" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editForm.errors.no }}</div>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Status:</label>
                                            <input 
                                                type="text" 
                                                v-model="editForm.status" 
                                                class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                            />
                                            <div v-if="editForm.errors.status" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editForm.errors.status }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-white/70">Judul Proses Bisnis:</label>
                                    <input 
                                        type="text" 
                                        v-model="editForm.proses_bisnis" 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        required
                                    />
                                    <div v-if="editForm.errors.proses_bisnis" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editForm.errors.proses_bisnis }}</div>
                                </div>
                            </div>

                            <!-- Form Content -->
                            <div class="p-6 space-y-4">
                                <!-- Tugas Box -->
                                <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                    <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                        Tugas / Aktivitas
                                    </div>
                                    <div class="p-4">
                                        <textarea 
                                            v-model="editForm.tugas" 
                                            rows="3" 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                            required
                                        ></textarea>
                                        <div v-if="editForm.errors.tugas" class="text-xs text-rose-500 font-medium mt-1">{{ editForm.errors.tugas }}</div>
                                    </div>
                                </div>

                                <!-- Hasil Box -->
                                <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                    <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                        Hasil / Output
                                    </div>
                                    <div class="p-4">
                                        <textarea 
                                            v-model="editForm.hasil" 
                                            rows="3" 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                            required
                                        ></textarea>
                                        <div v-if="editForm.errors.hasil" class="text-xs text-rose-500 font-medium mt-1">{{ editForm.errors.hasil }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Row -->
                            <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                                <button 
                                    type="button"
                                    @click="deleteItem(item)"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-800 transition dark:text-rose-400 dark:hover:text-rose-300"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Hapus Proses Bisnis
                                </button>
                                
                                <div class="flex gap-3">
                                    <button 
                                        type="button" 
                                        @click="cancelEdit"
                                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        class="rounded-xl bg-[#821f44] px-4 py-2 text-xs font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                        :disabled="editForm.processing"
                                    >
                                        <span v-if="editForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- View Mode (Premium Card) -->
                    <div v-else>
                        <!-- Burgundy Header Band -->
                        <div class="bg-[#821f44] p-5 text-white flex flex-col gap-1 transition-all duration-300">
                            <!-- Quick floating options visible on card group hover -->
                            <div class="absolute right-4 top-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                                <button 
                                    @click="startEdit(item)"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 border border-white/10 hover:bg-white/30 text-white backdrop-blur-md shadow transition-all duration-150 active:scale-90"
                                    title="Edit Proses Bisnis"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteItem(item)"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/80 hover:bg-rose-500 text-white backdrop-blur-md shadow transition-all duration-150 active:scale-90"
                                    title="Hapus Proses Bisnis"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold tracking-wider opacity-90">
                                <span class="bg-white/20 px-2 py-0.5 rounded uppercase font-bold">{{ item.organization?.name || '-' }}</span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold border border-white/20 bg-white/10 text-white">
                                    {{ item.status || 'Draft' }}
                                </span>
                            </div>
                            
                            <h2 class="mt-2 text-lg sm:text-xl font-bold tracking-tight pr-20">
                                Proses Bisnis <span class="font-mono text-yellow-300">#{{ item.no }}</span> — {{ item.proses_bisnis }}
                            </h2>
                        </div>

                        <!-- Card Content in Preview Mode -->
                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Tugas Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10 shadow-sm bg-white dark:bg-[#1f1f1f]">
                                <div class="bg-slate-50 px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Tugas / Aktivitas
                                </div>
                                <div class="p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ item.tugas }}
                                </div>
                            </div>

                            <!-- Hasil Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10 shadow-sm bg-white dark:bg-[#1f1f1f]">
                                <div class="bg-slate-50 px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 12.408l-2.25-2.25m0 0l2.25-2.25m-2.25 2.25h10.5" />
                                    </svg>
                                    Hasil / Output
                                </div>
                                <div class="p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ item.hasil }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Navigation Controls on the Right Side -->
            <div class="fixed right-6 bottom-24 z-[9999] flex flex-col gap-3 pointer-events-none">
                <!-- Add Shortcut Button -->
                <button 
                    @click="startCreate" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-lg border border-[#821f44]/20 transition-all hover:bg-[#9c2552] active:scale-90 hover:shadow-xl"
                    title="Tambah Proses Bisnis"
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
import { ref, watch, onMounted } from 'vue';
import { usePage, useForm, router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';

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
    prosesBisnis: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const localSuccess = ref(page.props.flash?.success || null);
const localError = ref(page.props.flash?.error || null);

// Watch flash message
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

// ---------------------------------------------------
// CREATE FORM STATE
// ---------------------------------------------------
const isCreating = ref(false);

const createForm = useForm({
    organization_id: '',
    no: '',
    proses_bisnis: '',
    tugas: '',
    hasil: '',
    status: '',
});

function startCreate() {
    isCreating.value = true;
    createForm.reset();
    createForm.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelCreate() {
    isCreating.value = false;
    createForm.reset();
    createForm.clearErrors();
}

function submitCreate() {
    createForm.post(route('architecture.proses-bisnis.store'), {
        onSuccess: () => {
            isCreating.value = false;
            createForm.reset();
            Swal.fire({
                title: 'Berhasil!',
                text: 'Proses Bisnis berhasil ditambahkan.',
                icon: 'success',
                confirmButtonColor: '#821f44',
                timer: 2000,
                timerProgressBar: true
            });
        },
        onError: () => {
            localError.value = 'Gagal menambahkan Proses Bisnis. Silakan periksa input Anda.';
        }
    });
}

// ---------------------------------------------------
// EDIT FORM STATE
// ---------------------------------------------------
const editingId = ref(null);

const editForm = useForm({
    organization_id: '',
    no: '',
    proses_bisnis: '',
    tugas: '',
    hasil: '',
    status: '',
});

function startEdit(item) {
    editingId.value = item.id;
    editForm.organization_id = item.organization_id;
    editForm.no = item.no;
    editForm.proses_bisnis = item.proses_bisnis;
    editForm.tugas = item.tugas;
    editForm.hasil = item.hasil;
    editForm.status = item.status || '';
    editForm.clearErrors();
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
}

function submitUpdate(id) {
    editForm.put(route('architecture.proses-bisnis.update', id), {
        onSuccess: () => {
            editingId.value = null;
            Swal.fire({
                title: 'Berhasil!',
                text: 'Proses Bisnis berhasil diperbarui.',
                icon: 'success',
                confirmButtonColor: '#821f44',
                timer: 2000,
                timerProgressBar: true
            });
        },
        onError: () => {
            localError.value = 'Gagal menyimpan perubahan. Silakan periksa input Anda.';
        }
    });
}

// ---------------------------------------------------
// DELETE FUNCTION
// ---------------------------------------------------
function deleteItem(item) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Proses Bisnis: "${item.proses_bisnis}". Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('architecture.proses-bisnis.destroy', item.id), {
                onSuccess: () => {
                    editingId.value = null;
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Proses Bisnis berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#821f44',
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal menghapus data.',
                        icon: 'error',
                        confirmButtonColor: '#821f44'
                    });
                }
            });
        }
    });
}

// ---------------------------------------------------
// ON MOUNTED: AUTO-EDIT DETECTOR
// ---------------------------------------------------
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const editIdFromUrl = urlParams.get('edit_id');
    if (editIdFromUrl) {
        const matched = props.prosesBisnis.find(p => p.id == editIdFromUrl);
        if (matched) {
            startEdit(matched);
            // Scroll to the editing card
            setTimeout(() => {
                const element = document.getElementById(`item-${editIdFromUrl}`);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 300);
        }
    }
});
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
