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

            <!-- Regulations Table Components -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                                <th scope="col" class="px-6 py-4">Judul</th>
                                <th scope="col" class="px-6 py-4">Nomor</th>
                                <th scope="col" class="px-6 py-4 w-36">Tipe</th>
                                <th scope="col" class="px-6 py-4">Fungsi</th>
                                <th scope="col" class="px-6 py-4">Organisasi</th>
                                <th scope="col" class="px-6 py-4 text-center w-24">Revisi</th>
                                <th scope="col" class="px-6 py-4 w-32">Terbit</th>
                                <th scope="col" class="px-6 py-4 w-32">Berlaku</th>
                                <th scope="col" class="px-6 py-4 text-center w-80">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                            <tr v-if="regulations.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data Regulasi. Silakan hubungi admin atau klik "Kelola Regulasi" untuk menambahkan.
                                </td>
                            </tr>
                            <tr 
                                v-for="(reg, index) in regulations" 
                                :key="reg.id" 
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                            >
                                <!-- No -->
                                <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                    {{ index + 1 }}
                                </td>
                                <!-- Judul -->
                                <td class="px-6 py-4 text-slate-900 dark:text-white font-bold leading-relaxed max-w-sm">
                                    {{ reg.judul }}
                                </td>
                                <!-- Nomor -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ reg.nomor || '-' }}
                                </td>
                                 <!-- Tipe -->
                                 <td class="px-6 py-4">
                                     <span 
                                         :class="[
                                             'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold border',
                                             reg.tipe === 'Policy' 
                                                 ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400' 
                                                 : reg.tipe === 'Procedure'
                                                     ? 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400'
                                                     : reg.tipe === 'Standart'
                                                         ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400'
                                                         : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400'
                                         ]"
                                     >
                                         {{ reg.tipe }} - {{ reg.stk || '-' }}
                                     </span>
                                 </td>
                                <!-- Owner -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ reg.owner }}
                                </td>
                                <!-- Organisasi -->
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ reg.organization?.name || '-' }}
                                </td>
                                <!-- Revisi -->
                                <td class="px-6 py-4 text-center font-mono font-semibold text-slate-800 dark:text-slate-200">
                                    {{ reg.revisi }}
                                </td>
                                <!-- Terbit -->
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">
                                    {{ formatDate(reg.terbit) }}
                                </td>
                                <!-- Berlaku -->
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">
                                    {{ formatDate(reg.berlaku) }}
                                </td>
                                <!-- Detail & Preview -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            @click="handleDetailClick(reg)"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#821f44] px-4 py-2 text-xs font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 active:scale-95"
                                            title="Lihat Detail Kebijakan"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Detail
                                        </button>
                                        <button 
                                            @click="handlePreviewClick(reg)"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                            title="Preview Dokumen Utuh"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c]">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Preview
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Teleport for Full Document Preview Modal -->
        <Teleport to="body">
            <transition name="fade">
                <div v-if="isPreviewModalOpen" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 print:p-0 print:bg-white">
                    <div class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl dark:bg-[#121212] border border-slate-200 dark:border-white/10 overflow-hidden flex flex-col h-[90vh] print:h-auto print:border-none print:shadow-none print:rounded-none animate-scale-up">
                        
                        <!-- Modal Header (Hidden in Print) -->
                        <div class="bg-[#821f44] p-4 text-white flex items-center justify-between sticky top-0 z-10 shrink-0 print:hidden">
                            <h3 class="text-base font-bold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Pratinjau Dokumen - {{ previewData?.regulation?.judul || 'Memuat...' }}
                            </h3>
                            <div class="flex items-center gap-3">
                                <button 
                                    v-if="previewData" 
                                    @click="printDocument" 
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-bold text-white transition active:scale-95"
                                    title="Cetak PDF"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                                    </svg>
                                    Cetak
                                </button>
                                <button @click="closePreviewModal" class="text-white/80 hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content Body -->
                        <div class="flex-1 overflow-y-auto p-8 sm:p-12 md:p-16 dark:bg-[#171717] print:overflow-visible print:p-0 print:m-0 print:bg-white" id="print-preview-container">
                            
                            <!-- Loading Spinner -->
                            <div v-if="isPreviewLoading" class="flex flex-col items-center justify-center py-32 space-y-4">
                                <div class="w-12 h-12 border-4 border-[#821f44] border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-sm text-slate-500 font-semibold dark:text-slate-400">Sedang memuat isi dokumen regulasi...</p>
                            </div>

                            <!-- Actual Document Rendering -->
                            <div v-else-if="previewData" class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] p-8 sm:p-12 md:p-16 border border-slate-200 dark:border-white/10 rounded-2xl relative text-justify print:border-none print:p-0 print:shadow-none print:bg-white text-slate-900 dark:text-slate-100" style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5;">
                                
                                <!-- Pertamina Document Grid Header -->
                                <PertaminaDocumentHeader :activeRegulation="previewData.regulation" class="flex-1" />

                                <!-- Render Content for Procedure (TKO) -->
                                <template v-if="previewData.tipe === 'Procedure'">
                                    <div class="mt-8 space-y-12">
                                        <!-- Dynamic TKO sections before order 4 (Bab I, II, III) -->
                                        <div v-for="sec in tkoBeforeSections" :key="sec.id" class="space-y-4">
                                            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                                {{ sec.label }}
                                            </h3>
                                            <div class="font-serif text-[15px] leading-relaxed space-y-2.5">
                                                <template v-if="sec.content">
                                                    <div 
                                                        v-for="(line, idx) in parseContentLines(sec.content)" 
                                                        :key="idx"
                                                        :class="[ line.type === 'list' ? 'flex gap-2 items-start' : '' ]"
                                                        :style="line.indent > 0 ? { paddingLeft: `${line.indent * 2.25}rem` } : {}"
                                                    >
                                                        <template v-if="line.type === 'list'">
                                                            <span class="font-bold select-none text-right min-w-[1.75rem] text-slate-950 dark:text-white font-sans">{{ line.marker }}</span>
                                                            <span class="flex-1 text-justify whitespace-pre-wrap">{{ line.text }}</span>
                                                        </template>
                                                        <template v-else-if="line.type === 'empty'">
                                                            <div class="h-3"></div>
                                                        </template>
                                                        <template v-else>
                                                            <span class="flex-1 text-justify whitespace-pre-wrap">{{ line.text }}</span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-slate-400 dark:text-slate-500 italic">Belum ada konten.</div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Bab IV: Fungsi Terkait (Actors) -->
                                        <div class="space-y-4">
                                            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                                IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                                            </h3>
                                            <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10 mt-4 font-sans text-xs">
                                                <table class="w-full border-collapse text-left">
                                                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                                        <tr>
                                                            <th class="px-6 py-3 w-20 text-center border-b border-slate-200 dark:border-white/10">No</th>
                                                            <th class="px-6 py-3 border-b border-slate-200 dark:border-white/10">Fungsi / Unit Organisasi / Jabatan</th>
                                                            <th class="px-6 py-3 border-b border-slate-200 dark:border-white/10">Jabatan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                                        <tr v-if="!previewData.actors || previewData.actors.length === 0">
                                                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">Belum ada data aktor terkait.</td>
                                                        </tr>
                                                        <tr v-for="(actor, index) in previewData.actors" :key="actor.id">
                                                            <td class="px-6 py-3 text-center font-medium">{{ index + 1 }}</td>
                                                            <td class="px-6 py-3 font-semibold text-slate-900 dark:text-white">{{ actor.name }}</td>
                                                            <td class="px-6 py-3 text-slate-700 dark:text-slate-300">
                                                                {{ actor.organization?.jabatan || '-' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Bab V: Prosedur (SOPs) -->
                                        <div class="space-y-4">
                                            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                                V. PROSEDUR
                                            </h3>
                                            <div class="mt-4 space-y-8 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 text-justify">
                                                <div v-if="!previewData.categories || previewData.categories.length === 0" class="text-slate-400 dark:text-slate-500 italic">Belum ada data SOP untuk regulasi ini.</div>
                                                <div v-for="cat in previewData.categories" :key="cat.id" class="space-y-3">
                                                    <h4 class="font-bold text-slate-950 dark:text-white font-sans text-sm">{{ cat.tipe }}</h4>
                                                    <div class="space-y-3">
                                                        <div v-for="(item, index) in getSopsForCategory(cat.id)" :key="item.id" class="flex gap-3 items-start pl-2">
                                                            <span class="font-bold min-w-[20px] text-right select-none font-sans text-sm">{{ index + 1 }}.</span>
                                                            <p class="whitespace-pre-line text-justify m-0 p-0">{{ item.description }}</p>
                                                        </div>
                                                        <div v-if="getSopsForCategory(cat.id).length === 0" class="text-slate-400 dark:text-slate-500 italic pl-6">Belum ada data SOP untuk kategori ini.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bab VI: Diagram Alir (Flowcharts) -->
                                        <div class="space-y-4">
                                            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                                VI. DIAGRAM ALIR
                                            </h3>
                                            <div class="mt-4 space-y-8 print:break-inside-avoid">
                                                <div v-if="!previewData.categories || previewData.categories.length === 0" class="text-slate-400 dark:text-slate-500 italic">Belum ada data diagram untuk regulasi ini.</div>
                                                <div v-for="cat in previewData.categories" :key="cat.id" class="overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#171717]">
                                                    <div class="border-b border-slate-200 px-5 py-3 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200 font-sans">{{ cat.tipe }}</h3>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <div class="min-w-[1200px] p-4">
                                                            <FlowChart 
                                                                :actors="previewData.actors" 
                                                                :sops="previewData.flowChartSops" 
                                                                readonly 
                                                                :flow-type="String(cat.id)" 
                                                                :categories="previewData.categories" 
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dynamic TKO sections after order 5 (Bab VII, VIII, IX) -->
                                        <div v-for="sec in tkoAfterSections" :key="sec.id" class="space-y-4">
                                            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 uppercase font-sans">
                                                {{ sec.label }}
                                            </h3>
                                            <div class="font-serif text-[15px] leading-relaxed space-y-2.5">
                                                <template v-if="sec.content">
                                                    <div 
                                                        v-for="(line, idx) in parseContentLines(sec.content)" 
                                                        :key="idx"
                                                        :class="[ line.type === 'list' ? 'flex gap-2 items-start' : '' ]"
                                                        :style="line.indent > 0 ? { paddingLeft: `${line.indent * 2.25}rem` } : {}"
                                                    >
                                                        <template v-if="line.type === 'list'">
                                                            <span class="font-bold select-none text-right min-w-[1.75rem] text-slate-950 dark:text-white font-sans">{{ line.marker }}</span>
                                                            <span class="flex-1 text-justify whitespace-pre-wrap">{{ line.text }}</span>
                                                        </template>
                                                        <template v-else-if="line.type === 'empty'">
                                                            <div class="h-3"></div>
                                                        </template>
                                                        <template v-else>
                                                            <span class="flex-1 text-justify whitespace-pre-wrap">{{ line.text }}</span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-slate-400 dark:text-slate-500 italic">Belum ada konten.</div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Render Content for Policy (General/Specific) -->
                                <template v-else>
                                    <div class="mt-8 space-y-12">
                                        
                                        <!-- Bab I: Pendahuluan -->
                                        <div class="space-y-4">
                                            <div class="text-center space-y-1">
                                                <h2 class="text-base sm:text-lg font-bold tracking-[0.15em] text-slate-950 dark:text-white uppercase font-sans">BAB I</h2>
                                                <h2 class="text-lg sm:text-xl font-bold tracking-[0.2em] text-slate-950 dark:text-white uppercase font-sans">PENDAHULUAN</h2>
                                            </div>
                                            <div class="font-serif text-[15px] leading-relaxed text-justify space-y-4">
                                                <p class="indent-8 text-justify">
                                                    Kata kunci yang sangat penting bagi suatu perusahaan agar mampu bertahan bahkan tumbuh dan berkembang, adalah pemanfaatan Informasi dan Teknologi (I&T). Dalam masyarakat informasi global saat ini, dimana informasi mengalir secara cepat tanpa terhalang oleh jarak dan waktu, menjadikan informasi sebagai aset yang sangat berharga dan dengan memanfaatkan teknologi secara optimal I&T merupakan aset yang sangat bernilai.
                                                </p>
                                                <p class="indent-8 text-justify">
                                                    Pada saat ini perubahan lingkungan bisnis yang sangat cepat dan persaingan usaha yang sangat ketat, manajemen Perusahaan semakin menaruh harapan yang tinggi terhadap peran fungsi organisasi Teknologi Informasi (TI), yang tercermin dari keinginan manajemen Perusahaan terhadap peningkatan kualitas, mempercepat waktu penyerahan, dan peningkatan layanan yang berkesinambungan serta senantiasa mengupayakan agar semua kegiatan tersebut dapat dilaksanakan secara efektif dan efisien.
                                                </p>
                                                <p class="indent-8 text-justify">
                                                    Sebagai antisipasi persaingan usaha yang sangat ketat sebagaimana disebut di atas, Perusahaan telah melakukan transformasi di segala bidang termasuk I&T dan lingkungan operasinya. Transformasi bidang I&T menuntut pengelolaan I&T yang baik, sejalan dengan bisnis perusahaan dan kepatuhan (compliance) dengan aturan yang berlaku, serta mempertimbangkan aspek resiko yang mungkin timbul dan dihadapi. Kepatuhan (compliance) dimaksud sebagai upaya perwujudan Good Corporate Governance Perusahaan.
                                                </p>
                                                <p class="indent-8 text-justify">
                                                    Di dalam Good Corporate Governance, Tata Kelola I&T (IT Governance) menjadi lebih berperan dan didefinisikan sebagai suatu struktur hubungan-hubungan dan proses-proses untuk mengarahkan dan mengendalikan Perusahaan agar mencapai tujuannya dengan menyeimbangkan resiko-resiko versus penghasilan/keuntungan melalui peran I&T.
                                                </p>
                                                <p class="indent-8 text-justify">
                                                    Fungsi organisasi TI harus memenuhi tuntutan kualitas dan keamanan informasi perusahaan, sebagaimana diberlakukan terhadap aset lainnya. Manajemen juga harus mengoptimalkan penggunaan sumber daya yang tersedia, termasuk data, sistem aplikasi, teknologi, fasilitas-fasilitas dan SDM. Dalam rangka melaksanakan tanggung jawab untuk mencapai tujuan perusahaan, manajemen harus mengerti dan memahami kondisi I&T Perusahaan serta menetapkan pengaturan dan pengendaliannya melalui kebijakan-kebijakan.
                                                </p>
                                                <p class="indent-8 text-justify">
                                                    Pedoman ini memuat kebijakan tentang kebutuhan manajemen untuk pengendalian, isu informasi, komunikasi dan teknologi untuk keperluan bisnis yang memenuhi kriteria keefektifan, keefisienan, kerahasiaan, keintegritasan, ketersediaan, keterpenuhan dan keandalan informasi yang dibutuhkan.
                                                </p>
                                                <p class="indent-8 text-justify">
                                                    Mempertimbangkan hal-hal di atas dan dengan adanya perkembangan bisnis, perubahan kebijakan dan perubahan organisasi Pertamina, maka dipandang perlu untuk menerbitkan suatu Pedoman baru serta mencabut dan menggantikan Pedoman No. A-002/I10000/2010-S0 Revisi Ke - 1 tentang Penyelenggaraan Tata Kelola TI Perusahaan (IT Governance).
                                                </p>

                                                <div class="space-y-2 mt-6">
                                                    <h3 class="font-bold text-slate-950 dark:text-white pb-1 font-sans text-sm">A. Tujuan</h3>
                                                    <p class="indent-8 text-justify font-serif">Pedoman ini disusun dengan tujuan:</p>
                                                    <ol class="list-decimal pl-8 space-y-1">
                                                        <li>Sebagai petunjuk untuk pelaksanaan tata kelola teknologi informasi dalam memenuhi persyaratan bisnis dan tata kelola I&T secara umum dengan pendekatan yang holistik sesuai struktur aturan penyelenggaraan Teknologi Informasi dari Kementerian BUMN Republik Indonesia.</li>
                                                        <li>Sebagai acuan dalam penyusunan Tata Kerja Organisasi (TKO), Tata Kerja Individu (TKI), Tata Kerja Penggunaan Alat (TKPA), dan Documented Information yang berkaitan dengan tata kelola TI di lingkungan Perusahaan.</li>
                                                    </ol>
                                                </div>

                                                <div class="space-y-2 mt-6">
                                                    <h3 class="font-bold text-slate-950 dark:text-white pb-1 font-sans text-sm">B. Ruang Lingkup</h3>
                                                    <p class="indent-8 text-justify font-serif">
                                                        Ruang lingkup Pedoman ini mencakup seluruh aspek pengelolaan I&T perusahaan, yang meliputi kegiatan perencanaan, pengadaan, pengembangan, pengoperasian, pemeliharaan, pengawasan, dan pembinaan informasi, komunikasi dan teknologi di Perusahaan. Pedoman ini berlaku di Pertamina Holding dan dapat dijadikan acuan atau diratifikasi oleh Subholding/ Anak Perusahaan/ Perusahaan Terafiliasi Pertamina lainnya dengan terlebih dahulu melakukan mekanisme internal berdasarkan Anggaran Dasarnya masing-masing dan memenuhi ketentuan pemberlakuan STK Holding.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bab II: Kebijakan (Umum & Khusus) -->
                                        <div class="space-y-6">
                                            <div class="text-center space-y-1">
                                                <h2 class="text-base sm:text-lg font-bold tracking-[0.15em] text-slate-950 dark:text-white uppercase font-sans">BAB II</h2>
                                                <h2 class="text-lg sm:text-xl font-bold tracking-[0.2em] text-slate-950 dark:text-white uppercase font-sans">KEBIJAKAN</h2>
                                            </div>

                                            <!-- Kebijakan Umum -->
                                            <div class="space-y-3">
                                                <h3 class="text-base font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 font-sans">
                                                    A. Kebijakan Umum
                                                </h3>
                                                <div class="mt-4 space-y-4 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 text-justify">
                                                    <div v-for="policy in previewData.policies" :key="policy.id" class="flex gap-3 items-start pl-2">
                                                        <span class="font-bold text-slate-950 dark:text-white min-w-[20px] text-right select-none font-sans text-sm">
                                                            {{ policy.number }}.
                                                        </span>
                                                        <p class="m-0 p-0 text-justify flex-1">
                                                            {{ formatDescription(policy.description) }}
                                                        </p>
                                                    </div>
                                                    <div v-if="!previewData.policies || previewData.policies.length === 0" class="text-slate-400 dark:text-slate-500 italic pl-2">
                                                        Kebijakan Umum tidak tersedia.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kebijakan Khusus -->
                                            <div class="space-y-3 mt-10">
                                                <h3 class="text-base font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10 font-sans">
                                                    B. Kebijakan Khusus
                                                </h3>
                                                <div class="mt-4 space-y-6 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100 text-justify pl-2">
                                                    <div v-for="(objectivesInDomain, domainName, domainIdx) in groupedObjectives" :key="domainName" class="space-y-4">
                                                        <div class="space-y-1">
                                                            <h3 class="font-bold text-slate-950 dark:text-white font-sans text-sm uppercase">
                                                                {{ domainIdx + 1 }}. {{ cleanDomainTitle(domainName) }}
                                                            </h3>
                                                            <p class="text-slate-700 dark:text-slate-300 italic text-justify pl-6 m-0 p-0">
                                                                {{ getDomainDescriptionByObjectives(objectivesInDomain) }}
                                                            </p>
                                                        </div>

                                                        <div v-for="(obj, objIdx) in objectivesInDomain" :key="obj.objective_id" class="pl-6 space-y-4">
                                                            <h4 class="font-bold text-slate-950 dark:text-white m-0 p-0 font-sans text-sm">
                                                                {{ objIdx + 1 }}) {{ obj.objective }}
                                                            </h4>

                                                            <!-- Deskripsi -->
                                                            <div v-if="obj.objective_description" class="pl-4 space-y-1">
                                                                <span class="font-bold text-slate-950 dark:text-white block font-sans text-[11px] uppercase tracking-wider text-slate-500">a. Deskripsi</span>
                                                                <div v-if="getListItems(obj.objective_description).length === 1" class="text-justify mt-0.5">
                                                                    {{ getListItems(obj.objective_description)[0] }}
                                                                </div>
                                                                <ol v-else class="list-none mt-0.5 space-y-1">
                                                                    <li v-for="(item, itemIdx) in getListItems(obj.objective_description)" :key="itemIdx" class="flex gap-2 text-justify">
                                                                        <span class="shrink-0 font-bold font-sans text-xs">{{ getLetterNumbering(itemIdx) }})</span>
                                                                        <span>{{ item }}</span>
                                                                    </li>
                                                                </ol>
                                                            </div>

                                                            <!-- Tujuan -->
                                                            <div v-if="obj.objective_purpose" class="pl-4 space-y-1">
                                                                <span class="font-bold text-slate-950 dark:text-white block font-sans text-[11px] uppercase tracking-wider text-slate-500">b. Tujuan</span>
                                                                <div v-if="getListItems(obj.objective_purpose).length === 1" class="text-justify mt-0.5">
                                                                    {{ getListItems(obj.objective_purpose)[0] }}
                                                                </div>
                                                                <ol v-else class="list-none mt-0.5 space-y-1">
                                                                    <li v-for="(item, itemIdx) in getListItems(obj.objective_purpose)" :key="itemIdx" class="flex gap-2 text-justify">
                                                                        <span class="shrink-0 font-bold font-sans text-xs">{{ getLetterNumbering(itemIdx) }})</span>
                                                                        <span>{{ item }}</span>
                                                                    </li>
                                                                </ol>
                                                            </div>

                                                            <!-- Butir Kebijakan -->
                                                            <div v-if="obj.practices && obj.practices.length > 0" class="pl-4 space-y-1">
                                                                <span class="font-bold text-slate-950 dark:text-white block font-sans text-[11px] uppercase tracking-wider text-slate-500">c. Butir Kebijakan</span>
                                                                <ol class="list-none mt-0.5 space-y-1">
                                                                    <li v-for="(prac, pracIdx) in obj.practices" :key="prac.practice_id" class="flex gap-2 text-justify">
                                                                        <span class="shrink-0 font-bold font-sans text-xs">{{ pracIdx + 1 }})</span>
                                                                        <span>
                                                                            {{ prac.practice_name }}
                                                                            <span v-if="prac.practice_description"> — {{ prac.practice_description }}</span>
                                                                        </span>
                                                                    </li>
                                                                </ol>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bab III: Tanggung Jawab (Roles) -->
                                        <div class="space-y-4">
                                            <div class="text-center space-y-1">
                                                <h2 class="text-base sm:text-lg font-bold tracking-[0.15em] text-slate-950 dark:text-white uppercase font-sans">BAB III</h2>
                                                <h2 class="text-lg sm:text-xl font-bold tracking-[0.2em] text-slate-950 dark:text-white uppercase font-sans">TANGGUNG JAWAB</h2>
                                            </div>
                                            <div class="font-serif text-[15px] leading-relaxed text-justify space-y-6 pl-2">
                                                <div v-for="(role, roleIdx) in previewData.roles" :key="role.id" class="space-y-2">
                                                    <h3 class="font-bold text-slate-950 dark:text-white font-sans text-base">
                                                        {{ roleIdx + 1 }}. {{ role.name }}
                                                    </h3>
                                                    <p class="text-justify italic pl-4 text-slate-700 dark:text-slate-300">
                                                        {{ role.description }}
                                                    </p>
                                                    <div class="pl-4">
                                                        <span class="font-bold text-slate-950 dark:text-white font-sans text-[11px] uppercase tracking-wider text-slate-500 mb-1 block">Tanggung Jawab:</span>
                                                        <ul class="list-disc pl-5 space-y-1 text-justify">
                                                            <li v-if="!role.responsibilities || role.responsibilities.length === 0" class="text-slate-400 dark:text-slate-500 italic">Belum ada tanggung jawab yang didefinisikan.</li>
                                                            <li v-for="resp in role.responsibilities" :key="resp.id">
                                                                {{ resp.content }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bab IV: Penutup -->
                                        <div class="space-y-4">
                                            <div class="text-center space-y-1">
                                                <h2 class="text-base sm:text-lg font-bold tracking-[0.15em] text-slate-950 dark:text-white uppercase font-sans">BAB IV</h2>
                                                <h2 class="text-lg sm:text-xl font-bold tracking-[0.2em] text-slate-950 dark:text-white uppercase font-sans">PENUTUP</h2>
                                            </div>
                                            <div class="font-serif text-[15px] leading-relaxed text-justify space-y-6 pl-2">
                                                <div class="space-y-2">
                                                    <h3 class="text-base font-bold text-slate-950 dark:text-white font-sans border-b border-slate-900/10 pb-2 dark:border-white/10">
                                                        1. Ketentuan Peralihan
                                                    </h3>
                                                    <p class="indent-8 text-justify font-serif">
                                                        Sejak ditetapkannya pedoman ini, seluruh ketentuan tata kelola dan operasional TI yang berlaku di lingkungan PT Pertamina (Persero), Subholding, dan Anak Perusahaan wajib menyesuaikan proses bisnisnya dengan pedoman ini dalam masa transisi paling lambat 6 (enam) bulan terhitung sejak tanggal pemberlakuan pedoman ini.
                                                    </p>
                                                    <p class="indent-8 text-justify font-serif">
                                                        Dokumen teknis berupa Standard Operating Procedure (SOP), Instruksi Kerja (IK), dan Ketentuan Pelaksana lainnya yang tidak bertentangan dengan prinsip-prinsip dasar pedoman ini dinyatakan tetap berlaku sampai dilakukannya revisi lanjutan atau penggantian dokumen terkait.
                                                    </p>
                                                </div>

                                                <div class="space-y-2">
                                                    <h3 class="text-base font-bold text-slate-950 dark:text-white font-sans border-b border-slate-900/10 pb-2 dark:border-white/10">
                                                        2. Pengawasan Kepatuhan
                                                    </h3>
                                                    <p class="indent-8 text-justify font-serif">
                                                        Fungsi Enterprise IT Korporat bersama dengan Audit Internal bertanggung jawab untuk secara berkala mengawasi, mengevaluasi, dan mengaudit kepatuhan (compliance) implementasi butir-butir kebijakan dalam pedoman ini di seluruh tingkatan organisasi. Hasil audit kepatuhan tata kelola TI akan dilaporkan secara langsung kepada Direksi dan menjadi salah satu indikator evaluasi kinerja strategis teknologi informasi Perusahaan.
                                                    </p>
                                                </div>

                                                <div class="space-y-2">
                                                    <h3 class="text-base font-bold text-slate-950 dark:text-white font-sans border-b border-slate-900/10 pb-2 dark:border-white/10">
                                                        3. Ketentuan Penutup
                                                    </h3>
                                                    <p class="indent-8 text-justify font-serif">
                                                        Pedoman Tata Kelola Teknologi Informasi ini akan ditinjau secara periodik minimal setiap 2 (dua) tahun sekali atau sewaktu-waktu sesuai kebutuhan strategis perusahaan serta perkembangan regulasi eksternal pemerintah. Perubahan dan penyempurnaan atas pedoman ini harus disetujui dan disahkan secara formal oleh Dewan Direksi PT Pertamina (Persero).
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </Teleport>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import UserLayout from '@/Layouts/UserLayout.vue';
import FlowChart from '@/Components/Procedure/FlowChart.vue';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';

defineProps({
    regulations: {
        type: Array,
        required: true,
    },
});

// Preview states
const isPreviewModalOpen = ref(false);
const isPreviewLoading = ref(false);
const previewData = ref(null);

function handleDetailClick(reg) {
    const targetRoute = String(reg.tipe || '').toLowerCase() === 'procedure'
        ? 'policy.procedure.index'
        : 'policy.general.index';

    router.visit(route(targetRoute, { regulation_id: reg.id }));
}

function handlePreviewClick(reg) {
    isPreviewModalOpen.value = true;
    isPreviewLoading.value = true;
    previewData.value = null;

    axios.get(route('regulation.preview', reg.id))
        .then(response => {
            previewData.value = response.data;
            isPreviewLoading.value = false;
        })
        .catch(error => {
            isPreviewLoading.value = false;
            isPreviewModalOpen.value = false;
            console.error('Failed to load regulation preview:', error);
            Swal.fire({
                title: 'Gagal Memuat!',
                text: 'Terjadi kesalahan saat mengambil data preview regulasi.',
                icon: 'error',
                confirmButtonColor: '#821f44'
            });
        });
}

function closePreviewModal() {
    isPreviewModalOpen.value = false;
    previewData.value = null;
}

function printDocument() {
    window.print();
}

// ---------------------------------------------------
// TKO (PROCEDURE) COMPUTED SEGMENTS
// ---------------------------------------------------
const tkoBeforeSections = computed(() => {
    if (!previewData.value || !previewData.value.tkoSections) return [];
    const romanNumerals = { 1: 'I', 2: 'II', 3: 'III' };
    return previewData.value.tkoSections
        .filter(s => s.order < 4)
        .map(s => ({
            id: s.id,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            content: s.contents?.[0]?.content || ''
        }));
});

const tkoAfterSections = computed(() => {
    if (!previewData.value || !previewData.value.tkoSections) return [];
    const romanNumerals = { 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX' };
    return previewData.value.tkoSections
        .filter(s => s.order > 5)
        .map(s => ({
            id: s.id,
            label: `${romanNumerals[s.order] || s.order}. ${s.name.toUpperCase()}`,
            content: s.contents?.[0]?.content || ''
        }));
});

function getSopsForCategory(categoryId) {
    if (!previewData.value || !previewData.value.sop) return [];
    return previewData.value.sop.filter(s => Number(s.category_id) === Number(categoryId));
}

// ---------------------------------------------------
// POLICY (GENERAL/SPECIFIC) COMPUTED & HELPERS
// ---------------------------------------------------
const groupedObjectives = computed(() => {
    if (!previewData.value || !previewData.value.objectives) return {};
    const groups = {};
    previewData.value.objectives.forEach(obj => {
        const domain = obj.domain || 'Domain Lainnya';
        if (!groups[domain]) {
            groups[domain] = [];
        }
        groups[domain].push(obj);
    });
    return groups;
});

function cleanDomainTitle(domain) {
    if (!domain) return '';
    return domain.replace(/\s*\(.*\)\s*$/, '').trim();
}

const domainDescriptions = {
    'EDM': 'Kebijakan ini berkaitan dengan aktivitas mengevaluasi opsi strategis, mengarahkan manajemen puncak pada opsi strategis yang dipilih dan memantau pencapaian strategi.',
    'APO': 'Kebijakan ini berkaitan dengan aktivitas menyelaraskan strategi TI dengan strategi bisnis, merencanakan arah pengembangan TI, dan mengatur organisasi serta sumber daya pendukung.',
    'BAI': 'Kebijakan ini berkaitan dengan aktivitas membangun, mengakuisisi, menerapkan solusi teknologi informasi secara aman dan efektif, serta mengintegrasikan solusi tersebut ke dalam proses bisnis.',
    'DSS': 'Kebijakan ini berkaitan dengan aktivitas pemberian layanan TI, operasional harian, pengelolaan keamanan dan kelangsungan layanan, serta penyediaan dukungan bagi pengguna.',
    'MEA': 'Kebijakan ini berkaitan dengan aktivitas memantau, mengevaluasi, dan mengukur kinerja serta kepatuhan tata kelola TI perusahaan terhadap standar internal maupun eksternal.'
};

function getDomainDescriptionByObjectives(objectivesList) {
    if (!objectivesList || objectivesList.length === 0) return '';
    const firstId = objectivesList[0].objective_id;
    if (firstId.startsWith('EDM')) return domainDescriptions['EDM'];
    if (firstId.startsWith('APO')) return domainDescriptions['APO'];
    if (firstId.startsWith('BAI')) return domainDescriptions['BAI'];
    if (firstId.startsWith('DSS')) return domainDescriptions['DSS'];
    if (firstId.startsWith('MEA')) return domainDescriptions['MEA'];
    return '';
}

function getListItems(text) {
    if (!text) return [];
    return text.split('\n')
        .map(line => line.trim())
        .filter(line => line.length > 0)
        .map(line => line.replace(/^([a-z0-9A-Z]+[\)\.]|[\-\*])\s*/i, ''));
}

function getLetterNumbering(index) {
    const letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    return letters[index] || (index + 1).toString();
}

function formatDescription(text) {
    if (!text) return '';
    return text.replace(/\s+([a-z])([\.\)])\s+/g, '\n   $1$2 ');
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

// ---------------------------------------------------
// TKO CONTENT LINES PARSER
// ---------------------------------------------------
function parseContentLines(content) {
    if (!content) return [];
    const lines = String(content).replace(/\r\n/g, '\n').split('\n');
    const parsed = [];
    
    const listPattern = /^(\s*)([0-9]+|[a-zA-Z]|[ivxIVX]+)([\.\)\-])\s+(.*)$/;
    const bulletPattern = /^(\s*)([\-\•\*\+])\s+(.*)$/;
    
    let currentListIndent = 0;
    
    lines.forEach((line) => {
        if (!line.trim()) {
            parsed.push({ type: 'empty', text: '', indent: 0 });
            currentListIndent = 0;
            return;
        }
        
        const listMatch = line.match(listPattern);
        if (listMatch) {
            const [_, spaces, num, separator, text] = listMatch;
            let listIndent = Math.floor(spaces.length / 2);
            
            const isSubList = /^[a-zA-Z]$/.test(num) || /^[ivxIVX]+$/.test(num);
            if (listIndent === 0 && currentListIndent > 0 && isSubList) {
                listIndent = currentListIndent;
            }
            
            parsed.push({
                type: 'list',
                marker: `${num}${separator}`,
                text: text,
                indent: listIndent
            });
            
            currentListIndent = listIndent + 1;
            return;
        }
        
        const bulletMatch = line.match(bulletPattern);
        if (bulletMatch) {
            const [_, spaces, bullet, text] = bulletMatch;
            let bulletIndent = Math.floor(spaces.length / 2);
            
            if (bulletIndent === 0 && currentListIndent > 0) {
                bulletIndent = currentListIndent;
            }
            
            parsed.push({
                type: 'list',
                marker: bullet,
                text: text,
                indent: bulletIndent
            });
            
            currentListIndent = bulletIndent + 1;
            return;
        }
        
        const spaceMatch = line.match(/^(\s+)(.*)$/);
        if (spaceMatch) {
            const [_, spaces, text] = spaceMatch;
            const indentLevel = Math.min(Math.floor(spaces.length / 2), 6);
            parsed.push({
                type: 'text',
                text: text,
                indent: indentLevel > 0 ? indentLevel : 1
            });
            currentListIndent = indentLevel;
            return;
        }
        
        parsed.push({
            type: 'text',
            text: line,
            indent: currentListIndent
        });
    });
    return parsed;
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

.animate-scale-up {
    animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
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

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
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

@media print {
    /* Hide all components by default on print */
    body * {
        visibility: hidden !important;
    }
    
    /* Explicitly show only the preview modal contents and its subelements */
    #print-preview-container, 
    #print-preview-container * {
        visibility: visible !important;
    }

    #print-preview-container {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        background: white !important;
        color: black !important;
    }
}
</style>
