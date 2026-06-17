<template>
    <UserLayout title="Regulation">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">List of Policy, Standard, and Procedure</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Regulation</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('policy.regulation.manage')"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Kelola Regulasi
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Toggles / Tabs for View Modes -->
            <div class="flex items-center gap-1.5 rounded-xl bg-slate-100 p-1 dark:bg-white/5 w-fit">
                <button
                    @click="activeViewMode = 'flat'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeViewMode === 'flat'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    Daftar Flat
                </button>
                <button
                    @click="activeViewMode = 'document'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeViewMode === 'document'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Hirarki Dokumen
                </button>
                <button
                    @click="activeViewMode = 'organization'"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                        activeViewMode === 'organization'
                            ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    Hirarki Organisasi
                </button>
            </div>

            <!-- Regulations Table Components -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-3 py-3 w-10 text-center border-r border-b border-slate-200 dark:border-white/10">No</th>
                                <th scope="col" class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10">Judul</th>
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Nomor</th>
                                <th scope="col" class="px-3 py-3 w-28 border-r border-b border-slate-200 dark:border-white/10">Tipe</th>
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Fungsi</th>
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Organisasi</th>
                                <th scope="col" class="px-3 py-3 text-center w-16 border-r border-b border-slate-200 dark:border-white/10">Revisi</th>
                                <th scope="col" class="px-3 py-3 w-24 border-r border-b border-slate-200 dark:border-white/10">Berlaku</th>
                                <th scope="col" class="px-3 py-3 text-center w-24 border-b border-slate-200 dark:border-white/10">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="dark:bg-transparent">
                            <!-- Flat Mode -->
                            <template v-if="activeViewMode === 'flat'">
                                <!-- Empty state -->
                                <tr v-if="sortedRegulations.length === 0">
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data Regulasi. Silakan hubungi admin atau klik "Kelola Regulasi" untuk menambahkan.
                                    </td>
                                </tr>

                                <tr
                                    v-for="(reg, index) in sortedRegulations"
                                    :key="reg.id"
                                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                                >
                                    <!-- No -->
                                    <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-white/10 w-10">
                                        {{ index + 1 }}
                                    </td>

                                    <!-- Judul -->
                                    <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[300px] break-words text-slate-900 dark:text-white font-bold leading-relaxed">
                                        {{ reg.judul }}
                                    </td>

                                    <!-- Nomor -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[120px] break-words">
                                        {{ reg.nomor || '-' }}
                                    </td>

                                    <!-- Tipe -->
                                    <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-28">
                                        <span
                                            :class="[
                                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                                                reg.tipe === 'Policy'
                                                    ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400'
                                                    : reg.tipe === 'Procedure'
                                                        ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                                                        : reg.tipe === 'Standart'
                                                            ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                                            : reg.tipe === 'Surat Keterangan'
                                                                ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                                                : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                                            ]"
                                        >
                                            {{ reg.tipe }} - {{ reg.stk || '-' }}
                                        </span>
                                    </td>

                                    <!-- Fungsi / Owner -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[200px] break-words">
                                        {{ reg.owner }}
                                    </td>

                                    <!-- Organisasi — show org name -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[140px] break-words">
                                        <div class="flex flex-col gap-0.5">
                                            <span v-if="reg.master" class="font-semibold text-slate-900 dark:text-white">
                                                {{ reg.master.jabatan || reg.master.name }}
                                            </span>
                                            <span v-if="reg.organization" class="text-[10px] text-slate-500 dark:text-slate-400">
                                                {{ reg.organization.jabatan || reg.organization.name }}
                                            </span>
                                            <span v-if="!reg.master && !reg.organization">-</span>
                                        </div>
                                    </td>

                                    <!-- Revisi -->
                                    <td class="px-3 py-3 text-center font-mono font-semibold text-slate-800 dark:text-slate-200 border-r border-b border-slate-200 dark:border-white/10 w-16">
                                        {{ reg.revisi }}
                                    </td>

                                    <!-- Berlaku -->
                                    <td class="px-3 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs border-r border-b border-slate-200 dark:border-white/10 w-24">
                                        {{ formatDate(reg.berlaku) }}
                                    </td>

                                    <!-- Detail -->
                                    <td class="px-3 py-3 text-center border-b border-slate-200 dark:border-white/10 w-24">
                                        <button
                                            @click="handleDetailClick(reg)"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-bold text-white shadow-md shadow-[#821f44]/20 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/35 active:scale-95"
                                            title="Lihat Detail Kebijakan"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <!-- Document Hierarchy Mode -->
                            <template v-if="activeViewMode === 'document'">
                                <tr v-if="visibleDocRows.length === 0">
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data Regulasi.
                                    </td>
                                </tr>
                                <tr
                                    v-for="(row, index) in visibleDocRows"
                                    :key="'doc-' + row.id"
                                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 animate-fade-in"
                                >
                                    <!-- No -->
                                    <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-white/10 w-10">
                                        {{ index + 1 }}
                                    </td>
                                    <!-- Judul -->
                                    <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[300px] break-words" :style="{ paddingLeft: (row.depth * 24 + 12) + 'px' }">
                                        <div class="flex items-center gap-1.5">
                                            <!-- Toggle Button -->
                                            <button 
                                                v-if="row.hasChildren" 
                                                @click.stop="toggleDocExpand(row.id)" 
                                                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0"
                                            >
                                                <svg v-if="row.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                </svg>
                                            </button>
                                            <span v-else-if="row.depth > 0" class="text-slate-300 dark:text-white/20 mr-1 font-mono shrink-0">├─</span>
                                            
                                            <!-- Document Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            
                                            <span :class="[row.depth === 0 ? 'font-bold text-slate-900 dark:text-white' : 'font-medium text-slate-700 dark:text-slate-300']">
                                                {{ row.judul }}
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Nomor -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[120px] break-words">
                                        {{ row.nomor || '-' }}
                                    </td>
                                    <!-- Tipe -->
                                    <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-28">
                                        <span
                                            :class="[
                                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                                                row.tipe === 'Policy'
                                                    ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400'
                                                    : row.tipe === 'Procedure'
                                                        ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                                                        : row.tipe === 'Standart'
                                                            ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                                            : row.tipe === 'Surat Keterangan'
                                                                ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                                                : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                                            ]"
                                        >
                                            {{ row.tipe }} - {{ row.stk || '-' }}
                                        </span>
                                    </td>
                                    <!-- Fungsi / Owner -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[200px] break-words">
                                        {{ row.owner }}
                                    </td>
                                    <!-- Organisasi -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[140px] break-words">
                                        <div class="flex flex-col gap-0.5">
                                            <span v-if="row.master" class="font-semibold text-slate-900 dark:text-white">
                                                {{ row.master.jabatan || row.master.name }}
                                            </span>
                                            <span v-if="row.organization" class="text-[10px] text-slate-500 dark:text-slate-400">
                                                {{ row.organization.jabatan || row.organization.name }}
                                            </span>
                                            <span v-if="!row.master && !row.organization">-</span>
                                        </div>
                                    </td>
                                    <!-- Revisi -->
                                    <td class="px-3 py-3 text-center font-mono font-semibold text-slate-800 dark:text-slate-200 border-r border-b border-slate-200 dark:border-white/10 w-16">
                                        {{ row.revisi }}
                                    </td>
                                    <!-- Berlaku -->
                                    <td class="px-3 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs border-r border-b border-slate-200 dark:border-white/10 w-24">
                                        {{ formatDate(row.berlaku) }}
                                    </td>
                                    <!-- Detail -->
                                    <td class="px-3 py-3 text-center border-b border-slate-200 dark:border-white/10 w-24">
                                        <button
                                            @click="handleDetailClick(row)"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-bold text-white shadow-md shadow-[#821f44]/20 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/35 active:scale-95"
                                            title="Lihat Detail Kebijakan"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <!-- Organization Hierarchy Mode -->
                            <template v-if="activeViewMode === 'organization'">
                                <tr v-if="regulationsByOrgHierarchy.length === 0">
                                    <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data Regulasi.
                                    </td>
                                </tr>

                                <tr
                                    v-for="(reg, index) in regulationsByOrgHierarchy"
                                    :key="'org-flat-' + reg.id"
                                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150 animate-fade-in"
                                >
                                    <!-- No -->
                                    <td class="px-3 py-3 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-b border-slate-200 dark:border-white/10 w-10">
                                        {{ index + 1 }}
                                    </td>

                                    <!-- Judul -->
                                    <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 max-w-[300px] break-words text-slate-900 dark:text-white font-bold leading-relaxed">
                                        {{ reg.judul }}
                                    </td>

                                    <!-- Nomor -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[120px] break-words">
                                        {{ reg.nomor || '-' }}
                                    </td>

                                    <!-- Tipe -->
                                    <td class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10 w-28">
                                        <span
                                            :class="[
                                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                                                reg.tipe === 'Policy'
                                                    ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400'
                                                    : reg.tipe === 'Procedure'
                                                        ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                                                        : reg.tipe === 'Standart'
                                                            ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                                            : reg.tipe === 'Surat Keterangan'
                                                                ? 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'
                                                                : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                                            ]"
                                        >
                                            {{ reg.tipe }} - {{ reg.stk || '-' }}
                                        </span>
                                    </td>

                                    <!-- Fungsi / Owner -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[200px] break-words">
                                        {{ reg.owner }}
                                    </td>

                                    <!-- Organisasi — show org name -->
                                    <td class="px-3 py-3 text-slate-700 dark:text-slate-300 font-medium border-r border-b border-slate-200 dark:border-white/10 max-w-[140px] break-words">
                                        <div class="flex flex-col gap-0.5">
                                            <span v-if="reg.master" class="font-semibold text-slate-900 dark:text-white">
                                                {{ reg.master.jabatan || reg.master.name }}
                                            </span>
                                            <span v-if="reg.organization" class="text-[10px] text-slate-500 dark:text-slate-400">
                                                {{ reg.organization.jabatan || reg.organization.name }}
                                            </span>
                                            <span v-if="!reg.master && !reg.organization">-</span>
                                        </div>
                                    </td>

                                    <!-- Revisi -->
                                    <td class="px-3 py-3 text-center font-mono font-semibold text-slate-800 dark:text-slate-200 border-r border-b border-slate-200 dark:border-white/10 w-16">
                                        {{ reg.revisi }}
                                    </td>

                                    <!-- Berlaku -->
                                    <td class="px-3 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs border-r border-b border-slate-200 dark:border-white/10 w-24">
                                        {{ formatDate(reg.berlaku) }}
                                    </td>

                                    <!-- Detail -->
                                    <td class="px-3 py-3 text-center border-b border-slate-200 dark:border-white/10 w-24">
                                        <button
                                            @click="handleDetailClick(reg)"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-bold text-white shadow-md shadow-[#821f44]/20 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/35 active:scale-95"
                                            title="Lihat Detail Kebijakan"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        default: () => [],
    },
});

