<template>
    <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl font-sans animate-fade-in-up">
        <PertaminaDocumentHeader :activeRegulation="activeRegulation" />

        <div class="mt-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-900/10 pb-2 dark:border-white/10">
            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide">
                IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
            </h3>
            <div class="flex items-center gap-3 print:hidden">
                <button
                    @click="openAddActorModal"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 dark:border-white/10 bg-transparent px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-white/5 active:scale-95"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Peran
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
                        <th class="px-6 py-3 w-24 text-center print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-if="actors.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada data peran terkait.</td>
                    </tr>
                    <tr v-for="(actor, index) in actors" :key="actor.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5">
                        <td class="px-6 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-6 py-3 align-middle font-medium text-slate-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                <span>{{ actor.name }}</span>
                                <span 
                                    v-if="actor.tipe" 
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider capitalize border"
                                    :class="{
                                        'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-800/30': actor.tipe === 'fungsi',
                                        'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800/30': actor.tipe === 'organisasi',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800/30': actor.tipe === 'jabatan'
                                    }"
                                >
                                    {{ actor.tipe }}
                                </span>
                            </div>
                        </td>
                        <!-- Kolom Jabatan -->
                        <td class="px-6 py-3 align-middle text-slate-700 dark:text-slate-300">
                            <span v-if="actor.organization">
                                {{ actor.organization.jabatan }} ({{ actor.organization.name || '-' }} - {{ actor.organization.code || '-' }})
                            </span>
                            <span v-else class="text-slate-400 italic">—</span>
                        </td>
                        <td class="px-6 py-2 align-middle text-center print:hidden">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <button
                                    @click="openEditActorModal(actor)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteActor(actor)"
                                    class="w-14 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-2 py-0.5 text-[10px] font-bold text-rose-700 transition hover:bg-rose-50 hover:border-rose-300 dark:border-rose-500/30 dark:bg-[#1a1a1a] dark:text-rose-400 dark:hover:bg-rose-500/10 active:scale-95"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Edit/Add Actor Modal -->
        <ConfirmationModal
            :show="isActorModalOpen"
            :title="editingActorId ? 'Edit Peran' : 'Tambah Peran'"
            :message="editingActorId ? 'Silakan ubah data peran di bawah ini.' : 'Silakan isi data peran baru di bawah ini.'"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            maxWidth="2xl"
            :loading="actorForm.processing"
            @close="closeActorModal"
            @confirm="submitActorForm"
        >
            <div 
                class="mt-4 space-y-4 font-sans text-sm transition-all duration-200"
                :class="(actorType === 'fungsi' && isFunctionDropdownOpen) || (actorType === 'organisasi' && isOrgDropdownOpen) ? 'pb-80' : 'pb-4'"
            >
                <div class="flex flex-col gap-1.5">
                    <label for="actor_name" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Peran</label>
                    <input
                        id="actor_name"
                        v-model="actorForm.name"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        placeholder="Contoh: Direktur Utama"
                        required
                    />
                    <span v-if="actorForm.errors.name" class="text-xs text-red-500 font-medium">{{ actorForm.errors.name }}</span>
                </div>

                <!-- Tipe Peran Selection -->
                <div class="flex flex-col gap-1.5">
                    <label for="actor_type" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tipe Peran</label>
                    <select
                        id="actor_type"
                        v-model="actorType"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        required
                    >
                        <option value="fungsi">Fungsi</option>
                        <option value="organisasi">Organisasi</option>
                        <option value="jabatan">Jabatan</option>
                    </select>
                </div>

                <div v-if="actorType === 'jabatan'" class="flex flex-col gap-1.5">
                    <label for="actor_org" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jabatan</label>
                    <select
                        id="actor_org"
                        v-model="actorForm.organization_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                        required
                    >
                        <option value="">-- Pilih Jabatan --</option>
                        <option v-for="org in filteredOrganizations" :key="org.id" :value="org.id">
                            {{ getLevelPrefix(org) }}{{ org.jabatan }} ({{ org.name || '-' }} - {{ org.code || '-' }})
                        </option>
                    </select>
                    <span v-if="actorForm.errors.organization_id" class="text-xs text-red-500 font-medium">{{ actorForm.errors.organization_id }}</span>
                </div>

                <!-- Tambah Fungsi (Opsional) -->
                <div v-if="actorType === 'fungsi'" class="flex flex-col gap-1.5 relative">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tambah Fungsi (Opsional)</label>
                    
                    <!-- Trigger Button -->
                    <div class="relative">
                        <button 
                            type="button"
                            @click="toggleFunctionDropdown"
                            class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-white/10 flex justify-between items-center"
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
                    <div v-if="isFunctionDropdownOpen" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-80 overflow-y-auto p-2 space-y-2">
                        <!-- Search input inside dropdown -->
                        <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                            <input 
                                type="text" 
                                v-model="functionSearchQuery" 
                                placeholder="Cari fungsi..." 
                                class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                    'w-full text-left px-3 py-2 text-sm rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
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
                            <div v-if="filteredFunctions.length === 0" class="text-center py-4 text-sm text-slate-400">
                                Tidak ada hasil ditemukan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected list display -->
                    <div v-if="actorForm.function_ids && actorForm.function_ids.length > 0" class="mt-1 flex flex-wrap gap-1.5">
                        <span 
                            v-for="id in actorForm.function_ids" 
                            :key="id"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 pl-3 pr-2 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
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
                <div v-if="actorType === 'organisasi'" class="flex flex-col gap-1.5 relative">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tambah Organisasi (Opsional)</label>
                    
                    <!-- Trigger Button -->
                    <div class="relative">
                        <button 
                            type="button"
                            @click="toggleOrgDropdown"
                            class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-left focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-white/10 flex justify-between items-center"
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
                    <div v-if="isOrgDropdownOpen" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl dark:bg-[#1a1a1a] dark:border-white/10 z-40 max-h-80 overflow-y-auto p-2 space-y-2">
                        <!-- Search input inside dropdown -->
                        <div class="sticky top-0 bg-white dark:bg-[#1a1a1a] pb-1.5">
                            <input 
                                type="text" 
                                v-model="orgDropdownSearchQuery" 
                                placeholder="Cari organisasi..." 
                                class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-black/20 dark:text-white dark:border-white/10"
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
                                    'w-full text-left px-3 py-2 text-sm rounded hover:bg-slate-100 dark:hover:bg-white/5 transition flex items-center justify-between',
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
                            <div v-if="filteredOrgDropdown.length === 0" class="text-center py-4 text-sm text-slate-400">
                                Tidak ada hasil ditemukan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected list display -->
                    <div v-if="actorForm.organization_ids && actorForm.organization_ids.length > 0" class="mt-1 flex flex-wrap gap-1.5">
                        <span 
                            v-for="id in actorForm.organization_ids" 
                            :key="id"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 pl-3 pr-2 py-1 text-xs font-medium text-slate-700 dark:bg-white/5 dark:text-slate-300 border border-slate-200 dark:border-white/10"
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

const hasUnsavedChanges = computed(() => false);

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
const editingActorId = ref(null);
const actorType = ref('fungsi'); // 'fungsi' | 'organisasi' | 'jabatan'
const actorForm = useForm({
    name: '',
    tipe: 'fungsi',
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
    editingActorId.value = null;
    actorType.value = 'fungsi';
    actorForm.reset();
    actorForm.clearErrors();
    actorForm.regulation_id = props.activeRegulation?.id || '';
    actorForm.organization_id = '';
    actorForm.function_ids = [];
    actorForm.organization_ids = [];
    actorForm.tipe = 'fungsi';
    isFunctionDropdownOpen.value = false;
    functionSearchQuery.value = '';
    isOrgDropdownOpen.value = false;
    orgDropdownSearchQuery.value = '';
    isActorModalOpen.value = true;
}

function openEditActorModal(actor) {
    editingActorId.value = actor.id;
    actorForm.clearErrors();
    actorForm.name = actor.name || '';
    actorForm.organization_id = actor.organization_id || '';
    actorForm.regulation_id = props.activeRegulation?.id || '';
    actorForm.function_ids = actor.functions ? actor.functions.map(f => f.id) : [];
    actorForm.organization_ids = actor.organizations ? actor.organizations.map(o => o.id) : [];
    
    // Set actorType dynamically based on database column or existing mapping data
    if (actor.tipe) {
        actorType.value = actor.tipe;
    } else if (actor.organization_id) {
        actorType.value = 'jabatan';
    } else if (actor.organizations && actor.organizations.length > 0) {
        actorType.value = 'organisasi';
    } else {
        actorType.value = 'fungsi';
    }
    actorForm.tipe = actorType.value;
    
    isFunctionDropdownOpen.value = false;
    functionSearchQuery.value = '';
    isOrgDropdownOpen.value = false;
    orgDropdownSearchQuery.value = '';
    isActorModalOpen.value = true;
}

function closeActorModal() {
    isActorModalOpen.value = false;
    editingActorId.value = null;
    actorForm.reset();
    isFunctionDropdownOpen.value = false;
    functionSearchQuery.value = '';
    isOrgDropdownOpen.value = false;
    orgDropdownSearchQuery.value = '';
}

function submitActorForm() {
    actorForm.tipe = actorType.value;
    // Clear the unused mapping fields based on selected type
    if (actorType.value === 'fungsi') {
        actorForm.organization_id = '';
        actorForm.organization_ids = [];
    } else if (actorType.value === 'organisasi') {
        actorForm.organization_id = '';
        actorForm.function_ids = [];
    } else if (actorType.value === 'jabatan') {
        actorForm.function_ids = [];
        actorForm.organization_ids = [];
    }

    if (editingActorId.value) {
        actorForm.put(route('policy.procedure.actor.update', editingActorId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeActorModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Peran telah diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    } else {
        actorForm.post(route('policy.procedure.actor.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeActorModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Peran baru telah ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    }
}

function deleteActor(actor) {
    Swal.fire({
        title: 'Hapus Peran',
        text: `Apakah Anda yakin ingin menghapus peran '${actor.name}'?`,
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
    return Promise.resolve();
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
