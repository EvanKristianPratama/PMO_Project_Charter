<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" class="relative z-50" @close="emit('close')">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-lg transform rounded-2xl border border-slate-200 bg-white p-6 shadow-xl transition-all dark:border-white/10 dark:bg-[#1a1a1a]"
                        >
                            <DialogTitle class="text-lg font-bold text-slate-900 dark:text-white">
                                New {{ tipeLabel }} Initiative
                            </DialogTitle>

                            <form class="mt-4 space-y-4" @submit.prevent="submit">
                                <!-- Code -->
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        Code
                                    </label>
                                    <input
                                        v-model="form.code"
                                        type="number"
                                        min="0"
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 dark:border-white/10 dark:bg-[#171717] dark:text-slate-200"
                                        placeholder="e.g. 1001"
                                    />
                                    <p v-if="form.errors.code" class="mt-1 text-xs text-red-500">{{ form.errors.code }}</p>
                                </div>

                                <!-- Name -->
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        Nama Initiative <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 dark:border-white/10 dark:bg-[#171717] dark:text-slate-200"
                                        placeholder="Nama initiative"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        Deskripsi
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 dark:border-white/10 dark:bg-[#171717] dark:text-slate-200"
                                        placeholder="Deskripsi initiative"
                                    />
                                    <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                                </div>

                                <!-- CoE -->
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        Centre of Excellence (CoE)
                                    </label>
                                    <select
                                        v-model="form.coe_id"
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 dark:border-white/10 dark:bg-[#171717] dark:text-slate-200"
                                    >
                                        <option :value="null">— Pilih CoE —</option>
                                        <option v-for="coe in coeOptions" :key="coe.id" :value="coe.id">
                                            {{ coe.name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.coe_id" class="mt-1 text-xs text-red-500">{{ form.errors.coe_id }}</p>
                                </div>

                                <!-- Project Owner -->
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        Project Owner
                                    </label>
                                    <select
                                        v-model="form.business_unit"
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400 dark:border-white/10 dark:bg-[#171717] dark:text-slate-200"
                                    >
                                        <option :value="null">— Pilih Project Owner —</option>
                                        <option v-for="org in organizationOptions" :key="org.id" :value="org.id">
                                            {{ org.groub ? `${org.groub} — ${org.name}` : org.name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.business_unit" class="mt-1 text-xs text-red-500">{{ form.errors.business_unit }}</p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                                        @click="emit('close')"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow transition hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        <span v-if="form.processing">Menyimpan...</span>
                                        <span v-else>Simpan</span>
                                    </button>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    tipeInitiative: {
        type: Number,
        required: true,
        validator: (v) => [1, 2].includes(v),
    },
    coeOptions: {
        type: Array,
        default: () => [],
    },
    organizationOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const tipeLabel = computed(() => (props.tipeInitiative === 1 ? 'Digital' : 'IT'));

const form = useForm({
    code: '',
    name: '',
    description: '',
    tipe_initiative: props.tipeInitiative,
    coe_id: null,
    business_unit: null,
    status: 'drafting',
});

// Keep tipe_initiative in sync if prop changes
watch(() => props.tipeInitiative, (val) => {
    form.tipe_initiative = val;
});

// Reset form when modal opens
watch(() => props.show, (open) => {
    if (open) {
        form.reset();
        form.clearErrors();
        form.tipe_initiative = props.tipeInitiative;
    }
});

const submit = () => {
    form.post(route('master-data.mst-initiatives.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
