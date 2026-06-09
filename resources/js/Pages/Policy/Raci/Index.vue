<template>
    <UserLayout title="RACI Analisis">
        <div class="space-y-6 animate-fade-in-up print:m-0 print:p-0">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">RACI Analisis</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Print Button -->
                        <button
                            @click="printDocument"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
                            </svg>
                            Cetak PDF
                        </button>

                        <!-- Go to Master Responsible CRUD page -->
                        <Link
                            :href="route('policy.responsible.manage')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H3.75A2.25 2.25 0 001.5 4.5v15a2.25 2.25 0 002.25 2.25h12.75a2.25 2.25 0 002.25-2.25V14.25z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15.75h6m-6-4.5h6m-6-4.5h3.75" />
                            </svg>
                            Kelola Master Responsible
                        </Link>

                        <!-- Go to Management Mapping page -->
                        <Link
                            :href="route('policy.raci.manage')"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Kelola Pemetaan RACI
                        </Link>
                    </div>
                </div>
            </section>

            <!-- A4 Document Page Preview (Modified to fit stacked tables) -->
            <div class="w-full bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-4 sm:p-6 md:p-8 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0 overflow-visible">
                
                <!-- Document Title Section -->
                <div class="text-center space-y-1.5 relative z-10 py-4 mb-2">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#821f44] dark:text-[#a83262] uppercase">
                        RACI Analisis & Referensi COBIT 2019
                    </h2>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest">
                        Perbandingan Struktur Tata Kelola dan Struktur Organisasi
                    </p>
                </div>

                <!-- RACI Legend Key Guide -->
                <div class="mt-4 flex flex-wrap gap-4 items-center justify-center bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-4 rounded-xl print:hidden">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Keterangan RACI:</div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-rose-50 text-[11px] font-black text-rose-600 border border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/30 shadow-sm">A</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Accountable</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-[11px] font-black text-emerald-600 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/30 shadow-sm">R</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Responsible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-amber-50 text-[11px] font-black text-amber-600 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/30 shadow-sm">C</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Consulted</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-indigo-50 text-[11px] font-black text-indigo-600 border border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-400 dark:border-indigo-900/30 shadow-sm">I</span>
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Informed</span>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TOP SECTION: INTERNAL RACI MATRIX          -->
                <!-- ========================================== -->
                <div class="mt-8">
                    <!-- Section Header & Dedicated Filters -->
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between border-b border-slate-200 dark:border-white/10 pb-4 mb-4 gap-4 print:hidden">
                        <div>
                            <h3 class="text-lg font-extrabold text-[#821f44] dark:text-[#a83262] uppercase tracking-wide">1. RACI Analisis Internal</h3>
                        </div>
                        
                        <!-- Local Filters for Internal Table -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Filter GAMO (Internal) -->
                            <div class="w-full sm:w-auto min-w-[200px]">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Kebijakan Internal</label>
                                <select v-model="selectedGamo" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 shadow-sm transition focus:border-[#821f44] focus:ring-2 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white">
                                    <option value="">Semua Kebijakan Internal</option>
                                    <option v-for="obj in objectives" :key="obj.objective_id" :value="obj.objective_id">
                                        {{ obj.objective_id }} - {{ obj.objective }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Filter Role (Internal) -->
                            <div class="w-full sm:w-auto min-w-[150px]">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Role</label>
                                <select v-model="selectedRole" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 shadow-sm transition focus:border-[#821f44] focus:ring-2 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white">
                                    <option value="">Semua Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Hide Empty Columns Switch -->
                            <div class="flex items-center h-[38px] pt-4 select-none">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="hideEmptyColumns" 
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

                    <!-- Print Title (Only shows in print layout) -->
                    <h3 class="hidden print:block text-base font-extrabold text-[#821f44] uppercase tracking-wide border-b border-black pb-1 mb-3">
                        1. RACI Analisis Internal
                    </h3>

                    <!-- RACI Matrix Table container -->
                    <div class="overflow-x-auto border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full">
                        <table class="min-w-full border-collapse text-center">
                            <thead>
                                <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246]">
                                    <th class="sticky left-0 z-20 bg-[#d2e4df] dark:bg-[#1b3a32] p-2 text-left w-[180px] min-w-[180px] max-w-[180px] border-r border-[#b2cfc7] dark:border-[#255246] select-none">
                                        <div class="text-[9.5px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">Kebijakan & Butir Kebijakan</div>
                                    </th>
                                    <th 
                                        v-for="role in filteredRoles" 
                                        :key="role.id" 
                                        class="p-0.5 text-center w-[18px] min-w-[18px] max-w-[18px] align-bottom pb-2 select-none"
                                        :title="role.name"
                                    >
                                        <div class="inline-flex items-center justify-center h-36 w-[14px] mx-auto">
                                            <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                                {{ role.name }}
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                                <template v-for="obj in filteredObjectives" :key="obj.objective_id">
                                    <tr class="bg-slate-100 dark:bg-[#202020] border-y border-slate-300 dark:border-white/10">
                                        <td 
                                            :colspan="filteredRoles.length + 1" 
                                            class="sticky left-0 p-1.5 text-left font-bold text-[9px] text-slate-900 dark:text-white tracking-wide uppercase select-none"
                                        >
                                            {{ obj.objective_id }}: {{ obj.objective }}
                                        </td>
                                    </tr>
                                    <tr 
                                        v-for="pr in obj.practices" 
                                        :key="pr.practice_id" 
                                        class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5"
                                    >
                                        <td class="sticky left-0 z-10 bg-white dark:bg-[#1a1a1a] border-r border-slate-200 dark:border-white/10 p-1.5 text-left shadow-[2px_0_5px_rgba(0,0,0,0.02)] select-none">
                                            <div class="flex items-start gap-1">
                                                <span class="text-[9px] font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ pr.practice_id }}</span>
                                                <span class="text-[9px] font-bold text-slate-900 dark:text-white" :title="pr.practice_name">
                                                    {{ pr.practice_name || pr.practice_description || '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td 
                                            v-for="role in filteredRoles" 
                                            :key="role.id" 
                                            class="p-0 text-center w-[18px] min-w-[18px] max-w-[18px] align-middle h-[20px]"
                                        >
                                            <template v-if="getRaciValue(pr.practice_id, role.id)">
                                                   <span 
                                                       v-if="getRaciValue(pr.practice_id, role.id).toUpperCase() === 'I'"
                                                       class="inline-block w-[2.5px] h-[10px] bg-[#1e293b] dark:bg-slate-200 rounded-[0.5px] align-middle"
                                                       :title="`${pr.practice_id} - ${role.name}: Informed`"
                                                   ></span>
                                                <span 
                                                    v-else
                                                    :class="getRaciTextClass(getRaciValue(pr.practice_id, role.id))"
                                                    :title="`${pr.practice_id} - ${role.name}: ${getRaciLabel(getRaciValue(pr.practice_id, role.id))}`"
                                                >
                                                    {{ getRaciValue(pr.practice_id, role.id) }}
                                                </span>
                                            </template>
                                        </td>
                                    </tr>
                                    <tr v-if="!obj.practices || obj.practices.length === 0">
                                        <td 
                                            :colspan="filteredRoles.length + 1" 
                                            class="p-2 text-center text-[9px] italic text-slate-400 dark:text-slate-500"
                                        >
                                            Belum ada practices untuk Objective ini.
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="filteredObjectives.length === 0">
                                    <td 
                                        :colspan="filteredRoles.length + 1" 
                                        class="p-6 text-center text-xs font-sans text-slate-400 dark:text-slate-500"
                                    >
                                        Belum ada data Governance Objective / Practices.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Decorative Section Separator (Stacked Visual Layout Divider) -->
                <div class="my-10 border-t border-dashed border-slate-200 dark:border-white/10 print:my-6 print:border-black"></div>

                <!-- ========================================== -->
                <!-- BOTTOM SECTION: REFERENSI COBIT 2019       -->
                <!-- ========================================== -->
                <div>
                    <!-- Section Header & Dedicated Filters -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200 dark:border-white/10 pb-4 mb-4 gap-4 print:hidden">
                        <div>
                            <h3 class="text-lg font-extrabold text-[#821f44] dark:text-[#a83262] uppercase tracking-wide">2. Referensi COBIT 2019</h3>
                        </div>
                        
                        <!-- Local Filters for COBIT Reference Table -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Filter GAMO -->
                            <div class="w-full sm:w-auto min-w-[220px]">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter GAMO (COBIT)</label>
                                <select v-model="selectedGamoCobit" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 shadow-sm transition focus:border-[#821f44] focus:ring-2 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white">
                                    <option value="">Semua GAMO</option>
                                    <optgroup v-for="group in cobitObjectives" :key="group.domain" :label="group.domain" class="text-slate-400 dark:text-zinc-500 font-bold bg-white dark:bg-[#1a1a1a]">
                                        <option v-for="item in group.items" :key="item.id" :value="item.id" class="text-slate-800 dark:text-slate-200 font-semibold">
                                            {{ item.id }} - {{ item.name }}
                                        </option>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Filter Role (COBIT) dynamically populated from API roles list -->
                            <div class="w-full sm:w-auto min-w-[180px]">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Role (COBIT)</label>
                                <select v-model="selectedRoleCobit" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-900 shadow-sm transition focus:border-[#821f44] focus:ring-2 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white" :disabled="!cobitData.roles || cobitData.roles.length === 0">
                                    <option value="">Semua Role</option>
                                    <option v-for="role in cobitData.roles" :key="role.role_id" :value="role.role_id">
                                        {{ cleanRoleName(role.role_name) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Hide Empty Columns Switch -->
                            <div class="flex items-center h-[38px] pt-4 select-none">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="hideEmptyColumnsCobit" 
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

                    <!-- Print Title (Only shows in print layout) -->
                    <h3 class="hidden print:block text-base font-extrabold text-[#821f44] uppercase tracking-wide border-b border-black pb-1 mb-3">
                        2. Referensi COBIT 2019
                    </h3>

                    <!-- Loading State -->
                    <div v-if="cobitLoading" class="flex flex-col items-center justify-center p-16 border border-dashed border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 animate-pulse">
                        <div class="inline-block w-8 h-8 border-4 border-[#821f44] border-t-transparent rounded-full animate-spin"></div>
                        <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Mengambil data referensi COBIT 2019...</p>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="cobitError" class="p-8 border border-red-200 dark:border-red-950/30 rounded-2xl bg-red-50/50 dark:bg-red-950/10 flex flex-col items-center text-center">
                        <div class="h-12 w-12 rounded-full bg-red-100 dark:bg-red-950/50 flex items-center justify-center text-red-600 dark:text-red-400 mb-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Gagal Memuat Referensi COBIT</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-md">{{ cobitError }}</p>
                        <button 
                            @click="fetchCobitMatrix"
                            class="mt-5 px-4 py-2 bg-[#821f44] hover:bg-[#9c2552] text-white font-bold text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-1.5"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Coba Lagi
                        </button>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="!cobitData.matrix || cobitData.matrix.length === 0" class="p-12 border border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 text-center flex flex-col items-center">
                        <div class="h-12 w-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18" />
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak ada data referensi COBIT 2019 yang ditemukan.</p>
                    </div>

                    <!-- Live Grid Reference Table -->
                    <div v-else class="overflow-x-auto border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full">
                        <table class="min-w-full border-collapse text-center">
                            <thead>
                                <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246]">
                                    <th class="sticky left-0 z-20 bg-[#d2e4df] dark:bg-[#1b3a32] p-2 text-left w-[240px] min-w-[240px] max-w-[240px] border-r border-[#b2cfc7] dark:border-[#255246] select-none">
                                        <div class="text-[9.5px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300"> Management Practice Of Governance and Management Objectives</div>
                                    </th>
                                    <th 
                                    
                                        v-for="role in filteredCobitRoles" 
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
                                <tr 
                                    v-for="row in cobitData.matrix" 
                                    :key="row.practice_id" 
                                    class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5"
                                >
                                    <td class="sticky left-0 z-10 bg-white dark:bg-[#1a1a1a] border-r border-slate-200 dark:border-white/10 p-1.5 text-left shadow-[2px_0_5px_rgba(0,0,0,0.02)] select-none">
                                        <div class="flex items-start gap-1">
                                            <span class="text-[9px] font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ cleanPracticeName(row.practice_id) }}</span>
                                            <span class="text-[9px] font-bold text-slate-900 dark:text-white" :title="cleanPracticeName(row.practice_name)">
                                                {{ cleanPracticeName(row.practice_name) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td 
                                        v-for="role in filteredCobitRoles" 
                                        :key="role.role_id" 
                                        class="p-0 text-center w-[18px] min-w-[18px] max-w-[18px] align-middle h-[20px]"
                                    >
                                        <template v-if="cleanPracticeName(row.role_assignments[role.role_id])">
                                            <span 
                                                v-if="cleanPracticeName(row.role_assignments[role.role_id]).toUpperCase() === 'I'"
                                                class="inline-block w-[2.5px] h-[10px] bg-[#1e293b] dark:bg-slate-200 rounded-[0.5px] align-middle"
                                                :title="`${cleanPracticeName(row.practice_id)} - ${cleanRoleName(role.role_name)}: Informed`"
                                            ></span>
                                            <span 
                                                v-else
                                                :class="getRaciTextClass(cleanPracticeName(row.role_assignments[role.role_id]))"
                                                :title="`${cleanPracticeName(row.practice_id)} - ${cleanRoleName(role.role_name)}: ${getRaciLabel(cleanPracticeName(row.role_assignments[role.role_id]))}`"
                                            >
                                                {{ cleanPracticeName(row.role_assignments[role.role_id]) }}
                                            </span>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Floating Navigation Button for print or scroll -->
        <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-30 print:hidden">
            <button 
                @click="scrollToTop"
                class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 transition-all hover:bg-slate-50 active:scale-90 hover:shadow-xl dark:bg-[#1a1a1a] dark:border-white/10"
                title="Kembali ke Atas"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-[#0a2540] dark:text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
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
    </UserLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

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

const selectedGamo = ref('');
const selectedGamoCobit = ref('');
const selectedRole = ref('');
const selectedRoleCobit = ref('');
const hideEmptyColumns = ref(true); // Hide columns with no data by default
const hideEmptyColumnsCobit = ref(true);

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

// Live COBIT Reference API State
const cobitLoading = ref(false);
const cobitError = ref(null);
const cobitData = ref({ roles: [], matrix: [] });

// Clean escaped quotes from API role names (e.g. "\"Board\"" -> "Board")
function cleanRoleName(name) {
    if (!name) return '';
    return name.replace(/^["'\\\s]+|["'\\\s]+$/g, '').replace(/\\"/g, '"');
}

// Clean escaped quotes from API practice names (e.g. "\"Managed Risk\"" -> "Managed Risk")
function cleanPracticeName(name) {
    if (!name) return '';
    return String(name).replace(/^["'\\\s]+|["'\\\s]+$/g, '').replace(/\\"/g, '"');
}

async function fetchCobitMatrix() {
    cobitLoading.value = true;
    cobitError.value = null;
    try {
        let url = 'https://cobit2019.divusi.co.id/api/cobit/roles-matrix';
        if (selectedGamoCobit.value) {
            url += `?objective_id=${encodeURIComponent(selectedGamoCobit.value)}`;
        }
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Server returned status ${response.status}`);
        }
        const resJson = await response.json();
        if (!resJson.success) {
            throw new Error(resJson.message || 'API response indicated failure');
        }
        cobitData.value = {
            roles: resJson.roles || [],
            matrix: resJson.matrix || []
        };
    } catch (err) {
        console.error('Error fetching COBIT data:', err);
        cobitError.value = err.message || 'Gagal memuat data referensi COBIT.';
    } finally {
        cobitLoading.value = false;
    }
}

// Watch selectedGamoCobit to fetch COBIT reference data live on mount or change
watch(selectedGamoCobit, () => {
    fetchCobitMatrix();
    selectedRoleCobit.value = ''; // Reset role filter when objective changes
}, { immediate: true });

const filteredObjectives = computed(() => {
    if (!selectedGamo.value) return props.objectives;
    return props.objectives.filter(o => String(o.objective_id) === String(selectedGamo.value));
});

const displayedPracticeIds = computed(() => {
    const ids = [];
    filteredObjectives.value.forEach(obj => {
        if (obj.practices) {
            obj.practices.forEach(pr => {
                ids.push(String(pr.practice_id));
            });
        }
    });
    return ids;
});

const raciLookup = computed(() => {
    const entries = props.mappings.map((mapping) => [
        `${String(mapping.practice_id)}::${String(mapping.role_id)}`,
        mapping.r_a || '',
    ]);

    return new Map(entries);
});

// Get RACI value for a specific cell
function getRaciValue(practiceId, roleId) {
    return raciLookup.value.get(`${String(practiceId)}::${String(roleId)}`) || '';
}

const filteredRoles = computed(() => {
    let list = props.roles;

    if (selectedRole.value) {
        list = list.filter(r => String(r.id) === String(selectedRole.value));
    }

    if (hideEmptyColumns.value) {
        const practiceIds = displayedPracticeIds.value;
        list = list.filter(role => {
            return practiceIds.some(practiceId => {
                const val = getRaciValue(practiceId, role.id);
                return val !== null && val !== '';
            });
        });
    }

    return list;
});

// Filter roles dynamically for the COBIT matrix based on selectedRoleCobit dropdown
const filteredCobitRoles = computed(() => {
    let list = cobitData.value.roles || [];
    if (selectedRoleCobit.value) {
        list = list.filter(r => String(r.role_id) === String(selectedRoleCobit.value));
    }
    if (hideEmptyColumnsCobit.value) {
        const matrix = cobitData.value.matrix || [];
        list = list.filter(role => {
            return matrix.some(row => {
                const val = row.role_assignments && row.role_assignments[role.role_id];
                return val && val !== '';
            });
        });
    }
    return list;
});

// Get descriptive tooltip label
function getRaciLabel(val) {
    const labels = {
        'A': 'Accountable',
        'R': 'Responsible',
        'C': 'Consulted',
        'I': 'Informed',
    };
    return labels[val.toUpperCase()] || '';
}

// Return beautifully stylized color class for RACI letters
function getRaciTextClass(val) {
    const cleanVal = val.toUpperCase();
    if (cleanVal === 'A') {
        return "text-[#d9383a] dark:text-[#f87171] font-black text-[10.5px] select-none";
    }
    if (cleanVal === 'R') {
        return "text-[#2d9a68] dark:text-[#34d399] font-black text-[10.5px] select-none";
    }
    if (cleanVal === 'C') {
        return "text-[#dd7d36] dark:text-[#fbbf24] font-black text-[10.5px] select-none";
    }
    return "text-slate-800 dark:text-slate-200 font-bold text-[10px] select-none";
}

function printDocument() {
    window.print();
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
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

@media print {
    body {
        background-color: white !important;
        color: black !important;
    }
    /* Hide scrollbars during print */
    .overflow-x-auto {
        overflow-x: visible !important;
    }
    /* Keep sticky columns standard on print */
    .sticky {
        position: static !important;
        box-shadow: none !important;
    }
}
</style>