// View mode state
const activeViewMode = ref('flat'); // 'flat', 'document', 'organization'

// ─────────────────────────────────────────────────────────────────────────────
// Computed property to sort regulations hierarchically by organization code
// and compute their organization level based on the last non-zero digit.
// ─────────────────────────────────────────────────────────────────────────────
const sortedRegulations = computed(() => {
    const normalizeCode = (value) => String(value ?? '').trim();
    const padCode = (val) => {
        let str = normalizeCode(val);
        if (!str) return '';
        while (str.length < 7) {
            str += '0';
        }
        return str;
    };
    const getLevel = (codeStr) => {
        const padded = padCode(codeStr);
        if (!padded) return 1;
        for (let i = 6; i >= 0; i--) {
            if (padded[i] !== '0') {
                return i + 1;
            }
        }
        return 1;
    };

    const mapped = props.regulations.map(reg => {
        const orgCode = reg.organization?.code || '';
        return {
            ...reg,
            org_code: orgCode,
            org_level: getLevel(orgCode)
        };
    });

    return mapped.sort((a, b) => {
        const left = padCode(a.org_code);
        const right = padCode(b.org_code);

        if (left === '' && right === '') return 0;
        if (left === '') return 1; // puts empty code at the end
        if (right === '') return -1;

        const codeCompare = left.localeCompare(right, undefined, { numeric: false, sensitivity: 'base' });
        if (codeCompare !== 0) return codeCompare;

        // Fallback secondary sort
        return a.id - b.id;
    });
});

