<template>
    <div class="flex flex-col items-center justify-center py-32 animate-fade-in">
        <!-- Main Circular Progress -->
        <div class="relative w-32 h-32 mb-8">
            <svg class="w-full h-full transform -rotate-90">
                <circle
                    cx="64"
                    cy="64"
                    r="58"
                    fill="transparent"
                    stroke-width="10"
                    class="stroke-slate-200 dark:stroke-white/10"
                />
                <circle
                    cx="64"
                    cy="64"
                    r="58"
                    fill="transparent"
                    stroke-width="10"
                    stroke-dasharray="364.4"
                    :stroke-dashoffset="364.4 - (364.4 * progress) / 100"
                    stroke-linecap="round"
                    class="stroke-[#1C75BC] dark:stroke-blue-500 transition-all duration-300 ease-out"
                />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-black text-[#1C75BC] dark:text-blue-500 tracking-tighter">
                    {{ progress }}%
                </span>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mt-1">
                    Loading
                </span>
            </div>
        </div>

        <div class="flex flex-col items-center gap-1">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 tracking-wide uppercase">
                {{ title }}
            </h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium italic">
                {{ progress < 100 ? message : 'Data siap ditampilkan!' }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    progress: { type: Number, default: 0 },
    title: { type: String, default: 'Memproses Data' },
    message: { type: String, default: 'Mohon tunggu sebentar...' }
});
</script>
