<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Function List</h2>
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari function..."
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-48"
                />
                <select
                    v-model="companyFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-40 truncate"
                >
                    <option value="">Semua Perusahaan</option>
                    <option v-for="option in availableCompanies" :key="option.id" :value="option.id">
                        {{ option.name }}
                    </option>
                </select>
                <select
                    v-model="parentFilterId"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white w-40 truncate"
                >
                    <option value="">Semua Function</option>
                    <option v-for="fn in parentFilterOptions" :key="fn.id" :value="fn.id">
                        {{ getLevelPrefix(fn) }}{{ fn.name }}
                    </option>
                </select>
                <select
                    v-model="expandLevel"
                    @change="handleExpandLevelChange"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white cursor-pointer"
                >
                    <option value="custom" disabled>Expand Level...</option>
                    <option value="0">Collapse All</option>
                    <option v-for="depth in maxDepth + 1" :key="depth" :value="depth">
                        Level {{ depth }}
                    </option>
                    <option value="all">Expand All</option>
                </select>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Function
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-0 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-16">Company</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Function Name</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Alias</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Organization</th>
                        <th class="px-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Description</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="fn in visibleRows"
                        :key="'fn-' + fn.id"
                        class="group transition duration-150 hover:bg-slate-50/50 dark:hover:bg-white/5 animate-fade-in"
                    >
                        <td
                            v-if="fn.companyRowspan > 0"
                            :rowspan="fn.companyRowspan"
                            class="px-2 py-2 text-slate-600 dark:text-slate-300 text-xs whitespace-normal break-words max-w-[80px] align-top border-r border-slate-100 dark:border-white/5 bg-slate-50/60 dark:bg-white/[0.02]"
                        >
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ fn.company?.name || '-' }}</span>
                        </td>
                        <td 
                            class="px-4 py-2 text-slate-900 dark:text-white text-xs break-words font-medium" 
                            :style="{ paddingLeft: (fn.depth * 24 + 16) + 'px' }"
                        >
                            <div class="flex items-center gap-2">
                                <!-- Toggle Button / Branch Spacer -->
                                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                    <button 
                                        v-if="fn.hasChildren" 
                                        @click.stop="toggleExpand(fn.id)" 
                                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0 flex items-center justify-center"
                                    >
                                        <svg v-if="fn.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                    <span v-else-if="fn.depth > 0" class="text-slate-300 dark:text-white/20 font-mono text-xs select-none">├─</span>
                                </div>
                                
                                <span>
                                    {{ fn.name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-xs">
                            {{ displayValue(fn.alias) }}
                        </td>
                        <td class="px-4 py-2 text-slate-600 dark:text-slate-300 text-xs">
                            <div v-if="fn.organizations && fn.organizations.length > 0" class="space-y-1">
                                <div v-for="org in fn.organizations" :key="org.id">
                                    {{ getBodLabel(org.id) }}
                                </div>
                            </div>
                            <span v-else class="text-slate-400 italic">-</span>
                        </td>
                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-xs whitespace-pre-wrap break-words">
                            {{ fn.deskripsi || '-' }}
                        </td>
                        <td class="px-4 py-2 text-center print:hidden">
                            <div class="flex items-center justify-center gap-1.5">
                                <button
                                    @click="openEditModal(fn)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteModal(fn)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="visibleRows.length === 0">
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data function tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah Function' : 'Edit Function'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan function baru.' : 'Silakan sesuaikan data function di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4">
            <!-- Company Option Select -->
            <div class="flex flex-col gap-1.5">
                <label for="company_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Perusahaan</label>
                <select
                    id="company_id"
                    v-model="form.company_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih Perusahaan...</option>
                    <option v-for="option in companyOptions" :key="option.id" :value="option.id">
                        {{ option.name }}
                    </option>
                </select>
                <span v-if="form.errors.company_id" class="text-xs text-red-500 font-medium">{{ form.errors.company_id }}</span>
            </div>

            <!-- Parent Function Select -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_parent_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Function Induk</label>
                <select
                    id="fn_parent_id"
                    v-model="form.parent_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                >
                    <option value="">Tanpa Induk (Root / Level 1)</option>
                    <option v-for="fn in filteredParentOptions" :key="fn.id" :value="fn.id">
                        {{ getLevelPrefix(fn) }}{{ fn.name }}
                    </option>
                </select>
                <span v-if="form.errors.parent_id" class="text-xs text-red-500 font-medium">{{ form.errors.parent_id }}</span>
            </div>


            <!-- Organization Input -->
            <div class="flex flex-col gap-1.5 relative">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Organization</label>
                
                <div class="relative">
                    <button 
                        type="button"
                        @click="toggleBodDropdown"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-left focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white flex justify-between items-center"
                    >
                        <span class="truncate text-slate-400 dark:text-slate-500">
                            -- Pilih Organization --
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </div>

                <div v-if="isBodDropdownOpen" class="fixed inset-0 z-30" @click="isBodDropdownOpen = false"></div>

                <div v-if="isBodDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                        <input 
                            type="text" 
                            v-model="bodSearchQuery" 
                            placeholder="Cari organization atau pejabat..." 
                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                            ref="bodSearchInput"
                            @click.stop
                        />
                    </div>
                    
                    <div class="space-y-0.5">
                        <button
                            v-for="bod in filteredBodOptions" 
                            :key="bod.id"
                            type="button"
                            @click="toggleBod(bod.id)"
                            :class="[
                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                form.organization_ids.includes(bod.id) ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white font-semibold' : 'text-slate-700 dark:text-slate-300'
                            ]"
                        >
                            <span class="truncate">
                                {{ getLevelPrefixForDepth(bod.depth) }}{{ getBodDisplayName(bod) }}
                            </span>
                            <svg v-if="form.organization_ids.includes(bod.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-900 dark:text-white shrink-0">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="filteredBodOptions.length === 0" class="text-center py-4 text-xs text-slate-400">
                            Tidak ada hasil ditemukan.
                        </div>
                    </div>
                </div>

                <div v-if="form.organization_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                    <span 
                        v-for="id in form.organization_ids" 
                        :key="id"
                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                    >
                        <span class="max-w-[200px] truncate">
                            {{ getBodLabel(id) }}
                        </span>
                        <button 
                            type="button" 
                            @click="removeBodId(id)"
                            class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </span>
                </div>
                <span v-if="form.errors.organization_ids" class="text-xs text-red-500 font-medium">{{ form.errors.organization_ids }}</span>
            </div>


            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Function</label>
                <input
                    id="fn_name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: IT Operations"
                    required
                />
                <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
            </div>

            <!-- Alias Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_alias" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Alias (Singkatan)</label>
                <input
                    id="fn_alias"
                    v-model="form.alias"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: FN-OPS"
                />
                <span v-if="form.errors.alias" class="text-xs text-red-500 font-medium">{{ form.errors.alias }}</span>
            </div>

            <!-- Deskripsi Input -->
            <div class="flex flex-col gap-1.5">
                <label for="fn_deskripsi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi</label>
                <textarea
                    id="fn_deskripsi"
                    v-model="form.deskripsi"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Tulis deskripsi function di sini..."
                ></textarea>
                <span v-if="form.errors.deskripsi" class="text-xs text-red-500 font-medium">{{ form.errors.deskripsi }}</span>
            </div>

            <!-- Regulation Links Input -->
            <!-- Regulation Links Input -->
            <div class="flex flex-col gap-1.5 relative">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Regulation Links</label>
                
                <!-- Trigger Button -->
                <div class="relative">
                    <button 
                        type="button"
                        @click="toggleRegulationDropdown"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-left focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white flex justify-between items-center"
                    >
                        <span class="truncate text-slate-400 dark:text-slate-500">
                            -- Pilih Regulation --
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </div>

                <!-- Overlay for click outside -->
                <div v-if="isRegulationDropdownOpen" class="fixed inset-0 z-30" @click="isRegulationDropdownOpen = false"></div>

                <!-- Dropdown Content -->
                <div v-if="isRegulationDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                    <!-- Search input inside dropdown -->
                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                        <input 
                            type="text" 
                            v-model="regulationSearchQuery" 
                            placeholder="Cari regulation berdasarkan judul atau nomor..." 
                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                            ref="regulationSearchInput"
                            @click.stop
                        />
                    </div>
                    
                    <!-- Options list -->
                    <div class="space-y-0.5">
                        <button
                            v-for="reg in filteredFormRegulations" 
                            :key="reg.id"
                            type="button"
                            @click="toggleRegulation(reg.id)"
                            :class="[
                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                form.regulation_ids.includes(reg.id) ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white font-semibold' : 'text-slate-700 dark:text-slate-300'
                            ]"
                        >
                            <span class="truncate">
                                [{{ reg.tipe }}] {{ reg.judul }} {{ reg.nomor ? `(${reg.nomor})` : '' }}
                            </span>
                            <svg v-if="form.regulation_ids.includes(reg.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-900 dark:text-white shrink-0">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="filteredFormRegulations.length === 0" class="text-center py-4 text-xs text-slate-400">
                            Tidak ada hasil ditemukan.
                        </div>
                    </div>
                </div>

                <!-- Selected list display -->
                <div v-if="form.regulation_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                    <span 
                        v-for="id in form.regulation_ids" 
                        :key="id"
                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                    >
                        <span class="max-w-[200px] truncate">
                            {{ getRegulationLabel(id) }}
                        </span>
                        <button 
                            type="button" 
                            @click="removeRegulationId(id)"
                            class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </span>
                </div>
                <span v-if="form.errors.regulation_ids" class="text-xs text-red-500 font-medium">{{ form.errors.regulation_ids }}</span>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus Function"
        :message="`Apakah Anda yakin ingin menghapus function '${selectedFn?.name}'? Tindakan ini tidak dapat dibatalkan.`"
        confirm-text="Hapus"
        cancel-text="Batal"
        type="danger"
        :loading="form.processing"
        @close="isDeleteModalOpen = false"
        @confirm="submitDelete"
    />
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    functions: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    bodOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

