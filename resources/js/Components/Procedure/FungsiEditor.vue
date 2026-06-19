<template>
    <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
        <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

        <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
            </h3>
            <div class="flex items-center gap-3 print:hidden">
                <!-- Save status indicator -->
                <span class="text-[11px] flex items-center gap-1.5 select-none">
                    <span v-if="isSaving" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold animate-pulse">
                        <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else-if="saveStatus === 'saved'" class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Tersimpan
                    </span>
                    <span v-else-if="saveStatus === 'error'" class="text-rose-600 dark:text-rose-400 font-bold">
                        Gagal menyimpan
                    </span>
                    <span v-else-if="modifiedActors.size > 0" class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-semibold">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Belum disimpan
                    </span>
                    <span v-else class="text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-emerald-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tersimpan
                    </span>
                </span>

                <!-- Manual Save Button -->
                <button
                    @click="saveAll"
                    :disabled="isSaving || modifiedActors.size === 0"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95 disabled:opacity-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    Simpan
                </button>

                <button
                    @click="openAddActorModal"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 dark:border-white/10 bg-transparent px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-white/5 active:scale-95"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Aktor
                </button>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-200 dark:border-white/10">
            <table class="w-full border-collapse text-left text-[11px]">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3 w-20 text-center">No</th>
                        <th class="px-6 py-3">Fungsi / Unit Organisasi / Jabatan</th>
                        <th class="px-6 py-3">Jabatan</th>
                        <th class="px-6 py-3">Fungsi</th>
                        <th class="px-6 py-3">Organisasi</th>
                        <th class="px-6 py-3 w-24 text-center print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-if="actors.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data aktor terkait.</td>
                    </tr>
                    <tr v-for="(actor, index) in actors" :key="actor.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5">
                        <td class="px-6 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-6 py-2 align-middle">
                            <textarea
                                v-if="actorLocal[actor.id]"
                                v-model="actorLocal[actor.id].name"
                                @input="markModified(actor.id)"
                                @keydown.enter.prevent
                                rows="2"
                                class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10 resize-none leading-normal"
                                placeholder="Nama Aktor / Jabatan"
                            ></textarea>
                        </td>
                        <td class="px-6 py-2 align-middle relative">
                            <div v-if="actorLocal[actor.id]" class="relative">
                                <!-- Trigger Button -->
                                <button 
                                    type="button"
                                    @click="toggleActorDropdown(actor.id)"
                                    class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] hover:border-slate-300 dark:hover:border-white/10 text-left flex justify-between items-center text-[11px]"
                                >
                                    <span class="line-clamp-2 whitespace-normal break-words pr-2">
                                        {{ getSelectedOrgName(actor.id) }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-slate-400 shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>

                                <!-- Click Outside Overlay -->
                                <div v-if="activeDropdownActorId === actor.id" class="fixed inset-0 z-30" @click="activeDropdownActorId = null"></div>

                                <!-- Dropdown Content -->
                                <div v-if="activeDropdownActorId === actor.id" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                                    <!-- Search Input -->
                                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                                        <input 
                                            :id="`actor-search-input-${actor.id}`"
                                            type="text" 
                                            v-model="actorSearchQuery" 
                                            placeholder="Cari jabatan/organisasi..." 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                            @click.stop
                                        />
                                    </div>

                                    <!-- Option List -->
                                    <div class="space-y-0.5">
                                        <button
                                            type="button"
                                            @click="selectActorOrganization(actor.id, '')"
                                            class="w-full text-left px-2.5 py-1.5 text-[11px] rounded hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500 dark:text-slate-400"
                                        >
                                            -- Pilih Organisasi --
                                        </button>
                                        <button
                                            v-for="org in filteredSearchOrganizations" 
                                            :key="org.id"
                                            type="button"
                                            @click="selectActorOrganization(actor.id, org.id)"
                                            :class="[
                                                'w-full text-left px-2.5 py-1.5 text-[11px] rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                                actorLocal[actor.id].organization_id === org.id ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                            ]"
                                        >
                                            <span class="truncate pr-1">
                                                {{ getLevelPrefix(org) }}{{ org.jabatan }} ({{ org.name || '-' }} - {{ org.code || '-' }})
                                            </span>
                                            <svg v-if="actorLocal[actor.id].organization_id === org.id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-[#821f44] dark:text-[#db588c] shrink-0">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div v-if="filteredSearchOrganizations.length === 0" class="text-center py-4 text-[11px] text-slate-400">
                                            Tidak ada hasil ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <!-- Kolom Fungsi -->
                        <td class="px-6 py-2 align-middle">
                            <div class="flex flex-wrap gap-1">
                                <template v-if="actor.functions && actor.functions.length > 0">
                                    <span
                                        v-for="func in actor.functions"
                                        :key="func.id"
                                        class="inline-flex items-center rounded-md bg-violet-50 dark:bg-violet-900/20 px-2 py-0.5 text-[10px] font-medium text-violet-700 dark:text-violet-300 ring-1 ring-inset ring-violet-600/20 dark:ring-violet-400/20"
                                    >
                                        {{ func.name }}<template v-if="func.code"> ({{ func.code }})</template>
                                    </span>
                                </template>
                                <span v-else class="text-[10px] text-slate-400 italic">—</span>
                            </div>
                        </td>
                        <!-- Kolom Organisasi -->
                        <td class="px-6 py-2 align-middle">
                            <div class="flex flex-wrap gap-1">
                                <template v-if="actor.organizations && actor.organizations.length > 0">
                                    <span
                                        v-for="org in actor.organizations"
                                        :key="org.id"
                                        class="inline-flex items-center rounded-md bg-sky-50 dark:bg-sky-900/20 px-2 py-0.5 text-[10px] font-medium text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-600/20 dark:ring-sky-400/20"
                                    >
                                        {{ org.jabatan || org.name }}<template v-if="org.code"> ({{ org.code }})</template>
                                    </span>
                                </template>
                                <span v-else class="text-[10px] text-slate-400 italic">—</span>
                            </div>
                        </td>
                        <td class="px-6 py-2 align-middle text-center print:hidden">
                            <button
                                @click="deleteActor(actor)"
                                class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Edit/Add Actor Modal -->
        <ConfirmationModal
            :show="isActorModalOpen"
            title="Tambah Aktor"
            message="Silakan isi data aktor baru di bawah ini."
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            :loading="actorForm.processing"
            @close="closeActorModal"
            @confirm="submitActorForm"
        >
            <div class="mt-4 space-y-4 font-sans text-xs">
                <div class="flex flex-col gap-1.5">
                    <label for="actor_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Aktor</label>
                    <input
                        id="actor_name"
                        v-model="actorForm.name"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        placeholder="Contoh: Direktur Utama"
                        required
                    />
                    <span v-if="actorForm.errors.name" class="text-xs text-red-500 font-medium">{{ actorForm.errors.name }}</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="actor_org" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jabatan / Organisasi</label>
                    <select
                        id="actor_org"
                        v-model="actorForm.organization_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        required
                    >
                        <option value="" disabled>Pilih Organisasi...</option>
                        <option v-for="org in filteredOrganizations" :key="org.id" :value="org.id">
                            {{ getLevelPrefix(org) }}{{ org.jabatan }} ({{ org.name || '-' }} - {{ org.code || '-' }})
                        </option>
                    </select>
                    <span v-if="actorForm.errors.organization_id" class="text-xs text-red-500 font-medium">{{ actorForm.errors.organization_id }}</span>
                </div>

                <!-- Tambah Fungsi (Opsional) -->
                <div class="flex flex-col gap-1.5 relative">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tambah Fungsi (Opsional)</label>
                    
                    <!-- Trigger Button -->
                    <div class="relative">
                        <button 
                            type="button"
                            @click="toggleFunctionDropdown"
                            class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-3.5 py-2 text-xs text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-white/10 flex justify-between items-center"
                        >
                            <span class="truncate text-slate-400">
                                -- Pilih Fungsi --
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Overlay for click outside -->
                    <div v-if="isFunctionDropdownOpen" class="fixed inset-0 z-30" @click="isFunctionDropdownOpen = false"></div>

                    <!-- Dropdown Content -->
                    <div v-if="isFunctionDropdownOpen" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-48 overflow-y-auto p-2 space-y-2">
                        <!-- Search input inside dropdown -->
                        <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                            <input 
                                type="text" 
                                v-model="functionSearchQuery" 
                                placeholder="Cari fungsi..." 
                                class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                ref="functionSearchInput"
                                @click.stop
                            />
                        </div>
                        
                        <!-- Options list -->
                        <div class="space-y-0.5">
                            <button
                                v-for="func in filteredFunctions" 
                                :key="func.id"
                                type="button"
                                @click="toggleFunctionSelection(func.id)"
                                :class="[
                                    'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                    actorForm.function_ids.includes(func.id) ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                ]"
                            >
                                <span class="truncate">
                                    {{ func.name }} {{ func.code ? `(${func.code})` : '' }}
                                </span>
                                <svg v-if="actorForm.function_ids.includes(func.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="filteredFunctions.length === 0" class="text-center py-4 text-xs text-slate-400">
                                Tidak ada hasil ditemukan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected list display -->
                    <div v-if="actorForm.function_ids && actorForm.function_ids.length > 0" class="mt-1 flex flex-wrap gap-1.5">
                        <span 
                            v-for="id in actorForm.function_ids" 
                            :key="id"
                            class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-[11px] font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                        >
                            <span class="max-w-[200px] truncate">
                                {{ getFunctionTitle(id) }}
                            </span>
                            <button 
                                type="button" 
                                @click="removeFunctionId(id)"
                                class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </span>
                    </div>
                    <span v-if="actorForm.errors.function_ids" class="text-xs text-red-500 font-medium">{{ actorForm.errors.function_ids }}</span>
                </div>

                <!-- Tambah Organisasi (Opsional) -->
                <div class="flex flex-col gap-1.5 relative">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tambah Organisasi (Opsional)</label>
                    
                    <!-- Trigger Button -->
                    <div class="relative">
                        <button 
                            type="button"
                            @click="toggleOrgDropdown"
                            class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-3.5 py-2 text-xs text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-white/10 flex justify-between items-center"
                        >
                            <span class="truncate text-slate-400">
                                -- Pilih Organisasi --
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Overlay for click outside -->
                    <div v-if="isOrgDropdownOpen" class="fixed inset-0 z-30" @click="isOrgDropdownOpen = false"></div>

                    <!-- Dropdown Content -->
                    <div v-if="isOrgDropdownOpen" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-48 overflow-y-auto p-2 space-y-2">
                        <!-- Search input inside dropdown -->
                        <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                            <input 
                                type="text" 
                                v-model="orgDropdownSearchQuery" 
                                placeholder="Cari organisasi..." 
                                class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                ref="orgDropdownSearchInput"
                                @click.stop
                            />
                        </div>
                        
                        <!-- Options list -->
                        <div class="space-y-0.5">
                            <button
                                v-for="org in filteredOrgDropdown" 
                                :key="org.id"
                                type="button"
                                @click="toggleOrgSelection(org.id)"
                                :class="[
                                    'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                    actorForm.organization_ids.includes(org.id) ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                ]"
                            >
                                <span class="truncate">
                                    {{ getLevelPrefix(org) }}{{ org.jabatan }} ({{ org.name || '-' }} - {{ org.code || '-' }})
                                </span>
                                <svg v-if="actorForm.organization_ids.includes(org.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="filteredOrgDropdown.length === 0" class="text-center py-4 text-xs text-slate-400">
                                Tidak ada hasil ditemukan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected list display -->
                    <div v-if="actorForm.organization_ids && actorForm.organization_ids.length > 0" class="mt-1 flex flex-wrap gap-1.5">
                        <span 
                            v-for="id in actorForm.organization_ids" 
                            :key="id"
                            class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-[11px] font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                        >
                            <span class="max-w-[200px] truncate">
                                {{ getOrgTitle(id) }}
                            </span>
                            <button 
                                type="button" 
                                @click="removeOrgId(id)"
                                class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </span>
                    </div>
                    <span v-if="actorForm.errors.organization_ids" class="text-xs text-red-500 font-medium">{{ actorForm.errors.organization_ids }}</span>
                </div>
            </div>
        </ConfirmationModal>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
    activeRegulation: {
        type: Object,
        default: null,
    },
    functions: {
        type: Array,
        default: () => [],
    },
});

