<template>
    <UserLayout title="Peran dan Tanggung Jawab">
        <div class="space-y-6 animate-fade-in-up print:m-0 print:p-0">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul || 'PEDOMAN TATA KELOLA TEKNOLOGI INFORMASI' }}</p>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Kelola Dokumen</h1>
                    </div>
                    <GuidanceChapterNavigation />
                </div>
            </section>

            <!-- External Sub Menu Switcher (print:hidden) -->
            <div class="print:hidden flex justify-center mb-6">
                <div class="inline-flex rounded-xl bg-slate-100 p-1 dark:bg-white/5 border border-slate-200/55 dark:border-white/10 shadow-sm">
                    <button 
                        @click="activeSubMenu = 'document'"
                        class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-200"
                        :class="[
                            activeSubMenu === 'document'
                                ? 'bg-white text-[#821f44] shadow-md shadow-[#821f44]/5 dark:bg-[#262626] dark:text-white'
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Daftar Peran & Tanggung Jawab</span>
                    </button>
                    <button 
                        @click="activeSubMenu = 'matrix'"
                        class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-200"
                        :class="[
                            activeSubMenu === 'matrix'
                                ? 'bg-white text-[#821f44] shadow-md shadow-[#821f44]/5 dark:bg-[#262626] dark:text-white'
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>Matriks Peran vs Aktor</span>
                    </button>
                    <button 
                        @click="activeSubMenu = 'policy_mapping'"
                        class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-200"
                        :class="[
                            activeSubMenu === 'policy_mapping'
                                ? 'bg-white text-[#821f44] shadow-md shadow-[#821f44]/5 dark:bg-[#262626] dark:text-white'
                                : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 0 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 12.44a7.9 7.9 0 1 1-7.558-7.558m0 0a7.9 7.9 0 0 1 7.558 7.558z" />
                        </svg>
                        <span>Mapping Tanggung Jawab vs Kebijakan</span>
                    </button>
                </div>
            </div>

            <!-- Dynamic Landscape print style injection when Matrix or Policy Mapping is active -->
            <component :is="'style'" v-if="activeSubMenu === 'matrix' || activeSubMenu === 'policy_mapping'">
                @media print {
                    @page {
                        size: A4 landscape !important;
                        margin: 1cm !important;
                    }
                    body {
                        background-color: white !important;
                        color: black !important;
                    }
                }
            </component>

            <!-- 1. A4 Portrait Document Page Preview -->
            <div v-show="activeSubMenu === 'document'" class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative text-justify print:shadow-none print:border-none print:p-0 print:m-0"
                style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #1e293b;">
                
                <!-- Formal Pertamina Document Grid Header -->
                <div class="border border-slate-900 dark:border-white text-[11px] text-slate-950 dark:text-white font-sans uppercase z-10 relative mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-900 dark:divide-white">
                        <!-- Left Column (Fungsi & Judul) -->
                        <div class="md:col-span-7 flex flex-col divide-y divide-slate-900 dark:divide-white">
                            <div class="p-3 flex items-start gap-1.5 min-h-[46px]">
                                <span class="font-bold shrink-0">FUNGSI :</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.owner || 'ENTERPRISE IT – DIREKTORAT PENUNJANG BISNIS' }}</span>
                            </div>
                            <div class="p-3 flex items-start gap-1.5 min-h-[46px]">
                                <span class="font-bold shrink-0">JUDUL :</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.judul || 'TATA KELOLA TEKNOLOGI INFORMASI' }}</span>
                            </div>
                        </div>
                        <!-- Right Column (Metadata) -->
                        <div class="md:col-span-5 flex flex-col divide-y divide-slate-900 dark:divide-white">
                            <div class="p-2.5 flex items-center gap-1.5 min-h-[23px]">
                                <span class="font-bold shrink-0">NOMOR :</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ activeRegulation ? `REG-${activeRegulation.id.toString().padStart(4, '0')}` : '-' }}</span>
                            </div>
                            <div class="p-2.5 flex items-center min-h-[23px]">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold shrink-0">REVISI KE :</span>
                                    <template v-if="[0, 1, 2, 3, 4].includes(parseInt(activeRegulation?.revisi))">
                                        <span v-for="num in [0, 1, 2, 3, 4]" :key="num" class="inline-flex items-center gap-1 mr-1">
                                            <span class="w-3.5 h-3.5 border border-slate-900 dark:border-white flex items-center justify-center text-[10px] font-black bg-transparent select-none animate-none">
                                                {{ parseInt(activeRegulation?.revisi) === num ? '✓' : '' }}
                                            </span>
                                            <span class="font-mono">{{ num }}</span>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span class="w-3.5 h-3.5 border border-slate-900 dark:border-white flex items-center justify-center text-[10px] font-black bg-transparent select-none mr-1">✓</span>
                                        <span class="font-bold text-slate-900 dark:text-white mr-2">{{ activeRegulation?.revisi || '0' }}</span>
                                    </template>
                                </div>
                            </div>
                            <div class="p-2.5 flex items-center gap-1.5 min-h-[23px]">
                                <span class="font-bold shrink-0">BERLAKU TMT :</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.berlaku ? formatDate(activeRegulation.berlaku) : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Document Title Section -->
                <div class="mt-16 text-center space-y-2 relative z-10">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                        BAB III
                    </h2>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        TUGAS, KEBIJAKAN DAN TANGGUNG JAWAB
                    </h2>
                </div>

                <!-- Part 1: Literal Document Content (Active on 'document' tab, always printed) -->
                <div class="mt-8 space-y-8 text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 font-serif">
                    <!-- Short Description Paragraph -->
                    <p class="text-justify font-serif text-[15px] leading-relaxed mb-6">
                        Tugas, kewenangan, dan tanggung jawab pada Pedoman ini merujuk pada peran yang dijelaskan pada kerangka COBIT 2019 sesuai dengan pemetaan peran dan tanggung jawab, serta diselaraskan dengan hasil pemetaan kebijakan Pedoman ini dengan pemetaan proses bisnis Perusahaan (SK 07/C00000/2021-08 tentang Model Proses Bisnis Pertamina Group).
                    </p>
                    
                    <div v-for="(role, idx) in roles" :key="role.id" class="space-y-3">
                        <!-- Role Title (A. Dewan Direksi (Board), B. Executive Committee, dsb) -->
                        <h4 class="text-[16px] font-bold text-slate-950 dark:text-white">
                            {{ getLetterNumbering(idx) }}. {{ role.name }}
                        </h4>

                        <!-- Role Description -->
                        <p v-if="role.description" class="text-slate-700 dark:text-slate-300 italic text-justify pl-4 font-sans text-[14px]">
                            {{ role.description }}
                        </p>

                        <!-- Responsibilities List -->
                        <div class="pl-4 space-y-1.5 mt-2">
                            <div 
                                v-for="(resp, respIdx) in role.responsibilities" 
                                :key="resp.id"
                                class="flex gap-3 items-start py-0"
                            >
                                <span class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right font-serif select-none">
                                    {{ respIdx + 1 }}
                                </span>
                                <p class="text-justify font-serif text-[15px]">
                                    {{ resp.content }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="roles.length === 0" class="text-center text-slate-400 py-12 font-sans">
                        Belum ada data Peran & Tanggung Jawab. Silakan klik "Kelola Peran & Tanggung Jawab" di atas untuk menambah data.
                    </div>
                </div>
            </div>

            <!-- 2. Widescreen Landscape Matrix Card -->
            <div v-show="activeSubMenu === 'matrix'" class="w-full bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 md:p-10 rounded-2xl relative text-justify print:shadow-none print:border-none print:p-0 print:m-0 overflow-visible"
                style="font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #1e293b;">
                
                <!-- Title Section inside Landscape Card -->
                <div class="text-center space-y-2 relative z-10">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        MATRIKS HUBUNGAN TANGGUNG JAWAB DAN AKTOR
                    </h2>
                </div>

                <!-- Matrix Section -->
                <div class="mt-8 space-y-6 font-sans">

                    <!-- Filter and Toolbar Section (print:hidden) -->
                    <div class="print:hidden flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50 dark:bg-white/5 p-4 rounded-xl border border-slate-200/60 dark:border-white/10 shadow-sm mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xs font-black uppercase tracking-wider text-[#821f44] dark:text-[#db588c]">Filter Jabatan:</span>
                            <div class="relative min-w-[220px]">
                                <select 
                                    v-model="selectedRoleFilter"
                                    class="w-full text-xs font-semibold bg-white border border-slate-300 rounded-lg px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:border-white/10 dark:text-white shadow-sm cursor-pointer"
                                >
                                    <option value="all">Semua Jabatan / Peran</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500">
                            Menampilkan <span class="font-bold text-[#821f44] dark:text-pink-400">{{ filteredResponsibles.length }}</span> butir tanggung jawab
                        </div>
                    </div>

                    <!-- Matrix Table Container -->
                    <div class="overflow-x-auto border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full bg-white dark:bg-[#1a1a1a] print:border-slate-300 print:shadow-none">
                        <table class="min-w-full border-collapse text-center">
                            <thead>
                                <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246]">
                                    <th class="p-2.5 text-center w-12 border-r border-[#b2cfc7] dark:border-[#255246] select-none text-[9.5px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        No
                                    </th>
                                    <th class="p-2.5 text-left w-[360px] min-w-[360px] border-r border-[#b2cfc7] dark:border-[#255246] select-none text-[9.5px] font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                        Butir tanggung jawab
                                    </th>
                                    <th 
                                        v-for="role in filteredRoles" 
                                        :key="role.id" 
                                        class="p-0.5 text-center w-[18px] min-w-[18px] max-w-[18px] align-bottom pb-2 select-none border-r border-[#b2cfc7] dark:border-[#255246] last:border-r-0"
                                        :title="getLetterNumbering(props.roles.findIndex(r => r.id === role.id)) + '. ' + role.name"
                                    >
                                        <div class="inline-flex items-center justify-center h-36 w-[14px] mx-auto">
                                            <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                                {{ getLetterNumbering(props.roles.findIndex(r => r.id === role.id)) }}. {{ role.name }}
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                                <tr v-for="(resp, rIdx) in filteredResponsibles" :key="resp.id" class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5 transition-colors print:hover:bg-transparent">
                                    <!-- Row Number (No) -->
                                    <td class="p-2.5 border-r border-slate-200 dark:border-white/10 text-center font-mono font-bold text-slate-500 dark:text-slate-400 text-xs">
                                        {{ rIdx + 1 }}
                                    </td>
                                    
                                    <!-- Responsibility statement (Butir Tanggung Jawab) -->
                                    <td class="p-2.5 border-r border-slate-200 dark:border-white/10 text-left font-serif text-[11px] leading-relaxed text-slate-900 dark:text-slate-100 text-justify">
                                        {{ resp.responsible }}
                                    </td>
                                    
                                    <!-- Matching Columns (Roles) -->
                                    <td v-for="role in filteredRoles" :key="role.id" class="p-0.5 text-center w-[18px] min-w-[18px] max-w-[18px] align-middle h-[20px] border-r border-slate-200 dark:border-white/10 last:border-r-0">
                                        <template v-if="isMapped(role, resp.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3.5 h-3.5 mx-auto text-[#821f44] dark:text-pink-400">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </template>
                                        <template v-else>
                                            <span class="text-transparent">—</span>
                                        </template>
                                    </td>
                                </tr>
                                <tr v-if="filteredResponsibles.length === 0">
                                    <td :colspan="2 + filteredRoles.length" class="p-8 text-center text-slate-400 dark:text-slate-500 font-sans text-xs">
                                        Tidak ada data butir tanggung jawab untuk jabatan ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Policy Mapping Editor tab -->
            <div v-show="activeSubMenu === 'policy_mapping'" class="w-full bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 md:p-10 rounded-2xl relative text-justify print:shadow-none print:border-none print:p-0 print:m-0 overflow-visible"
                style="font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #1e293b;">
                
                <!-- Transparent click overlay to close dropdown when clicking outside -->
                <div v-if="activeAddDropdownPracId !== null" class="fixed inset-0 z-30" @click="activeAddDropdownPracId = null"></div>

                <!-- Title Section -->
                <div class="text-center space-y-2 relative z-10 mb-6">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        PEMETAAN TANGGUNG JAWAB VS BUTIR KEBIJAKAN
                    </h2>
                </div>

                <!-- Toolbar and Filter section (print:hidden) -->
                <div class="print:hidden flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-50 dark:bg-white/5 p-4 rounded-xl border border-slate-200/60 dark:border-white/10 shadow-sm mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Domain Filter -->
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black uppercase tracking-wider text-[#821f44] dark:text-[#db588c]">Domain Kebijakan:</span>
                            <select 
                                v-model="selectedDomainFilter"
                                class="text-xs font-semibold bg-white border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:border-white/10 dark:text-white shadow-sm cursor-pointer"
                            >
                                <option value="all">Semua Domain</option>
                                <option value="EDM">EDM (Evaluate, Direct & Monitor)</option>
                                <option value="APO">APO (Align, Plan & Organize)</option>
                                <option value="BAI">BAI (Build, Acquire & Implement)</option>
                                <option value="DSS">DSS (Deliver, Service & Support)</option>
                                <option value="MEA">MEA (Monitor, Evaluate & Assess)</option>
                            </select>
                        </div>

                        <!-- Search input for policies -->
                        <div class="relative w-64">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                                </svg>
                            </span>
                            <input 
                                v-model="practiceSearchQuery"
                                type="text"
                                placeholder="Cari butir kebijakan..."
                                class="w-full pl-9 pr-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:border-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white shadow-sm"
                            />
                        </div>
                    </div>

                    <!-- Action Save Button -->
                    <div>
                        <button 
                            @click="submitMapping"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-[#821f44]/25 transition hover:bg-[#9c2552] disabled:opacity-60 active:scale-95"
                            :disabled="mappingForm.processing || isSavingMapping"
                        >
                            <span v-if="mappingForm.processing || isSavingMapping" class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                            Simpan Perubahan Pemetaan
                        </button>
                    </div>
                </div>

                <!-- Mapping Grid Table -->
                <div class="overflow-visible border border-slate-300 dark:border-white/10 rounded-xl shadow-sm max-w-full bg-white dark:bg-[#1a1a1a] print:border-slate-300 print:shadow-none">
                    <table class="min-w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 divide-x divide-[#b2cfc7] dark:divide-[#255246] border-b border-[#b2cfc7] dark:border-[#255246] text-xs uppercase tracking-wider font-bold">
                                <th class="p-3.5 text-center w-16 border-r border-[#b2cfc7] dark:border-[#255246]">
                                    No
                                </th>
                                <th class="p-3.5 text-center w-28 border-r border-[#b2cfc7] dark:border-[#255246]">
                                    Nomor Kebijakan
                                </th>
                                <th class="p-3.5 text-left border-r border-[#b2cfc7] dark:border-[#255246] w-80 min-w-[200px]">
                                    Kebijakan
                                </th>
                                <th class="p-3.5 text-left border-r border-[#b2cfc7] dark:border-[#255246] w-96 min-w-[240px]">
                                    Deskripsi
                                </th>
                                <th class="p-3.5 text-left min-w-[320px]">
                                    Mapping Kebijakan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 text-slate-800 dark:text-slate-200">
                            <tr v-for="(obj, oIdx) in filteredObjectivesForMap" :key="obj.objective_id" class="hover:bg-slate-50 dark:hover:bg-white/5 divide-x divide-slate-100 dark:divide-white/5 transition-colors print:hover:bg-transparent">
                                <!-- Row Number -->
                                <td class="p-3.5 border-r border-slate-200 dark:border-white/10 text-center font-mono font-bold text-slate-500 dark:text-slate-400 text-xs bg-slate-50/40 dark:bg-white/5 select-none">
                                    {{ oIdx + 1 }}
                                </td>

                                <!-- Nomor Kebijakan -->
                                <td class="p-3.5 border-r border-slate-200 dark:border-white/10 text-center font-mono font-bold text-[#821f44] dark:text-pink-400 text-xs align-top">
                                    {{ obj.objective_id }}
                                </td>

                                <!-- Kebijakan -->
                                <td class="p-3.5 border-r border-slate-200 dark:border-white/10 text-left font-sans text-xs leading-relaxed text-slate-900 dark:text-slate-100 font-bold align-top">
                                    {{ obj.objective }}
                                </td>

                                <!-- Deskripsi -->
                                <td class="p-3.5 border-r border-slate-200 dark:border-white/10 text-left font-sans text-[11px] leading-relaxed text-slate-500 dark:text-slate-400 italic align-top">
                                    {{ obj.objective_description || '-' }}
                                </td>

                                <!-- Selected Responsibilities Dropdown & Cards -->
                                <td class="p-3.5 align-top relative">
                                    <div class="flex flex-col gap-2">
                                        <!-- Trigger Dropdown button -->
                                        <div class="relative">
                                            <button 
                                                @click="toggleAddResponsibilityDropdown(obj.objective_id)"
                                                class="w-full text-left inline-flex items-center justify-between gap-2 rounded-xl border border-slate-300 dark:border-white/10 bg-white hover:bg-slate-50 dark:bg-[#1a1a1a] dark:hover:bg-white/5 px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-sm active:scale-95 transition-all"
                                            >
                                                <span class="truncate">
                                                    {{ getSelectedResponsiblesCountText(obj.objective_id) }}
                                                </span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                            
                                            <!-- Dropdown Menu -->
                                            <div 
                                                v-if="activeAddDropdownPracId === obj.objective_id" 
                                                class="absolute right-0 mt-1.5 z-40 w-80 max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#222] p-2 divide-y divide-slate-100 dark:divide-white/5 space-y-2"
                                            >
                                                <div class="px-2.5 py-1.5 flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wider select-none">
                                                    <span>Pilih Tanggung Jawab</span>
                                                    <span class="text-[#821f44] dark:text-pink-400 font-extrabold">{{ getMappedResponsiblesList(obj.objective_id).length }} Terpilih</span>
                                                </div>
                                                
                                                <!-- Search Input inside Dropdown -->
                                                <div class="p-1">
                                                    <input 
                                                        v-model="responsibilityDropdownSearch"
                                                        type="text"
                                                        placeholder="Cari butir tanggung jawab..."
                                                        class="w-full px-2.5 py-1 text-[11px] rounded-lg border border-slate-200 bg-white dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white shadow-inner"
                                                        @click.stop
                                                    />
                                                </div>
                                                
                                                <div class="py-1 max-h-48 overflow-y-auto space-y-0.5" @click.stop>
                                                    <div 
                                                        v-for="resp in filteredResponsiblesForDropdown"
                                                        :key="resp.id"
                                                        @click="toggleMapping(resp.id, obj.objective_id)"
                                                        class="w-full text-left px-2.5 py-2 text-xs font-semibold rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-start gap-2.5 cursor-pointer text-slate-700 dark:text-slate-300"
                                                    >
                                                        <span 
                                                            class="w-3.5 h-3.5 rounded border flex items-center justify-center text-[10px] font-black transition-all select-none shrink-0 mt-0.5"
                                                            :class="isPracticeMapped(resp.id, obj.objective_id)
                                                                ? 'bg-[#821f44] border-[#821f44] text-white shadow-sm'
                                                                : 'border-slate-300 dark:border-white/20 bg-transparent text-transparent'"
                                                        >
                                                            ✓
                                                        </span>
                                                        <span class="leading-relaxed font-medium text-[11px] text-justify">{{ resp.responsible }}</span>
                                                    </div>
                                                    <div v-if="filteredResponsiblesForDropdown.length === 0" class="px-3 py-4 text-center text-xs text-slate-400 dark:text-slate-500">
                                                        Tanggung jawab tidak ditemukan.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selected Cards display -->
                                        <div class="flex flex-col gap-1.5 mt-2">
                                            <div 
                                                v-for="resp in getMappedResponsiblesList(obj.objective_id)" 
                                                :key="resp.id"
                                                class="flex items-start justify-between gap-2.5 rounded-xl bg-slate-50/80 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-3 text-xs text-slate-800 dark:text-slate-200 shadow-sm"
                                            >
                                                <span class="font-serif leading-relaxed text-[11px] text-justify">{{ resp.responsible }}</span>
                                                <button 
                                                    @click.stop="toggleMapping(resp.id, obj.objective_id)"
                                                    class="hover:text-rose-600 text-slate-400 dark:hover:text-rose-400 focus:outline-none shrink-0 font-extrabold text-sm"
                                                    title="Hapus Pemetaan"
                                                >
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredObjectivesForMap.length === 0">
                                <td colspan="5" class="p-8 text-center text-slate-400 dark:text-slate-500 font-sans text-xs">
                                    Tidak ada data butir kebijakan untuk ditampilkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sticky bottom save action bar -->
                <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 dark:border-white/5 pt-6 print:hidden">
                    <button 
                        @click="submitMapping"
                        class="rounded-xl bg-[#821f44] px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition hover:bg-[#9c2552] disabled:opacity-60"
                        :disabled="mappingForm.processing || isSavingMapping"
                    >
                        <span v-if="mappingForm.processing || isSavingMapping" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                        Simpan Pemetaan
                    </button>
                </div>
            </div>

            <!-- Print Floating Control Bar for Easy Navigation (print:hidden) -->
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
                <!-- Go to Management CRUD page -->
                <Link 
                    :href="route('policy.roles.manage')" 
                    class="pointer-events-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#821f44] text-white shadow-2xl shadow-[#821f44]/30 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 active:scale-95"
                    title="Kelola Peran & Tanggung Jawab"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 transition-transform group-hover:rotate-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </Link>
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
                    <div v-if="localError" class="pointer-events-auto flex items-center gap-3 rounded-xl border border-rose-200 bg-white/90 p-4 text-sm text-[#991b1b] shadow-2xl backdrop-blur-sm dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ localError }}</span>
                    </div>
                </transition>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import GuidanceChapterNavigation from '@/Components/Regulation/GuidanceChapterNavigation.vue';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    responsibles: {
        type: Array,
        default: () => [],
    },
    objectives: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

// Selected Regulation state
const selectedRegulationId = ref(null);

// Sub menu active state for Bab III (document | matrix | policy_mapping)
const activeSubMenu = ref('document');

// Role/position filter state for matrix view ('all' or specific roleId)
const selectedRoleFilter = ref('all');

// Mapping state filters
const selectedDomainFilter = ref('all');
const practiceSearchQuery = ref('');

// Flattened list of all responsibilities across all roles
const allResponsibilities = computed(() => {
    const list = [];
    props.roles.forEach(role => {
        (role.responsibilities || []).forEach(resp => {
            list.push({
                id: resp.id,
                content: resp.content,
                roleId: role.id,
                roleName: role.name
            });
        });
    });
    return list;
});

// Filtered list of responsibles (blue box) based on active role filter selection
const filteredResponsibles = computed(() => {
    let list = [];
    if (selectedRoleFilter.value === 'all') {
        list = [...props.responsibles];
    } else {
        const selectedRole = props.roles.find(r => r.id === parseInt(selectedRoleFilter.value));
        if (!selectedRole || !selectedRole.mapped_responsibles) {
            list = [];
        } else {
            list = [...selectedRole.mapped_responsibles];
        }
    }

    // Sort responsibilities based on the order of their earliest mapped roles
    list.sort((a, b) => {
        let indexA = props.roles.findIndex(role => isMapped(role, a.id));
        if (indexA === -1) indexA = 9999;

        let indexB = props.roles.findIndex(role => isMapped(role, b.id));
        if (indexB === -1) indexB = 9999;

        if (indexA !== indexB) {
            return indexA - indexB;
        }
        return a.id - b.id;
    });

    return list;
});

// Filtered list of roles (columns) based on active role filter selection
const filteredRoles = computed(() => {
    if (selectedRoleFilter.value === 'all') {
        return props.roles;
    }
    return props.roles.filter(r => r.id === parseInt(selectedRoleFilter.value));
});

// Check if a role maps to a master responsible ID
function isMapped(role, responsibleId) {
    if (!role || !role.mapped_responsibles) return false;
    return role.mapped_responsibles.some(r => r.id === responsibleId);
}

// Extract short role name in parentheses, e.g. "Dewan Direksi (Board)" -> "Board"
function getShortRoleName(fullName) {
    if (!fullName) return '';
    const match = fullName.match(/\(([^)]+)\)/);
    if (match && match[1]) {
        return match[1];
    }
    return fullName;
}

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || null;
});

