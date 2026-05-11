<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-6 shadow-xl dark:border-white/10 dark:bg-[#1a1a1a]">
            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Hapus Data</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Yakin ingin menghapus <strong>{{ name }}</strong>?
            </p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" @click="$emit('cancel')"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5">Batal</button>
                <button type="button" @click="$emit('confirm')"
                    class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Hapus</button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    name: { type: String, required: true },
});
defineEmits(['cancel', 'confirm']);
</script>
