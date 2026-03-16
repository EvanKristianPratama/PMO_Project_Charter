<template>
    <UserLayout title="Master Data — Edit Initiative">
        <div class="animate-fade-in-up space-y-4">
            <!-- Header -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Master Initiative</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ initiative.code ?? '' }} — {{ initiative.name }}
                        </p>
                    </div>
                    <a href="/master-data/master-initiatives"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5">
                        ← Kembali
                    </a>
                </div>
            </section>

            <!-- ═══ MASTER DATA — TABLE FORM ═══ -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-white/10">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200">Informasi Utama</h2>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">Detail Data Master Initiative</p>
                    </div>
                    <button type="button" @click="submitMaster" :disabled="masterForm.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#0f63b5] px-5 py-2 text-xs font-bold text-white shadow-md hover:bg-[#0c4e8f] focus:ring-2 focus:ring-[#0f63b5]/50 active:scale-95 transition-all disabled:opacity-50">
                        <svg v-if="masterForm.processing" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <colgroup>
                            <col class="w-[180px]">
                            <col>
                        </colgroup>
                        <tbody>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Code</td>
                                <td class="px-3 py-1.5">
                                    <input v-model="masterForm.code" type="number" min="0" placeholder="—" class="form-input-inline" />
                                    <p v-if="masterForm.errors.code" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.code }}</p>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Nama <span class="text-rose-400">*</span></td>
                                <td class="px-3 py-1.5">
                                    <input v-model="masterForm.name" type="text" placeholder="—" class="form-input-inline" />
                                    <p v-if="masterForm.errors.name" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.name }}</p>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Tipe <span class="text-rose-400">*</span></td>
                                <td class="px-3 py-1.5">
                                    <select v-model="masterForm.tipe_initiative" class="form-input-inline">
                                        <option value="">— Pilih —</option>
                                        <option v-for="t in tipeOptions" :key="t.id" :value="t.id">{{ t.label }}</option>
                                    </select>
                                    <p v-if="masterForm.errors.tipe_initiative" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.tipe_initiative }}</p>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">CoE</td>
                                <td class="px-3 py-1.5">
                                    <select v-model="masterForm.coe_id" class="form-input-inline">
                                        <option :value="null">— Pilih —</option>
                                        <option v-for="opt in coeOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                    </select>
                                    <p v-if="masterForm.errors.coe_id" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.coe_id }}</p>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Sumber Data</td>
                                <td class="px-3 py-1.5">
                                    <select v-model="masterForm.source" class="form-input-inline">
                                        <option :value="null">— Pilih —</option>
                                        <option v-for="opt in sourceOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                    </select>
                                    <p v-if="masterForm.errors.source" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.source }}</p>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-100 dark:border-white/5">
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 align-top pt-3">Organisasi</td>
                                <td class="px-3 py-1.5">
                                    <div class="max-h-32 overflow-y-auto border border-slate-200 dark:border-white/10 rounded divide-y divide-slate-100 dark:divide-white/5">
                                        <label v-for="opt in organizationOptions" :key="opt.id"
                                            class="flex items-center gap-2 px-2 py-1.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5">
                                            <input v-model="masterForm.organization_ids" type="checkbox" :value="opt.id"
                                                class="h-3 w-3 rounded border-slate-300 text-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10" />
                                            <span class="text-[11px] text-slate-700 dark:text-slate-300 truncate">{{ opt.name }}</span>
                                            <span v-if="opt.groub" class="ml-auto shrink-0 text-[9px] text-slate-400 dark:text-slate-500">{{ opt.groub }}</span>
                                        </label>
                                    </div>
                                    <p v-if="masterForm.errors.organization_ids" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.organization_ids }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 dark:bg-white/[0.03] text-[11px] font-semibold text-slate-500 dark:text-slate-400 align-top pt-3">Deskripsi</td>
                                <td class="px-3 py-1.5">
                                    <textarea v-model="masterForm.description" rows="3" placeholder="—" class="form-input-inline resize-none"></textarea>
                                    <p v-if="masterForm.errors.description" class="mt-0.5 text-[10px] text-rose-500">{{ masterForm.errors.description }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ═══ STATUS HISTORY — TABLE ═══ -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-white/10">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200">Riwayat Status</h2>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">Track perkembangan inisiatif ({{ initiative.status_history?.length ?? 0 }})</p>
                    </div>
                    <button v-if="!showNewRow" type="button" @click="openNewRow"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-600 transition-all active:scale-95">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Tambah Status
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-[11px] dark:divide-white/5">
                        <colgroup>
                            <col class="w-[50px]"><col class="w-[150px]"><col class="w-[150px]"><col><col class="w-[150px]">
                        </colgroup>
                        <thead class="bg-slate-50/50 dark:bg-white/5">
                            <tr>
                                <th v-for="h in statusHeaders" :key="h" class="px-6 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ h }}</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-white/5 dark:bg-[#1a1a1a]">
                            <tr v-for="(entry, idx) in initiative.status_history" :key="entry.id"
                                class="transition-colors hover:bg-slate-50/50 dark:hover:bg-white/5 group">
                                <td class="px-6 py-4 text-slate-400 font-medium">{{ idx + 1 }}</td>

                                <!-- View mode -->
                                <template v-if="editingId !== entry.id">
                                    <td class="px-6 py-4">
                                        <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-tight capitalize', getStatusClass(entry.status)]">
                                            {{ entry.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium">{{ formatDate(entry.tanggal) }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 italic">{{ entry.notes ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2 transition-opacity">
                                            <button type="button" @click="startEdit(entry)"
                                                class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-700/40 transition-colors">
                                                Edit
                                            </button>
                                            <button type="button" @click="deleteStatus(entry.id)" :disabled="deletingId === entry.id"
                                                class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1.5 text-[10px] font-bold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-100 disabled:opacity-50 dark:bg-rose-900/20 dark:text-rose-400 dark:ring-rose-700/40 transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </template>

                                <!-- Edit mode -->
                                <template v-else>
                                    <td class="px-6 py-4">
                                        <select v-model="editRow.status" class="form-input-premium py-1">
                                            <option value="">— Pilih —</option>
                                            <option v-for="opt in statusList" :key="opt" :value="opt">{{ opt }}</option>
                                        </select>
                                        <p v-if="editErrors.status" class="mt-0.5 text-[10px] text-rose-500 font-medium">{{ editErrors.status }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input v-model="editRow.tanggal" type="date" class="form-input-premium py-1" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <input v-model="editRow.notes" type="text" placeholder="Catatan..." class="form-input-premium py-1" />
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button type="button" @click="saveEdit(entry.id)" :disabled="editProcessing"
                                                class="inline-flex items-center rounded-lg bg-emerald-500 px-3 py-1.5 text-[10px] font-bold text-white hover:bg-emerald-600 disabled:opacity-50 transition-colors shadow-sm">
                                                Simpan
                                            </button>
                                            <button type="button" @click="cancelEdit"
                                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1.5 text-[10px] font-bold text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:text-slate-300 transition-colors">
                                                Batal
                                            </button>
                                        </div>
                                    </td>
                                </template>
                            </tr>

                            <tr v-if="!initiative.status_history?.length && !showNewRow">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-[11px] font-medium text-slate-400 uppercase tracking-widest italic">Belum ada riwayat status yang tercatat.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Add new status form (Inline Card) -->
                <transition enter-active-class="transition ease-out duration-200" enter-from-class="transform opacity-0 -translate-y-2" enter-to-class="transform opacity-100 translate-y-0">
                    <div v-if="showNewRow" class="border-t border-slate-200 bg-slate-50/30 p-6 dark:border-white/10 dark:bg-white/2">
                        <div class="flex flex-col md:flex-row items-end gap-4">
                            <div class="flex-1 space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Status Progress *</label>
                                <select v-model="newRow.status" class="form-input-premium">
                                    <option value="">— Pilih Progress —</option>
                                    <option v-for="opt in statusList" :key="opt" :value="opt">{{ opt }}</option>
                                </select>
                                <p v-if="newRowErrors.status" class="text-[10px] text-rose-500 font-medium">{{ newRowErrors.status }}</p>
                            </div>
                            <div class="w-full md:w-48 space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</label>
                                <input v-model="newRow.tanggal" type="date" class="form-input-premium" />
                            </div>
                            <div class="flex-[2] space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Catatan Tambahan</label>
                                <input v-model="newRow.notes" type="text" placeholder="Berikan sedikit konteks..." class="form-input-premium" />
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="saveNewRow" :disabled="newRowProcessing"
                                    class="inline-flex rounded-lg bg-emerald-500 px-5 py-2 text-[11px] font-bold text-white hover:bg-emerald-600 shadow-md transition-all active:scale-95 disabled:opacity-50">
                                    Simpan
                                </button>
                                <button type="button" @click="showNewRow = false"
                                    class="inline-flex rounded-lg border border-slate-200 bg-white px-5 py-2 text-[11px] font-bold text-slate-600 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 transition-all active:scale-95">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- ═══ STRATEGIC MAPPING — TAGGING ═══ -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-[#1a1a1a]">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-white/10 bg-slate-50/30 dark:bg-white/[0.02]">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200">Strategic Mapping</h2>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">Hubungan ke Strategic Pillar & Theme</p>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-col gap-8">
                        <!-- Current Mappings (Atas) -->
                        <div class="w-full">
                            <div class="mb-4 flex items-center justify-between px-1">
                                <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Mapping Saat Ini ({{ initiative.taggings?.length ?? 0 }})</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div
                                    v-for="tag in initiative.taggings"
                                    :key="tag.id"
                                    class="group flex items-start justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-[#0f63b5]/30 hover:shadow-md dark:border-white/10 dark:bg-[#1a1a1a]"
                                >
                                    <div class="flex flex-col gap-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-black text-[#0f63b5] dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ tag.goal }}
                                            </span>
                                            <span v-if="tag.theme" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Theme Connection</span>
                                            <span v-else class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Direct Pillar</span>
                                        </div>
                                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate leading-tight mt-1" :title="tag.theme?.name || 'Strategic Pillar Connection'">
                                            {{ tag.theme?.name || 'Connected directly to Strategic Pillar' }}
                                        </p>
                                    </div>
                                    <button @click="removeTagging(tag.id)" class="shrink-0 rounded-lg p-1.5 text-slate-300 hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-900/20 transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <div
                                    v-if="!initiative.taggings?.length"
                                    class="col-span-full flex flex-col items-center justify-center py-12 rounded-2xl border-2 border-dashed border-slate-100 dark:border-white/5"
                                >
                                    <svg class="h-8 w-8 text-slate-200 dark:text-white/5 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest italic text-center">Belum ada strategic mapping.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Add New Mapping Form (Bawah) -->
                        <div class="border-t border-slate-100 dark:border-white/5 pt-8">
                            <div class="max-w-2xl mx-auto rounded-2xl border border-slate-100 bg-slate-50/50 p-6 dark:border-white/5 dark:bg-white/[0.03]">
                                <h3 class="mb-4 text-[10px] font-bold uppercase tracking-widest text-[#0f63b5]">Tambah Hubungan Baru</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Strategic Pillar (Goal)</label>
                                        <select v-model="mappingForm.goalId" @change="onGoalChange" class="form-input-premium">
                                            <option value="">— Pilih Pillar —</option>
                                            <option v-for="g in allGoals" :key="g.id" :value="g.id">[{{ g.code }}] {{ g.title }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Theme (Opsional)</label>
                                        <select v-model="mappingForm.themes_id" :disabled="!mappingForm.goalId" class="form-input-premium disabled:opacity-50">
                                            <option value="">— Hubungkan ke Pillar Langsung —</option>
                                            <option v-for="t in filteredThemes" :key="t.id" :value="t.id">{{ t.theme_number }}. {{ t.name }}</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2 mt-2">
                                        <button
                                            @click="addTagging"
                                            :disabled="!mappingForm.goalId || mappingProcessing"
                                            class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#0f63b5] px-6 text-xs font-bold text-white shadow-lg hover:bg-[#0c4e8f] active:scale-95 transition-all disabled:opacity-50"
                                        >
                                            <svg v-if="mappingProcessing" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            {{ mappingProcessing ? 'Memproses...' : 'Tambah Mapping Baru' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
    initiative:          { type: Object, required: true },
    coeOptions:          { type: Array, default: () => [] },
    organizationOptions: { type: Array, default: () => [] },
    tipeOptions:         { type: Array, default: () => [] },
    sourceOptions:       { type: Array, default: () => [] },
    selectedOrganizationIds: { type: Array, default: () => [] },
    allGoals:             { type: Array, default: () => [] },
    allThemes:            { type: Array, default: () => [] },
});

const masterHeaders = ['Code', 'Tipe', 'CoE', 'Organisasi', 'Sumber Data', 'Nama', 'Deskripsi'];
const statusHeaders = ['No', 'Status', 'Tanggal', 'Notes'];

// ── Master form ──
const masterForm = useForm({
    code:            props.initiative.code ?? '',
    name:            props.initiative.name ?? '',
    description:     props.initiative.description ?? '',
    tipe_initiative: props.initiative.tipe_initiative ?? '',
    coe_id:          props.initiative.coe_id ?? null,
    business_unit:   props.initiative.business_unit ?? null,
    organization_ids: Array.isArray(props.selectedOrganizationIds) && props.selectedOrganizationIds.length
        ? props.selectedOrganizationIds.map((id) => Number(id))
        : (
            Array.isArray(props.initiative.organizations) && props.initiative.organizations.length
                ? props.initiative.organizations.map((item) => Number(item?.id))
                : (props.initiative.business_unit ? [Number(props.initiative.business_unit)] : [])
        ),
    source:          props.initiative.source ?? null,
});

const normalizeOrganizationIds = (ids) => {
    if (!Array.isArray(ids)) return [];

    return [...new Set(ids
        .map((id) => Number(id))
        .filter((id) => Number.isInteger(id) && id > 0))];
};

const submitMaster = () => {
    masterForm
        .transform((data) => {
            const organizationIds = normalizeOrganizationIds(data.organization_ids);

            return {
                ...data,
                organization_ids: organizationIds,
                business_unit: organizationIds[0] ?? null,
            };
        })
        .put(`/master-data/master-initiatives/${props.initiative.id}`);
};

// ── Add new status ──
const showNewRow = ref(false);
const newRow = reactive({ status: '', tanggal: '', notes: '' });
const newRowErrors = reactive({ status: '' });
const newRowProcessing = ref(false);

const statusList = ['Drafting', 'Propose', 'Review', 'Approved', 'Postpone'];

const openNewRow = () => {
    showNewRow.value = true;
    newRow.status = ''; newRow.tanggal = ''; newRow.notes = '';
    newRowErrors.status = '';
};

const saveNewRow = () => {
    newRowErrors.status = '';
    if (!newRow.status.trim()) { newRowErrors.status = 'Status wajib diisi.'; return; }
    newRowProcessing.value = true;
    router.post(`/master-data/master-initiatives/${props.initiative.id}/status`, {
        status: newRow.status,
        tanggal: newRow.tanggal || null,
        notes: newRow.notes || null,
    }, {
        preserveScroll: true,
        onFinish: () => { newRowProcessing.value = false; showNewRow.value = false; },
    });
};

// ── Edit status ──
const editingId = ref(null);
const editRow = reactive({ status: '', tanggal: '', notes: '' });
const editErrors = reactive({ status: '' });
const editProcessing = ref(false);

const startEdit = (entry) => {
    editingId.value = entry.id;
    editRow.status = entry.status ?? '';
    editRow.tanggal = entry.tanggal ?? '';
    editRow.notes = entry.notes ?? '';
    editErrors.status = '';
};

const cancelEdit = () => {
    editingId.value = null;
};

const saveEdit = (statusId) => {
    editErrors.status = '';
    if (!editRow.status.trim()) { editErrors.status = 'Status wajib diisi.'; return; }
    editProcessing.value = true;
    router.put(`/master-data/master-initiatives/status/${statusId}`, {
        status: editRow.status,
        tanggal: editRow.tanggal || null,
        notes: editRow.notes || null,
    }, {
        preserveScroll: true,
        onFinish: () => { editProcessing.value = false; editingId.value = null; },
    });
};

// ── Delete status ──
const deletingId = ref(null);

const deleteStatus = (statusId) => {
    if (!window.confirm('Hapus riwayat status ini?')) return;
    deletingId.value = statusId;
    router.delete(`/master-data/master-initiatives/status/${statusId}`, {
        preserveScroll: true,
        onFinish: () => { deletingId.value = null; },
    });
};

// ── Strategic Mapping (Tagging) ──
const mappingForm = reactive({
    goalId: '',
    themes_id: ''
});
const mappingProcessing = ref(false);

const filteredThemes = computed(() => {
    if (!mappingForm.goalId) return [];
    return props.allThemes.filter(t => t.idGoal === mappingForm.goalId);
});

const onGoalChange = () => {
    mappingForm.themes_id = '';
};

const addTagging = () => {
    if (!mappingForm.goalId) {
        alert('Pilih Strategic Pillar terlebih dahulu.');
        return;
    }
    
    const goalObj = props.allGoals.find(g => g.id === mappingForm.goalId);
    if (!goalObj) return;

    mappingProcessing.value = true;
    router.post('/strategic-pillars/tagging', {
        initiative_id: props.initiative.id,
        goal: goalObj.code,
        themes_id: mappingForm.themes_id || null
    }, {
        preserveScroll: true,
        onSuccess: () => {
            mappingForm.goalId = '';
            mappingForm.themes_id = '';
        },
        onFinish: () => {
            mappingProcessing.value = false;
        }
    });
};

const removeTagging = (tagId) => {
    if (!window.confirm('Hapus mapping ini?')) return;
    
    router.delete(`/strategic-pillars/tagging/${tagId}`, {
        preserveScroll: true
    });
};

// ── Helpers ──
const getStatusClass = (status) => {
    const s = String(status || '').toLowerCase();
    if (s.includes('draft')) return 'bg-slate-100 text-slate-600 ring-1 ring-slate-300';
    if (s.includes('propose')) return 'bg-blue-100 text-blue-700 ring-1 ring-blue-300';
    if (s.includes('review')) return 'bg-amber-100 text-amber-700 ring-1 ring-amber-300';
    if (s.includes('baseline')) return 'bg-purple-100 text-purple-700 ring-1 ring-purple-300';
    if (s.includes('approve')) return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300';
    if (s.includes('postpone')) return 'bg-rose-100 text-rose-700 ring-1 ring-rose-300';
    return 'bg-slate-100 text-slate-500 ring-1 ring-slate-200';
};

const formatDate = (d) => {
    if (!d) return '-';
    try { return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};
</script>

<style scoped>
@reference "tailwindcss";
.form-input-premium {
    @apply w-full rounded-xl border-slate-200 bg-white px-4 py-2.5 text-xs font-medium shadow-sm transition-all focus:border-[#0f63b5] focus:ring-4 focus:ring-[#0f63b5]/10 hover:border-slate-300 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:focus:border-[#0f63b5] dark:placeholder-slate-600;
}

.form-input-inline {
    @apply w-full rounded border border-slate-200 bg-white px-2 py-1 text-xs font-medium transition-all focus:border-[#0f63b5] focus:ring-1 focus:ring-[#0f63b5]/20 dark:border-white/10 dark:bg-[#131313] dark:text-slate-100 dark:placeholder-slate-500;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    @apply bg-transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    @apply rounded-full bg-slate-200 dark:bg-white/10 hover:bg-slate-300 dark:hover:bg-white/20;
}
</style>
