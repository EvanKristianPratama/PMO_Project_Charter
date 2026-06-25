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
                            <th class="px-3 py-2.5 text-left text-[9px] font-semibold uppercase tracking-wider text-slate-500 w-48">Structure</th>
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
                                <!-- Anggota: grouped by grup_function for AD HOC, else plain list -->
                                <div v-if="row.functions && row.functions.length > 0" class="space-y-1.5">
                                    <template v-for="fun in buildTree(row.functions)" :key="fun.structure_id">
                                        <!-- Root structure label -->
                                        <div class="text-[10px] font-bold text-slate-700 dark:text-slate-200">{{ fun.name }}</div>

                                        <!-- === AD HOC: grouped by grup_function === -->
                                        <template v-if="isAdHocStructure(fun.name)">
                                            <div class="pl-2 space-y-1">
                                                <template v-for="grup in groupMembersByGrupFunction(getMembersForStructure(row.members, fun.structure_id))" :key="grup.label">
                                                    <!-- Group header (collapsible) -->
                                                    <div
                                                        @click="toggleGrupCollapse(fun.structure_id, grup.label)"
                                                        class="flex items-center gap-1 cursor-pointer select-none rounded-md px-1.5 py-0.5 transition hover:bg-slate-100 dark:hover:bg-white/5"
                                                    >
                                                        <svg
                                                            class="h-2.5 w-2.5 shrink-0 text-blue-500 dark:text-blue-400 transition-transform duration-200"
                                                            :class="isGrupCollapsed(fun.structure_id, grup.label) ? '-rotate-90' : 'rotate-0'"
                                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                        <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400">{{ grup.label }}</span>
                                                        <span class="ml-auto text-[9px] text-slate-400 dark:text-slate-500">{{ grup.members.length }}</span>
                                                    </div>
                                                    <!-- Group members (collapsible body) -->
                                                    <div
                                                        v-show="!isGrupCollapsed(fun.structure_id, grup.label)"
                                                        class="pl-4 space-y-0.5"
                                                    >
                                                        <div
                                                            v-for="member in grup.members"
                                                            :key="member.organization_id"
                                                            class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                        >
                                                            <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                            <span class="leading-tight">
                                                                {{ member.name }}
                                                                <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <!-- No members at all -->
                                                <div v-if="getMembersForStructure(row.members, fun.structure_id).length === 0" class="text-[9px] italic text-slate-400 dark:text-slate-500 pl-1">—</div>
                                            </div>
                                        </template>

                                        <!-- === Normal structure: flat indented list === -->
                                        <template v-else>
                                            <div v-if="getMembersForStructure(row.members, fun.structure_id).length > 0" class="pl-3 space-y-0.5">
                                                <div
                                                    v-for="member in getMembersForStructure(row.members, fun.structure_id)"
                                                    :key="member.organization_id"
                                                    class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                >
                                                    <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                    <span class="leading-tight">
                                                        {{ member.name }}
                                                        <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Children structures -->
                                        <template v-if="fun.children && fun.children.length > 0">
                                            <div v-for="child in fun.children" :key="child.structure_id" class="pl-3 space-y-0.5">
                                                <!-- Child label -->
                                                <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                    <span class="text-slate-300 dark:text-slate-600">└</span>
                                                    {{ child.name }}
                                                </div>

                                                <!-- Child: AD HOC grouped -->
                                                <template v-if="isAdHocStructure(child.name)">
                                                    <div class="pl-2 space-y-1">
                                                        <template v-for="grup in groupMembersByGrupFunction(getMembersForStructure(row.members, child.structure_id))" :key="grup.label">
                                                            <div
                                                                @click="toggleGrupCollapse(child.structure_id, grup.label)"
                                                                class="flex items-center gap-1 cursor-pointer select-none rounded-md px-1.5 py-0.5 transition hover:bg-slate-100 dark:hover:bg-white/5"
                                                            >
                                                                <svg
                                                                    class="h-2.5 w-2.5 shrink-0 text-blue-500 dark:text-blue-400 transition-transform duration-200"
                                                                    :class="isGrupCollapsed(child.structure_id, grup.label) ? '-rotate-90' : 'rotate-0'"
                                                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                                                >
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                                <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400">{{ grup.label }}</span>
                                                                <span class="ml-auto text-[9px] text-slate-400 dark:text-slate-500">{{ grup.members.length }}</span>
                                                            </div>
                                                            <div v-show="!isGrupCollapsed(child.structure_id, grup.label)" class="pl-4 space-y-0.5">
                                                                <div
                                                                    v-for="member in grup.members"
                                                                    :key="member.organization_id"
                                                                    class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                                >
                                                                    <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                                    <span class="leading-tight">
                                                                        {{ member.name }}
                                                                        <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <div v-if="getMembersForStructure(row.members, child.structure_id).length === 0" class="text-[9px] italic text-slate-400 dark:text-slate-500 pl-1">—</div>
                                                    </div>
                                                </template>

                                                <!-- Child: normal flat list -->
                                                <template v-else>
                                                    <div v-if="getMembersForStructure(row.members, child.structure_id).length > 0" class="pl-4 space-y-0.5">
                                                        <div
                                                            v-for="member in getMembersForStructure(row.members, child.structure_id)"
                                                            :key="member.organization_id"
                                                            class="flex items-start gap-1 text-[10px] text-slate-600 dark:text-slate-300"
                                                        >
                                                            <span class="shrink-0 text-slate-400 dark:text-slate-500 leading-none mt-px">–</span>
                                                            <span class="leading-tight">
                                                                {{ member.name }}
                                                                <span v-if="member.company_name" class="block text-[9px] text-slate-400 dark:text-slate-500 italic font-normal">{{ member.company_name }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div v-else class="pl-4 text-[9px] text-slate-400 dark:text-slate-500 italic">—</div>
                                                </template>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                                <span v-else class="text-slate-400 dark:text-slate-500">—</span>
                            </td>

                            <td class="px-3 py-2 text-center w-40 align-top">
                                <div class="flex flex-col gap-1 items-center justify-center">
                                    <button
                                        @click="openFunctionModal(row)"
                                        class="inline-flex items-center justify-center rounded-full bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-400 px-3 py-1 text-[10px] font-semibold transition w-full max-w-[96px]"
                                    >
                                        Structure
                                    </button>
                                    <button
                                        @click="openEditModal(row)"
                                        class="inline-flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-slate-200 px-3 py-1 text-[10px] font-semibold transition w-full max-w-[96px]"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="openDeleteModal(row)"
                                        class="inline-flex items-center justify-center rounded-full bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-500/10 dark:hover:bg-red-500/20 dark:text-red-400 px-3 py-1 text-[10px] font-semibold transition w-full max-w-[96px]"
                                    >
                                        Hapus
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

    <!-- Kelola Structure Modal -->
    <ConfirmationModal
        :show="isFunctionModalOpen"
        :title="`Kelola Structure - ${selectedFunctional?.name}`"
        message="Berikut adalah daftar structure (struktur internal) yang dikaitkan dengan organisasi fungsional ini."
        confirm-text="Tutup"
        cancel-text=""
        type="info"
        :loading="functionForm.processing"
        max-width="lg"
        @close="isFunctionModalOpen = false"
        @confirm="isFunctionModalOpen = false"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Add Structure Form -->
            <form @submit.prevent="submitAddFunction" class="flex flex-col gap-3 p-3 bg-slate-50 rounded-xl dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Tambah Structure Baru</h4>
                <div class="flex flex-col gap-1.5">
                    <label for="function_name_input" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Nama Structure / Struktur Internal</label>
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
                    <label class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Parent Structure <span class="font-normal text-slate-400">(opsional)</span></label>
                    <!-- Custom listbox (scrollable, clickable) -->
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="functionParentSearch"
                                type="text"
                                placeholder="Cari parent structure..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-44 overflow-y-auto">
                            <!-- Option for Root/Tanpa Parent -->
                            <li
                                @click="functionForm.parent_id = null"
                                class="flex items-center cursor-pointer py-1.5 px-3 text-[11px] border-b border-slate-50 dark:border-white/5 transition select-none font-medium text-slate-600 dark:text-slate-400"
                                :class="functionForm.parent_id === null
                                    ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 font-semibold'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                — Tanpa Parent (Root) —
                            </li>
                            <li v-if="filteredParentFunctions.length === 0 && functionParentSearch"
                                class="px-3 py-2 text-[11px] text-slate-400 dark:text-slate-500 text-center italic">
                                Tidak ada structure tersedia
                            </li>
                            <li
                                v-for="fun in filteredParentFunctions"
                                :key="fun.structure_id"
                                @click="functionForm.parent_id = fun.structure_id"
                                class="flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
                                :style="{ paddingLeft: `${8 + (fun._level || 0) * 14}px` }"
                                :class="functionForm.parent_id === fun.structure_id
                                    ? 'bg-blue-50 dark:bg-blue-500/10'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                <!-- — prefix with level-based styling -->
                                <span
                                    class="mr-1.5 shrink-0 font-medium"
                                    :class="fun._level === 0
                                        ? 'text-slate-600 dark:text-slate-400'
                                        : 'text-slate-400 dark:text-slate-500'"
                                >—</span>
                                <span
                                    :class="functionForm.parent_id === fun.structure_id
                                        ? 'font-semibold text-blue-700 dark:text-blue-300'
                                        : fun._level === 0
                                            ? 'font-medium text-slate-800 dark:text-slate-200'
                                            : 'text-slate-600 dark:text-slate-400'"
                                >{{ fun.name }}</span>
                            </li>
                        </ul>
                    </div>
                    <span v-if="functionForm.errors.parent_id" class="text-[10px] text-red-500 font-medium">{{ functionForm.errors.parent_id }}</span>
                </div>
            </form>

            <!-- List of Current Structures (tree) -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Structure Saat Ini</h4>
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
                                        title="Hapus Structure">
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
                                            title="Hapus Sub-Structure">
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
                    Belum ada structure. Silakan ketik nama structure di atas untuk menambahkan.
                </div>
            </div>
        </div>
    </ConfirmationModal>

    <!-- Kelola Anggota Modal -->
    <ConfirmationModal
        :show="isMemberModalOpen"
        :title="`Kelola Anggota — ${selectedStructure?.name}`"
        :message="selectedFunctional?.name ? `Organisasi: ${selectedFunctional.name}` : 'Berikut adalah daftar anggota yang tergabung dalam structure ini.'"
        confirm-text="Tutup"
        cancel-text=""
        type="info"
        :loading="memberForm.processing"
        max-width="xl"
        @close="closeMemberModal"
        @confirm="closeMemberModal"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Add Member Form -->
            <form @submit.prevent="submitAddMember" class="flex flex-col gap-3 p-3 bg-slate-50 rounded-xl dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Tambah Anggota Baru</h4>
                <!-- Pilih Perusahaan (Company) -->
                <div class="flex flex-col gap-1">
                    <label for="member_company_id" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Pilih Perusahaan (Company)</label>
                    <select
                        id="member_company_id"
                        v-model="memberCompanyId"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    >
                        <option value="">— Pilih Perusahaan —</option>
                        <option
                            v-for="company in companies"
                            :key="company.id"
                            :value="String(company.id)"
                        >
                            {{ company.name }}
                        </option>
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="member_organization_id" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Pilih Jabatan (BOD)</label>
                    <!-- Custom listbox (scrollable, clickable) -->
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="bodSearch"
                                type="text"
                                placeholder="Cari jabatan..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-44 overflow-y-auto">
                            <li v-if="filteredAvailableBods.length === 0"
                                class="px-3 py-2 text-[11px] text-slate-400 dark:text-slate-500 text-center italic">
                                Tidak ada jabatan tersedia
                            </li>
                            <li
                                v-for="bod in filteredAvailableBods"
                                :key="bod.id"
                                @click="memberForm.organization_id = bod.id"
                                class="flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
                                :style="{ paddingLeft: `${8 + (bod._level || 0) * 14}px` }"
                                :class="memberForm.organization_id === bod.id
                                    ? 'bg-blue-50 dark:bg-blue-500/10'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                <!-- — prefix with level-based styling -->
                                <span
                                    class="mr-1.5 shrink-0 font-medium"
                                    :class="bod._level === 0
                                        ? 'text-slate-600 dark:text-slate-400'
                                        : 'text-slate-400 dark:text-slate-500'"
                                >—</span>
                                <span
                                    :class="memberForm.organization_id === bod.id
                                        ? 'font-semibold text-blue-700 dark:text-blue-300'
                                        : bod._level === 0
                                            ? 'font-medium text-slate-800 dark:text-slate-200'
                                            : 'text-slate-600 dark:text-slate-400'"
                                >{{ bod.name }}</span>
                            </li>
                        </ul>
                    </div>
                    <!-- Selected label -->
                    <p v-if="memberForm.organization_id" class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">
                        ✓ Dipilih: {{ selectedBodName }}
                    </p>
                    <span v-if="memberForm.errors.organization_id" class="text-[10px] text-red-500 font-medium">{{ memberForm.errors.organization_id }}</span>
                    <button
                        type="submit"
                        :disabled="memberForm.processing || !memberForm.organization_id"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ memberForm.processing ? 'Menambahkan...' : 'Tambah Anggota' }}
                    </button>
                </div>
            </form>

            <!-- List of Current Members -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Anggota Saat Ini</h4>
                    <span v-if="currentMembersForStructure.length > 0" class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20">
                        {{ currentMembersForStructure.length }} anggota
                    </span>
                </div>
                <div v-if="currentMembersForStructure.length > 0" class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                    <div
                        v-for="member in currentMembersForStructure"
                        :key="member.organization_id"
                        class="flex items-center justify-between px-3 py-2 rounded-lg border border-slate-100 bg-white dark:border-white/5 dark:bg-[#1a1a1a] hover:bg-slate-50 dark:hover:bg-white/5 transition"
                    >
                        <div class="flex items-center gap-2">
                            <span class="flex h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>
                            <div class="text-xs font-medium text-slate-800 dark:text-slate-200">
                                {{ member.name }}
                                <span v-if="member.company_name" class="ml-1 text-[10px] text-slate-400 dark:text-slate-500 font-normal">
                                    ({{ member.company_name }})
                                </span>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="deleteMember(member.organization_id)"
                            class="inline-flex items-center justify-center rounded-md p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition shrink-0"
                            title="Hapus Anggota"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div v-else class="flex flex-col items-center justify-center gap-2 py-8 text-center border border-dashed border-slate-200 rounded-xl dark:border-white/10">
                    <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada anggota.<br>Pilih jabatan di atas untuk menambahkan.</p>
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
const memberCompanyId = ref('');
const selectedFunctional = ref(null);
const selectedStructure = ref(null);
const modalMode = ref('create'); // 'create' or 'edit'

