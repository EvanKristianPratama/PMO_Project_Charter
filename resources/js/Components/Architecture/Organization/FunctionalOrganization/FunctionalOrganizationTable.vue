<template>
    <div class="space-y-4">
        <!-- Functional Organization Main Card -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Header Section containing Filters and Add Button -->
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
                <!-- Filters & Search -->
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Search by Query -->
                    <div class="w-full sm:w-48 flex flex-col gap-1.5">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama atau SK..."
                                class="w-full rounded-lg border border-slate-300 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            />
                            <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Company Filter -->
                    <div class="w-full sm:w-40 flex flex-col gap-1.5">
                        <select
                            v-model="selectedCompanyId"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        >
                            <option value="">Semua Company</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">
                                {{ company.name }}
                            </option>
                        </select>
                    </div>

                    <!-- SK Filter -->
                    <div class="w-full sm:w-40 flex flex-col gap-1.5">
                        <select
                            v-model="selectedSkId"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        >
                            <option value="">Semua SK</option>
                            <option v-for="sk in skOrganizations" :key="sk.id" :value="sk.id">
                                {{ sk.sk }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Add Button -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-950 dark:focus:ring-white dark:focus:ring-offset-[#171717]"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Functional Organization
                    </button>
                </div>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-[11px] dark:divide-white/10 table-auto min-w-[900px]">
                    <thead class="bg-slate-50 dark:bg-white/5">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-10 text-center">No</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-36">Company</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-44">Nama Organisasi</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">SK Organisasi</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-48">Fungsi (Struktur Internal)</th>
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-56">Anggota</th>
                            <th class="px-3 py-2.5 text-center text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-40">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        <tr
                            v-for="(row, idx) in filteredRows"
                            :key="row.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-white/5"
                        >
                            <td class="px-3 py-2 text-slate-500 dark:text-slate-400 w-10 text-center align-top">{{ idx + 1 }}</td>
                            <td class="px-3 py-2 text-slate-600 dark:text-slate-300 w-36 font-medium align-top">
                                <div class="truncate max-w-[9rem]" :title="row.company_name">{{ row.company_name || '-' }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-900 dark:text-white font-medium w-44 align-top">
                                <div class="truncate max-w-[11rem]" :title="row.name">{{ row.name }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-600 dark:text-slate-300 w-40 align-top">
                                <div class="truncate max-w-[10rem]" :title="row.sk_name">{{ row.sk_name || '-' }}</div>
                            </td>
                            <td class="px-3 py-2 w-48 align-top">
                                <!-- Fungsi: tree structure (root + indented children) -->
                                <div v-if="row.functions && row.functions.length > 0" class="space-y-1">
                                    <template v-for="fun in buildTree(row.functions)" :key="fun.structure_id">
                                        <!-- Root level -->
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20">
                                                {{ fun.name }}
                                            </span>
                                        </div>
                                        <!-- Children (level 1) -->
                                        <div v-if="fun.children && fun.children.length > 0" class="pl-3 space-y-0.5 border-l border-slate-200 dark:border-white/10 ml-1.5">
                                            <div v-for="child in fun.children" :key="child.structure_id" class="flex items-center gap-1">
                                                <span class="text-[9px] text-slate-400 dark:text-slate-500">└</span>
                                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 ring-1 ring-inset ring-slate-200 dark:bg-white/5 dark:text-slate-300 dark:ring-white/10">
                                                    {{ child.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <span v-else class="text-slate-400 dark:text-slate-500">—</span>
                            </td>
                            <td class="px-3 py-2 w-56 align-top">
                                <!-- Anggota: grouped by fungsi (tree) -->
                                <div v-if="row.functions && row.functions.length > 0" class="space-y-2">
                                    <template v-for="fun in buildTree(row.functions)" :key="fun.structure_id">
                                        <!-- Root fungsi -->
                                        <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-2 dark:border-white/5 dark:bg-white/5">
                                            <div class="text-[9px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1.5 flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                {{ fun.name }}
                                            </div>
                                            <!-- Members of root -->
                                            <div v-if="getMembersForStructure(row.members, fun.structure_id).length > 0" class="space-y-1 pl-1 border-l border-slate-200 dark:border-white/10">
                                                <div v-for="member in getMembersForStructure(row.members, fun.structure_id)" :key="member.organization_id" class="text-[10px]">
                                                    <div class="font-semibold text-slate-900 dark:text-white">{{ member.name }}</div>
                                                    <div v-if="member.pejabat" class="text-[9px] text-slate-500 dark:text-slate-400 font-mono">Pejabat: {{ member.pejabat }}</div>
                                                </div>
                                            </div>
                                            <div v-else class="text-[9px] text-slate-400 dark:text-slate-500 italic pl-1">Belum ada anggota</div>
                                            <!-- Children fungsi -->
                                            <div v-if="fun.children && fun.children.length > 0" class="mt-2 pl-3 space-y-2 border-l-2 border-blue-100 dark:border-blue-500/20">
                                                <div v-for="child in fun.children" :key="child.structure_id" class="rounded-lg border border-slate-100 bg-white p-1.5 dark:border-white/5 dark:bg-[#1a1a1a]">
                                                    <div class="text-[9px] font-semibold text-slate-600 dark:text-slate-400 flex items-center gap-1 mb-1">
                                                        <span class="text-slate-300 dark:text-slate-600">└</span>
                                                        {{ child.name }}
                                                    </div>
                                                    <div v-if="getMembersForStructure(row.members, child.structure_id).length > 0" class="space-y-0.5 pl-1 border-l border-slate-100 dark:border-white/5">
                                                        <div v-for="member in getMembersForStructure(row.members, child.structure_id)" :key="member.organization_id">
                                                            <div class="text-[9px] font-semibold text-slate-800 dark:text-slate-200">{{ member.name }}</div>
                                                            <div v-if="member.pejabat" class="text-[9px] text-slate-400 font-mono">{{ member.pejabat }}</div>
                                                        </div>
                                                    </div>
                                                    <div v-else class="text-[9px] text-slate-400 italic pl-1">—</div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <span v-else class="text-slate-400 dark:text-slate-500">—</span>
                            </td>
                            <td class="px-3 py-2 text-center w-40 align-top">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        @click="openFunctionModal(row)"
                                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[10px] font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 transition"
                                    >
                                        <svg class="h-3 w-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kelola Fungsi
                                    </button>
                                    <button
                                        @click="openEditModal(row)"
                                        class="inline-flex items-center justify-center rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white transition"
                                        title="Edit Organisasi"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="openDeleteModal(row)"
                                        class="inline-flex items-center justify-center rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition"
                                        title="Hapus Organisasi"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredRows.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-xs text-slate-500 dark:text-slate-400">
                                Organisasi Fungsional Tidak Ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Create & Edit Modal -->
    <ConfirmationModal
        :show="isModalOpen"
        :title="modalMode === 'create' ? 'Tambah Organisasi Fungsional' : 'Edit Organisasi Fungsional'"
        :message="modalMode === 'create' ? 'Silakan isi formulir di bawah ini untuk menambahkan organisasi fungsional baru.' : 'Silakan sesuaikan data organisasi fungsional di bawah ini.'"
        confirm-text="Simpan"
        cancel-text="Batal"
        type="info"
        :loading="form.processing"
        @close="isModalOpen = false"
        @confirm="submitForm"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Company Select -->
            <div class="flex flex-col gap-1.5">
                <label for="company_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Company</label>
                <select
                    id="company_id"
                    v-model="form.company_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih Company...</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
                    </option>
                </select>
                <span v-if="form.errors.company_id" class="text-xs text-red-500 font-medium">{{ form.errors.company_id }}</span>
            </div>

            <!-- Name Input -->
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Organisasi</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    placeholder="Contoh: Divisi Keuangan fungsional"
                    required
                />
                <span v-if="form.errors.name" class="text-xs text-red-500 font-medium">{{ form.errors.name }}</span>
            </div>

            <!-- SK Select -->
            <div class="flex flex-col gap-1.5">
                <label for="sk_id" class="text-xs font-semibold text-slate-700 dark:text-slate-300">SK Organisasi</label>
                <select
                    id="sk_id"
                    v-model="form.sk_id"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    required
                >
                    <option value="" disabled>Pilih SK...</option>
                    <option v-for="sk in skOrganizations" :key="sk.id" :value="sk.id">
                        {{ sk.sk }}
                    </option>
                </select>
                <span v-if="form.errors.sk_id" class="text-xs text-red-500 font-medium">{{ form.errors.sk_id }}</span>
            </div>

        </div>
    </ConfirmationModal>

    <!-- Kelola Fungsi Modal -->
    <ConfirmationModal
        :show="isFunctionModalOpen"
        :title="`Kelola Fungsi - ${selectedFunctional?.name}`"
        message="Berikut adalah daftar fungsi (struktur internal) yang dikaitkan dengan organisasi fungsional ini."
        confirm-text="Tutup"
        cancel-text=""
        type="info"
        :loading="functionForm.processing"
        max-width="lg"
        @close="isFunctionModalOpen = false"
        @confirm="isFunctionModalOpen = false"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Add Function Form -->
            <form @submit.prevent="submitAddFunction" class="flex flex-col gap-3 p-3 bg-slate-50 rounded-xl dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Tambah Fungsi Baru</h4>
                <div class="flex flex-col gap-1.5">
                    <label for="function_name_input" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Nama Fungsi / Struktur Internal</label>
                    <div class="flex gap-2">
                        <input
                            id="function_name_input"
                            v-model="functionForm.name"
                            type="text"
                            placeholder="Contoh: Ketua, Sekretaris, Anggota..."
                            class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            required
                        />
                        <button
                            type="submit"
                            :disabled="functionForm.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                        >
                            {{ functionForm.processing ? 'Adding...' : 'Tambah' }}
                        </button>
                    </div>
                    <span v-if="functionForm.errors.name" class="text-[10px] text-red-500 font-medium">{{ functionForm.errors.name }}</span>
                </div>
                <!-- Parent Selector -->
                <div class="flex flex-col gap-1">
                    <label for="function_parent_input" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Parent Fungsi <span class="font-normal text-slate-400">(opsional)</span></label>
                    <select
                        id="function_parent_input"
                        v-model="functionForm.parent_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    >
                        <option :value="null">— Tanpa Parent (Root) —</option>
                        <option
                            v-for="fun in selectedFunctional?.functions ?? []"
                            :key="fun.structure_id"
                            :value="fun.structure_id"
                        >
                            {{ fun.name }}
                        </option>
                    </select>
                    <span v-if="functionForm.errors.parent_id" class="text-[10px] text-red-500 font-medium">{{ functionForm.errors.parent_id }}</span>
                </div>
            </form>

            <!-- List of Current Functions (tree) -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Fungsi Saat Ini</h4>
                <div v-if="selectedFunctional?.functions && selectedFunctional.functions.length > 0" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    <template v-for="fun in buildTree(selectedFunctional?.functions ?? [])" :key="fun.structure_id">
                        <!-- Root item -->
                        <div class="rounded-xl border border-slate-100 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a] transition hover:shadow-md">
                            <div class="flex items-center justify-between px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                    <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ fun.name }}</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 italic">root</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="openMemberModalForStructure(fun)"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 px-2.5 py-1 text-xs font-semibold shadow-sm transition dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Anggota
                                    </button>
                                    <button type="button" @click="deleteFunction(fun.structure_id)"
                                        class="inline-flex items-center justify-center rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 transition"
                                        title="Hapus Fungsi">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Children -->
                            <div v-if="fun.children && fun.children.length > 0" class="border-t border-slate-100 dark:border-white/5 divide-y divide-slate-50 dark:divide-white/5">
                                <div v-for="child in fun.children" :key="child.structure_id"
                                    class="flex items-center justify-between px-3 py-2 pl-7 bg-slate-50/50 dark:bg-white/[0.02]">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-300 dark:text-slate-600 text-xs">└</span>
                                        <span class="flex h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        <span class="text-xs text-slate-700 dark:text-slate-300">{{ child.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openMemberModalForStructure(child)"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 px-2.5 py-1 text-xs font-semibold shadow-sm transition dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            Anggota
                                        </button>
                                        <button type="button" @click="deleteFunction(child.structure_id)"
                                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 transition"
                                            title="Hapus Sub-Fungsi">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div v-else class="text-xs text-slate-500 dark:text-slate-400 py-6 text-center border border-dashed border-slate-200 rounded-xl dark:border-white/10">
                    Belum ada fungsi. Silakan ketik nama fungsi di atas untuk menambahkan.
                </div>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Kelola Anggota Modal -->
    <ConfirmationModal
        :show="isMemberModalOpen"
        :title="`Kelola Anggota - ${selectedStructure?.name} (${selectedFunctional?.name})`"
        message="Berikut adalah daftar anggota (jabatan) yang tergabung dalam fungsi ini."
        confirm-text="Tutup"
        cancel-text=""
        type="info"
        :loading="memberForm.processing"
        max-width="lg"
        @close="isMemberModalOpen = false"
        @confirm="isMemberModalOpen = false"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Add Member Form -->
            <form @submit.prevent="submitAddMember" class="flex flex-col gap-3 p-3 bg-slate-50 rounded-xl dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Tambah Anggota Baru</h4>
                <div class="flex flex-col gap-1.5">
                    <label for="member_organization_id" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Pilih Jabatan (BOD)</label>
                    <div class="flex gap-2">
                        <select
                            id="member_organization_id"
                            v-model="memberForm.organization_id"
                            class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                            required
                        >
                            <option value="" disabled>Pilih Jabatan...</option>
                            <option v-for="bod in availableBodsForCompany" :key="bod.id" :value="bod.id">
                                {{ bod.name }} {{ bod.pejabat ? '(' + bod.pejabat + ')' : '' }}
                            </option>
                        </select>
                        <button
                            type="submit"
                            :disabled="memberForm.processing"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                        >
                            {{ memberForm.processing ? 'Adding...' : 'Tambah' }}
                        </button>
                    </div>
                    <span v-if="memberForm.errors.organization_id" class="text-[10px] text-red-500 font-medium">{{ memberForm.errors.organization_id }}</span>
                </div>
            </form>

            <!-- List of Current Members -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Anggota Saat Ini</h4>
                <div v-if="currentMembersForStructure.length > 0" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    <div
                        v-for="member in currentMembersForStructure"
                        :key="member.organization_id"
                        class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a] transition hover:shadow-md"
                    >
                        <div>
                            <div class="text-xs font-semibold text-slate-900 dark:text-white">{{ member.name }}</div>
                            <div class="text-[9px] text-slate-500 dark:text-slate-400 font-mono mt-0.5" v-if="member.pejabat">Pejabat: {{ member.pejabat }}</div>
                        </div>
                        <button
                            type="button"
                            @click="deleteMember(member.organization_id)"
                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition"
                            title="Hapus Anggota"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div v-else class="text-xs text-slate-500 dark:text-slate-400 py-6 text-center border border-dashed border-slate-200 rounded-xl dark:border-white/10">
                    Belum ada anggota. Silakan pilih jabatan di atas untuk menambahkan.
                </div>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
        :show="isDeleteModalOpen"
        title="Hapus Organisasi Fungsional"
        :message="`Apakah Anda yakin ingin menghapus organisasi fungsional '${selectedFunctional?.name}'? Tindakan ini tidak dapat dibatalkan.`"
        confirm-text="Hapus"
        cancel-text="Batal"
        type="danger"
        :loading="form.processing"
        @close="isDeleteModalOpen = false"
        @confirm="submitDelete"
    />
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    functionalOrganizations: {
        type: Array,
        default: () => [],
    },
    skOrganizations: {
        type: Array,
        default: () => [],
    },
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
    functions: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');
const selectedCompanyId = ref('');
const selectedSkId = ref('');

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isFunctionModalOpen = ref(false);
const isMemberModalOpen = ref(false);
const selectedFunctional = ref(null);
const selectedStructure = ref(null);
const modalMode = ref('create'); // 'create' or 'edit'

const form = useForm({
    company_id: '',
    name: '',
    sk_id: '',
});

const functionForm = useForm({
    functional_org_id: '',
    name: '',
    parent_id: null,
});

const memberForm = useForm({
    structure_id: '',
    organization_id: '',
});

const currentMembersForStructure = computed(() => {
    if (!selectedFunctional.value || !selectedStructure.value) return [];
    return (selectedFunctional.value.members || []).filter(
        m => Number(m.structure_id) === Number(selectedStructure.value.structure_id)
    );
});

const availableBodsForCompany = computed(() => {
    if (!selectedFunctional.value || !selectedStructure.value) return [];
    const targetCompanyId = selectedFunctional.value.company_id;
    let list = props.bods;
    if (targetCompanyId) {
        list = list.filter(b => String(b.company_id) === String(targetCompanyId));
    }
    const currentMemberIdsForStructure = new Set(
        currentMembersForStructure.value.map(m => Number(m.organization_id))
    );
    return list.filter(b => !currentMemberIdsForStructure.has(Number(b.id)));
});

const filteredRows = computed(() => {
    let rows = props.functionalOrganizations;

    // Filter by Company
    if (selectedCompanyId.value) {
        rows = rows.filter(row => String(row.company_id) === String(selectedCompanyId.value));
    }

    // Filter by SK Organization
    if (selectedSkId.value) {
        rows = rows.filter(row => String(row.sk_id) === String(selectedSkId.value));
    }

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        rows = rows.filter(row => 
            (row.name || '').toLowerCase().includes(query) ||
            (row.company_name || '').toLowerCase().includes(query) ||
            (row.sk_name || '').toLowerCase().includes(query)
        );
    }

    return rows;
});

watch(() => props.functionalOrganizations, (newVal) => {
    if (selectedFunctional.value) {
        const updated = newVal.find(f => f.id === selectedFunctional.value.id);
        if (updated) {
            selectedFunctional.value = updated;
        }
    }
}, { deep: true });

const getFunctionName = (functionsList, structureId) => {
    const fn = (functionsList || []).find(f => Number(f.structure_id) === Number(structureId));
    return fn ? fn.name : '—';
};

const getMembersForStructure = (members, structureId) => {
    return (members || []).filter(m => Number(m.structure_id) === Number(structureId));
};

/**
 * Build a tree from a flat functions array using parent_id.
 * Returns only root nodes (parent_id == null), each with a `children` array.
 */
const buildTree = (functions) => {
    if (!functions || functions.length === 0) return [];
    const map = {};
    const roots = [];
    functions.forEach(fn => {
        map[fn.structure_id] = { ...fn, children: [] };
    });
    functions.forEach(fn => {
        if (fn.parent_id && map[fn.parent_id]) {
            map[fn.parent_id].children.push(map[fn.structure_id]);
        } else {
            roots.push(map[fn.structure_id]);
        }
    });
    return roots;
};

const openCreateModal = () => {
    modalMode.value = 'create';
    form.clearErrors();
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (row) => {
    modalMode.value = 'edit';
    selectedFunctional.value = row;
    form.clearErrors();
    form.company_id = String(row.company_id || '');
    form.name = row.name || '';
    form.sk_id = String(row.sk_id || '');
    isModalOpen.value = true;
};

const openDeleteModal = (row) => {
    selectedFunctional.value = row;
    isDeleteModalOpen.value = true;
};

const openFunctionModal = (row) => {
    selectedFunctional.value = row;
    functionForm.clearErrors();
    functionForm.reset();
    functionForm.functional_org_id = row.id;
    functionForm.parent_id = null;
    isFunctionModalOpen.value = true;
};

const openMemberModalForStructure = (fun) => {
    selectedStructure.value = fun;
    memberForm.clearErrors();
    memberForm.reset();
    memberForm.structure_id = fun.structure_id;
    isMemberModalOpen.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('architecture.organization-structure.functional.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('architecture.organization-structure.functional.update', selectedFunctional.value.id), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitDelete = () => {
    form.delete(route('architecture.organization-structure.functional.destroy', selectedFunctional.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};

const submitAddFunction = () => {
    functionForm.post(route('architecture.organization-structure.functional.structure.store'), {
        onSuccess: () => {
            functionForm.reset('name');
            functionForm.parent_id = null;
            const updatedRow = props.functionalOrganizations.find(f => f.id === selectedFunctional.value.id);
            if (updatedRow) {
                selectedFunctional.value = updatedRow;
            }
        },
    });
};

const deleteFunction = (structureId) => {
    const delFuncForm = useForm({
        structure_id: structureId,
    });
    delFuncForm.delete(route('architecture.organization-structure.functional.structure.destroy'), {
        data: {
            structure_id: structureId,
        },
        onSuccess: () => {
            const updatedRow = props.functionalOrganizations.find(f => f.id === selectedFunctional.value.id);
            if (updatedRow) {
                selectedFunctional.value = updatedRow;
            }
        },
    });
};

const submitAddMember = () => {
    memberForm.post(route('architecture.organization-structure.functional.member.store'), {
        onSuccess: () => {
            memberForm.reset('organization_id');
            const updatedRow = props.functionalOrganizations.find(f => f.id === selectedFunctional.value.id);
            if (updatedRow) {
                selectedFunctional.value = updatedRow;
            }
        },
    });
};

const deleteMember = (orgId) => {
    const delForm = useForm({
        structure_id: selectedStructure.value.structure_id,
        organization_id: orgId,
    });
    delForm.delete(route('architecture.organization-structure.functional.member.destroy'), {
        data: {
            structure_id: selectedStructure.value.structure_id,
            organization_id: orgId,
        },
        onSuccess: () => {
            const updatedRow = props.functionalOrganizations.find(f => f.id === selectedFunctional.value.id);
            if (updatedRow) {
                selectedFunctional.value = updatedRow;
            }
        },
    });
};
</script>
