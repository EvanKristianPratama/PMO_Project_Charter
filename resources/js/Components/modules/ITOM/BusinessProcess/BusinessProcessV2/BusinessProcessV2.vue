<template>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
            <div class="flex items-center gap-3">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari Proses Bisnis..."
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
                    <option value="">Semua Parent</option>
                    <option v-for="item in parentFilterOptions" :key="item.id" :value="item.id">
                        {{ getLevelPrefix(item) }}{{ item.name }}
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
                    @click="showDescriptionColumn = !showDescriptionColumn"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500/20 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path v-if="showDescriptionColumn" stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path v-if="showDescriptionColumn" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.893 7.893L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <span>{{ showDescriptionColumn ? 'Hide Desc' : 'Show Desc' }}</span>
                </button>
                <button
                    @click="showKpiColumn = !showKpiColumn"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500/20 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path v-if="showKpiColumn" stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path v-if="showKpiColumn" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.893 7.893L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <span>{{ showKpiColumn ? 'Hide KPI' : 'Show KPI' }}</span>
                </button>
                <button
                    @click="showRegulationColumn = !showRegulationColumn"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500/20 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path v-if="showRegulationColumn" stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path v-if="showRegulationColumn" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.893 7.893L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <span>{{ showRegulationColumn ? 'Hide STK' : 'Show STK' }}</span>
                </button>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500/20 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Business Process
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-slate-200 text-sm dark:divide-white/10">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="px-0 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-16">Company</th>
                        <th class="px-1 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-6">No</th>
                        <th :class="nameColWidth" class="pl-2 pr-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Business Process Name</th>
                        <th v-if="showDescriptionColumn" :class="flexibleColWidth" class="px-0 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Description / Tugas</th>
                        <th v-if="showKpiColumn" :class="flexibleColWidth" class="pl-0 pr-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Hasil / KPI</th>
                        <th v-if="showRegulationColumn" :class="flexibleColWidth" class="pl-0 pr-4 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Daftar STK</th>
                        <th class="px-4 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500 w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr
                        v-for="item in visibleRows"
                        :key="'pb2-' + item.id"
                        class="group transition duration-150 hover:bg-slate-50/50 dark:hover:bg-white/5 animate-fade-in"
                    >
                        <td class="px-0 py-2 text-slate-600 dark:text-slate-300 text-xs whitespace-normal break-words max-w-[64px]">
                            {{ item.depth === 0 ? (item.company?.name || '-') : '' }}
                        </td>
                        <td class="px-1 py-2 text-slate-600 dark:text-slate-300 text-xs w-6">
                            {{ item.order !== null && item.order !== undefined ? item.order : '-' }}
                        </td>
                        <td 
                            :class="nameColWidth"
                            class="pl-0 pr-4 py-2 text-slate-900 dark:text-white text-xs break-words font-medium" 
                            :style="{ paddingLeft: (item.depth * 16 + 8) + 'px' }"
                        >
                            <div class="flex items-center gap-2">
                                <!-- Toggle Button / Branch Spacer -->
                                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                    <button 
                                        v-if="item.hasChildren" 
                                        @click.stop="toggleExpand(item.id)" 
                                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none shrink-0 flex items-center justify-center"
                                    >
                                        <svg v-if="item.isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                    <span v-else-if="item.depth > 0" class="text-slate-300 dark:text-white/20 font-mono text-xs select-none">├─</span>
                                </div>
                                
                                <span>
                                    {{ item.name }}
                                </span>
                            </div>
                        </td>
                        <td v-if="showDescriptionColumn" :class="flexibleColWidth" class="px-0 py-2 text-slate-500 dark:text-slate-400 text-xs whitespace-pre-wrap break-words">
                            {{ item.deskripsi || '-' }}
                        </td>
                        <td v-if="showKpiColumn" :class="flexibleColWidth" class="pl-0 pr-4 py-2 text-slate-600 dark:text-slate-300 text-xs">
                            <!-- Display mapped KPIs as ordered list with numbering -->
                            <ol v-if="item.kpis && item.kpis.length > 0" class="space-y-1.5 list-none">
                                <li 
                                    v-for="(kpi, kpiIndex) in item.kpis" 
                                    :key="'item-kpi-' + kpi.id"
                                    class="flex items-start gap-1 whitespace-pre-wrap leading-relaxed text-[11px]"
                                >
                                    <span class="shrink-0 select-none font-mono text-slate-400 dark:text-slate-500">{{ kpiIndex + 1 }}.</span>
                                    <span>{{ kpi.deskripsi }}</span>
                                </li>
                            </ol>
                            <span v-else class="text-slate-400 dark:text-slate-600 font-mono text-xs select-none">-</span>
                        </td>
                        <td v-if="showRegulationColumn" :class="flexibleColWidth" class="pl-0 pr-4 py-2 text-slate-600 dark:text-slate-300 text-xs">
                            <ul v-if="item.regulations && item.regulations.length > 0" class="space-y-1.5 list-none">
                                <li 
                                    v-for="reg in item.regulations" 
                                    :key="'item-reg-' + reg.id"
                                    class="flex items-start gap-1 whitespace-pre-wrap leading-relaxed text-[11px]"
                                >
                                    <span class="shrink-0 select-none">-</span>
                                    <span class="flex flex-col items-start text-left">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <Link 
                                                :href="route('itom.policy.regulation.procedure.index', { regulation_id: reg.id })" 
                                                class="font-semibold text-slate-900 dark:text-white hover:underline hover:text-[#821f44] dark:hover:text-[#db588c]"
                                            >
                                                {{ reg.judul }}
                                            </Link>
                                            <span v-if="reg.status" :class="getStatusBadgeClass(reg.status)">
                                                {{ reg.status }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">{{ reg.nomor }}</span>
                                        
                                        <!-- Category List -->
                                        <ul v-if="reg.sop_categories && reg.sop_categories.length > 0" class="mt-1 pl-2 space-y-0.5">
                                            <li 
                                                v-for="cat in reg.sop_categories" 
                                                :key="'cat-' + cat.id"
                                                class="text-[10px] text-black dark:text-slate-300 list-none flex items-start gap-1"
                                            >
                                                <span class="shrink-0 select-none">-</span>
                                                <Link 
                                                    :href="route('itom.policy.regulation.procedure.index', { regulation_id: reg.id }) + '#sop-cat-' + cat.id"
                                                    class="hover:underline hover:text-[#821f44] dark:hover:text-[#db588c]"
                                                >
                                                    {{ cat.tipe }}
                                                </Link>
                                            </li>
                                        </ul>
                                    </span>
                                </li>
                            </ul>
                            <span v-else class="text-slate-400 dark:text-slate-600 font-mono text-xs select-none">-</span>
                        </td>
                        <td class="px-4 py-2 text-center print:hidden">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditModal(item)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="openDeleteModal(item)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="visibleRows.length === 0">
                        <td :colspan="4 + (showDescriptionColumn ? 1 : 0) + (showKpiColumn ? 1 : 0) + (showRegulationColumn ? 1 : 0)" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                            Data Proses Bisnis tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah Proses Bisnis v2' : 'Edit Proses Bisnis v2'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan proses bisnis baru.' : 'Silakan sesuaikan data proses bisnis di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        max-width="2xl"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 text-left">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- Left Column: Info Utama -->
                <div class="space-y-4">
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

                    <!-- Parent Select -->
                    <div class="flex flex-col gap-1.5">
                        <label for="parent_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Proses Bisnis Parent</label>
                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        >
                            <option value="">Tanpa Parent (Root / Level 1)</option>
                            <option v-for="item in filteredParentOptions" :key="item.id" :value="item.id">
                                {{ getLevelPrefix(item) }}{{ item.name }}
                            </option>
                        </select>
                        <span v-if="form.errors.parent_id" class="text-xs text-red-500 font-medium">{{ form.errors.parent_id }}</span>
                    </div>

                    <!-- Order Input -->
                    <div class="flex flex-col gap-1.5">
                        <label for="order" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Urutan (Order)</label>
                        <input
                            id="order"
                            v-model="form.order"
                            type="number"
                            min="0"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            placeholder="Contoh: 1"
                        />
                        <span v-if="form.errors.order" class="text-xs text-red-500 font-medium">{{ form.errors.order }}</span>
                    </div>

                    <!-- Name Input -->
                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Proses Bisnis</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            placeholder="Contoh: Perencanaan Bisnis Strategis"
                            required
                        />
                        <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
                    </div>

                    <!-- Deskripsi Input -->
                    <div class="flex flex-col gap-1.5">
                        <label for="deskripsi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi</label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="3"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            placeholder="Tulis deskripsi atau tujuan proses bisnis di sini..."
                        ></textarea>
                        <span v-if="form.errors.deskripsi" class="text-xs text-red-500 font-medium">{{ form.errors.deskripsi }}</span>
                    </div>
                </div>

                <!-- Right Column: KPI Mapping -->
                <div class="space-y-4">
                    <!-- Mapping KPI -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Mapping KPI</label>
                        <div class="relative">
                            <button 
                                type="button"
                                @click="toggleKpiDropdown"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-left focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white flex justify-between items-center"
                            >
                                <span class="truncate text-slate-400 dark:text-slate-500">
                                    -- Pilih KPI --
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <!-- Dropdown Content (Relative positioning to naturally expand container height) -->
                            <div v-if="isKpiDropdownOpen" class="relative mt-1 bg-slate-50 border border-slate-200 rounded-lg shadow-inner dark:bg-black/20 dark:border-white/10 z-10 max-h-60 overflow-y-auto p-2 space-y-2 w-full">
                                <!-- Search input inside dropdown -->
                                <div class="sticky top-0 bg-slate-50 dark:bg-[#1a1a1a] pb-1.5">
                                    <input 
                                        type="text" 
                                        v-model="kpiSearchQuery" 
                                        placeholder="Cari KPI..." 
                                        class="w-full bg-white text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                        ref="kpiSearchInput"
                                        @click.stop
                                    />
                                </div>
                                
                                <!-- Options list -->
                                <div class="space-y-0.5">
                                    <button
                                        v-for="kpi in filteredFormKpis" 
                                        :key="kpi.id"
                                        type="button"
                                        @click="toggleKpi(kpi.id)"
                                        :class="[
                                            'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                            form.kpi_ids.includes(kpi.id) ? 'bg-slate-100 text-slate-900 dark:bg-white/10 dark:text-white font-semibold' : 'text-slate-700 dark:text-slate-300'
                                        ]"
                                    >
                                        <span class="whitespace-normal break-words pr-2 leading-relaxed">
                                            {{ kpi.deskripsi }}
                                        </span>
                                        <svg v-if="form.kpi_ids.includes(kpi.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-900 dark:text-white shrink-0">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div v-if="filteredFormKpis.length === 0" class="text-center py-4 text-xs text-slate-400">
                                        Tidak ada hasil ditemukan.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selected list display (restricted height with scroll to prevent modal scrolling) -->
                        <div v-if="form.kpi_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5 max-h-[220px] overflow-y-auto pr-1 pb-1">
                            <span 
                                v-for="id in form.kpi_ids" 
                                :key="id"
                                class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                            >
                                <span class="whitespace-normal break-words font-medium text-slate-800 dark:text-slate-200 leading-normal">
                                    {{ getKpiLabel(id) }}
                                </span>
                                <button 
                                    type="button" 
                                    @click="removeKpiId(id)"
                                    class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </span>
                        </div>
                        <span v-if="form.errors.kpi_ids" class="text-xs text-red-500 font-medium">{{ form.errors.kpi_ids }}</span>
                    </div>

                    <!-- Mapping Regulation -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Mapping Regulasi</label>
                        <div class="relative">
                            <button 
                                type="button"
                                @click="toggleRegulationDropdown"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-left focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white flex justify-between items-center"
                            >
                                <span class="truncate text-slate-400 dark:text-slate-500">
                                    -- Pilih Regulasi --
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <!-- Dropdown Content (Relative positioning to naturally expand container height) -->
                            <div v-if="isRegulationDropdownOpen" class="relative mt-1 bg-slate-50 border border-slate-200 rounded-lg shadow-inner dark:bg-black/20 dark:border-white/10 z-10 max-h-60 overflow-y-auto p-2 space-y-2 w-full">
                                <!-- Search input inside dropdown -->
                                <div class="sticky top-0 bg-slate-50 dark:bg-[#1a1a1a] pb-1.5">
                                    <input 
                                        type="text" 
                                        v-model="regulationSearchQuery" 
                                        placeholder="Cari Regulasi..." 
                                        class="w-full bg-white text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                        <span class="whitespace-normal break-words pr-2 leading-relaxed flex flex-col items-start text-left">
                                            <span class="font-semibold">{{ reg.judul }}</span>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">{{ reg.nomor }}</span>
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
                        </div>

                        <!-- Selected list display -->
                        <div v-if="form.regulation_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5 max-h-[220px] overflow-y-auto pr-1 pb-1">
                            <span 
                                v-for="id in form.regulation_ids" 
                                :key="id"
                                class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                            >
                                <span class="whitespace-normal break-words font-medium text-slate-800 dark:text-slate-200 leading-normal flex flex-col items-start text-left">
                                    <span class="font-semibold">{{ getRegulationTitle(id) }}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">{{ getRegulationNomor(id) }}</span>
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
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus Proses Bisnis v2"
        :message="`Apakah Anda yakin ingin menghapus proses bisnis '${selectedItem?.name}'? Tindakan ini tidak dapat dibatalkan.`"
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
import { useForm, Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    prosesBisnisV2: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    kpiList: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});

