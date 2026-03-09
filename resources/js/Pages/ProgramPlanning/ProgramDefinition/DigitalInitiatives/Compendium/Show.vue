<template>
    <UserLayout title="Program Definition Digital Initiatives — New Compendium">
        <div class="animate-fade-in-up space-y-6 pb-20">
            <!-- Header Section -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-wrap items-center gap-3 px-4 py-3">
                    <Link href="/program-planning/program-definition/digital-initiatives/compendium"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-50 dark:border-white/10 dark:text-slate-400 dark:hover:bg-white/5 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" />
                        </svg>
                        Kembali
                    </Link>
                    <div class="h-6 w-px bg-slate-200 dark:bg-white/10 mx-1"></div>
                    <div class="flex-1">
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white leading-none">New Compendium</h1>
                    </div>
                    <button type="button" @click="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#0f63b5] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-[#0c4e8f] disabled:opacity-50 transition-all active:scale-95">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Compendium' }}
                    </button>
                </div>

                <div class="border-t border-slate-100 dark:border-white/5 px-4 py-3 bg-slate-50/50 dark:bg-white/[0.02] flex items-center gap-6">
                    <div class="w-full sm:max-w-xs">
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Document Status</label>
                        <select v-model="form.status"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100">
                            <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Initiative Mapping Section (Reverted to Dropdown + Tags) -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="mb-4">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Master Initiative Mapping</h2>
                    <p class="text-xs text-slate-500 mt-1">Pilih inisiatif yang akan dihubungkan dengan dokumen ini.</p>
                </div>
                
                <div class="space-y-4">
                    <div class="max-w-xl">
                        <select 
                            @change="(e) => { addInitiative(Number(e.target.value)); e.target.value = ''; }"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-[#0f63b5] focus:ring-[#0f63b5] dark:border-white/10 dark:bg-[#131313] dark:text-slate-100"
                        >
                            <option value="">+ Pilih Initiative untuk dimapping...</option>
                            <option v-for="opt in initiativeOptions" :key="opt.id" :value="opt.id" :disabled="form.initiative_ids.includes(opt.id)">
                                {{ opt.code ? `[${opt.code}] ` : '' }}{{ opt.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Selected Tags -->
                    <div class="flex flex-wrap gap-2 min-h-[40px] p-3 bg-slate-50 dark:bg-white/5 border border-dashed border-slate-200 dark:border-white/10 rounded-xl">
                        <div v-for="id in form.initiative_ids" :key="`tag-${id}`" 
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-[#1a1a1a] text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 rounded-lg text-xs font-bold shadow-sm">
                            <span class="truncate max-w-[200px]">{{ initiativeOptions.find(o => o.id === id)?.code || id }}</span>
                            <button type="button" @click="removeInitiative(id)" class="text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p v-if="!form.initiative_ids.length" class="text-xs text-slate-400 italic flex items-center">Belum ada inisiatif yang dipilih.</p>
                    </div>
                </div>

                <div v-if="form.errors.initiative_ids" class="mt-2 text-xs text-rose-500 italic">* {{ form.errors.initiative_ids }}</div>
            </section>

            <CompendiumCharterDocument :form="form" :source-options="sourceOptions" :editable="true" />
        </div>
    </UserLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import CompendiumCharterDocument from '@/Components/Compendium/CompendiumCharterDocument.vue';

defineProps({
    initiativeOptions: { type: Array, default: () => [] },
    sourceOptions: { type: Array, default: () => [] },
});

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
    { value: 'draft', label: 'Draft' },
];

const form = useForm({
    initiative_ids: [],
    owner: '',
    usecase: '',
    description: '',
    source_id: '',
    value: 4,
    urgency: 4,
    status: 'active',
});

const addInitiative = (id) => {
    if (id && !form.initiative_ids.includes(id)) {
        form.initiative_ids.push(id);
    }
};

const removeInitiative = (id) => {
    form.initiative_ids = form.initiative_ids.filter(item => item !== id);
};

const submit = () => {
    form.post('/program-planning/program-definition/digital-initiatives/compendium', {
        preserveScroll: true,
    });
};
</script>