console.log('FunctionTable props.regulations:', props.regulations);

const displayValue = (value) => value ?? '-';

// ─── Helpers ────────────────────────────────────────────────────────────────

const fnMap = computed(() => new Map(props.functions.map(fn => [fn.id, fn])));

const availableCompanies = computed(() => {
    const compMap = new Map();
    props.functions.forEach(fn => {
        if (fn.company) {
            compMap.set(fn.company.id, fn.company);
        }
    });
    return Array.from(compMap.values()).sort((a, b) => (a.name || '').localeCompare(b.name || ''));
});

/** Hitung kedalaman hierarki dari sebuah node */
const getDepth = (id) => {
    let depth = 0;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = fnMap.value.get(currentId);
        if (node?.parent_id) {
            depth++;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return depth;
};

const getLevelPrefix = (fn) => {
    const depth = getDepth(fn.id);
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const getParentLabel = (parentId) => {
    if (!parentId) return '-';
    const parent = fnMap.value.get(parentId);
    return parent ? parent.name : '-';
};

/** Cek apakah fnId adalah turunan dari targetParentId */
const isDescendant = (id, targetId) => {
    if (!id || !targetId) return false;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = fnMap.value.get(currentId);
        if (node?.parent_id) {
            if (Number(node.parent_id) === Number(targetId)) return true;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return false;
};

// ─── Hierarchy tree mapping ──────────────────────────────────────────────────
const functionTree = computed(() => {
    const map = {};
    const roots = [];

    props.functions.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });

    props.functions.forEach(item => {
        const mapped = map[item.id];
        if (item.parent_id && map[item.parent_id]) {
            map[item.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    return roots;
});

// ─── Expand / Collapse State ─────────────────────────────────────────────────
const expandedIds = ref(new Set());
const expandLevel = ref('all');

const maxDepth = computed(() => {
    let max = 0;
    props.functions.forEach(item => {
        const d = getDepth(item.id);
        if (d > max) max = d;
    });
    return max;
});

const toggleExpand = (id) => {
    expandLevel.value = 'custom';
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id);
    } else {
        expandedIds.value.add(id);
    }
    expandedIds.value = new Set(expandedIds.value);
};

const handleExpandLevelChange = () => {
    const val = expandLevel.value;
    if (val === 'custom') return;

    const ids = new Set();

    if (val === 'all') {
        props.functions.forEach(item => {
            const isParent = props.functions.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
    } else {
        const targetDepth = parseInt(val, 10);
        if (targetDepth > 0) {
            props.functions.forEach(item => {
                const depth = getDepth(item.id);
                if (depth < targetDepth) {
                    const isParent = props.functions.some(r => r.parent_id === item.id);
                    if (isParent) {
                        ids.add(item.id);
                    }
                }
            });
        }
    }
    expandedIds.value = ids;
};

const initializeExpanded = () => {
    const ids = new Set();
    props.functions.forEach(item => {
        const isParent = props.functions.some(r => r.parent_id === item.id);
        if (isParent) {
            ids.add(item.id);
        }
    });
    expandedIds.value = ids;
    expandLevel.value = 'all';
};

onMounted(() => {
    initializeExpanded();
});

watch(
    () => props.functions,
    () => {
        initializeExpanded();
    },
    { deep: false }
);

// ─── Filters & Search ────────────────────────────────────────────────────────
const searchQuery = ref('');
const companyFilterId = ref('');
const parentFilterId = ref('');

const matchesSearch = (node) => {
    if (!searchQuery.value) return true;
    const q = searchQuery.value.toLowerCase().trim();
    const matchesName = (node.name || '').toLowerCase().includes(q);
    const matchesAlias = (node.alias || '').toLowerCase().includes(q);
    const matchesDesc = (node.deskripsi || '').toLowerCase().includes(q);
    const matchesReg = (node.regulations || []).some(reg =>
        (reg.judul || '').toLowerCase().includes(q) ||
        (reg.nomor || '').toLowerCase().includes(q)
    );
    const matchesBod = (node.organizations || []).some(org =>
        getBodLabel(org.id).toLowerCase().includes(q)
    );
    return matchesName || matchesAlias || matchesDesc || matchesReg || matchesBod;
};

const shouldShowNode = (node) => {
    if (matchesSearch(node)) return true;
    if (node.children && node.children.length > 0) {
        return node.children.some(child => shouldShowNode(child));
    }
    return false;
};

watch(searchQuery, (newQuery) => {
    if (newQuery) {
        const ids = new Set();
        props.functions.forEach(item => {
            const isParent = props.functions.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
        expandedIds.value = ids;
        expandLevel.value = 'all';
    } else {
        initializeExpanded();
    }
});

// Root selection filter
const filteredRoots = computed(() => {
    let roots = functionTree.value;
    
    if (companyFilterId.value) {
        const compId = Number(companyFilterId.value);
        roots = roots.filter(root => Number(root.company_id) === compId);
    }

    if (parentFilterId.value) {
        const filterId = Number(parentFilterId.value);
        
        const findNode = (nodes) => {
            for (const n of nodes) {
                if (n.id === filterId) return [n];
                if (n.children && n.children.length > 0) {
                    const found = findNode(n.children);
                    if (found) return found;
                }
            }
            return null;
        };
        
        return findNode(roots) || [];
    }
    return roots;
});

// Build active visible rows
const visibleRows = computed(() => {
    const rows = [];
    
    const traverse = (node, depth = 0) => {
        const visibleChildren = (node.children || []).filter(child => shouldShowNode(child));
        const hasChildren = visibleChildren.length > 0;
        const isExpanded = expandedIds.value.has(node.id);
        
        rows.push({
            ...node,
            depth,
            hasChildren,
            isExpanded
        });
        
        if (hasChildren && isExpanded) {
            visibleChildren.forEach(child => {
                traverse(child, depth + 1);
            });
        }
    };
    
    filteredRoots.value.forEach(root => {
        if (shouldShowNode(root)) {
            traverse(root, 0);
        }
    });
    
    // Compute companyRowspan: for each root group, count total visible rows belonging to it
    // Group consecutive rows by their root company
    // We tag each row with companyRowspan > 0 only for the first row of each company group
    // and companyRowspan = 0 for subsequent rows in the same group (so we skip rendering td)
    const rootCompanyOf = (row) => {
        // The root-level company: travel up from row's ancestry
        // Since rows retain their company_id from the flat functions list, use that.
        return row.company_id ?? null;
    };

    let i = 0;
    while (i < rows.length) {
        const companyId = rootCompanyOf(rows[i]);
        // Find how many consecutive rows share the same company_id
        let span = 1;
        while (i + span < rows.length && rootCompanyOf(rows[i + span]) === companyId) {
            span++;
        }
        rows[i].companyRowspan = span;
        for (let j = 1; j < span; j++) {
            rows[i + j].companyRowspan = 0;
        }
        i += span;
    }
    
    return rows;
});

// Flatten tree for dropdown selection filter
const parentFilterOptions = computed(() => {
    const result = [];
    const traverse = (node, depth = 0) => {
        result.push({ ...node, depth });
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => traverse(child, depth + 1));
        }
    };
    
    let roots = functionTree.value;
    if (companyFilterId.value) {
        const compId = Number(companyFilterId.value);
        roots = roots.filter(root => Number(root.company_id) === compId);
    }
    
    roots.forEach(root => traverse(root, 0));
    return result;
});

watch(companyFilterId, (newCompanyFilterId) => {
    if (parentFilterId.value) {
        const selectedParent = props.functions.find(f => Number(f.id) === Number(parentFilterId.value));
        if (selectedParent && newCompanyFilterId && Number(selectedParent.company_id) !== Number(newCompanyFilterId)) {
            parentFilterId.value = '';
        }
    }
});

watch(() => props.functions, (newFunctions) => {
    if (parentFilterId.value && !newFunctions.some(fn => Number(fn.id) === Number(parentFilterId.value))) {
        parentFilterId.value = '';
    }
});

// ─── Modal state ─────────────────────────────────────────────────────────────

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedFn = ref(null);
const modalMode = ref('create');
const regulationSearchQuery = ref('');
const bodSearchQuery = ref('');

// Dropdown selector state & helpers
const isRegulationDropdownOpen = ref(false);
const regulationSearchInput = ref(null);
const isBodDropdownOpen = ref(false);
const bodSearchInput = ref(null);

const toggleRegulationDropdown = () => {
    isRegulationDropdownOpen.value = !isRegulationDropdownOpen.value;
    if (isRegulationDropdownOpen.value) {
        regulationSearchQuery.value = '';
        setTimeout(() => {
            regulationSearchInput.value?.focus();
        }, 100);
    }
};

const removeRegulationId = (id) => {
    const index = form.regulation_ids.indexOf(id);
    if (index > -1) {
        form.regulation_ids.splice(index, 1);
    }
};

const toggleBodDropdown = () => {
    isBodDropdownOpen.value = !isBodDropdownOpen.value;
    if (isBodDropdownOpen.value) {
        bodSearchQuery.value = '';
        setTimeout(() => {
            bodSearchInput.value?.focus();
        }, 100);
    }
};

const getBodDisplayName = (bod) => {
    const name = bod.name || '-';
    const alias = bod.alias ? ` (${bod.alias})` : '';
    return `${name}${alias}`;
};

const getLevelPrefixForDepth = (depth) => {
    if (!depth) return '';
    return '\u00A0\u00A0'.repeat(depth) + '- ';
};

const bodOptionsWithDepth = computed(() => {
    const map = new Map(props.bodOptions.map(bod => [bod.id, { ...bod, children: [] }]));
    const roots = [];

    props.bodOptions.forEach(bod => {
        const node = map.get(bod.id);
        if (bod.parent_id && map.has(bod.parent_id)) {
            map.get(bod.parent_id).children.push(node);
        } else {
            roots.push(node);
        }
    });

    const sortNodes = (nodes) => {
        nodes.sort((a, b) => Number(a.order ?? 0) - Number(b.order ?? 0) || (a.name || '').localeCompare(b.name || ''));
        nodes.forEach(node => sortNodes(node.children));
    };
    sortNodes(roots);

    const flattened = [];
    const traverse = (node, depth = 0) => {
        flattened.push({ ...node, depth });
        node.children.forEach(child => traverse(child, depth + 1));
    };
    roots.forEach(root => traverse(root));

    return flattened;
});

const filteredBodOptions = computed(() => {
    const query = bodSearchQuery.value.toLowerCase().trim();
    const pool = bodOptionsWithDepth.value;
    if (!query) return pool;
    return pool.filter(bod =>
        (bod.name || '').toLowerCase().includes(query) ||
        (bod.alias || '').toLowerCase().includes(query) ||
        (bod.pejabat || '').toLowerCase().includes(query) ||
        (bod.tipe || '').toLowerCase().includes(query)
    );
});

const getBodLabel = (id) => {
    const bod = props.bodOptions.find(item => Number(item.id) === Number(id));
    return bod ? getBodDisplayName(bod) : '';
};

const toggleBod = (id) => {
    const index = form.organization_ids.indexOf(id);
    if (index > -1) {
        form.organization_ids.splice(index, 1);
    } else {
        form.organization_ids.push(id);
    }
};

const removeBodId = (id) => {
    const index = form.organization_ids.indexOf(id);
    if (index > -1) {
        form.organization_ids.splice(index, 1);
    }
};

const filteredFormRegulations = computed(() => {
    const query = regulationSearchQuery.value.toLowerCase().trim();
    if (!query) return props.regulations;
    return props.regulations.filter(reg =>
        (reg.judul || '').toLowerCase().includes(query) ||
        (reg.nomor || '').toLowerCase().includes(query) ||
        (reg.tipe || '').toLowerCase().includes(query)
    );
});

const getRegulationLabel = (id) => {
    const reg = props.regulations.find(r => r.id === Number(id));
    return reg ? `[${reg.tipe}] ${reg.judul}` : '';
};

const toggleRegulation = (id) => {
    const index = form.regulation_ids.indexOf(id);
    if (index > -1) {
        form.regulation_ids.splice(index, 1);
    } else {
        form.regulation_ids.push(id);
    }
};

const form = useForm({
    company_id: '',
    parent_id: '',
    name: '',
    alias: '',
    deskripsi: '',
    regulation_ids: [],
    organization_ids: [],
});

/** Opsi parent di modal — exclude diri sendiri & turunannya saat edit, pre-filtered by company if selected */
const filteredParentOptions = computed(() => {
    const targetCompanyId = form.company_id ? String(form.company_id) : null;
    const fns = targetCompanyId
        ? props.functions.filter(fn => String(fn.company_id ?? '') === targetCompanyId)
        : props.functions;

    const map = new Map(fns.map(fn => [fn.id, { ...fn, children: [] }]));
    const roots = [];

    fns.forEach(fn => {
        const node = map.get(fn.id);
        if (fn.parent_id && map.has(fn.parent_id)) {
            map.get(fn.parent_id).children.push(node);
        } else {
            roots.push(node);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    const result = [];
    const traverse = (node) => {
        result.push(node);
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);

    return result.filter(fn => {
        if (modalMode.value === 'edit' && selectedFn.value) {
            const currentId = selectedFn.value.id;
            if (Number(fn.id) === Number(currentId)) return false;
            if (isDescendant(fn.id, currentId)) return false;
        }
        return true;
    });
});

// Watch company_id to clear parent_id if company changes and is no longer matching
watch(() => form.company_id, (newCompanyId) => {
    if (form.parent_id) {
        const parentFn = props.functions.find(f => String(f.id) === String(form.parent_id));
        if (parentFn && String(parentFn.company_id) !== String(newCompanyId)) {
            form.parent_id = '';
        }
    }
});

// Watch parent_id to set company_id to parent's company_id if selected parent belongs to a company
watch(() => form.parent_id, (newParentId) => {
    if (newParentId) {
        const parentFn = props.functions.find(f => String(f.id) === String(newParentId));
        if (parentFn && parentFn.company_id && String(form.company_id) !== String(parentFn.company_id)) {
            form.company_id = String(parentFn.company_id);
        }
    }
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    regulationSearchQuery.value = '';
    isRegulationDropdownOpen.value = false;
    bodSearchQuery.value = '';
    isBodDropdownOpen.value = false;
    isModalOpen.value = true;
};

const openEditModal = (fn) => {
    modalMode.value = 'edit';
    selectedFn.value = fn;
    form.clearErrors();
    form.company_id = String(fn.company_id ?? '');
    form.parent_id = fn.parent_id ? String(fn.parent_id) : '';
    form.name = fn.name || '';
    form.alias = fn.alias || '';
    form.deskripsi = fn.deskripsi || '';
    form.regulation_ids = fn.regulations ? fn.regulations.map(r => r.id) : [];
    form.organization_ids = fn.organizations ? fn.organizations.map(o => o.id) : [];
    regulationSearchQuery.value = '';
    isRegulationDropdownOpen.value = false;
    bodSearchQuery.value = '';
    isBodDropdownOpen.value = false;

    isModalOpen.value = true;
};

const openDeleteModal = (fn) => {
    selectedFn.value = fn;
    isDeleteModalOpen.value = true;
};

// ─── Submit ───────────────────────────────────────────────────────────────────

const submitForm = () => {
    const payload = {
        ...form.data(),
        parent_id: form.parent_id || null,
    };

    if (modalMode.value === 'create') {
        form.transform(() => payload).post(route('itom.business-process.function.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.transform(() => payload).put(route('itom.business-process.function.update', selectedFn.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('itom.business-process.function.destroy', selectedFn.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
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
