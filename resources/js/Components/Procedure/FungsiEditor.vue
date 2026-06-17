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
                    @click="addActor"
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
                        <th class="px-6 py-3 w-24 text-center print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    <tr v-if="actors.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada data aktor terkait.</td>
                    </tr>
                    <tr v-for="(actor, index) in actors" :key="actor.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5">
                        <td class="px-6 py-3 text-center align-middle font-medium text-slate-500 dark:text-slate-400">{{ index + 1 }}</td>
                        <td class="px-6 py-2 align-middle">
                            <input
                                v-if="actorLocal[actor.id]"
                                v-model="actorLocal[actor.id].name"
                                @input="markModified(actor.id)"
                                type="text"
                                class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10"
                                placeholder="Nama Aktor / Jabatan"
                            />
                        </td>
                        <td class="px-6 py-2 align-middle relative">
                            <div v-if="actorLocal[actor.id]" class="relative">
                                <!-- Trigger Button -->
                                <button 
                                    type="button"
                                    @click="toggleActorDropdown(actor.id)"
                                    class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] hover:border-slate-300 dark:hover:border-white/10 text-left flex justify-between items-center text-[11px]"
                                >
                                    <span class="truncate pr-2">
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
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';

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

function addActor() {
    const defaultOrgId = filteredOrganizations.value[0]?.id || '';
    if (!defaultOrgId) {
        Swal.fire({ title: 'Error', text: 'Tidak ada organisasi terkait yang valid.', icon: 'error' });
        return;
    }
    router.post(
        route('policy.procedure.actor.store'),
        {
            name: 'Aktor Baru',
            organization_id: defaultOrgId,
            regulation_id: props.activeRegulation?.id || '',
        },
        { preserveScroll: true }
    );
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
