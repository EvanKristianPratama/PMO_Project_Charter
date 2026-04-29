<template>
    <UserLayout :title="pageTitle">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <!-- Navigation & Title Header -->
            <section
                class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <button @click="goBack"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 transition-colors hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </button>

                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10" />

                    <label for="initiative-nav" class="text-xs font-medium text-slate-700 dark:text-slate-200">Pilih
                        Initiative</label>
                    <select id="initiative-nav" v-model="selectedInitiativeId"
                        class="w-full max-w-sm rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 focus:border-[#1C75BC] focus:outline-none dark:border-white/10 dark:bg-[#101826] dark:text-slate-100">
                        <option value="" disabled>-- Pilih Initiative --</option>
                        <option v-for="option in (initiativeOptions ?? [])" :key="`initiative-opt-${option.id}`"
                            :value="String(option.id)">
                            {{ formatInitiativeLabel(option) }}
                        </option>
                    </select>

                    <div class="ml-auto flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5">
                        <button v-for="tab in ['Planning', 'Implementation', 'Evaluation']" :key="tab"
                            @click="activeTab = tab" class="rounded-md px-3 py-1 text-xs font-semibold transition-all"
                            :class="activeTab === tab ? 'bg-white text-[#1C75BC] shadow-sm dark:bg-[#1A1A1A] dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'">
                            {{ tab }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- Planning Tab Content -->
            <div v-if="activeTab === 'Planning'" class="space-y-6">
                <!-- 1. Digital Project Charter Document -->
                <div v-if="initiativeMaster" class="space-y-4">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-[#1e4f8f]"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Planning</h2>
                    </div>

                    <!-- Status History Table -->
                    <div
                        class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                        <div
                            class="bg-slate-50 px-4 py-2 border-b border-slate-200 dark:bg-white/5 dark:border-white/10">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                Status History</h3>
                        </div>
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead
                                class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400 border-b border-slate-200 dark:border-white/10">
                                <tr>
                                    <th class="px-4 py-2 w-40">Tanggal</th>
                                    <th class="px-4 py-2 w-48">Status</th>
                                    <th class="px-4 py-2">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                <tr v-for="status in (initiativeMaster.status_history ?? [])" :key="status.id"
                                    class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ formatDate(status.tanggal) }}</td>
                                    <td class="px-4 py-2">
                                        <span
                                            :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-tight capitalize shadow-sm transition-all', getStatusClass(status.status)]">
                                            {{ status.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-slate-500">{{ status.notes || '-' }}</td>
                                </tr>
                                <tr v-if="!initiativeMaster.status_history?.length">
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-500 italic">No status
                                        history available.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <DigitalInitiativeCharterDocument :initiative="initiativeMaster" />
                </div>

                <!-- 2. Digital Roadmap Component -->
                <div v-if="roadmapItems && roadmapItems.length"
                    class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
                    </div>
                    <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear"
                        :end-year="roadmapEndYear" />
                </div>

                <!-- Roadmap not available message -->
                <div v-else class="space-y-1 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
                    </div>
                    <div
                        class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Roadmap Not Available
                        </h3>
                    </div>
                </div>

                <!-- 3. Compendium Charter Document -->
                <div v-if="compendiumData" class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-[#3b5e96]"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Compendium</h2>
                    </div>
                    <CompendiumCharterDocument :form="compendiumData" :editable="false" :coe-options="coeOptions"
                        :source-options="sourceOptions" :theme-options="themeOptions" />
                </div>

                <!-- Compendium not available message -->
                <div v-else class="space-y-1 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-[#3b5e96]"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Compendium</h2>
                    </div>
                    <div
                        class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Compendium Not Available
                        </h3>
                    </div>
                </div>

                <!-- 4. Appendix Charter Document -->
                <div v-if="appendixData" class="space-y-4 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
                    </div>
                    <AppendixCharterDocument :initiative="computedAppendixData" :editable="false"
                        :coe-options="coeOptions" :theme-options="themeOptions"
                        :organization-options="organizationOptions" />
                </div>

                <!-- Appendix not available message -->
                <div v-else class="space-y-1 pt-6 mt-6 border-t border-slate-200 dark:border-white/10">
                    <div class="flex items-center gap-2 px-1">
                        <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Appendix</h2>
                    </div>
                    <div
                        class="rounded-1xl border border-dashed border-slate-300 bg-slate-50/50 p-1 text-center dark:border-white/10 dark:bg-white/5">
                        <h3 class="mt-1 mb-1 text-sm font-bold text-slate-900 dark:text-white">Appendix Not Available
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Implementation Tab Content -->
            <div v-if="activeTab === 'Implementation'" class="space-y-6">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Implementation</h2>
                </div>



                <!-- Digital Initiative Header -->
                <DigitalInitiativeHeader v-if="unifiedInitiative" :initiative="unifiedInitiative"
                    class="overflow-hidden" />

                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status Implementation</h2>
                </div>

                <div
                    class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Start</th>
                                <th class="px-4 py-3">End</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Current PIC</th>
                                <th class="px-4 py-3">Review Status</th>
                                <th class="px-4 py-3">Status Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-for="impl in statusImplementations" :key="impl.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">{{ impl.start || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.end || '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ impl.year || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.pic || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                        {{ impl.review_status || '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ impl.status_updated || '-' }}</td>
                            </tr>
                            <tr v-if="!statusImplementations || !statusImplementations.length">
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500 italic">
                                    Status Implementation Not Available
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-purple-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Digital Initiative Roadmap</h2>
                </div>

                <DigitalRoadmapComponent :data="roadmapItems" :start-year="roadmapStartYear"
                    :end-year="roadmapEndYear" />
            </div>

            <!-- Evaluation Tab Content -->
            <div v-if="activeTab === 'Evaluation'" class="space-y-6">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Evaluation</h2>
                </div>
                <!-- Digital Initiative Evaluation Details -->
                <DigitalInitiativeEvaluation v-if="unifiedInitiative" :initiative="unifiedInitiative"
                    class="overflow-hidden" />

                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status Implementation Analysis</h2>
                </div>

                <div class="flex justify-start">
                    <div class="score-panel w-full max-w-4xl overflow-hidden border border-[#3b82f6] flex flex-col">
                        <div v-if="roadmapDuration" 
                            class="flex border-b border-[#3b82f6] cursor-pointer hover:bg-slate-50 group"
                            @click="isRoadmapExpanded = !isRoadmapExpanded"
                        >
                            <!-- Section 1: Project Duration Info -->
                            <div class="flex flex-1 border-r border-[#3b82f6]">
                                <div
                                    class="bar-sub-mini flex items-center shrink-0 min-w-[130px] justify-center border-r border-[#3b82f6] group-hover:bg-[#255b8a]">
                                    Project Duration</div>
                                <div class="panel-body-mini flex items-center flex-1 justify-start px-4 text-left">
                                    <div class="flex items-center justify-between w-full">
                                        <span>{{ roadmapDuration }}</span>
                                        <svg class="h-4 w-4 text-[#3b82f6] transition-transform duration-200" 
                                            :class="{ 'rotate-180': isRoadmapExpanded }"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Section 2: Mini Roadmap Grid -->
                            <div class="panel-body-mini flex-1 p-0 overflow-hidden relative min-h-[32px]">
                                <!-- Mini Roadmap Grid -->
                                <div class="absolute inset-0 flex">
                                    <div v-for="year in roadmapYears" :key="year"
                                        class="flex-1 border-r-[#3b82f6] border-r-[1.5px] last:border-0 flex flex-col">
                                        <div
                                            class="h-[18px] bg-slate-50 flex items-center justify-center text-[10px] font-bold text-slate-500 border-b border-[#3b82f6] leading-none uppercase tracking-wider">
                                            {{ year }}
                                        </div>
                                        <div class="flex-1 flex">
                                            <div v-for="q in 4" :key="q" class="flex-1 border-r border-slate-100 last:border-0"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Roadmap Bar -->
                                <div class="absolute inset-x-0 top-[18px] bottom-0 flex items-center">
                                    <div class="relative w-full h-2">
                                                <div class="absolute h-full bg-[#1e4f8f] rounded-sm shadow-sm"
                                                    :style="roadmapBarStyle" :title="roadmapDuration"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Status Updated Header (Outside) -->
                            <div class="panel-body-mini w-[100px] border-l border-[#3b82f6] flex items-center justify-center text-[10px] font-bold text-[#1e4f8f] bg-slate-50 uppercase text-center">
                                Status Updated
                            </div>
                        </div>

                        <!-- Expanded Roadmap Content -->
                        <div v-if="isRoadmapExpanded" class="bg-white border-b border-[#3b82f6] animate-fade-in overflow-hidden">
                            <DigitalRoadmapSummary 
                                :items="roadmapItems" 
                                :start-year="roadmapStartYear" 
                                :end-year="roadmapEndYear" 
                            />
                        </div>

                        <div v-if="computedAppendixData && computedAppendixData.urgency_expected !== '-'"
                            class="flex border-b border-[#3b82f6] last:border-0">
                            <!-- Section 1: Expected Go Live Info -->
                            <div class="flex flex-1 border-r border-[#3b82f6]">
                                <div
                                    class="bar-sub-mini flex items-center shrink-0 min-w-[130px] justify-center border-r border-[#3b82f6]">
                                    Expected Go Live</div>
                                <div class="panel-body-mini flex items-center flex-1 justify-start px-4 text-left">
                                    {{ computedAppendixData.urgency_expected }}
                                </div>
                            </div>

                            <!-- Section 2: Mini Roadmap Grid -->
                            <div class="panel-body-mini flex-1 p-0 overflow-hidden relative min-h-[32px]">
                                <!-- Mini Roadmap Grid (Only if expanded) -->
                                <div v-if="isRoadmapExpanded" class="absolute inset-0 flex">
                                    <div v-for="year in roadmapYears" :key="year"
                                        class="flex-1 border-r-[#3b82f6] border-r-[1.5px] last:border-0 flex flex-col">
                                        <div class="flex-1 flex">
                                            <div v-for="q in 4" :key="q" class="flex-1 border-r border-slate-100 last:border-0"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Go Live Bar (Only if expanded) -->
                                <div v-if="isRoadmapExpanded" class="absolute inset-0">
                                    <div class="absolute h-full bg-emerald-500"
                                        :style="goLiveBarStyle" :title="'Expected Go Live: ' + computedAppendixData.urgency_expected"></div>
                                </div>
                            </div>

                            <!-- Section 3: Status Updated Placeholder (Outside) -->
                            <div class="panel-body-mini w-[100px] border-l border-[#3b82f6] bg-slate-50/30"></div>
                        </div>

                        <template v-if="computedAppendixData && computedAppendixData.urgency_expected !== '-'">
                            <DigitalRoadmapStatus
                                v-for="(marker, idx) in statusReviewMarkers"
                                :key="`status-row-${idx}`"
                                :show="true"
                                :isRoadmapExpanded="isRoadmapExpanded"
                                :roadmapYears="roadmapYears"
                                :markers="[marker]"
                                :statusReviewData="`${marker.label} - ${marker.status}`"
                                :statusUpdated="marker.statusUpdated"
                                :isFirst="idx === 0"
                                :isLast="idx === statusReviewMarkers.length - 1"
                            />
                        </template>
                    </div>
                </div>

                <div
                    class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Start</th>
                                <th class="px-4 py-3">End</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Current PIC</th>
                                <th class="px-4 py-3">Review Status</th>
                                <th class="px-4 py-3">Status Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-for="impl in statusImplementations" :key="impl.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">{{ impl.start || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.end || '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ impl.year || '-' }}</td>
                                <td class="px-4 py-3">{{ impl.pic || '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-300">
                                        {{ impl.review_status || '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ impl.status_updated || '-' }}</td>
                            </tr>
                            <tr v-if="!statusImplementations || !statusImplementations.length">
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500 italic">
                                    Status Implementation Not Available
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="flex items-center gap-2 px-1">
                    <div class="h-6 w-1 rounded-full bg-red-600"></div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Summary Review</h2>
                </div>

                <!-- Notes Section -->
                <div class="space-y-4">
                    <!-- Note Form -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#1A1A1A]">
                        <form @submit.prevent="submitNote" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Month</label>
                                    <select v-model="noteForm.month" class="w-full rounded-lg border-slate-200 text-sm dark:border-white/10 dark:bg-white/5">
                                        <option v-for="m in months" :key="m" :value="m">{{ m }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Year</label>
                                    <select v-model="noteForm.year" class="w-full rounded-lg border-slate-200 text-sm dark:border-white/10 dark:bg-white/5">
                                        <option v-for="y in noteYears" :key="y" :value="y">{{ y }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Notes</label>
                                <textarea v-model="noteForm.notes" rows="3" class="w-full rounded-lg border-slate-200 text-sm dark:border-white/10 dark:bg-white/5" placeholder="Enter review notes..."></textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button v-if="editingNoteId" type="button" @click="cancelEditNote" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="noteForm.processing" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                                    {{ editingNoteId ? 'Update Note' : 'Add Note' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notes List -->
                    <div v-if="summaryReviewNotes && summaryReviewNotes.length > 0" class="space-y-3">
                        <div v-for="note in summaryReviewNotes" :key="note.id" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[#1A1A1A] group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold text-blue-600 uppercase">{{ note.month }} {{ note.year }}</span>
                                    </div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ note.notes }}</p>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="editNote(note)" class="p-1 text-slate-400 hover:text-blue-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteNote(note.id)" class="p-1 text-slate-400 hover:text-red-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="rounded-xl border border-dashed border-slate-200 p-8 text-center dark:border-white/10">
                        <p class="text-sm text-slate-500 italic">No review notes available for this initiative.</p>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

// Using DigitalInitiativeCharterDocument that displays the core project charter fields
import DigitalInitiativeCharterDocument from '@/Components/DigitalInitiative/DigitalInitiativeCharterDocument.vue';
import DigitalInitiativeHeader from '@/Components/DigitalInitiative/DigitalInitiativeHeader.vue';
import DigitalInitiativeEvaluation from '@/Components/DigitalInitiative/DigitalInitiativeEvaluation.vue';
import CompendiumCharterDocument from '@/Components/Compendium/CompendiumCharterDocument.vue';
import AppendixCharterDocument from '@/Components/Appendix/AppendixCharterDocument.vue';
import DigitalRoadmapComponent from '@/Components/Roadmap/Digital/DigitalRoadmapComponent.vue';
import DigitalRoadmapSummary from '@/Components/Roadmap/Digital/DigitalRoadmapSummary.vue';
import DigitalRoadmapStatus from '@/Components/Roadmap/Digital/DigitalRoadmapStatus.vue';

const props = defineProps({
    initiativeMaster: { type: Object, default: () => ({}) },
    projectCharter: { type: Object, default: null },
    compendiumData: { type: Object, default: null },
    appendixData: { type: Object, default: null },
    roadmapItems: { type: Array, default: () => [] },
    roadmapStartYear: { type: Number, default: 2024 },
    roadmapEndYear: { type: Number, default: 2029 },
    statusImplementations: { type: Array, default: () => [] },
    summaryReviewNotes: { type: Array, default: () => [] },

    // Options
    coeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
    themeOptions: { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    initiativeOptions: { type: Array, default: () => [] },
});

const activeTab = ref('Planning');
const route = useRouteHelper();

const isRoadmapExpanded = ref(false);

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const noteYears = computed(() => {
    const currentYear = new Date().getFullYear();
    const list = [];
    for (let i = currentYear - 2; i <= currentYear + 5; i++) {
        list.push(i);
    }
    return list;
});

const editingNoteId = ref(null);
const noteForm = useForm({
    initiative_id: props.initiativeMaster?.id,
    month: months[new Date().getMonth()],
    year: new Date().getFullYear(),
    notes: '',
});

const submitNote = () => {
    if (editingNoteId.value) {
        noteForm.put(route('program-evaluation.summary-review.notes.update', editingNoteId.value), {
            onSuccess: () => cancelEditNote(),
        });
    } else {
        noteForm.post(route('program-evaluation.summary-review.notes.store'), {
            onSuccess: () => noteForm.reset('notes'),
        });
    }
};

const editNote = (note) => {
    editingNoteId.value = note.id;
    noteForm.month = note.month;
    noteForm.year = note.year;
    noteForm.notes = note.notes;
};

const cancelEditNote = () => {
    editingNoteId.value = null;
    noteForm.reset('notes', 'month', 'year');
};

const deleteNote = (id) => {
    if (confirm('Are you sure you want to delete this note?')) {
        router.delete(route('program-evaluation.summary-review.notes.destroy', id));
    }
};

const goBack = () => {
    window.history.back();
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(date);
};

const initiativeId = computed(() => Number(props.initiativeMaster?.id ?? 0));

const selectedInitiativeId = computed({
    get: () => (initiativeId.value > 0 ? String(initiativeId.value) : ''),
    set: (value) => {
        const selectedValue = String(value ?? '').trim();
        if (!selectedValue) return;
        if (initiativeId.value > 0 && selectedValue === String(initiativeId.value)) return;
        router.visit(route('program-planning.program-definition.digital-initiatives.summary.index', selectedValue));
    },
});

const formatInitiativeLabel = (option) => {
    const code = String(option?.code ?? '').replace(/#/g, '').trim();
    const name = String(option?.name ?? '').trim();
    if (code && name) return `[${code}] ${name}`;
    return name || code || `Initiative #${option?.id ?? '-'}`;
};

const pageTitle = computed(() => `Capsule Summary - ${props.initiativeMaster?.code}`);

const unifiedInitiative = computed(() => ({
    ...props.initiativeMaster,
    appendix_data: props.appendixData,
    project_charter: props.projectCharter,
}));

const hasAnyData = computed(() => {
    return props.projectCharter || props.compendiumData || props.appendixData || (props.roadmapItems && props.roadmapItems.length > 0);
});

// We need to map `appendixData` props to the shape that `AppendixCharterDocument`'s `initiative` needs
// Just like in Compendium/Show.vue `appendixData` computed property.
const computedAppendixData = computed(() => {
    const a = props.appendixData;
    if (!a) return null;

    const getLabel = (val) => {
        if (val === 1) return 'High';
        if (val === 2) return 'Medium';
        if (val === 3) return 'Low';
        return '-';
    };

    let signBy = a?.sign_by ?? [];
    if (typeof signBy === 'string') {
        try { signBy = JSON.parse(signBy); } catch { signBy = signBy ? [signBy] : []; }
    }

    const themeMap = new Map((props.themeOptions ?? []).map(t => [Number(t.id), t]));
    const rjppThemes = (a?.rjpp_tagging_ids ?? []).map(id => themeMap.get(Number(id))).filter(Boolean);

    return {
        usecase: a?.usecase ?? '-',
        description: a?.description ?? '-',
        owner: a?.owner ?? '-',
        coe: a?.coe ?? '-',
        value_label: getLabel(a?.value),
        urgency_label: getLabel(a?.urgency),
        organization: a?.organization ?? '-',
        update_doc: a?.update_doc ?? '-',
        situation: a?.situation ?? '-',
        key_functionalities: a?.key_functionalities ?? '-',
        value_rationale: a?.value_rationale ?? '-',
        value_matrics: a?.value_matrics ?? '-',
        urgency_rationale: a?.urgency_rationale ?? '-',
        urgency_expected: a?.urgency_expected ?? '-',
        expected_q: a?.expected_q,
        year_q: a?.year_q,
        ease_label: getLabel(a?.ease),
        ease_rationale: a?.ease_rationale ?? '-',
        ease_detail: a?.ease_detail ?? '-',
        resource_label: getLabel(a?.resource),
        resource_rationale: a?.resource_rationale ?? '-',
        resource_detail: a?.resource_detail ?? '-',
        predecessor: a?.predecessor ?? '-',
        successor: a?.successor ?? '-',
        otherBU: a?.otherBU ?? '-',
        sign_by: signBy,
        rjppThemes,
    };
});

const getStatusClass = (status) => {
    const s = String(status || '').toLowerCase();
    if (s.includes('draft')) return 'bg-slate-100 text-slate-600 ring-1 ring-slate-300';
    if (s.includes('propose')) return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
    if (s.includes('review')) return 'bg-amber-100 text-amber-700 ring-1 ring-amber-300';
    if (s.includes('baseline')) return 'bg-purple-100 text-purple-700 ring-1 ring-purple-300';
    if (s.includes('approve')) return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
    if (s.includes('postpone')) return 'bg-rose-100 text-rose-700 ring-1 ring-rose-300';
    return 'bg-slate-100 text-slate-500 ring-1 ring-slate-200';
};

const roadmapDuration = computed(() => {
    if (!props.roadmapItems || props.roadmapItems.length === 0) return null;

    const startYears = props.roadmapItems.map(item => Number(item.startYear)).filter(y => y > 0);
    const endYears = props.roadmapItems.map(item => Number(item.endYear)).filter(y => y > 0);

    if (startYears.length === 0 || endYears.length === 0) return null;

    const minStartYear = Math.min(...startYears);
    const maxEndYear = Math.max(...endYears);

    // Get Q for min start year
    const startQs = props.roadmapItems
        .filter(item => Number(item.startYear) === minStartYear)
        .map(item => {
            const m = String(item.startQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 1;
        });
    const minStartQ = Math.min(...startQs);

    // Get Q for max end year
    const endQs = props.roadmapItems
        .filter(item => Number(item.endYear) === maxEndYear)
        .map(item => {
            const m = String(item.endQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 4;
        });
    const maxEndQ = Math.max(...endQs);

    const years = maxEndYear - minStartYear + 1;
    const yearLabel = years > 1 ? `${years} Years` : `${years} Year`;

    if (minStartYear === maxEndYear) {
        return `${yearLabel} - (Q${minStartQ} - Q${maxEndQ} ${minStartYear})`;
    } else {
        return `${yearLabel} - (Q${minStartQ} ${minStartYear} - Q${maxEndQ} ${maxEndYear})`;
    }
});

const roadmapYears = computed(() => {
    const start = props.roadmapStartYear || 2024;
    const end = props.roadmapEndYear || 2029;
    const list = [];
    for (let y = start; y <= end; y++) {
        list.push(y);
    }
    return list;
});

const quarterCells = computed(() =>
    roadmapYears.value.flatMap((year) => [1, 2, 3, 4].map((quarter) => ({ year, quarter })))
);

const roadmapBarStyle = computed(() => {
    if (!props.roadmapItems || props.roadmapItems.length === 0) return { width: '0%', left: '0%' };

    const startYears = props.roadmapItems.map(item => Number(item.startYear)).filter(y => y > 0);
    const endYears = props.roadmapItems.map(item => Number(item.endYear)).filter(y => y > 0);

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;

    if (startYears.length === 0 || endYears.length === 0) return { width: '0%', left: '0%' };

    const minStartYear = Math.max(minGlobalYear, Math.min(...startYears));
    const maxEndYear = Math.min(maxGlobalYear, Math.max(...endYears));

    const startQs = props.roadmapItems
        .filter(item => Number(item.startYear) === minStartYear)
        .map(item => {
            const m = String(item.startQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 1;
        });
    const minStartQ = startQs.length ? Math.min(...startQs) : 1;

    const endQs = props.roadmapItems
        .filter(item => Number(item.endYear) === maxEndYear)
        .map(item => {
            const m = String(item.endQ).match(/Q?([1-4])/);
            return m ? Number(m[1]) : 4;
        });
    const maxEndQ = endQs.length ? Math.max(...endQs) : 4;

    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;

    const startQuarterIndex = (minStartYear - minGlobalYear) * 4 + (minStartQ - 1);
    const endQuarterIndex = (maxEndYear - minGlobalYear) * 4 + (maxEndQ - 1);

    const left = (startQuarterIndex / totalQuarters) * 100;
    const width = ((endQuarterIndex - startQuarterIndex + 1) / totalQuarters) * 100;

    return {
        left: `${left}%`,
        width: `${width}%`
    };
});

const goLiveBarStyle = computed(() => {
    if (!computedAppendixData.value?.expected_q || !computedAppendixData.value?.year_q) return { width: '0%', left: '0%', display: 'none' };

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;
    const year = Number(computedAppendixData.value.year_q);
    
    const qMatch = String(computedAppendixData.value.expected_q).match(/Q?([1-4])/);
    const q = qMatch ? Number(qMatch[1]) : 1;

    if (year < minGlobalYear || year > maxGlobalYear) return { width: '0%', left: '0%', display: 'none' };

    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;

    const quarterIndex = (year - minGlobalYear) * 4 + (q - 1);
    const left = (quarterIndex / totalQuarters) * 100;
    const width = (1 / totalQuarters) * 100; // Single quarter bar

    return {
        left: `${left}%`,
        width: `${width}%`
    };
});

const statusReviewMarkers = computed(() => {
    if (!props.statusImplementations || props.statusImplementations.length === 0) return [];

    const monthMap = {
        jan:1, january:1, feb:2, february:2, mar:3, march:3, apr:4, april:4,
        may:5, mei:5, jun:6, june:6, jul:7, july:7, aug:8, augus:8, august:8, agu:8,
        sep:9, sept:9, september:9, oct:10, octo:10, october:10, okt:10,
        nov:11, november:11, dec:12, december:12, des:12
    };

    const minGlobalYear = props.roadmapStartYear || 2024;
    const maxGlobalYear = props.roadmapEndYear || 2029;
    const totalYears = maxGlobalYear - minGlobalYear + 1;
    const totalQuarters = totalYears * 4;

    const getStatusColor = (status) => {
        const s = String(status || '').toLowerCase();
        if (s.includes('done')) return '#10b981'; // Green
        if (s.includes('review')) return '#f97316'; // Orange
        if (s.includes('progress')) return '#3b82f6'; // Blue
        return '#1e4f8f'; // Default Blue
    };

    return props.statusImplementations.map(impl => {
        const monthStr = String(impl.start || '').trim().toLowerCase();
        const yearNum = Number(impl.year) || NaN;

        if (!monthStr || isNaN(yearNum)) return null;

        let monthNum = Number(monthStr);
        if (!Number.isFinite(monthNum) || monthNum <= 0 || monthNum > 12) {
            const key = monthStr.slice(0,3);
            monthNum = monthMap[key] || monthMap[monthStr] || NaN;
        }

        if (!Number.isFinite(monthNum)) return null;
        if (yearNum < minGlobalYear || yearNum > maxGlobalYear) return null;

        const q = Math.floor((monthNum - 1) / 3) + 1;
        const quarterIndex = (yearNum - minGlobalYear) * 4 + (q - 1);
        const left = (quarterIndex / totalQuarters) * 100;

        return {
            left: `${left}%`,
            label: `${impl.start} ${impl.year}`,
            status: impl.review_status || '-',
            color: getStatusColor(impl.review_status),
            statusUpdated: impl.status_updated || '-'
        };
    }).filter(marker => marker !== null);
});

const statusReviewData = computed(() => {
    if (!props.statusImplementations || props.statusImplementations.length === 0) return '-';
    
    // Sort implementation by year and month if needed, but here we just map them
    const periods = props.statusImplementations
        .map(impl => `${impl.start || ''} ${impl.year || ''}`.trim())
        .filter(str => str !== '');
    
    return periods.length > 0 ? periods.join(', ') : '-';
});
</script>

<style scoped>
.score-panel {
    display: flex;
    background: #fff;
}

.bar-sub-mini {
    background: #2e6ea2;
    color: #fff;
    padding: 3px 12px;
    font-size: 11px;
    line-height: 1.2;
    text-align: center;
    min-height: 32px;
}

.panel-body-mini {
    padding: 3px 12px;
    background: #fff;
    min-height: 32px;
    display: flex;
    align-items: center;
    font-size: 12px;
    color: #0f172a;
}
</style>