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

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
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
                        <td class="px-6 py-2 align-middle">
                            <select
                                v-if="actorLocal[actor.id]"
                                v-model="actorLocal[actor.id].organization_id"
                                @change="markModified(actor.id)"
                                class="w-full bg-transparent px-2 py-1 text-slate-900 dark:text-white border border-transparent rounded focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44] focus:bg-white dark:focus:bg-[#1e1e1e] hover:border-slate-300 dark:hover:border-white/10"
                            >
                                <option value="" disabled>-- Pilih Organisasi --</option>
                                <option v-for="org in filteredOrganizations" :key="org.id" :value="org.id">
                                    {{ org.jabatan }} ({{ org.name }})
                                </option>
                            </select>
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
import { ref, computed, watch } from 'vue';
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

// ─── Computed ─────────────────────────────────────────────────────────────────
const filteredOrganizations = computed(() =>
    props.organizations.filter(org => org.jabatan && String(org.jabatan).trim() !== '')
);

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