// ─── State ────────────────────────────────────────────────────────────────────
const actorLocal = ref({});
const modifiedActors = ref(new Set());
const isSaving = ref(false);
const saveStatus = ref(null); // null | 'saved' | 'error'

// Searchable actor organization select state and helpers
const activeDropdownActorId = ref(null);
const actorSearchQuery = ref('');

function toggleActorDropdown(actorId) {
    if (activeDropdownActorId.value === actorId) {
        activeDropdownActorId.value = null;
    } else {
        activeDropdownActorId.value = actorId;
        actorSearchQuery.value = '';
        nextTick(() => {
            const input = document.getElementById(`actor-search-input-${actorId}`);
            input?.focus();
        });
    }
}

function selectActorOrganization(actorId, orgId) {
    if (actorLocal.value[actorId]) {
        actorLocal.value[actorId].organization_id = orgId;
        markModified(actorId);
    }
    activeDropdownActorId.value = null;
}

const getSelectedOrgName = (actorId) => {
    const orgId = actorLocal.value[actorId]?.organization_id;
    if (!orgId) return '-- Pilih Organisasi --';
    const org = props.organizations.find(o => o.id === orgId);
    return org ? `${org.jabatan} (${org.name || '-'} - ${org.code || '-'})` : '-- Pilih Organisasi --';
};