watch(memberCompanyId, () => {
    memberForm.organization_id = '';
    bodSearch.value = '';
});

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
    const targetCompanyId = memberCompanyId.value;
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

const bodSearch = ref('');

/**
 * Flatten a parent-child array (with parent_id/id fields) into an ordered
 * flat list preserving tree order, each item annotated with `_level`.
 * idKey/parentKey default to 'id' and 'parent_id'.
 */
const flattenWithLevel = (items, idKey = 'id', parentKey = 'parent_id') => {
    if (!items || items.length === 0) return [];
    const map = {};
    const roots = [];
    items.forEach(item => { map[item[idKey]] = { ...item, _children: [], _level: 0 }; });
    items.forEach(item => {
        if (item[parentKey] && map[item[parentKey]]) {
            map[item[parentKey]]._children.push(map[item[idKey]]);
        } else {
            roots.push(map[item[idKey]]);
        }
    });
    const result = [];
    const walk = (node, level) => {
        node._level = level;
        result.push(node);
        node._children.forEach(child => walk(child, level + 1));
    };
    roots.forEach(r => walk(r, 0));
    return result;
};

const flatAvailableBods = computed(() => flattenWithLevel(availableBodsForCompany.value));

const filteredAvailableBods = computed(() => {
    const q = bodSearch.value.toLowerCase().trim();
    if (!q) return flatAvailableBods.value;
    // When searching, filter flat (ignore hierarchy)
    return availableBodsForCompany.value
        .filter(b => b.name.toLowerCase().includes(q))
        .map(b => ({ ...b, _level: 0 }));
});