// Format Date helper
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

// Letter numbering utility for roles (A, B, C, ..., AA, BB, CC, ..., AAA, BBB, CCC, ...)
function getLetterNumbering(index) {
    const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    if (index < 0) return '';
    const char = letters[index % 26];
    const times = Math.floor(index / 26) + 1;
    return char.repeat(times);
}

function printDocument() {
    window.print();
}

// Scroll to top helper
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Mapping Logic for Tab 3 (Responsibility vs Practice/Kebijakan - Swapped)
const mappingState = ref({});
const isSavingMapping = ref(false);
const localSuccess = ref('');
const localError = ref('');
const activeAddDropdownPracId = ref(null);
const responsibilityDropdownSearch = ref('');

const mappingForm = useForm({
    mappings: []
});

const initMappingState = () => {
    const state = {};
    props.responsibles.forEach(resp => {
        state[resp.id] = {};
        if (resp.objectives) {
            resp.objectives.forEach(obj => {
                state[resp.id][obj.objective_id] = true;
            });
        }
    });
    mappingState.value = state;
};

// Initialize mapping state
initMappingState();

// Watch for changes in props.responsibles (e.g. after refresh/save)
watch(() => props.responsibles, () => {
    initMappingState();
}, { deep: true });

// Filtered objectives for the Y-axis rows based on search and selected domain filter
const filteredObjectivesForMap = computed(() => {
    const list = [];
    props.objectives.forEach(obj => {
        const domainMatch = selectedDomainFilter.value === 'all' || 
            (obj.domain && obj.domain.includes(selectedDomainFilter.value)) ||
            (obj.objective_id && obj.objective_id.startsWith(selectedDomainFilter.value));
        if (domainMatch) {
            list.push(obj);
        }
    });

    let result = [...list];
    if (practiceSearchQuery.value.trim()) {
        const query = practiceSearchQuery.value.toLowerCase();
        result = result.filter(obj => 
            obj.objective_id.toLowerCase().includes(query) || 
            (obj.objective && obj.objective.toLowerCase().includes(query))
        );
    }
    // Sort objectives by objective_id to be neat
    result.sort((a, b) => a.objective_id.localeCompare(b.objective_id));
    return result;
});

