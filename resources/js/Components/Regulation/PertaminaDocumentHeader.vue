<template>
    <div
        class="border border-slate-900 dark:border-white text-[11px] text-slate-950 dark:text-white font-sans uppercase z-10 relative">
        <div
            class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-900 dark:divide-white">
            <!-- Left Column (Fungsi & Judul) -->
            <div class="md:col-span-7 flex flex-col divide-y divide-slate-900 dark:divide-white">
                <div class="p-3 flex items-start gap-1.5 min-h-[46px]">
                    <span class="font-bold shrink-0">FUNGSI :</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.owner }}</span>
                </div>
                <div class="p-3 flex items-start gap-1.5 min-h-[46px]">
                    <span class="font-bold shrink-0">JUDUL :</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.judul }}</span>
                </div>
            </div>
            <!-- Right Column (Metadata) -->
            <div class="md:col-span-5 flex flex-col divide-y divide-slate-900 dark:divide-white">
                <div class="p-2.5 flex items-center gap-1.5 min-h-[23px]">
                    <span class="font-bold shrink-0">NOMOR :</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">{{ activeRegulation?.nomor || '-' }}</span>
                </div>
                <div class="p-2.5 flex items-center min-h-[23px]">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-bold shrink-0">REVISI KE :</span>
                        <template v-if="[0, 1, 2, 3, 4].includes(parseInt(activeRegulation?.revisi))">
                            <span v-for="num in [0, 1, 2, 3, 4]" :key="num"
                                class="inline-flex items-center gap-1 mr-1">
                                <span
                                    class="w-3.5 h-3.5 border border-slate-900 dark:border-white flex items-center justify-center text-[10px] font-black bg-transparent select-none animate-none">
                                    {{ parseInt(activeRegulation?.revisi) === num ? '✓' : '' }}
                                </span>
                                <span class="font-mono">{{ num }}</span>
                            </span>
                        </template>
                        <template v-else>
                            <span
                                class="w-3.5 h-3.5 border border-slate-900 dark:border-white flex items-center justify-center text-[10px] font-black bg-transparent select-none mr-1">✓</span>
                            <span class="font-bold text-slate-900 dark:text-white mr-2">{{
                                activeRegulation?.revisi || '0' }}</span>
                        </template>
                    </div>
                </div>
                <div class="p-2.5 flex items-center gap-1.5 min-h-[23px]">
                    <span class="font-bold shrink-0">BERLAKU TMT :</span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ activeRegulation?.berlaku ? formatDate(activeRegulation.berlaku) : '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    activeRegulation: {
        type: Object,
        default: null,
    },
});

// Format Date helper
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
</script>
