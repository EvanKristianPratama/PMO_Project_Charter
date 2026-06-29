<template>
    <ModulLayout title="ITSP Information Flow Analysis">
        <div class="space-y-6 animate-fade-in-up print:m-0 print:p-0">
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">
                            Pedoman Tata Kelola Teknologi Informasi Pertamina (Persero)
                        </p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            ITSP Information Flow Analysis
                        </h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            :href="route('policy.raci.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            Matriks RACI
                        </Link>
                        <Link
                            :href="route('policy.itsp-infoflow.manage')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </section>

            <div class="w-full rounded-2xl border border-slate-200 bg-white p-4 font-sans text-slate-800 shadow-xl dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200 sm:p-6 md:p-8">
                <div class="mb-6 space-y-1.5 py-2 text-center">
                    <h2 class="text-2xl font-extrabold uppercase tracking-tight text-[#821f44] dark:text-[#a83262] sm:text-3xl">
                        ITSP Information Flow Analysis
                    </h2>
                </div>

                <div class="mb-6 flex flex-col gap-4 border-b border-slate-200 pb-6 dark:border-white/10 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid flex-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Pilih Kebijakan
                            </label>
                            <select
                                v-model="selectedObjectiveId"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm transition focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            >
                                <option value="">-- Pilih Kebijakan --</option>
                                <optgroup
                                    v-for="group in objectiveGroups"
                                    :key="group.domain"
                                    :label="group.domain"
                                    class="bg-white font-bold text-slate-400 dark:bg-[#1a1a1a] dark:text-zinc-500"
                                >
                                    <option
                                        v-for="item in group.items"
                                        :key="item.id"
                                        :value="item.id"
                                        class="font-semibold text-slate-800 dark:text-slate-200"
                                    >
                                        {{ item.id }} - {{ item.name }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Filter Aktifitas
                            </label>
                            <select
                                v-model="selectedPracticeId"
                                :disabled="!selectedObjectiveId || displayPractices.length === 0"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 shadow-sm transition focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Semua Aktifitas</option>
                                <option
                                    v-for="practice in objectiveData.practices"
                                    :key="practice.practice_id"
                                    :value="practice.practice_id"
                                >
                                    {{ cleanText(practice.practice_id) }} - {{ cleanText(practice.practice_name) }}
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            v-if="selectedObjectiveId && !isEditMode"
                            @click="enterEditMode"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition hover:bg-[#9c2552] active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit Aliran
                        </button>

                        <template v-if="isEditMode">
                            <button
                                @click="exitEditMode"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Batal
                            </button>
                            <button
                                @click="saveChanges"
                                :disabled="saving"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <svg v-if="saving" class="-ml-1 h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </template>

                        <button
                            v-if="selectedObjectiveId"
                            @click="selectedPracticeId = ''"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>

                <div v-if="loading" class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-24 dark:border-white/10 dark:bg-white/5">
                    <div class="inline-block h-9 w-9 animate-spin rounded-full border-4 border-[#821f44] border-t-transparent"></div>
                    <p class="mt-4 text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        Memuat data aliran informasi ITSP...
                    </p>
                </div>

                <div v-else-if="error" class="flex flex-col items-center rounded-2xl border border-red-200 bg-red-50/50 p-12 text-center dark:border-red-950/30 dark:bg-red-950/10">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600 shadow-sm dark:bg-red-950/50 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Gagal Memuat Referensi Aliran Informasi</h3>
                    <p class="mt-2 max-w-md text-xs text-slate-500 dark:text-slate-400">{{ error }}</p>
                    <button
                        @click="fetchInfoflowData"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2 text-xs font-bold text-white shadow-md transition active:scale-95 hover:bg-[#9c2552]"
                    >
                        Coba Lagi
                    </button>
                </div>

                <div v-else-if="!selectedObjectiveId" class="flex flex-col items-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-20 text-center dark:border-white/10 dark:bg-white/5">
                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-slate-400 dark:border-white/5 dark:bg-white/5 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H3.75A2.25 2.25 0 0 0 1.5 4.5v15a2.25 2.25 0 0 0 2.25 2.25h12.75a2.25 2.25 0 0 0 2.25-2.25V14.25z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-800 dark:text-slate-200">
                        Pilih Kebijakan Terlebih Dahulu
                    </h3>
                    <p class="mt-2 max-w-sm text-xs text-slate-500 dark:text-slate-400">
                        Gunakan dropdown di atas untuk memilih kebijakan dan melihat alur data input, butir kebijakan, RACI Matrix, serta output.
                    </p>
                </div>

                <div v-else class="space-y-4">
                    <div class="border-b border-slate-200 pb-3 dark:border-white/10">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded bg-[#821f44]/10 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-[#821f44] dark:bg-[#821f44]/20 dark:text-rose-300">
                                {{ cleanText(objectiveData.objective_id) }}
                            </span>
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">
                                {{ cleanText(objectiveData.objective) }}
                            </h3>
                        </div>
                        <p class="mt-1 max-w-5xl text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                            {{ cleanText(objectiveData.objective_description) }}
                            <span v-if="objectiveData.objective_purpose" class="ml-1 italic">
                                {{ cleanText(objectiveData.objective_purpose) }}
                            </span>
                        </p>
                    </div>

                    <!-- COBIT Reference Matrix (Pedoman Pengisian) -->
                    <div v-if="isEditMode && cobitRefData" class="space-y-2 p-3.5 rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/5 dark:bg-amber-950/5 animate-fade-in-up">
                        <div 
                            @click="showCobitRef = !showCobitRef" 
                            class="flex items-center justify-between cursor-pointer select-none pb-2"
                            :class="showCobitRef ? 'border-b border-amber-200/50 dark:border-amber-900/30' : ''"
                        >
                            <div class="flex-1">
                                <h4 class="text-[11px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wide flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Pedoman Aliran Informasi Standar COBIT 2019
                                </h4>
                                <p v-show="showCobitRef" class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5 font-medium leading-relaxed">
                                    Gunakan tabel acuan standar COBIT ini untuk mempermudah pengisian input dan output ITSP di bawah.
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded bg-amber-100 dark:bg-amber-900/30 px-1.5 py-0.5 text-[9px] font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                                    Referensi COBIT
                                </span>
                                <button class="p-0.5 rounded hover:bg-amber-100 dark:hover:bg-amber-900/50 text-amber-800 dark:text-amber-300 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" 
                                        class="w-3.5 h-3.5 transform transition-transform duration-200"
                                        :class="showCobitRef ? 'rotate-180' : ''"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div v-show="showCobitRef" class="mt-1">
                            <div v-if="loadingCobitRef" class="py-4 flex flex-col items-center justify-center">
                                <div class="inline-block w-5 h-5 border-2 border-amber-600 border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-[9px] font-bold uppercase tracking-wider text-amber-600 mt-1.5">Memuat referensi COBIT...</p>
                            </div>
                            
                            <div v-else class="overflow-x-auto max-h-[260px] overflow-y-auto rounded-xl border border-amber-200/40 bg-white dark:bg-[#121212] shadow-sm max-w-full">
                                <table class="min-w-[1200px] w-full border-collapse text-left text-[10px] text-slate-500 dark:text-slate-400 border border-amber-100 dark:border-amber-950/20">
                                    <thead class="sticky top-0 z-10 bg-amber-50 dark:bg-[#121212] text-[9px] font-bold uppercase tracking-[0.14em] text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-white/10 shadow-sm">
                                        <tr class="divide-x divide-slate-200 dark:divide-white/10">
                                            <th scope="col" class="w-10 px-2 py-2 text-center border-r border-slate-200 dark:border-white/10 sticky top-0 bg-amber-50 dark:bg-[#121212]">No</th>
                                            <th scope="col" class="w-[28%] px-3 py-2 border-r border-slate-200 dark:border-white/10 sticky top-0 bg-amber-50 dark:bg-[#121212]">Butir Kebijakan</th>
                                            <th scope="col" class="w-[24%] px-3 py-2 border-r border-slate-200 dark:border-white/10 sticky top-0 bg-amber-50 dark:bg-[#121212]">Input</th>
                                            
                                            <!-- Dynamic Role Columns -->
                                            <th 
                                                v-for="role in activeRoles" 
                                                :key="role.id" 
                                                class="p-0.5 text-center w-[20px] min-w-[20px] max-w-[20px] align-bottom pb-1.5 select-none bg-[#d2e4df] dark:bg-[#1b3a32] text-slate-800 dark:text-slate-200 border-r border-[#b2cfc7] dark:border-[#255246] sticky top-0"
                                                :title="role.name"
                                            >
                                                <div class="inline-flex items-center justify-center h-28 w-[12px] mx-auto">
                                                    <span class="[writing-mode:vertical-lr] rotate-180 text-[7px] font-black uppercase tracking-tight whitespace-nowrap text-left leading-tight py-0.5">
                                                        {{ role.name }}
                                                    </span>
                                                </div>
                                            </th>

                                            <th scope="col" class="w-[24%] px-3 py-2 sticky top-0 bg-amber-50 dark:bg-[#121212]">Output</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                        <tr
                                            v-for="(practice, idx) in cobitRefData.practices"
                                            :key="practice.practice_id"
                                            class="align-top hover:bg-amber-50/10 dark:hover:bg-amber-950/5 divide-x divide-slate-200 dark:divide-white/10"
                                        >
                                            <!-- No -->
                                            <td class="px-2 py-2 text-center font-medium text-slate-700 dark:text-slate-300 border-r border-slate-200 dark:border-white/10">
                                                {{ idx + 1 }}
                                            </td>
                                            
                                            <!-- Butir Kebijakan -->
                                            <td class="px-3 py-2 border-r border-slate-200 dark:border-white/10">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="rounded bg-amber-100 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-wider text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
                                                            {{ translateCobitPracticeIdToLocal(practice.practice_id) }}
                                                        </span>
                                                        <h4 class="text-[11px] font-extrabold text-slate-900 dark:text-white leading-tight">
                                                            {{ cleanText(practice.practice_name) }}
                                                        </h4>
                                                    </div>
                                                    <p v-if="practice.practice_description" class="text-[10px] leading-relaxed text-slate-500 dark:text-slate-400">
                                                        {{ cleanText(practice.practice_description) }}
                                                    </p>
                                                </div>
                                            </td>
                                            
                                            <!-- Input -->
                                            <td class="px-3 py-2 border-r border-slate-200 dark:border-white/10">
                                                <div class="space-y-2">
                                                    <div v-for="input in practice.inputs" :key="input.input_id" class="space-y-0.5 bg-slate-50/50 dark:bg-white/5 p-1.5 rounded-lg border border-slate-100/50 dark:border-white/5">
                                                        <span
                                                            class="inline-block rounded px-1 py-0.5 text-[8px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300"
                                                        >
                                                            Dari: {{ localizeReference(input.from) }}
                                                        </span>
                                                        <p class="whitespace-pre-line text-[10px] leading-relaxed text-slate-700 dark:text-slate-300 font-semibold">
                                                            {{ cleanText(input.description) }}
                                                        </p>
                                                    </div>
                                                    <div v-if="!practice.inputs || practice.inputs.length === 0" class="text-[10px] italic text-slate-400 dark:text-slate-600">
                                                        Tidak ada input khusus
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Dynamic Role RACI badges -->
                                            <td 
                                                v-for="role in activeRoles" 
                                                :key="role.id" 
                                                class="p-0 text-center w-[20px] min-w-[20px] max-w-[20px] align-middle h-[16px] border-r border-slate-200 dark:border-white/10 bg-[#fbfdfb] dark:bg-[#14231b]"
                                            >
                                                <template v-if="practice.role_assignments && practice.role_assignments[role.id]">
                                                    <span 
                                                        v-if="practice.role_assignments[role.id].raci.toUpperCase() === 'I'"
                                                        class="inline-block w-[3px] h-[10px] bg-[#1e293b] dark:bg-slate-200 rounded-[0.5px] align-middle"
                                                        :title="`${practice.practice_id} - ${role.name}: Informed`"
                                                    ></span>
                                                    <span 
                                                        v-else
                                                        :class="getRaciTextClass(practice.role_assignments[role.id].raci)"
                                                        :title="`${practice.practice_id} - ${role.name}: ${getRaciLabel(practice.role_assignments[role.id].raci)}`"
                                                    >
                                                        {{ practice.role_assignments[role.id].raci }}
                                                    </span>
                                                </template>
                                            </td>
                                            
                                            <!-- Output -->
                                            <td class="px-3 py-2">
                                                <div class="space-y-2">
                                                    <div v-for="output in practice.outputs" :key="output.output_id" class="space-y-0.5 bg-slate-50/50 dark:bg-white/5 p-1.5 rounded-lg border border-slate-100/50 dark:border-white/5">
                                                        <span class="inline-block rounded bg-pink-100 px-1.5 py-0.5 text-[8px] font-bold text-pink-700 dark:bg-pink-900/50 dark:text-pink-300">
                                                            Ke: {{ localizeReference(output.to) }}
                                                        </span>
                                                        <p class="whitespace-pre-line text-[10px] leading-relaxed text-slate-700 dark:text-slate-300 font-semibold">
                                                            {{ cleanText(output.description) }}
                                                        </p>
                                                    </div>
                                                    <div v-if="!practice.outputs || practice.outputs.length === 0" class="text-[10px] italic text-slate-400 dark:text-slate-600">
                                                        Tidak ada output khusus
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
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
                                <tr v-if="displayPractices.length === 0">
                                    <td :colspan="activeRoles.length + 4" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                                        Tidak ada aktifitas yang sesuai dengan filter.
                                    </td>
                                </tr>
                                <tr
                                    v-for="(practice, idx) in displayPractices"
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
                                        <div v-if="isEditMode" class="space-y-2.5">
                                            <div v-if="!practice.inputs || practice.inputs.length === 0" class="text-[11px] italic text-slate-400 dark:text-slate-500">
                                                Belum ada input
                                            </div>
                                            <div v-for="(input, inputIndex) in practice.inputs" :key="`${practice.practice_id}-input-${inputIndex}`" class="space-y-2 p-2 rounded-lg border border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0">Dari:</span>
                                                    <input
                                                        v-model="input.from"
                                                        type="text"
                                                        class="w-full rounded-lg border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                        placeholder="Contoh: 2.2.05"
                                                    />
                                                    <button
                                                        @click="removeInput(practice, inputIndex)"
                                                        class="rounded-lg p-1 text-rose-500 transition hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-500/10 shrink-0"
                                                        title="Hapus Input"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <textarea
                                                    v-model="input.description"
                                                    rows="2"
                                                    class="w-full rounded-lg border-slate-200 bg-white px-2 py-1 text-[11px] leading-relaxed text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                    placeholder="Deskripsi input..."
                                                ></textarea>
                                            </div>
                                            <button
                                                @click="addInput(practice)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white transition hover:bg-blue-700 active:scale-95"
                                            >
                                                + Tambah Input
                                            </button>
                                        </div>
                                        <div v-else class="space-y-3">
                                            <div v-for="input in practice.inputs" :key="input.input_id" class="space-y-1 bg-slate-50/50 dark:bg-white/5 p-2 rounded-lg border border-slate-100/50 dark:border-white/5">
                                                <span
                                                    class="inline-block rounded px-1.5 py-0.5 text-[9px] font-bold"
                                                    :class="cleanText(input.from) === 'Outside COBIT'
                                                        ? 'bg-slate-200 text-slate-700 dark:bg-zinc-700 dark:text-slate-300'
                                                        : 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300'"
                                                >
                                                    Dari: {{ cleanText(input.from) }}
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
                                        <div v-if="isEditMode" class="space-y-2.5">
                                            <div v-if="!practice.outputs || practice.outputs.length === 0" class="text-[11px] italic text-slate-400 dark:text-slate-500">
                                                Belum ada output
                                            </div>
                                            <div v-for="(output, outputIndex) in practice.outputs" :key="`${practice.practice_id}-output-${outputIndex}`" class="space-y-2 p-2 rounded-lg border border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0">Ke:</span>
                                                    <input
                                                        v-model="output.to"
                                                        type="text"
                                                        class="w-full rounded-lg border-slate-200 bg-white px-2 py-1 text-[11px] text-slate-800 focus:border-pink-500 focus:ring-1 focus:ring-pink-500/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                        placeholder="Contoh: 3.1.01"
                                                    />
                                                    <button
                                                        @click="removeOutput(practice, outputIndex)"
                                                        class="rounded-lg p-1 text-rose-500 transition hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-500/10 shrink-0"
                                                        title="Hapus Output"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <textarea
                                                    v-model="output.description"
                                                    rows="2"
                                                    class="w-full rounded-lg border-slate-200 bg-white px-2 py-1 text-[11px] leading-relaxed text-slate-800 focus:border-pink-500 focus:ring-1 focus:ring-pink-500/20 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                                                    placeholder="Deskripsi output..."
                                                ></textarea>
                                            </div>
                                            <button
                                                @click="addOutput(practice)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-pink-600 px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white transition hover:bg-pink-700 active:scale-95"
                                            >
                                                + Tambah Output
                                            </button>
                                        </div>
                                        <div v-else class="space-y-3">
                                            <div v-for="output in practice.outputs" :key="output.output_id" class="space-y-1 bg-slate-50/50 dark:bg-white/5 p-2 rounded-lg border border-slate-100/50 dark:border-white/5">
                                                <span class="inline-block rounded bg-pink-100 px-1.5 py-0.5 text-[9px] font-bold text-pink-700 dark:bg-pink-900/50 dark:text-pink-300">
                                                    Ke: {{ cleanText(output.to) }}
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

                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import ModulLayout from '@/Layouts/ModulLayout.vue';

const props = defineProps({
    objectiveGroups: {
        type: Array,
        required: true,
    },
});

const selectedObjectiveId = ref('');
const selectedPracticeId = ref('');
const loading = ref(false);
const saving = ref(false);
const error = ref(null);
const isEditMode = ref(false);
const editablePractices = ref([]);
const objectiveData = ref(createEmptyObjectiveData());

const domainCode = computed(() => {
    if (!selectedObjectiveId.value) return '';
    const code = selectedObjectiveId.value.split('.')[0];
    const map = {
        '1': 'EDM',
        '2': 'APO',
        '3': 'BAI',
        '4': 'DSS',
        '5': 'MEA',
    };
    return map[code] || 'ITSP';
});

const domainColorClasses = computed(() => {
    const code = domainCode.value;
    if (code === 'EDM') {
        return 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/20 dark:text-red-300 dark:border-red-900/40';
    }
    if (code === 'APO') {
        return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-300 dark:border-blue-900/40';
    }
    if (code === 'BAI') {
        return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-300 dark:border-emerald-900/40';
    }
    if (code === 'DSS') {
        return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-300 dark:border-amber-900/40';
    }
    if (code === 'MEA') {
        return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/20 dark:text-purple-300 dark:border-purple-900/40';
    }
    return 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700/50';
});

const activePractices = computed(() => {
    return isEditMode.value ? editablePractices.value : (objectiveData.value.practices || []);
});

const displayPractices = computed(() => {
    if (!selectedPracticeId.value) {
        return activePractices.value;
    }
    return activePractices.value.filter((practice) => practice.practice_id === selectedPracticeId.value);
});

const activeRoles = computed(() => {
    const rolesMap = {};
    displayPractices.value.forEach(practice => {
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

const cobitRefData = ref(null);
const loadingCobitRef = ref(false);
const showCobitRef = ref(true);

const cobitObjectiveId = computed(() => {
    if (!selectedObjectiveId.value) return '';
    const parts = selectedObjectiveId.value.split('.');
    if (parts.length !== 2) return selectedObjectiveId.value;
    const domainNum = parseInt(parts[0], 10);
    const objNum = parseInt(parts[1], 10);
    const domains = {
        1: 'EDM',
        2: 'APO',
        3: 'BAI',
        4: 'DSS',
        5: 'MEA'
    };
    const prefix = domains[domainNum] || 'COBIT';
    const objSeq = String(objNum).padStart(2, '0');
    return `${prefix}${objSeq}`;
});

async function fetchCobitReference() {
    if (!selectedObjectiveId.value) {
        cobitRefData.value = null;
        return;
    }
    loadingCobitRef.value = true;
    try {
        const cobitId = cobitObjectiveId.value;
        const url = `https://cobit2019.divusi.co.id/api/cobit/gamo-infoflow?objective_id=${encodeURIComponent(cobitId)}`;
        const response = await fetch(url);
        if (response.ok) {
            const dataJson = await response.json();
            if (dataJson.success && dataJson.objectives && dataJson.objectives[0]) {
                cobitRefData.value = dataJson.objectives[0];
            }
        }
    } catch (err) {
        console.error('Error fetching COBIT reference:', err);
    } finally {
        loadingCobitRef.value = false;
    }
}

watch([isEditMode, selectedObjectiveId], () => {
    if (isEditMode.value && selectedObjectiveId.value) {
        fetchCobitReference();
    } else if (!isEditMode.value) {
        cobitRefData.value = null;
    }
});

function translateCobitPracticeIdToLocal(cobitId) {
    if (!cobitId) return '';
    const cleanId = String(cobitId).toUpperCase().trim();
    const match = cleanId.match(/^([A-Z]{3})(\d{2})\.(\d{2})$/);
    if (!match) return cobitId;
    
    const domainsMap = {
        'EDM': 1,
        'APO': 2,
        'BAI': 3,
        'DSS': 4,
        'MEA': 5,
    };
    const prefix = match[1];
    const oNum = parseInt(match[2], 10);
    const pNum = parseInt(match[3], 10);
    
    if (domainsMap[prefix]) {
        const domainVal = domainsMap[prefix];
        const objVal = oNum;
        const pracVal = String(pNum).padStart(2, '0');
        return `${domainVal}.${objVal}.${pracVal}`;
    }
    return cobitId;
}

function localizeReference(refVal) {
    if (!refVal) return '';
    const cleanRef = String(refVal).toUpperCase().trim();
    const domainsMap = {
        'EDM': 1,
        'APO': 2,
        'BAI': 3,
        'DSS': 4,
        'MEA': 5,
    };
    
    // Match e.g. "APO02.05"
    let match = cleanRef.match(/^([A-Z]{3})(\d{2})\.(\d{2})$/);
    if (match) {
        const prefix = match[1];
        const oNum = parseInt(match[2], 10);
        const pNum = parseInt(match[3], 10);
        if (domainsMap[prefix]) {
            return `${domainsMap[prefix]}.${oNum}.${pNum}`;
        }
    }
    
    // Match e.g. "APO02"
    match = cleanRef.match(/^([A-Z]{3})(\d{2})$/);
    if (match) {
        const prefix = match[1];
        const oNum = parseInt(match[2], 10);
        if (domainsMap[prefix]) {
            return `${domainsMap[prefix]}.${oNum}`;
        }
    }
    
    return refVal;
}

function createEmptyObjectiveData() {
    return {
        objective_id: '',
        objective: '',
        objective_description: '',
        objective_purpose: '',
        is_local: false,
        responsibles: [],
        practices: [],
    };
}

async function fetchInfoflowData() {
    if (!selectedObjectiveId.value) {
        objectiveData.value = createEmptyObjectiveData();
        editablePractices.value = [];
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const url = `/policy/itsp-infoflow/data?objective_id=${encodeURIComponent(selectedObjectiveId.value)}`;
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`Koneksi server gagal dengan status ${response.status}`);
        }

        const dataJson = await response.json();
        if (!dataJson.success) {
            throw new Error(dataJson.message || 'Gagal memuat data dari controller.');
        }

        objectiveData.value = dataJson;
        if (isEditMode.value) {
            editablePractices.value = JSON.parse(JSON.stringify(dataJson.practices || []));
        }
    } catch (err) {
        console.error('Error fetching ITSP infoflow:', err);
        error.value = err.message || 'Gagal memuat data referensi aliran informasi ITSP.';
    } finally {
        loading.value = false;
    }
}

watch(selectedObjectiveId, () => {
    selectedPracticeId.value = '';
    isEditMode.value = false;
    editablePractices.value = [];
    fetchInfoflowData();
});

function enterEditMode() {
    isEditMode.value = true;
    editablePractices.value = JSON.parse(JSON.stringify(objectiveData.value.practices || []));
}

function exitEditMode() {
    isEditMode.value = false;
    editablePractices.value = [];
}

function addInput(practice) {
    if (!practice.inputs) {
        practice.inputs = [];
    }
    practice.inputs.push({
        from: '',
        description: '',
    });
}

function removeInput(practice, inputIdx) {
    practice.inputs.splice(inputIdx, 1);
}

function addOutput(practice) {
    if (!practice.outputs) {
        practice.outputs = [];
    }
    practice.outputs.push({
        to: '',
        description: '',
    });
}

function removeOutput(practice, outputIdx) {
    practice.outputs.splice(outputIdx, 1);
}

async function saveChanges() {
    for (const practice of editablePractices.value) {
        for (const input of practice.inputs || []) {
            if (!input.from?.trim() || !input.description?.trim()) {
                Swal.fire('Validasi Gagal', 'Kolom Dari dan Deskripsi Input wajib diisi.', 'warning');
                return;
            }
        }

        for (const output of practice.outputs || []) {
            if (!output.to?.trim() || !output.description?.trim()) {
                Swal.fire('Validasi Gagal', 'Kolom Ke dan Deskripsi Output wajib diisi.', 'warning');
                return;
            }
        }
    }

    saving.value = true;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch('/policy/itsp-infoflow/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                objective_id: selectedObjectiveId.value,
                practices: editablePractices.value,
            }),
        });

        if (!response.ok) {
            throw new Error(`Koneksi server gagal dengan status ${response.status}`);
        }

        const resData = await response.json();
        if (!resData.success) {
            throw new Error(resData.message || 'Gagal menyimpan perubahan.');
        }

        await Swal.fire({
            title: 'Berhasil!',
            text: 'Aliran informasi ITSP berhasil diperbarui secara lokal.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
        });

        isEditMode.value = false;
        await fetchInfoflowData();
    } catch (err) {
        console.error('Error saving ITSP infoflow:', err);
        Swal.fire('Gagal Menyimpan', err.message || 'Gagal memperbarui data aliran informasi.', 'error');
    } finally {
        saving.value = false;
    }
}

function getRaciRoleContainerClass(raci) {
    const val = String(raci).toUpperCase();
    if (val === 'A') {
        return 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40';
    }
    if (val === 'R') {
        return 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/40';
    }
    if (val === 'C') {
        return 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40';
    }
    return 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/40';
}

function getRaciBadgeClass(raci) {
    const val = String(raci).toUpperCase();
    if (val === 'A') {
        return 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300';
    }
    if (val === 'R') {
        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
    }
    if (val === 'C') {
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

function cleanText(val) {
    if (!val) return '';
    return String(val)
        .replace(/^["'\\\s]+|["'\\\s]+$/g, '')
        .replace(/\\"/g, '"')
        .trim();
}
</script>

<style scoped>
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
