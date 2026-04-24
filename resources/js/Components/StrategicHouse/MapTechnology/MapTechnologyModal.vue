<script setup>
import { computed, reactive, ref, watch } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    initiativeOptions: {
        type: Array,
        default: () => [],
    },
    techColumns: {
        type: Array,
        default: () => [],
    },
    processing: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close', 'confirm']);

const form = reactive({
    initiative_id: '',
    coe_ids: [],
});

const resetForm = () => {
    form.initiative_id = '';
    form.coe_ids = [];
};

watch(() => props.show, (isVisible) => {
    if (isVisible) {
        resetForm();
    }
});

const canSubmit = computed(() => {
    return form.initiative_id !== '' && form.coe_ids.length > 0;
});

const handleConfirm = () => {
    if (!canSubmit.value) return;
    emit('confirm', { ...form });
};

const initiativeOptionLabel = (initiative) => {
    const code = String(initiative?.code ?? '').trim().replace(/#/g, '');
    const name = String(initiative?.name ?? '').trim();

    if (code !== '' && name !== '') {
        return `[${code}] ${name}`;
    }
    return name || code || '-';
};

const toggleTech = (id) => {
    const index = form.coe_ids.indexOf(id);
    if (index === -1) {
        form.coe_ids.push(id);
    } else {
        form.coe_ids.splice(index, 1);
    }
};
</script>

<template>
    <ConfirmationModal
        :show="show"
        title="Tambah Mapping Teknologi"
        message="Pilih initiative dan teknologi yang ingin dimapping."
        confirm-text="Simpan"
        cancel-text="Batal"
        :loading="processing"
        @close="emit('close')"
        @confirm="handleConfirm"
    >
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Initiative</label>
                <select
                    v-model="form.initiative_id"
                    class="edit-select"
                >
                    <option value="">- Pilih Initiative -</option>
                    <option
                        v-for="option in initiativeOptions"
                        :key="`modal-ini-opt-${option.id}`"
                        :value="option.id"
                    >
                        {{ initiativeOptionLabel(option) }}
                    </option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Teknologi (CoE)</label>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <label
                        v-for="tech in techColumns"
                        :key="`modal-tech-${tech.id}`"
                        class="flex items-center gap-2 rounded-lg border border-slate-200 p-2 transition-colors hover:bg-slate-50 dark:border-white/10 dark:hover:bg-white/5 cursor-pointer"
                        :class="{ 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800': form.coe_ids.includes(tech.id) }"
                    >
                        <input
                            type="checkbox"
                            :value="tech.id"
                            :checked="form.coe_ids.includes(tech.id)"
                            @change="toggleTech(tech.id)"
                            class="rounded border-slate-300 text-[#1C75BC] focus:ring-[#1C75BC] dark:border-slate-700 dark:bg-slate-800"
                        />
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ tech.label }}</span>
                    </label>
                </div>
            </div>

            <p
                v-if="error"
                class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700"
            >
                {{ error }}
            </p>
        </div>
    </ConfirmationModal>
</template>

<style scoped>
.edit-select {
    width: 100%;
    height: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    background: #ffffff;
    padding: 0 0.875rem;
    font-size: 12px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.edit-select:focus {
    border-color: #0f6fb7;
    box-shadow: 0 0 0 4px rgba(15, 111, 183, 0.12);
}

:deep(.dark) .edit-select {
    background-color: #1e293b;
    border-color: rgba(148, 163, 184, 0.2);
    color: #f1f5f9;
}
</style>
