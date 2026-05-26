<template>
    <UserLayout title="Procedure">
        <template v-if="isManagePage">
            <div class="animate-fade-in-up space-y-8">
            <div class="print:hidden">
                <PertaminaDocumentHeader :activeRegulation="activeRegulation" />
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
                <button @click="openAddActorModal"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#821f44] hover:bg-[#9c2552] text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-95">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Actor
                </button>
            </div>

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-20 text-center">No</th>
                                <th scope="col" class="px-6 py-3">Fungsi/ Unit Organisasi/Jabatan terkait</th>
                                <th scope="col" class="px-6 py-3">Jabatan</th>
                                <th scope="col" class="px-6 py-3 w-32 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                            <tr v-if="actors.length === 0"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td colspan="4" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data unit organisasi terkait.
                                </td>
                            </tr>
                            <tr v-for="(actor, index) in actors" :key="actor.id"
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td class="px-6 py-2 w-20 text-center font-medium text-slate-500 dark:text-slate-400">
                                    <span class="inline-block pl-2">{{ index + 1 }}</span>
                                </td>
                                <td class="px-6 py-2 text-slate-900 dark:text-white font-medium leading-snug">
                                    {{ actor.name }}
                                </td>
                                <td class="px-6 py-2 text-slate-700 dark:text-slate-300 leading-snug">
                                    <template v-if="actor.organization">
                                        <div v-if="actor.organization.jabatan"
                                            class="font-medium text-slate-900 dark:text-white">
                                            {{ actor.organization.jabatan }}
                                        </div>
                                        <span v-else class="text-slate-400 dark:text-slate-500">-</span>
                                    </template>
                                    <span v-else class="text-slate-400 dark:text-slate-500">-</span>
                                </td>
                                <td class="px-6 py-2 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="openEditActorModal(actor)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-blue-600 hover:text-blue-800 transition-colors">
                                            Edit
                                        </button>
                                        <button @click="openDeleteActorModal(actor)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-rose-600 hover:text-rose-800 transition-colors">
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

        <!-- SOP Section A -->
        <section class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center gap-2">
                    <div class="h-5 w-1 rounded-full bg-blue-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        A. Penyusunan RSTI
                    </h2>
                </div>
                <button @click="openAddSopModal('A')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-95">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add SOP
                </button>
            </div>
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-3 py-3 w-10 text-right align-middle">No</th>
                                <th scope="col" class="px-6 py-3">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 w-32 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                            <tr v-if="sopA.length === 0"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td colspan="3" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data SOP untuk kategori ini.
                                </td>
                            </tr>
                            <tr v-for="(item, index) in sopA" :key="item.id"
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td class="px-3 py-2 w-10 text-right align-middle font-medium">
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-6 py-2 text-slate-900 dark:text-white leading-[1.15] whitespace-pre-line break-words">
                                    {{ item.description }}
                                </td>
                                <td class="px-6 py-2 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="openEditSopModal(item)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-blue-600 hover:text-blue-800 transition-colors">
                                            Edit
                                        </button>
                                        <button @click="openDeleteSopModal(item)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-rose-600 hover:text-rose-800 transition-colors">
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

        <!-- SOP Section B -->
        <section class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center gap-2">
                    <div class="h-5 w-1 rounded-full bg-emerald-600"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        B. Reviu & Pembaruan Berkala RSTI
                    </h2>
                </div>
                <button @click="openAddSopModal('B')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-95">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add SOP
                </button>
            </div>
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                        <thead
                            class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-3 py-3 w-10 text-right align-middle">No</th>
                                <th scope="col" class="px-6 py-3">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 w-32 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10 dark:bg-transparent">
                            <tr v-if="sopB.length === 0"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td colspan="3" class="px-6 py-7 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data SOP untuk kategori ini.
                                </td>
                            </tr>
                            <tr v-for="(item, index) in sopB" :key="item.id"
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150">
                                <td class="px-3 py-2 w-10 text-right align-middle font-medium">
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-6 py-2 text-slate-900 dark:text-white leading-[1.15] whitespace-pre-line break-words">
                                    {{ tightSopText(item.description) }}
                                </td>
                                <td class="px-6 py-2 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="openEditSopModal(item)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-blue-600 hover:text-blue-800 transition-colors">
                                            Edit
                                        </button>
                                        <button @click="openDeleteSopModal(item)"
                                            class="text-[9px] font-bold uppercase tracking-wider text-rose-600 hover:text-rose-800 transition-colors">
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

        <!-- Actor Form Modal -->
        <ConfirmationModal :show="isActorModalOpen" :title="editingActorId ? 'Edit Aktor' : 'Tambah Aktor Baru'"
            :message="editingActorId ? 'Silakan sesuaikan data aktor di bawah ini.' : 'Silakan isi formulir di bawah ini untuk menambahkan aktor baru.'"
            confirm-text="Simpan" cancel-text="Batal" type="info" :loading="actorForm.processing"
            @close="closeActorModal" @confirm="submitActorForm">
            <div class="mt-4 space-y-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Aktor / Jabatan</label>
                    <input v-model="actorForm.name" type="text"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                        placeholder="Contoh: Manager IT" />
                    <p v-if="actorForm.errors.name" class="text-xs text-rose-500 mt-0.5">{{ actorForm.errors.name }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Organisasi Terkait</label>
                    <select v-model="actorForm.organization_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white">
                        <option value="" disabled>-- Pilih Organisasi --</option>
                        <option v-for="org in filteredOrganizations" :key="org.id" :value="org.id">
                            {{ org.jabatan }} ({{ org.name }})
                        </option>
                    </select>
                    <p v-if="actorForm.errors.organization_id" class="text-xs text-rose-500 mt-0.5">{{
                        actorForm.errors.organization_id }}</p>
                </div>
            </div>
        </ConfirmationModal>

        <!-- Delete Actor Modal -->
        <ConfirmationModal :show="isDeleteActorModalOpen" title="Hapus Aktor"
            :message="`Apakah Anda yakin ingin menghapus aktor '${selectedActor?.name}'? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus" cancel-text="Batal" type="danger" :loading="actorForm.processing"
            @close="isDeleteActorModalOpen = false" @confirm="submitDeleteActor" />

        <!-- SOP Form Modal -->
        <ConfirmationModal :show="isSopModalOpen" :title="editingSopId ? 'Edit SOP' : `Tambah SOP ${activeSopTitle}`"
            :message="editingSopId ? 'Silakan sesuaikan deskripsi SOP di bawah ini.' : `Silakan isi deskripsi untuk ${activeSopTitle}.`"
            confirm-text="Simpan" cancel-text="Batal" type="info" max-width="lg" :loading="sopForm.processing"
            @close="closeSopModal" @confirm="submitSopForm">
            <div class="mt-4 space-y-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori</label>
                    <input :value="activeSopTitle" type="text" disabled
                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-300" />
                    <p v-if="sopForm.errors.tipe" class="text-xs text-rose-500 mt-0.5">{{ sopForm.errors.tipe }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi SOP</label>
                    <textarea v-model="sopForm.description" rows="6"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-white/10 dark:bg-black/20 dark:text-white"
                        placeholder="Tuliskan deskripsi aktivitas SOP"></textarea>
                    <p v-if="sopForm.errors.description" class="text-xs text-rose-500 mt-0.5">{{
                        sopForm.errors.description }}
                    </p>
                </div>
            </div>
        </ConfirmationModal>

        <!-- Delete SOP Modal -->
        <ConfirmationModal :show="isDeleteSopModalOpen" title="Hapus SOP"
            :message="`Apakah Anda yakin ingin menghapus SOP '${shortSopDescription(selectedSop?.description)}'? Tindakan ini tidak dapat dibatalkan.`"
            confirm-text="Hapus" cancel-text="Batal" type="danger" :loading="sopForm.processing"
            @close="isDeleteSopModalOpen = false" @confirm="submitDeleteSop" />
        </template>

        <template v-else>
            <div class="animate-fade-in-up space-y-6">
                <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                    <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                    <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">{{ activeRegulation?.judul }}</p>
                            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kebijakan Dokumen</h1>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                @click="printDocument"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-900/15 transition-all hover:bg-slate-800 focus:ring-2 focus:ring-slate-900/20 active:scale-95 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 17.25v-3A2.25 2.25 0 015.25 12h14.25z" />
                                </svg>
                                Cetak
                            </button>
                            <Link
                                :href="route('policy.procedure.manage', activeRegulation ? { regulation_id: activeRegulation.id } : {})"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 focus:ring-2 focus:ring-[#821f44]/20 active:scale-95"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Kelola Procedure
                            </Link>
                        </div>
                    </div>
                </section>

                <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-8 sm:p-12 md:p-16 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                    <PertaminaDocumentHeader :activeRegulation="activeRegulation" class="flex-1" />

                    <div class="mt-10 relative z-10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                            IV. FUNGSI/ UNIT ORGANISASI/ JABATAN TERKAIT
                        </h3>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-left text-[11px] text-slate-500 dark:text-slate-400">
                                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 w-20 text-center">No</th>
                                        <th scope="col" class="px-6 py-3">Fungsi / Unit Organisasi / Jabatan</th>
                                        <th scope="col" class="px-6 py-3">Jabatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                    <tr v-if="actors.length === 0">
                                        <td colspan="3" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                                            Belum ada data aktor terkait.
                                        </td>
                                    </tr>
                                    <tr v-for="(actor, index) in actors" :key="actor.id">
                                        <td class="px-6 py-3 text-center font-medium text-slate-700 dark:text-slate-300">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-3 font-medium text-slate-900 dark:text-white">
                                            {{ actor.name }}
                                        </td>
                                        <td class="px-6 py-3 text-slate-700 dark:text-slate-300">
                                            <template v-if="actor.organization">
                                                <span v-if="actor.organization.jabatan">{{ actor.organization.jabatan }}</span>
                                                <span v-else>-</span>
                                            </template>
                                            <span v-else>-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-10 relative z-10">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                            V. PROSEDUR
                        </h3>
                    </div>

                    <div class="mt-6 space-y-8 font-serif text-[15px] leading-relaxed text-slate-900 dark:text-slate-100">
                        <div>
                            <h4 class="mb-3 font-bold text-slate-950 dark:text-white">A. Penyusunan RSTI</h4>
                            <div class="space-y-3">
                                <div v-for="(item, index) in sopA" :key="item.id" class="flex gap-3 items-start">
                                    <span class="font-bold min-w-[20px] text-right select-none">{{ index + 1 }}.</span>
                                    <p class="whitespace-pre-line text-justify">{{ item.description }}</p>
                                </div>
                                <div v-if="sopA.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400 dark:border-white/10 dark:text-slate-500">
                                    Belum ada data SOP untuk kategori ini.
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="mb-3 font-bold text-slate-950 dark:text-white">B. Reviu & Pembaruan Berkala RSTI</h4>
                            <div class="space-y-3">
                                <div v-for="(item, index) in sopB" :key="item.id" class="flex gap-3 items-start">
                                    <span class="font-bold min-w-[20px] text-right select-none">{{ index + 1 }}.</span>
                                    <p class="whitespace-pre-line text-justify">{{ tightSopText(item.description) }}</p>
                                </div>
                                <div v-if="sopB.length === 0" class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-slate-400 dark:border-white/10 dark:text-slate-500">
                                    Belum ada data SOP untuk kategori ini.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="mt-4 space-y-6">
                    <div class="flex items-center gap-2 px-1">
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white tracking-wide border-b border-slate-900/10 pb-2 dark:border-white/10">
                            VI. DIAGRAM ALIR
                        </h3>
                    </div>

                    <div class="space-y-6">
                        <div
                            v-for="diagram in procedureDiagramSections"
                            :key="diagram.value"
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]"
                        >
                            <div class="border-b border-slate-200 px-5 py-3 dark:border-white/10">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                                    {{ diagram.label }}
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <div class="min-w-[1200px] p-4">
                                    <FlowChart
                                        :actors="actors"
                                        :sops="flowChartSops"
                                        readonly
                                        :flow-type="diagram.value"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </template>
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
const isManagePage = computed(() => route().current('policy.procedure.manage'));

const sopA = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter(s => s.tipe === 'A');
});

const sopB = computed(() => {
    if (!props.sop) return [];
    return props.sop.filter(s => s.tipe === 'B');
});

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || props.regulations[0] || null;
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

const procedureDiagramSections = [
    { value: 'A', label: 'A. Penyusunan RSTI' },
    { value: 'B', label: 'B. Reviu & Pembaruan Berkala RSTI' },
];

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

function printDocument() {
    window.print();
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
