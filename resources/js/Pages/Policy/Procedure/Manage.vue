<template>
    <UserLayout title="Kelola Procedure">
        <div class="animate-fade-in-up mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8 print:m-0 print:px-0">
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">
                            {{ activeRegulation?.judul || 'Belum ada regulasi aktif' }}
                        </p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Kelola Dokumen
                        </h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            :href="route('policy.procedure.index', activeRegulation ? { regulation_id: activeRegulation.id } : {})"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Lihat Dokumen
                        </Link>
                        <button
                            @click="scrollToActors"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Data
                        </button>
                    </div>
                </div>
            </section>

            <section ref="actorSection" class="space-y-4">
                <div class="flex items-center justify-between gap-4 px-1">
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-1 rounded-full bg-[#821f44]"></div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                        </h2>
                    </div>
                    <button
                        @click="openAddActorModal"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-[#9c2552] active:scale-95"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Actor
                    </button>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-6 py-3 w-20 text-center">No</th>
                                    <th scope="col" class="px-6 py-3">Fungsi/ Unit Organisasi/Jabatan terkait</th>
                                    <th scope="col" class="px-6 py-3">Jabatan</th>
                                    <th scope="col" class="px-6 py-3 w-32 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                                <tr v-if="actors.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td colspan="4" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data unit organisasi terkait.
                                    </td>
                                </tr>
                                <tr v-for="(actor, index) in actors" :key="actor.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td class="px-6 py-2 w-20 text-center font-medium text-slate-500 dark:text-slate-400">
                                        <span class="inline-block pl-2">{{ index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-2 font-medium leading-snug text-slate-900 dark:text-white">
                                        {{ actor.name }}
                                    </td>
                                    <td class="px-6 py-2 leading-snug text-slate-700 dark:text-slate-300">
                                        <template v-if="actor.organization">
                                            <div v-if="actor.organization.jabatan" class="font-medium text-slate-900 dark:text-white">
                                                {{ actor.organization.jabatan }}
                                            </div>
                                            <span v-else class="text-slate-400 dark:text-slate-500">-</span>
                                        </template>
                                        <span v-else class="text-slate-400 dark:text-slate-500">-</span>
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button
                                                @click="openEditActorModal(actor)"
                                                class="text-[9px] font-bold uppercase tracking-wider text-blue-600 transition-colors hover:text-blue-800"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="openDeleteActorModal(actor)"
                                                class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between gap-4 px-1">
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-1 rounded-full bg-blue-600"></div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            A. Penyusunan RSTI
                        </h2>
                    </div>
                    <button
                        @click="openAddSopModal('A')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-95"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add SOP
                    </button>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-3 py-3 w-10 text-right align-middle">No</th>
                                    <th scope="col" class="px-6 py-3">Deskripsi</th>
                                    <th scope="col" class="px-6 py-3 w-32 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                                <tr v-if="sopA.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td colspan="3" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data SOP untuk kategori ini.
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in sopA" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td class="px-3 py-2 w-10 text-right align-middle font-medium">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-pre-line break-words leading-[1.15] text-slate-900 dark:text-white">
                                        {{ item.description }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button
                                                @click="openEditSopModal(item)"
                                                class="text-[9px] font-bold uppercase tracking-wider text-blue-600 transition-colors hover:text-blue-800"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="openDeleteSopModal(item)"
                                                class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between gap-4 px-1">
                    <div class="flex items-center gap-2">
                        <div class="h-5 w-1 rounded-full bg-emerald-600"></div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            B. Reviu & Pembaruan Berkala RSTI
                        </h2>
                    </div>
                    <button
                        @click="openAddSopModal('B')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-95"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add SOP
                    </button>
                </div>
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-3 py-3 w-10 text-right align-middle">No</th>
                                    <th scope="col" class="px-6 py-3">Deskripsi</th>
                                    <th scope="col" class="px-6 py-3 w-32 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                                <tr v-if="sopB.length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td colspan="3" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                        Belum ada data SOP untuk kategori ini.
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in sopB" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                    <td class="px-3 py-2 w-10 text-right align-middle font-medium">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-pre-line break-words leading-[1.15] text-slate-900 dark:text-white">
                                        {{ tightSopText(item.description) }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button
                                                @click="openEditSopModal(item)"
                                                class="text-[9px] font-bold uppercase tracking-wider text-blue-600 transition-colors hover:text-blue-800"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="openDeleteSopModal(item)"
                                                class="text-[9px] font-bold uppercase tracking-wider text-rose-600 transition-colors hover:text-rose-800"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-5 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        DIAGRAM ALIR RSTI
                    </h2>
                </div>
                <FlowChart :actors="actors" :sops="flowChartSops" />
            </section>
        </div>

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
                    <p v-if="actorForm.errors.name" class="mt-0.5 text-xs text-rose-500">{{ actorForm.errors.name }}</p>
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
                    <p v-if="actorForm.errors.organization_id" class="mt-0.5 text-xs text-rose-500">
                        {{ actorForm.errors.organization_id }}
                    </p>
                </div>
            </div>
        </ConfirmationModal>

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

        <ConfirmationModal
            :show="isSopModalOpen"
            :title="editingSopId ? 'Edit SOP' : `Tambah SOP ${activeSopTitle}`"
            :message="editingSopId ? 'Silakan sesuaikan deskripsi SOP di bawah ini.' : `Silakan isi deskripsi untuk ${activeSopTitle}.`"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            max-width="lg"
            :loading="sopForm.processing"
            @close="closeSopModal"
            @confirm="submitSopForm"
        >
            <div class="mt-4 space-y-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori</label>
                    <input
                        :value="activeSopTitle"
                        type="text"
                        disabled
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300"
                    />
                    <p v-if="sopForm.errors.tipe" class="mt-0.5 text-xs text-rose-500">{{ sopForm.errors.tipe }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi SOP</label>
                    <textarea
                        v-model="sopForm.description"
                        rows="6"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                        placeholder="Tuliskan deskripsi aktivitas SOP"
                    ></textarea>
                    <p v-if="sopForm.errors.description" class="mt-0.5 text-xs text-rose-500">
                        {{ sopForm.errors.description }}
                    </p>
                </div>
            </div>
        </ConfirmationModal>

        <ConfirmationModal
            :show="isDeleteSopModalOpen"
            title="Hapus SOP"
            :message="`Apakah Anda yakin ingin menghapus SOP '${shortSopDescription(selectedSop?.description)}'? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="sopForm.processing"
            @close="isDeleteSopModalOpen = false"
            @confirm="submitDeleteSop"
        />

    </UserLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import FlowChart from '@/Components/Procedure/FlowChart.vue';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';
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
    flowChartSops: {
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
    selectedRegulationId: {
        type: Number,
        default: null,
    },
});

const selectedRegulationId = ref(props.selectedRegulationId);
const actorSection = ref(null);

const sopA = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter((s) => s.tipe === 'A');
});

const sopB = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter((s) => s.tipe === 'B');
});

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find((r) => r.id === selectedRegulationId.value) || props.regulations[0] || null;
});

