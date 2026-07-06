<template>
    <!-- Kelola Anggota Modal -->
    <ConfirmationModal
        :show="isMemberModalOpen"
        :title="`Kelola Anggota — ${selectedStructure?.name}`"
        :message="selectedFunctional?.name ? `Organisasi: ${selectedFunctional.name}` : 'Berikut adalah daftar anggota yang tergabung dalam structure ini.'"
        confirm-text="Tutup"
        cancel-text=""
        type="info"
        :loading="memberForm.processing"
        max-width="xl"
        @close="closeModal"
        @confirm="closeModal"
    >
        <div class="mt-4 space-y-4 text-left">
            <!-- Add Member Form -->
            <form @submit.prevent="submitAddMember" class="flex flex-col gap-3 p-3 bg-slate-50 rounded-xl dark:bg-white/5 border border-slate-200 dark:border-white/10">
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Tambah Anggota Baru</h4>
                
                <!-- Tipe Anggota (BOD vs Fungsi) Selector -->
                <div class="flex flex-col gap-1">
                    <label for="member_type_select" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Tipe Anggota</label>
                    <select
                        id="member_type_select"
                        v-model="memberType"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    >
                        <option value="bod">Jabatan (Organization)</option>
                        <option value="function">Fungsi (Function)</option>
                    </select>
                </div>

                <!-- Pilih Perusahaan (Company) -->
                <div class="flex flex-col gap-1">
                    <label for="member_company_id" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">Pilih Perusahaan (Company)</label>
                    <select
                        id="member_company_id"
                        v-model="memberCompanyId"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs text-slate-900 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-white"
                    >
                        <option value="">— Pilih Perusahaan —</option>
                        <option
                            v-for="company in companies"
                            :key="company.id"
                            :value="String(company.id)"
                        >
                            {{ company.name }}
                        </option>
                    </select>
                </div>

                <!-- Select Box / Autocomplete -->
                <div class="flex flex-col gap-2">
                    <label for="member_organization_id" class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">
                        Pilih {{ memberType === 'bod' ? 'Jabatan (BoD)' : 'Fungsi' }}
                    </label>
                    <div class="rounded-lg border border-slate-300 bg-white dark:border-white/10 dark:bg-[#1a1a1a] overflow-hidden">
                        <!-- Search bar -->
                        <div class="px-2 py-1.5 border-b border-slate-200 dark:border-white/10">
                            <input
                                v-model="bodSearch"
                                type="text"
                                :placeholder="memberType === 'bod' ? 'Cari jabatan...' : 'Cari fungsi...'"
                                class="w-full rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] text-slate-900 focus:border-slate-400 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white placeholder-slate-400"
                            />
                        </div>
                        <!-- Scrollable list -->
                        <ul class="max-h-44 overflow-y-auto">
                            <li v-if="filteredAvailableItems.length === 0"
                                class="px-3 py-2 text-[11px] text-slate-400 dark:text-slate-500 text-center italic">
                                Tidak ada {{ memberType === 'bod' ? 'jabatan' : 'fungsi' }} tersedia
                            </li>
                            <li
                                v-for="item in filteredAvailableItems"
                                :key="item.id"
                                @click="selectMemberId(item.id)"
                                class="flex items-center cursor-pointer py-1.5 pr-3 text-[11px] border-b border-slate-50 dark:border-white/5 last:border-0 transition select-none"
                                :style="{ paddingLeft: `${8 + (item._level || 0) * 14}px` }"
                                :class="selectedItemId === item.id
                                    ? 'bg-blue-50 dark:bg-blue-500/10'
                                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                            >
                                <span
                                    class="mr-1.5 shrink-0 font-medium"
                                    :class="item._level === 0
                                        ? 'text-slate-600 dark:text-slate-400'
                                        : 'text-slate-400 dark:text-slate-500'"
                                >—</span>
                                <span
                                    :class="selectedItemId === item.id
                                        ? 'font-semibold text-blue-700 dark:text-blue-300'
                                        : item._level === 0
                                            ? 'font-medium text-slate-800 dark:text-slate-200'
                                            : 'text-slate-600 dark:text-slate-400'"
                                >{{ item.name }}</span>
                            </li>
                        </ul>
                    </div>
                    <!-- Selected label -->
                    <p v-if="selectedItemId" class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">
                        ✓ Dipilih: {{ selectedItemName }}
                    </p>
                    <span v-if="memberForm.errors.organization_id" class="text-[10px] text-red-500 font-medium">{{ memberForm.errors.organization_id }}</span>
                    <button
                        type="submit"
                        :disabled="memberForm.processing || !selectedItemId"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 text-xs font-semibold shadow-sm transition-all focus:outline-none"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ memberForm.processing ? 'Menambahkan...' : 'Tambah Anggota' }}
                    </button>
                </div>
            </form>

            <!-- List of Current Members -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Anggota Saat Ini</h4>
                    <span v-if="currentMembersForStructure.length > 0" class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20">
                        {{ currentMembersForStructure.length }} anggota
                    </span>
                </div>
                <div v-if="currentMembersForStructure.length > 0" class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                    <div
                        v-for="member in currentMembersForStructure"
                        :key="member.member_type === 'function' ? 'func-' + member.function_id : 'bod-' + member.organization_id"
                        class="flex items-center justify-between px-3 py-2 rounded-lg border border-slate-100 bg-white dark:border-white/5 dark:bg-[#1a1a1a] hover:bg-slate-50 dark:hover:bg-white/5 transition"
                    >
                        <div class="flex items-center gap-2">
                            <span class="flex h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>
                            <div class="text-xs font-medium text-slate-800 dark:text-slate-200">
                                {{ member.name }}
                                <span v-if="member.company_name" class="ml-1 text-[10px] text-slate-400 dark:text-slate-500 font-normal">
                                    ({{ member.company_name }})
                                </span>
                                <span class="ml-1.5 inline-flex items-center rounded bg-slate-100 px-1 py-0.5 text-[8px] font-bold text-slate-600 dark:bg-white/10 dark:text-slate-300">
                                    {{ member.member_type === 'function' ? 'Fungsi' : 'BoD' }}
                                </span>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="deleteMember(member)"
                            class="inline-flex items-center justify-center rounded-md p-1 text-red-400 hover:bg-red-50 hover:text-red-600 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300 transition shrink-0"
                            title="Hapus Anggota"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div v-else class="flex flex-col items-center justify-center gap-2 py-8 text-center border border-dashed border-slate-200 rounded-xl dark:border-white/10">
                    <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada anggota.<br>Pilih tipe anggota di atas untuk menambahkan.</p>
                </div>
            </div>
        </div>
    </ConfirmationModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
    bods: {
        type: Array,
        default: () => [],
    },
    functions: {
        type: Array,
        default: () => [],
    },
    functionalOrganizations: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const isMemberModalOpen = ref(false);
