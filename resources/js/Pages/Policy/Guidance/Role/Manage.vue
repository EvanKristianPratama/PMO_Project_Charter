<template>
    <ModulLayout title="Kelola Peran & Tanggung Jawab">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Peran & Tanggung Jawab</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('policy.roles.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke Dokumen
                        </Link>

                        <!-- Go to Master Responsible CRUD page -->
                        <Link
                            :href="route('policy.responsible.manage')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H3.75A2.25 2.25 0 001.5 4.5v15a2.25 2.25 0 002.25 2.25h12.75a2.25 2.25 0 002.25-2.25V14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15.75h6m-6-4.5h6m-6-4.5h3.75" />
                            </svg>
                            Kelola Master Responsible
                        </Link>
                        <button
                            @click="startCreateRole"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Role Baru
                        </button>
                    </div>
                </div>
            </section>

            <!-- Mode Switcher -->
            <div class="flex justify-center">
                <div class="inline-flex rounded-xl bg-slate-100 p-1 dark:bg-white/5 border border-slate-200/55 dark:border-white/10 shadow-sm">
                    <button
                        @click="activeInputMode = 'role'"
                        class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-200"
                        :class="[
                            activeInputMode === 'role'
                                ? 'bg-white text-[#821f44] shadow-md shadow-[#821f44]/5 dark:bg-[#262626] dark:text-white'
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Mode Role</span>
                    </button>
                    <button
                        @click="activeInputMode = 'responsible'"
                        class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-200"
                        :class="[
                            activeInputMode === 'responsible'
                                ? 'bg-white text-blue-700 shadow-md shadow-blue-500/5 dark:bg-[#262626] dark:text-blue-300'
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                        </svg>
                        <span>Mode Butir Kebijakan</span>
                        <span class="ml-1 rounded-full bg-blue-600/10 px-1.5 py-0.5 text-[10px] font-extrabold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                            {{ responsibles.length }}
                        </span>
                    </button>
                </div>
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

            <!-- Inline Role Creator (Shown when isCreatingRole is true) -->
            <transition name="slide-down">
                <div v-if="isCreatingRole" class="max-w-4xl mx-auto overflow-hidden rounded-2xl border-2 border-dashed border-[#821f44]/30 bg-slate-50/50 p-6 dark:bg-white/5">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3 dark:border-white/10 mb-4">
                        <h3 class="text-base font-bold text-[#821f44] dark:text-[#a83262] flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#821f44] animate-ping"></span>
                            Buat Role Baru
                        </h3>
                        <button @click="cancelCreateRole" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreateRole" class="space-y-4 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden dark:border-white/15 dark:bg-[#1a1a1a]">
                        <div class="bg-[#821f44] p-5 text-white">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-white/80">Nama Role:</label>
                                <input 
                                    type="text" 
                                    v-model="roleForm.name" 
                                    placeholder="Contoh: Dewan Direksi (Board) atau Executive Committee" 
                                    class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                    required
                                />
                                <div v-if="roleForm.errors.name" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ roleForm.errors.name }}</div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Description Box -->
                            <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                    Deskripsi Role
                                </div>
                                <div class="p-4">
                                    <textarea 
                                        v-model="roleForm.description" 
                                        rows="3" 
                                        placeholder="Masukkan deskripsi detail wewenang/tanggung jawab umum role di sini..." 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="bg-amber-50 text-amber-800 border border-amber-200 rounded-lg p-4 text-xs dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-300 flex items-start gap-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <div>
                                    <span class="font-bold">Info Tanggung Jawab (Responsibility):</span> 
                                    Buat dan simpan data Role terlebih dahulu. Setelah disimpan, Anda dapat mulai memasukkan daftar tanggung jawab (responsibilities) secara terperinci untuk role tersebut.
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                            <button 
                                type="button" 
                                @click="cancelCreateRole"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                :disabled="roleForm.processing"
                            >
                                <span v-if="roleForm.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                Simpan Role
                            </button>
                        </div>
                    </form>
                </div>
            </transition>

            <!-- Roles List & Responsibilities CRUD -->
            <div v-show="activeInputMode === 'role'" class="space-y-8">
                <!-- If empty -->
                <div v-if="roles.length === 0 && !isCreatingRole" class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-400 mx-auto mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Belum ada Data Peran & Tanggung Jawab</p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Klik tombol di atas untuk membuat Role dan daftar Responsibilities pertama Anda.
                    </p>
                    <button 
                        @click="startCreateRole"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3.5 py-2 text-xs font-bold text-white hover:bg-[#9c2552] transition"
                    >
                        Buat Sekarang
                    </button>
                </div>

                <!-- Loop through actual roles -->
                <div 
                    v-for="role in roles" 
                    :key="role.id"
                    class="group relative bg-white rounded-2xl border border-slate-200 shadow-md hover:shadow-xl transition-all duration-300 dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden"
                >
                    <!-- Edit Mode for Role -->
                    <div v-if="editingRoleId === role.id">
                        <form @submit.prevent="submitUpdateRole(role.id)">
                            <div class="bg-[#821f44] p-5 text-white">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-white/70">Nama Role:</label>
                                    <input 
                                        type="text" 
                                        v-model="editRoleForm.name" 
                                        class="w-full bg-white/10 text-white placeholder-white/40 border border-white/20 rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/40"
                                        required
                                    />
                                    <div v-if="editRoleForm.errors.name" class="text-xs text-red-200 font-medium bg-red-900/30 p-2 rounded">{{ editRoleForm.errors.name }}</div>
                                </div>
                            </div>

                            <div class="p-6 space-y-4">
                                <div class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10">
                                    <div class="bg-slate-100 px-4 py-2 text-xs font-bold uppercase text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10">
                                        Deskripsi Role
                                    </div>
                                    <div class="p-4">
                                        <textarea 
                                            v-model="editRoleForm.description" 
                                            rows="3" 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 px-6 py-4 flex justify-between items-center border-t border-slate-100 dark:bg-black/20 dark:border-white/5">
                                <button 
                                    type="button"
                                    @click="deleteRole(role)"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-800 transition dark:text-rose-400 dark:hover:text-rose-300"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Hapus Role
                                </button>
                                
                                <div class="flex gap-3">
                                    <button 
                                        type="button" 
                                        @click="cancelEditRole"
                                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        class="rounded-xl bg-[#821f44] px-4 py-2 text-xs font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                        :disabled="editRoleForm.processing"
                                    >
                                        <span v-if="editRoleForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Preview Mode for Role -->
                    <div v-else>
                        <div class="bg-[#821f44] p-5 text-white flex flex-col gap-1 relative">
                            <div class="absolute right-4 top-4 opacity-85 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                                <button 
                                    @click="startEditRole(role)"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 border border-white/10 hover:bg-white/30 text-white backdrop-blur-md shadow transition-all duration-150 active:scale-90"
                                    title="Edit Role"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteRole(role)"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/80 hover:bg-rose-500 text-white backdrop-blur-md shadow transition-all duration-150 active:scale-90"
                                    title="Hapus Role"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                            
                            <h2 class="text-lg sm:text-xl font-bold tracking-tight pr-24">
                                Role: {{ role.name }}
                            </h2>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Description Box -->
                            <div v-if="role.description" class="border border-slate-200 rounded-lg overflow-hidden dark:border-white/10 shadow-sm bg-white dark:bg-[#1f1f1f]">
                                <div class="bg-slate-50 px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#821f44]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 11.517 1.361l-.04.02a.75.75 0 01-.518-1.361zM12.75 9.75l-.04.02a.75.75 0 11-.517-1.36l.04-.02a.75.75 0 01.517 1.36zM12 21a9 9 0 100-18 9 9 0 000 18z" />
                                    </svg>
                                    Deskripsi Role
                                </div>
                                <div class="p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ role.description }}
                                </div>
                            </div>

                            <!-- Responsibilities Grid Layout (Manual on Left, Master Mapping on Right) -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- LEFT COLUMN: Manual Responsibilities -->
                                <div class="border border-[#821f44]/25 rounded-lg overflow-hidden dark:border-[#a83262]/30 shadow-sm bg-white dark:bg-[#1a1a1a]">
                                    <div class="bg-[#821f44]/5 px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-800 dark:bg-white/5 dark:text-slate-300 border-b border-[#821f44]/25 dark:border-white/10 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-[#821f44] dark:text-[#db588c]">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Daftar Tanggung Jawab (Responsibility)
                                        </div>
                                        <button 
                                            @click="startCreateResponsibility(role.id)"
                                            class="inline-flex items-center gap-1 rounded bg-[#821f44] px-2.5 py-1 text-[10px] font-bold text-white hover:bg-[#9c2552] transition active:scale-95 shadow-sm shadow-[#821f44]/20"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Tambah Tanggung Jawab
                                        </button>
                                    </div>

                                    <!-- Responsibility Editor Form (Inline inside the card) -->
                                    <div v-if="activeRespRoleId === role.id" class="bg-slate-50 p-4 border-b border-dashed border-[#821f44]/20 dark:bg-[#262626]">
                                        <h4 class="text-xs font-bold text-[#821f44] dark:text-[#a83262] mb-3 flex items-center gap-1.5">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#821f44] animate-pulse"></span>
                                            {{ editingRespId ? 'Edit Tanggung Jawab' : 'Tambah Tanggung Jawab Baru' }}
                                        </h4>
                                        
                                        <form @submit.prevent="submitResponsibilityForm" class="space-y-3">
                                            <div class="space-y-1">
                                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Isi Tanggung Jawab:</label>
                                                <textarea 
                                                    v-model="respForm.content" 
                                                    rows="2" 
                                                    placeholder="Contoh: Menetapkan IT Governance atau Memonitor perkembangan teknologi..."
                                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg p-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                                    required
                                                ></textarea>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button 
                                                    type="button" 
                                                    @click="cancelResponsibilityForm"
                                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a]"
                                                >
                                                    Batal
                                                </button>
                                                <button 
                                                    type="submit" 
                                                    class="rounded-lg bg-[#821f44] px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#9c2552] disabled:opacity-60"
                                                    :disabled="respForm.processing"
                                                >
                                                    <span v-if="respForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                                    Simpan Tanggung Jawab
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Responsibilities Table / List -->
                                    <div class="divide-y divide-slate-100 dark:divide-white/5">
                                        <div v-if="!role.responsibilities || role.responsibilities.length === 0" class="p-4 text-center text-xs text-slate-400 dark:text-slate-500">
                                            Belum ada tanggung jawab yang didaftarkan. Klik "+ Tambah Tanggung Jawab" di atas untuk memasukkan data.
                                        </div>

                                        <div 
                                            v-for="(resp, respIdx) in role.responsibilities" 
                                            :key="resp.id"
                                            class="p-4 hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 flex gap-3 items-start group/row"
                                        >
                                            <div class="shrink-0 font-bold text-xs bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">
                                                {{ respIdx + 1 }}
                                            </div>
                                            <div class="flex-1 text-xs text-slate-800 dark:text-slate-200 font-serif leading-relaxed">
                                                {{ resp.content }}
                                            </div>
                                            <div class="shrink-0 flex gap-2 opacity-80 group-hover/row:opacity-100 transition-opacity duration-150">
                                                <button 
                                                    @click="startEditResponsibility(role.id, resp)"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#821f44] hover:bg-[#821f44]/5 hover:border-[#821f44]/20 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400"
                                                    title="Edit Tanggung Jawab"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                                <button 
                                                    @click="deleteResponsibility(resp)"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400"
                                                    title="Hapus Tanggung Jawab"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT COLUMN: Mapped Master Responsibilities -->
                                <div class="border border-blue-500/25 rounded-lg overflow-hidden dark:border-blue-500/30 shadow-sm bg-white dark:bg-[#1a1a1a]">
                                    <div class="bg-blue-500/5 px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-800 dark:bg-white/5 dark:text-slate-300 border-b border-blue-500/25 dark:border-white/10 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-blue-800 dark:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                            </svg>
                                            Pemetaan Tanggung Jawab (Master Responsible)
                                        </div>
                                        <button 
                                            @click="startAddMapping(role.id)"
                                            class="inline-flex items-center gap-1 rounded bg-blue-600 px-2.5 py-1 text-[10px] font-bold text-white hover:bg-blue-700 transition active:scale-95 shadow-sm shadow-blue-600/20"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Petakan Responsible
                                        </button>
                                    </div>

                                    <!-- Add Mapping Inline Form -->
                                    <div v-if="activeMappingRoleId === role.id" class="bg-slate-50 p-4 border-b border-dashed border-blue-500/20 dark:bg-[#262626]">
                                        <h4 class="text-xs font-bold text-blue-800 dark:text-blue-400 mb-3 flex items-center gap-1.5">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                            Petakan Master Responsible
                                        </h4>
                                        <form @submit.prevent="submitMappingForm" class="space-y-3">
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Cari & Pilih Master Responsible:</label>
                                                
                                                <!-- Search input field with search icon -->
                                                <div class="relative">
                                                    <input 
                                                        type="text" 
                                                        v-model="mappingSearchQuery" 
                                                        placeholder="Ketik kata kunci untuk mencari..." 
                                                        class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg pl-8 pr-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                                    />
                                                    <div class="absolute left-2.5 top-2.5 text-slate-400">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <!-- Selection Count -->
                                                <div v-if="getAvailableResponsiblesForRole(role).length > 0" class="flex items-center justify-between mt-1 mb-2 bg-slate-100/50 dark:bg-black/10 px-2.5 py-1.5 rounded-md">
                                                    <span class="text-[10px] text-slate-500 dark:text-slate-400">
                                                        Terpilih: <strong class="text-blue-600 dark:text-blue-400 font-extrabold">{{ mappingForm.responsible_ids.length }}</strong> item
                                                    </span>
                                                </div>
                                                
                                                <!-- Filtered Scrollable List of Responsibles -->
                                                <div v-if="filteredSearchResponsibles(role).length > 0" class="max-h-40 overflow-y-auto border border-slate-200 dark:border-white/10 rounded-lg divide-y divide-slate-100 dark:divide-white/5 bg-white dark:bg-[#1a1a1a] shadow-inner">
                                                    <button 
                                                        v-for="resp in filteredSearchResponsibles(role)" 
                                                        :key="resp.id"
                                                        type="button"
                                                        @click="toggleResponsibleForMapping(resp.id)"
                                                        class="w-full text-left px-3 py-2.5 text-xs text-slate-700 hover:bg-blue-50/50 dark:text-slate-300 dark:hover:bg-blue-950/30 transition flex items-center justify-between group/item"
                                                        :class="{'bg-blue-50/80 dark:bg-blue-950/20 font-bold text-blue-700 dark:text-blue-400': mappingForm.responsible_ids.includes(resp.id)}"
                                                    >
                                                        <div class="flex items-center gap-2.5 flex-1 pr-4">
                                                            <!-- Modern Checkbox indicator -->
                                                            <div class="w-4 h-4 rounded border flex items-center justify-center shrink-0 transition"
                                                                :class="{
                                                                    'bg-blue-600 border-blue-600 text-white dark:bg-blue-500 dark:border-blue-500': mappingForm.responsible_ids.includes(resp.id),
                                                                    'border-slate-300 bg-white group-hover/item:border-blue-400 dark:border-white/20 dark:bg-transparent': !mappingForm.responsible_ids.includes(resp.id)
                                                                }"
                                                            >
                                                                <svg v-if="mappingForm.responsible_ids.includes(resp.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z" clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                            <span class="leading-snug">{{ resp.responsible }}</span>
                                                        </div>
                                                    </button>
                                                </div>
                                                <div v-else class="p-3 text-center text-xs text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-white/10 rounded-lg bg-slate-50/50 dark:bg-black/10">
                                                    Tidak ada master responsible yang cocok atau belum dipetakan.
                                                </div>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-2">
                                                <button 
                                                    type="button" 
                                                    @click="cancelMappingForm"
                                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a]"
                                                >
                                                    Batal
                                                </button>
                                                <button 
                                                    type="submit" 
                                                    class="rounded-lg bg-blue-600 px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-blue-700 disabled:opacity-60 transition active:scale-95"
                                                    :disabled="mappingForm.processing || mappingForm.responsible_ids.length === 0"
                                                >
                                                    <span v-if="mappingForm.processing" class="inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                                    Simpan Pemetaan
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Mapped Responsibles List -->
                                    <div class="divide-y divide-slate-100 dark:divide-white/5">
                                        <div v-if="!role.mapped_responsibles || role.mapped_responsibles.length === 0" class="p-4 text-center text-xs text-slate-400 dark:text-slate-500">
                                            Belum ada master responsible yang dipetakan. Klik "+ Petakan Responsible" di atas.
                                        </div>

                                        <div 
                                            v-for="(mr, mrIdx) in role.mapped_responsibles" 
                                            :key="mr.id"
                                            class="p-4 hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 flex gap-3 items-start group/row"
                                        >
                                            <div class="shrink-0 font-bold text-xs bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">
                                                {{ mrIdx + 1 }}
                                            </div>
                                            <div class="flex-1 text-xs text-slate-800 dark:text-slate-200 font-sans leading-relaxed">
                                                {{ mr.responsible }}
                                            </div>
                                            <div class="shrink-0 flex gap-2 opacity-80 group-hover/row:opacity-100 transition-opacity duration-150">
                                                <button 
                                                    @click="deleteMapping(role.id, mr.id)"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition-all duration-150 active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400"
                                                    title="Hapus Pemetaan"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
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
            </div>

            <!-- =============================================
                 MODE BUTIR KEBIJAKAN (REVERSED INPUT MODE)
                 ============================================= -->
            <div v-show="activeInputMode === 'responsible'" class="space-y-4">

                <!-- Info Banner -->
                <div class="rounded-2xl bg-blue-500/5 p-5 border border-blue-500/20 dark:bg-blue-500/10 dark:border-blue-500/30 flex items-start gap-3.5 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 3M21 7.5H7.5" />
                    </svg>
                    <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1">
                        <p class="font-extrabold uppercase tracking-wider text-blue-700 dark:text-blue-300">Mode Input Terbalik — dari Butir Kebijakan</p>
                        <p>Pilih satu butir kebijakan/tanggung jawab, lalu centang role-role mana saja yang bertanggung jawab atas butir tersebut. Perubahan langsung tersimpan ke database.</p>
                    </div>
                </div>

                <!-- Search Bar for Responsibles -->
                <div class="relative">
                    <input
                        type="text"
                        v-model="respSearchQuery"
                        placeholder="Cari butir kebijakan / tanggung jawab..."
                        class="w-full bg-white text-slate-800 border border-slate-300 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:bg-[#1a1a1a] dark:text-white dark:border-white/10 shadow-sm"
                    />
                    <div class="absolute left-3.5 top-3.5 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                        </svg>
                    </div>
                    <div class="absolute right-3.5 top-3 text-xs text-slate-400 font-semibold">
                        {{ filteredResponsiblesForMode.length }} butir
                    </div>
                </div>

                <!-- Responsibles List -->
                <div class="space-y-3">
                    <div v-if="filteredResponsiblesForMode.length === 0" class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-white/10 dark:bg-[#171717]">
                        <p class="text-sm text-slate-400">Tidak ada butir kebijakan yang ditemukan.</p>
                    </div>

                    <div
                        v-for="(resp, rIdx) in filteredResponsiblesForMode"
                        :key="resp.id"
                        class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden"
                    >
                        <!-- Header row: butir number + text + expand btn -->
                        <button
                            type="button"
                            @click="toggleExpandResp(resp.id)"
                            class="w-full text-left flex items-start gap-4 px-5 py-4 hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
                        >
                            <!-- Index number -->
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-blue-600/10 dark:bg-blue-500/20 flex items-center justify-center font-extrabold text-blue-700 dark:text-blue-300 text-xs mt-0.5">
                                {{ rIdx + 1 }}
                            </div>

                            <!-- Responsible text -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white leading-relaxed">
                                    {{ resp.responsible }}
                                </p>
                                <!-- Mapped role badges -->
                                <div class="flex flex-wrap gap-1.5 mt-2" v-if="resp.mapped_roles && resp.mapped_roles.length > 0">
                                    <span
                                        v-for="mr in resp.mapped_roles"
                                        :key="mr.id"
                                        class="inline-flex items-center rounded-full bg-blue-600/10 dark:bg-blue-500/20 px-2.5 py-0.5 text-[10px] font-bold text-blue-700 dark:text-blue-300"
                                    >
                                        {{ mr.name }}
                                    </span>
                                </div>
                                <p v-else class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 italic">Belum ada role yang dipetakan</p>
                            </div>

                            <!-- Toggle chevron -->
                            <div class="shrink-0 flex items-center gap-2 self-center">
                                <span class="text-[10px] font-bold rounded-full px-2 py-0.5" :class="(resp.mapped_roles || []).length > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500'">
                                    {{ (resp.mapped_roles || []).length }} role
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="expandedRespId === resp.id ? 'rotate-180' : ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </button>

                        <!-- Expanded: Role checkboxes -->
                        <div v-show="expandedRespId === resp.id" class="border-t border-slate-200 dark:border-white/10 bg-slate-50/60 dark:bg-black/20">
                            <div class="p-5">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-300 mb-3">
                                    Pilih Role yang Bertanggung Jawab:
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    <button
                                        v-for="role in roles"
                                        :key="role.id"
                                        type="button"
                                        @click="toggleRoleMappingForResp(resp, role)"
                                        :disabled="reverseMappingLoading === `${resp.id}-${role.id}`"
                                        class="flex items-center gap-3 rounded-xl border px-3 py-2.5 text-xs text-left transition-all duration-150 hover:shadow-sm active:scale-95 disabled:opacity-50"
                                        :class="isRoleMappedToResp(resp, role.id)
                                            ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-600/20 dark:bg-blue-600 dark:border-blue-600'
                                            : 'bg-white border-slate-200 text-slate-700 hover:border-blue-300 hover:bg-blue-50/50 dark:bg-[#1a1a1a] dark:border-white/10 dark:text-slate-300'"
                                    >
                                        <!-- Spinner or check icon -->
                                        <span v-if="reverseMappingLoading === `${resp.id}-${role.id}`" class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin shrink-0"></span>
                                        <span v-else class="w-4 h-4 rounded border flex items-center justify-center shrink-0 transition"
                                            :class="isRoleMappedToResp(resp, role.id) ? 'bg-white/20 border-white/50' : 'border-slate-300 dark:border-white/20'"
                                        >
                                            <svg v-if="isRoleMappedToResp(resp, role.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <span class="font-semibold leading-snug">{{ role.name }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Navigation Controls on the Right Side -->
            <div class="fixed right-6 bottom-24 z-[9999] flex flex-col gap-3 pointer-events-none">
                <button 
                    @click="startCreateRole" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-lg border border-[#821f44]/20 transition-all hover:bg-[#9c2552] active:scale-90 hover:shadow-xl"
                    title="Tambah Role Baru"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>

                <button 
                    @click="scrollToTop" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Kembali ke Atas"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                    </svg>
                </button>

                <button 
                    @click="scrollToBottom" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                    title="Ke Paling Bawah"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                    </svg>
                </button>

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
import { ref, computed, watch } from 'vue';
import { usePage, useForm, router, Link } from '@inertiajs/vue3';
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
    roles: {
        type: Array,
        required: true,
    },
    responsibles: {
        type: Array,
        required: true,
    },
});