// Visual depth in grid hierarchy (0, 1, or 2 empty cells)
const getVisualDepth = (level) => {
    if (level <= 1) return 0;
    if (level <= 3) return 1;
    return 2;
};

// Badge color for the level indicator in the Organisasi column
function levelBadgeClass(level) {
    const map = {
        1: 'bg-slate-100   text-slate-600  dark:bg-white/10 dark:text-slate-300',
        2: 'bg-sky-100     text-sky-700    dark:bg-sky-500/10 dark:text-sky-400',
        3: 'bg-indigo-100  text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
        4: 'bg-violet-100  text-violet-700 dark:bg-violet-500/10 dark:text-violet-400',
        5: 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-500/10 dark:text-fuchsia-400',
        6: 'bg-rose-100    text-rose-700   dark:bg-rose-500/10 dark:text-rose-400',
        7: 'bg-orange-100  text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
    };
    return map[level] ?? map[1];
}

// ─────────────────────────────────────────────────────────────────────────────
// DOCUMENT HIERARCHY TREE
// ─────────────────────────────────────────────────────────────────────────────
const documentTree = computed(() => {
    const map = {};
    const roots = [];
    
    props.regulations.forEach(reg => {
        map[reg.id] = { ...reg, children: [] };
    });
    
    props.regulations.forEach(reg => {
        const mapped = map[reg.id];
        if (reg.parent_id && map[reg.parent_id]) {
            map[reg.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });
    
    return roots;
});

const expandedDocIds = ref(new Set());

const toggleDocExpand = (id) => {
    if (expandedDocIds.value.has(id)) {
        expandedDocIds.value.delete(id);
    } else {
        expandedDocIds.value.add(id);
    }
    expandedDocIds.value = new Set(expandedDocIds.value);
};

const visibleDocRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        const hasChildren = node.children && node.children.length > 0;
        const isExpanded = expandedDocIds.value.has(node.id);
        
        rows.push({
            ...node,
            depth,
            hasChildren,
            isExpanded
        });
        
        if (hasChildren && isExpanded) {
            node.children.forEach(child => {
                traverse(child, depth + 1);
            });
        }
    };
    
    documentTree.value.forEach(root => {
        traverse(root, 0);
    });
    
    return rows;
});