const selectedBodName = computed(() => {
    if (!memberForm.organization_id) return '';
    const bod = props.bods.find(b => Number(b.id) === Number(memberForm.organization_id));
    if (!bod) return '';
    return bod.company_name ? `${bod.name} (${bod.company_name})` : bod.name;
});

const functionParentSearch = ref('');

const flatAvailableFunctions = computed(() => {
    return flattenWithLevel(selectedFunctional.value?.functions ?? [], 'structure_id', 'parent_id');
});

const filteredParentFunctions = computed(() => {
    const q = functionParentSearch.value.toLowerCase().trim();
    if (!q) return flatAvailableFunctions.value;
    // When searching, filter flat (ignore hierarchy)
    return (selectedFunctional.value?.functions ?? [])
        .filter(f => f.name.toLowerCase().includes(q))
        .map(f => ({ ...f, _level: 0 }));
});

const selectedParentFunctionName = computed(() => {
    if (functionForm.parent_id === null || functionForm.parent_id === undefined) {
        return '— Tanpa Parent (Root) —';
    }
    const fn = (selectedFunctional.value?.functions ?? []).find(f => Number(f.structure_id) === Number(functionForm.parent_id));
    return fn ? fn.name : '';
});

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

// ─── AD HOC Grouping Helpers ─────────────────────────────────────────────────