const showKpiColumn = ref(true);
const showDescriptionColumn = ref(true);
const showRegulationColumn = ref(true);

// Equal-proportion width for flexible columns based on how many are visible
const flexibleColWidth = computed(() => {
    const count = (showDescriptionColumn.value ? 1 : 0)
        + (showKpiColumn.value ? 1 : 0)
        + (showRegulationColumn.value ? 1 : 0);
    if (count === 1) return 'w-auto';
    if (count === 2) return 'w-1/2';
    return 'w-1/3';
});

const nameColWidth = computed(() => {
    const count = (showDescriptionColumn.value ? 1 : 0)
        + (showKpiColumn.value ? 1 : 0)
        + (showRegulationColumn.value ? 1 : 0);
    if (count === 0) return 'w-auto';
    if (count === 1) return 'w-[30%]';
    if (count === 2) return 'w-[25%]';
    return 'w-[20%]';
});

const getStatusBadgeClass = (status) => {
    const base = 'inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[8px] font-bold tracking-wide uppercase border shrink-0';
    if (!status) return `${base} bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400`;
    
    const formatted = status.toLowerCase().trim();
    if (formatted === 'aktif' || formatted === 'active' || formatted === 'berlaku') {
        return `${base} bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400`;
    }
    if (formatted === 'draft' || formatted === 'draft usulan' || formatted === 'draft dicabut') {
        return `${base} bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400`;
    }
    if (formatted === 'dicabut' || formatted === 'revisi' || formatted === 'expired' || formatted === 'tidak berlaku') {
        return `${base} bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400`;
    }
    return `${base} bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-500/10 dark:border-slate-500/20 dark:text-slate-400`;
};

