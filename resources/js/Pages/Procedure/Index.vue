<template>
    <UserLayout title="Procedure">
        <div class="animate-fade-in-up space-y-8">
            <div class="max-w-2xl mr-auto ml-0 bg-white dark:bg-[#1a1a1a] shadow-md border border-slate-900 dark:border-white p-0 rounded-none relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                <!-- Formal Pertamina Document Grid Header -->
                    <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-900 dark:divide-white">
                        <div class="md:col-span-7 flex flex-col divide-y divide-slate-900 dark:divide-white">
                            <div class="px-1.5 py-1 flex items-start gap-1 min-h-[26px]">
                                <span class="font-bold shrink-0 text-[9px] leading-none pt-[1px]">FUNGSI :</span>
                                <span class="font-bold text-[9px] leading-tight text-slate-900 dark:text-white">{{ activeRegulation?.owner }}</span>
                            </div>
                            <div class="px-1.5 py-1 flex items-start gap-1 min-h-[26px]">
                                <span class="font-bold shrink-0 text-[9px] leading-none pt-[1px]">JUDUL :</span>
                                <span class="font-bold text-[9px] leading-tight text-slate-900 dark:text-white">{{ activeRegulation?.judul }}</span>
                            </div>
                        </div>
                        <div class="md:col-span-5 flex flex-col divide-y divide-slate-900 dark:divide-white">
                            <div class="px-1.5 py-1 flex items-center gap-1 min-h-[22px]">
                                <span class="font-bold shrink-0 text-[9px] leading-none">NOMOR :</span>
                                <span class="font-mono font-bold text-[9px] leading-none text-slate-900 dark:text-white">{{ activeRegulation ? `REG-${activeRegulation.id.toString().padStart(4, '0')}` : '-' }}</span>
                            </div>
                            <div class="px-1.5 py-1 flex items-center min-h-[22px]">
                                <div class="flex items-center gap-1 flex-wrap leading-none">
                                    <span class="font-bold shrink-0 text-[9px] leading-none">REVISI KE :</span>
                                    <template v-if="[0, 1, 2, 3, 4].includes(parseInt(activeRegulation?.revisi))">
                                        <span v-for="num in [0, 1, 2, 3, 4]" :key="num" class="inline-flex items-center gap-0.5 mr-0.5">
                                            <span class="w-2.5 h-2.5 border border-slate-900 dark:border-white flex items-center justify-center bg-transparent select-none animate-none leading-none">
                                                <svg v-if="parseInt(activeRegulation?.revisi) === num" class="h-2 w-2 text-slate-900 dark:text-white" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                    <path d="M2.25 6.25L4.75 8.75L9.75 3.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <span class="font-mono text-[9px] leading-none">{{ num }}</span>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span class="w-2.5 h-2.5 border border-slate-900 dark:border-white flex items-center justify-center text-[8px] font-black bg-transparent select-none mr-0.5 leading-none">-</span>
                                        <span class="font-bold text-[9px] leading-none text-slate-900 dark:text-white mr-1">{{ activeRegulation?.revisi || '0' }}</span>
                                    </template>
                                </div>
                            </div>
                            <div class="px-1.5 py-1 flex items-center gap-1 min-h-[22px]">
                                <span class="font-bold shrink-0 text-[9px] leading-none">BERLAKU TMT :</span>
                                <span class="font-bold text-[9px] leading-none text-slate-900 dark:text-white">{{ activeRegulation?.berlaku ? formatDate(activeRegulation.berlaku) : '-' }}</span>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- Actors Section -->
            <section class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-1 rounded-full bg-[#821f44]"></div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                        </h2>
                    </div>
                    <button
                        @click="openAddActorModal"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] hover:bg-[#9c2552] text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-95"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Aktor
                    </button>
                </div>
                
                <div class="px-6">
                    <ol class="list-decimal space-y-3">
                        <li 
                            v-for="actor in actors" 
                            :key="actor.id"
                            class="group text-sm font-semibold text-slate-700 dark:text-slate-300"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <span>
                                    {{ actor.name }}
                                    <span v-if="actor.organization" class="ml-2 text-[10px] text-slate-400 font-normal uppercase italic">
                                        ({{ actor.organization.name }})
                                    </span>
                                </span>
                                <div class="flex items-center gap-3">
                                    <button 
                                        @click="openEditActorModal(actor)"
                                        class="text-[10px] font-bold uppercase tracking-wider text-blue-600 hover:text-blue-800 transition-colors"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        @click="openDeleteActorModal(actor)"
                                        class="text-[10px] font-bold uppercase tracking-wider text-rose-600 hover:text-rose-800 transition-colors"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ol>
                    <div v-if="actors.length === 0" class="py-4 text-sm text-slate-400">
                        Belum ada data unit organisasi terkait.
                    </div>
                </div>
            </section>

            <!-- SOP Section A -->
            <section class="space-y-4">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-5 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        A. Penyusunan RSTI
                    </h2>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                                    <th scope="col" class="px-6 py-4">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                                <tr v-if="sopA.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td colspan="2" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data SOP untuk kategori ini.
                                    </td>
                                </tr>
                                <tr 
                                    v-for="(item, index) in sopA" 
                                    :key="item.id" 
                                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                                >
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 w-16 text-center font-medium">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-900 dark:text-white leading-relaxed whitespace-pre-line">
                                        {{ item.description }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SOP Section B -->
            <section class="space-y-4">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-5 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        B. Reviu & Pembaruan Berkala RSTI
                    </h2>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                                    <th scope="col" class="px-6 py-4">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                                <tr v-if="sopB.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td colspan="2" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data SOP untuk kategori ini.
                                    </td>
                                </tr>
                                <tr 
                                    v-for="(item, index) in sopB" 
                                    :key="item.id" 
                                    class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                                >
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 w-16 text-center font-medium">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-900 dark:text-white leading-relaxed whitespace-pre-line">
                                        {{ item.description }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <!-- Actor Form Modal -->
        <ConfirmationModal
            :show="isActorModalOpen"
            :title="editingActorId ? 'Edit Aktor' : 'Tambah Aktor Baru'"
            :message="editingActorId ? 'Silakan sesuaikan data aktor di bawah ini.' : 'Silakan isi formulir di bawah ini untuk menambahkan aktor baru.'"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            :loading="actorForm.processing"
            @close="closeActorModal"
            @confirm="submitActorForm"
        >
            <div class="mt-4 space-y-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Aktor / Jabatan</label>
                    <input 
                        v-model="actorForm.name"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                        placeholder="Contoh: Manager IT"
                    />
                    <p v-if="actorForm.errors.name" class="text-xs text-rose-500 mt-0.5">{{ actorForm.errors.name }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Organisasi Terkait</label>
                    <select 
                        v-model="actorForm.organization_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                    >
                        <option value="" disabled>-- Pilih Organisasi --</option>
                        <option v-for="org in filteredOrganizations" :key="org.id" :value="org.id">
                            {{ org.jabatan }} ({{ org.name }})
                        </option>
                    </select>
                    <p v-if="actorForm.errors.organization_id" class="text-xs text-rose-500 mt-0.5">{{ actorForm.errors.organization_id }}</p>
                </div>
            </div>
        </ConfirmationModal>

        <!-- Delete Actor Modal -->
        <ConfirmationModal
            :show="isDeleteActorModalOpen"
            title="Hapus Aktor"
            :message="`Apakah Anda yakin ingin menghapus aktor '${selectedActor?.name}'? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="actorForm.processing"
            @close="isDeleteActorModalOpen = false"
            @confirm="submitDeleteActor"
        />
    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    actors: {
        type: Array,
        default: () => [],
    },
    sop: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
    organizations: {
        type: Array,
        default: () => [],
    },
});

const sopA = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter(s => s.tipe === 'A');
});