const filteredOrganizations = computed(() => {
    return props.organizations.filter((org) => org.jabatan && String(org.jabatan).trim() !== '');
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

// ---------------------------------------------------
// SOP CRUD
// ---------------------------------------------------
const isSopModalOpen = ref(false);
const isDeleteSopModalOpen = ref(false);
const editingSopId = ref(null);
const selectedSop = ref(null);
const activeSopType = ref('A');

const sopForm = useForm({
    regulation_id: '',
    tipe: 'A',
    description: '',
});

const activeSopTitle = computed(() => {
    return activeSopType.value === 'B'
        ? 'B. Reviu & Pembaruan Berkala RSTI'
        : 'A. Penyusunan RSTI';
});

function openAddSopModal(tipe) {
    activeSopType.value = tipe;
    editingSopId.value = null;
    sopForm.reset();
    sopForm.clearErrors();
    sopForm.regulation_id = activeRegulation.value?.id || '';
    sopForm.tipe = tipe;
    isSopModalOpen.value = true;
}

function openEditSopModal(item) {
    activeSopType.value = item.tipe || 'A';
    editingSopId.value = item.id;
    sopForm.regulation_id = item.regulation_id || activeRegulation.value?.id || '';
    sopForm.tipe = item.tipe || 'A';
    sopForm.description = item.description || '';
    sopForm.clearErrors();
    isSopModalOpen.value = true;
}

function openDeleteSopModal(item) {
    selectedSop.value = item;
    activeSopType.value = item?.tipe || 'A';
    isDeleteSopModalOpen.value = true;
}

function closeSopModal() {
    isSopModalOpen.value = false;
    editingSopId.value = null;
    sopForm.reset();
}

function submitSopForm() {
    if (editingSopId.value) {
        sopForm.put(route('policy.procedure.sop.update', editingSopId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeSopModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'SOP berhasil diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    } else {
        sopForm.post(route('policy.procedure.sop.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeSopModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'SOP baru telah ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    }
}

function submitDeleteSop() {
    if (!selectedSop.value) return;
    sopForm.delete(route('policy.procedure.sop.destroy', selectedSop.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteSopModalOpen.value = false;
            selectedSop.value = null;
            Swal.fire({
                title: 'Dihapus!',
                text: 'SOP berhasil dihapus.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
    });
}

function scrollToActors() {
    actorSection.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function tightSopText(text) {
    if (!text) return '';
    return String(text)
        .replace(/\r\n/g, '\n')
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line !== '')
        .join('\n');
}

function shortSopDescription(text) {
    if (!text) return '-';
    const normalized = tightSopText(text).replace(/\n/g, ' ');
    return normalized.length > 80 ? `${normalized.slice(0, 80)}...` : normalized;
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
