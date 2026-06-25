<template>
    <UserLayout title="Regulation">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">List of Policy, Standard, and Procedure</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Regulation</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            @click="openAddModal"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Regulasi
                        </button>
                    </div>
                </div>
            </section>

            <!-- Controls: View Modes & Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Toggles / Tabs for View Modes -->
                <div class="flex items-center gap-1.5 rounded-xl bg-slate-100 p-1 dark:bg-white/5 w-fit">
                    <button
                        @click="activeViewMode = 'document'"
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                            activeViewMode === 'document'
                                ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                                : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Hirarki Dokumen
                    </button>
                    <button
                        @click="activeViewMode = 'organization'"
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-bold transition-all duration-200 active:scale-95',
                            activeViewMode === 'organization'
                                ? 'bg-[#821f44] text-white shadow-md shadow-[#821f44]/20'
                                : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Hirarki Organisasi
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex items-center gap-2">
                    <!-- Filter by Status (Multi-select Dropdown) -->
                    <div class="relative inline-block text-left">
                        <button 
                            type="button"
                            @click="isStatusDropdownOpen = !isStatusDropdownOpen"
                            class="inline-flex items-center justify-between gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 cursor-pointer min-w-[150px] text-left select-none active:scale-[0.98] transition-transform duration-100"
                        >
                            <span class="truncate">
                                {{ selectedStatuses.length === 0 ? 'Semua Status' : selectedStatuses.join(', ') }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-slate-400 shrink-0">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div v-if="isStatusDropdownOpen" class="fixed inset-0 z-30" @click="isStatusDropdownOpen = false"></div>

                        <div v-if="isStatusDropdownOpen" class="absolute left-0 mt-1 w-44 rounded-lg bg-white border border-slate-200 shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 z-40 p-2 space-y-1">
                            <label 
                                v-for="status in uniqueStatuses" 
                                :key="status"
                                class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-white/5 transition duration-150 cursor-pointer select-none text-xs text-slate-700 dark:text-slate-300 font-medium"
                            >
                                <input 
                                    type="checkbox" 
                                    :value="status" 
                                    v-model="selectedStatuses"
                                    class="rounded border-slate-300 text-[#821f44] focus:ring-[#821f44] dark:border-white/10 dark:bg-black/20"
                                />
                                <span>{{ status }}</span>
                            </label>
                            <div v-if="selectedStatuses.length > 0" class="border-t border-slate-100 dark:border-white/5 pt-1.5 mt-1 flex justify-end">
                                <button 
                                    type="button" 
                                    @click="selectedStatuses = []" 
                                    class="text-[10px] text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-bold"
                                >
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter by Akses Role -->
                    <div class="relative">
                        <select
                            v-model="selectedAksesRoleId"
                            class="appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-8 py-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[200px]"
                        >
                            <option value="">Semua Akses Role</option>
                            <option
                                v-for="role in uniqueAksesRoles"
                                :key="role.id"
                                :value="role.id"
                            >
                                {{ role.name }} ({{ role.code }})
                            </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Expand Level Filter (only for document hierarchy) -->
                    <div v-if="activeViewMode === 'document'" class="relative">
                        <select
                            v-model="expandLevel"
                            class="appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-8 py-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10 transition-all hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer min-w-[130px]"
                        >
                            <option value="custom" disabled>Expand Level...</option>
                            <option value="0">Collapse All (Level 0)</option>
                            <option v-for="depth in maxDepth + 1" :key="depth" :value="depth">
                                Level {{ depth }}
                            </option>
                            <option value="all">Expand All</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regulations Table Components -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-3 py-3 w-10 text-center border-r border-b border-slate-200 dark:border-white/10">No</th>
                                <th scope="col" class="px-3 py-3 text-center border-r border-b border-slate-200 dark:border-white/10">Judul</th>
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Nomor</th>
                                <th scope="col" class="px-3 py-3 w-28 border-r border-b border-slate-200 dark:border-white/10">Tipe</th>
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Pemilik Dokumen</th>
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Akses Role / Pemilik Dokumen</th>
                                <th scope="col" class="px-3 py-3 text-center w-24 border-r border-b border-slate-200 dark:border-white/10">Status</th>
                                <th scope="col" class="px-3 py-3 text-center w-16 border-r border-b border-slate-200 dark:border-white/10">Revisi</th>
                                <th scope="col" class="px-3 py-3 w-24 border-r border-b border-slate-200 dark:border-white/10">Berlaku</th>
                                <th scope="col" class="px-3 py-3 text-center w-24 border-b border-slate-200 dark:border-white/10">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="dark:bg-transparent">
                            <!-- Document Hierarchy Mode -->
                            <template v-if="activeViewMode === 'document'">
                                <DocumentHierarki
                                    :regulations="filteredRegulations"
                                    :formatDate="formatDate"
                                    :expand-level="expandLevel"
                                    @update:expand-level="expandLevel = $event"
                                    @detail="handleDetailClick"
                                    @edit="openEditModal"
                                    @delete="deleteRegulation"
                                />
                            </template>

                            <!-- Organization Hierarchy Mode -->
                            <template v-if="activeViewMode === 'organization'">
                                <OrganizationHierarki
                                    :regulations="filteredRegulations"
                                    :organizations="organizations"
                                    :formatDate="formatDate"
                                    @detail="handleDetailClick"
                                    @edit="openEditModal"
                                    @delete="deleteRegulation"
                                />
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Manage Regulation Modal Component -->
            <ManageRegulation
                ref="manageRegulationModal"
                :regulations="regulations"
                :organizations="organizations"
            />
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';
import DocumentHierarki from '@/Components/ITOperatingModel/Regulation/DocumentHierarki.vue';
import OrganizationHierarki from '@/Components/ITOperatingModel/Regulation/OrganizationHierarki.vue';
import ManageRegulation from '@/Components/ITOperatingModel/Regulation/ManageRegulation.vue';

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

// View mode state
const activeViewMode = ref('document'); // 'document', 'organization'

// Filters
const selectedAksesRoleId = ref('');
const selectedStatuses = ref([]);
const isStatusDropdownOpen = ref(false);
const expandLevel = ref('all');

// Compute depth of a regulation
const getDepth = (id) => {
    let depth = 0;
    let currentId = id;
    const visited = new Set();
    while (currentId && !visited.has(currentId)) {
        visited.add(currentId);
        const node = props.regulations.find(r => r.id === currentId);
        if (node?.parent_id) {
            depth++;
            currentId = node.parent_id;
        } else {
            break;
        }
    }
    return depth;
};

const maxDepth = computed(() => {
    let max = 0;
    props.regulations.forEach(item => {
        const d = getDepth(item.id);
        if (d > max) max = d;
    });
    return max;
});

const uniqueStatuses = computed(() => {
    const statusSet = new Set();
    props.regulations.forEach(reg => {
        if (reg.status) {
            statusSet.add(reg.status);
        }
    });
    return Array.from(statusSet).sort((a, b) => a.localeCompare(b));
});

const uniqueAksesRoles = computed(() => {
    const rolesMap = new Map();
    props.regulations.forEach(reg => {
        if (reg.master_id && reg.master) {
            rolesMap.set(reg.master_id, {
                id: reg.master_id,
                name: reg.master.jabatan || reg.master.name,
                code: reg.master.code
            });
        }
    });
    return Array.from(rolesMap.values()).sort((a, b) => a.name.localeCompare(b.name));
});

const filteredRegulations = computed(() => {
    let result = props.regulations;

    if (selectedAksesRoleId.value) {
        const targetId = Number(selectedAksesRoleId.value);

        // Helper: collect all descendant IDs (recursively) for a given parent ID
        const collectDescendantIds = (parentId, allRegs) => {
            const ids = new Set();
            const queue = [parentId];
            while (queue.length > 0) {
                const current = queue.shift();
                allRegs.forEach(reg => {
                    if (reg.parent_id === current && !ids.has(reg.id)) {
                        ids.add(reg.id);
                        queue.push(reg.id);
                    }
                });
            }
            return ids;
        };

        // Find documents whose access role matches the selected role
        const matchedRegs = result.filter(reg => reg.master_id === targetId);

        // Collect all descendant IDs for each matched document
        const allIncludedIds = new Set(matchedRegs.map(r => r.id));
        matchedRegs.forEach(reg => {
            const descendantIds = collectDescendantIds(reg.id, props.regulations);
            descendantIds.forEach(id => allIncludedIds.add(id));
        });

        result = props.regulations.filter(reg => allIncludedIds.has(reg.id));
    }

    if (selectedStatuses.value && selectedStatuses.value.length > 0) {
        const allRegs = props.regulations;

        // Helper: collect all descendant IDs recursively
        const collectDescendantIds = (parentId) => {
            const ids = new Set();
            const queue = [parentId];
            while (queue.length > 0) {
                const current = queue.shift();
                allRegs.forEach(reg => {
                    if (reg.parent_id === current && !ids.has(reg.id)) {
                        ids.add(reg.id);
                        queue.push(reg.id);
                    }
                });
            }
            return ids;
        };

        // Helper: collect all ancestor IDs (walk up parent chain)
        const collectAncestorIds = (reg) => {
            const ids = new Set();
            let current = reg;
            while (current.parent_id) {
                const parent = allRegs.find(r => r.id === current.parent_id);
                if (!parent) break;
                ids.add(parent.id);
                current = parent;
            }
            return ids;
        };

        // Step 1: find all docs within current result that match the status
        const matchedByStatus = result.filter(reg => reg.status && selectedStatuses.value.includes(reg.status));

        const includedIds = new Set();

        matchedByStatus.forEach(reg => {
            // Include the matched doc itself
            includedIds.add(reg.id);

            // Include all ancestors so hierarchy is visible
            const ancestorIds = collectAncestorIds(reg);
            ancestorIds.forEach(id => includedIds.add(id));

            // Include all descendants of the matched doc
            const descendantIds = collectDescendantIds(reg.id);
            descendantIds.forEach(id => includedIds.add(id));
        });

        result = result.filter(reg => includedIds.has(reg.id));
    }

    return result;
});

const manageRegulationModal = ref(null);

function openAddModal() {
    manageRegulationModal.value?.openAddModal();
}

function openEditModal(reg) {
    manageRegulationModal.value?.openEditModal(reg);
}

// Actions
// ─────────────────────────────────────────────────────────────────────────────
function handleDetailClick(reg) {
    const targetRoute = String(reg.tipe || '').toLowerCase() === 'procedure'
        ? 'policy.procedure.index'
        : 'policy.general.index';

    router.visit(route(targetRoute, { regulation_id: reg.id }));
}

function deleteRegulation(reg) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Regulasi: "${reg.judul}". Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('policy.regulation.destroy', reg.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Regulasi berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#821f44',
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal menghapus data.',
                        icon: 'error',
                        confirmButtonColor: '#821f44'
                    });
                }
            });
        }
    });
}

// ---------------------------------------------------
// DATE FORMATTER HELPER
// ---------------------------------------------------
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return dateString;
        return d.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