const sopB = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter(s => s.tipe === 'B');
});

const activeRegulation = computed(() => {
    if (props.regulations && props.regulations.length > 0) {
        return props.regulations[0];
    }

    if (!props.sop || props.sop.length === 0) return null;

    const sopWithRegulation = props.sop.find(item => item.regulation);
    return sopWithRegulation?.regulation || null;
});

const filteredOrganizations = computed(() => {
    return props.organizations.filter(org => org.jabatan && String(org.jabatan).trim() !== '');
});

// ---------------------------------------------------
// ACTOR CRUD
// ---------------------------------------------------
const isActorModalOpen = ref(false);
const isDeleteActorModalOpen = ref(false);
const editingActorId = ref(null);
const selectedActor = ref(null);

const actorForm = useForm({
    name: '',
    organization_id: '',
});

function openAddActorModal() {
    editingActorId.value = null;
    actorForm.reset();
    actorForm.clearErrors();
    isActorModalOpen.value = true;
}

function openEditActorModal(actor) {
    editingActorId.value = actor.id;
    actorForm.name = actor.name;
    actorForm.organization_id = actor.organization_id || '';
    actorForm.clearErrors();
    isActorModalOpen.value = true;
}

function openDeleteActorModal(actor) {
    selectedActor.value = actor;
    isDeleteActorModalOpen.value = true;
}

function closeActorModal() {
    isActorModalOpen.value = false;
    editingActorId.value = null;
    actorForm.reset();
}

function submitActorForm() {
    if (editingActorId.value) {
        actorForm.put(route('policy.procedure.actor.update', editingActorId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeActorModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Aktor berhasil diperbarui.',
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
                    text: 'Aktor baru telah ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    }
}

function submitDeleteActor() {
    if (!selectedActor.value) return;
    actorForm.delete(route('policy.procedure.actor.destroy', selectedActor.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteActorModalOpen.value = false;
            selectedActor.value = null;
            Swal.fire({
                title: 'Dihapus!',
                text: 'Aktor berhasil dihapus.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
    });
}

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
</style>


