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

                <!-- Filter by Akses Role -->
                <div class="flex items-center gap-2">
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
                                <th scope="col" class="px-3 py-3 border-r border-b border-slate-200 dark:border-white/10">Akses Role / Pemilik Dokumen (Internal)</th>
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

// Filter by Akses Role
const selectedAksesRoleId = ref('');

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
    if (!selectedAksesRoleId.value) {
        return props.regulations;
    }
    const targetId = Number(selectedAksesRoleId.value);
    return props.regulations.filter(reg => reg.master_id === targetId);
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