const memberType = ref('bod');
const memberCompanyId = ref('');
const selectedFunctional = ref(null);
const selectedStructure = ref(null);
const bodSearch = ref('');

const selectedItemId = ref(null);

const memberForm = useForm({
    functional_id: '',
    structure_id: '',
    organization_id: '',
    member_type: 'bod',
});

// Clear selections when switching company or member type
watch(memberCompanyId, () => {
    selectedItemId.value = null;
    memberForm.organization_id = '';
    bodSearch.value = '';
});

watch(memberType, (newVal) => {
    selectedItemId.value = null;
    memberForm.organization_id = '';
    memberForm.member_type = newVal;
    bodSearch.value = '';
});

watch(() => props.functionalOrganizations, (newVal) => {
    if (selectedFunctional.value) {
        const updated = newVal.find(f => f.id === selectedFunctional.value.id);
        if (updated) {
            selectedFunctional.value = updated;
        }
    }
}, { deep: true });

const open = (functional, structure) => {
    selectedFunctional.value = functional;
    selectedStructure.value = structure;
    memberType.value = 'bod';
    selectedItemId.value = null;
    memberForm.clearErrors();
    memberForm.reset();
    memberForm.functional_id = functional.id;
    memberForm.structure_id = structure.structure_id;
    memberForm.member_type = 'bod';
    bodSearch.value = '';
    
    // Set default company to the current organization's company
    memberCompanyId.value = functional?.company_id ? String(functional.company_id) : '';
    
    isMemberModalOpen.value = true;
};

const closeModal = () => {
    isMemberModalOpen.value = false;
    bodSearch.value = '';
    emit('close', selectedFunctional.value);
};

const selectMemberId = (id) => {
    selectedItemId.value = id;
    memberForm.organization_id = id;
};