const page = usePage();

// Active input mode: 'role' (normal) | 'responsible' (reversed)
const activeInputMode = ref('role');

// Reversed mode: which responsible item is expanded
const expandedRespId = ref(null);

// Reversed mode: search filter for responsibles
const respSearchQuery = ref('');

// Reversed mode: loading state key (e.g. "respId-roleId")
const reverseMappingLoading = ref(null);

// Filtered responsibles list for reversed mode
const filteredResponsiblesForMode = computed(() => {
    if (!respSearchQuery.value) return props.responsibles;
    const q = respSearchQuery.value.toLowerCase();
    return props.responsibles.filter(r => r.responsible.toLowerCase().includes(q));
});

function toggleExpandResp(respId) {
    expandedRespId.value = expandedRespId.value === respId ? null : respId;
}

function isRoleMappedToResp(resp, roleId) {
    if (!resp.mapped_roles) return false;
    return resp.mapped_roles.some(r => r.id === roleId);
}

function toggleRoleMappingForResp(resp, role) {
    const alreadyMapped = isRoleMappedToResp(resp, role.id);
    const loadingKey = `${resp.id}-${role.id}`;
    reverseMappingLoading.value = loadingKey;

    if (alreadyMapped) {
        // Remove mapping
        router.delete(route('policy.roles.mapped-responsible.destroy', { roleId: role.id, responsibleId: resp.id }), {
            preserveScroll: true,
            onSuccess: () => {
                reverseMappingLoading.value = null;
                localSuccess.value = `Pemetaan "${role.name}" → "${resp.responsible}" berhasil dihapus.`;
            },
            onError: () => {
                reverseMappingLoading.value = null;
                localError.value = 'Gagal menghapus pemetaan.';
            }
        });
    } else {
        // Add mapping
        router.post(route('policy.roles.mapped-responsible.store'), {
            role_id: role.id,
            responsible_ids: [resp.id],
        }, {
            preserveScroll: true,
            onSuccess: () => {
                reverseMappingLoading.value = null;
                localSuccess.value = `Pemetaan "${role.name}" → "${resp.responsible}" berhasil ditambahkan.`;
            },
            onError: () => {
                reverseMappingLoading.value = null;
                localError.value = 'Gagal menambahkan pemetaan.';
            }
        });
    }
}