const filteredSearchOrganizations = computed(() => {
    const query = actorSearchQuery.value.toLowerCase().trim();
    if (!query) return filteredOrganizations.value;
    return filteredOrganizations.value.filter(org => 
        (org.name || '').toLowerCase().includes(query) || 
        (org.code || '').toLowerCase().includes(query) || 
        (org.alias || '').toLowerCase().includes(query) ||
        (org.jabatan || '').toLowerCase().includes(query)
    );
});

// ─── Computed ─────────────────────────────────────────────────────────────────
const getOrganizationDepth = (orgId) => {
    let depth = 0;
    let currentId = orgId;
    const orgMap = new Map(props.organizations.map(org => [org.id, org]));
    const visited = new Set();
    
    const org = orgMap.get(orgId);
    if (!org) return 0;

    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const currentOrg = orgMap.get(currentId);
        if (currentOrg && currentOrg.parent_id) {
            depth++;
            currentId = currentOrg.parent_id;
        } else {
            break;
        }
    }

    // Special depth adjustment for Upstream Support Staff (e.g. Corporate Secretary 01100100)
    // to ensure they are indented as Level 3 (depth 2) and NOT parallel to Supportive Directors (depth 1 / Level 2)
    const code = String(org.code || '').trim();
    if (code.startsWith('011')) {
        if (code.startsWith('01100') && code !== '01100000') {
            return 2;
        }
        
        let tempId = org.parent_id;
        const tempVisited = new Set();
        let hasSupportStaffAncestor = false;
        while (tempId && !tempVisited.has(tempId)) {
            tempVisited.add(tempId);
            const parentOrg = orgMap.get(tempId);
            if (parentOrg) {
                const parentCode = String(parentOrg.code || '').trim();
                if (parentCode.startsWith('01100') && parentCode !== '01100000') {
                    hasSupportStaffAncestor = true;
                    break;
                }
                tempId = parentOrg.parent_id;
            } else {
                break;
            }
        }
        if (hasSupportStaffAncestor) {
            return depth + 1;
        }
    }

    return depth;
};