// Filtered responsibles for the dropdown list based on search
const filteredResponsiblesForDropdown = computed(() => {
    let list = [...props.responsibles];
    if (responsibilityDropdownSearch.value.trim()) {
        const query = responsibilityDropdownSearch.value.toLowerCase();
        list = list.filter(r => r.responsible && r.responsible.toLowerCase().includes(query));
    }
    return list;
});

// Check if a practice/objective is mapped to a master responsible ID
function isPracticeMapped(responsibleId, objectiveId) {
    return !!(mappingState.value[responsibleId] && mappingState.value[responsibleId][objectiveId]);
}

// Toggle mapping of an objective to a master responsible ID
function toggleMapping(responsibleId, objectiveId) {
    if (!mappingState.value[responsibleId]) {
        mappingState.value[responsibleId] = {};
    }
    mappingState.value[responsibleId][objectiveId] = !mappingState.value[responsibleId][objectiveId];
}

// Get the list of mapped responsibilities for an objective ID
function getMappedResponsiblesList(objectiveId) {
    const list = [];
    props.responsibles.forEach(resp => {
        if (isPracticeMapped(resp.id, objectiveId)) {
            list.push(resp);
        }
    });
    return list;
}

// Get button label for selected responsibilities count
function getSelectedResponsiblesCountText(objectiveId) {
    const count = getMappedResponsiblesList(objectiveId).length;
    return count > 0 ? `${count} Tanggung Jawab Terhubung` : 'Pilih Tanggung Jawab';
}

