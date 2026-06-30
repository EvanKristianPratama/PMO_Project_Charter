<template>
    <ModulLayout title="Kamus Component COBIT 2019">
        <div class="space-y-6 animate-fade-in-up">
            <!-- Header section matching reference cbioo -->
            <div class="flex flex-col sm:flex-row justify-between sm:items-center bg-white dark:bg-zinc-900 p-4 border border-slate-200 dark:border-zinc-800 gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl font-bold text-slate-800 dark:text-zinc-100 flex items-center gap-2">
                        <Squares2X2Icon class="h-6 w-6 text-[#0f2b5c]" />
                        Kamus Component COBIT 2019 (Core Model)
                    </h1>
                </div>
                
                <div class="flex items-center gap-2">
                    <Link
                        href="/policy/infoflow"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 border border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-zinc-700 transition active:scale-95 shadow-sm rounded-none"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#0f2b5c]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935-2.186 2.25 2.25 0 00-3.935 2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                        </svg>
                        Buka Analisis Alur
                    </Link>
                </div>
            </div>

            <!-- Mode Selector -->
            <div class="bg-white dark:bg-zinc-900 p-3 border border-slate-200 dark:border-zinc-800">
                <div class="grid grid-cols-3 gap-2">
                    <button
                        @click="setMode('gamo')"
                        class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider transition border text-center"
                        :class="activeMode === 'gamo'
                            ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white shadow-md'
                            : 'bg-white hover:bg-slate-50 border-slate-200 text-[#0f2b5c] dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700'"
                    >
                        View by GAMO
                    </button>
                    <button
                        @click="setMode('component')"
                        class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider transition border text-center"
                        :class="activeMode === 'component'
                            ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white shadow-md'
                            : 'bg-white hover:bg-slate-50 border-slate-200 text-[#0f2b5c] dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700'"
                    >
                        View by Component
                    </button>
                    <button
                        @click="setMode('master')"
                        class="py-2.5 px-4 text-xs font-bold uppercase tracking-wider transition border text-center"
                        :class="activeMode === 'master'
                            ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white shadow-md'
                            : 'bg-white hover:bg-slate-50 border-slate-200 text-[#0f2b5c] dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-700'"
                    >
                        Master
                    </button>
                </div>

                <div class="mt-3 flex justify-between items-center border-t border-slate-150 dark:border-zinc-800 pt-3">
                    <div class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">
                        Status API: <span class="text-emerald-500 font-bold">LIVE</span>
                    </div>
                    <button
                        disabled
                        class="px-3 py-1 text-[11px] font-bold border border-slate-300 text-slate-400 bg-slate-50 dark:bg-zinc-800 dark:border-zinc-700 cursor-not-allowed"
                    >
                        Input Mode: {{ inputMode ? 'ON' : 'OFF' }}
                    </button>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div v-if="loading" class="flex flex-col items-center justify-center p-12 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800">
                <ArrowPathIcon class="h-10 w-10 text-[#0f2b5c] animate-spin" />
                <p class="mt-4 text-xs font-semibold text-slate-600 dark:text-zinc-400">Memuat data referensi COBIT dari public API...</p>
            </div>

            <!-- Error Banner -->
            <div v-else-if="error" class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40 text-rose-700 dark:text-rose-400 text-xs font-medium">
                <p>⚠️ Terjadi kesalahan: {{ error }}</p>
                <button @click="retryLoad" class="mt-2 text-rose-600 dark:text-rose-300 underline font-bold">Coba Lagi</button>
            </div>

            <div v-else>
                <!-- 1. MODE VIEW BY GAMO -->
                <div v-if="activeMode === 'gamo'" class="space-y-4">
                    <!-- Domain Tab Group -->
                    <div class="bg-white dark:bg-zinc-900 p-2 border border-slate-200 dark:border-zinc-800">
                        <div class="flex flex-wrap gap-1 border-b border-slate-100 dark:border-zinc-800 pb-2">
                            <button
                                v-for="dom in domains"
                                :key="dom"
                                @click="selectDomain(dom)"
                                class="px-4 py-2 text-xs font-bold uppercase tracking-wider transition"
                                :class="activeDomain === dom
                                    ? 'bg-[#0f2b5c] text-white shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200'"
                            >
                                {{ dom }}
                            </button>
                        </div>

                        <!-- Objective Tabs (filtered by Domain) -->
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            <button
                                v-for="obj in filteredObjectives"
                                :key="obj.objective_id"
                                @click="selectObjective(obj.objective_id)"
                                class="px-3 py-1.5 text-xs font-bold tracking-wider transition border"
                                :class="activeObjectiveId === obj.objective_id
                                    ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white'
                                    : 'bg-white border-slate-200 text-[#0f2b5c] hover:bg-slate-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300'"
                            >
                                {{ obj.objective_id }}
                            </button>
                        </div>
                    </div>

                    <!-- Selected Objective Heading Banner -->
                    <div class="bg-[#0f2b5c] text-white p-3.5 font-bold text-sm tracking-wide shadow-sm flex items-center justify-between">
                        <span>{{ activeObjectiveId }} — {{ cleanText(activeObjectiveName) }}</span>
                    </div>

                    <!-- Component Navigation Tabs -->
                    <div class="bg-white dark:bg-zinc-900 p-2 border border-slate-200 dark:border-zinc-800">
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-1">
                            <button
                                v-for="(lbl, key) in COMPONENT_LABELS"
                                :key="key"
                                @click="activeComponent = key"
                                class="py-2 px-1 text-[10px] font-bold uppercase tracking-wider text-center transition border"
                                :class="activeComponent === key
                                    ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white shadow-sm'
                                    : 'bg-white border-slate-200 text-[#0f2b5c] hover:bg-slate-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300'"
                            >
                                {{ lbl }}
                            </button>
                        </div>
                    </div>

                    <!-- Component Display Wrapper -->
                    <div class="bg-white dark:bg-zinc-900 p-4 border border-slate-200 dark:border-zinc-800 shadow-sm">
                        <!-- OVERVIEW RENDERER -->
                        <div v-if="activeComponent === 'overview'" class="space-y-4">
                            <div v-if="overviewData" class="space-y-4">
                                <!-- Objective Maroon Header Banner -->
                                <div class="flex flex-col md:flex-row items-stretch border-b border-slate-200 dark:border-zinc-700">
                                    <div class="flex-1 bg-[#7a2433] text-white p-4 flex flex-col justify-center">
                                        <div class="text-[14px] font-bold tracking-wide">Domain: {{ cleanText(activeDomainName) }}</div>
                                        <div class="mt-1 text-sm font-bold">{{ activeObjectiveId }} — {{ cleanText(activeObjectiveName) }}</div>
                                    </div>
                                    <div class="md:w-80 bg-[#7a2433] text-white flex items-center justify-center p-3 border-t md:border-t-0 md:border-l-4 border-white">
                                        <div class="text-center font-bold text-xs">{{ activeFocusArea?.name || 'COBIT Core Model' }}</div>
                                    </div>
                                </div>

                                <!-- Description and Purpose -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 p-3.5 rounded-none">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-zinc-100 mb-2">Description</h3>
                                        <p class="text-xs text-slate-600 dark:text-zinc-300 leading-relaxed">{{ cleanText(overviewData.description) }}</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 p-3.5 rounded-none">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-zinc-100 mb-2">Purpose</h3>
                                        <p class="text-xs text-slate-600 dark:text-zinc-300 leading-relaxed">{{ cleanText(overviewData.purpose) }}</p>
                                    </div>
                                </div>

                                <!-- Enterprise vs Alignment Goals mapping grid -->
                                <div class="grid grid-cols-1 md:grid-cols-11 items-stretch gap-2 mt-4">
                                    <!-- Enterprise Goals Box -->
                                    <div class="md:col-span-5 border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                        <div class="bg-slate-100 dark:bg-zinc-800 px-3 py-2 border-b border-slate-200 dark:border-zinc-700 text-xs font-bold">Enterprise Goals</div>
                                        <div class="p-3 space-y-2.5">
                                            <div v-for="eg in goalsData?.entergoals" :key="eg.entergoals_id" class="text-xs">
                                                <span class="font-bold text-[#0f2b5c] dark:text-sky-400">{{ eg.entergoals_id }}:</span> {{ cleanText(eg.description) }}
                                            </div>
                                            <div v-if="!goalsData?.entergoals?.length" class="text-xs text-slate-400 italic">No enterprise goals mapped</div>
                                        </div>
                                    </div>

                                    <!-- Arrow separator -->
                                    <div class="md:col-span-1 flex items-center justify-center py-2 md:py-0">
                                        <div class="h-8 w-8 rounded bg-slate-100 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 flex items-center justify-center font-bold text-slate-700 dark:text-zinc-300">→</div>
                                    </div>

                                    <!-- Alignment Goals Box -->
                                    <div class="md:col-span-5 border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                        <div class="bg-slate-100 dark:bg-zinc-800 px-3 py-2 border-b border-slate-200 dark:border-zinc-700 text-xs font-bold">Alignment Goals</div>
                                        <div class="p-3 space-y-2.5">
                                            <div v-for="ag in goalsData?.aligngoals" :key="ag.aligngoals_id" class="text-xs">
                                                <span class="font-bold text-[#0f2b5c] dark:text-sky-400">{{ ag.aligngoals_id }}:</span> {{ cleanText(ag.description) }}
                                            </div>
                                            <div v-if="!goalsData?.aligngoals?.length" class="text-xs text-slate-400 italic">No alignment goals mapped</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Goal metrics mapping grid -->
                                <div class="grid grid-cols-1 md:grid-cols-11 items-stretch gap-2 mt-4">
                                    <!-- Enterprise Goal Metrics -->
                                    <div class="md:col-span-5 border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                        <div class="bg-slate-100 dark:bg-zinc-800 px-3 py-2 border-b border-slate-200 dark:border-zinc-700 text-xs font-bold">Example Metrics for Enterprise Goals</div>
                                        <div class="p-3 space-y-4">
                                            <div v-for="eg in goalsData?.entergoals" :key="eg.entergoals_id" class="space-y-1">
                                                <div class="text-xs font-bold text-[#0f2b5c] dark:text-sky-400 border-b border-slate-100 dark:border-zinc-800 pb-0.5">{{ eg.entergoals_id }}</div>
                                                <ul class="list-disc pl-4 space-y-1 text-slate-655 dark:text-zinc-350 text-[11px]">
                                                    <li v-for="m in eg.metrics" :key="m.entergoalsmetr_id">{{ cleanText(m.description) }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Spacer/Separator -->
                                    <div class="md:col-span-1 py-2 md:py-0"></div>

                                    <!-- Alignment Goal Metrics -->
                                    <div class="md:col-span-5 border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                        <div class="bg-slate-100 dark:bg-zinc-800 px-3 py-2 border-b border-slate-200 dark:border-zinc-700 text-xs font-bold">Example Metrics for Alignment Goals</div>
                                        <div class="p-3 space-y-4">
                                            <div v-for="ag in goalsData?.aligngoals" :key="ag.aligngoals_id" class="space-y-1">
                                                <div class="text-xs font-bold text-[#0f2b5c] dark:text-sky-400 border-b border-slate-100 dark:border-zinc-800 pb-0.5">{{ ag.aligngoals_id }}</div>
                                                <ul class="list-disc pl-4 space-y-1 text-slate-655 dark:text-zinc-350 text-[11px]">
                                                    <li v-for="m in ag.metrics" :key="m.aligngoalsmetr_id">{{ cleanText(m.description) }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PRACTICES RENDERER -->
                        <div v-else-if="activeComponent === 'practices'" class="space-y-6">
                            <!-- Summary accordion per reference show.blade.php -->
                            <div class="border border-slate-200 dark:border-zinc-800">
                                <button
                                    @click="isPracticeSummaryExpanded = !isPracticeSummaryExpanded"
                                    class="w-full bg-slate-50 dark:bg-zinc-800 hover:bg-slate-100 px-4 py-2.5 text-xs font-bold flex items-center justify-between text-slate-700 dark:text-zinc-350"
                                >
                                    <span>PRACTICE ACTIVITY COUNT SUMMARY</span>
                                    <span>{{ isPracticeSummaryExpanded ? '▲' : '▼' }}</span>
                                </button>
                                <div v-show="isPracticeSummaryExpanded" class="p-3 border-t border-slate-200 dark:border-zinc-800 overflow-x-auto">
                                    <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                        <thead class="bg-[#0f2b5c] text-white text-center font-bold">
                                            <tr>
                                                <th rowspan="2" class="p-2 border border-slate-200 text-left min-w-[200px]">Practice</th>
                                                <th colspan="4" class="p-1 border border-slate-200">Total of Activities</th>
                                                <th rowspan="2" class="p-2 border border-slate-200 w-[80px]">Total</th>
                                            </tr>
                                            <tr>
                                                <th class="p-1 border border-slate-200 w-[70px]">Lv 2</th>
                                                <th class="p-1 border border-slate-200 w-[70px]">Lv 3</th>
                                                <th class="p-1 border border-slate-200 w-[70px]">Lv 4</th>
                                                <th class="p-1 border border-slate-200 w-[70px]">Lv 5</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="ps in practicesSummaryList" :key="ps.practice_id" class="hover:bg-slate-50 dark:hover:bg-zinc-850">
                                                <td class="p-2 border border-slate-200 font-bold text-slate-700 dark:text-zinc-300">{{ ps.practice_id }} — {{ cleanText(ps.practice_name) }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ ps.counts['2'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ ps.counts['3'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ ps.counts['4'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ ps.counts['5'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center font-semibold">{{ ps.total || '-' }}</td>
                                            </tr>
                                            <tr class="bg-amber-50 dark:bg-zinc-800 font-bold border-t-2 border-slate-300 dark:border-zinc-650">
                                                <td class="p-2 border border-slate-200 text-right">Total</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ practicesSummaryTotals['2'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ practicesSummaryTotals['3'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ practicesSummaryTotals['4'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ practicesSummaryTotals['5'] || '-' }}</td>
                                                <td class="p-2 border border-slate-200 text-center">{{ practicesSummaryTotals.total || '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- List of practices -->
                            <div v-for="p in activeObjectivePractices" :key="p.practice_id" class="border border-slate-200 dark:border-zinc-800">
                                <!-- Process Component Banner -->
                                <div class="bg-[#1a3665] text-white px-3 py-2 text-xs font-bold">A. Component: Process</div>

                                <!-- Side by Side: Management Practice vs Example Metrics -->
                                <div class="grid grid-cols-1 md:grid-cols-2 border-b border-slate-200 dark:border-zinc-800 text-xs">
                                    <div class="p-3 border-b md:border-b-0 md:border-r border-slate-200 dark:border-zinc-800">
                                        <div class="font-bold text-[#0f2b5c] dark:text-sky-400 mb-1">{{ p.practice_id }} {{ cleanText(p.practice_name) }}</div>
                                        <div class="text-slate-600 dark:text-zinc-300 leading-relaxed">{{ cleanText(p.practice_description) }}</div>
                                    </div>
                                    <div class="p-3 bg-slate-50 dark:bg-zinc-800/50">
                                        <div class="font-bold text-slate-700 dark:text-zinc-200 mb-1.5 uppercase tracking-wide text-[10px]">Example Metrics</div>
                                        <ul class="list-disc pl-4 space-y-1.5 text-slate-655 dark:text-zinc-350">
                                            <li v-for="(m, idx) in p.metrics" :key="m.id">
                                                {{ String.fromCharCode(97 + idx) }}. {{ cleanText(m.description) }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Activities Table -->
                                <div class="bg-slate-100 dark:bg-zinc-800/80 px-3 py-1.5 font-bold text-xs border-b border-slate-200 dark:border-zinc-800 text-slate-750 dark:text-zinc-200">Activities</div>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse text-xs">
                                        <thead class="bg-slate-50 dark:bg-zinc-850 text-slate-700 dark:text-zinc-300 border-b border-slate-200 dark:border-zinc-800">
                                            <tr>
                                                <th class="p-2 border-r border-slate-200 dark:border-zinc-800 text-center w-[6%]">NO</th>
                                                <th class="p-2 border-r border-slate-200 dark:border-zinc-800 text-left">Activity</th>
                                                <th class="p-2 text-center w-[18%]">Capability Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(act, idx) in p.activities" :key="act.activity_id" class="border-b border-slate-100 dark:border-zinc-800/60 hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                                <td class="p-2 border-r border-slate-200 dark:border-zinc-850 text-center font-medium">{{ idx + 1 }}</td>
                                                <td class="p-2 border-r border-slate-200 dark:border-zinc-850 leading-relaxed">{{ cleanText(act.description) }}</td>
                                                <!-- Rowspan Capability Level -->
                                                <td
                                                    v-if="p.rowspans[idx] > 0"
                                                    :rowspan="p.rowspans[idx]"
                                                    class="p-2 text-center font-bold text-slate-700 dark:text-zinc-300 align-middle bg-slate-50/80 dark:bg-zinc-850/80"
                                                >
                                                    {{ act.capability_lvl ?? act.capability_level ?? '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Related Guidance -->
                                <div class="bg-slate-250 dark:bg-zinc-800 px-3 py-1.5 font-bold text-xs border-y border-slate-200 dark:border-zinc-800 flex justify-between">
                                    <span class="w-[65%]">Related Guidance (Standards, Frameworks, Compliance Requirements)</span>
                                    <span class="w-[35%] text-center border-l border-slate-200 dark:border-zinc-700">Detailed Reference</span>
                                </div>
                                <div class="overflow-x-auto text-xs">
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr v-for="gd in p.guidances" :key="gd.guidance_id" class="border-b border-slate-100 dark:border-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-850">
                                                <td class="p-2 w-[65%] leading-relaxed border-r border-slate-200 dark:border-zinc-800">{{ cleanText(gd.guidance) }}</td>
                                                <td class="p-2 w-[35%] leading-relaxed text-slate-500 dark:text-zinc-400">{{ cleanText(gd.reference) }}</td>
                                            </tr>
                                            <tr v-if="!p.guidances?.length">
                                                <td colspan="2" class="p-3 text-center text-slate-400 italic bg-white dark:bg-zinc-900">
                                                    No related guidance for this management practice
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMATION FLOWS RENDERER -->
                        <div v-else-if="activeComponent === 'infoflows'" class="space-y-4">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-center">
                                        <tr>
                                            <th class="p-2 border border-slate-200 text-left min-w-[200px]">Management Practice</th>
                                            <th class="p-2 border border-slate-200 w-[120px]">From</th>
                                            <th class="p-2 border border-slate-200">Input Description</th>
                                            <th class="p-2 border border-slate-200">Output Description</th>
                                            <th class="p-2 border border-slate-200 w-[140px]">To</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, idx) in activeObjectiveInfoflowRows" :key="idx" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <!-- Rowspanned Practice column -->
                                            <td
                                                v-if="activeObjectiveInfoflowRowspans[idx] > 0"
                                                :rowspan="activeObjectiveInfoflowRowspans[idx]"
                                                class="p-2 border border-slate-200 font-semibold text-slate-700 dark:text-zinc-300 bg-slate-50/80 dark:bg-zinc-850/80 align-middle"
                                            >
                                                {{ cleanText(row.practiceLabel) }}
                                            </td>
                                            <td class="p-2 border border-slate-200">{{ cleanText(row.from) || '-' }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed">{{ cleanText(row.input) || '(No information flows)' }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed">{{ cleanText(row.output) || '-' }}</td>
                                            <td class="p-2 border border-slate-200">{{ cleanText(row.to) || '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ORGANIZATIONAL RENDERER -->
                        <div v-else-if="activeComponent === 'organizational'" class="space-y-4">
                            <div class="bg-slate-50 dark:bg-zinc-800 p-2.5 font-bold text-xs text-slate-800 dark:text-zinc-200 border-b border-slate-200 dark:border-zinc-700">
                                B. Component: Organizational Structures for {{ activeObjectiveId }} — {{ cleanText(activeObjectiveName) }}
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#d2e2d8] dark:bg-zinc-800 text-slate-800 dark:text-zinc-200">
                                        <tr>
                                            <th class="p-2 border border-slate-200 text-left min-w-[260px] align-middle">MANAGEMENT PRACTICE OF GOVERNANCE AND MANAGEMENT OBJECTIVES</th>
                                            <th v-for="rn in activeObjectiveOrganizational.roles" :key="rn" class="p-0 border border-slate-200 w-[44px] text-center align-middle">
                                                <div class="vertical-text text-[10px] font-bold py-1 select-none text-slate-800 dark:text-zinc-355 uppercase">
                                                    {{ cleanText(rn) }}
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="p in activeObjectiveOrganizational.practices" :key="p.practice_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 font-bold text-slate-800 dark:text-zinc-300 leading-normal truncate max-w-[400px]" :title="p.practice_id + ' ' + p.practice_name">
                                                {{ p.practice_id }} {{ cleanText(p.practice_name) }}
                                            </td>
                                            <td v-for="rn in activeObjectiveOrganizational.roles" :key="rn" class="p-2 border border-slate-200 text-center font-extrabold text-sm" :class="getRaciColorClass(p.roleAssignments[rn])">
                                                {{ p.roleAssignments[rn] || '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td :colspan="activeObjectiveOrganizational.roles.length + 1" class="p-0 border-none pt-4">
                                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-850 text-xs">
                                                    <thead>
                                                        <tr class="bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-bold">
                                                            <th class="p-2 border border-slate-200 text-left">Related Guidance</th>
                                                            <th class="p-2 border border-slate-200 text-left">Detailed Reference</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2" class="p-3 text-center text-slate-400 italic bg-white dark:bg-zinc-900">
                                                                No related guidance for this component
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- POLICIES RENDERER -->
                        <div v-else-if="activeComponent === 'policies'" class="space-y-4">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-center">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[90px]">GAMO</th>
                                            <th class="p-2 border border-slate-200 w-[200px] text-left">Policy</th>
                                            <th class="p-2 border border-slate-200 text-left">Description</th>
                                            <th class="p-2 border border-slate-200 text-left">Related Guidance</th>
                                            <th class="p-2 border border-slate-200 text-left w-[200px]">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="pol in activeObjectivePolicies" :key="pol.policy_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 text-center font-bold text-[#0f2b5c] dark:text-sky-400">{{ activeObjectiveId }}</td>
                                            <td class="p-2 border border-slate-200 font-bold text-slate-700 dark:text-zinc-200 leading-normal">{{ cleanText(pol.policy) }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed">{{ cleanText(pol.description) }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-655 dark:text-zinc-350" v-html="cleanText(pol.guidance) || '-'"></td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-500 dark:text-zinc-400" v-html="cleanText(pol.reference) || '-'"></td>
                                        </tr>
                                        <tr v-if="!activeObjectivePolicies.length">
                                            <td colspan="5" class="p-3 text-center text-slate-400 italic bg-white dark:bg-zinc-900">
                                                No policies / procedures found for this objective
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SKILLS RENDERER -->
                        <div v-else-if="activeComponent === 'skills'" class="space-y-4">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-center">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[90px]">GAMO</th>
                                            <th class="p-2 border border-slate-200 text-left">Skill</th>
                                            <th class="p-2 border border-slate-200 text-left">Related Guidance</th>
                                            <th class="p-2 border border-slate-200 text-left w-[200px]">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="sk in activeObjectiveSkills" :key="sk.skill_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 text-center font-bold text-[#0f2b5c] dark:text-sky-400">{{ activeObjectiveId }}</td>
                                            <td class="p-2 border border-slate-200 font-bold text-slate-700 dark:text-zinc-200 leading-normal">{{ cleanText(sk.skill) }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-655 dark:text-zinc-350" v-html="cleanText(sk.guidance) || '-'"></td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-500 dark:text-zinc-400" v-html="cleanText(sk.reference) || '-'"></td>
                                        </tr>
                                        <tr v-if="!activeObjectiveSkills.length">
                                            <td colspan="4" class="p-3 text-center text-slate-400 italic bg-white dark:bg-zinc-900">
                                                No skills found for this objective
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- CULTURE & ETHICS RENDERER -->
                        <div v-else-if="activeComponent === 'culture'" class="space-y-4">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-center">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[90px]">GAMO</th>
                                            <th class="p-2 border border-slate-200 text-left">Element</th>
                                            <th class="p-2 border border-slate-200 text-left">Guidance</th>
                                            <th class="p-2 border border-slate-200 text-left w-[200px]">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="cul in activeObjectiveCulture" :key="cul.keyculture_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 text-center font-bold text-[#0f2b5c] dark:text-sky-400">{{ activeObjectiveId }}</td>
                                            <td class="p-2 border border-slate-200 font-bold text-slate-700 dark:text-zinc-200 leading-normal">{{ cleanText(cul.element) }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-655 dark:text-zinc-350" v-html="cleanText(cul.guidance) || '-'"></td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-500 dark:text-zinc-400" v-html="cleanText(cul.reference) || '-'"></td>
                                        </tr>
                                        <tr v-if="!activeObjectiveCulture.length">
                                            <td colspan="4" class="p-3 text-center text-slate-400 italic bg-white dark:bg-zinc-900">
                                                No culture elements found for this objective
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SERVICES RENDERER -->
                        <div v-else-if="activeComponent === 'services'" class="space-y-4">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-center">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[90px]">GAMO</th>
                                            <th class="p-2 border border-slate-200 text-left">Service / SIA Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="sia in activeObjectiveServices" :key="sia.sia_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 text-center font-bold text-[#0f2b5c] dark:text-sky-400">{{ activeObjectiveId }}</td>
                                            <td class="p-2 border border-slate-200 leading-normal">{{ cleanText(sia.description) }}</td>
                                        </tr>
                                        <tr v-if="!activeObjectiveServices.length">
                                            <td colspan="2" class="p-3 text-center text-slate-400 italic bg-white dark:bg-zinc-900">
                                                No services / SIA found for this objective
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. MODE VIEW BY COMPONENT -->
                <div v-else-if="activeMode === 'component'" class="space-y-4">
                    <!-- Dropdown Component Selector -->
                    <div class="bg-white dark:bg-zinc-900 p-3.5 border border-slate-200 dark:border-zinc-800 flex flex-col md:flex-row items-center gap-3">
                        <label for="componentSelect" class="text-xs font-bold text-slate-700 dark:text-zinc-300 w-24">Component</label>
                        <select
                            id="componentSelect"
                            v-model="selectedComponent"
                            class="flex-1 text-xs border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 p-2 outline-none"
                        >
                            <option value="">-- Lihat per Component (semua objective) --</option>
                            <option value="overview">Overview</option>
                            <option value="practices">A.Component: Process</option>
                            <option value="organizational">B.Component: Organizational Structures</option>
                            <option value="infoflows">C.Component: Information Flows and Items</option>
                            <option value="skills">D.Component: People, Skills and Competencies</option>
                            <option value="policies">E.Component: Policies and Procedures</option>
                            <option value="culture">F.Component: Culture, Ethics and Behavior</option>
                            <option value="services">G.Component: Services, Infrastructure and Applications</option>
                        </select>
                    </div>

                    <!-- Component display area if selected -->
                    <div v-if="selectedComponent" class="space-y-4">
                        <!-- Prefix Filter Pills for Component Mode -->
                        <div class="bg-white dark:bg-zinc-900 p-2.5 border border-slate-200 dark:border-zinc-800 space-y-3">
                            <div class="flex flex-wrap gap-1 border-b border-slate-100 dark:border-zinc-800 pb-2">
                                <button
                                    @click="selectComponentDomain('ALL')"
                                    class="px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition border"
                                    :class="componentDomainFilter === 'ALL'
                                        ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white shadow-sm'
                                        : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-350'"
                                >
                                    All
                                </button>
                                <button
                                    v-for="dom in domains"
                                    :key="dom"
                                    @click="selectComponentDomain(dom)"
                                    class="px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition border"
                                    :class="componentDomainFilter === dom
                                        ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white shadow-sm'
                                        : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-350'"
                                >
                                    {{ dom }}
                                </button>
                            </div>

                            <!-- Objective filters inside component mode -->
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <button
                                    @click="componentObjectiveFilter = 'ALL'"
                                    class="px-2.5 py-1 text-xs font-bold transition border"
                                    :class="componentObjectiveFilter === 'ALL'
                                        ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white'
                                        : 'bg-white border-slate-200 text-[#0f2b5c] hover:bg-slate-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-355'"
                                >
                                    All
                                </button>
                                <button
                                    v-for="obj in filteredObjectivesForComponentMode"
                                    :key="obj.objective_id"
                                    @click="componentObjectiveFilter = obj.objective_id"
                                    class="px-2.5 py-1 text-xs font-bold transition border"
                                    :class="componentObjectiveFilter === obj.objective_id
                                        ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white'
                                        : 'bg-white border-slate-200 text-[#0f2b5c] hover:bg-slate-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-355'"
                                >
                                    {{ obj.objective_id }}
                                </button>
                            </div>
                        </div>

                        <!-- Card displaying aggregated data -->
                        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 shadow-sm p-4">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-[#0f2b5c] dark:text-sky-400 mb-4 border-b border-slate-100 dark:border-zinc-800 pb-2">
                                Menampilkan {{ COMPONENT_LABELS[selectedComponent] }} dari {{ componentObjectiveFilter === 'ALL' ? 'Semua Objective' : componentObjectiveFilter }}
                            </h2>

                            <!-- Dynamic render panel depending on selectedComponent -->
                            <div class="space-y-6">
                                <!-- Loop each objective -->
                                <div v-for="obj in filteredObjectivesForComponent" :key="obj.objective_id" class="border border-slate-250 dark:border-zinc-800 p-3 space-y-4">
                                    <div class="bg-[#0f2b5c] text-white px-3 py-1.5 font-bold text-xs">
                                        {{ obj.objective_id }} — {{ cleanText(obj.objective) }}
                                    </div>

                                    <!-- Render overview inside loop -->
                                    <div v-if="selectedComponent === 'overview'" class="space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                            <div class="bg-slate-50 dark:bg-zinc-850 p-2 border border-slate-100 dark:border-zinc-800">
                                                <div class="font-bold text-slate-800 dark:text-zinc-200 mb-1">Description</div>
                                                <p>{{ cleanText(getOverviewForObjective(obj.objective_id)?.description) || '-' }}</p>
                                            </div>
                                            <div class="bg-slate-50 dark:bg-zinc-850 p-2 border border-slate-100 dark:border-zinc-800">
                                                <div class="font-bold text-slate-800 dark:text-zinc-200 mb-1">Purpose</div>
                                                <p>{{ cleanText(getOverviewForObjective(obj.objective_id)?.purpose) || '-' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Render practices inside loop -->
                                    <div v-else-if="selectedComponent === 'practices'" class="space-y-4">
                                        <div v-for="p in getPracticesForObjective(obj.objective_id)" :key="p.practice_id" class="border border-slate-100 dark:border-zinc-800 text-xs">
                                            <div class="bg-slate-50 dark:bg-zinc-800 px-2 py-1 font-bold text-[#0f2b5c] dark:text-sky-400 border-b border-slate-100 dark:border-zinc-700">
                                                {{ p.practice_id }} {{ cleanText(p.practice_name) }}
                                            </div>
                                            <div class="p-2 text-slate-600 dark:text-zinc-300 leading-normal">{{ cleanText(p.practice_description) }}</div>
                                        </div>
                                    </div>

                                    <!-- Render infoflows inside loop -->
                                    <div v-else-if="selectedComponent === 'infoflows'" class="space-y-2">
                                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-800 text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-bold border-b border-slate-200 dark:border-zinc-800">
                                                    <th class="p-1 border border-slate-200">Practice</th>
                                                    <th class="p-1 border border-slate-200">From</th>
                                                    <th class="p-1 border border-slate-200">Input</th>
                                                    <th class="p-1 border border-slate-200">Output</th>
                                                    <th class="p-1 border border-slate-200">To</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(row, rIdx) in getInfoflowRowsForObjective(obj.objective_id)" :key="rIdx" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                                    <td v-if="getInfoflowRowspansForObjective(obj.objective_id)[rIdx] > 0" :rowspan="getInfoflowRowspansForObjective(obj.objective_id)[rIdx]" class="p-1 border border-slate-200 font-bold align-middle bg-slate-50/50 dark:bg-zinc-850/50">
                                                        {{ row.practiceId }}
                                                    </td>
                                                    <td class="p-1 border border-slate-200">{{ cleanText(row.from) || '-' }}</td>
                                                    <td class="p-1 border border-slate-200 leading-relaxed">{{ cleanText(row.input) }}</td>
                                                    <td class="p-1 border border-slate-200 leading-relaxed">{{ cleanText(row.output) || '-' }}</td>
                                                    <td class="p-1 border border-slate-200">{{ cleanText(row.to) || '-' }}</td>
                                                </tr>
                                                <tr v-if="!getInfoflowRowsForObjective(obj.objective_id).length">
                                                    <td colspan="5" class="p-2 text-center text-slate-400 italic">No information flows found</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Render organizational matrix inside loop -->
                                    <div v-else-if="selectedComponent === 'organizational'" class="space-y-2">
                                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-800 text-[11px]">
                                            <thead>
                                                <tr class="bg-[#d2e2d8] dark:bg-zinc-800 text-slate-800 dark:text-zinc-200">
                                                    <th class="p-1.5 border border-slate-200 text-left min-w-[260px] align-middle">MANAGEMENT PRACTICE OF GOVERNANCE AND MANAGEMENT OBJECTIVES</th>
                                                    <th v-for="rn in getOrgRolesForObjective(obj.objective_id).roles" :key="rn" class="p-0 border border-slate-200 w-[44px] text-center align-middle">
                                                        <div class="vertical-text text-[10px] font-bold py-1 select-none text-slate-855 dark:text-zinc-250 uppercase">
                                                            {{ cleanText(rn) }}
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="p in getOrgRolesForObjective(obj.objective_id).practices" :key="p.practice_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                                    <td class="p-1.5 border border-slate-200 font-bold text-slate-800 dark:text-zinc-300">
                                                        {{ p.practice_id }} {{ cleanText(p.practice_name) }}
                                                    </td>
                                                    <td v-for="rn in getOrgRolesForObjective(obj.objective_id).roles" :key="rn" class="p-1.5 border border-slate-200 text-center font-extrabold text-sm" :class="getRaciColorClass(p.roleAssignments[rn])">
                                                        {{ p.roleAssignments[rn] || '' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Render policies inside loop -->
                                    <div v-else-if="selectedComponent === 'policies'" class="space-y-2">
                                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-800 text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 dark:bg-zinc-800">
                                                    <th class="p-1 border border-slate-200 text-left w-[180px]">Policy</th>
                                                    <th class="p-1 border border-slate-200 text-left">Description</th>
                                                    <th class="p-1 border border-slate-200 text-left w-[200px]">Reference</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="p in getPoliciesForObjective(obj.objective_id)" :key="p.policy_id">
                                                    <td class="p-1.5 border border-slate-200 font-bold">{{ cleanText(p.policy) }}</td>
                                                    <td class="p-1.5 border border-slate-200 leading-normal">{{ cleanText(p.description) }}</td>
                                                    <td class="p-1.5 border border-slate-200 text-slate-550" v-html="cleanText(p.reference) || '-'"></td>
                                                </tr>
                                                <tr v-if="!getPoliciesForObjective(obj.objective_id).length">
                                                    <td colspan="3" class="p-2 text-center text-slate-400 italic">No policies mapped</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Render skills inside loop -->
                                    <div v-else-if="selectedComponent === 'skills'" class="space-y-2">
                                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-800 text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 dark:bg-zinc-800">
                                                    <th class="p-1 border border-slate-200 text-left">Skill</th>
                                                    <th class="p-1 border border-slate-200 text-left">Guidance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="s in getSkillsForObjective(obj.objective_id)" :key="s.skill_id">
                                                    <td class="p-1.5 border border-slate-200 font-bold">{{ cleanText(s.skill) }}</td>
                                                    <td class="p-1.5 border border-slate-200 leading-normal" v-html="cleanText(s.guidance) || '-'"></td>
                                                </tr>
                                                <tr v-if="!getSkillsForObjective(obj.objective_id).length">
                                                    <td colspan="2" class="p-2 text-center text-slate-400 italic">No skills mapped</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Render culture inside loop -->
                                    <div v-else-if="selectedComponent === 'culture'" class="space-y-2">
                                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-800 text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 dark:bg-zinc-800">
                                                    <th class="p-1 border border-slate-200 text-left">Element</th>
                                                    <th class="p-1 border border-slate-200 text-left">Guidance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="c in getCultureForObjective(obj.objective_id)" :key="c.keyculture_id">
                                                    <td class="p-1.5 border border-slate-200 font-bold">{{ cleanText(c.element) }}</td>
                                                    <td class="p-1.5 border border-slate-200 leading-normal" v-html="cleanText(c.guidance) || '-'"></td>
                                                </tr>
                                                <tr v-if="!getCultureForObjective(obj.objective_id).length">
                                                    <td colspan="2" class="p-2 text-center text-slate-400 italic">No culture elements mapped</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Render services inside loop -->
                                    <div v-else-if="selectedComponent === 'services'" class="space-y-2">
                                        <table class="w-full border-collapse border border-slate-200 dark:border-zinc-800 text-xs">
                                            <thead>
                                                <tr class="bg-slate-50 dark:bg-zinc-800">
                                                    <th class="p-1 border border-slate-200 text-left">Service / SIA Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="s in getServicesForObjective(obj.objective_id)" :key="s.sia_id">
                                                    <td class="p-1.5 border border-slate-200">{{ cleanText(s.description) }}</td>
                                                </tr>
                                                <tr v-if="!getServicesForObjective(obj.objective_id).length">
                                                    <td class="p-2 text-center text-slate-400 italic">No services mapped</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. MODE MASTER VIEW -->
                <div v-else-if="activeMode === 'master'" class="space-y-4">
                    <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 shadow-sm p-4">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-[#0f2b5c] dark:text-sky-400 border-b border-slate-100 dark:border-zinc-800 pb-2 mb-4">
                            Data Master
                        </h2>

                        <!-- Master Tab Buttons -->
                        <div class="flex border-b border-slate-200 dark:border-zinc-800 mb-4">
                            <button
                                @click="activeMasterTab = 'eg'"
                                class="px-4 py-2 text-xs font-bold border-b-2 tracking-wider"
                                :class="activeMasterTab === 'eg'
                                    ? 'border-[#0f2b5c] text-[#0f2b5c] dark:border-sky-400 dark:text-sky-400 font-extrabold'
                                    : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            >
                                Enterprise Goals
                            </button>
                            <button
                                @click="activeMasterTab = 'ag'"
                                class="px-4 py-2 text-xs font-bold border-b-2 tracking-wider"
                                :class="activeMasterTab === 'ag'
                                    ? 'border-[#0f2b5c] text-[#0f2b5c] dark:border-sky-400 dark:text-sky-400 font-extrabold'
                                    : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            >
                                Alignment Goals
                            </button>
                            <button
                                @click="activeMasterTab = 'roles'"
                                class="px-4 py-2 text-xs font-bold border-b-2 tracking-wider"
                                :class="activeMasterTab === 'roles'
                                    ? 'border-[#0f2b5c] text-[#0f2b5c] dark:border-sky-400 dark:text-sky-400 font-extrabold'
                                    : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            >
                                Roles
                            </button>
                            <button
                                @click="activeMasterTab = 'cap'"
                                class="px-4 py-2 text-xs font-bold border-b-2 tracking-wider"
                                :class="activeMasterTab === 'cap'
                                    ? 'border-[#0f2b5c] text-[#0f2b5c] dark:border-sky-400 dark:text-sky-400 font-extrabold'
                                    : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            >
                                Capability & Maturity Level
                            </button>
                        </div>

                        <!-- Panel: Enterprise Goals -->
                        <div v-show="activeMasterTab === 'eg'" class="space-y-2">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-left">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[140px]">Enterprise Goal</th>
                                            <th class="p-2 border border-slate-200">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="eg in masterGoalsData.entergoals" :key="eg.id" class="hover:bg-slate-50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 font-bold text-[#0f2b5c] dark:text-sky-400">{{ eg.id }}</td>
                                            <td class="p-2 border border-slate-200 text-slate-700 dark:text-zinc-300 leading-normal">{{ cleanText(eg.description) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Panel: Alignment Goals -->
                        <div v-show="activeMasterTab === 'ag'" class="space-y-2">
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-left">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[140px]">Alignment Goal</th>
                                            <th class="p-2 border border-slate-200">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ag in masterGoalsData.aligngoals" :key="ag.id" class="hover:bg-slate-50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 font-bold text-[#0f2b5c] dark:text-sky-400">{{ ag.id }}</td>
                                            <td class="p-2 border border-slate-200 text-slate-700 dark:text-zinc-300 leading-normal">{{ cleanText(ag.description) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Panel: Roles -->
                        <div v-show="activeMasterTab === 'roles'" class="space-y-4">
                            <div class="text-[11px] text-slate-500 italic flex items-center gap-1.5 bg-slate-50 dark:bg-zinc-800 p-2 border-l-4 border-sky-400">
                                <span class="font-bold text-sky-600 dark:text-sky-400">💡 Tip:</span> Klik pada baris role untuk melihat pemetaan GAMO mana saja yang didukung role tersebut beserta RACI-nya.
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-left">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[180px]">Role Name</th>
                                            <th class="p-2 border border-slate-200">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="r in masterRolesData"
                                            :key="r.name"
                                            @click="selectRoleForDetail(r)"
                                            class="hover:bg-slate-50 dark:hover:bg-zinc-850 cursor-pointer"
                                            :class="selectedRoleDetail?.name === r.name ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-l-4 border-[#0f2b5c] font-semibold' : ''"
                                        >
                                            <td class="p-2 border border-slate-200 font-bold text-slate-700 dark:text-zinc-200">{{ cleanText(r.name) }}</td>
                                            <td class="p-2 border border-slate-200 text-slate-500 dark:text-zinc-400">{{ cleanText(r.description) || '(no description)' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Role GAMO Detail Panel inside drawer -->
                            <div v-if="selectedRoleDetail" class="mt-4 border border-sky-300 dark:border-zinc-700 bg-sky-50/10 dark:bg-zinc-900 shadow-sm">
                                <div class="bg-sky-600 text-white p-3.5 font-bold text-xs flex justify-between items-center">
                                    <span>ROLE DETAIL MATRIX: {{ cleanText(selectedRoleDetail.name) }} (Muncul di {{ activeRoleGamoRows.length }} Practice)</span>
                                    <button @click="selectedRoleDetail = null" class="text-white hover:text-sky-200 text-sm font-extrabold">&times;</button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                        <thead class="bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-bold">
                                            <tr>
                                                <th class="p-2 border border-slate-200 text-center w-[120px]">GAMO</th>
                                                <th class="p-2 border border-slate-200 text-left">Practice</th>
                                                <th class="p-2 border border-slate-200 text-center w-[100px]">RACI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="d in activeRoleGamoRows" :key="d.practice_id" class="hover:bg-slate-100 dark:hover:bg-zinc-850">
                                                <td class="p-2 border border-slate-200 text-center font-bold text-[#0f2b5c] dark:text-sky-400">{{ d.gamo }}</td>
                                                <td class="p-2 border border-slate-200 leading-normal">{{ d.practice_id }} — {{ cleanText(d.practice_name) }}</td>
                                                <td class="p-2 border border-slate-200 text-center font-extrabold text-[#7a2433] dark:text-rose-450">{{ d.raci || '-' }}</td>
                                            </tr>
                                            <tr v-if="!activeRoleGamoRows.length">
                                                <td colspan="3" class="p-3 text-center text-slate-400 italic">Role ini tidak ditemukan di GAMO manapun</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Panel: Capability Levels -->
                        <div v-show="activeMasterTab === 'cap'" class="space-y-4">
                            <!-- Mini tab filter prefixes -->
                            <div class="flex flex-wrap gap-1 border-b border-slate-100 dark:border-zinc-800 pb-2">
                                <button
                                    @click="activeCapPrefix = 'ALL'"
                                    class="px-3 py-1.5 text-xs font-bold uppercase border"
                                    :class="activeCapPrefix === 'ALL'
                                        ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white'
                                        : 'bg-white border-slate-200 text-slate-655 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-350'"
                                >
                                    All
                                </button>
                                <button
                                    v-for="pref in domains"
                                    :key="pref"
                                    @click="activeCapPrefix = pref"
                                    class="px-3 py-1.5 text-xs font-bold uppercase border"
                                    :class="activeCapPrefix === pref
                                        ? 'bg-[#0f2b5c] border-[#0f2b5c] text-white'
                                        : 'bg-white border-slate-200 text-slate-655 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-350'"
                                >
                                    {{ pref }}
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-200 dark:border-zinc-700 text-xs">
                                    <thead class="bg-[#0f2b5c] text-white font-bold text-center">
                                        <tr>
                                            <th class="p-2 border border-slate-200 w-[100px]">GAMO</th>
                                            <th class="p-2 border border-slate-200 w-[240px] text-left">Practice</th>
                                            <th class="p-2 border border-slate-200 text-left">Activity</th>
                                            <th class="p-2 border border-slate-200 w-[120px]">Capability Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, idx) in filteredCapabilityRows" :key="idx" class="hover:bg-slate-50/50 dark:hover:bg-zinc-850">
                                            <td class="p-2 border border-slate-200 text-center font-bold text-slate-700 dark:text-zinc-300">{{ row.gamo }}</td>
                                            <td class="p-2 border border-slate-200 leading-normal">{{ row.practice_id }} — {{ cleanText(row.practice_name) }}</td>
                                            <td class="p-2 border border-slate-200 leading-relaxed text-slate-655 dark:text-zinc-400">{{ cleanText(row.activity) }}</td>
                                            <!-- Rowspan capability level -->
                                            <td
                                                v-if="capabilityRowspans[idx] > 0"
                                                :rowspan="capabilityRowspans[idx]"
                                                class="p-2 border border-slate-200 text-center font-bold text-slate-700 dark:text-zinc-200 align-middle bg-slate-50/80 dark:bg-zinc-850/80"
                                            >
                                                {{ row.level || '-' }}
                                            </td>
                                        </tr>
                                        <tr v-if="!filteredCapabilityRows.length">
                                            <td colspan="4" class="p-3 text-center text-slate-400 italic">No capability data found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import {
    Squares2X2Icon,
    ArrowTopRightOnSquareIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline';

// Config & constants
const domains = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
const COMPONENT_LABELS = {
    overview: 'Overview',
    practices: 'Practices',
    infoflows: 'Information Flows',
    organizational: 'Organizational',
    policies: 'Policies',
    skills: 'Skills',
    culture: 'Culture & Ethics',
    services: 'Services'
};
const domainNamesMap = {
    EDM: 'Evaluate, Direct and Monitor',
    APO: 'Align, Plan and Organize',
    BAI: 'Build, Acquire and Implement',
    DSS: 'Deliver, Service and Support',
    MEA: 'Monitor, Evaluate and Assess'
};

// Application State
const activeMode = ref('gamo'); // 'gamo', 'component', 'master'
const activeDomain = ref('EDM');
const activeObjectiveId = ref('EDM01');
const activeComponent = ref('overview');
const selectedComponent = ref('');
const inputMode = ref(false);
const loading = ref(false);
const error = ref(null);

// Focus Area / Model State
const focusAreas = ref([]);
const activeFocusArea = ref(null);
const showFocusAreaDropdown = ref(false);

// Component Filter States for Component Mode
const componentDomainFilter = ref('ALL');
const componentObjectiveFilter = ref('ALL');

// Master Tab state
const activeMasterTab = ref('eg');
const activeCapPrefix = ref('ALL');
const selectedRoleDetail = ref(null);

// Accordion open/close state
const isPracticeSummaryExpanded = ref(false);

// Live API Caches
const gamoObjectives = ref([]); // stores `/api/cobit/gamo-infoflow` objectives
const masterRoles = ref([]); // stores roles from `/api/cobit/gamo-infoflow`
const cachedComponents = ref({
    overview: null,
    practices: null,
    infoflows: null,
    organizational: null,
    policies: null,
    skills: null,
    culture: null,
    services: null,
    goals: null
});

// Clean text function to strip quotes (leading, trailing, and internal)
function cleanText(str) {
    if (str === null || str === undefined) return '';
    let res = String(str);
    res = res
        .replaceAll('&amp;quot;', '')
        .replaceAll('&quot;', '')
        .replaceAll('&#34;', '')
        .replaceAll('&ldquo;', '')
        .replaceAll('&rdquo;', '')
        .replaceAll('“', '')
        .replaceAll('”', '')
        .replaceAll('&lsquo;', '')
        .replaceAll('&rsquo;', '')
        .replaceAll('‘', '')
        .replaceAll('’', '')
        .replaceAll('&#39;', '');
    // Strip leading and trailing quotes and backslashes
    res = res.trim().replace(/^["'\\\s]+|["'\\\s]+$/g, '');
    // Replace any remaining escaped quotes or literal double quotes
    res = res.replaceAll('\\"', '').replaceAll('"', '');
    return res;
}

// Helper: Calculate row spans for merged columns
function computeRowspans(list, keyFn) {
    const rowspans = [];
    for (let i = 0; i < list.length; ) {
        const val = keyFn(list[i]);
        let j = i + 1;
        while (j < list.length && keyFn(list[j]) === val) {
            j++;
        }
        rowspans[i] = j - i;
        for (let k = i + 1; k < j; k++) {
            rowspans[k] = 0;
        }
        i = j;
    }
    return rowspans;
}

// Fetch dynamic component endpoint from Live API
// Fetch dynamic component endpoint from Live API
async function loadComponentData(compName) {
    if (cachedComponents.value[compName]) return;
    loading.value = true;
    error.value = null;
    try {
        let url = `https://cobit2019.divusi.co.id/api/cobit/components/${compName}`;
        if (activeFocusArea.value) {
            url += `?focus_area_id=${activeFocusArea.value.id}`;
        }
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Server returned HTTP Error ${response.status}`);
        const result = await response.json();
        if (result.success) {
            // Hanya ambil core model (objective_id tidak mengandung titik, misal abaikan EDM.01.M25 atau .m3)
            cachedComponents.value[compName] = result.data.filter(obj => obj.objective_id && !obj.objective_id.includes('.'));
        } else {
            throw new Error(result.message || 'API call failed');
        }
    } catch (err) {
        console.error(`Error loading component ${compName}:`, err);
        error.value = `Gagal memuat komponen ${compName} dari API: ${err.message}`;
    } finally {
        loading.value = false;
    }
}

// Load GAMO info flow containing core list of objectives and practices
async function loadGamoData(focusAreaId = null) {
    loading.value = true;
    error.value = null;
    try {
        let url = 'https://cobit2019.divusi.co.id/api/cobit/gamo-infoflow';
        const faId = focusAreaId !== null ? focusAreaId : (activeFocusArea.value?.id || null);
        if (faId) {
            url += `?focus_area_id=${faId}`;
        }
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Server returned HTTP Error ${response.status}`);
        const result = await response.json();
        if (result.success) {
            // Hanya ambil core model
            gamoObjectives.value = result.objectives.filter(obj => obj.objective_id && !obj.objective_id.includes('.'));
            masterRoles.value = result.roles;
        } else {
            throw new Error(result.message || 'API call failed');
        }
    } catch (err) {
        console.error('Error loading GAMO data:', err);
        error.value = `Gagal memuat data GAMO: ${err.message}`;
    } finally {
        loading.value = false;
    }
}

// Fetch all available focus areas (models) from API
async function loadFocusAreas() {
    try {
        const url = 'https://cobit2019.divusi.co.id/api/cobit/focus-areas';
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Server returned HTTP Error ${response.status}`);
        const result = await response.json();
        if (result.success) {
            focusAreas.value = result.focus_areas || [];
            // Set Core Model as the default focus area
            if (focusAreas.value.length) {
                const coreModel = focusAreas.value.find(fa => fa.code === 'CORE MODEL' || fa.id === 1);
                activeFocusArea.value = coreModel || focusAreas.value[0];
            }
        }
    } catch (err) {
        console.error('Error loading focus areas:', err);
    }
}

// Handler for changing focus area/model
async function selectFocusArea(focusArea) {
    showFocusAreaDropdown.value = false;
    
    // Clear components cache
    cachedComponents.value = {
        overview: null,
        practices: null,
        infoflows: null,
        organizational: null,
        policies: null,
        skills: null,
        culture: null,
        services: null,
        goals: null
    };
    
    // Load GAMO data for the new focus area first
    await loadGamoData(focusArea.id);
    
    // Determine the next active objective ID
    let nextObjectiveId = activeObjectiveId.value;
    if (gamoObjectives.value.length) {
        const exists = gamoObjectives.value.some(o => o.objective_id === activeObjectiveId.value);
        if (!exists) {
            nextObjectiveId = gamoObjectives.value[0].objective_id;
        }
    }
    
    // Synchronously update selection state to trigger watchers in a single batch
    activeObjectiveId.value = nextObjectiveId;
    activeDomain.value = nextObjectiveId.substring(0, 3);
    activeFocusArea.value = focusArea;
}

onMounted(async () => {
    await loadFocusAreas();
    await loadGamoData();
});

// Reactively fetch components on tabs changes
watch([activeObjectiveId, activeComponent, activeMode, selectedComponent, activeFocusArea], async () => {
    if (activeMode.value === 'gamo') {
        if (activeComponent.value === 'overview') {
            await Promise.all([loadComponentData('overview'), loadComponentData('goals')]);
        } else if (activeComponent.value === 'practices') {
            await loadComponentData('practices');
        } else if (activeComponent.value === 'infoflows') {
            await loadComponentData('infoflows');
            await loadComponentData('practices');
        } else if (activeComponent.value === 'organizational') {
            await loadComponentData('practices');
        } else if (activeComponent.value === 'policies') {
            await loadComponentData('policies');
        } else if (activeComponent.value === 'skills') {
            await loadComponentData('skills');
        } else if (activeComponent.value === 'culture') {
            await loadComponentData('culture');
        } else if (activeComponent.value === 'services') {
            await loadComponentData('services');
        }
    } else if (activeMode.value === 'component') {
        if (selectedComponent.value) {
            await loadComponentData(selectedComponent.value);
            if (selectedComponent.value === 'infoflows') {
                await loadComponentData('practices');
            }
        }
    } else if (activeMode.value === 'master') {
        await Promise.all([loadComponentData('goals'), loadComponentData('practices')]);
    }
}, { immediate: false });

// Mode handler
function setMode(mode) {
    activeMode.value = mode;
    if (mode === 'component' && !selectedComponent.value) {
        selectedComponent.value = 'overview';
    }
}

// Domain Selector
function selectDomain(dom) {
    activeDomain.value = dom;
    // Auto select first objective of selected domain
    const filtered = gamoObjectives.value.filter(o => o.objective_id.startsWith(dom));
    if (filtered.length) {
        activeObjectiveId.value = filtered[0].objective_id;
    }
}

// Objective Selector
function selectObjective(objId) {
    activeObjectiveId.value = objId;
}

// Filter domain in Component Mode
function selectComponentDomain(dom) {
    componentDomainFilter.value = dom;
    componentObjectiveFilter.value = 'ALL';
}

// Reload action
async function retryLoad() {
    error.value = null;
    await loadGamoData();
}

// Active Objective information properties
const activeObjectiveName = computed(() => {
    const obj = gamoObjectives.value.find(o => o.objective_id === activeObjectiveId.value);
    return obj ? cleanText(obj.objective) : 'Governance Objective';
});

const activeDomainName = computed(() => {
    return cleanText(domainNamesMap[activeDomain.value] || 'IT Governance Domain');
});

// Objectives list filtered for Gamo mode
const filteredObjectives = computed(() => {
    return gamoObjectives.value.filter(o => o.objective_id.startsWith(activeDomain.value));
});

// Overview component data computed for current objective
const overviewData = computed(() => {
    const data = cachedComponents.value.overview;
    if (!data) return null;
    const item = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!item) return null;
    return {
        ...item,
        description: cleanText(item.description),
        purpose: cleanText(item.purpose)
    };
});

// Goals component data computed for current objective
const goalsData = computed(() => {
    const data = cachedComponents.value.goals;
    if (!data) return null;
    const item = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!item) return null;
    return {
        ...item,
        entergoals: (item.entergoals || []).map(eg => ({
            ...eg,
            description: cleanText(eg.description),
            metrics: (eg.metrics || []).map(m => ({
                ...m,
                description: cleanText(m.description)
            }))
        })),
        aligngoals: (item.aligngoals || []).map(ag => ({
            ...ag,
            description: cleanText(ag.description),
            metrics: (item.aligngoalsmetr || ag.metrics || []).map(m => ({
                ...m,
                description: cleanText(m.description)
            }))
        }))
    };
});

// Practices list computed for current objective
const activeObjectivePractices = computed(() => {
    const data = cachedComponents.value.practices;
    if (!data) return [];
    const obj = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!obj || !obj.practices) return [];

    return obj.practices.map(p => {
        // Pre-compute activity capability rowspans inside practice cards
        const rowspans = computeRowspans(p.activities || [], a => cleanText(String(a.capability_lvl ?? a.capability_level ?? '-')));
        return {
            ...p,
            practice_id: cleanText(p.practice_id),
            practice_name: cleanText(p.practice_name),
            practice_description: cleanText(p.practice_description),
            activities: (p.activities || []).map(act => ({
                ...act,
                description: cleanText(act.description),
                capability_lvl: cleanText(act.capability_lvl ?? act.capability_level ?? '-')
            })),
            metrics: (p.metrics || []).map(m => ({
                ...m,
                description: cleanText(m.description)
            })),
            guidances: (p.guidances || []).map(g => ({
                ...g,
                guidance: cleanText(g.guidance),
                reference: cleanText(g.reference)
            })),
            rowspans
        };
    });
});

// Practices activity summary list
const practicesSummaryList = computed(() => {
    return activeObjectivePractices.value.map(practice => {
        const counts = { '2': 0, '3': 0, '4': 0, '5': 0 };
        let total = 0;
        (practice.activities || []).forEach(act => {
            const raw = act.capability_lvl ?? act.capability_level ?? '';
            const m = String(raw).trim().match(/(\d+)/);
            if (m && ['2', '3', '4', '5'].includes(m[1])) {
                counts[m[1]]++;
                total++;
            }
        });
        return {
            practice_id: practice.practice_id,
            practice_name: practice.practice_name,
            counts,
            total
        };
    });
});

const practicesSummaryTotals = computed(() => {
    const totals = { '2': 0, '3': 0, '4': 0, '5': 0, total: 0 };
    practicesSummaryList.value.forEach(ps => {
        totals['2'] += ps.counts['2'];
        totals['3'] += ps.counts['3'];
        totals['4'] += ps.counts['4'];
        totals['5'] += ps.counts['5'];
        totals.total += ps.total;
    });
    return totals;
});

// Map Info flows layout for active objective
function getInfoflowRows(objId) {
    const rows = [];
    const infoflowsComp = cachedComponents.value.infoflows;
    if (!infoflowsComp) return rows;
    const objFlow = infoflowsComp.find(o => o.objective_id === objId);
    if (!objFlow || !objFlow.infoflows) return rows;

    const practicesList = cachedComponents.value.practices?.find(o => o.objective_id === objId)?.practices || [];
    const practicesMap = {};
    practicesList.forEach(p => {
        const pid = cleanText(p.practice_id);
        practicesMap[pid] = {
            practice_id: pid,
            practice_name: cleanText(p.practice_name),
            flows: []
        };
    });

    objFlow.infoflows.forEach(flow => {
        const pid = cleanText(flow.practice_id);
        if (!practicesMap[pid]) {
            practicesMap[pid] = { practice_id: pid, practice_name: '', flows: [] };
        }
        practicesMap[pid].flows.push(flow);
    });

    const sortedIds = Object.keys(practicesMap).sort();
    sortedIds.forEach(pid => {
        const pm = practicesMap[pid];
        const label = `${pm.practice_id} ${pm.practice_name}`.trim();
        const flows = pm.flows;

        if (!flows.length) {
            rows.push({
                practiceId: pm.practice_id,
                practiceLabel: label,
                from: '',
                input: '(No information flows)',
                output: '',
                to: ''
            });
        } else {
            flows.forEach(f => {
                const inp = f.input;
                const outputs = f.connectedoutputs || [];
                if (!outputs.length) {
                    rows.push({
                        practiceId: pm.practice_id,
                        practiceLabel: label,
                        from: cleanText(inp?.from) || '',
                        input: cleanText(inp?.description) || '',
                        output: '',
                        to: ''
                    });
                } else {
                    outputs.forEach(out => {
                        rows.push({
                            practiceId: pm.practice_id,
                            practiceLabel: label,
                            from: cleanText(inp?.from) || '',
                            input: cleanText(inp?.description) || '',
                            output: cleanText(out?.description) || '',
                            to: cleanText(out?.to) || ''
                        });
                    });
                }
            });
        }
    });

    return rows;
}

const activeObjectiveInfoflowRows = computed(() => {
    return getInfoflowRows(activeObjectiveId.value);
});

const activeObjectiveInfoflowRowspans = computed(() => {
    return computeRowspans(activeObjectiveInfoflowRows.value, r => r.practiceId);
});

// Organizational structures roles matrix
const activeObjectiveOrganizational = computed(() => {
    const practicesComp = cachedComponents.value.practices;
    if (!practicesComp) return { roles: [], practices: [] };
    const obj = practicesComp.find(o => o.objective_id === activeObjectiveId.value);
    if (!obj || !obj.practices) return { roles: [], practices: [] };

    const rolesSet = new Set();
    obj.practices.forEach(p => {
        (p.roles || []).forEach(r => {
            if (r.role_name || r.role) rolesSet.add(cleanText(r.role_name || r.role));
        });
    });
    const roles = Array.from(rolesSet).sort();

    const practices = obj.practices.map(p => {
        const roleAssignments = {};
        (p.roles || []).forEach(r => {
            roleAssignments[cleanText(r.role_name || r.role)] = r.raci || '';
        });
        return {
            practice_id: cleanText(p.practice_id),
            practice_name: cleanText(p.practice_name),
            roleAssignments
        };
    });

    return { roles, practices };
});

// Policies
const activeObjectivePolicies = computed(() => {
    const data = cachedComponents.value.policies;
    if (!data) return [];
    const obj = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!obj || !obj.policies) return [];
    return obj.policies.map(p => {
        const guidances = p.guidances || [];
        return {
            policy_id: p.policy_id,
            policy: cleanText(p.policy),
            description: cleanText(p.description),
            guidance: guidances.map(g => cleanText(g.guidance)).filter(Boolean).join('<br>'),
            reference: guidances.map(g => cleanText(g.reference)).filter(Boolean).join('<br>')
        };
    });
});

// Skills
const activeObjectiveSkills = computed(() => {
    const data = cachedComponents.value.skills;
    if (!data) return [];
    const obj = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!obj || !obj.skills) return [];
    return obj.skills.map(s => {
        const guidances = s.guidances || [];
        return {
            skill_id: s.skill_id,
            skill: cleanText(s.skill),
            guidance: guidances.map(g => cleanText(g.guidance)).filter(Boolean).join('<br>'),
            reference: guidances.map(g => cleanText(g.reference)).filter(Boolean).join('<br>')
        };
    });
});

// Culture
const activeObjectiveCulture = computed(() => {
    const data = cachedComponents.value.culture;
    if (!data) return [];
    const obj = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!obj || !obj.culture) return [];
    return obj.culture.map(c => {
        const guidances = c.guidances || [];
        return {
            keyculture_id: c.keyculture_id,
            element: cleanText(c.element),
            guidance: guidances.map(g => cleanText(g.guidance)).filter(Boolean).join('<br>'),
            reference: guidances.map(g => cleanText(g.reference)).filter(Boolean).join('<br>')
        };
    });
});

// Services
const activeObjectiveServices = computed(() => {
    const data = cachedComponents.value.services;
    if (!data) return [];
    const obj = data.find(o => o.objective_id === activeObjectiveId.value);
    if (!obj || !obj.s_i_a) return [];
    return obj.s_i_a.map(sia => ({
        ...sia,
        description: cleanText(sia.description)
    }));
});

// -------------------------------------------------------------
// VIEW BY COMPONENT MODE CALCULATIONS
// -------------------------------------------------------------
const filteredObjectivesForComponentMode = computed(() => {
    if (componentDomainFilter.value === 'ALL') {
        return gamoObjectives.value;
    }
    return gamoObjectives.value.filter(o => o.objective_id.startsWith(componentDomainFilter.value));
});

const filteredObjectivesForComponent = computed(() => {
    let list = gamoObjectives.value;
    if (componentDomainFilter.value !== 'ALL') {
        list = list.filter(o => o.objective_id.startsWith(componentDomainFilter.value));
    }
    if (componentObjectiveFilter.value !== 'ALL') {
        list = list.filter(o => o.objective_id === componentObjectiveFilter.value);
    }
    return list;
});

function getOverviewForObjective(objId) {
    const data = cachedComponents.value.overview;
    if (!data) return null;
    const item = data.find(o => o.objective_id === objId);
    if (!item) return null;
    return {
        ...item,
        description: cleanText(item.description),
        purpose: cleanText(item.purpose)
    };
}

function getPracticesForObjective(objId) {
    const data = cachedComponents.value.practices?.find(o => o.objective_id === objId)?.practices || [];
    return data.map(p => ({
        ...p,
        practice_id: cleanText(p.practice_id),
        practice_name: cleanText(p.practice_name),
        practice_description: cleanText(p.practice_description)
    }));
}

function getInfoflowRowsForObjective(objId) {
    return getInfoflowRows(objId);
}

function getInfoflowRowspansForObjective(objId) {
    return computeRowspans(getInfoflowRowsForObjective(objId), r => r.practiceId);
}

function getOrgRolesForObjective(objId) {
    const practicesComp = cachedComponents.value.practices;
    if (!practicesComp) return { roles: [], practices: [] };
    const obj = practicesComp.find(o => o.objective_id === objId);
    if (!obj || !obj.practices) return { roles: [], practices: [] };

    const rolesSet = new Set();
    obj.practices.forEach(p => {
        (p.roles || []).forEach(r => {
            if (r.role_name || r.role) rolesSet.add(cleanText(r.role_name || r.role));
        });
    });
    const roles = Array.from(rolesSet).sort();

    const practices = obj.practices.map(p => {
        const roleAssignments = {};
        (p.roles || []).forEach(r => {
            roleAssignments[cleanText(r.role_name || r.role)] = r.raci || '';
        });
        return {
            practice_id: cleanText(p.practice_id),
            practice_name: cleanText(p.practice_name),
            roleAssignments
        };
    });

    return { roles, practices };
}

function getPoliciesForObjective(objId) {
    const obj = cachedComponents.value.policies?.find(o => o.objective_id === objId);
    if (!obj || !obj.policies) return [];
    return obj.policies.map(p => ({
        policy_id: p.policy_id,
        policy: cleanText(p.policy),
        description: cleanText(p.description),
        reference: (p.guidances || []).map(g => cleanText(g.reference)).filter(Boolean).join('<br>')
    }));
}

function getSkillsForObjective(objId) {
    const obj = cachedComponents.value.skills?.find(o => o.objective_id === objId);
    if (!obj || !obj.skills) return [];
    return obj.skills.map(s => ({
        skill_id: s.skill_id,
        skill: cleanText(s.skill),
        guidance: (s.guidances || []).map(g => cleanText(g.guidance)).filter(Boolean).join('<br>')
    }));
}

function getCultureForObjective(objId) {
    const obj = cachedComponents.value.culture?.find(o => o.objective_id === objId);
    if (!obj || !obj.culture) return [];
    return obj.culture.map(c => ({
        keyculture_id: c.keyculture_id,
        element: cleanText(c.element),
        guidance: (c.guidances || []).map(g => cleanText(g.guidance)).filter(Boolean).join('<br>')
    }));
}

function getServicesForObjective(objId) {
    const list = cachedComponents.value.services?.find(o => o.objective_id === objId)?.s_i_a || [];
    return list.map(s => ({
        ...s,
        description: cleanText(s.description)
    }));
}

// -------------------------------------------------------------
// MASTER DATA MODE CALCULATIONS
// -------------------------------------------------------------
const masterGoalsData = computed(() => {
    const goalsComp = cachedComponents.value.goals;
    if (!goalsComp) return { entergoals: [], aligngoals: [] };

    const egMap = new Map();
    const agMap = new Map();

    goalsComp.forEach(obj => {
        (obj.entergoals || []).forEach(eg => {
            const id = String(eg.entergoals_id || '').toUpperCase().trim();
            if (id && !egMap.has(id)) {
                egMap.set(id, { id, description: cleanText(eg.description) });
            }
        });
        (obj.aligngoals || []).forEach(ag => {
            const id = String(ag.aligngoals_id || '').toUpperCase().trim();
            if (id && !agMap.has(id)) {
                agMap.set(id, { id, description: cleanText(ag.description) });
            }
        });
    });

    const entergoals = Array.from(egMap.values()).sort((a, b) => a.id.localeCompare(b.id));
    const aligngoals = Array.from(agMap.values()).sort((a, b) => a.id.localeCompare(b.id));

    return { entergoals, aligngoals };
});

const masterRolesData = computed(() => {
    const practicesComp = cachedComponents.value.practices;
    if (!practicesComp) return [];

    const roleMap = new Map();
    practicesComp.forEach(obj => {
        (obj.practices || []).forEach(p => {
            (p.roles || []).forEach(r => {
                const name = cleanText(r.role_name || r.role || '').trim();
                if (name && !roleMap.has(name)) {
                    roleMap.set(name, {
                        id: r.role_id,
                        name,
                        description: cleanText(r.description || '')
                    });
                }
            });
        });
    });

    return Array.from(roleMap.values()).sort((a, b) => a.name.localeCompare(b.name));
});

const roleGamoMap = computed(() => {
    const practicesComp = cachedComponents.value.practices;
    const mapping = {};
    if (!practicesComp) return mapping;

    practicesComp.forEach(obj => {
        (obj.practices || []).forEach(p => {
            (p.roles || []).forEach(r => {
                const name = cleanText(r.role_name || r.role || '').trim();
                if (!name) return;
                if (!mapping[name]) mapping[name] = [];
                mapping[name].push({
                    gamo: obj.objective_id,
                    practice_id: cleanText(p.practice_id),
                    practice_name: cleanText(p.practice_name),
                    raci: r.raci || ''
                });
            });
        });
    });

    return mapping;
});

const activeRoleGamoRows = computed(() => {
    if (!selectedRoleDetail.value) return [];
    return roleGamoMap.value[selectedRoleDetail.value.name] || [];
});

function selectRoleForDetail(role) {
    if (selectedRoleDetail.value?.name === role.name) {
        selectedRoleDetail.value = null;
    } else {
        selectedRoleDetail.value = role;
    }
}

// Capability maturity levels list
const capabilityRows = computed(() => {
    const practicesComp = cachedComponents.value.practices;
    const rows = [];
    if (!practicesComp) return rows;

    practicesComp.forEach(obj => {
        (obj.practices || []).forEach(p => {
            (p.activities || []).forEach(a => {
                const lvl = a.capability_lvl ?? a.capability_level ?? '';
                rows.push({
                    gamo: obj.objective_id,
                    practice_id: cleanText(p.practice_id),
                    practice_name: cleanText(p.practice_name),
                    activity: cleanText(a.description || ''),
                    level: lvl !== null ? cleanText(String(lvl)) : ''
                });
            });
        });
    });

    return rows;
});

const filteredCapabilityRows = computed(() => {
    let rows = capabilityRows.value;
    if (activeCapPrefix.value !== 'ALL') {
        rows = rows.filter(r => r.gamo.startsWith(activeCapPrefix.value));
    }

    const withSortKeys = (r) => {
        const lvlStr = String(r.level).trim();
        const parsed = parseInt(lvlStr.match(/\d+/)?.[0] || '0', 10);
        const isNum = !isNaN(parsed) && parsed > 0;
        const gamoNum = parseInt(r.gamo.match(/\d+/)?.[0] || '0', 10);
        return { parsed, isNum, gamoNum };
    };

    return rows.slice().sort((a, b) => {
        const ka = withSortKeys(a);
        const kb = withSortKeys(b);

        if (ka.isNum && kb.isNum) {
            if (ka.parsed !== kb.parsed) return ka.parsed - kb.parsed;
        } else if (ka.isNum) {
            return -1;
        } else if (kb.isNum) {
            return 1;
        } else {
            const c = a.level.localeCompare(b.level);
            if (c !== 0) return c;
        }

        if (ka.gamoNum !== kb.gamoNum) return ka.gamoNum - kb.gamoNum;
        const g = a.gamo.localeCompare(b.gamo);
        if (g !== 0) return g;

        const p = a.practice_id.localeCompare(b.practice_id);
        if (p !== 0) return p;

        return a.activity.localeCompare(b.activity);
    });
});

const capabilityRowspans = computed(() => {
    return computeRowspans(filteredCapabilityRows.value, r => r.level);
});

function getRaciColorClass(raci) {
    if (!raci) return '';
    const r = String(raci).toUpperCase().trim();
    if (r === 'A') return 'text-[#d93025]';
    if (r === 'R') return 'text-[#188038]';
    if (r === 'C') return 'text-amber-600';
    if (r === 'I') return 'text-blue-600';
    return '';
}
</script>

<style scoped>
.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    white-space: nowrap;
    min-height: 150px;
    display: inline-block;
}
.animate-fade-in-up {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