const getLevelPrefix = (org) => {
    const depth = getOrganizationDepth(org.id);
    if (depth === 0) return '';
    return '\u00A0\u00A0'.repeat(depth) + '— ';
};

const filteredOrganizations = computed(() => {
    const orgMap = new Map(props.organizations.map(org => [org.id, { ...org, children: [] }]));
    const roots = [];
    
    props.organizations.forEach(org => {
        const mapped = orgMap.get(org.id);
        if (org.parent_id && orgMap.has(org.parent_id)) {
            orgMap.get(org.parent_id).children.push(mapped);
        } else {
            roots.push(mapped);
        }
    });

    const sortNodes = (nodes) => {
        nodes.sort((a, b) => (a.code || '').localeCompare(b.code || ''));
        nodes.forEach(node => {
            if (node.children.length > 0) {
                sortNodes(node.children);
            }
        });
    };
    sortNodes(roots);

    const flattened = [];
    const traverse = (node) => {
        if (node.jabatan && String(node.jabatan).trim() !== '') {
            flattened.push(node);
        }
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);
    return flattened;
});

const hasUnsavedChanges = computed(() => modifiedActors.value.size > 0);

// ─── Init local state ─────────────────────────────────────────────────────────
function initLocal() {
    props.actors.forEach(actor => {
        if (!actorLocal.value[actor.id]) {
            actorLocal.value[actor.id] = {
                name: actor.name || '',
                organization_id: actor.organization_id || '',
            };
        } else if (!modifiedActors.value.has(actor.id)) {
            // Only sync if not currently being edited
            actorLocal.value[actor.id].name = actor.name || '';
            actorLocal.value[actor.id].organization_id = actor.organization_id || '';
        }
    });
}