// Toggle add responsibility dropdown visibility
function toggleAddResponsibilityDropdown(objectiveId) {
    if (activeAddDropdownPracId.value === objectiveId) {
        activeAddDropdownPracId.value = null;
    } else {
        activeAddDropdownPracId.value = objectiveId;
        responsibilityDropdownSearch.value = '';
    }
}

// Submit mapping to the backend
function submitMapping() {
    isSavingMapping.value = true;
    localSuccess.value = '';
    localError.value = '';

    const mappings = [];
    props.responsibles.forEach(resp => {
        const objectiveIds = [];
        const respState = mappingState.value[resp.id] || {};
        Object.keys(respState).forEach(objId => {
            if (respState[objId]) {
                objectiveIds.push(objId);
            }
        });
        mappings.push({
            responsible_id: resp.id,
            objective_ids: objectiveIds
        });
    });

    mappingForm.mappings = mappings;

    mappingForm.post(route('policy.roles.responsible-practice.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isSavingMapping.value = false;
            localSuccess.value = 'Pemetaan Tanggung Jawab vs Kebijakan berhasil diperbarui!';
            setTimeout(() => {
                localSuccess.value = '';
            }, 3000);
        },
        onError: (err) => {
            isSavingMapping.value = false;
            localError.value = 'Gagal memperbarui pemetaan Tanggung Jawab vs Kebijakan.';
            setTimeout(() => {
                localError.value = '';
            }, 4000);
        }
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
