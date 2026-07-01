<template>
    <ModulLayout title="Kelola RACI Analisis">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola RACI Analisis</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            @click="openCobitDrawer"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#821f44]/20 bg-[#821f44]/5 px-4 py-2.5 text-sm font-bold text-[#821f44] shadow-sm transition hover:bg-[#821f44]/10 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-.778.099-1.533.284-2.253" />
                            </svg>
                            Referensi COBIT 2019
                        </button>
                        <Link
                            :href="route('itom.operating-model.raci-analysis.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Kembali ke Dokumen
                        </Link>
                        <button
                            @click="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95 disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan Perubahan Matriks
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

            <!-- Mapping Grid Editor -->
            <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-slate-200 dark:border-white/10 p-6 shadow-sm overflow-visible">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">Matriks Input Pemetaan RACI</h2>
                    
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Silakan pilih RACI level di setiap cell, lalu klik Simpan di kanan atas atau bawah.
                    </div>
                </div>

                <!-- RACI Matrix Table container -->
                <div class="overflow-x-auto border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full">
                    <table class="min-w-full border-collapse text-center">
                        <!-- Headings -->
                        <thead>
                            <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246]">
                                <!-- Left Sticky Header space -->
                                <th class="sticky left-0 z-20 bg-[#d2e4df] dark:bg-[#1b3a32] p-2 text-left w-[180px] min-w-[180px] max-w-[180px] border-r border-[#b2cfc7] dark:border-[#255246] select-none">
                                    <div class="text-[9.5px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">Governance Processes</div>
                                </th>
                                <!-- Role Headers rotated vertically -->
                                <th 
                                    v-for="role in roles" 
                                    :key="role.id" 
                                    class="p-0.5 text-center w-[24px] min-w-[24px] max-w-[24px] align-bottom pb-2 select-none"
                                    :title="role.name"
                                >
                                    <div class="inline-flex items-center justify-center h-36 w-[18px] mx-auto">
                                        <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <!-- Body grouped by objective -->
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                            <template v-for="obj in objectives" :key="obj.objective_id">
                                <!-- Objective Header Row -->
                                <tr class="bg-slate-100 dark:bg-[#202020] border-y border-slate-300 dark:border-white/10">
                                    <td 
                                        :colspan="roles.length + 1" 
                                        class="sticky left-0 p-1.5 text-left font-bold text-[9px] text-slate-900 dark:text-white tracking-wide uppercase select-none"
                                    >
                                        {{ obj.objective_id }}: {{ obj.objective }}
                                    </td>
                                </tr>

                                <!-- Practices Rows -->
                                <tr 
                                    v-for="pr in obj.practices" 
                                    :key="pr.practice_id" 
                                    class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5"
                                >
                                    <!-- Practice details (sticky) -->
                                    <td class="sticky left-0 z-10 bg-white dark:bg-[#1a1a1a] border-r border-slate-200 dark:border-white/10 p-1.5 text-left shadow-[2px_0_5px_rgba(0,0,0,0.02)] select-none">
                                        <div class="text-[9px] font-bold text-slate-900 dark:text-white">{{ pr.practice_id }}</div>
                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 font-sans leading-tight mt-0.5 line-clamp-1" :title="pr.practice_name">
                                            {{ pr.practice_name || pr.practice_description || '-' }}
                                        </div>
                                    </td>

                                    <!-- RACI Cell Select Option Inputs -->
                                    <td 
                                        v-for="role in roles" 
                                        :key="role.id" 
                                        class="p-0 text-center w-[24px] min-w-[24px] max-w-[24px] align-middle h-[20px]"
                                    >
                                        <select 
                                            v-model="matrixState[pr.practice_id][role.id]"
                                            class="text-[9px] font-black rounded border text-center focus:ring-1 focus:ring-[#821f44]/40 h-5.5 w-[20px] transition-all duration-300 appearance-none bg-no-repeat bg-center select-none py-0 px-0"
                                            :class="getCellColorClass(matrixState[pr.practice_id]?.[role.id])"
                                        >
                                            <option value="" class="bg-white text-slate-400 dark:bg-[#222] dark:text-slate-600 font-semibold">-</option>
                                            <option value="R" class="bg-emerald-50 text-emerald-700 dark:bg-[#102a1c] dark:text-emerald-400 font-black">R</option>
                                            <option value="A" class="bg-rose-50 text-rose-700 dark:bg-[#341118] dark:text-rose-400 font-black">A</option>
                                            <option value="C" class="bg-amber-50 text-amber-700 dark:bg-[#2e1d0c] dark:text-amber-400 font-black">C</option>
                                            <option value="I" class="bg-indigo-50 text-indigo-700 dark:bg-[#13172e] dark:text-indigo-400 font-black">I</option>
                                        </select>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="objectives.length === 0">
                                <td 
                                    :colspan="roles.length + 1" 
                                    class="p-8 text-center text-xs font-sans text-slate-400 dark:text-slate-500"
                                >
                                    Belum ada data Governance Objective / Practices.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sticky bottom save action bar -->
                <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 dark:border-white/5 pt-6">
                    <Link
                        :href="route('itom.operating-model.raci-analysis.index')"
                        class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        Batal
                    </Link>
                    <button
                        @click="submit"
                        class="rounded-xl bg-[#821f44] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition hover:bg-[#9c2552] disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                        Simpan Pemetaan
                    </button>
                </div>

            </div>

            <!-- Floating Navigation Controls on the Right Side -->
            <div class="fixed right-6 bottom-24 z-[9999] flex flex-col gap-3 pointer-events-none">
                <button 
                    @click="submit" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-lg border border-[#821f44]/20 transition-all hover:bg-[#9c2552] active:scale-90 hover:shadow-xl"
                    title="Simpan Pemetaan RACI"
                    :disabled="form.processing"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
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
            </div>

            <!-- Slide-out Drawer Overlay & Container -->
            <transition name="fade">
                <div v-if="isDrawerOpen" class="fixed inset-0 z-[10000] flex justify-end pointer-events-auto bg-slate-900/60 backdrop-blur-sm" @click="closeCobitDrawer">
                    <!-- Drawer Panel -->
                    <div 
                        class="h-full w-full max-w-3xl bg-white dark:bg-[#171717] shadow-2xl flex flex-col animate-slide-in-right border-l border-slate-200 dark:border-white/10"
                        @click.stop
                    >
                        <!-- Drawer Header -->
                        <div class="p-5 border-b border-slate-100 dark:border-white/5 space-y-4 shrink-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-extrabold text-[#821f44] dark:text-[#a83262] uppercase tracking-wide">Referensi COBIT 2019</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Komponen Struktur Organisasi (Live API)</p>
                                </div>
                                <button @click="closeCobitDrawer" class="rounded-lg p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Filters Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <!-- GAMO Selector Dropdown -->
                                <div>
                                    <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Filter GAMO</label>
                                    <select 
                                        v-model="drawerSelectedObjective" 
                                        @change="fetchDrawerCobit"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm transition focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                    >
                                        <option value="">Semua GAMO</option>
                                        <optgroup v-for="group in cobitObjectives" :key="group.domain" :label="group.domain" class="text-slate-400 dark:text-zinc-500 font-bold bg-white dark:bg-[#1a1a1a]">
                                            <option v-for="item in group.items" :key="item.id" :value="item.id" class="text-slate-800 dark:text-slate-200 font-semibold">
                                                {{ item.id }} - {{ item.name }}
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- Filter Role (COBIT) dynamically populated from API roles list -->
                                <div>
                                    <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Filter Role (COBIT)</label>
                                    <select 
                                        v-model="drawerSelectedRole" 
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm transition focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white" 
                                        :disabled="!drawerCobitData.roles || drawerCobitData.roles.length === 0"
                                    >
                                        <option value="">Semua Role</option>
                                        <option v-for="role in drawerCobitData.roles" :key="role.role_id" :value="role.role_id">
                                            {{ cleanRoleName(role.role_name) }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Hide Empty Columns Switch (COBIT Drawer) -->
                                <div class="flex items-center h-[42px] select-none">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            v-model="drawerHideEmptyColumns" 
                                            class="sr-only peer"
                                        >
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-[#2c2c2c] peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:after:bg-zinc-500 peer-checked:bg-[#821f44]"></div>
                                        <span class="ml-2 text-xs font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                            Sembunyikan Kolom Kosong
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Drawer Body (Scrollable) -->
                        <div class="flex-1 overflow-y-auto p-5 min-h-0">
                            <!-- Loading Skeleton -->
                            <div v-if="drawerLoading" class="space-y-4 animate-pulse">
                                <div class="h-4 bg-slate-200 dark:bg-white/5 rounded-lg w-1/3"></div>
                                <div class="space-y-2.5">
                                    <div v-for="i in 8" :key="i" class="flex gap-2">
                                        <div class="h-6 bg-slate-200 dark:bg-white/5 rounded-lg w-1/4"></div>
                                        <div class="h-6 bg-slate-200 dark:bg-white/5 rounded-lg flex-1"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error State -->
                            <div v-else-if="drawerError" class="py-12 flex flex-col items-center text-center">
                                <div class="h-10 w-10 rounded-full bg-rose-100 dark:bg-rose-950/50 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-3 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Gagal Memuat Referensi COBIT</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 max-w-sm">{{ drawerError }}</p>
                                <button 
                                    @click="fetchDrawerCobit"
                                    class="mt-4 px-3 py-1.5 bg-[#821f44] hover:bg-[#9c2552] text-white font-bold text-[10px] rounded-lg shadow-sm transition active:scale-95 flex items-center gap-1"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Coba Lagi
                                </button>
                            </div>

                            <!-- Empty State -->
                            <div v-else-if="!drawerCobitData.matrix || drawerCobitData.matrix.length === 0" class="py-16 text-center flex flex-col items-center">
                                <div class="h-10 w-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak ada data referensi COBIT 2019.</p>
                            </div>

                            <!-- Live Grid Reference Table -->
                            <div v-else class="overflow-x-auto border border-slate-200 dark:border-white/10 rounded-xl shadow-sm max-w-full">
                                <table class="min-w-full border-collapse text-center">
                                    <thead>
                                        <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246]">
                                            <th class="sticky left-0 z-20 bg-[#d2e4df] dark:bg-[#1b3a32] p-2 text-left w-[180px] min-w-[180px] max-w-[180px] border-r border-[#b2cfc7] dark:border-[#255246] select-none">
                                                <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">Key Management Practice</div>
                                            </th>
                                            <th 
                                                v-for="role in filteredDrawerCobitRoles" 
                                                :key="role.role_id" 
                                                class="p-0.5 text-center w-[18px] min-w-[18px] max-w-[18px] align-bottom pb-2 select-none"
                                                :title="cleanRoleName(role.role_name)"
                                            >
                                                <div class="inline-flex items-center justify-center h-36 w-[14px] mx-auto">
                                                    <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                                        {{ cleanRoleName(role.role_name) }}
                                                    </span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                                        <template v-for="obj in groupedDrawerCobitMatrix" :key="obj.objective_id">
                                            <tr class="bg-slate-100 dark:bg-[#202020] border-y border-slate-300 dark:border-white/10">
                                                <td 
                                                    :colspan="filteredDrawerCobitRoles.length + 1" 
                                                    class="sticky left-0 p-1.5 text-left font-bold text-[9px] text-slate-900 dark:text-white tracking-wide uppercase select-none border-r border-slate-200 dark:border-white/10"
                                                >
                                                    {{ obj.objective_id }}: {{ obj.objective_name }}
                                                </td>
                                            </tr>
                                            <tr 
                                                v-for="row in obj.practices" 
                                                :key="row.practice_id" 
                                                class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5"
                                            >
                                                <td class="sticky left-0 z-10 bg-white dark:bg-[#171717] border-r border-slate-200 dark:border-white/10 p-1.5 text-left shadow-[2px_0_5px_rgba(0,0,0,0.02)] select-none">
                                                    <div class="flex items-start gap-1">
                                                        <span class="text-[8.5px] font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ cleanRoleName(row.practice_id) }}</span>
                                                        <span class="text-[8.5px] font-bold text-slate-900 dark:text-white leading-tight" :title="cleanRoleName(row.practice_name)">
                                                            {{ cleanRoleName(row.practice_name) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td 
                                                    v-for="role in filteredDrawerCobitRoles" 
                                                    :key="role.role_id" 
                                                    class="p-0 text-center w-[18px] min-w-[18px] max-w-[18px] align-middle h-[20px]"
                                                >
                                                    <template v-if="row.role_assignments[role.role_id]">
                                                        <span 
                                                            v-if="row.role_assignments[role.role_id].toUpperCase() === 'I'"
                                                            class="inline-block w-[2.5px] h-[9px] bg-[#1e293b] dark:bg-slate-200 rounded-[0.5px] align-middle"
                                                            :title="`${cleanRoleName(row.practice_id)} - ${cleanRoleName(role.role_name)}: Informed`"
                                                        ></span>
                                                        <span 
                                                            v-else
                                                            :class="getDrawerRaciTextClass(row.role_assignments[role.role_id])"
                                                            :title="`${cleanRoleName(row.practice_id)} - ${cleanRoleName(role.role_name)}: ${row.role_assignments[role.role_id]}`"
                                                        >
                                                            {{ row.role_assignments[role.role_id] }}
                                                        </span>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-if="groupedDrawerCobitMatrix.length === 0">
                                            <td 
                                                :colspan="filteredDrawerCobitRoles.length + 1" 
                                                class="p-6 text-center text-xs font-sans text-slate-400 dark:text-slate-500"
                                            >
                                                Belum ada data referensi COBIT 2019.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Drawer Footer -->
                        <div class="p-4 border-t border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-white/5 flex justify-end shrink-0">
                            <button 
                                @click="closeCobitDrawer"
                                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                            >
                                Tutup Referensi
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { usePage, useForm, Link } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';

const props = defineProps({
    objectives: {
        type: Array,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    mappings: {
        type: Array,
        required: true,
    },
});

const page = usePage();

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

// COBIT Reference Drawer State
const isDrawerOpen = ref(false);
const drawerSelectedObjective = ref('');
const drawerSelectedRole = ref('');
const drawerLoading = ref(false);
const drawerError = ref(null);
const drawerCobitData = ref({ roles: [], matrix: [] });
const drawerHideEmptyColumns = ref(true); // Hide empty columns in COBIT drawer table by default

// Official COBIT 2019 Governance and Management Objectives (GAMO) list grouped by domain
const cobitObjectives = [
    {
        domain: 'Evaluate, Direct and Monitor (EDM)',
        items: [
            { id: 'EDM01', name: 'Ensured Governance Framework Setting and Maintenance' },
            { id: 'EDM02', name: 'Ensured Benefits Delivery' },
            { id: 'EDM03', name: 'Ensured Risk Optimization' },
            { id: 'EDM04', name: 'Ensured Resource Optimization' },
            { id: 'EDM05', name: 'Ensured Stakeholder Engagement' }
        ]
    },
    {
        domain: 'Align, Plan and Organize (APO)',
        items: [
            { id: 'APO01', name: 'Managed I&T Management Framework' },
            { id: 'APO02', name: 'Managed Strategy' },
            { id: 'APO03', name: 'Managed Enterprise Architecture' },
            { id: 'APO04', name: 'Managed Innovation' },
            { id: 'APO05', name: 'Managed Portfolio' },
            { id: 'APO06', name: 'Managed Budget and Costs' },
            { id: 'APO07', name: 'Managed Human Resources' },
            { id: 'APO08', name: 'Managed Relationships' },
            { id: 'APO09', name: 'Managed Service Agreements' },
            { id: 'APO10', name: 'Managed Vendors' },
            { id: 'APO11', name: 'Managed Quality' },
            { id: 'APO12', name: 'Managed Risk' },
            { id: 'APO13', name: 'Managed Security' },
            { id: 'APO14', name: 'Managed Data' }
        ]
    },
    {
        domain: 'Build, Acquire and Implement (BAI)',
        items: [
            { id: 'BAI01', name: 'Managed Programs and Projects' },
            { id: 'BAI02', name: 'Managed Requirements Definition' },
            { id: 'BAI03', name: 'Managed Solutions Identification and Build' },
            { id: 'BAI04', name: 'Managed Availability and Capacity' },
            { id: 'BAI05', name: 'Managed Organizational Change' },
            { id: 'BAI06', name: 'Managed IT Changes' },
            { id: 'BAI07', name: 'Managed IT Change Acceptance and Transitioning' },
            { id: 'BAI08', name: 'Managed Knowledge' },
            { id: 'BAI09', name: 'Managed Assets' },
            { id: 'BAI10', name: 'Managed Configuration' },
            { id: 'BAI11', name: 'Managed Projects' }
        ]
    },
    {
        domain: 'Deliver, Service and Support (DSS)',
        items: [
            { id: 'DSS01', name: 'Managed Operations' },
            { id: 'DSS02', name: 'Managed Service Requests and Incidents' },
            { id: 'DSS03', name: 'Managed Problems' },
            { id: 'DSS04', name: 'Managed Continuity' },
            { id: 'DSS05', name: 'Managed Security Services' },
            { id: 'DSS06', name: 'Managed Business Process Controls' }
        ]
    },
    {
        domain: 'Monitor, Evaluate and Assess (MEA)',
        items: [
            { id: 'MEA01', name: 'Managed Performance and Conformance Monitoring' },
            { id: 'MEA02', name: 'Managed System of Internal Control' },
            { id: 'MEA03', name: 'Managed Compliance With External Requirements' },
            { id: 'MEA04', name: 'Managed Assurance' }
        ]
    }
];

function openCobitDrawer() {
    isDrawerOpen.value = true;
    fetchDrawerCobit();
}

function closeCobitDrawer() {
    isDrawerOpen.value = false;
}

// Clean escaped quotes from API role names (e.g. "\"Board\"" -> "Board")
function cleanRoleName(name) {
    if (!name) return '';
    return name.replace(/^["'\\\s]+|["'\\\s]+$/g, '').replace(/\\"/g, '"');
}

// Filter roles dynamically for the COBIT matrix inside the drawer
const filteredDrawerCobitRoles = computed(() => {
    let list = drawerCobitData.value.roles || [];
    if (drawerSelectedRole.value) {
        list = list.filter(r => String(r.role_id) === String(drawerSelectedRole.value));
    }
    if (drawerHideEmptyColumns.value) {
        const matrix = drawerCobitData.value.matrix || [];
        list = list.filter(role => {
            return matrix.some(row => {
                const val = row.role_assignments[role.role_id];
                return val !== null && val !== undefined && val !== '';
            });
        });
    }
    return list;
});

// Group COBIT matrix rows for the drawer by objective_id and objective_name
const groupedDrawerCobitMatrix = computed(() => {
    const groups = {};
    const matrix = drawerCobitData.value.matrix || [];
    
    let filteredMatrix = matrix;
    if (drawerSelectedRole.value) {
        filteredMatrix = matrix.filter(row => {
            const val = row.role_assignments[drawerSelectedRole.value];
            return val !== null && val !== undefined && val !== '';
        });
    }

    filteredMatrix.forEach(row => {
        const objId = cleanRoleName(row.objective_id) || 'Unknown';
        const objName = cleanRoleName(row.objective_name) || '';
        if (!groups[objId]) {
            groups[objId] = {
                objective_id: objId,
                objective_name: objName,
                practices: []
            };
        }
        groups[objId].practices.push(row);
    });
    return Object.values(groups);
});

// Reset role filter when objective changes
watch(drawerSelectedObjective, () => {
    drawerSelectedRole.value = '';
});

async function fetchDrawerCobit() {
    drawerLoading.value = true;
    drawerError.value = null;
    try {
        let url = 'https://cobit2019.divusi.co.id/api/cobit/roles-matrix';
        if (drawerSelectedObjective.value) {
            url += `?objective_id=${encodeURIComponent(drawerSelectedObjective.value)}`;
        }
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Server returned status ${response.status}`);
        }
        const resJson = await response.json();
        if (!resJson.success) {
            throw new Error(resJson.message || 'API response indicated failure');
        }
        drawerCobitData.value = {
            roles: resJson.roles || [],
            matrix: resJson.matrix || []
        };
    } catch (err) {
        console.error('Error fetching COBIT drawer data:', err);
        drawerError.value = err.message || 'Gagal memuat data referensi COBIT.';
    } finally {
        drawerLoading.value = false;
    }
}

function getDrawerRaciTextClass(val) {
    const cleanVal = val.toUpperCase();
    if (cleanVal === 'A') {
        return "text-[#d9383a] dark:text-[#f87171] font-black text-[10px] select-none";
    }
    if (cleanVal === 'R') {
        return "text-[#2d9a68] dark:text-[#34d399] font-black text-[10px] select-none";
    }
    if (cleanVal === 'C') {
        return "text-[#dd7d36] dark:text-[#fbbf24] font-black text-[10px] select-none";
    }
    return "text-slate-800 dark:text-slate-200 font-bold text-[9.5px] select-none";
}

// ---------------------------------------------------
// RACI MATRIX STATE & SUBMIT HELPERS
// ---------------------------------------------------
const matrixState = ref({});
const initialState = {}; // Snapshot of the original state for dirty-tracking

// Initialize 2D grid matrix state from current DB mapping
props.objectives.forEach(obj => {
    obj.practices.forEach(pr => {
        matrixState.value[pr.practice_id] = {};
        initialState[pr.practice_id] = {};
        props.roles.forEach(role => {
            const map = props.mappings.find(
                m => String(m.practice_id) === String(pr.practice_id) && String(m.role_id) === String(role.id)
            );
            const val = map ? map.r_a : '';
            matrixState.value[pr.practice_id][role.id] = val;
            initialState[pr.practice_id][role.id] = val;
        });
    });
});

const form = useForm({
    mappings: []
});

function submit() {
    // Only send mappings that have actually changed (dirty tracking)
    const list = [];
    Object.keys(matrixState.value).forEach(practiceId => {
        Object.keys(matrixState.value[practiceId]).forEach(roleId => {
            const current = matrixState.value[practiceId][roleId] || '';
            const original = (initialState[practiceId] && initialState[practiceId][roleId]) || '';
            if (current !== original) {
                list.push({
                    practice_id: practiceId,
                    role_id: parseInt(roleId),
                    r_a: current
                });
            }
        });
    });

    if (list.length === 0) {
        localSuccess.value = "Tidak ada perubahan untuk disimpan.";
        setTimeout(() => { localSuccess.value = null; }, 4000);
        return;
    }

    form.mappings = list;
    form.post(route('itom.operating-model.raci-analysis.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Update initial state snapshot to reflect saved state
            list.forEach(item => {
                if (initialState[item.practice_id]) {
                    initialState[item.practice_id][item.role_id] = item.r_a;
                }
            });
            localSuccess.value = `Matriks RACI berhasil disimpan! (${list.length} perubahan)`;
        },
        onError: () => {
            localError.value = "Terjadi kesalahan saat menyimpan matriks.";
        }
    });
}

// Return beautifully stylized color badges for RACI select boxes
function getCellColorClass(val) {
    if (!val) {
        return "bg-slate-50/50 text-slate-400 border-slate-200 dark:bg-transparent dark:border-white/10 dark:text-slate-600";
    }
    const cleanVal = val.toUpperCase();
    if (cleanVal === 'A') {
        return "bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800 font-extrabold focus:bg-rose-50";
    }
    if (cleanVal === 'R') {
        return "bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800 font-extrabold focus:bg-emerald-50";
    }
    if (cleanVal === 'C') {
        return "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800 font-extrabold focus:bg-amber-50";
    }
    if (cleanVal === 'I') {
        return "bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800 font-extrabold focus:bg-indigo-50";
    }
    return "bg-slate-50 text-slate-700 border-slate-200 dark:bg-transparent dark:border-white/10 dark:text-slate-400";
}

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
</script>

<style scoped>
/* Custom Webkit scrollbar for premium touch */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
    background-color: transparent;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: rgba(130, 31, 68, 0.2);
    border-radius: 99px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(130, 31, 68, 0.4);
}

/* Slide-in Drawer Animations */
.animate-slide-in-right {
    animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