const initializeExpandedDocs = () => {
    const ids = new Set();
    props.regulations.forEach(reg => {
        const isParent = props.regulations.some(r => r.parent_id === reg.id);
        if (isParent) {
            ids.add(reg.id);
        }
    });
    expandedDocIds.value = ids;
};

// ─────────────────────────────────────────────────────────────────────────────
// ORGANIZATION HIERARCHY FLAT TABLE (Sorted by parent_id of TrsOrganization)
// ─────────────────────────────────────────────────────────────────────────────
const getOrgDepth = (orgId) => {
    if (!orgId) return 0;
    let depth = 0;
    let currentId = orgId;
    let iterations = 0;
    
    const orgMap = {};
    props.organizations.forEach(org => {
        orgMap[org.id] = org.parent_id;
    });

    while (currentId && orgMap[currentId] && iterations < 20) {
        currentId = orgMap[currentId];
        depth++;
        iterations++;
    }
    return depth;
};

const getVisualOrgDepth = (depth) => {
    if (depth <= 0) return 0;
    if (depth === 1) return 1;
    return 2; // Column 3 for depth >= 2
};

const regulationsByOrgHierarchy = computed(() => {
    // Helper function to sort regulations under a specific context hierarchically (parent to children)
    const sortLocalRegulations = (regs) => {
        const map = {};
        const roots = [];
        
        regs.forEach(reg => {
            map[reg.id] = { ...reg, children: [] };
        });
        
        regs.forEach(reg => {
            const mapped = map[reg.id];
            if (reg.parent_id && map[reg.parent_id]) {
                map[reg.parent_id].children.push(mapped);
            } else {
                roots.push(mapped);
            }
        });
        
        const sorted = [];
        const traverseRegs = (regNode) => {
            sorted.push(regNode);
            regNode.children.forEach(child => {
                traverseRegs(child);
            });
        };
        
        roots.forEach(root => {
            traverseRegs(root);
        });
        
        return sorted;
    };

    const map = {};
    const roots = [];
    
    props.organizations.forEach(org => {
        map[org.id] = { ...org, children: [], regulations: [] };
    });
    
    props.regulations.forEach(reg => {
        if (reg.pic_id && map[reg.pic_id]) {
            map[reg.pic_id].regulations.push(reg);
        }
    });
    
    props.organizations.forEach(org => {
        const mapped = map[org.id];
        if (org.parent_id && map[org.parent_id]) {
            map[org.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });
    
    const orderedRegs = [];
    
    const traverse = (node, depth = 0) => {
        const sortedNodeRegs = sortLocalRegulations(node.regulations);
        sortedNodeRegs.forEach(reg => {
            orderedRegs.push({
                ...reg,
                org_depth: depth
            });
        });
        
        node.children.forEach(child => {
            traverse(child, depth + 1);
        });
    };
    
    roots.forEach(root => {
        traverse(root, 0);
    });
    
    const unassigned = props.regulations.filter(reg => !reg.pic_id);
    const sortedUnassigned = sortLocalRegulations(unassigned);
    sortedUnassigned.forEach(reg => {
        orderedRegs.push({
            ...reg,
            org_depth: 0
        });
    });
    
    return orderedRegs;
});

// Initialize tree expansion states
initializeExpandedDocs();

// ─────────────────────────────────────────────────────────────────────────────
// Actions
// ─────────────────────────────────────────────────────────────────────────────
function handleDetailClick(reg) {
    const targetRoute = String(reg.tipe || '').toLowerCase() === 'procedure'
        ? 'policy.procedure.index'
        : 'policy.general.index';

    router.visit(route(targetRoute, { regulation_id: reg.id }));
}

// ---------------------------------------------------
// DATE FORMATTER HELPER
// ---------------------------------------------------
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return dateString;
        return d.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
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

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