/**
 * Returns true if the structure name contains "ad hoc" (case-insensitive).
 * Used to trigger grup_function grouping in the Anggota column.
 */
const isAdHocStructure = (name) => {
    return (name || '').toLowerCase().includes('ad hoc');
};

/**
 * Groups an array of members by their `grup_function` field.
 * Members without grup_function are collected under "Lainnya".
 * Returns [{ label, members[] }, ...] sorted by label.
 */
const groupMembersByGrupFunction = (members) => {
    if (!members || members.length === 0) return [];
    const grouped = {};
    members.forEach(member => {
        const key = (member.grup_function || '').trim() || 'Lainnya';
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(member);
    });
    // Sort: prioritize Holding > Subholding > APFS > others > Lainnya
    const ORDER = ['Holding', 'Subholding', 'APFS'];
    return Object.keys(grouped)
        .sort((a, b) => {
            const ai = ORDER.indexOf(a);
            const bi = ORDER.indexOf(b);
            if (a === 'Lainnya') return 1;
            if (b === 'Lainnya') return -1;
            if (ai === -1 && bi === -1) return a.localeCompare(b);
            if (ai === -1) return 1;
            if (bi === -1) return -1;
            return ai - bi;
        })
        .map(label => ({ label, members: grouped[label] }));
};