// Alert notifications
const localSuccess = ref(page.props.flash?.success || null);
const localError = ref(page.props.flash?.error || null);

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
// ROLE: CREATE FORM STATE
// ---------------------------------------------------
const isCreatingRole = ref(false);
const roleForm = useForm({
    name: '',
    description: '',
});

function startCreateRole() {
    isCreatingRole.value = true;
    roleForm.reset();
    roleForm.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelCreateRole() {
    isCreatingRole.value = false;
    roleForm.reset();
    roleForm.clearErrors();
}

function submitCreateRole() {
    roleForm.post(route('policy.roles.role.store'), {
        onSuccess: () => {
            isCreatingRole.value = false;
            roleForm.reset();
            localSuccess.value = 'Role berhasil dibuat!';
        },
        onError: () => {
            localError.value = 'Mohon periksa kesalahan input Anda.';
        }
    });
}

// ---------------------------------------------------
// ROLE: EDIT & DELETE STATE
// ---------------------------------------------------
const editingRoleId = ref(null);
const editRoleForm = useForm({
    name: '',
    description: '',
});

function startEditRole(role) {
    editingRoleId.value = role.id;
    editRoleForm.name = role.name;
    editRoleForm.description = role.description || '';
    editRoleForm.clearErrors();
}

function cancelEditRole() {
    editingRoleId.value = null;
    editRoleForm.reset();
    editRoleForm.clearErrors();
}

function submitUpdateRole(id) {
    editRoleForm.put(route('policy.roles.role.update', id), {
        preserveScroll: true,
        onSuccess: () => {
            editingRoleId.value = null;
            editRoleForm.reset();
            localSuccess.value = 'Role berhasil diperbarui!';
        },
        onError: () => {
            localError.value = 'Gagal menyimpan perubahan Role.';
        }
    });
}

function deleteRole(role) {
    if (confirm(`Apakah Anda yakin ingin menghapus Role "${role.name}" beserta seluruh daftar Tanggung Jawab di dalamnya?`)) {
        router.delete(route('policy.roles.role.destroy', role.id), {
            preserveScroll: true,
            onSuccess: () => {
                localSuccess.value = 'Role berhasil dihapus.';
            },
            onError: () => {
                localError.value = 'Gagal menghapus Role.';
            }
        });
    }
}

// ---------------------------------------------------
// RESPONSIBILITY: INLINE FORM STATE
// ---------------------------------------------------
const activeRespRoleId = ref(null);
const editingRespId = ref(null); // ID responsibility if editing, null if creating

const respForm = useForm({
    role_id: '',
    content: '',
});

function startCreateResponsibility(roleId) {
    activeRespRoleId.value = roleId;
    editingRespId.value = null;
    
    respForm.reset();
    respForm.role_id = roleId;
    respForm.clearErrors();
}

function startEditResponsibility(roleId, responsibility) {
    activeRespRoleId.value = roleId;
    editingRespId.value = responsibility.id;
    
    respForm.role_id = roleId;
    respForm.content = responsibility.content;
    respForm.clearErrors();
}

function cancelResponsibilityForm() {
    activeRespRoleId.value = null;
    editingRespId.value = null;
    respForm.reset();
    respForm.clearErrors();
}

function submitResponsibilityForm() {
    if (editingRespId.value) {
        // Update mode
        respForm.put(route('policy.roles.responsibility.update', editingRespId.value), {
            preserveScroll: true,
            onSuccess: () => {
                activeRespRoleId.value = null;
                editingRespId.value = null;
                respForm.reset();
                localSuccess.value = 'Tanggung Jawab berhasil diperbarui!';
            },
            onError: () => {
                localError.value = 'Gagal menyimpan perubahan Tanggung Jawab.';
            }
        });
    } else {
        // Create mode
        respForm.post(route('policy.roles.responsibility.store'), {
            preserveScroll: true,
            onSuccess: () => {
                activeRespRoleId.value = null;
                respForm.reset();
                localSuccess.value = 'Tanggung Jawab berhasil ditambahkan!';
            },
            onError: () => {
                localError.value = 'Gagal menambahkan Tanggung Jawab baru.';
            }
        });
    }
}

function deleteResponsibility(resp) {
    if (confirm('Apakah Anda yakin ingin menghapus tanggung jawab ini?')) {
        router.delete(route('policy.roles.responsibility.destroy', resp.id), {
            preserveScroll: true,
            onSuccess: () => {
                localSuccess.value = 'Tanggung Jawab berhasil dihapus.';
            },
            onError: () => {
                localError.value = 'Gagal menghapus Tanggung Jawab.';
            }
        });
    }
}

// ---------------------------------------------------
// MAPPING: MASTER RESPONSIBLE TO ROLE STATE
// ---------------------------------------------------
const activeMappingRoleId = ref(null);
const mappingSearchQuery = ref('');
const mappingForm = useForm({
    role_id: '',
    responsible_id: '',
    responsible_ids: [],
});

function startAddMapping(roleId) {
    activeMappingRoleId.value = roleId;
    mappingSearchQuery.value = '';
    mappingForm.reset();
    mappingForm.role_id = roleId;
    mappingForm.responsible_ids = [];
    mappingForm.clearErrors();
}

function cancelMappingForm() {
    activeMappingRoleId.value = null;
    mappingSearchQuery.value = '';
    mappingForm.reset();
    mappingForm.clearErrors();
}

function getAvailableResponsiblesForRole(role) {
    const mappedIds = (role.mapped_responsibles || []).map(mr => mr.id);
    return props.responsibles.filter(resp => !mappedIds.includes(resp.id));
}

function filteredSearchResponsibles(role) {
    const available = getAvailableResponsiblesForRole(role);
    if (!mappingSearchQuery.value) return available;
    const query = mappingSearchQuery.value.toLowerCase();
    return available.filter(resp => resp.responsible.toLowerCase().includes(query));
}

function toggleResponsibleForMapping(respId) {
    const index = mappingForm.responsible_ids.indexOf(respId);
    if (index > -1) {
        mappingForm.responsible_ids.splice(index, 1);
    } else {
        mappingForm.responsible_ids.push(respId);
    }
}

function submitMappingForm() {
    if (mappingForm.responsible_ids.length === 0) {
        localError.value = 'Silakan pilih minimal satu Master Responsible terlebih dahulu.';
        return;
    }

    mappingForm.post(route('policy.roles.mapped-responsible.store'), {
        preserveScroll: true,
        onSuccess: () => {
            activeMappingRoleId.value = null;
            mappingSearchQuery.value = '';
            mappingForm.reset();
            localSuccess.value = 'Pemetaan Master Responsible berhasil ditambahkan!';
        },
        onError: () => {
            localError.value = 'Gagal menambahkan pemetaan Master Responsible.';
        }
    });
}

function deleteMapping(roleId, responsibleId) {
    if (confirm('Apakah Anda yakin ingin menghapus pemetaan master responsible ini?')) {
        router.delete(route('policy.roles.mapped-responsible.destroy', { roleId, responsibleId }), {
            preserveScroll: true,
            onSuccess: () => {
                localSuccess.value = 'Pemetaan Master Responsible berhasil dihapus.';
            },
            onError: () => {
                localError.value = 'Gagal menghapus pemetaan.';
            }
        });
    }
}
</script>

<style scoped>
/* Scoped styles */
</style>
