<template>
    <div class="animate-fade-in">
        <div v-if="flash.success" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
            {{ flash.success }}
        </div>
        <div v-if="flash.error" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
            {{ flash.error }}
        </div>

        <div class="mb-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ pageTitle }}</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Pindah konteks pilar untuk membuka mapping initiative yang berbeda.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button
                            v-for="option in pilarOptions"
                            :key="`pilar-nav-${option.id}`"
                            type="button"
                            @click="switchPilar(option.id)"
                            :class="String(option.id) === currentPilar
                                ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm dark:border-blue-400 dark:bg-blue-500/10 dark:text-blue-200'
                                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:bg-slate-800/60'"
                            class="inline-flex min-w-[140px] flex-col rounded-xl border px-3 py-2 text-left transition-colors"
                        >
                            <span class="text-[10px] font-semibold uppercase tracking-wider opacity-70">{{ option.label }}</span>
                            <span class="text-sm font-semibold">{{ option.name }}</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Filter Dropdown (hidden in matrix mode) -->
                    <template v-if="!matrixMode">
                        <div class="flex w-full flex-col items-start gap-1.5 sm:w-auto sm:flex-row sm:items-center sm:gap-2">
                            <label class="text-[10px] font-medium text-slate-700 dark:text-slate-300 sm:whitespace-nowrap">Blok:</label>
                            <select v-model="selectedGoalId" @change="applyFilter" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-[10px] text-slate-900 focus:border-transparent focus:ring-1 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white sm:min-w-[150px] sm:w-auto">
                                <option :value="null">Semua Blok</option>
                                <option v-for="goal in allGoals" :key="goal.id" :value="goal.id">{{ goal.code }} - {{ goal.title }}</option>
                            </select>
                        </div>
                        <div class="flex w-full flex-col items-start gap-1.5 sm:w-auto sm:flex-row sm:items-center sm:gap-2">
                            <label class="text-[10px] font-medium text-slate-700 dark:text-slate-300 sm:whitespace-nowrap">Owner:</label>
                            <select v-model="selectedOrgId" @change="applyFilter" class="w-full rounded border border-slate-300 bg-white px-2 py-1 text-[10px] text-slate-900 focus:border-transparent focus:ring-1 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white sm:min-w-[150px] sm:w-auto">
                                <option :value="null">All Owners</option>
                                <option v-for="org in allOrganizations" :key="org.id" :value="org.id">{{ org.name }}</option>
                            </select>
                        </div>
                    </template>

                    <!-- Matrix / Table toggle button -->
                    <button
                        @click="matrixMode = !matrixMode"
                        :class="matrixMode
                            ? 'bg-slate-600 hover:bg-slate-700 text-white'
                            : 'bg-indigo-600 hover:bg-indigo-700 text-white'"
                        class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-semibold transition-colors shadow-sm"
                    >
                        <template v-if="matrixMode">
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Table View
                        </template>
                        <template v-else>
                            <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/></svg>
                            Matrix
                        </template>
                    </button>

                    <!-- Add Tagging Button -->
                    <button @click="showTaggingModal = true" class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm">
                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Tagging
                    </button>
                </div>
            </div>
        </div>

        <div v-if="canManageStructure" class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Kelola Struktur Pilar 2</h3>
                    <p class="mt-1 max-w-2xl text-xs text-slate-500 dark:text-slate-400">
                        Data pada area ini disimpan ke <code>mst_goals</code> untuk blok dan <code>trs_themes</code> untuk theme.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-blue-700"
                        @click="openCreateGoalModal"
                    >
                        Tambah Blok
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="!hasStructureGoals"
                        @click="openCreateThemeModal"
                    >
                        Tambah Theme
                    </button>
                </div>
            </div>

            <div v-if="strategicPillars.length === 0" class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center dark:border-slate-700 dark:bg-slate-900/30">
                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Belum ada blok untuk Pilar 2.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Mulai dengan menambahkan blok, lalu isi theme di bawah blok tersebut.</p>
            </div>

            <div v-else class="mt-4 grid gap-3 xl:grid-cols-2">
                <div
                    v-for="goal in strategicPillars"
                    :key="`structure-goal-${goal.id}`"
                    class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-700 dark:bg-slate-900/30"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-blue-700 dark:bg-blue-500/15 dark:text-blue-200">
                                {{ goal.code }}
                            </div>
                            <h4 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ goal.title }}</h4>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">{{ goal.themes?.length ?? 0 }} theme</p>
                        </div>

                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                class="rounded-md border border-slate-200 px-2 py-1 text-[11px] font-medium text-slate-600 transition-colors hover:border-slate-300 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                @click="openEditGoalModal(goal)"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                class="rounded-md border border-rose-200 px-2 py-1 text-[11px] font-medium text-rose-600 transition-colors hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10"
                                @click="confirmStructureDelete('goal', goal)"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <div
                            v-for="theme in goal.themes ?? []"
                            :key="`structure-theme-${theme.id}`"
                            class="flex items-start justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-[#1a1a1a]"
                        >
                            <div class="min-w-0">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Theme {{ theme.theme_number }}</div>
                                <div class="mt-1 text-sm text-slate-800 dark:text-slate-200">{{ theme.name }}</div>
                            </div>

                            <div class="flex shrink-0 items-center gap-1">
                                <button
                                    type="button"
                                    class="rounded-md border border-slate-200 px-2 py-1 text-[11px] font-medium text-slate-600 transition-colors hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                    @click="openEditThemeModal(theme)"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md border border-rose-200 px-2 py-1 text-[11px] font-medium text-rose-600 transition-colors hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-300 dark:hover:bg-rose-500/10"
                                    @click="confirmStructureDelete('theme', theme)"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs font-medium text-slate-600 transition-colors hover:border-slate-400 hover:bg-white dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800"
                            @click="openCreateThemeModal(goal)"
                        >
                            + Tambah theme di blok ini
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend (hidden in matrix mode) -->
        <div v-if="!matrixMode" class="flex flex-wrap items-center gap-3 mb-3 px-1">
            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Legend:</span>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded-sm bg-slate-300 border border-slate-400 dark:bg-slate-700 dark:border-slate-500"></span>
                <span class="text-[10px] text-slate-600 dark:text-slate-400">Drafting</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded-sm bg-blue-400/30 border border-blue-400/50"></span>
                <span class="text-[10px] text-slate-600 dark:text-slate-400">Propose</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded-sm bg-amber-400/30 border border-amber-400/50"></span>
                <span class="text-[10px] text-slate-600 dark:text-slate-400">Review</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded-sm bg-emerald-400/30 border border-emerald-400/50"></span>
                <span class="text-[10px] text-slate-600 dark:text-slate-400">Approve</span>
            </div>
        </div>

        <!-- ===================== TABLE VIEW ===================== -->
        <template v-if="!matrixMode">
            <div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm dark:border-slate-600 dark:bg-[#1a1a1a]">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800 border-b-2 border-slate-300 dark:border-slate-600">
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 w-12">Code</th>
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 w-1/5">Blok / Goal</th>
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 w-[250px]">Themes</th>
                                <th class="px-2 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Digital Initiatives</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pillar in filteredPillars" :key="pillar.id" class="border-b border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-2 py-2 border-r border-slate-300 dark:border-slate-600 text-center align-top">
                                    <span class="text-[11px] font-bold text-slate-900 dark:text-white">{{ pillar.code }}</span>
                                </td>
                                <td class="px-2 py-2 border-r border-slate-300 dark:border-slate-600 text-center align-top">
                                    <div class="text-[11px] font-medium text-slate-900 dark:text-white">{{ pillar.title }}</div>
                                    <div v-if="getGoalInitiatives(pillar.code).length > 0" class="mt-3 flex flex-col gap-1.5 items-center w-full">
                                        <div class="text-[9px] font-semibold tracking-wider text-slate-400 dark:text-slate-500 uppercase">Mapped directly to Pillar</div>
                                        <div class="flex flex-wrap justify-center gap-1 w-full">
                                            <div v-for="tag in getGoalInitiatives(pillar.code)" :key="tag.id" @click="navigateToScope(tag)" :class="['inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-[9px] font-medium border w-[220px] cursor-pointer hover:brightness-95 transition-all', getStatusColor(tag)]">
                                                <div class="flex items-center gap-1 overflow-hidden flex-1 min-w-0" :title="tag.initiative ? `${tag.initiative.code} - ${tag.initiative.name}` : ''">
                                                    <span class="font-bold flex-shrink-0">{{ tag.initiative ? tag.initiative.code : '?' }}</span>
                                                    <span class="truncate opacity-90">{{ tag.initiative ? tag.initiative.name : '' }}</span>
                                                    <span v-if="tag.initiative?.organization?.name" class="shrink-0 opacity-60 text-[8px]">· {{ tag.initiative.organization.name }}</span>
                                                </div>
                                                <button @click.stop="confirmDelete(tag)" class="flex-shrink-0 opacity-50 hover:opacity-100 hover:text-red-600 transition-opacity" title="Remove">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2" class="p-0 align-top">
                                    <div v-if="pillar.themes && pillar.themes.length > 0" class="w-full h-full">
                                        <table class="w-full h-full border-collapse">
                                            <tbody>
                                                <tr v-for="theme in pillar.themes" :key="theme.id" class="border-b border-slate-300 dark:border-slate-600 last:border-b-0">
                                                    <td class="px-2 py-2 text-[11px] text-slate-700 dark:text-slate-200 border-r border-slate-300 dark:border-slate-600 w-[250px] align-top">
                                                        <div class="flex items-start justify-between gap-1">
                                                            <span><span class="font-semibold mr-1">{{ theme.theme_number }}.</span>{{ theme.name }}</span>
                                                            <span v-if="getThemeInitiatives(theme.id).length > 0" class="flex-shrink-0 inline-flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-semibold min-w-[18px] h-[18px] px-1" :title="`${getThemeInitiatives(theme.id).length} initiatives`">
                                                                {{ getThemeInitiatives(theme.id).length }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-2 py-2 align-top">
                                                        <div v-if="getThemeInitiatives(theme.id).length > 0" class="grid grid-cols-2 gap-1">
                                                            <div v-for="tag in getThemeInitiatives(theme.id)" :key="tag.id" @click="navigateToScope(tag)" :class="['inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-[9px] font-medium border w-full cursor-pointer hover:brightness-95 transition-all', getStatusColor(tag)]">
                                                                <div class="flex items-center gap-1 overflow-hidden flex-1 min-w-0" :title="tag.initiative ? `${tag.initiative.code} - ${tag.initiative.name}` : ''">
                                                                    <span class="font-bold flex-shrink-0">{{ tag.initiative ? tag.initiative.code : '?' }}</span>
                                                                    <span class="truncate opacity-90">{{ tag.initiative ? tag.initiative.name : '' }}</span>
                                                                    <span v-if="tag.initiative?.organization?.name" class="shrink-0 opacity-60 text-[8px]">· {{ tag.initiative.organization.name }}</span>
                                                                </div>
                                                                <button @click.stop="confirmDelete(tag)" class="flex-shrink-0 opacity-50 hover:opacity-100 hover:text-red-600 transition-opacity" title="Remove">
                                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <span v-else class="text-[10px] italic text-slate-400 dark:text-slate-500">—</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else class="py-3 grid h-full" style="grid-template-columns: 250px 1fr">
                                        <div class="border-r border-slate-300 dark:border-slate-600 flex items-center h-full px-2">
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">No themes</span>
                                        </div>
                                        <div class="flex items-center h-full px-2">
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">—</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <!-- TBC row — initiatives with no strategic pillar assigned -->
                        <tbody v-if="tbcInitiatives.length > 0 && !selectedGoalId">
                            <tr class="border-b border-slate-300 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-900/20">
                                <td class="px-2 py-2 border-r border-slate-300 dark:border-slate-600 text-center align-top">
                                    <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">TBC</span>
                                </td>
                                <td class="px-2 py-2 border-r border-slate-300 dark:border-slate-600 text-center align-top">
                                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 italic mb-2">Di luar Strategic Pillar</div>
                                    <div class="flex flex-wrap justify-center gap-1 w-full">
                                        <div v-for="tag in tbcInitiatives" :key="tag.id" @click="navigateToScope(tag)" :class="['inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-[9px] font-medium border w-[220px] cursor-pointer hover:brightness-95 transition-all', getStatusColor(tag)]">
                                            <div class="flex items-center gap-1 overflow-hidden flex-1 min-w-0" :title="tag.initiative ? `${tag.initiative.code} - ${tag.initiative.name}` : ''">
                                                <span class="font-bold flex-shrink-0">{{ tag.initiative ? tag.initiative.code : '?' }}</span>
                                                <span class="truncate opacity-90">{{ tag.initiative ? tag.initiative.name : '' }}</span>
                                                <span v-if="tag.initiative?.organization?.name" class="shrink-0 opacity-60 text-[8px]">· {{ tag.initiative.organization.name }}</span>
                                            </div>
                                            <button @click.stop="confirmDelete(tag)" class="flex-shrink-0 opacity-50 hover:opacity-100 hover:text-red-600 transition-opacity" title="Remove">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2" class="px-2 py-2 align-top">
                                    <span class="text-[10px] italic text-slate-400 dark:text-slate-500">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!filteredPillars || filteredPillars.length === 0" class="text-center py-16 bg-white dark:bg-[#1a1a1a] rounded-xl border border-slate-200 dark:border-white/5 mt-4">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No strategic pillars found</h3>
                <p class="text-slate-500 dark:text-slate-400">{{ selectedGoalId ? 'No data for the selected pillar.' : 'Start by adding your strategic pillars and themes.' }}</p>
            </div>
        </template>

        <!-- ===================== MATRIX VIEW ===================== -->
        <template v-else>
            <div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm dark:border-slate-600 dark:bg-[#1a1a1a]">
                <!-- Matrix info bar -->
                <div class="flex items-center gap-4 px-3 py-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-[10px] text-slate-500 dark:text-slate-400">
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Initiative × Theme Matrix</span>
                    <span>■ = mapped to theme</span>
                    <span class="ml-auto">{{ matrixInitiatives.length }} digital initiatives · {{ allThemes.length }} themes · {{ matrixTaggedCount }} mappings</span>
                </div>
                <!-- Scrollable matrix table -->
                <div class="overflow-auto" style="max-height: calc(100vh - 180px)">
                    <table class="border-collapse text-[9px]" style="min-width: max-content">
                        <thead>
                            <!-- Group header row -->
                            <tr class="bg-slate-200 dark:bg-slate-700 sticky top-0 z-10">
                                <th rowspan="2" class="sticky left-0 z-20 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 px-2 py-1.5 text-left font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap min-w-[220px]">
                                    Initiative
                                </th>
                                <th v-for="group in themeGroupsWithCount" :key="group.label" :colspan="group.count" class="border border-slate-300 dark:border-slate-600 px-2 py-1 text-center text-[8px] font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                    {{ group.label }}
                                </th>
                                <!-- TBC group header -->
                                <th rowspan="2" class="border border-slate-300 dark:border-slate-600 px-2 py-1 text-center text-[8px] font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap w-[52px] bg-slate-100 dark:bg-slate-800">
                                    TBC
                                </th>
                            </tr>
                            <!-- Theme number row -->
                            <tr class="bg-slate-100 dark:bg-slate-800 sticky top-[29px] z-10">
                                <th v-for="theme in sortedMatrixThemes" :key="theme.id" class="border border-slate-300 dark:border-slate-600 px-1 py-1 text-center font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap w-[44px] min-w-[44px]" :title="`${theme.goal?.code ?? '-'} · ${theme.theme_number}. ${theme.name}`">
                                    <div class="text-[8px] font-bold">T{{ theme.theme_number }}</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(initiative, idx) in matrixInitiatives" :key="initiative.id"
                                :class="idx % 2 === 0 ? 'bg-white dark:bg-[#1a1a1a]' : 'bg-slate-50 dark:bg-slate-800/30'"
                                class="hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors">
                                <td class="sticky left-0 z-10 border border-slate-300 dark:border-slate-600 px-2 py-1 font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap"
                                    :class="idx % 2 === 0 ? 'bg-white dark:bg-[#1a1a1a]' : 'bg-slate-50 dark:bg-slate-800/40'">
                                    <span class="text-slate-400 dark:text-slate-500 mr-1.5">{{ idx + 1 }}.</span>
                                    <span class="font-bold mr-1">{{ initiative.code }}</span>
                                    <span class="text-[9px]">{{ initiative.name }}</span>
                                </td>
                                <!-- Theme columns -->
                                <td v-for="theme in sortedMatrixThemes" :key="theme.id" class="border border-slate-300 dark:border-slate-600 text-center py-1 w-[44px]">
                                    <span v-if="isTagged(initiative.id, theme.id)" class="inline-block w-3.5 h-3.5 rounded-sm bg-indigo-500 dark:bg-indigo-400" :title="`${initiative.code} → T${theme.theme_number}`"></span>
                                </td>
                                <!-- TBC column -->
                                <td class="border border-slate-300 dark:border-slate-600 text-center py-1 w-[52px] bg-slate-50/50 dark:bg-slate-900/10">
                                    <span v-if="isTBCTagged(initiative.id)" class="inline-block w-3.5 h-3.5 rounded-sm bg-slate-400 dark:bg-slate-500" :title="`${initiative.code} → TBC`"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- Tagging Modal -->
        <InitiativeTaggingModal
            :show="showTaggingModal"
            :initiatives="allInitiatives"
            :goals="strategicPillars"
            :pilar="currentPilar"
            :pilar-label="currentPilarOption.name"
            @close="showTaggingModal = false"
        />

        <GoalFormModal
            :show="showGoalFormModal"
            :goal="editingGoal"
            :pilar="currentPilar"
            :pilar-label="currentPilarOption.name"
            @close="closeGoalModal"
        />

        <ThemeFormModal
            :show="showThemeFormModal"
            :theme="editingTheme"
            :goals="allGoals"
            :pilar="currentPilar"
            :pilar-label="currentPilarOption.name"
            @close="closeThemeModal"
        />

        <!-- Delete Confirmation -->
        <ConfirmationModal
            :show="showDeleteModal"
            title="Hapus Initiative Tagging"
            message="Apakah Anda yakin ingin menghapus mapping / tagging ini?"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="deleteForm.processing"
            @close="showDeleteModal = false"
            @confirm="executeDelete"
        />

        <ConfirmationModal
            :show="showStructureDeleteModal"
            :title="structureDeleteTitle"
            :message="structureDeleteMessage"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="structureDeleteForm.processing"
            @close="showStructureDeleteModal = false"
            @confirm="executeStructureDelete"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import GoalFormModal from '@/Components/StrategicPillar/GoalFormModal.vue';
import InitiativeTaggingModal from '@/Components/StrategicPillar/InitiativeTaggingModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import ThemeFormModal from '@/Components/StrategicPillar/ThemeFormModal.vue';

const route = useRouteHelper();
const page = usePage();

const props = defineProps({
    strategicPillars: { type: Array, default: () => [] },
    allGoals:         { type: Array, default: () => [] },
    taggings:         { type: Array, default: () => [] },
    allInitiatives:   { type: Array, default: () => [] },
    allThemes:        { type: Array, default: () => [] },
    matrixInitiatives:{ type: Array, default: () => [] },
    allOrganizations: { type: Array, default: () => [] },
    filters:          { type: Object, default: () => ({}) },
    pilarOptions:     { type: Array, default: () => [] },
    baseUrlRoute:     { type: String, default: 'strategic-pillars.index' },
});

const emit = defineEmits(['update-filters']);

// --- Filters (client-side only) ---
const initialGoalId = props.filters.goal_id ? Number(props.filters.goal_id) : null;
const selectedGoalId = ref(
    props.allGoals.some(goal => Number(goal.id) === initialGoalId)
        ? initialGoalId
        : null
);
const selectedOrgId  = ref(props.filters.org_id  ? Number(props.filters.org_id)  : null);
const applyFilter = () => {};
const currentPilar = computed(() => String(props.filters.pilar ?? 1));
const flash = computed(() => page.props.flash ?? {});
const currentPilarOption = computed(() =>
    props.pilarOptions.find(option => String(option.id) === currentPilar.value)
    ?? {
        id: Number(currentPilar.value),
        label: `Pilar ${currentPilar.value}`,
        name: `Pilar ${currentPilar.value}`,
    }
);
const pageTitle = computed(() => currentPilarOption.value.name);
const canManageStructure = computed(() => currentPilar.value === '2');
const hasStructureGoals = computed(() => props.strategicPillars.length > 0);

const filteredPillars = computed(() => {
    if (!selectedGoalId.value) return props.strategicPillars;
    return props.strategicPillars.filter(p => p.id === selectedGoalId.value);
});

const switchPilar = (pilarId) => {
    if (String(pilarId) === currentPilar.value) return;

    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('pilar', pilarId);

    if (selectedOrgId.value) {
        urlParams.set('org_id', selectedOrgId.value);
    } else {
        urlParams.delete('org_id');
    }

    // Preserve other existing params
    const payload = Object.fromEntries(urlParams.entries());

    router.get(route(props.baseUrlRoute), payload, {
        preserveScroll: true,
    });
};

// --- Navigation ---
const navigateToScope = (tag) => {
    const initiative = tag.initiative;
    if (!initiative) return;
    if (initiative.mapped_projects?.length > 0) {
        router.get(route('it-initiatives.show', initiative.mapped_projects[0].id));
        return;
    }
    if (initiative.map_sc?.length > 0) {
        router.get(route('program-planning.program-definition.digital-initiatives.compendium.edit', initiative.map_sc[0].sc_id));
    }
};

// --- Initiative Groupings ---
const sortByCode = (tags) => [...tags].sort((a, b) =>
    Number(a.initiative?.code ?? 99999) - Number(b.initiative?.code ?? 99999)
);

const getGoalInitiatives = (pillarCode) => {
    if (!props.taggings) return [];
    return sortByCode(props.taggings.filter(tag => {
        if (tag.goal !== pillarCode || tag.themes_id) return false;
        if (selectedOrgId.value && tag.initiative?.business_unit != selectedOrgId.value) return false;
        return true;
    }));
};

const getThemeInitiatives = (themeId) => {
    if (!props.taggings) return [];
    return sortByCode(props.taggings.filter(tag => {
        if (tag.themes_id !== themeId) return false;
        if (selectedOrgId.value && tag.initiative?.business_unit != selectedOrgId.value) return false;
        return true;
    }));
};

const tbcInitiatives = computed(() => {
    if (!props.taggings) return [];
    return sortByCode(props.taggings.filter(tag => {
        if (tag.goal !== null) return false;
        if (selectedOrgId.value && tag.initiative?.business_unit != selectedOrgId.value) return false;
        return true;
    }));
});

// --- Status Styling ---
const getStatusColor = (tag) => {
    const initiative = tag?.initiative;
    if (!initiative) return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
    let statusVal = initiative.latest_status?.status || initiative.status;
    const rawStatus = String(statusVal || '').toLowerCase().trim();
    if (rawStatus === '4' || rawStatus.includes('approved'))
        return 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
    if (rawStatus === '5' || rawStatus.includes('baseline'))
        return 'bg-violet-500/10 text-violet-700 border-violet-500/20 dark:bg-violet-500/10 dark:text-violet-400 dark:border-violet-500/20';
    if (rawStatus === '0' || rawStatus === '1' || rawStatus.includes('draft') || rawStatus.includes('not start'))
        return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
    if (rawStatus === '2' || rawStatus.includes('propose'))
        return 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
    if (rawStatus === '3' || rawStatus.includes('review'))
        return 'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
    if (rawStatus.includes('progress') || rawStatus.includes('active') || rawStatus.includes('implement'))
        return 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
    if (rawStatus.includes('cancel') || rawStatus.includes('reject') || rawStatus.includes('drop') || rawStatus.includes('hold'))
        return 'bg-rose-500/10 text-rose-700 border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
    return 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
};

// --- Modals ---
const showTaggingModal = ref(false);
const showDeleteModal  = ref(false);
const matrixMode       = ref(false);
const pendingDeleteTag = ref(null);
const deleteForm       = useForm({});
const showGoalFormModal = ref(false);
const showThemeFormModal = ref(false);
const editingGoal = ref(null);
const editingTheme = ref(null);
const showStructureDeleteModal = ref(false);
const pendingStructureDelete = ref(null);
const structureDeleteForm = useForm({
    pilar: String(currentPilar.value),
});

// --- Matrix helpers ---
const sortedMatrixThemes = computed(() =>
    [...props.allThemes].sort((left, right) => {
        const goalCompare = String(left.goal?.code ?? '').localeCompare(String(right.goal?.code ?? ''));
        if (goalCompare !== 0) return goalCompare;
        return Number(left.theme_number ?? 0) - Number(right.theme_number ?? 0);
    })
);

const themeGroupsWithCount = computed(() =>
    props.strategicPillars
        .map(goal => ({
            label: `${goal.code} - ${goal.title}`,
            count: (goal.themes ?? []).length,
        }))
        .filter(group => group.count > 0)
);

// initiative_id-theme_id pairs that are tagged
const taggedSet = computed(() => {
    const set = new Set();
    props.taggings.forEach(tag => {
        if (tag.initiative?.id && tag.themes_id) set.add(`${tag.initiative.id}-${tag.themes_id}`);
    });
    return set;
});

// initiative ids that are TBC-tagged (goal=null, themes_id=null)
const tbcTaggedSet = computed(() => {
    const set = new Set();
    props.taggings.forEach(tag => {
        if (tag.goal === null && !tag.themes_id && tag.initiative?.id) set.add(tag.initiative.id);
    });
    return set;
});

const isTagged    = (initiativeId, themeId) => taggedSet.value.has(`${initiativeId}-${themeId}`);
const isTBCTagged = (initiativeId)          => tbcTaggedSet.value.has(initiativeId);

const matrixTaggedCount = computed(() => taggedSet.value.size + tbcTaggedSet.value.size);

// ESC exits matrix mode
const handleKeydown = (e) => { if (e.key === 'Escape' && matrixMode.value) matrixMode.value = false; };
onMounted(() => window.addEventListener('keydown', handleKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', handleKeydown));

const confirmDelete = (tag) => { pendingDeleteTag.value = tag; showDeleteModal.value = true; };
const executeDelete = () => {
    if (!pendingDeleteTag.value) return;
    deleteForm.delete(route('strategic-pillars.tagging.destroy', pendingDeleteTag.value.id), {
        preserveScroll: true,
        onSuccess: () => { showDeleteModal.value = false; pendingDeleteTag.value = null; },
    });
};

const closeGoalModal = () => {
    showGoalFormModal.value = false;
    editingGoal.value = null;
};

const closeThemeModal = () => {
    showThemeFormModal.value = false;
    editingTheme.value = null;
};

const openCreateGoalModal = () => {
    editingGoal.value = null;
    showGoalFormModal.value = true;
};

const openEditGoalModal = (goal) => {
    editingGoal.value = goal;
    showGoalFormModal.value = true;
};

const openCreateThemeModal = (goal = null) => {
    editingTheme.value = goal
        ? { idGoal: goal.id }
        : null;
    showThemeFormModal.value = true;
};

const openEditThemeModal = (theme) => {
    editingTheme.value = theme;
    showThemeFormModal.value = true;
};

const confirmStructureDelete = (type, item) => {
    pendingStructureDelete.value = { type, item };
    showStructureDeleteModal.value = true;
};

const structureDeleteTitle = computed(() => {
    if (pendingStructureDelete.value?.type === 'goal') {
        return 'Hapus Blok Pilar';
    }

    return 'Hapus Theme';
});

const structureDeleteMessage = computed(() => {
    if (pendingStructureDelete.value?.type === 'goal') {
        return 'Blok akan dihapus dari mst_goals. Pastikan theme dan tagging yang terkait sudah dibersihkan.';
    }

    return 'Theme akan dihapus dari trs_themes. Pastikan theme ini belum dipakai oleh tagging.';
});

const extractFirstError = (errors = {}) => {
    const firstValue = Object.values(errors)[0];

    if (Array.isArray(firstValue)) {
        return firstValue[0] ?? 'Terjadi kesalahan.';
    }

    return firstValue ?? 'Terjadi kesalahan.';
};

const executeStructureDelete = () => {
    if (!pendingStructureDelete.value) return;

    structureDeleteForm.pilar = String(currentPilar.value);

    const { type, item } = pendingStructureDelete.value;
    const routeName = type === 'goal'
        ? route('strategic-pillars.goals.destroy', item.id)
        : route('strategic-pillars.themes.destroy', item.id);

    structureDeleteForm.delete(routeName, {
        preserveScroll: true,
        onSuccess: () => {
            showStructureDeleteModal.value = false;
            pendingStructureDelete.value = null;
        },
        onError: (errors) => {
            window.alert(extractFirstError(errors));
        },
    });
};
</script>
