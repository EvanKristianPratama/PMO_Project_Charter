<template>
    <section
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <div class="flex items-center justify-end gap-2 border-b border-slate-200 px-5 py-2 dark:border-white/10">
            <button
                v-if="displayMode === 'all'"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200"
                @click="showAllTable = !showAllTable"
            >
                <svg v-if="showAllTable" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.543 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ showAllTable ? 'Sembunyikan Tabel' : 'Tampilkan Tabel' }}
            </button>
            <button
                v-if="displayMode === 'all'"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200"
                @click="savePositions"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Simpan Posisi
            </button>
            <button
                v-if="displayMode === 'all'"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200"
                @click="toggleLockPositions"
            >
                <svg v-if="isPositionsLocked" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                {{ isPositionsLocked ? 'Buka Kunci Posisi' : 'Kunci Posisi' }}
            </button>
            <button
                type="button"
                :disabled="isExporting"
                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-50 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200"
                @click="downloadScreenshot"
            >
                <svg v-if="isExporting" class="h-3.5 w-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ isExporting ? 'Exporting...' : 'Export PNG' }}
            </button>
            <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11px] font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-200"
                @click="showFilters = !showFilters"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                {{ showFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter' }}
            </button>
        </div>

        <div v-show="showFilters">
            <div
                class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <div
                    class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <label for="initiative-type-filter" class="text-[11px]">Filter Type</label>
                        <select id="initiative-type-filter" v-model="selectedType"
                            class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="all">All</option>
                            <option value="digital">Digital</option>
                            <option value="it">IT</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="model-relasi-filter" class="text-[11px]">Model Relasi</label>
                        <select id="model-relasi-filter" v-model="selectedModelRelasi"
                            class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="all">All</option>
                            <option v-for="option in modelRelasiOptions" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="initiative-filter" class="text-[11px]">Initiative</label>
                        <select id="initiative-filter" v-model="selectedInitiative"
                            :disabled="displayMode === 'all'"
                            class="w-48 max-w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="all">All</option>
                            <option v-for="option in filteredInitiativeOptions" :key="option.id" :value="option.id">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="relation-view-mode" class="text-[11px]">Mode</label>
                        <select id="relation-view-mode" v-model="displayMode"
                            class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-200">
                            <option value="per-code">Per Code</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-5 py-3 text-[11px] font-semibold text-slate-600 dark:border-white/10 dark:text-slate-300">
                <span class="uppercase tracking-wider text-slate-500 dark:text-slate-400">Legend Status</span>
                <span
                    v-for="legend in statusLegend"
                    :key="legend.key"
                    class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-2.5 py-1 shadow-sm dark:border-white/10 dark:bg-[#1f1f1f]"
                >
                    <span
                        class="h-2 w-2 shrink-0 rotate-45 rounded-sm border"
                        :style="legend.swatchStyle"
                    ></span>
                    <span>{{ legend.label }}</span>
                    <span class="text-slate-400 dark:text-slate-500">({{ legend.count }})</span>
                </span>

                <div class="ml-auto flex items-center gap-2 border-l border-slate-200 pl-4 dark:border-white/10">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" v-model="showEdgeLabels" class="peer sr-only">
                        <div class="peer h-4 w-7 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-3 after:w-3 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none dark:bg-slate-700"></div>
                        <span class="ml-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Show Labels</span>
                    </label>
                </div>
            </div>
        </div>



        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <colgroup>
                    <col style="width: 16%;">
                    <col style="width: 5%;">
                    <col style="width: 16%;">
                    <col style="width: 30%;">
                    <col style="width: 5%;">
                    <col style="width: 5%;">
                </colgroup>
                <tbody>
                    <template v-if="displayMode === 'per-code'">
                        <tr v-if="!filteredInitiatives.length">
                            <td colspan="6"
                                class="border border-slate-200 px-4 py-6 text-center text-sm text-slate-500 dark:border-white/10 dark:text-slate-400">
                                Belum ada data initiative.
                            </td>
                        </tr>
                        <template v-for="initiative in filteredInitiatives" :key="initiative.id">
                            <tr class="bg-slate-100 dark:bg-slate-900/30">
                                <td colspan="6"
                                    class="border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:text-slate-200">
                                    <span class="text-slate-600 dark:text-slate-400">Code:</span>
                                    {{ initiative.code ?? initiative.id ?? '-' }}
                                    <span class="mx-2 text-slate-400">|</span>
                                    <span class="text-slate-600 dark:text-slate-400">Initiative:</span>
                                    {{ initiative.name ?? '-' }}
                                </td>
                            </tr>
                            <tr class="bg-slate-50 dark:bg-white/5">
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Initiative A
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Tipe Relasi
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Initiative B
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Justifikasi
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Sumber
                                </th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                    Action
                                </th>
                            </tr>
                            <tr v-for="(relation, index) in relationRowsByInitiative(initiative)" :key="`${initiative.id}-${index}`"
                                class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                                <td
                                    class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    <span v-if="relation"
                                        class="inline-block wrap-break-word rounded px-2 py-0.5"
                                        :style="relationInitiativeChipStyle(relation.predecessor_id)">
                                        {{ relation.predecessor }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>

                                <td
                                    class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    <span
                                        v-if="relation"
                                        class="inline-block rounded px-2 py-0.5"
                                        :style="relationTypeChipStyle(relation)"
                                    >
                                        {{ getRelationPositionLabel(relation.type_relation) }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    <span
                                        v-if="relation"
                                        class="inline-block wrap-break-word rounded px-2 py-0.5"
                                        :style="relationInitiativeChipStyle(relation.successor_id)"
                                    >
                                        {{ relation.successor }}
                                    </span>
                                    <span v-else class="text-slate-400">-</span>
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-ellipsis overflow-hidden">
                                    {{ relation?.justifikasi ?? '-' }}
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                    {{ relation?.model_relasi ?? '-' }}
                                </td>
                                <td
                                    class="border border-slate-200 px-4 py-3 text-center align-middle dark:border-white/10 overflow-hidden">
                                    <button
                                        v-if="relation"
                                        type="button"
                                        class="relative z-10 cursor-pointer rounded-full border border-blue-300 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                        @click="handleEditRelation(initiative, relation)"
                                    >
                                        Edit
                                    </button>
                                    <span v-else class="text-slate-300 dark:text-slate-600">-</span>
                                </td>
                            </tr>
                            <tr v-if="!relationRowsByInitiative(initiative).length"
                                class="transition hover:bg-slate-50 dark:hover:bg-white/5">
                                <td colspan="6"
                                    class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-400 dark:border-white/10 dark:text-slate-500">
                                    Tidak ada relasi
                                </td>
                            </tr>

                            <tr>
                                <td colspan="6"
                                    class="border border-slate-200 bg-slate-50/70 px-4 py-4 dark:border-white/10 dark:bg-[#141414]">
                                    <div class="rounded-xl border border-slate-200 bg-white dark:border-white/10 dark:bg-[#101010]">
                                        <div
                                            class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
                                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                                Diagram Relasi Predecessor -> Successor
                                            </h3>
                                            <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <span class="h-0.5 w-6 rounded bg-emerald-600 dark:bg-emerald-400"></span>
                                                    Garis Predecessor
                                                </span>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <span class="h-0.5 w-6 rounded bg-blue-600 dark:bg-blue-400"></span>
                                                    Garis Successor
                                                </span>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <div v-if="!relationRowsByInitiative(initiative).length"
                                                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                                                Belum ada relasi untuk divisualisasikan.
                                            </div>
                                            <div
                                                v-else
                                                class="rounded-lg border border-slate-200 dark:border-white/10"
                                                :style="{ height: `${relationGraphByInitiative(initiative).height}px` }"
                                            >
                                                <VueFlow
                                                    ref="initiativeFlowRefs"
                                                    class="initiative-relation-flow"
                                                    :nodes="relationGraphByInitiative(initiative).nodes"
                                                    :edges="relationGraphByInitiative(initiative).edges"
                                                    :fit-view-on-init="true"
                                                    :nodes-draggable="true"
                                                    :nodes-connectable="false"
                                                    :elements-selectable="true"
                                                    :zoom-on-double-click="false"
                                                    :min-zoom="0.35"
                                                    :max-zoom="1.5"
                                                >
                                                        <template #node-initiative-status-card="nodeProps">
                                                            <InitiativeRelationFlowNode :data="nodeProps.data" :selected="nodeProps.selected" />
                                                    </template>
                                                </VueFlow>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <template v-else>
                        <tr>
                            <td colspan="6"
                                class="border border-slate-200 bg-slate-50/70 px-4 py-4 dark:border-white/10 dark:bg-[#141414]">
                                <div class="rounded-xl border border-slate-200 bg-white dark:border-white/10 dark:bg-[#101010]">
                                    <div
                                        class="flex flex-wrap items-center justify-end gap-3 border-b border-slate-200 px-4 py-3 dark:border-white/10">
                                        <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="h-0.5 w-6 rounded bg-emerald-600 dark:bg-emerald-400"></span>
                                                Garis Predecessor
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="h-0.5 w-6 rounded bg-blue-600 dark:bg-blue-400"></span>
                                                Garis Successor
                                            </span>
                                            <span v-if="allRelationGraph.usesStoredPositions" class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                                                Posisi dari `x/y` tersimpan: {{ allRelationGraph.storedPositionCount }}
                                            </span>
                                            <span v-if="allRelationGraph.missingPositionCount" class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-300">
                                                Fallback otomatis: {{ allRelationGraph.missingPositionCount }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div v-if="!allRelationRows.length"
                                            class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                                            Tidak ada relasi untuk divisualisasikan pada filter saat ini.
                                        </div>
                                        <div v-else class="overflow-x-auto rounded-lg border border-slate-200 dark:border-white/10">
                                            <div
                                                :style="{ width: `${allRelationGraph.width}px`, height: `${allRelationGraph.height}px` }"
                                            >
                                                <VueFlow
                                                    :id="ALL_FLOW_ID"
                                                    ref="allFlowRef"
                                                    class="initiative-relation-flow"
                                                    :nodes="allRelationGraph.nodes"
                                                    :edges="allRelationGraph.edges"
                                                    :fit-view-on-init="true"
                                                    :nodes-draggable="!isPositionsLocked"
                                                    :nodes-connectable="false"
                                                    :elements-selectable="true"
                                                    :zoom-on-double-click="false"
                                                    :min-zoom="0.25"
                                                    :max-zoom="1.5"
                                                    @node-click="handleAllNodeClick"
                                                    @pane-click="handleAllPaneClick"
                                                >
                                                    <template #node-initiative-status-card="nodeProps">
                                                        <InitiativeRelationFlowNode :data="nodeProps.data" :selected="nodeProps.selected" />
                                                    </template>
                                                </VueFlow>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <template v-if="showAllTable">
                            <tr class="bg-slate-50 dark:bg-white/5">
                                <th
                                    class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Initiative A
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Tipe Relasi
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Initiative B
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Justifikasi
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Sumber
                            </th>
                            <th
                                class="border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-white/10 dark:bg-[#1f1f1f] dark:text-slate-400">
                                Action
                            </th>
                        </tr>
                        <tr v-if="!allRelationRows.length">
                            <td colspan="6"
                                class="border border-slate-200 px-4 py-6 text-center text-sm text-slate-500 dark:border-white/10 dark:text-slate-400">
                                Tidak ada relasi untuk filter saat ini.
                            </td>
                        </tr>
                        <tr
                            v-for="(relation, index) in allRelationRows"
                            :key="`all-${relationRowKey(relation)}-${index}`"
                            class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                        >
                            <td
                                class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                <span
                                    class="inline-block wrap-break-word rounded px-2 py-0.5"
                                    :style="relationInitiativeChipStyle(relation.predecessor_id)"
                                >
                                    {{ relation.predecessor }}
                                </span>
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                <span
                                    class="inline-block rounded px-2 py-0.5"
                                    :style="relationTypeChipStyle(relation)"
                                >
                                    {{ getRelationPositionLabel(relation.type_relation) }}
                                </span>
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                <span
                                    class="inline-block wrap-break-word rounded px-2 py-0.5"
                                    :style="relationInitiativeChipStyle(relation.successor_id)"
                                >
                                    {{ relation.successor }}
                                </span>
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-ellipsis overflow-hidden">
                                {{ relation?.justifikasi ?? '-' }}
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-center text-sm text-slate-800 dark:border-white/10 dark:text-slate-200 overflow-hidden">
                                {{ relation?.model_relasi ?? '-' }}
                            </td>
                            <td
                                class="border border-slate-200 px-4 py-3 text-center align-middle dark:border-white/10 overflow-hidden">
                                <button
                                    type="button"
                                    class="relative z-10 cursor-pointer rounded-full border border-blue-300 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                    @click="handleEditRelation(initiativeFromRelation(relation), relation)"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { MarkerType, Position, VueFlow, useVueFlow } from '@vue-flow/core';
import { router } from '@inertiajs/vue3';
import { toPng } from 'html-to-image';
import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';
import InitiativeRelationFlowNode from '@/Components/InitiativeRelation/InitiativeRelationFlowNode.vue';

const ALL_FLOW_ID = 'initiative-relation-all-flow';
const { updateNode: updateAllFlowNode, getNodes: getAllFlowNodes } = useVueFlow(ALL_FLOW_ID);
const allSelectedNodeId = ref(null);
const allFlowRef = ref(null);
const initiativeFlowRefs = ref([]);

const handleAllNodeClick = ({ node }) => {
    const prevId = allSelectedNodeId.value;
    if (prevId && prevId !== node.id) {
        updateAllFlowNode(prevId, { draggable: false });
    }
    if (prevId === node.id) {
        updateAllFlowNode(node.id, { draggable: false });
        allSelectedNodeId.value = null;
    } else {
        updateAllFlowNode(node.id, { draggable: true });
        allSelectedNodeId.value = node.id;
    }
};

const handleAllPaneClick = () => {
    if (allSelectedNodeId.value) {
        updateAllFlowNode(allSelectedNodeId.value, { draggable: false });
        allSelectedNodeId.value = null;
    }
};

const props = defineProps({
    mstInitiatives: {
        type: Array,
        default: () => [],
    },
    modelRelationOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['edit-relation']);

const showFilters = ref(false);
const showAllTable = ref(false);
const selectedType = ref('all');
const selectedModelRelasi = ref('v3');
const selectedInitiative = ref('all');
const displayMode = ref('all');
const showEdgeLabels = ref(true);
const isExporting = ref(false);

const isPositionsLocked = ref(
    props.mstInitiatives.some((initiative) => initiative.relation_position?.is_locked)
);

const savePositions = () => {
    const nodes = getAllFlowNodes.value.filter(n => n.type === 'initiative-status-card' || n.id.startsWith('initiative-'));
    const positionsToSync = nodes.map((node) => {
        const initiativeId = Number(String(node.id).replace('initiative-', ''));
        return {
            initiative_id: initiativeId,
            x: node.position.x,
            y: node.position.y,
            is_locked: isPositionsLocked.value,
        };
    }).filter(pos => !isNaN(pos.initiative_id) && pos.initiative_id > 0);

    router.post(route('initiative-relations.sync-positions'), {
        positions: positionsToSync,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
};

const toggleLockPositions = () => {
    const nextLockState = !isPositionsLocked.value;
    
    // Get nodes before updating the state
    const nodes = getAllFlowNodes.value.filter(n => n.type === 'initiative-status-card' || n.id.startsWith('initiative-'));
    const positionsToSync = nodes.map((node) => {
        const initiativeId = Number(String(node.id).replace('initiative-', ''));
        return {
            initiative_id: initiativeId,
            x: node.position.x,
            y: node.position.y,
            is_locked: nextLockState,
        };
    }).filter(pos => !isNaN(pos.initiative_id) && pos.initiative_id > 0);

    // Flip state
    isPositionsLocked.value = nextLockState;

    router.post(route('initiative-relations.sync-positions'), {
        positions: positionsToSync,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
};

watch(displayMode, (mode) => {
    if (mode === 'all') {
        selectedType.value = 'all';
        selectedInitiative.value = 'all';
    }
});

const downloadScreenshot = () => {
    if (isExporting.value) return;

    let element = null;
    if (displayMode.value === 'all') {
        // Target the element via ref or ID
        element = allFlowRef.value?.$el || document.getElementById(ALL_FLOW_ID);
    } else {
        // In per-code mode, it's trickier, we take the first visible one
        element = document.querySelector('.initiative-relation-flow');
    }
    
    if (!element) {
        console.error('Export Error: Diagram element not found');
        alert('Gagal mengekspor: Elemen diagram tidak ditemukan.');
        return;
    }

    isExporting.value = true;
    console.log('Starting export for element:', element);

    // Small timeout to ensure UI is ready
    setTimeout(() => {
        toPng(element, {
            backgroundColor: '#ffffff',
            pixelRatio: 2,
            style: {
                borderRadius: '0',
            },
            // Include some margin if needed
            width: element.offsetWidth,
            height: element.offsetHeight,
        }).then((dataUrl) => {
            if (!dataUrl || dataUrl.length < 100) {
                throw new Error('Generated image is empty');
            }
            const link = document.createElement('a');
            link.download = `initiative-relation-${new Date().getTime()}.png`;
            link.href = dataUrl;
            link.click();
            console.log('Export successful');
        }).catch((err) => {
            console.error('Export failed:', err);
            alert('Gagal mengekspor gambar. Silakan cek konsol browser untuk detailnya.');
        }).finally(() => {
            isExporting.value = false;
        });
    }, 100);
};

const handleEditRelation = (initiative, relation) => {
    emit('edit-relation', {
        initiative,
        relation,
    });
};

const getRelationPositionLabel = (typeRelation) => {
    const key = typeRelation != null ? Number(typeRelation) : null;
    const positionConfig = {
        1: 'Predecessor',
        2: 'Successor',
    };
    return positionConfig[key] ?? 'Unknown';
};

const modelRelasiOptions = computed(() => {
    if (!props.modelRelationOptions?.length) {
        return [];
    }

    return props.modelRelationOptions.filter((option) => option);
});

const initiativeOptions = computed(() => {
    return props.mstInitiatives.map((initiative) => {
        const code = initiative.code ?? initiative.id ?? '-';
        const name = initiative.name ?? '-';

        return {
            id: initiative.id,
            code,
            name,
            label: `${code} - ${name}`,
            type: initiative.tipe_initiative != null ? Number(initiative.tipe_initiative) : null,
        };
    });
});

const filteredInitiativeOptions = computed(() => {
    if (selectedType.value === 'digital') {
        return initiativeOptions.value.filter((initiative) => initiative.type === 1);
    }
    if (selectedType.value === 'it') {
        return initiativeOptions.value.filter((initiative) => initiative.type === 2);
    }
    return initiativeOptions.value;
});

const filteredInitiatives = computed(() => {
    let initiatives = props.mstInitiatives;

    if (selectedType.value !== 'all') {
        const expectedType = selectedType.value === 'digital' ? 1 : 2;
        initiatives = initiatives.filter((initiative) => Number(initiative.tipe_initiative) === expectedType);
    }

    if (selectedInitiative.value !== 'all') {
        initiatives = initiatives.filter((initiative) => (
            Number(initiative.id) === Number(selectedInitiative.value)
        ));
    }

    // Filter by model relasi if selected
    if (selectedModelRelasi.value !== 'all') {
        initiatives = initiatives.filter((initiative) => extractRelations(initiative).length > 0);
    }

    return initiatives;
});

const IT_STATUS_PALETTE = {
    'On Track': {
        className: 'status-on-track',
        borderColor: '#0ea5e9',
        backgroundColor: '#f0f9ff',
        textColor: '#0369a1',
        swatchColor: '#38bdf8',
    },
    'Done': {
        className: 'status-done',
        borderColor: '#16a34a',
        backgroundColor: '#f0fdf4',
        textColor: '#166534',
        swatchColor: '#22c55e',
    },
    'At Risk': {
        className: 'status-at-risk',
        borderColor: '#eab308',
        backgroundColor: '#fefce8',
        textColor: '#854d0e',
        swatchColor: '#facc15',
    },
    'Delayed': {
        className: 'status-delayed',
        borderColor: '#dc2626',
        backgroundColor: '#fef2f2',
        textColor: '#991b1b',
        swatchColor: '#ef4444',
    },
    'Not Signed': {
        className: 'status-not-signed',
        borderColor: '#dc2626',
        backgroundColor: '#fef2f2',
        textColor: '#991b1b',
        swatchColor: '#ef4444',
    },
    'Not Started': {
        className: 'status-not-started',
        borderColor: '#2563eb',
        backgroundColor: '#eff6ff',
        textColor: '#1e40af',
        swatchColor: '#3b82f6',
    },
};

const normalizeItStatus = (rawStatus) => {
    const normalized = String(rawStatus ?? '').trim().toLowerCase();

    if (!normalized || normalized === '-' || normalized === 'null') {
        return 'Not Started';
    }

    if (normalized.includes('on track')) return 'On Track';
    if (normalized.includes('at risk')) return 'At Risk';
    if (normalized.includes('not signed')) return 'Not Signed';
    if (normalized.includes('not started')) return 'Not Started';
    if (normalized.includes('done') || normalized.includes('completed')) return 'Done';
    if (normalized.includes('delayed')) return 'Delayed';

    if (normalized === '4' || normalized === 'approved') {
        return 'On Track';
    }

    if (normalized === '3' || normalized === 'review' || normalized === 'propose') {
        return 'At Risk';
    }

    if (normalized === '2' || normalized === 'draft' || normalized === 'drafting' || normalized === '0' || normalized === '1') {
        return 'Not Started';
    }

    return 'Not Started';
};

const resolveInitiativeStatusLabel = (initiative) => normalizeItStatus(
    initiative?.latestStatusImplementation?.review_status
    ?? initiative?.latest_status_implementation?.review_status
    ?? initiative?.latestStatus?.review_status
    ?? initiative?.latestStatus?.status
    ?? initiative?.status
    ?? ''
);

const getStatusColorClass = (statusLabel) => {
    const normalized = normalizeItStatus(statusLabel);
    return IT_STATUS_PALETTE[normalized]?.className ?? IT_STATUS_PALETTE['Not Started'].className;
};

const statusLegend = computed(() => {
    const order = ['On Track', 'At Risk', 'Done', 'Delayed', 'Not Started', 'Not Signed'];
    const counts = order.reduce((accumulator, label) => {
        accumulator[label] = 0;
        return accumulator;
    }, {});

    filteredInitiatives.value.forEach((initiative) => {
        const label = resolveInitiativeStatusLabel(initiative);
        if (counts[label] !== undefined) {
            counts[label] += 1;
            return;
        }

        counts['Not Started'] += 1;
    });

    return order.map((label) => ({
        key: label,
        label,
        count: counts[label] ?? 0,
        swatchStyle: {
            backgroundColor: IT_STATUS_PALETTE[label].swatchColor,
            borderColor: IT_STATUS_PALETTE[label].borderColor,
        },
    }));
});

const buildInitiativeNodeData = (initiative, fallbackCode, isCurrent = false) => {
    const code = initiative?.code ?? initiative?.id ?? fallbackCode ?? '-';
    const name = initiative?.name ?? '-';
    const statusLabel = resolveInitiativeStatusLabel(initiative);

    return {
        code,
        name,
        statusLabel,
        statusClass: getStatusColorClass(statusLabel),
        isCurrent,
        label: formatInitiative(initiative, fallbackCode),
    };
};

const resolveInitiativeNodeStyle = () => ({
    backgroundColor: 'transparent',
    border: 'none',
    boxShadow: 'none',
    padding: '0',
    width: 'auto',
    height: 'auto',
});

const initiativeById = computed(() => {
    const map = new Map();
    props.mstInitiatives.forEach((initiative) => {
        map.set(Number(initiative.id), initiative);
    });
    return map;
});

const findInitiativeById = (initiativeId) => initiativeById.value.get(Number(initiativeId)) ?? null;

const formatInitiative = (initiative, fallbackCode) => {
    const code = initiative?.code ?? initiative?.id ?? fallbackCode ?? '-';
    const name = initiative?.name;
    if (!name) {
        return code;
    }
    return `[${code}] ${name}`;
};

const toNumericId = (value) => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : null;
};

const toCoordinate = (value) => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : null;
};

const getRelationDirection = (relation) => {
    const typeRelation = relation?.type_relation != null ? Number(relation.type_relation) : null;
    const rowId = toNumericId(relation?.initiative_code_row);
    const columnId = toNumericId(relation?.initiative_code_column);

    if (typeRelation === 2) {
        return {
            predecessorId: columnId,
            successorId: rowId,
        };
    }

    return {
        predecessorId: rowId,
        successorId: columnId,
    };
};

const initiativeNodeId = (initiativeId) => `initiative-${initiativeId}`;
const FLOW_NODE_WIDTH = 240;
const FLOW_NODE_HEIGHT = 74;
const FLOW_GRAPH_PADDING = 56;
const ALL_GRAPH_FALLBACK_COLUMNS = 4;
const ALL_GRAPH_FALLBACK_X_GAP = 320;
const ALL_GRAPH_FALLBACK_Y_GAP = 112;

const STATUS_BLOCK_STYLE_MAP = {
    drafting: {
        borderColor: '#D97706',
        backgroundColor: '#F59E0B',
        color: '#ffffff',
    },
    propose: {
        borderColor: '#0EA5E9',
        backgroundColor: '#0284C7',
        color: '#ffffff',
    },
    review: {
        borderColor: '#DC2626',
        backgroundColor: '#F87171',
        color: '#ffffff',
    },
    approved: {
        borderColor: '#22C55E',
        backgroundColor: '#BBF7D0',
        color: '#0f5132',
    },
    postpone: {
        borderColor: '#EAB308',
        backgroundColor: '#FEF08A',
        color: '#7a6000',
    },
};

const STATUS_ALIAS_MAP = {
    draft: 'drafting',
    approve: 'approved',
    aproved: 'approved',
};

const VALID_STATUS_KEYS = new Set(Object.keys(STATUS_BLOCK_STYLE_MAP));

const normalizeStatus = (value) => String(value ?? '').trim().toLowerCase();

const resolveInitiativeStatusKey = (initiative) => {
    const raw = normalizeStatus(
        initiative?.latest_status?.status
        ?? initiative?.latestStatus?.status
        ?? initiative?.status
        ?? 'drafting',
    );
    const canonical = STATUS_ALIAS_MAP[raw] ?? raw;

    return VALID_STATUS_KEYS.has(canonical) ? canonical : 'drafting';
};

const resolveInitiativeBlockStyle = (initiative, isCurrent = false) => {
    const statusKey = resolveInitiativeStatusKey(initiative);
    const palette = STATUS_BLOCK_STYLE_MAP[statusKey] ?? STATUS_BLOCK_STYLE_MAP.propose;

    return {
        borderColor: palette.borderColor,
        backgroundColor: palette.backgroundColor,
        color: palette.color,
        borderWidth: '1px',
        borderStyle: 'solid',
        borderRadius: '0.5rem',
        fontSize: '10px',
        fontWeight: '600',
        lineHeight: '1.25',
        padding: '6px 8px',
        boxShadow: isCurrent
            ? '0 0 0 2px rgba(15, 23, 42, 0.18), 0 4px 12px rgba(15, 23, 42, 0.25)'
            : '0 2px 6px rgba(15, 23, 42, 0.16)',
    };
};

const relationInitiativeChipStyle = (initiativeId) => {
    const initiative = findInitiativeById(initiativeId);
    const statusLabel = resolveInitiativeStatusLabel(initiative);
    const palette = IT_STATUS_PALETTE[statusLabel] ?? IT_STATUS_PALETTE['Not Started'];

    return {
        borderColor: palette.borderColor,
        backgroundColor: palette.backgroundColor,
        color: palette.textColor,
        borderWidth: '1px',
        borderStyle: 'solid',
        fontSize: '0.95em',
        fontWeight: 600,
    };
};

const relationTypeChipStyle = (relation) => {
    const relationType = relation?.type_relation != null ? Number(relation.type_relation) : null;
    if (relationType === 1) {
        return {
            borderColor: '#059669',
            backgroundColor: '#d1fae5',
            color: '#065f46',
            borderWidth: '1px',
            borderStyle: 'solid',
            fontSize: '0.95em',
            fontWeight: 600,
        };
    }
    if (relationType === 2) {
        return {
            borderColor: '#2563eb',
            backgroundColor: '#dbeafe',
            color: '#1e40af',
            borderWidth: '1px',
            borderStyle: 'solid',
            fontSize: '0.95em',
            fontWeight: 600,
        };
    }

    return {
        borderColor: '#64748b',
        backgroundColor: '#f1f5f9',
        color: '#334155',
        borderWidth: '1px',
        borderStyle: 'solid',
        fontSize: '0.95em',
        fontWeight: 600,
    };
};

const relationLineColor = (typeRelation) => {
    const relationType = typeRelation != null ? Number(typeRelation) : null;
    if (relationType === 1) return '#059669';
    if (relationType === 2) return '#2563eb';
    return '#64748b';
};

const extractRelations = (initiative) => {
    const initiativeRelationsRow = initiative?.initiativeRelationsRow
        ?? initiative?.initiative_relations_row
        ?? [];
    const initiativeRelationsColumn = initiative?.initiativeRelationsColumn
        ?? initiative?.initiative_relations_column
        ?? [];

    const justifikasiValue = (relation) => relation.justifikasi ?? '-';
    const shouldIncludeRelation = (relation) => {
        if (selectedModelRelasi.value === 'all') {
            return true;
        }

        return relation?.model_relasi === selectedModelRelasi.value;
    };
    const rows = [];
    const seen = new Set();

    const relationKey = (relation) => {
        if (relation?.id) {
            return `id-${relation.id}`;
        }

        return `row-${relation?.initiative_code_row}-col-${relation?.initiative_code_column}`;
    };

    initiativeRelationsRow.forEach((relation) => {
        if (!shouldIncludeRelation(relation)) {
            return;
        }

        const key = relationKey(relation);
        if (seen.has(key)) {
            return;
        }
        seen.add(key);

        const { predecessorId, successorId } = getRelationDirection(relation);

        rows.push({
            id: relation.id,
            predecessor: formatInitiative(findInitiativeById(predecessorId), predecessorId),
            successor: formatInitiative(findInitiativeById(successorId), successorId),
            predecessor_id: predecessorId,
            successor_id: successorId,
            row_initiative_id: toNumericId(relation?.initiative_code_row),
            column_initiative_id: toNumericId(relation?.initiative_code_column),
            justifikasi: justifikasiValue(relation),
            model_relasi: relation.model_relasi ?? '-',
            type_relation: relation.type_relation != null ? Number(relation.type_relation) : null,
            x: toCoordinate(relation?.x),
            y: toCoordinate(relation?.y),
        });
    });

    initiativeRelationsColumn.forEach((relation) => {
        if (!shouldIncludeRelation(relation)) {
            return;
        }

        const key = relationKey(relation);
        if (seen.has(key)) {
            return;
        }
        seen.add(key);

        const { predecessorId, successorId } = getRelationDirection(relation);

        rows.push({
            id: relation.id,
            predecessor: formatInitiative(findInitiativeById(predecessorId), predecessorId),
            successor: formatInitiative(findInitiativeById(successorId), successorId),
            predecessor_id: predecessorId,
            successor_id: successorId,
            row_initiative_id: toNumericId(relation?.initiative_code_row),
            column_initiative_id: toNumericId(relation?.initiative_code_column),
            justifikasi: justifikasiValue(relation),
            model_relasi: relation.model_relasi ?? '-',
            type_relation: relation.type_relation != null ? Number(relation.type_relation) : null,
            x: toCoordinate(relation?.x),
            y: toCoordinate(relation?.y),
        });
    });

    return rows;
};

const relationsByInitiativeId = computed(() => {
    const map = new Map();
    props.mstInitiatives.forEach((initiative) => {
        map.set(Number(initiative.id), extractRelations(initiative));
    });
    return map;
});

const relationRowsByInitiative = (initiative) => (
    relationsByInitiativeId.value.get(Number(initiative?.id)) ?? []
);

const relationRowKey = (relation) => {
    if (relation?.id != null) {
        return `id-${relation.id}`;
    }

    return [
        relation?.predecessor_id ?? '-',
        relation?.successor_id ?? '-',
        relation?.type_relation ?? '-',
        relation?.model_relasi ?? '-',
    ].join('|');
};

const allRelationRows = computed(() => {
    const rows = [];
    const seen = new Set();

    filteredInitiatives.value.forEach((initiative) => {
        relationRowsByInitiative(initiative).forEach((relation) => {
            const key = relationRowKey(relation);
            if (seen.has(key)) {
                return;
            }
            seen.add(key);
            rows.push(relation);
        });
    });

    return rows.sort((left, right) => {
        const leftPred = Number(left?.predecessor_id ?? 0);
        const rightPred = Number(right?.predecessor_id ?? 0);
        if (leftPred !== rightPred) {
            return leftPred - rightPred;
        }

        const leftSucc = Number(left?.successor_id ?? 0);
        const rightSucc = Number(right?.successor_id ?? 0);
        if (leftSucc !== rightSucc) {
            return leftSucc - rightSucc;
        }

        return Number(left?.id ?? 0) - Number(right?.id ?? 0);
    });
});

const initiativeFromRelation = (relation) => (
    findInitiativeById(relation?.predecessor_id) ?? findInitiativeById(relation?.successor_id) ?? null
);

const resolveStoredAllGraphPositionsFromInitiatives = (initiativeIds) => {
    const positions = new Map();
    initiativeIds.forEach((initiativeId) => {
        const initiative = findInitiativeById(initiativeId);
        if (initiative?.relation_position?.x != null && initiative?.relation_position?.y != null) {
            positions.set(initiativeId, { 
                x: Number(initiative.relation_position.x), 
                y: Number(initiative.relation_position.y) 
            });
        }
    });
    return positions;
};

const buildGraphForAllRelations = (relations) => {
    if (!relations.length) {
        return {
            nodes: [],
            edges: [],
            width: 960,
            height: 360,
            storedPositionCount: 0,
            missingPositionCount: 0,
            usesStoredPositions: false,
        };
    }

    const uniqueInitiativeIds = Array.from(
        new Set(
            relations.flatMap((relation) => [
                toNumericId(relation?.predecessor_id),
                toNumericId(relation?.successor_id),
            ]).filter((initiativeId) => initiativeId != null),
        ),
    ).sort((left, right) => left - right);

    if (!uniqueInitiativeIds.length) {
        return {
            nodes: [],
            edges: [],
            width: 960,
            height: 360,
            storedPositionCount: 0,
            missingPositionCount: 0,
            usesStoredPositions: false,
        };
    }

    const storedPositions = resolveStoredAllGraphPositionsFromInitiatives(uniqueInitiativeIds);
    const rawPositions = new Map(storedPositions);
    const storedPositionCount = storedPositions.size;
    const missingPositionCount = Math.max(uniqueInitiativeIds.length - storedPositionCount, 0);
    const storedPositionValues = Array.from(storedPositions.values());
    const fallbackAnchorX = storedPositionValues.length
        ? Math.max(...storedPositionValues.map(({ x }) => x)) + ALL_GRAPH_FALLBACK_X_GAP
        : 0;
    const fallbackAnchorY = storedPositionValues.length
        ? Math.min(...storedPositionValues.map(({ y }) => y))
        : 0;

    let fallbackIndex = 0;
    uniqueInitiativeIds.forEach((initiativeId) => {
        if (rawPositions.has(initiativeId)) {
            return;
        }

        const column = fallbackIndex % ALL_GRAPH_FALLBACK_COLUMNS;
        const row = Math.floor(fallbackIndex / ALL_GRAPH_FALLBACK_COLUMNS);

        rawPositions.set(initiativeId, {
            x: fallbackAnchorX + (column * ALL_GRAPH_FALLBACK_X_GAP),
            y: fallbackAnchorY + (row * ALL_GRAPH_FALLBACK_Y_GAP),
        });

        fallbackIndex += 1;
    });

    const rawPositionValues = Array.from(rawPositions.values());
    const minX = Math.min(...rawPositionValues.map(({ x }) => x));
    const maxX = Math.max(...rawPositionValues.map(({ x }) => x));
    const minY = Math.min(...rawPositionValues.map(({ y }) => y));
    const maxY = Math.max(...rawPositionValues.map(({ y }) => y));

    const normalizedPositions = new Map(
        Array.from(rawPositions.entries()).map(([initiativeId, position]) => [
            initiativeId,
            {
                x: position.x - minX + FLOW_GRAPH_PADDING,
                y: position.y - minY + FLOW_GRAPH_PADDING,
            },
        ]),
    );

    const nodes = uniqueInitiativeIds.map((initiativeId) => {
        const linkedInitiative = findInitiativeById(initiativeId);

        return {
            id: initiativeNodeId(initiativeId),
            position: normalizedPositions.get(initiativeId) ?? { x: FLOW_GRAPH_PADDING, y: FLOW_GRAPH_PADDING },
            data: buildInitiativeNodeData(linkedInitiative, initiativeId),
            type: 'initiative-status-card',
            class: 'initiative-status-card',
            sourcePosition: Position.Right,
            targetPosition: Position.Left,
            draggable: !isPositionsLocked.value,
            selectable: true,
            style: resolveInitiativeNodeStyle(linkedInitiative),
        };
    });

    const edges = relations
        .map((relation, index) => {
            const predecessorId = toNumericId(relation?.predecessor_id);
            const successorId = toNumericId(relation?.successor_id);

            if (predecessorId == null || successorId == null) {
                return null;
            }

            const lineColor = relationLineColor(relation?.type_relation);

            return {
                id: relation?.id != null ? `all-relation-${relation.id}` : `all-relation-${index}`,
                source: initiativeNodeId(predecessorId),
                target: initiativeNodeId(successorId),
                markerEnd: {
                    type: MarkerType.ArrowClosed,
                    color: lineColor,
                },
                type: 'straight',
                style: {
                    stroke: lineColor,
                    strokeWidth: 2.1,
                },
                label: showEdgeLabels.value && relation?.model_relasi && relation.model_relasi !== '-' ? relation.model_relasi : undefined,
                labelStyle: {
                    fill: '#475569',
                    fontSize: 11,
                    fontWeight: 600,
                },
                labelBgStyle: {
                    fill: '#ffffff',
                    fillOpacity: 0.95,
                },
                labelBgPadding: [4, 2],
                selectable: false,
                focusable: false,
            };
        })
        .filter(Boolean);

    return {
        nodes,
        edges,
        width: Math.max(960, (maxX - minX) + FLOW_NODE_WIDTH + (FLOW_GRAPH_PADDING * 2)),
        height: Math.max(600, (maxY - minY) + FLOW_NODE_HEIGHT + (FLOW_GRAPH_PADDING * 2)),
        storedPositionCount,
        missingPositionCount,
        usesStoredPositions: storedPositionCount > 0,
    };
};

const buildGraphForInitiative = (initiative, relations) => {
    const currentId = toNumericId(initiative?.id);
    if (currentId == null) {
        return { nodes: [], edges: [], height: 260 };
    }

    const predecessors = [];
    const successors = [];
    const predecessorSet = new Set();
    const successorSet = new Set();

    relations.forEach((relation) => {
        const predecessorId = toNumericId(relation?.predecessor_id);
        const successorId = toNumericId(relation?.successor_id);

        if (
            successorId === currentId
            && predecessorId != null
            && predecessorId !== currentId
            && !predecessorSet.has(predecessorId)
        ) {
            predecessorSet.add(predecessorId);
            predecessors.push(predecessorId);
        }

        if (
            predecessorId === currentId
            && successorId != null
            && successorId !== currentId
            && !successorSet.has(successorId)
        ) {
            successorSet.add(successorId);
            successors.push(successorId);
        }
    });

    const rowGap = 92;
    const rowCount = Math.max(predecessors.length, successors.length, 1);
    const centerY = 36 + ((rowCount - 1) * rowGap) / 2;
    const startYByCount = (count) => centerY - ((Math.max(count, 1) - 1) * rowGap) / 2;
    const diagramHeight = Math.max(260, Math.min(560, rowCount * rowGap + 120));

    const nodes = [
        {
            id: initiativeNodeId(currentId),
            position: { x: 360, y: centerY },
            data: buildInitiativeNodeData(initiative, currentId, true),
            type: 'initiative-status-card',
            class: 'initiative-status-card initiative-status-card--focus',
            sourcePosition: Position.Right,
            targetPosition: Position.Left,
            draggable: false,
            selectable: false,
            style: resolveInitiativeNodeStyle(initiative, true),
        },
    ];

    const predecessorStartY = startYByCount(predecessors.length);
    predecessors.forEach((initiativeId, index) => {
        const linkedInitiative = findInitiativeById(initiativeId);
        nodes.push({
            id: initiativeNodeId(initiativeId),
            position: { x: 30, y: predecessorStartY + (index * rowGap) },
            data: buildInitiativeNodeData(linkedInitiative, initiativeId),
            type: 'initiative-status-card',
            class: 'initiative-status-card',
            sourcePosition: Position.Right,
            targetPosition: Position.Right,
            draggable: false,
            selectable: false,
            style: resolveInitiativeNodeStyle(linkedInitiative),
        });
    });

    const successorStartY = startYByCount(successors.length);
    successors.forEach((initiativeId, index) => {
        const linkedInitiative = findInitiativeById(initiativeId);
        nodes.push({
            id: initiativeNodeId(initiativeId),
            position: { x: 690, y: successorStartY + (index * rowGap) },
            data: buildInitiativeNodeData(linkedInitiative, initiativeId),
            type: 'initiative-status-card',
            class: 'initiative-status-card',
            sourcePosition: Position.Left,
            targetPosition: Position.Left,
            draggable: false,
            selectable: false,
            style: resolveInitiativeNodeStyle(linkedInitiative),
        });
    });

    const edges = relations
        .map((relation, index) => {
            const predecessorId = toNumericId(relation?.predecessor_id);
            const successorId = toNumericId(relation?.successor_id);

            if (predecessorId == null || successorId == null) {
                return null;
            }

            const lineColor = relationLineColor(relation?.type_relation);

            return {
                id: relation?.id != null ? `relation-${relation.id}` : `relation-${currentId}-${index}`,
                source: initiativeNodeId(predecessorId),
                target: initiativeNodeId(successorId),
                markerEnd: {
                    type: MarkerType.ArrowClosed,
                    color: lineColor,
                },
                type: 'straight',
                style: {
                    stroke: lineColor,
                    strokeWidth: 2.1,
                },
                label: showEdgeLabels.value && relation?.model_relasi && relation.model_relasi !== '-' ? relation.model_relasi : undefined,
                labelStyle: {
                    fill: '#475569',
                    fontSize: 11,
                    fontWeight: 600,
                },
                labelBgStyle: {
                    fill: '#ffffff',
                    fillOpacity: 0.95,
                },
                labelBgPadding: [4, 2],
                selectable: false,
                focusable: false,
            };
        })
        .filter(Boolean);

    return {
        nodes,
        edges,
        height: diagramHeight,
    };
};

const relationGraphByInitiativeId = computed(() => {
    const map = new Map();
    props.mstInitiatives.forEach((initiative) => {
        const relations = relationRowsByInitiative(initiative);
        map.set(Number(initiative?.id), buildGraphForInitiative(initiative, relations));
    });
    return map;
});

const relationGraphByInitiative = (initiative) => (
    relationGraphByInitiativeId.value.get(Number(initiative?.id)) ?? { nodes: [], edges: [], height: 260 }
);

const allRelationGraph = computed(() => buildGraphForAllRelations(allRelationRows.value));

</script>

<style scoped>
:deep(.initiative-relation-flow.vue-flow) {
    background: transparent;
}

:deep(.initiative-relation-flow .vue-flow__node) {
    width: 240px;
    white-space: normal;
    text-align: center;
    cursor: pointer;
}

:deep(.initiative-relation-flow .vue-flow__node.selected) {
    cursor: grab;
}

:deep(.initiative-relation-flow .vue-flow__node.selected.dragging),
:deep(.initiative-relation-flow .vue-flow__node.selected:active) {
    cursor: grabbing;
}

:deep(.initiative-relation-flow .vue-flow__node.initiative-node-block) {
    font-family: inherit;
}

:deep(.initiative-relation-flow .vue-flow__node.initiative-node-block--focus) {
    transform: translateZ(0);
}

:deep(.initiative-relation-flow .vue-flow__handle) {
    width: 8px;
    height: 8px;
    opacity: 0;
    pointer-events: none;
    border: 0;
    background: transparent;
}

:deep(.initiative-relation-flow .vue-flow__edge-path) {
    stroke-linecap: round;
    stroke-linejoin: round;
}

:deep(.dark .initiative-relation-flow.vue-flow) {
    background: transparent;
}
</style>