/**
 * Reactive map: key = `${structureId}__${grupLabel}`, value = true (collapsed).
 * All groups start expanded (no key = expanded).
 */
const collapsedGrups = ref({});

const toggleGrupCollapse = (structureId, grupLabel) => {
    const key = `${structureId}__${grupLabel}`;
    collapsedGrups.value = {
        ...collapsedGrups.value,
        [key]: !collapsedGrups.value[key],
    };
};

const isGrupCollapsed = (structureId, grupLabel) => {
    const key = `${structureId}__${grupLabel}`;
    return !!collapsedGrups.value[key];
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
    functionParentSearch.value = '';
    isFunctionModalOpen.value = true;
};

const openMemberModalForStructure = (fun) => {
    // Refresh selectedFunctional dari props agar company_id selalu terbaru
    const freshRow = props.functionalOrganizations.find(f => f.id === selectedFunctional.value?.id);
    if (freshRow) selectedFunctional.value = freshRow;

    selectedStructure.value = fun;
    memberForm.clearErrors();
    memberForm.reset();
    memberForm.structure_id = fun.structure_id;
    bodSearch.value = '';
    
    // Set default company to the current organization's company
    memberCompanyId.value = selectedFunctional.value?.company_id ? String(selectedFunctional.value.company_id) : '';

    // Tutup modal fungsi sebelum buka modal anggota agar tidak overlap
    isFunctionModalOpen.value = false;
    isMemberModalOpen.value = true;
};

const closeMemberModal = () => {
    isMemberModalOpen.value = false;
    bodSearch.value = '';
    // Kembalikan ke modal fungsi agar user dapat terus mengelola fungsi
    isFunctionModalOpen.value = true;
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
            functionParentSearch.value = '';
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
