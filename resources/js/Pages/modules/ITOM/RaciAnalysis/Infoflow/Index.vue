<template>
    <ModulLayout title="Information Flow Analysis">
        <div class="space-y-6 animate-fade-in-up print:m-0 print:p-0">
            <!-- Sleek Action Bar at Top (print:hidden) -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">
                            Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)
                        </p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Information Flow Analysis
                        </h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('itom.operating-model.raci-analysis.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            Matriks RACI
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Main Page Panel -->
            <div class="w-full bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-4 sm:p-6 md:p-8 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 overflow-visible">
                
                <!-- Page Title -->
                <div class="text-center space-y-1.5 relative z-10 py-4 mb-6">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#821f44] dark:text-[#a83262] uppercase">
                        GAMO Information Flow Analysis
                    </h2>
                </div>

                <!-- Selector & General Filters (print:hidden) -->
                <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between border-b border-slate-200 dark:border-white/10 pb-6 mb-6 gap-4 print:hidden">
                    <div class="flex flex-col sm:flex-row gap-4 items-center flex-1">
                        <!-- Objective selector -->
                        <div class="w-full sm:w-80">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                Pilih Governance & Management Objective
                            </label>
                            <select
                                v-model="selectedObjectiveId"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm transition focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            >
                                <option value="">-- Pilih GAMO --</option>
                                <optgroup
                                    v-for="group in gamoGroups"
                                    :key="group.domain"
                                    :label="group.domain"
                                    class="text-slate-400 dark:text-zinc-500 font-bold bg-white dark:bg-[#1a1a1a]"
                                >
                                    <option
                                        v-for="item in group.items"
                                        :key="item.id"
                                        :value="item.id"
                                        class="text-slate-800 dark:text-slate-200 font-semibold"
                                    >
                                        {{ item.id }} - {{ item.name }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Practice sub-filter selector -->
                        <div class="w-full sm:w-80">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                Filter Practice
                            </label>
                            <select
                                v-model="selectedPracticeId"
                                :disabled="!selectedObjectiveId || !objectiveData.practices || objectiveData.practices.length === 0"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm transition focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white disabled:opacity-50"
                            >
                                <option value="">Semua Practice</option>
                                <option
                                    v-for="p in objectiveData.practices"
                                    :key="p.practice_id"
                                    :value="p.practice_id"
                                >
                                    {{ cleanText(p.practice_id) }} - {{ cleanText(p.practice_name) }}
                                </option>
                            </select>
                        </div>
                    </div>


                    <!-- View Mode Toggle -->
                    <div class="flex items-center">
                        <div class="inline-flex rounded-xl border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/5 p-1 gap-1">
                            <button
                                @click="viewMode = 'table'"
                                :class="[
                                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200',
                                    viewMode === 'table'
                                        ? 'bg-white dark:bg-[#1a1a1a] text-[#821f44] dark:text-[#a83262] shadow-sm border border-slate-200 dark:border-white/10'
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                                ]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M11.25 12h-7.5m8.625 0c.621 0 1.125.504 1.125 1.125" />
                                </svg>
                                Mode Tabel
                            </button>
                            <button
                                @click="viewMode = 'diagram'"
                                :class="[
                                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200',
                                    viewMode === 'diagram'
                                        ? 'bg-white dark:bg-[#1a1a1a] text-[#821f44] dark:text-[#a83262] shadow-sm border border-slate-200 dark:border-white/10'
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
                                ]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                </svg>
                                Diagram Aliran
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex flex-col items-center justify-center p-24 border border-dashed border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 animate-pulse">
                    <div class="inline-block w-9 h-9 border-4 border-[#821f44] border-t-transparent rounded-full animate-spin"></div>
                    <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                        Memuat diagram aliran informasi COBIT 2019...
                    </p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="p-12 border border-red-200 dark:border-red-950/30 rounded-2xl bg-red-50/50 dark:bg-red-950/10 flex flex-col items-center text-center">
                    <div class="h-12 w-12 rounded-full bg-red-100 dark:bg-red-950/50 flex items-center justify-center text-red-600 dark:text-red-400 mb-4 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Gagal Memuat Referensi Aliran Informasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-md">{{ error }}</p>
                    <button 
                        @click="fetchInfoflowData"
                        class="mt-5 px-4 py-2 bg-[#821f44] hover:bg-[#9c2552] text-white font-bold text-xs rounded-xl shadow-md transition active:scale-95 flex items-center gap-1.5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Coba Lagi
                    </button>
                </div>

                <!-- Empty State (No selection) -->
                <div v-else-if="!selectedObjectiveId" class="p-20 border border-dashed border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 text-center flex flex-col items-center">
                    <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-5 border border-slate-200 dark:border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H3.75A2.25 2.25 0 0 0 1.5 4.5v15a2.25 2.25 0 0 0 2.25 2.25h12.75a2.25 2.25 0 0 0 2.25-2.25V14.25z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">
                        Pilih Governance & Management Objective
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-sm">
                        Gunakan dropdown di atas untuk memilih GAMO dan melihat alur data input, practice, output, dan RACI.
                    </p>
                </div>

                <!-- Flow Visualizer Loaded State -->
                <div v-else class="space-y-12">
                    
                    <!-- Meta Info Panel -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-[#1a1a1a]/50 p-6 flex flex-col md:flex-row gap-5 items-start">
                        <div class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-slate-500/5 blur-xl"></div>
                        
                        <!-- Domain Badge -->
                        <div 
                            class="flex-shrink-0 w-16 h-16 rounded-full flex flex-col items-center justify-center font-extrabold text-sm border shadow-sm"
                            :class="domainColorClasses"
                        >
                            <span class="text-[10px] tracking-widest opacity-80 uppercase">{{ domainCode }}</span>
                            <span class="text-lg -mt-1">{{ domainNumber }}</span>
                        </div>

                        <!-- Objective Metadata -->
                        <div class="space-y-1.5 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-[#821f44]/10 dark:bg-[#821f44]/20 text-[#821f44] dark:text-rose-300 tracking-wider">
                                    {{ cleanText(objectiveData.objective_id) }}
                                </span>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                                    {{ cleanText(objectiveData.objective) }}
                                </h3>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                {{ cleanText(objectiveData.objective_description) }}
                            </p>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════════════════ -->
                    <!-- MODE: TABEL (existing table view) -->
                    <!-- ═══════════════════════════════════════════════════ -->
                    <template v-if="viewMode === 'table'">
                        <div v-if="filteredPractices.length > 0" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                            <table class="min-w-[1200px] w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10">
                                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                    <tr class="divide-x divide-slate-200 dark:divide-white/10 border-b border-slate-200 dark:border-white/10">
                                        <th scope="col" class="w-12 px-3 py-3 text-center border-r border-slate-200 dark:border-white/10">No</th>
                                        <th scope="col" class="w-[28%] px-4 py-3 border-r border-slate-200 dark:border-white/10">Butir Kebijakan</th>
                                        <th scope="col" class="w-[24%] px-4 py-3 border-r border-slate-200 dark:border-white/10">Input</th>
                                        
                                        <!-- Dynamic Role Columns -->
                                        <th 
                                            v-for="role in activeRoles" 
                                            :key="role.id" 
                                            class="p-0.5 text-center w-[24px] min-w-[24px] max-w-[24px] align-bottom pb-2 select-none bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 border-r border-[#b2cfc7] dark:border-[#255246]"
                                            :title="role.name"
                                        >
                                            <div class="inline-flex items-center justify-center h-36 w-[14px] mx-auto">
                                                <span class="[writing-mode:vertical-lr] rotate-180 text-[8px] font-black uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                        </th>

                                        <th scope="col" class="w-[24%] px-4 py-3">Output</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                    <tr
                                        v-for="(practice, idx) in filteredPractices"
                                        :key="practice.practice_id"
                                        class="align-top hover:bg-slate-50/40 dark:hover:bg-white/5 divide-x divide-slate-200 dark:divide-white/10"
                                    >
                                        <!-- No -->
                                        <td class="px-3 py-4 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-white/10">
                                            {{ idx + 1 }}
                                        </td>
                                        
                                        <!-- Butir Kebijakan -->
                                        <td class="px-4 py-4 border-r border-slate-200 dark:border-white/10">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="rounded bg-purple-100 px-2 py-0.5 text-[9px] font-black uppercase tracking-wider text-purple-700 dark:bg-purple-950/50 dark:text-purple-300">
                                                        {{ cleanText(practice.practice_id) }}
                                                    </span>
                                                    <h4 class="text-[12px] font-extrabold text-slate-900 dark:text-white leading-tight">
                                                        {{ cleanText(practice.practice_name) }}
                                                    </h4>
                                                </div>
                                                <p v-if="practice.practice_description" class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                                                    {{ cleanText(practice.practice_description) }}
                                                </p>
                                            </div>
                                        </td>
                                        
                                        <!-- Input -->
                                        <td class="px-4 py-4 border-r border-slate-200 dark:border-white/10">
                                            <div class="space-y-3">
                                                <div v-for="input in practice.inputs" :key="input.input_id" class="space-y-1 bg-slate-50/50 dark:bg-white/5 p-2 rounded-lg border border-slate-100/50 dark:border-white/5">
                                                    <span
                                                        class="inline-block rounded px-1.5 py-0.5 text-[9px] font-bold"
                                                        :class="cleanText(input.from) === 'Outside COBIT'
                                                            ? 'bg-slate-200 text-slate-700 dark:bg-zinc-700 dark:text-slate-300'
                                                            : 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300'"
                                                    >
                                                        Dari: 
                                                        <template v-for="(token, tIdx) in parseCobitTokens(input.from)" :key="tIdx">
                                                            <span
                                                                v-if="token.isId"
                                                                class="cursor-pointer hover:underline font-extrabold"
                                                                @click="navigateToCode(token.text)"
                                                            >{{ token.text }}</span>
                                                            <span v-else>{{ token.text }}</span>
                                                        </template>
                                                    </span>
                                                    <p class="whitespace-pre-line text-[11px] leading-relaxed text-slate-700 dark:text-slate-300 font-semibold">
                                                        {{ cleanText(input.description) }}
                                                    </p>
                                                </div>
                                                <div v-if="!practice.inputs || practice.inputs.length === 0" class="text-[11px] italic text-slate-400 dark:text-slate-600">
                                                    Tidak ada input
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Dynamic Role RACI badges -->
                                        <td 
                                            v-for="role in activeRoles" 
                                            :key="role.id" 
                                            class="p-0 text-center w-[24px] min-w-[24px] max-w-[24px] align-middle h-[20px] border-r border-slate-200 dark:border-white/10 bg-[#fbfdfb] dark:bg-[#14231b]"
                                        >
                                            <template v-if="getPracticeRaciValue(practice, role.id)">
                                                <span 
                                                    v-if="getPracticeRaciValue(practice, role.id).toUpperCase() === 'I'"
                                                    class="inline-block w-[3px] h-[12px] bg-[#1e293b] dark:bg-slate-200 rounded-[0.5px] align-middle"
                                                    :title="`${practice.practice_id} - ${role.name}: Informed`"
                                                ></span>
                                                <span 
                                                    v-else
                                                    :class="getRaciTextClass(getPracticeRaciValue(practice, role.id))"
                                                    :title="`${practice.practice_id} - ${role.name}: ${getRaciLabel(getPracticeRaciValue(practice, role.id))}`"
                                                >
                                                    {{ getPracticeRaciValue(practice, role.id) }}
                                                </span>
                                            </template>
                                        </td>
                                        
                                        <!-- Output -->
                                        <td class="px-4 py-4">
                                            <div class="space-y-3">
                                                <div v-for="output in practice.outputs" :key="output.output_id" class="space-y-1 bg-slate-50/50 dark:bg-white/5 p-2 rounded-lg border border-slate-100/50 dark:border-white/5">
                                                    <span
                                                        class="inline-block rounded bg-pink-100 px-1.5 py-0.5 text-[9px] font-bold text-pink-700 dark:bg-pink-900/50 dark:text-pink-300"
                                                    >
                                                        Ke: 
                                                        <template v-for="(token, tIdx) in parseCobitTokens(output.to)" :key="tIdx">
                                                            <span
                                                                v-if="token.isId"
                                                                class="cursor-pointer hover:underline font-extrabold"
                                                                @click="navigateToCode(token.text)"
                                                            >{{ token.text }}</span>
                                                            <span v-else>{{ token.text }}</span>
                                                        </template>
                                                    </span>
                                                    <p class="whitespace-pre-line text-[11px] leading-relaxed text-slate-700 dark:text-slate-300 font-semibold">
                                                        {{ cleanText(output.description) }}
                                                    </p>
                                                </div>
                                                <div v-if="!practice.outputs || practice.outputs.length === 0" class="text-[11px] italic text-slate-400 dark:text-slate-600">
                                                    Tidak ada output
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="p-12 border border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 text-center flex flex-col items-center">
                            <div class="h-10 w-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-3 border border-slate-200 dark:border-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak ada practice yang sesuai dengan filter.</p>
                        </div>
                    </template>

                    <!-- ═══════════════════════════════════════════════════ -->
                    <!-- MODE: DIAGRAM ALIRAN (Table-Flow Layout) -->
                    <!-- ═══════════════════════════════════════════════════ -->
                    <template v-if="viewMode === 'diagram'">
                        <!-- Diagram Sub-Toggle: Per Practice vs Keseluruhan -->
                        <div class="flex items-center justify-between">
                            <div class="inline-flex rounded-lg border border-slate-200 dark:border-white/10 bg-slate-100/80 dark:bg-white/5 p-0.5 gap-0.5">
                                <button
                                    @click="diagramScope = 'per-practice'"
                                    :class="[
                                        'px-3 py-1.5 text-[10px] font-bold rounded-md transition-all duration-200 uppercase tracking-wider',
                                        diagramScope === 'per-practice'
                                            ? 'bg-white dark:bg-[#1a1a1a] text-[#821f44] dark:text-[#a83262] shadow-sm'
                                            : 'text-slate-400 dark:text-slate-500 hover:text-slate-600'
                                    ]"
                                >
                                    Per Practice
                                </button>
                                <button
                                    @click="diagramScope = 'keseluruhan'"
                                    :class="[
                                        'px-3 py-1.5 text-[10px] font-bold rounded-md transition-all duration-200 uppercase tracking-wider',
                                        diagramScope === 'keseluruhan'
                                            ? 'bg-white dark:bg-[#1a1a1a] text-[#821f44] dark:text-[#a83262] shadow-sm'
                                            : 'text-slate-400 dark:text-slate-500 hover:text-slate-600'
                                    ]"
                                >
                                    Keseluruhan
                                </button>
                            </div>
                        </div>

                        <!-- ── Per Practice Flow Diagrams ── -->
                        <template v-if="diagramScope === 'per-practice'">
                            <div v-if="filteredPractices.length > 0" class="space-y-8">
                                <div 
                                    v-for="(practice, pIdx) in filteredPractices" 
                                    :key="practice.practice_id"
                                    class="overflow-x-auto"
                                >
                                    <!-- Flow Row: INPUT table → arrow → PRACTICE → arrow → OUTPUT table -->
                                    <div class="flow-row">
                                        <!-- ▸ INPUT TABLE -->
                                        <div class="flow-section flow-section--input">
                                            <table class="flow-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2" class="flow-table__title flow-table__title--input">INPUT</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="flow-table__th flow-table__th--input" style="width: 35%;">FROM</th>
                                                        <th class="flow-table__th flow-table__th--input">DESCRIPTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="!practice.inputs || practice.inputs.length === 0">
                                                        <td colspan="2" class="flow-table__td flow-table__td--empty">—</td>
                                                    </tr>
                                                    <tr v-for="(inp, iIdx) in practice.inputs" :key="inp.input_id || iIdx">
                                                        <td class="flow-table__td flow-table__td--from">
                                                            <template v-for="(token, tIdx) in parseCobitTokens(inp.from)" :key="tIdx">
                                                                <span
                                                                    v-if="token.isId"
                                                                    class="cursor-pointer hover:underline font-bold text-blue-600 dark:text-blue-400"
                                                                    @click="navigateToCode(token.text)"
                                                                >{{ token.text }}</span>
                                                                <span v-else>{{ token.text }}</span>
                                                            </template>
                                                        </td>
                                                        <td class="flow-table__td">{{ cleanText(inp.description) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- ▸ ARROW → -->
                                        <div class="flow-connector">
                                            <div class="flow-connector__line flow-connector__line--blue"></div>
                                            <div class="flow-connector__arrow flow-connector__arrow--blue">▶</div>
                                        </div>

                                        <!-- ▸ PRACTICE CENTER -->
                                        <div class="flow-section flow-section--practice">
                                            <table class="flow-table">
                                                <thead>
                                                    <tr>
                                                        <th class="flow-table__title flow-table__title--practice">PRACTICE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="flow-table__td flow-table__td--practice">
                                                            <div class="flow-practice-content">
                                                                <span class="flow-practice-content__id">{{ cleanText(practice.practice_id) }}</span>
                                                                <span class="flow-practice-content__name">{{ cleanText(practice.practice_name) }}</span>
                                                                
                                                                <!-- RACI Role Assignments -->
                                                                <div v-if="practice.role_assignments && Object.values(practice.role_assignments).some(a => a && a.raci)" class="flow-practice-raci-list">
                                                                    <template v-for="(assign, roleId) in practice.role_assignments" :key="roleId">
                                                                        <div v-if="assign && assign.raci" class="flow-practice-raci-item">
                                                                            <span class="flow-raci-badge" :class="getRaciBadgeClass(assign.raci)">{{ assign.raci }}</span>
                                                                            <span class="flow-raci-role">{{ cleanText(assign.role_name) }}</span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- ▸ ARROW → -->
                                        <div class="flow-connector">
                                            <div class="flow-connector__line flow-connector__line--pink"></div>
                                            <div class="flow-connector__arrow flow-connector__arrow--pink">▶</div>
                                        </div>

                                        <!-- ▸ OUTPUT TABLE -->
                                        <div class="flow-section flow-section--output">
                                            <table class="flow-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2" class="flow-table__title flow-table__title--output">OUTPUT</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="flow-table__th flow-table__th--output">DESCRIPTION</th>
                                                        <th class="flow-table__th flow-table__th--output" style="width: 35%;">TO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="!practice.outputs || practice.outputs.length === 0">
                                                        <td colspan="2" class="flow-table__td flow-table__td--empty">—</td>
                                                    </tr>
                                                    <tr v-for="(out, oIdx) in practice.outputs" :key="out.output_id || oIdx">
                                                        <td class="flow-table__td">{{ cleanText(out.description) }}</td>
                                                        <td class="flow-table__td flow-table__td--to">
                                                            <template v-for="(token, tIdx) in parseCobitTokens(out.to)" :key="tIdx">
                                                                <span
                                                                    v-if="token.isId"
                                                                    class="cursor-pointer hover:underline font-bold text-pink-600 dark:text-pink-400"
                                                                    @click="navigateToCode(token.text)"
                                                                >{{ token.text }}</span>
                                                                <span v-else>{{ token.text }}</span>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-12 border border-dashed border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 text-center">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak ada practice yang sesuai dengan filter.</p>
                            </div>
                        </template>

                        <!-- ── Keseluruhan Flow Diagram ── -->
                        <template v-if="diagramScope === 'keseluruhan'">
                            <div v-if="filteredPractices.length > 0" class="overflow-x-auto">
                                <div class="flow-row">
                                    <!-- ▸ ALL INPUTS TABLE -->
                                    <div class="flow-section flow-section--input">
                                        <table class="flow-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="flow-table__title flow-table__title--input">INPUT ({{ allUniqueInputs.length }})</th>
                                                </tr>
                                                <tr>
                                                    <th class="flow-table__th flow-table__th--input" style="width: 35%;">FROM</th>
                                                    <th class="flow-table__th flow-table__th--input">DESCRIPTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="allUniqueInputs.length === 0">
                                                    <td colspan="2" class="flow-table__td flow-table__td--empty">—</td>
                                                </tr>
                                                <tr v-for="(inp, i) in allUniqueInputs" :key="'ki-' + i">
                                                    <td class="flow-table__td flow-table__td--from">
                                                        <template v-for="(token, tIdx) in parseCobitTokens(inp.from)" :key="tIdx">
                                                            <span
                                                                v-if="token.isId"
                                                                class="cursor-pointer hover:underline font-bold text-blue-600 dark:text-blue-400"
                                                                @click="navigateToCode(token.text)"
                                                            >{{ token.text }}</span>
                                                            <span v-else>{{ token.text }}</span>
                                                        </template>
                                                    </td>
                                                    <td class="flow-table__td">{{ inp.label }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- ▸ ARROW → -->
                                    <div class="flow-connector">
                                        <div class="flow-connector__line flow-connector__line--blue"></div>
                                        <div class="flow-connector__arrow flow-connector__arrow--blue">▶</div>
                                    </div>

                                    <!-- ▸ GAMO OBJECTIVE CENTER -->
                                    <div class="flow-section flow-section--practice">
                                        <table class="flow-table">
                                            <thead>
                                                <tr>
                                                    <th class="flow-table__title flow-table__title--practice">GAMO OBJECTIVE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="flow-table__td flow-table__td--practice">
                                                        <div class="flow-practice-content">
                                                            <span class="flow-practice-content__id">{{ cleanText(objectiveData.objective_id) }}</span>
                                                            {{ cleanText(objectiveData.objective) }}
                                                        </div>
                                                        <div class="flow-practice-list">
                                                            <div v-for="p in filteredPractices" :key="p.practice_id" class="flow-practice-list__item">
                                                                <span class="flow-practice-list__dot"></span>
                                                                <div class="flex-1 min-w-0">
                                                                    <span class="font-semibold block text-slate-800 dark:text-slate-200">{{ cleanText(p.practice_id) }} {{ cleanText(p.practice_name) }}</span>
                                                                    <!-- RACI Role Assignments for this practice -->
                                                                    <div v-if="p.role_assignments && Object.values(p.role_assignments).some(a => a && a.raci)" class="flow-practice-raci-list">
                                                                        <template v-for="(assign, roleId) in p.role_assignments" :key="roleId">
                                                                            <div v-if="assign && assign.raci" class="flow-practice-raci-item">
                                                                                <span class="flow-raci-badge" :class="getRaciBadgeClass(assign.raci)">{{ assign.raci }}</span>
                                                                                <span class="flow-raci-role">{{ cleanText(assign.role_name) }}</span>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- ▸ ARROW → -->
                                    <div class="flow-connector">
                                        <div class="flow-connector__line flow-connector__line--pink"></div>
                                        <div class="flow-connector__arrow flow-connector__arrow--pink">▶</div>
                                    </div>

                                    <!-- ▸ ALL OUTPUTS TABLE -->
                                    <div class="flow-section flow-section--output">
                                        <table class="flow-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="flow-table__title flow-table__title--output">OUTPUT ({{ allUniqueOutputs.length }})</th>
                                                </tr>
                                                <tr>
                                                    <th class="flow-table__th flow-table__th--output">DESCRIPTION</th>
                                                    <th class="flow-table__th flow-table__th--output" style="width: 35%;">TO</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="allUniqueOutputs.length === 0">
                                                    <td colspan="2" class="flow-table__td flow-table__td--empty">—</td>
                                                </tr>
                                                <tr v-for="(out, i) in allUniqueOutputs" :key="'ko-' + i">
                                                    <td class="flow-table__td">{{ out.label }}</td>
                                                    <td class="flow-table__td flow-table__td--to">
                                                        <template v-for="(token, tIdx) in parseCobitTokens(out.to)" :key="tIdx">
                                                            <span
                                                                v-if="token.isId"
                                                                class="cursor-pointer hover:underline font-bold text-pink-600 dark:text-pink-400"
                                                                @click="navigateToCode(token.text)"
                                                            >{{ token.text }}</span>
                                                            <span v-else>{{ token.text }}</span>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-12 border border-dashed border-slate-200 dark:border-white/10 rounded-2xl bg-slate-50/50 dark:bg-white/5 text-center">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak ada data aliran untuk ditampilkan.</p>
                            </div>
                        </template>
                    </template>
                </div>

            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';

// Selected GAMO ID (e.g. 'EDM01')
const selectedObjectiveId = ref('');
const selectedPracticeId = ref('');
const loading = ref(false);
const error = ref(null);

// View mode: 'table' | 'diagram'
const viewMode = ref('table');
// Diagram scope: 'per-practice' | 'keseluruhan'
const diagramScope = ref('per-practice');

// Objective Details state fetched from the live COBIT API
const objectiveData = ref({
    objective_id: '',
    objective: '',
    objective_description: '',
    practices: []
});

// Computed property to filter practices
const filteredPractices = computed(() => {
    if (!selectedPracticeId.value) {
        return objectiveData.value.practices || [];
    }
    return (objectiveData.value.practices || []).filter(
        p => p.practice_id === selectedPracticeId.value
    );
});

const activeRoles = computed(() => {
    const rolesMap = {};
    filteredPractices.value.forEach(practice => {
        if (practice.role_assignments) {
            Object.entries(practice.role_assignments).forEach(([roleId, assign]) => {
                if (assign && assign.raci) {
                    rolesMap[roleId] = {
                        id: roleId,
                        name: cleanText(assign.role_name)
                    };
                }
            });
        }
    });
    return Object.values(rolesMap).sort((a, b) => {
        const idA = parseInt(a.id, 10);
        const idB = parseInt(b.id, 10);
        if (!isNaN(idA) && !isNaN(idB)) {
            return idA - idB;
        }
        return String(a.id).localeCompare(String(b.id));
    });
});

// Grouped GAMO objectives for selector dropdown
const gamoGroups = [
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

// Helper to extract domain prefix (e.g. 'EDM', 'APO')
const domainCode = computed(() => {
    if (!selectedObjectiveId.value) return '';
    return selectedObjectiveId.value.substring(0, 3).toUpperCase();
});

// Helper to extract domain number (e.g. '01', '02')
const domainNumber = computed(() => {
    if (!selectedObjectiveId.value) return '';
    return selectedObjectiveId.value.substring(3);
});

// Stylized colors based on selected domain
const domainColorClasses = computed(() => {
    const code = domainCode.value;
    if (code === 'EDM') {
        return 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/20 dark:text-red-300 dark:border-red-900/40';
    } else if (code === 'APO') {
        return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-300 dark:border-blue-900/40';
    } else if (code === 'BAI') {
        return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900/40';
    } else if (code === 'DSS') {
        return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-300 dark:border-amber-900/40';
    } else if (code === 'MEA') {
        return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/20 dark:text-purple-300 dark:border-purple-900/40';
    }
    return 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700/50';
});

// Fetch detailed GAMO flow data from the live API
async function fetchInfoflowData() {
    if (!selectedObjectiveId.value) {
        objectiveData.value = { objective_id: '', objective: '', objective_description: '', practices: [] };
        return;
    }
    loading.value = true;
    error.value = null;

    try {
        const url = `https://cobit2019.divusi.co.id/api/cobit/gamo-infoflow?objective_id=${encodeURIComponent(selectedObjectiveId.value)}`;
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Koneksi server gagal dengan status ${response.status}`);
        }
        const dataJson = await response.json();
        if (!dataJson.success) {
            throw new Error(dataJson.message || 'API response returned success: false');
        }

        // Populated data
        const obj = dataJson.objectives && dataJson.objectives[0];
        if (obj) {
            objectiveData.value = obj;
        } else {
            throw new Error('Data Objective tidak ditemukan di respons server.');
        }
    } catch (err) {
        console.error('Error fetching infoflow:', err);
        error.value = err.message || 'Gagal memuat data referensi aliran informasi COBIT.';
    } finally {
        loading.value = false;
        if (pendingPracticeId.value && objectiveData.value && objectiveData.value.practices) {
            const hasPractice = objectiveData.value.practices.some(
                p => p.practice_id === pendingPracticeId.value
            );
            if (hasPractice) {
                selectedPracticeId.value = pendingPracticeId.value;
            } else {
                selectedPracticeId.value = '';
            }
            pendingPracticeId.value = '';
        }
    }
}

// ─── Diagram: unique inputs/outputs for keseluruhan mode ───
const allUniqueInputs = computed(() => {
    const practices = filteredPractices.value;
    if (!practices || practices.length === 0) return [];
    const map = new Map();
    practices.forEach(p => {
        (p.inputs || []).forEach(inp => {
            const key = `${cleanText(inp.from)}::${cleanText(inp.description)}`;
            if (!map.has(key)) {
                map.set(key, {
                    label: cleanText(inp.description),
                    from: cleanText(inp.from),
                    subtitle: `Dari: ${cleanText(inp.from)}`,
                });
            }
        });
    });
    return Array.from(map.values());
});

const allUniqueOutputs = computed(() => {
    const practices = filteredPractices.value;
    if (!practices || practices.length === 0) return [];
    const map = new Map();
    practices.forEach(p => {
        (p.outputs || []).forEach(out => {
            const key = `${cleanText(out.to)}::${cleanText(out.description)}`;
            if (!map.has(key)) {
                map.set(key, {
                    label: cleanText(out.description),
                    to: cleanText(out.to),
                    subtitle: `Ke: ${cleanText(out.to)}`,
                });
            }
        });
    });
    return Array.from(map.values());
});

watch(selectedObjectiveId, () => {
    selectedPracticeId.value = ''; // Reset practice sub-filter on objective change
    fetchInfoflowData();
});

// RACI pill border / background classes
function getRaciRoleContainerClass(raci) {
    const val = String(raci).toUpperCase();
    if (val === 'A') {
        return 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40';
    } else if (val === 'R') {
        return 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/40';
    } else if (val === 'C') {
        return 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40';
    }
    return 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/40';
}

// RACI badge color classes
function getRaciBadgeClass(raci) {
    const val = String(raci).toUpperCase();
    if (val === 'A') {
        return 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300';
    } else if (val === 'R') {
        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
    } else if (val === 'C') {
        return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
    }
    return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300';
}

function getPracticeRaciValue(practice, roleId) {
    if (practice.role_assignments && practice.role_assignments[roleId]) {
        return practice.role_assignments[roleId].raci || '';
    }
    return '';
}

function getRaciLabel(val) {
    if (!val) return '';
    const labels = {
        'A': 'Accountable',
        'R': 'Responsible',
        'C': 'Consulted',
        'I': 'Informed',
    };
    return labels[val.toUpperCase()] || '';
}

function getRaciTextClass(val) {
    if (!val) return '';
    const cleanVal = val.toUpperCase();
    if (cleanVal === 'A') {
        return "text-[#d9383a] dark:text-[#f87171] font-black text-[11px] select-none";
    }
    if (cleanVal === 'R') {
        return "text-[#2d9a68] dark:text-[#34d399] font-black text-[11px] select-none";
    }
    if (cleanVal === 'C') {
        return "text-[#dd7d36] dark:text-[#fbbf24] font-black text-[11px] select-none";
    }
    return "text-slate-800 dark:text-slate-200 font-bold text-[10.5px] select-none";
}

// Clean escaped double quotes and extra spacing/escapes from text
function cleanText(val) {
    if (!val) return '';
    return String(val)
        .replace(/^["'\\\s]+|["'\\\s]+$/g, '')
        .replace(/\\"/g, '"')
        .trim();
}

const pendingPracticeId = ref('');

function isCobitId(val) {
    if (!val) return false;
    const clean = cleanText(val).toUpperCase();
    return /^([A-Z]{3,4}\d{2})(?:\.\d{2})?$/.test(clean);
}

function navigateToCode(code) {
    if (!code) return;
    const clean = cleanText(code).toUpperCase();
    const match = clean.match(/^([A-Z]{3,4}\d{2})(?:\.\d{2})?$/);
    if (match) {
        const objectiveId = match[1]; // e.g. "MEA03"
        const practiceId = match[0];  // e.g. "MEA03.02" or "MEA03"
        
        // Find if this objectiveId belongs to gamoGroups
        const allObjectiveIds = gamoGroups.flatMap(group => group.items.map(item => item.id));
        if (allObjectiveIds.includes(objectiveId)) {
            if (selectedObjectiveId.value === objectiveId) {
                // If it is the same objective, just select the practice directly
                selectedPracticeId.value = practiceId === objectiveId ? '' : practiceId;
            } else {
                // Set pending state and swap objective
                pendingPracticeId.value = practiceId === objectiveId ? '' : practiceId;
                selectedObjectiveId.value = objectiveId;
            }
        }
    }
}

function parseCobitTokens(text) {
    if (!text) return [];
    const clean = cleanText(text);
    // Matches any COBIT ID pattern (e.g. MEA03.02, EDM01, etc.)
    const regex = /([A-Z]{3,4}\d{2}(?:\.\d{2})?)/gi;
    const parts = clean.split(regex);
    
    return parts.map(part => {
        const isId = /^[A-Z]{3,4}\d{2}(?:\.\d{2})?$/i.test(part);
        return {
            text: part,
            isId: isId
        };
    });
}
</script>

<style scoped>
/* ─── Table-Flow Diagram Layout (Simplified & Minimal) ─── */
.flow-row {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 0;
    margin-bottom: 2rem;
    width: 100%;
}

.flow-section {
    flex: 1;
    min-width: 0;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

:deep(.dark) .flow-section {
    background: #171717;
    border-color: rgba(255, 255, 255, 0.08);
}

.flow-section--practice {
    flex: 1.15;
}

/* Minimal Table Styles */
.flow-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    text-align: left;
}

.flow-table__title {
    padding: 8px 12px;
    font-size: 11.5px;
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.flow-table__title--input {
    background: #eff6ff;
    color: #1e40af;
    border-bottom: 1px solid #bfdbfe;
}

.flow-table__title--practice {
    background: #f5f3ff;
    color: #5b21b6;
    border-bottom: 1px solid #ddd6fe;
}

.flow-table__title--output {
    background: #fdf2f8;
    color: #9d174d;
    border-bottom: 1px solid #fbcfe8;
}

:deep(.dark) .flow-table__title--input {
    background: rgba(59, 130, 246, 0.08);
    color: #93c5fd;
    border-bottom-color: rgba(59, 130, 246, 0.2);
}

:deep(.dark) .flow-table__title--practice {
    background: rgba(124, 58, 237, 0.08);
    color: #c4b5fd;
    border-bottom-color: rgba(124, 58, 237, 0.2);
}

:deep(.dark) .flow-table__title--output {
    background: rgba(219, 39, 119, 0.08);
    color: #f9a8d4;
    border-bottom-color: rgba(219, 39, 119, 0.2);
}

.flow-table__th {
    padding: 6px 10px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: #f8fafc;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

:deep(.dark) .flow-table__th {
    background: rgba(255, 255, 255, 0.02);
    color: #a3a3a3;
    border-bottom-color: rgba(255, 255, 255, 0.08);
}

.flow-table__td {
    padding: 8px 10px;
    line-height: 1.4;
    vertical-align: top;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}

:deep(.dark) .flow-table__td {
    color: #cbd5e1;
    border-bottom-color: rgba(255, 255, 255, 0.04);
}

.flow-table__td--from {
    font-weight: 600;
    color: #3b82f6;
    word-break: break-word;
}

:deep(.dark) .flow-table__td--from {
    color: #60a5fa;
}

.flow-table__td--to {
    font-weight: 600;
    color: #ec4899;
    word-break: break-word;
}

:deep(.dark) .flow-table__td--to {
    color: #f472b6;
}

.flow-table__td--empty {
    text-align: center;
    color: #94a3b8;
    font-style: italic;
    padding: 16px;
}

.flow-table__td--practice {
    padding: 14px;
    vertical-align: middle;
}

/* Practice Center styles (Minimal Card) */
.flow-practice-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.4;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 10px;
}

:deep(.dark) .flow-practice-content {
    color: #e5e7eb;
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.08);
}

.flow-practice-content__id {
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #7c3aed;
    margin-bottom: 2px;
}

:deep(.dark) .flow-practice-content__id {
    color: #a78bfa;
}

/* Practice list in overall mode */
.flow-practice-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 6px;
    text-align: left;
}

.flow-practice-list__item {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 10.5px;
    font-weight: 500;
    color: #4b5563;
    padding: 5px 8px;
    background: #f9fafb;
    border-radius: 4px;
    border: 1px solid #e5e7eb;
}

:deep(.dark) .flow-practice-list__item {
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.06);
}

.flow-practice-list__dot {
    flex-shrink: 0;
    width: 5px;
    height: 5px;
    background-color: #7c3aed;
    border-radius: 50%;
    margin-top: 5px;
}

/* Connectors (Simple 1px Line & Chevron) */
.flow-connector {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    flex-shrink: 0;
    position: relative;
    user-select: none;
}

.flow-connector__line {
    height: 1px;
    width: 100%;
}

.flow-connector__line--blue {
    background-color: #bfdbfe;
}

.flow-connector__line--pink {
    background-color: #fbcfe8;
}

:deep(.dark) .flow-connector__line--blue {
    background-color: rgba(59, 130, 246, 0.25);
}

:deep(.dark) .flow-connector__line--pink {
    background-color: rgba(219, 39, 119, 0.25);
}

.flow-connector__arrow {
    position: absolute;
    font-size: 7px;
    color: #94a3b8;
    line-height: 1;
}

.flow-connector__arrow--blue {
    color: #3b82f6;
}

.flow-connector__arrow--pink {
    color: #db2777;
}

/* RACI List inside Practice Section */
.flow-practice-raci-list {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    justify-content: center;
    margin-top: 8px;
    width: 100%;
    border-top: 1px solid #f1f5f9;
    padding-top: 6px;
}

:deep(.dark) .flow-practice-raci-list {
    border-top-color: rgba(255, 255, 255, 0.06);
}

.flow-practice-list .flow-practice-raci-list {
    justify-content: flex-start;
    border-top: none;
    padding-top: 0;
    margin-top: 4px;
}

.flow-practice-raci-item {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #f3f4f6;
    padding: 1.5px 5px;
    border-radius: 3px;
}

:deep(.dark) .flow-practice-raci-item {
    background: rgba(255, 255, 255, 0.04);
}

.flow-raci-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 12px;
    height: 12px;
    border-radius: 2px;
    font-size: 8px;
    font-weight: 950;
    line-height: 1;
}

.flow-raci-role {
    font-size: 9px;
    font-weight: 600;
    color: #4b5563;
    white-space: nowrap;
}

:deep(.dark) .flow-raci-role {
    color: #cbd5e1;
}

/* Mobile responsive layout */
@media (max-width: 1024px) {
    .flow-row {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .flow-connector {
        width: 100%;
        height: 24px;
        flex-direction: column;
    }
    
    .flow-connector__line {
        width: 1px;
        height: 100%;
    }
    
    .flow-connector__arrow {
        transform: rotate(90deg);
    }
}

/* Print styling adjustments */
@media print {
    body {
        background-color: white !important;
        color: black !important;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>


