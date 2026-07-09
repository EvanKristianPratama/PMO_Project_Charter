<template>
    <div class="space-y-6">
        <!-- Single Card containing both Controls Header and Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
            <!-- Header/Controls Section -->
            <div class="flex flex-row items-center justify-between gap-3 px-5 py-3 border-b border-slate-200 dark:border-white/10 flex-wrap">
                <!-- Left Section: Search Input -->
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    <div class="relative flex-1 min-w-[180px] max-w-xs">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="pointer-events-none absolute inset-y-0 left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                            />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari kebijakan khusus..."
                            class="w-full appearance-none bg-white text-slate-800 border border-slate-200 rounded-xl pl-8 pr-8 py-1.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-[#1a1a1a] dark:text-slate-300 dark:border-white/10"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            type="button"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="w-3.5 h-3.5"
                            >
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Right Section: Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <Link
                        :href="route('itom.policy.specific.mapping', { regulation_id: props.selectedRegulationId })"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95 cursor-pointer dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                        Mapping COBIT
                    </Link>
                    <button
                        @click="openAddObjectiveModal"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-750 active:scale-95 cursor-pointer dark:bg-slate-200 dark:text-slate-900 dark:hover:bg-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-3.5 h-3.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                        Tambah Kebijakan Khusus
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-white/5 dark:bg-[#1f1f1f]/50 dark:text-slate-400">
                        <tr class="divide-x divide-slate-200 dark:divide-white/10">
                            <th class="px-3.5 py-3 w-20 text-center">ID</th>
                            <th class="px-3.5 py-3 w-48">Nama Kebijakan</th>
                            <th class="px-3.5 py-3">Deskripsi & Tujuan</th>
                            <th class="px-3.5 py-3 w-72">Butir Kebijakan</th>
                            <th class="px-3.5 py-3 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-slate-300 font-medium">
                        <tr v-if="filteredObjectives.length === 0">
                            <td colspan="5" class="px-3.5 py-12 text-center text-slate-400 font-medium dark:text-slate-500">
                                Tidak ada data kebijakan khusus ditemukan.
                            </td>
                        </tr>
                        
                        <template v-for="(objectives, domainName) in groupedFilteredObjectives" :key="domainName">
                            <!-- Group Domain Header Row -->
                            <tr class="bg-slate-50/80 dark:bg-white/[0.02] text-slate-800 dark:text-slate-200 font-bold divide-x divide-slate-200 dark:divide-white/10 border-t border-b border-slate-200 dark:border-white/10">
                                <td colspan="5" class="px-3.5 py-2.5 font-bold uppercase tracking-wider text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ domainName }}
                                </td>
                            </tr>
                            
                            <!-- Objectives Rows under this Domain -->
                            <tr
                                v-for="obj in objectives"
                                :key="obj.objective_id"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition duration-150 divide-x divide-slate-200 dark:divide-white/10"
                            >
                                <!-- Column 1: Objective ID -->
                                <td class="px-3.5 py-3.5 text-center align-top font-mono font-bold text-slate-900 dark:text-white w-20">
                                    <span class="inline-block bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-200 px-1.5 py-0.5 rounded text-[10px] font-bold">
                                        {{ obj.objective_id }}
                                    </span>
                                </td>

                                <!-- Column 2: Objective Title -->
                                <td class="px-3.5 py-3.5 align-top whitespace-pre-wrap leading-relaxed w-48">
                                    {{ obj.objective }}
                                </td>

                                <!-- Column 3: Objective Desc & Purpose -->
                                <td class="px-3.5 py-3.5 align-top whitespace-pre-wrap leading-relaxed">
                                    <div class="text-[11px] leading-relaxed">
                                        <strong class="block text-slate-700 dark:text-slate-300">Deskripsi:</strong>
                                        <div class="mt-0.5 text-slate-500 dark:text-slate-400">{{ obj.objective_description || '—' }}</div>
                                    </div>
                                    <div class="text-[11px] mt-2.5 leading-relaxed">
                                        <strong class="block text-slate-700 dark:text-slate-300">Tujuan:</strong>
                                        <div class="mt-0.5 text-slate-500 dark:text-slate-400">{{ obj.objective_purpose || '—' }}</div>
                                    </div>
                                </td>

                                <!-- Column 4: Practices (Inline list in cell) -->
                                <td class="px-3.5 py-3.5 align-top w-72">
                                    <ul v-if="obj.practices && obj.practices.length > 0" class="space-y-1">
                                        <li
                                            v-for="prac in obj.practices"
                                            :key="prac.practice_id"
                                            class="flex items-start gap-1.5 text-[10px] text-slate-600 dark:text-slate-300 leading-snug"
                                        >
                                            <span class="shrink-0 mt-[4px] h-1 w-1 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                            <span class="font-mono font-bold text-slate-700 dark:text-slate-300 shrink-0">{{ prac.practice_id }}:</span>
                                            <span class="whitespace-pre-wrap leading-relaxed">{{ prac.practice_name }}</span>
                                        </li>
                                    </ul>
                                    <span v-else class="text-[10px] text-slate-400 italic">— Belum ada butir kebijakan —</span>
                                </td>

                                <!-- Column 5: Action buttons (Vertically stacked) -->
                                <td class="px-3.5 py-3.5 align-top text-center w-24">
                                    <div class="flex flex-col items-center justify-center gap-1 w-full">
                                        <button
                                            @click="openManagePracticesModal(obj)"
                                            type="button"
                                            class="w-full max-w-[70px] inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 gap-0.5"
                                            title="Kelola Butir Kebijakan"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                            Butir
                                        </button>
                                        <button
                                            @click="openEditObjectiveModal(obj)"
                                            type="button"
                                            class="w-full max-w-[70px] inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                            title="Edit Kebijakan Khusus"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteObjective(obj)"
                                            type="button"
                                            class="w-full max-w-[70px] inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-red-600 transition hover:bg-red-50 hover:border-red-300 active:scale-95 dark:border-red-500/20 dark:bg-[#1a1a1a] dark:text-red-400 dark:hover:bg-red-500/10"
                                            title="Hapus Kebijakan Khusus"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ConfirmationModal: Add & Edit Objective -->
        <ConfirmationModal
            :show="isObjectiveModalOpen"
            :title="editingObjectiveId ? 'Edit Kebijakan Khusus' : 'Tambah Kebijakan Khusus'"
            :message="editingObjectiveId ? 'Perbarui data kebijakan khusus di bawah ini.' : 'Isi formulir di bawah ini untuk membuat kebijakan khusus baru.'"
            confirm-text="Simpan"
            cancel-text="Batal"
            type="info"
            :loading="objectiveForm.processing"
            max-width="3xl"
            @close="closeObjectiveModal"
            @confirm="submitObjectiveForm"
        >
            <div class="mt-4 space-y-4 text-left">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Regulation Select -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Pilih Regulasi <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="objectiveForm.regulation_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition cursor-pointer"
                        >
                            <option value="">-- Pilih Regulasi --</option>
                            <option v-for="reg in regulations" :key="reg.id" :value="reg.id">
                                {{ reg.judul }}
                            </option>
                        </select>
                        <div v-if="objectiveForm.errors.regulation_id" class="text-xs text-rose-500 font-medium">
                            {{ objectiveForm.errors.regulation_id }}
                        </div>
                    </div>

                    <!-- Domain field -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Nama Kebijakan (Domain) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="objectiveForm.domain"
                            placeholder="Nama kebijakan (e.g. Evaluasi, Arahan dan Pemantauan)"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                        />
                        <div v-if="objectiveForm.errors.domain" class="text-xs text-rose-500 font-medium">
                            {{ objectiveForm.errors.domain }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Objective ID -->
                    <div class="flex flex-col gap-1.5 md:col-span-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            ID Kebijakan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="objectiveForm.objective_id"
                            placeholder="Contoh: EDM01"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                        />
                        <div v-if="objectiveForm.errors.objective_id" class="text-xs text-rose-500 font-medium">
                            {{ objectiveForm.errors.objective_id }}
                        </div>
                    </div>

                    <!-- Objective Title -->
                    <div class="flex flex-col gap-1.5 md:col-span-3">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Nama Kebijakan Khusus <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="objectiveForm.objective"
                            placeholder="Nama Kebijakan Khusus..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition"
                        />
                        <div v-if="objectiveForm.errors.objective" class="text-xs text-rose-500 font-medium">
                            {{ objectiveForm.errors.objective }}
                        </div>
                    </div>
                </div>

                <!-- Description Field -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi Kebijakan</label>
                    <textarea
                        v-model="objectiveForm.objective_description"
                        rows="3"
                        placeholder="Masukkan deskripsi lengkap kebijakan khusus..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition leading-relaxed"
                    ></textarea>
                    <div v-if="objectiveForm.errors.objective_description" class="text-xs text-rose-500 font-medium">
                        {{ objectiveForm.errors.objective_description }}
                    </div>
                </div>

                <!-- Purpose Field -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tujuan Kebijakan</label>
                    <textarea
                        v-model="objectiveForm.objective_purpose"
                        rows="3"
                        placeholder="Masukkan tujuan kebijakan khusus..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white transition leading-relaxed"
                    ></textarea>
                    <div v-if="objectiveForm.errors.objective_purpose" class="text-xs text-rose-500 font-medium">
                        {{ objectiveForm.errors.objective_purpose }}
                    </div>
                </div>
            </div>
        </ConfirmationModal>

        <!-- ConfirmationModal: Manage Practices (Butir Kebijakan) -->
        <ConfirmationModal
            :show="isPracticesModalOpen"
            :title="selectedObjectiveForPractices ? `Kelola Butir Kebijakan: ${selectedObjectiveForPractices.objective_id}` : 'Kelola Butir Kebijakan'"
            :message="selectedObjectiveForPractices ? selectedObjectiveForPractices.objective : ''"
            confirm-text=""
            cancel-text="Tutup"
            type="info"
            max-width="4xl"
            @close="closePracticesModal"
        >
            <div v-if="selectedObjectiveForPractices" class="mt-4 space-y-5 text-left">
                <!-- Inline Form for Practice inside Modal -->
                <div
                    v-if="activePracticeObjectiveId === selectedObjectiveForPractices.objective_id"
                    class="border border-slate-200 dark:border-white/10 rounded-xl p-4 bg-slate-50/50 dark:bg-white/[0.02] space-y-3"
                >
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-500 animate-pulse"></span>
                        {{ editingPracticeId ? 'Edit Butir Kebijakan' : 'Tambah Butir Kebijakan Baru' }}
                    </h4>
                    
                    <form @submit.prevent="submitPracticeForm" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="space-y-1.5 sm:col-span-1">
                                <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID Butir Kebijakan:</label>
                                <input
                                    type="text"
                                    v-model="practiceForm.practice_id"
                                    placeholder="Contoh: EDM01.01"
                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-[#1a1a1a] dark:text-white dark:border-white/10"
                                    required
                                />
                                <div v-if="practiceForm.errors.practice_id" class="text-[10px] text-red-500 font-medium font-mono">
                                    {{ practiceForm.errors.practice_id }}
                                </div>
                            </div>
                            <div class="space-y-1.5 sm:col-span-2">
                                <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Butir Kebijakan:</label>
                                <input
                                    type="text"
                                    v-model="practiceForm.practice_name"
                                    placeholder="Nama Butir Kebijakan..."
                                    class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-[#1a1a1a] dark:text-white dark:border-white/10"
                                />
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi Detail:</label>
                            <textarea
                                v-model="practiceForm.practice_description"
                                rows="2"
                                placeholder="Detail aktivitas atau deskripsi butir kebijakan..."
                                class="w-full bg-white text-slate-800 border border-slate-300 rounded-lg p-2 text-xs focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:bg-[#1a1a1a] dark:text-white dark:border-white/10"
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button
                                type="button"
                                @click="cancelPracticeForm"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="rounded-lg bg-slate-800 px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-slate-750 disabled:opacity-60 dark:bg-slate-200 dark:text-slate-900 dark:hover:bg-white"
                                :disabled="practiceForm.processing"
                            >
                                <span v-if="practiceForm.processing" class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>

                <div v-else class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Butir Kebijakan:</span>
                    <button
                        @click="startCreatePractice(selectedObjectiveForPractices.objective_id)"
                        type="button"
                        class="inline-flex items-center gap-1 rounded bg-slate-800 px-2.5 py-1 text-[10px] font-bold text-white hover:bg-slate-750 transition active:scale-95 shadow-sm dark:bg-slate-200 dark:text-slate-900 dark:hover:bg-white"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Butir Kebijakan
                    </button>
                </div>

                <!-- Practices List Table -->
                <div class="overflow-x-auto border border-slate-200 dark:border-white/10 rounded-xl max-h-[350px] overflow-y-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50/70 dark:bg-[#1f1f1f]/50 text-slate-500 dark:text-slate-400 font-bold uppercase border-b border-slate-200 dark:border-white/5">
                            <tr class="divide-x divide-slate-200 dark:divide-white/10">
                                <th class="px-4 py-2.5 w-24">ID</th>
                                <th class="px-4 py-2.5">Nama & Deskripsi Butir Kebijakan</th>
                                <th class="px-4 py-2.5 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-if="!selectedObjectiveForPractices.practices || selectedObjectiveForPractices.practices.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada Butir Kebijakan.
                                </td>
                            </tr>
                            <tr
                                v-for="prac in selectedObjectiveForPractices.practices"
                                :key="prac.practice_id"
                                class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition divide-x divide-slate-100 dark:divide-white/5"
                            >
                                <td class="px-4 py-3 align-top font-mono font-bold text-slate-900 dark:text-white">
                                    {{ prac.practice_id }}
                                </td>
                                <td class="px-4 py-3 align-top whitespace-pre-wrap leading-relaxed">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 mb-1">
                                        {{ prac.practice_name || 'Tanpa Nama' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                                        {{ prac.practice_description || 'Belum ada deskripsi.' }}
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 align-top text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            @click="startEditPractice(selectedObjectiveForPractices.objective_id, prac)"
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                            title="Edit"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deletePractice(prac)"
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-2 py-1 text-[11px] font-semibold text-red-600 transition hover:bg-red-50 hover:border-red-300 active:scale-95 dark:border-red-500/20 dark:bg-[#1a1a1a] dark:text-red-400 dark:hover:bg-red-500/10"
                                            title="Hapus"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </ConfirmationModal>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { usePage, useForm, router, Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    objectives: {
        type: Array,
        required: true,
    },
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: [Number, String],
        default: null
    }
});

const activeRegulation = computed(() => {
    if (!props.selectedRegulationId) return props.regulations[0] || null;
    return props.regulations.find(r => r.id === props.selectedRegulationId) || null;
});

const page = usePage();
const searchQuery = ref('');

// Filtering objectives locally by search query
const filteredObjectives = computed(() => {
    let result = props.objectives || [];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(
            (item) =>
                (item.objective_id || '').toLowerCase().includes(q) ||
                (item.objective || '').toLowerCase().includes(q) ||
                (item.objective_description || '').toLowerCase().includes(q)
        );
    }
    return result;
});

// Group objectives by their domain
const groupedFilteredObjectives = computed(() => {
    const groups = {};
    filteredObjectives.value.forEach((obj) => {
        const domain = obj.domain || getDomainName(obj.objective_id);
        if (!groups[domain]) {
            groups[domain] = [];
        }
        groups[domain].push(obj);
    });
    return groups;
});

// Helper to determine Domain name by Objective ID prefix (English fallback)
function getDomainName(id) {
    if (!id) return 'Governance / Management Domain';
    const cleanId = id.trim().toUpperCase();
    if (cleanId.startsWith('EDM')) return 'Evaluate, Direct and Monitor';
    if (cleanId.startsWith('APO')) return 'Align, Plan and Organize';
    if (cleanId.startsWith('BAI')) return 'Build, Acquire and Implement';
    if (cleanId.startsWith('DSS')) return 'Deliver, Service and Support';
    if (cleanId.startsWith('MEA')) return 'Monitor, Evaluate and Assess';
    return 'COBIT Governance Objective';
}

// Indonesian auto-suggestion mapping helper
function getDomainNameInIndonesian(id) {
    if (!id) return '';
    const cleanId = id.trim().toUpperCase();
    if (cleanId.startsWith('EDM')) return 'Evaluasi, Arahan dan Pemantauan (EDM)';
    if (cleanId.startsWith('APO')) return 'Penyelarasan, Perencanaan dan Pengorganisasian (APO)';
    if (cleanId.startsWith('BAI')) return 'Pembangunan, Perolehan dan Penerapan (BAI)';
    if (cleanId.startsWith('DSS')) return 'Pemberian Layanan, Operasional dan Dukungan (DSS)';
    if (cleanId.startsWith('MEA')) return 'Pemantauan, Evaluasi dan Pengukuran (MEA)';
    return '';
}

// ---------------------------------------------------
// OBJECTIVE: MODAL & FORM STATE
// ---------------------------------------------------
const isObjectiveModalOpen = ref(false);
const editingObjectiveId = ref(null);

const objectiveForm = useForm({
    objective_id: '',
    regulation_id: props.selectedRegulationId || '',
    domain: '',
    objective: '',
    objective_description: '',
    objective_purpose: '',
});

// Watch activeRegulation changes to update forms' regulation_id
watch(
    () => activeRegulation.value,
    (reg) => {
        if (reg) {
            objectiveForm.regulation_id = reg.id;
        }
    },
    { immediate: true }
);

// Suggest domain automatically when typing objective_id
watch(
    () => objectiveForm.objective_id,
    (newVal) => {
        const indSuggestion = getDomainNameInIndonesian(newVal);
        if (indSuggestion) {
            objectiveForm.domain = indSuggestion;
        }
    }
);

function openAddObjectiveModal() {
    editingObjectiveId.value = null;
    objectiveForm.reset();
    objectiveForm.regulation_id = props.selectedRegulationId || '';
    objectiveForm.clearErrors();
    isObjectiveModalOpen.value = true;
}

function openEditObjectiveModal(obj) {
    editingObjectiveId.value = obj.objective_id;
    objectiveForm.objective_id = obj.objective_id;
    objectiveForm.regulation_id = obj.regulation_id || props.selectedRegulationId || '';
    objectiveForm.domain = obj.domain || '';
    objectiveForm.objective = obj.objective;
    objectiveForm.objective_description = obj.objective_description || '';
    objectiveForm.objective_purpose = obj.objective_purpose || '';
    objectiveForm.clearErrors();
    isObjectiveModalOpen.value = true;
}

function closeObjectiveModal() {
    isObjectiveModalOpen.value = false;
    editingObjectiveId.value = null;
    objectiveForm.reset();
    objectiveForm.clearErrors();
}

function submitObjectiveForm() {
    if (editingObjectiveId.value) {
        objectiveForm.put(route('itom.policy.objective.update', { objective: editingObjectiveId.value }), {
            preserveScroll: true,
            onSuccess: () => {
                closeObjectiveModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kebijakan Khusus berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#1e293b',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    } else {
        objectiveForm.post(route('itom.policy.objective.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeObjectiveModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Kebijakan Khusus berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#1e293b',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    }
}

function deleteObjective(obj) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Kebijakan Khusus "${obj.objective_id} - ${obj.objective}" beserta seluruh Butir Kebijakan di dalamnya!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('itom.policy.objective.destroy', obj.objective_id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Kebijakan Khusus berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#1e293b',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        }
    });
}

// ---------------------------------------------------
// PRACTICE: FORM & MODAL STATE
// ---------------------------------------------------
const selectedObjectiveIdForPractices = ref(null);
const isPracticesModalOpen = ref(false);

const selectedObjectiveForPractices = computed(() => {
    if (!selectedObjectiveIdForPractices.value) return null;
    return props.objectives.find(o => o.objective_id === selectedObjectiveIdForPractices.value) || null;
});

const activePracticeObjectiveId = ref(null);
const editingPracticeId = ref(null);

const practiceForm = useForm({
    practice_id: '',
    objective_id: '',
    practice_name: '',
    practice_description: '',
});

function openManagePracticesModal(obj) {
    selectedObjectiveIdForPractices.value = obj.objective_id;
    isPracticesModalOpen.value = true;
    cancelPracticeForm();
}

function closePracticesModal() {
    isPracticesModalOpen.value = false;
    selectedObjectiveIdForPractices.value = null;
    cancelPracticeForm();
}

function startCreatePractice(objId) {
    activePracticeObjectiveId.value = objId;
    editingPracticeId.value = null;
    
    // Automatically suggest practice ID pattern
    const objIdPrefix = objId.trim();
    const matchedObj = props.objectives.find(o => o.objective_id === objId);
    let nextIndex = 1;
    if (matchedObj && matchedObj.practices) {
        nextIndex = matchedObj.practices.length + 1;
    }
    const suggestedIndex = nextIndex < 10 ? `0${nextIndex}` : nextIndex;

    practiceForm.reset();
    practiceForm.objective_id = objId;
    practiceForm.practice_id = `${objIdPrefix}.${suggestedIndex}`;
    practiceForm.clearErrors();
}

function startEditPractice(objId, practice) {
    activePracticeObjectiveId.value = objId;
    editingPracticeId.value = practice.practice_id;
    
    practiceForm.objective_id = objId;
    practiceForm.practice_id = practice.practice_id;
    practiceForm.practice_name = practice.practice_name || '';
    practiceForm.practice_description = practice.practice_description || '';
    practiceForm.clearErrors();
}

function cancelPracticeForm() {
    activePracticeObjectiveId.value = null;
    editingPracticeId.value = null;
    practiceForm.reset();
    practiceForm.clearErrors();
}

function submitPracticeForm() {
    if (editingPracticeId.value) {
        practiceForm.put(route('itom.policy.practice.update', { practice: editingPracticeId.value }), {
            preserveScroll: true,
            onSuccess: () => {
                cancelPracticeForm();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Butir Kebijakan berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#1e293b',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    } else {
        practiceForm.post(route('itom.policy.practice.store'), {
            preserveScroll: true,
            onSuccess: () => {
                cancelPracticeForm();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Butir Kebijakan berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#1e293b',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    }
}

function deletePractice(practice) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus Butir Kebijakan "${practice.practice_id} - ${practice.practice_name || ''}"!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('itom.policy.practice.destroy', practice.practice_id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Butir Kebijakan berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#1e293b',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        }
    });
}
</script>

<style scoped>
/* Custom styled utilities */
.bg-slate-750 {
    background-color: #334155;
}
.bg-slate-750:hover {
    background-color: #1e293b;
}
.bg-slate-850 {
    background-color: #1e293b;
}
.bg-slate-850:hover {
    background-color: #0f172a;
}
</style>