const itemsMap = computed(() => new Map(props.prosesBisnisV2.map(item => [item.id, item])));

const availableCompanies = computed(() => {
    const compMap = new Map();
    props.prosesBisnisV2.forEach(item => {
        if (item.company) {
            compMap.set(item.company.id, item.company);
        }
    });
    return Array.from(compMap.values()).sort((a, b) => (a.name || '').localeCompare(b.name || ''));
});

const getLevelPrefix = (item) => {
    const depth = item.depth !== undefined ? item.depth : 0;
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const getParentLabel = (parentId) => {
    if (!parentId) return '-';
    const parent = itemsMap.value.get(parentId);
    return parent ? parent.name : '-';
};

const isDescendant = (id, targetId) => {
    if (!id || !targetId) return false;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = itemsMap.value.get(currentId);
        if (node?.parent_id) {
            if (Number(node.parent_id) === Number(targetId)) return true;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return false;
};

// ─── Hierarchy tree mapping ───
const treeData = computed(() => {
    const map = {};
    const roots = [];

    props.prosesBisnisV2.forEach(item => {
        map[item.id] = { ...item, children: [] };
    });

    props.prosesBisnisV2.forEach(item => {
        const mapped = map[item.id];
        if (item.parent_id && map[item.parent_id]) {
            map[item.parent_id].children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sort = (nodes) => {
        nodes.sort((a, b) => {
            const orderA = a.order !== null && a.order !== undefined ? Number(a.order) : null;
            const orderB = b.order !== null && b.order !== undefined ? Number(b.order) : null;
            
            if (orderA !== null && orderB !== null) {
                if (orderA !== orderB) {
                    return orderA - orderB;
                }
            } else if (orderA !== null) {
                return -1;
            } else if (orderB !== null) {
                return 1;
            }
            return (a.name || '').localeCompare(b.name || '');
        });
        nodes.forEach(n => sort(n.children));
    };
    sort(roots);

    return roots;
});

// ─── Expand / Collapse State ───
const expandedIds = ref(new Set());
const expandLevel = ref('all');

const maxDepth = computed(() => {
    let max = 0;
    props.prosesBisnisV2.forEach(item => {
        const d = item.depth !== undefined ? item.depth : 0;
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
        props.prosesBisnisV2.forEach(item => {
            const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
            if (isParent) {
                ids.add(item.id);
            }
        });
    } else {
        const targetDepth = parseInt(val, 10);
        if (targetDepth > 0) {
            props.prosesBisnisV2.forEach(item => {
                const depth = item.depth !== undefined ? item.depth : 0;
                if (depth < targetDepth) {
                    const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
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
    props.prosesBisnisV2.forEach(item => {
        const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
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
    () => props.prosesBisnisV2,
    (newVal) => {
        initializeExpanded();
        if (parentFilterId.value && !newVal.some(item => Number(item.id) === Number(parentFilterId.value))) {
            parentFilterId.value = '';
        }
    },
    { deep: false }
);

// ─── Search / Filter ───
const searchQuery = ref('');
const companyFilterId = ref('');
const parentFilterId = ref('');

const matchesSearch = (node) => {
    if (!searchQuery.value) return true;
    const q = searchQuery.value.toLowerCase().trim();
    const matchesName = (node.name || '').toLowerCase().includes(q);
    const matchesDesc = (node.deskripsi || '').toLowerCase().includes(q);
    return matchesName || matchesDesc;
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
        props.prosesBisnisV2.forEach(item => {
            const isParent = props.prosesBisnisV2.some(r => r.parent_id === item.id);
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
    let roots = treeData.value;

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
    
    return rows;
});

// Flatten tree for dropdown select
const parentFilterOptions = computed(() => {
    const result = [];
    const traverse = (node, depth = 0) => {
        result.push({ ...node, depth });
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => traverse(child, depth + 1));
        }
    };
    
    let roots = treeData.value;
    if (companyFilterId.value) {
        const compId = Number(companyFilterId.value);
        roots = roots.filter(root => Number(root.company_id) === compId);
    }

    roots.forEach(root => traverse(root, 0));
    return result;
});

watch(companyFilterId, (newCompanyFilterId) => {
    if (parentFilterId.value) {
        const selectedParent = props.prosesBisnisV2.find(item => Number(item.id) === Number(parentFilterId.value));
        if (selectedParent && newCompanyFilterId && Number(selectedParent.company_id) !== Number(newCompanyFilterId)) {
            parentFilterId.value = '';
        }
    }
});

// ─── Modal state & form ───
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedItem = ref(null);
const modalMode = ref('create');

const form = useForm({
    company_id: '',
    parent_id: '',
    name: '',
    deskripsi: '',
    order: '',
    kpi_ids: [],
    regulation_ids: [],
});

const isKpiDropdownOpen = ref(false);
const kpiSearchQuery = ref('');
const kpiSearchInput = ref(null);

const toggleKpiDropdown = () => {
    isKpiDropdownOpen.value = !isKpiDropdownOpen.value;
    if (isKpiDropdownOpen.value) {
        kpiSearchQuery.value = '';
        setTimeout(() => {
            kpiSearchInput.value?.focus();
        }, 100);
    }
};

const toggleKpi = (id) => {
    const index = form.kpi_ids.indexOf(id);
    if (index > -1) {
        form.kpi_ids.splice(index, 1);
    } else {
        form.kpi_ids.push(id);
    }
};

const removeKpiId = (id) => {
    const index = form.kpi_ids.indexOf(id);
    if (index > -1) {
        form.kpi_ids.splice(index, 1);
    }
};

const filteredFormKpis = computed(() => {
    const query = kpiSearchQuery.value.toLowerCase().trim();
    if (!query) return props.kpiList;
    return props.kpiList.filter(kpi =>
        (kpi.deskripsi || '').toLowerCase().includes(query)
    );
});

const getKpiLabel = (id) => {
    const kpi = props.kpiList.find(k => k.id === Number(id));
    return kpi ? kpi.deskripsi : '-';
};

const isRegulationDropdownOpen = ref(false);
const regulationSearchQuery = ref('');
const regulationSearchInput = ref(null);

const toggleRegulationDropdown = () => {
    isRegulationDropdownOpen.value = !isRegulationDropdownOpen.value;
    if (isRegulationDropdownOpen.value) {
        regulationSearchQuery.value = '';
        setTimeout(() => {
            regulationSearchInput.value?.focus();
        }, 100);
    }
};

const toggleRegulation = (id) => {
    const index = form.regulation_ids.indexOf(id);
    if (index > -1) {
        form.regulation_ids.splice(index, 1);
    } else {
        form.regulation_ids.push(id);
    }
};

const removeRegulationId = (id) => {
    const index = form.regulation_ids.indexOf(id);
    if (index > -1) {
        form.regulation_ids.splice(index, 1);
    }
};

const filteredFormRegulations = computed(() => {
    const query = regulationSearchQuery.value.toLowerCase().trim();
    if (!query) return props.regulations;
    return props.regulations.filter(reg =>
        (reg.judul || '').toLowerCase().includes(query) ||
        (reg.nomor || '').toLowerCase().includes(query)
    );
});

const getRegulationLabel = (id) => {
    const reg = props.regulations.find(r => r.id === Number(id));
    return reg ? `${reg.judul} (${reg.nomor})` : '-';
};

const getRegulationTitle = (id) => {
    const reg = props.regulations.find(r => r.id === Number(id));
    return reg ? reg.judul : '-';
};

const getRegulationNomor = (id) => {
    const reg = props.regulations.find(r => r.id === Number(id));
    return reg ? reg.nomor : '';
};

const filteredParentOptions = computed(() => {
    const targetCompanyId = form.company_id ? String(form.company_id) : null;
    const items = targetCompanyId
        ? parentFilterOptions.value.filter(item => String(item.company_id ?? '') === targetCompanyId)
        : parentFilterOptions.value;

    return items.filter(item => {
        if (modalMode.value === 'edit' && selectedItem.value) {
            const currentId = selectedItem.value.id;
            if (Number(item.id) === Number(currentId)) return false;
            if (isDescendant(item.id, currentId)) return false;
        }
        return true;
    });
});

// Watch company_id to clear parent_id if company changes and is no longer matching
watch(() => form.company_id, (newCompanyId) => {
    if (form.parent_id) {
        const parent = props.prosesBisnisV2.find(item => String(item.id) === String(form.parent_id));
        if (parent && String(parent.company_id) !== String(newCompanyId)) {
            form.parent_id = '';
        }
    }
});

// Watch parent_id to set company_id to parent's company_id if selected parent belongs to a company
watch(() => form.parent_id, (newParentId) => {
    if (newParentId) {
        const parent = props.prosesBisnisV2.find(item => String(item.id) === String(newParentId));
        if (parent && parent.company_id && String(form.company_id) !== String(parent.company_id)) {
            form.company_id = String(parent.company_id);
        }
    }
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    form.kpi_ids = [];
    form.regulation_ids = [];
    isKpiDropdownOpen.value = false;
    isRegulationDropdownOpen.value = false;
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    modalMode.value = 'edit';
    selectedItem.value = item;
    form.clearErrors();
    form.company_id = String(item.company_id ?? '');
    form.parent_id = item.parent_id ? String(item.parent_id) : '';
    form.name = item.name || '';
    form.deskripsi = item.deskripsi || '';
    form.order = item.order !== null && item.order !== undefined ? String(item.order) : '';
    form.kpi_ids = item.kpis ? item.kpis.map(k => k.id) : [];
    form.regulation_ids = item.regulations ? item.regulations.map(r => r.id) : [];
    isKpiDropdownOpen.value = false;
    isRegulationDropdownOpen.value = false;
    isModalOpen.value = true;
};

const openDeleteModal = (item) => {
    selectedItem.value = item;
    isDeleteModalOpen.value = true;
};

const submitForm = () => {
    const payload = {
        company_id: form.company_id ? Number(form.company_id) : null,
        name: form.name,
        deskripsi: form.deskripsi,
        parent_id: form.parent_id ? Number(form.parent_id) : null,
        order: form.order !== '' && form.order !== null && form.order !== undefined ? Number(form.order) : null,
        kpi_ids: form.kpi_ids,
        regulation_ids: form.regulation_ids,
    };

    if (modalMode.value === 'create') {
        form.transform(() => payload).post(route('itom.business-process.business-process-v2.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.transform(() => payload).put(route('itom.business-process.business-process-v2.update', selectedItem.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('itom.business-process.business-process-v2.destroy', selectedItem.value.id), {
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
