<template>
    <Teleport to="body">
        <transition name="fade">
            <div v-if="isModalOpen" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl dark:bg-[#1a1a1a] border border-slate-200 dark:border-white/10 overflow-y-auto max-h-[85vh] animate-scale-up">
                    <!-- Modal Header -->
                    <div class="bg-[#821f44] p-5 text-white flex items-center justify-between sticky top-0 z-10 shrink-0">
                        <h3 class="text-base font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            {{ editingId ? 'Edit Regulasi Kebijakan' : 'Tambah Regulasi Baru' }}
                        </h3>
                        <button @click="closeModal" class="text-white/80 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Form body -->
                    <form @submit.prevent="submitForm">
                        <div class="p-6 space-y-4">
                            <!-- Judul -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Judul Kebijakan:</label>
                                <input 
                                    type="text" 
                                    v-model="form.judul" 
                                    placeholder="Masukkan Judul Regulasi..." 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                    required
                                />
                                <div v-if="form.errors.judul" class="text-xs text-rose-500 font-medium">{{ form.errors.judul }}</div>
                            </div>

                            <!-- Nomor -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nomor Regulasi:</label>
                                <input 
                                    type="text" 
                                    v-model="form.nomor" 
                                    placeholder="Masukkan Nomor Regulasi..." 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                />
                                <div v-if="form.errors.nomor" class="text-xs text-rose-500 font-medium">{{ form.errors.nomor }}</div>
                            </div>

                            <!-- Tipe & Owner -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tipe Regulasi:</label>
                                    <select 
                                        v-model="form.tipe" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        required
                                    >
                                        <option value="" disabled>Pilih Tipe...</option>
                                        <option value="Policy">Policy</option>
                                        <option value="Procedure">Procedure</option>
                                        <option value="Standart">Standart</option>
                                        <option value="Surat Keputusan">Surat Keputusan</option>
                                        <option value="Surat Perintah">Surat Perintah</option>
                                    </select>
                                    <div v-if="form.errors.tipe" class="text-xs text-rose-500 font-medium">{{ form.errors.tipe }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pemilik Dokumen:</label>
                                    <input 
                                        type="text" 
                                        v-model="form.owner" 
                                        placeholder="Contoh: Kementerian BUMN, Pertamina" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                        required
                                    />
                                    <div v-if="form.errors.owner" class="text-xs text-rose-500 font-medium">{{ form.errors.owner }}</div>
                                </div>
                            </div>

                            <!-- STK -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">STK:</label>
                                <input 
                                    type="text" 
                                    v-model="form.stk" 
                                    placeholder="Masukkan STK..." 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                />
                                <div v-if="form.errors.stk" class="text-xs text-rose-500 font-medium">{{ form.errors.stk }}</div>
                            </div>

                            <!-- 
                            Internal) Selection with Search -->
                            <div class="space-y-1.5 relative">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pemilik Dokumen:</label>
                                
                                <!-- Trigger Button -->
                                <div class="relative">
                                    <button 
                                        type="button"
                                        @click="togglePicDropdown"
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10 flex justify-between items-center"
                                    >
                                        <span class="truncate">
                                            {{ selectedPicName || '-- Pilih Pemilik Dokumen (Internal) --' }}
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Overlay for click outside -->
                                <div v-if="isPicDropdownOpen" class="fixed inset-0 z-30" @click="isPicDropdownOpen = false"></div>

                                <!-- Dropdown Content -->
                                <div v-if="isPicDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                                    <!-- Search input inside dropdown -->
                                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                                        <input 
                                            type="text" 
                                            v-model="picSearchQuery" 
                                            placeholder="Cari pemilik dokumen (internal)..." 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                            ref="picSearchInput"
                                            @click.stop
                                        />
                                    </div>
                                    
                                    <!-- Options list -->
                                    <div class="space-y-0.5">
                                        <button
                                            type="button"
                                            @click="selectPic('')"
                                            class="w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500 dark:text-slate-400"
                                        >
                                            -- Pilih Pemilik Dokumen (Internal) --
                                        </button>
                                        <button
                                            v-for="org in filteredPicOrganizations" 
                                            :key="org.id"
                                            type="button"
                                            @click="selectPic(org.id)"
                                            :class="[
                                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                                form.pic_id === org.id ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                            ]"
                                        >
                                            <span class="truncate">
                                                {{ getLevelPrefix(org) }}{{ org.name }} ({{ org.code }})
                                            </span>
                                            <svg v-if="form.pic_id === org.id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div v-if="filteredPicOrganizations.length === 0" class="text-center py-4 text-xs text-slate-400">
                                            Tidak ada hasil ditemukan.
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.pic_id" class="text-xs text-rose-500 font-medium">{{ form.errors.pic_id }}</div>
                            </div>

                            <!-- Akses Role Selection with Search -->
                            <div class="space-y-1.5 relative">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Access Roles:</label>
                                
                                <!-- Trigger Button -->
                                <div class="relative">
                                    <button 
                                        type="button"
                                        @click="toggleMasterDropdown"
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10 flex justify-between items-center"
                                    >
                                        <span class="truncate">
                                            {{ selectedMasterName || '-- Pilih Akses Role --' }}
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Overlay for click outside -->
                                <div v-if="isMasterDropdownOpen" class="fixed inset-0 z-30" @click="isMasterDropdownOpen = false"></div>

                                <!-- Dropdown Content -->
                                <div v-if="isMasterDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                                    <!-- Search input inside dropdown -->
                                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                                        <input 
                                            type="text" 
                                            v-model="masterSearchQuery" 
                                            placeholder="Cari akses role..." 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                            ref="masterSearchInput"
                                            @click.stop
                                        />
                                    </div>
                                    
                                    <!-- Options list -->
                                    <div class="space-y-0.5">
                                        <button
                                            type="button"
                                            @click="selectMaster('')"
                                            class="w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500 dark:text-slate-400"
                                        >
                                            -- Pilih Akses Role --
                                        </button>
                                        <button
                                            v-for="org in filteredMasterOrganizations" 
                                            :key="org.id"
                                            type="button"
                                            @click="selectMaster(org.id)"
                                            :class="[
                                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                                form.master_id === org.id ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                            ]"
                                        >
                                            <span class="truncate">
                                                {{ getLevelPrefix(org) }}{{ org.name }} ({{ org.code }})
                                            </span>
                                            <svg v-if="form.master_id === org.id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div v-if="filteredMasterOrganizations.length === 0" class="text-center py-4 text-xs text-slate-400">
                                            Tidak ada hasil ditemukan.
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.master_id" class="text-xs text-rose-500 font-medium">{{ form.errors.master_id }}</div>
                            </div>

                            <!-- Parent Regulation Selection -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Parent Regulasi (Opsional):</label>
                                <select 
                                    v-model="form.parent_id" 
                                    class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                >
                                    <option value="">-- Pilih Parent Regulasi --</option>
                                    <option 
                                        v-for="reg in regulations" 
                                        :key="reg.id" 
                                        :value="reg.id"
                                        v-show="!editingId || reg.id !== editingId"
                                    >
                                        [{{ reg.tipe }}] {{ reg.judul }}
                                    </option>
                                </select>
                                <div v-if="form.errors.parent_id" class="text-xs text-rose-500 font-medium">{{ form.errors.parent_id }}</div>
                            </div>

                            <!-- Mencabut Dokumen (Opsional) -->
                            <div class="space-y-1.5 relative">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mencabut Dokumen (Opsional):</label>
                                
                                <!-- Trigger Button -->
                                <div class="relative">
                                    <button 
                                        type="button"
                                        @click="toggleRevokedDropdown"
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10 flex justify-between items-center"
                                    >
                                        <span class="truncate text-slate-400">
                                            -- Pilih Dokumen yang Dicabut --
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Overlay for click outside -->
                                <div v-if="isRevokedDropdownOpen" class="fixed inset-0 z-30" @click="isRevokedDropdownOpen = false"></div>

                                <!-- Dropdown Content -->
                                <div v-if="isRevokedDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                                    <!-- Search input inside dropdown -->
                                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                                        <input 
                                            type="text" 
                                            v-model="revokedSearchQuery" 
                                            placeholder="Cari regulasi..." 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                            ref="revokedSearchInput"
                                            @click.stop
                                        />
                                    </div>
                                    
                                    <!-- Options list -->
                                    <div class="space-y-0.5">
                                        <button
                                            v-for="reg in filteredRevokedRegulations" 
                                            :key="reg.id"
                                            type="button"
                                            @click="toggleRevokedSelection(reg.id)"
                                            :class="[
                                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                                form.revoked_ids.includes(reg.id) ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                            ]"
                                        >
                                            <span class="truncate">
                                                [{{ reg.tipe }}] {{ reg.judul }} {{ reg.nomor ? `(${reg.nomor})` : '' }}
                                            </span>
                                            <svg v-if="form.revoked_ids.includes(reg.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div v-if="filteredRevokedRegulations.length === 0" class="text-center py-4 text-xs text-slate-400">
                                            Tidak ada hasil ditemukan.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selected list display -->
                                <div v-if="form.revoked_ids && form.revoked_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                                    <span 
                                        v-for="id in form.revoked_ids" 
                                        :key="id"
                                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 pl-2.5 pr-1.5 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
                                    >
                                        <span class="max-w-[200px] truncate">
                                            {{ getRegulationTitle(id) }}
                                        </span>
                                        <button 
                                            type="button" 
                                            @click="removeRevokedId(id)"
                                            class="rounded-full p-0.5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                            </svg>
                                        </button>
                                    </span>
                                </div>
                                <div v-if="form.errors.revoked_ids" class="text-xs text-rose-500 font-medium">{{ form.errors.revoked_ids }}</div>
                            </div>

                            <!-- Dokumen Terkait (Opsional) -->
                            <div class="space-y-1.5 relative">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dokumen Terkait (Opsional):</label>
                                
                                <!-- Trigger Button -->
                                <div class="relative">
                                    <button 
                                        type="button"
                                        @click="toggleRelatedDropdown"
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10 flex justify-between items-center"
                                    >
                                        <span class="truncate text-slate-400">
                                            -- Pilih Dokumen Terkait --
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Overlay for click outside -->
                                <div v-if="isRelatedDropdownOpen" class="fixed inset-0 z-30" @click="isRelatedDropdownOpen = false"></div>

                                <!-- Dropdown Content -->
                                <div v-if="isRelatedDropdownOpen" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-60 overflow-y-auto p-2 space-y-2">
                                    <!-- Search input inside dropdown -->
                                    <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                                        <input 
                                            type="text" 
                                            v-model="relatedSearchQuery" 
                                            placeholder="Cari dokumen terkait..." 
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
                                            ref="relatedSearchInput"
                                            @click.stop
                                        />
                                    </div>
                                    
                                    <!-- Options list -->
                                    <div class="space-y-0.5">
                                        <button
                                            v-for="reg in filteredRelatedRegulations" 
                                            :key="reg.id"
                                            type="button"
                                            @click="toggleRelatedSelection(reg.id)"
                                            :class="[
                                                'w-full text-left px-2.5 py-1.5 text-xs rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
                                                form.related_ids.includes(reg.id) ? 'bg-[#821f44]/5 text-[#821f44] dark:bg-[#db588c]/10 dark:text-[#db588c] font-semibold' : 'text-slate-700 dark:text-slate-300'
                                            ]"
                                        >
                                            <span class="truncate">
                                                [{{ reg.tipe }}] {{ reg.judul }} {{ reg.nomor ? `(${reg.nomor})` : '' }}
                                            </span>
                                            <svg v-if="form.related_ids.includes(reg.id)" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#db588c] shrink-0">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div v-if="filteredRelatedRegulations.length === 0" class="text-center py-4 text-xs text-slate-400">
                                            Tidak ada hasil ditemukan.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selected list display -->
                                <div v-if="form.related_ids && form.related_ids.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                                    <span 
                                        v-for="id in form.related_ids" 
                                        :key="id"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 pl-2.5 pr-1.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-700/30"
                                    >
                                        <span class="max-w-[200px] truncate">
                                            {{ getRegulationTitle(id) }}
                                        </span>
                                        <button 
                                            type="button" 
                                            @click="removeRelatedId(id)"
                                            class="rounded-full p-0.5 hover:bg-blue-100 dark:hover:bg-blue-700/30 text-blue-400 hover:text-blue-600 dark:hover:text-blue-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                            </svg>
                                        </button>
                                    </span>
                                </div>
                                <div v-if="form.errors.related_ids" class="text-xs text-rose-500 font-medium">{{ form.errors.related_ids }}</div>
                            </div>

                            <!-- Revisi & Status -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Revisi / Versi:</label>
                                    <input 
                                        type="text" 
                                        v-model="form.revisi" 
                                        placeholder="Contoh: Rev 0, Rev 1.2" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-white/10"
                                        required
                                    />
                                    <div v-if="form.errors.revisi" class="text-xs text-rose-500 font-medium">{{ form.errors.revisi }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status:</label>
                                    <select 
                                        v-model="form.status" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-white/10"
                                    >
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Draft Dicabut">Draft Dicabut</option>
                                        <option value="Berlaku">Berlaku</option>
                                        <option value="Dicabut">Dicabut</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-xs text-rose-500 font-medium">{{ form.errors.status }}</div>
                                </div>
                            </div>

                            <!-- Tanggal Terbit & Tanggal Berlaku -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Terbit (Opsional):</label>
                                    <input 
                                        type="date" 
                                        v-model="form.terbit" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                    />
                                    <div v-if="form.errors.terbit" class="text-xs text-rose-500 font-medium">{{ form.errors.terbit }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Berlaku (Opsional):</label>
                                    <input 
                                        type="date" 
                                        v-model="form.berlaku" 
                                        class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                    />
                                    <div v-if="form.errors.berlaku" class="text-xs text-rose-500 font-medium">{{ form.errors.berlaku }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer Actions -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5 sticky bottom-0 z-10 shrink-0">
                            <button 
                                type="button" 
                                @click="closeModal"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                Simpan Regulasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    organizations: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['success']);

// ---------------------------------------------------
// MODAL & FORM STATE
// ---------------------------------------------------
const isModalOpen = ref(false);
const editingId = ref(null);

// Searchable select dropdown state and helpers
const isPicDropdownOpen = ref(false);
const isMasterDropdownOpen = ref(false);
const isRevokedDropdownOpen = ref(false);
const isRelatedDropdownOpen = ref(false);
const picSearchQuery = ref('');
const masterSearchQuery = ref('');
const revokedSearchQuery = ref('');
const relatedSearchQuery = ref('');
const picSearchInput = ref(null);
const masterSearchInput = ref(null);
const revokedSearchInput = ref(null);
const relatedSearchInput = ref(null);

function togglePicDropdown() {
    isPicDropdownOpen.value = !isPicDropdownOpen.value;
    if (isPicDropdownOpen.value) {
        picSearchQuery.value = '';
        setTimeout(() => {
            picSearchInput.value?.focus();
        }, 100);
    }
}

function toggleMasterDropdown() {
    isMasterDropdownOpen.value = !isMasterDropdownOpen.value;
    if (isMasterDropdownOpen.value) {
        masterSearchQuery.value = '';
        setTimeout(() => {
            masterSearchInput.value?.focus();
        }, 100);
    }
}

function selectPic(orgId) {
    form.pic_id = orgId;
    isPicDropdownOpen.value = false;
}

function selectMaster(orgId) {
    form.master_id = orgId;
    isMasterDropdownOpen.value = false;
}

function toggleRevokedDropdown() {
    isRevokedDropdownOpen.value = !isRevokedDropdownOpen.value;
    if (isRevokedDropdownOpen.value) {
        revokedSearchQuery.value = '';
        setTimeout(() => {
            revokedSearchInput.value?.focus();
        }, 100);
    }
}

function toggleRevokedSelection(id) {
    if (!form.revoked_ids) {
        form.revoked_ids = [];
    }
    const index = form.revoked_ids.indexOf(id);
    if (index > -1) {
        form.revoked_ids.splice(index, 1);
    } else {
        form.revoked_ids.push(id);
    }
}

function removeRevokedId(id) {
    if (!form.revoked_ids) return;
    const index = form.revoked_ids.indexOf(id);
    if (index > -1) {
        form.revoked_ids.splice(index, 1);
    }
}

function toggleRelatedDropdown() {
    isRelatedDropdownOpen.value = !isRelatedDropdownOpen.value;
    if (isRelatedDropdownOpen.value) {
        relatedSearchQuery.value = '';
        setTimeout(() => {
            relatedSearchInput.value?.focus();
        }, 100);
    }
}

function toggleRelatedSelection(id) {
    if (!form.related_ids) {
        form.related_ids = [];
    }
    const index = form.related_ids.indexOf(id);
    if (index > -1) {
        form.related_ids.splice(index, 1);
    } else {
        form.related_ids.push(id);
    }
}

function removeRelatedId(id) {
    if (!form.related_ids) return;
    const index = form.related_ids.indexOf(id);
    if (index > -1) {
        form.related_ids.splice(index, 1);
    }
}

function getRegulationTitle(id) {
    const reg = props.regulations.find(r => r.id === id);
    return reg ? `[${reg.tipe}] ${reg.judul}` : '';
}

const filteredRevokedRegulations = computed(() => {
    const pool = props.regulations.filter(r => !editingId.value || r.id !== editingId.value);
    const query = revokedSearchQuery.value.toLowerCase().trim();
    if (!query) return pool;
    return pool.filter(r => 
        (r.judul || '').toLowerCase().includes(query) || 
        (r.nomor || '').toLowerCase().includes(query) || 
        (r.tipe || '').toLowerCase().includes(query)
    );
});

const filteredRelatedRegulations = computed(() => {
    const pool = props.regulations.filter(r => !editingId.value || r.id !== editingId.value);
    const query = relatedSearchQuery.value.toLowerCase().trim();
    if (!query) return pool;
    return pool.filter(r => 
        (r.judul || '').toLowerCase().includes(query) || 
        (r.nomor || '').toLowerCase().includes(query) || 
        (r.tipe || '').toLowerCase().includes(query)
    );
});

const selectedPicName = computed(() => {
    if (!form.pic_id) return '';
    const org = props.organizations.find(o => o.id === form.pic_id);
    return org ? `${org.name} (${org.code})` : '';
});

const selectedMasterName = computed(() => {
    if (!form.master_id) return '';
    const org = props.organizations.find(o => o.id === form.master_id);
    return org ? `${org.name} (${org.code})` : '';
});

const filteredPicOrganizations = computed(() => {
    const query = picSearchQuery.value.toLowerCase().trim();
    if (!query) return hierarchicalOrganizations.value;
    return hierarchicalOrganizations.value.filter(org => 
        (org.name || '').toLowerCase().includes(query) || 
        (org.code || '').toLowerCase().includes(query) || 
        (org.alias || '').toLowerCase().includes(query)
    );
});

const filteredMasterOrganizations = computed(() => {
    const query = masterSearchQuery.value.toLowerCase().trim();
    if (!query) return hierarchicalOrganizations.value;
    return hierarchicalOrganizations.value.filter(org => 
        (org.name || '').toLowerCase().includes(query) || 
        (org.code || '').toLowerCase().includes(query) || 
        (org.alias || '').toLowerCase().includes(query)
    );
});

const form = useForm({
    judul: '',
    nomor: '',
    tipe: '',
    stk: '',
    owner: '',
    revisi: '',
    status: '',
    terbit: '',
    berlaku: '',
    pic_id: '',
    master_id: '',
    parent_id: '',
    revoked_ids: [],
    related_ids: [],
});

function openAddModal() {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.tipe = ''; // default empty
    picSearchQuery.value = '';
    masterSearchQuery.value = '';
    revokedSearchQuery.value = '';
    relatedSearchQuery.value = '';
    isPicDropdownOpen.value = false;
    isMasterDropdownOpen.value = false;
    isRevokedDropdownOpen.value = false;
    isRelatedDropdownOpen.value = false;
    isModalOpen.value = true;
}

function openEditModal(reg) {
    editingId.value = reg.id;
    
    // Parse date values to YYYY-MM-DD for standard html input compatibility
    let terbitVal = '';
    let berlakuVal = '';
    if (reg.terbit) {
        const d = new Date(reg.terbit);
        terbitVal = d.toISOString().split('T')[0];
    }
    if (reg.berlaku) {
        const d = new Date(reg.berlaku);
        berlakuVal = d.toISOString().split('T')[0];
    }

    form.judul = reg.judul;
    form.nomor = reg.nomor || '';
    form.tipe = reg.tipe;
    form.stk = reg.stk || '';
    form.owner = reg.owner;
    form.revisi = reg.revisi;
    form.status = reg.status || '';
    form.terbit = terbitVal;
    form.berlaku = berlakuVal;
    form.pic_id = reg.pic_id || '';
    form.master_id = reg.master_id || '';
    form.parent_id = reg.parent_id || '';
    form.revoked_ids = reg.revoked_regulations ? reg.revoked_regulations.map(r => r.id) : [];
    form.related_ids = reg.related_regulations ? reg.related_regulations.map(r => r.id) : [];
    
    picSearchQuery.value = '';
    masterSearchQuery.value = '';
    revokedSearchQuery.value = '';
    relatedSearchQuery.value = '';
    isPicDropdownOpen.value = false;
    isMasterDropdownOpen.value = false;
    isRevokedDropdownOpen.value = false;
    isRelatedDropdownOpen.value = false;
    form.clearErrors();
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    editingId.value = null;
    isPicDropdownOpen.value = false;
    isMasterDropdownOpen.value = false;
    isRevokedDropdownOpen.value = false;
    isRelatedDropdownOpen.value = false;
    relatedSearchQuery.value = '';
    form.reset();
    form.clearErrors();
}

function submitForm() {
    if (editingId.value) {
        form.put(route('policy.regulation.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                emit('success', 'Regulasi berhasil diperbarui.');
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Regulasi berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    } else {
        form.post(route('policy.regulation.store'), {
            onSuccess: () => {
                closeModal();
                emit('success', 'Regulasi berhasil ditambahkan.');
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Regulasi berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#821f44',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    }
}

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

const hierarchicalOrganizations = computed(() => {
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
        flattened.push(node);
        node.children.forEach(traverse);
    };
    roots.forEach(traverse);
    return flattened;
});

// Expose modal trigger controls
defineExpose({
    openAddModal,
    openEditModal,
    closeModal
});
</script>

<style scoped>
.animate-scale-up {
    animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