initLocal();

watch(() => props.actors, () => { initLocal(); }, { deep: true });

// ─── Actions ──────────────────────────────────────────────────────────────────
function markModified(actorId) {
    modifiedActors.value.add(actorId);
}

// ─── Modal State and Methods ──────────────────────────────────────────────────
const isActorModalOpen = ref(false);
const actorForm = useForm({
    name: '',
    organization_id: '',
    regulation_id: '',
    function_ids: [],
    organization_ids: [],
});

const isFunctionDropdownOpen = ref(false);
const functionSearchQuery = ref('');
const functionSearchInput = ref(null);

function toggleFunctionDropdown() {
    isFunctionDropdownOpen.value = !isFunctionDropdownOpen.value;
    if (isFunctionDropdownOpen.value) {
        functionSearchQuery.value = '';
        setTimeout(() => {
            functionSearchInput.value?.focus();
        }, 100);
    }
}

function toggleFunctionSelection(id) {
    if (!actorForm.function_ids) {
        actorForm.function_ids = [];
    }
    const index = actorForm.function_ids.indexOf(id);
    if (index > -1) {
        actorForm.function_ids.splice(index, 1);
    } else {
        actorForm.function_ids.push(id);
    }
}

function removeFunctionId(id) {
    if (!actorForm.function_ids) return;
    const index = actorForm.function_ids.indexOf(id);
    if (index > -1) {
        actorForm.function_ids.splice(index, 1);
    }
}

function getFunctionTitle(id) {
    const func = props.functions.find(f => f.id === id);
    return func ? func.name : '';
}

const filteredFunctions = computed(() => {
    const pool = props.functions;
    const query = functionSearchQuery.value.toLowerCase().trim();
    if (!query) return pool;
    return pool.filter(f => 
        (f.name || '').toLowerCase().includes(query) || 
        (f.code || '').toLowerCase().includes(query) ||
        (f.alias || '').toLowerCase().includes(query)
    );
});

// ─── Org Multi-Select Dropdown State ──────────────────────────────────────────
const isOrgDropdownOpen = ref(false);
const orgDropdownSearchQuery = ref('');
const orgDropdownSearchInput = ref(null);

