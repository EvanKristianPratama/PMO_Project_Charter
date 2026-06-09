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
                            @click="openAddCategoryModal"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#821f44] bg-transparent px-4 py-2.5 text-sm font-bold text-[#821f44] hover:bg-[#821f44]/5 transition-all focus:ring-2 focus:ring-[#821f44]/20 active:scale-95 dark:border-[#a83262] dark:text-[#a83262]"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Kategori
                        </button>
                    </div>
                </div>
            </section>

            <!-- 👇 Section Navigation -->
            <section class="print:hidden">
                <ProcedureSectionNavigation />
            </section>

            <section id="procedure-section-fungsi" ref="actorSection" class="space-y-4 scroll-mt-24">
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

            <!-- 👇 Dynamic Categories List -->
            <section id="procedure-section-prosedur" class="space-y-6 scroll-mt-24">
                <div v-if="categories.length === 0" class="rounded-2xl border border-slate-200 bg-white px-6 py-8 text-center text-sm text-slate-400 shadow-sm dark:border-white/10 dark:bg-[#171717] dark:text-slate-500">
                    Belum ada kategori SOP untuk regulasi ini. Silakan klik "Tambah Kategori" di atas.
                </div>

                <div v-for="cat in categories" :key="cat.id" class="space-y-4">
                    <div class="flex items-center justify-between gap-4 px-1">
                        <div class="flex items-center gap-3">
                            <div class="h-5 w-1 rounded-full bg-blue-600"></div>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                {{ cat.tipe }}
                            </h2>
                            <div class="flex items-center gap-2 print:hidden ml-2">
                                <button
                                    @click="openEditCategoryModal(cat)"
                                    class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    Edit
                                </button>
                                <span class="text-slate-300 dark:text-white/10 text-[10px]">|</span>
                                <button
                                    @click="openDeleteCategoryModal(cat)"
                                    class="text-[10px] font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <button
                            @click="openAddSopModal(cat.id)"
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
                                    <tr v-if="getSopsForCategory(cat.id).length === 0" class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                        <td colspan="3" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                            Belum ada data SOP untuk kategori ini.
                                        </td>
                                    </tr>
                                    <tr v-for="(item, index) in getSopsForCategory(cat.id)" :key="item.id" class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
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
                </div>
            </section>

            <section id="procedure-section-diagram" class="space-y-4 scroll-mt-24">
                <div class="flex items-center gap-2 px-1">
                    <div class="h-5 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        DIAGRAM ALIR RSTI
                    </h2>
                </div>
                <FlowChart :actors="actors" :sops="flowChartSops" :categories="categories" />
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

        <!-- Category Form Modal -->
        <ConfirmationModal
            :show="isCategoryModalOpen"
            :title="editingCategoryId ? 'Edit Kategori SOP' : 'Tambah Kategori SOP Baru'"
            :message="editingCategoryId ? 'Silakan sesuaikan nama kategori di bawah ini.' : 'Silakan isi formulir di bawah ini untuk menambahkan kategori SOP baru.'"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            :loading="categoryForm.processing"
            @close="closeCategoryModal"
            @confirm="submitCategoryForm"
        >
            <div class="mt-4 space-y-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Kategori SOP</label>
                    <input
                        v-model="categoryForm.tipe"
                        type="text"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                        placeholder="Contoh: A. Penyusunan RSTI"
                    />
                    <p v-if="categoryForm.errors.tipe" class="mt-0.5 text-xs text-rose-500">{{ categoryForm.errors.tipe }}</p>
                </div>
            </div>
        </ConfirmationModal>

        <!-- Delete Category Modal -->
        <ConfirmationModal
            :show="isDeleteCategoryModalOpen"
            title="Hapus Kategori SOP"
            :message="`Apakah Anda yakin ingin menghapus kategori '${selectedCategory?.tipe}' beserta seluruh SOP di dalamnya? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus"
            cancel-text="Batal"
            type="danger"
            :loading="categoryForm.processing"
            @close="isDeleteCategoryModalOpen = false"
            @confirm="submitDeleteCategory"
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
                    <p v-if="sopForm.errors.category_id" class="mt-0.5 text-xs text-rose-500">{{ sopForm.errors.category_id }}</p>
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
import { computed, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import FlowChart from '@/Components/Procedure/FlowChart.vue';
import PertaminaDocumentHeader from '@/Components/Regulation/PertaminaDocumentHeader.vue';
import ProcedureSectionNavigation from '@/Components/Regulation/ProcedureSectionNavigation.vue';
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
    categories: {
        type: Array,
        default: () => [],
    },
});