const submitAddMember = () => {
    memberForm.post(route('itom.business-process.organization-structure.functional.member.store'), {
        onSuccess: () => {
            selectedItemId.value = null;
            memberForm.reset('organization_id');
        },
    });
};

const deleteMember = (member) => {
    const isFunc = member.member_type === 'function';
    const orgId = isFunc ? member.function_id : member.organization_id;
    const payload = {
        member_type: member.member_type,
        functional_id: selectedFunctional.value.id,
        structure_id: isFunc ? null : selectedStructure.value.structure_id,
        organization_id: orgId,
    };
    const delForm = useForm(payload);
    delForm.delete(route('itom.business-process.organization-structure.functional.member.destroy'), {
        data: payload,
    });
};

// ─── Helpers ─────────────────────────────────────────────────────────────────
const currentMembersForStructure = computed(() => {
    if (!selectedFunctional.value || !selectedStructure.value) return [];
    const bods = (selectedFunctional.value.members || []).filter(
        m => Number(m.structure_id) === Number(selectedStructure.value.structure_id)
    );
    const funcs = (selectedFunctional.value.assigned_functions || []);
    return [...bods, ...funcs];
});

const availableBodsForCompany = computed(() => {
    if (!selectedFunctional.value || !selectedStructure.value) return [];
    const targetCompanyId = memberCompanyId.value;
    let list = props.bods;
    if (targetCompanyId) {
        list = list.filter(b => String(b.company_id) === String(targetCompanyId));
    }
    const currentMemberIdsForStructure = new Set(
        currentMembersForStructure.value
            .filter(m => m.member_type === 'bod')
            .map(m => Number(m.organization_id))
    );
    return list.filter(b => !currentMemberIdsForStructure.has(Number(b.id)));
});

const availableFunctionsForCompany = computed(() => {
    if (!selectedFunctional.value) return [];
    const targetCompanyId = memberCompanyId.value;
    let list = props.functions;
    if (targetCompanyId) {
        list = list.filter(f => String(f.company_id) === String(targetCompanyId));
    }
    const currentFunctionIds = new Set(
        (selectedFunctional.value.assigned_functions || []).map(m => Number(m.function_id))
    );
    return list.filter(f => !currentFunctionIds.has(Number(f.id)));
});

const flattenWithLevel = (items, idKey = 'id', parentKey = 'parent_id') => {
    if (!items || items.length === 0) return [];
    const map = {};
    const roots = [];
    items.forEach(item => { map[item[idKey]] = { ...item, _children: [], _level: 0 }; });
    items.forEach(item => {
        if (item[parentKey] && map[item[parentKey]]) {
            map[item[parentKey]]._children.push(map[item[idKey]]);
        } else {
            roots.push(map[item[idKey]]);
        }
    });
    const result = [];
    const walk = (node, level) => {
        node._level = level;
        result.push(node);
        node._children.forEach(child => walk(child, level + 1));
    };
    roots.forEach(r => walk(r, 0));
    return result;
};

const filteredAvailableItems = computed(() => {
    const q = bodSearch.value.toLowerCase().trim();
    if (memberType.value === 'bod') {
        const flatBods = flattenWithLevel(availableBodsForCompany.value);
        if (!q) return flatBods;
        return availableBodsForCompany.value
            .filter(b => b.name.toLowerCase().includes(q))
            .map(b => ({ ...b, _level: 0 }));
    } else {
        const list = availableFunctionsForCompany.value;
        if (!q) return list.map(f => ({ ...f, _level: 0 }));
        return list
            .filter(f => f.name.toLowerCase().includes(q))
            .map(f => ({ ...f, _level: 0 }));
    }
});

const selectedItemName = computed(() => {
    if (!selectedItemId.value) return '';
    if (memberType.value === 'bod') {
        const bod = props.bods.find(b => Number(b.id) === Number(selectedItemId.value));
        if (!bod) return '';
        return bod.company_name ? `${bod.name} (${bod.company_name})` : bod.name;
    } else {
        const fun = props.functions.find(f => Number(f.id) === Number(selectedItemId.value));
        if (!fun) return '';
        const company = props.companies.find(c => Number(c.id) === Number(fun.company_id));
        return company ? `${fun.name} (${company.name})` : fun.name;
    }
});

defineExpose({
    open,
    close: closeModal,
});
</script>
