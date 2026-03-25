<template>
    <UserLayout title="Digital Initiatives - Compendium List">
        <div class="animate-fade-in space-y-4">
            <section class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <SummaryCard
                    :total="totalDigitalInitiatives"
                    @create="showCreateModal = true"
                    @show-all="toggleShowAll"
                />

                <div class="lg:col-span-2">
                    <TimelineFlow
                        :status-counts="statusCounts"
                        :postpone-from-counts="postponeFromCounts"
                        :active-status="activeStatusFilter"
                        @select="toggleStatusFilter"
                    />
                </div>
            </section>

            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-[#171717] w-fit">
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.master.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Digital Initiatives List
                </Link>
                <div
                    class="rounded-lg bg-blue-50 px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider text-blue-600 shadow-sm dark:bg-blue-500/10 dark:text-blue-400"
                >
                    Compendium List
                </div>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.appendix.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Appendix List
                </Link>
                <Link
                    :href="route('program-planning.program-definition.digital-initiatives.mapping.index')"
                    class="rounded-lg px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider transition-all text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-white/5"
                >
                    Mapping
                </Link>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Compendium List</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                        >
                            Total: {{ filteredCompendiumItems.length }}
                        </span>
                        <button
                            type="button"
                            @click="openCreateModal"
                            class="inline-flex items-center rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f]"
                        >
                            New Compendium
                        </button>
                    </div>
                </div>
            </section>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <!-- Filters Section (Compact Toolbar Style) -->
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 border-b border-slate-100 bg-slate-50/30 px-3.5 py-2 dark:border-white/5 dark:bg-white/5">
                    <div class="flex items-center gap-1.5">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Owner:</label>
                        <select
                            v-model="filters.owner"
                            class="w-[230px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[10px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All Project Owners</option>
                            <option v-for="owner in uniqueOwners" :key="owner" :value="owner">{{ owner }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">CoE:</label>
                        <select
                            v-model="filters.coe"
                            class="w-[88px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[10px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All CoE</option>
                            <option v-for="coe in uniqueCoes" :key="coe" :value="coe">{{ coe }}</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Value:</label>
                        <select
                            v-model="filters.value"
                            class="w-[100px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[10px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                            <option value="TBC">TBC</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Urgency:</label>
                        <select
                            v-model="filters.urgency"
                            class="w-[100px] rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[10px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                        >
                            <option value="">All</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                            <option value="TBC">TBC</option>
                        </select>
                    </div>

                    <div class="order-2 flex basis-full flex-wrap items-center gap-x-4 gap-y-2 border-t border-slate-100 pt-2 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Master Initiative:</label>
                            <select
                                v-model="filters.masterInitiative"
                                class="w-[400px] max-w-full rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[10px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                            >
                                <option value="">All</option>
                                <option value="none">No Master Initiative</option>
                                <option v-for="mi in sortedMasterInitiatives" :key="mi" :value="mi">{{ mi }}</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Compendium:</label>
                            <select
                                v-model="filters.compendium"
                                class="w-[400px] max-w-full rounded-lg border border-slate-200 bg-white py-1 pl-2 pr-7 text-[10px] text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-200"
                            >
                                <option value="">All</option>
                                <option v-for="comp in uniqueCompendiums" :key="comp" :value="comp">{{ comp }}</option>
                            </select>
                        </div>
                    </div>

                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="resetFilters"
                        class="order-1 ml-auto text-[10px] font-bold uppercase tracking-tighter text-rose-500 hover:text-rose-600 dark:text-rose-400"
                    >
                        Reset Filters
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[40px]">
                            <col class="w-[120px]">
                            <col class="w-[150px]">
                            <col class="w-[200px]">
                            <col class="w-[70px]">
                            <col class="w-[70px]">
                            <col class="w-[80px]">
                            <col class="w-[80px]">
                            <col class="w-[100px]">
                            <col class="w-[80px]">
                        </colgroup>
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Project Owner</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Use Case</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Value</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgency</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">RJPP</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">CoE</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Master Initiative</th>
                                <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(item, index) in filteredCompendiumItems" :key="`compendium-${item.id}`" class="transition-colors hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-3 py-3 text-center text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.project_owner ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200 font-medium">{{ item.use_case ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    <p class="whitespace-pre-line text-xs leading-snug text-slate-700 dark:text-slate-200">
                                        {{ item.desc ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.value)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.value ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="scoreClass(item.urgency)" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase">
                                        {{ item.urgency ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ (item.rjpp ?? '-').replace(/#/g, '') }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ item.coe ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    {{ item.master_initiatives }}
                                </td>
                                <td class="px-3 py-3 text-[10px] font-medium">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="route('program-planning.program-definition.digital-initiatives.compendium.edit', item.id)"
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-semibold text-amber-800 transition hover:bg-amber-200 dark:bg-amber-500/20 dark:text-amber-300"
                                            title="Edit Compendium"
                                        >
                                            Show
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredCompendiumItems.length === 0">
                                <td colspan="10" class="px-6 py-10 text-center text-xs text-slate-500 dark:text-slate-400 italic">
                                    Data tidak ditemukan berdasarkan filter yang dipilih.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            v-if="isCreateModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-4"
            @click.self="closeCreateModal"
        >
            <div class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-[#171717]">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-white/10">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-white">
                        New Compendium
                    </h2>
                    <button
                        type="button"
                        class="rounded-md px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-slate-200"
                        @click="closeCreateModal"
                    >
                        Close
                    </button>
                </div>

                <form class="flex-1 space-y-4 overflow-y-auto px-5 py-4" @submit.prevent="submitCreate">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Initiative</label>
                            <div class="space-y-2">
                                <select
                                    @change="(e) => { addInitiative(Number(e.target.value)); e.target.value = ''; }"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">+ Pilih Initiative...</option>
                                    <option
                                        v-for="opt in initiativeOptions"
                                        :key="`initiative-opt-${opt.id}`"
                                        :value="opt.id"
                                        :disabled="form.initiative_ids.includes(opt.id)"
                                    >
                                        {{ initiativeDisplayLabel(opt) }}
                                    </option>
                                </select>
                                <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                    <template v-if="form.initiative_ids.length">
                                        <span
                                            v-for="id in form.initiative_ids"
                                            :key="`initiative-tag-${id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-800 dark:bg-blue-500/20 dark:text-blue-300"
                                        >
                                            {{ initiativeLabel(id) }}
                                            <button type="button" class="text-blue-700/70 hover:text-rose-500 dark:text-blue-300/80" @click="removeInitiative(id)">x</button>
                                        </span>
                                    </template>
                                    <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada initiative dipilih.</span>
                                </div>
                            </div>
                            <p v-if="form.errors.initiative_ids" class="mt-1 text-[10px] text-rose-500">{{ form.errors.initiative_ids }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Project Owner</label>
                                <input
                                    v-model="form.owner"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Input project owner..."
                                >
                                <p v-if="form.errors.owner" class="mt-1 text-[10px] text-rose-500">{{ form.errors.owner }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">CoE</label>
                                <select
                                    v-model="form.coe"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                >
                                    <option value="">Pilih CoE...</option>
                                    <option v-for="coe in coeOptions" :key="coe.id" :value="coe.name">{{ coe.name }}</option>
                                </select>
                                <p v-if="form.errors.coe" class="mt-1 text-[10px] text-rose-500">{{ form.errors.coe }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Usecase</label>
                                <input
                                    v-model="form.usecase"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                                    placeholder="Input usecase..."
                                >
                                <p v-if="form.errors.usecase" class="mt-1 text-[10px] text-rose-500">{{ form.errors.usecase }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            placeholder="Input description..."></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-[10px] text-rose-500">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Source</label>
                        <select
                            v-model="form.source_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                        >
                            <option value="">Pilih Source...</option>
                            <option v-for="source in sourceOptions" :key="`source-${source.id}`" :value="source.id">
                                {{ sourceDisplayLabel(source) }}
                            </option>
                        </select>
                        <p v-if="form.errors.source_id" class="mt-1 text-[10px] text-rose-500">{{ form.errors.source_id }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Value</label>
                            <select
                                v-model="form.value"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="opt in scoreOptions" :key="`value-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.value" class="mt-1 text-[10px] text-rose-500">{{ form.errors.value }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Urgency</label>
                            <select
                                v-model="form.urgency"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="opt in scoreOptions" :key="`urgency-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.urgency" class="mt-1 text-[10px] text-rose-500">{{ form.errors.urgency }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Status</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option v-for="opt in statusOptions" :key="`status-${opt.value}`" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-[10px] text-rose-500">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">RJPP Tagging</label>
                        <div class="space-y-2">
                            <select
                                @change="(e) => { addRjppTagging(Number(e.target.value)); e.target.value = ''; }"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                            >
                                <option value="">+ Pilih RJPP Tagging...</option>
                                <option
                                    v-for="opt in themeOptions"
                                    :key="`theme-opt-${opt.id}`"
                                    :value="opt.id"
                                    :disabled="form.rjpp_tagging_ids.includes(opt.id)"
                                >
                                    {{ rjppDisplayLabel(opt) }}
                                </option>
                            </select>
                            <div class="flex min-h-10 flex-wrap gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
                                <template v-if="form.rjpp_tagging_ids.length">
                                    <span
                                        v-for="id in form.rjpp_tagging_ids"
                                        :key="`rjpp-tag-${id}`"
                                        class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-300"
                                    >
                                        {{ themeLabel(id) }}
                                        <button type="button" class="text-amber-700/70 hover:text-rose-500 dark:text-amber-300/80" @click="removeRjppTagging(id)">x</button>
                                    </span>
                                </template>
                                <span v-else class="text-[10px] italic text-slate-500 dark:text-slate-400">Belum ada RJPP dipilih.</span>
                            </div>
                        </div>
                        <p v-if="form.errors.rjpp_tagging_ids" class="mt-1 text-[10px] text-rose-500">{{ form.errors.rjpp_tagging_ids }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-3 dark:border-white/10">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/10"
                            @click="closeCreateModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-[#0f63b5] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#0c4e8f] disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Compendium' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    compendiumItems: {
        type: Array,
        default: () => [],
    },
    totalCompendiumItems: {
        type: Number,
        default: 0,
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    sourceOptions: {
        type: Array,
        default: () => [],
    },
    themeOptions: {
        type: Array,
        default: () => [],
    },
    uniqueMasterInitiatives: {
        type: Array,
        default: () => [],
    },
});

const route = useRouteHelper();

const isCreateModalOpen = ref(false);

const openCreateModal = () => {
    isCreateModalOpen.value = true;
};

const scoreOptions = [
    { value: 1, label: 'High' },
    { value: 2, label: 'Medium' },
    { value: 3, label: 'Low' },
    { value: null, label: 'TBC' },
];

const statusOptions = [
    { value: 1, label: 'Drafting' },
    { value: 2, label: 'Propose' },
    { value: 3, label: 'Review' },
    { value: 4, label: 'Baseline' },
    { value: 5, label: 'Approved' },
];

const filters = ref({
    owner: '',
    value: '',
    urgency: '',
    coe: '',
    masterInitiative: '',
    compendium: '',
});

const sourceOneItems = computed(() => {
    return props.compendiumItems.filter(item => Number(item.source_id) === 1);
});

const uniqueOwners = computed(() => {
    const owners = props.compendiumItems
        .map((item) => String(item.project_owner ?? '').trim())
        .filter((owner) => owner !== '' && owner !== '-');
    return [...new Set(owners)].sort();
});

const uniqueCoes = computed(() => {
    const coes = props.compendiumItems
        .map((item) => String(item.coe ?? '').trim())
        .filter((coe) => coe !== '' && coe !== '-');
    return [...new Set(coes)].sort();
});

const uniqueCompendiums = computed(() => {
    const compendiums = props.compendiumItems
        .map((item) => String(item.use_case ?? '').trim())
        .filter((comp) => comp !== '' && comp !== '-');
    return [...new Set(compendiums)].sort();
});

const sortedMasterInitiatives = computed(() => {
    return [...props.uniqueMasterInitiatives].sort((a, b) => {
        const aMatch = String(a).match(/\d+/);
        const bMatch = String(b).match(/\d+/);
        const aNum = aMatch ? parseInt(aMatch[0]) : 0;
        const bNum = bMatch ? parseInt(bMatch[0]) : 0;
        
        if (aNum !== bNum) return aNum - bNum;
        return String(a).localeCompare(String(b));
    });
});

const filteredCompendiumItems = computed(() => {
    return props.compendiumItems.filter((item) => {
        const ownerVal = String(item.project_owner ?? '').trim() || '-';
        const coeVal = String(item.coe ?? '').trim() || '-';
        const compVal = String(item.use_case ?? '').trim() || '-';

        const matchOwner = !filters.value.owner || ownerVal === filters.value.owner;
        const matchValue = !filters.value.value || item.value === filters.value.value;
        const matchUrgency = !filters.value.urgency || item.urgency === filters.value.urgency;
        const matchCoe = !filters.value.coe || coeVal === filters.value.coe;
        const matchCompendium = !filters.value.compendium || compVal === filters.value.compendium;
        
        let matchMasterInitiative = true;
        if (filters.value.masterInitiative === 'none') {
            matchMasterInitiative = item.master_initiatives === '-';
        } else if (filters.value.masterInitiative) {
            matchMasterInitiative = (item.master_initiatives ?? '').includes(filters.value.masterInitiative);
        }

        return matchOwner && matchValue && matchUrgency && matchCoe && matchMasterInitiative && matchCompendium;
    });
});

const hasActiveFilters = computed(() => {
    return !!(filters.value.owner || filters.value.value || filters.value.urgency || filters.value.coe || filters.value.masterInitiative || filters.value.compendium);
});

const resetFilters = () => {
    filters.value.owner = '';
    filters.value.value = '';
    filters.value.urgency = '';
    filters.value.coe = '';
    filters.value.masterInitiative = '';
    filters.value.compendium = '';
};

const form = useForm({
    initiative_ids: [],
    owner: '',
    coe: '',
    usecase: '',
    description: '',
    source_id: '',
    value: null,
    urgency: null,
    rjpp_tagging_ids: [],
    status: 1,
});

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
    resetForm();
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
    form.initiative_ids = [];
    form.owner = '';
    form.coe = '';
    form.source_id = '';
    form.rjpp_tagging_ids = [];
    form.value = null;
    form.urgency = null;
    form.status = 1;
};

const addInitiative = (id) => {
    if (id && !form.initiative_ids.includes(id)) {
        form.initiative_ids.push(id);
    }
};

const removeInitiative = (id) => {
    form.initiative_ids = form.initiative_ids.filter((item) => item !== id);
};

const stripInitiativePrefix = (name, code) => {
    const rawName = String(name ?? '').trim();
    const rawCode = String(code ?? '').trim().replace(/#/g, '');
    if (!rawName || !rawCode) return rawName;
    const escaped = rawCode.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const pattern = new RegExp(`^\\s*(\\[\\s*)?${escaped}(\\s*\\])?\\s*[-.:)]?\\s*`, 'i');
    const cleaned = rawName.replace(pattern, '').trim();
    return cleaned !== '' ? cleaned : rawName;
};

const initiativeDisplayLabel = (initiative) => {
    if (!initiative) return '-';
    const code = String(initiative?.code ?? '').trim().replace(/#/g, '');
    const name = stripInitiativePrefix(initiative?.name ?? '', code);
    if (code && name) return `[${code}] - ${name}`;
    if (code) return `[${code}]`;
    return name || '-';
};

const initiativeLabel = (id) => {
    const selected = props.initiativeOptions.find((item) => item.id === id);
    if (!selected) return String(id);
    return initiativeDisplayLabel(selected) || String(id);
};

const addRjppTagging = (id) => {
    if (id && !form.rjpp_tagging_ids.includes(id)) {
        form.rjpp_tagging_ids.push(id);
    }
};

const removeRjppTagging = (id) => {
    form.rjpp_tagging_ids = form.rjpp_tagging_ids.filter((item) => item !== id);
};

const rjppDisplayLabel = (theme) => {
    if (!theme) return '-';

    const code = String(theme?.code ?? theme?.goal_code ?? '').trim().replace(/#/g, '');
    const goal = String(theme?.goal ?? theme?.strategic_pillar ?? theme?.strategic_pillar_title ?? '').trim();
    const themeNumber = String(theme?.theme_number ?? theme?.theme_code ?? '').trim().replace(/#/g, '');
    const themeName = String(theme?.themes ?? theme?.theme_name ?? theme?.name ?? '').trim();

    const parts = [
        code !== '' ? code : '-',
        goal !== '' ? goal : '-',
        themeNumber !== '' ? themeNumber : '-',
        themeName !== '' ? themeName : '-',
    ];

    return parts.join(' - ');
};

const themeLabel = (id) => {
    const theme = props.themeOptions.find((item) => item.id === id);
    return rjppDisplayLabel(theme) || String(id);
};

const sourceDisplayLabel = (source) => {
    if (!source) return '-';

    const name = String(source?.name ?? '').trim();
    const month = String(source?.month ?? '').trim();
    const year = String(source?.year ?? '').trim();

    if (name === '') return '-';

    let datePart = '';
    if (month !== '' && year !== '') {
        datePart = ` (${month} ${year})`;
    } else if (year !== '') {
        datePart = ` (${year})`;
    }

    return `${name}${datePart}`;
};

const submitCreate = () => {
    form.transform((data) => ({
        ...data,
        initiative_ids: Array.isArray(data.initiative_ids) ? data.initiative_ids : [],
        value: data.value === '' ? null : data.value,
        urgency: data.urgency === '' ? null : data.urgency,
    })).post(route('program-planning.program-definition.digital-initiatives.compendium.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isCreateModalOpen.value = false;
            resetForm();
        },
    });
};

const scoreClass = (label) => {
    const l = String(label ?? '').toLowerCase();
    if (l === 'high') return 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300';
    if (l === 'medium') return 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300';
    if (l === 'low') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300';
    return 'bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-300';
};
</script>

<style scoped>
</style>