const selectedRegulationId = ref(props.selectedRegulationId);
watch(() => props.selectedRegulationId, (newId) => {
    selectedRegulationId.value = newId;
});
const actorSection = ref(null);

function getSopsForCategory(categoryId) {
    if (!props.sop) return [];
    return props.sop.filter((s) => Number(s.category_id) === Number(categoryId));
}

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
    regulation_id: '',
});

function openAddActorModal() {
    editingActorId.value = null;
    actorForm.reset();
    actorForm.clearErrors();
    actorForm.regulation_id = activeRegulation.value?.id || '';
    isActorModalOpen.value = true;
}

function openEditActorModal(actor) {
    editingActorId.value = actor.id;
    actorForm.name = actor.name;
    actorForm.organization_id = actor.organization_id || '';
    actorForm.regulation_id = actor.regulation_id || activeRegulation.value?.id || '';
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
                    toast: true,
                    position: 'top-end',
                });
            }
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
                    toast: true,
                    position: 'top-end',
                });
            }
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
                toast: true,
                position: 'top-end',
            });
        }
    });
}

// ---------------------------------------------------
// CATEGORY CRUD
// ---------------------------------------------------
const isCategoryModalOpen = ref(false);
const isDeleteCategoryModalOpen = ref(false);
const editingCategoryId = ref(null);
const selectedCategory = ref(null);

const categoryForm = useForm({
    tipe: '',
    regulation_id: '',
});

function openAddCategoryModal() {
    editingCategoryId.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
    categoryForm.regulation_id = activeRegulation.value?.id || '';
    isCategoryModalOpen.value = true;
}

function openEditCategoryModal(category) {
    editingCategoryId.value = category.id;
    categoryForm.tipe = category.tipe;
    categoryForm.regulation_id = category.regulation_id;
    categoryForm.clearErrors();
    isCategoryModalOpen.value = true;
}

function openDeleteCategoryModal(category) {
    selectedCategory.value = category;
    isDeleteCategoryModalOpen.value = true;
}

function closeCategoryModal() {
    isCategoryModalOpen.value = false;
    editingCategoryId.value = null;
    categoryForm.reset();
}

function submitCategoryForm() {
    if (editingCategoryId.value) {
        categoryForm.put(route('policy.procedure.category.update', editingCategoryId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeCategoryModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kategori SOP berhasil diperbarui.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            }
        });
    } else {
        categoryForm.post(route('policy.procedure.category.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeCategoryModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kategori SOP baru telah ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            }
        });
    }
}

function submitDeleteCategory() {
    if (!selectedCategory.value) return;
    categoryForm.delete(route('policy.procedure.category.destroy', selectedCategory.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteCategoryModalOpen.value = false;
            selectedCategory.value = null;
            Swal.fire({
                title: 'Dihapus!',
                text: 'Kategori SOP berhasil dihapus.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        }
    });
}

// ---------------------------------------------------
// SOP CRUD
// ---------------------------------------------------
const isSopModalOpen = ref(false);
const isDeleteSopModalOpen = ref(false);
const editingSopId = ref(null);
const selectedSop = ref(null);
const activeCategoryId = ref(null);

const sopForm = useForm({
    category_id: '',
    description: '',
});

const activeCategoryForSop = computed(() => {
    return props.categories.find(c => Number(c.id) === Number(activeCategoryId.value)) || null;
});

const activeSopTitle = computed(() => {
    return activeCategoryForSop.value ? activeCategoryForSop.value.tipe : '';
});

function openAddSopModal(categoryId) {
    activeCategoryId.value = categoryId;
    editingSopId.value = null;
    sopForm.reset();
    sopForm.clearErrors();
    sopForm.category_id = categoryId;
    isSopModalOpen.value = true;
}

function openEditSopModal(item) {
    activeCategoryId.value = item.category_id;
    editingSopId.value = item.id;
    sopForm.category_id = item.category_id;
    sopForm.description = item.description || '';
    sopForm.clearErrors();
    isSopModalOpen.value = true;
}

function openDeleteSopModal(item) {
    selectedSop.value = item;
    activeCategoryId.value = item?.category_id;
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
                    toast: true,
                    position: 'top-end',
                });
            }
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
                    toast: true,
                    position: 'top-end',
                });
            }
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
                toast: true,
                position: 'top-end',
            });
        }
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