function toggleOrgDropdown() {
    isOrgDropdownOpen.value = !isOrgDropdownOpen.value;
    if (isOrgDropdownOpen.value) {
        orgDropdownSearchQuery.value = '';
        setTimeout(() => {
            orgDropdownSearchInput.value?.focus();
        }, 100);
    }
}

function toggleOrgSelection(id) {
    if (!actorForm.organization_ids) {
        actorForm.organization_ids = [];
    }
    const index = actorForm.organization_ids.indexOf(id);
    if (index > -1) {
        actorForm.organization_ids.splice(index, 1);
    } else {
        actorForm.organization_ids.push(id);
    }
}

function removeOrgId(id) {
    if (!actorForm.organization_ids) return;
    const index = actorForm.organization_ids.indexOf(id);
    if (index > -1) {
        actorForm.organization_ids.splice(index, 1);
    }
}

function getOrgTitle(id) {
    const org = props.organizations.find(o => o.id === id);
    return org ? `${org.jabatan || ''} (${org.name || '-'})` : '';
}

const filteredOrgDropdown = computed(() => {
    const pool = filteredOrganizations.value;
    const query = orgDropdownSearchQuery.value.toLowerCase().trim();
    if (!query) return pool;
    return pool.filter(o =>
        (o.name || '').toLowerCase().includes(query) ||
        (o.code || '').toLowerCase().includes(query) ||
        (o.alias || '').toLowerCase().includes(query) ||
        (o.jabatan || '').toLowerCase().includes(query)
    );
});

function openAddActorModal() {
    actorForm.reset();
    actorForm.clearErrors();
    actorForm.regulation_id = props.activeRegulation?.id || '';
    // Set first valid organization as default if available
    if (filteredOrganizations.value && filteredOrganizations.value.length > 0) {
        actorForm.organization_id = filteredOrganizations.value[0].id;
    }
    actorForm.function_ids = [];
    actorForm.organization_ids = [];
    isFunctionDropdownOpen.value = false;
    functionSearchQuery.value = '';
    isOrgDropdownOpen.value = false;
    orgDropdownSearchQuery.value = '';
    isActorModalOpen.value = true;
}

function closeActorModal() {
    isActorModalOpen.value = false;
    actorForm.reset();
    isFunctionDropdownOpen.value = false;
    functionSearchQuery.value = '';
    isOrgDropdownOpen.value = false;
    orgDropdownSearchQuery.value = '';
}

function submitActorForm() {
    actorForm.post(route('policy.procedure.actor.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeActorModal();
            Swal.fire({
                title: 'Berhasil!',
                text: 'Aktor baru telah ditambahkan.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
    });
}

function deleteActor(actor) {
    Swal.fire({
        title: 'Hapus Aktor',
        text: `Apakah Anda yakin ingin menghapus aktor '${actor.name}'?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#821f44',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            router.delete(route('policy.procedure.actor.destroy', actor.id), { preserveScroll: true });
        }
    });
}

async function saveAll() {
    if (modifiedActors.value.size === 0) return;
    isSaving.value = true;
    saveStatus.value = null;

    try {
        const promises = [];
        for (const actorId of modifiedActors.value) {
            const data = actorLocal.value[actorId];
            promises.push(
                axios.put(
                    route('policy.procedure.actor.update', actorId),
                    {
                        name: data.name,
                        organization_id: data.organization_id,
                        regulation_id: props.activeRegulation?.id || '',
                    },
                    { headers: { 'Accept': 'application/json' } }
                )
            );
        }
        await Promise.all(promises);
        modifiedActors.value.clear();
        saveStatus.value = 'saved';
        // Reload after delay so the "Tersimpan" notification is visible first
        setTimeout(() => {
            saveStatus.value = null;
            router.reload({ preserveScroll: true });
        }, 2000);
    } catch (error) {
        console.error('Gagal menyimpan aktor:', error);
        saveStatus.value = 'error';
    } finally {
        isSaving.value = false;
    }
}

// ─── Expose for parent tab-switch guard ───────────────────────────────────────
defineExpose({ hasUnsavedChanges, saveAll });
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
