<template>
    <div class="w-full">
        <!-- Horizontal Segmented Section Switcher -->
        <div class="relative overflow-x-auto pb-2 scrollbar-none">
            <nav class="flex flex-row gap-1.5 rounded-2xl bg-slate-100 p-1.5 dark:bg-white/5 min-w-[480px] md:min-w-0 md:w-full">
                <button
                    v-for="section in sections"
                    :key="section.id"
                    @click="$emit('update:modelValue', section.id)"
                    class="flex-1 flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-center text-xs font-black tracking-wide uppercase transition-all duration-300 active:scale-[0.98]"
                    :class="[
                        modelValue === section.id
                            ? 'bg-white text-[#821f44] shadow-md shadow-[#821f44]/5 scale-[1.01] border border-slate-200/50 dark:bg-[#1a1a1a] dark:text-white dark:border-white/5'
                            : 'text-slate-500 hover:text-slate-900 hover:bg-white/40 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-white/5'
                    ]"
                >
                    <!-- Users Icon -->
                    <svg v-if="section.id === 'fungsi'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                        class="w-4 h-4 shrink-0 transition-transform duration-300"
                        :class="[modelValue === section.id ? 'text-[#821f44] dark:text-[#db588c] scale-110' : 'text-slate-400']">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <!-- Document Text Icon -->
                    <svg v-else-if="section.id === 'prosedur'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                        class="w-4 h-4 shrink-0 transition-transform duration-300"
                        :class="[modelValue === section.id ? 'text-[#821f44] dark:text-[#db588c] scale-110' : 'text-slate-400']">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <!-- Diagram / Flowchart Icon -->
                    <svg v-else-if="section.id === 'diagram'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                        class="w-4 h-4 shrink-0 transition-transform duration-300"
                        :class="[modelValue === section.id ? 'text-[#821f44] dark:text-[#db588c] scale-110' : 'text-slate-400']">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                    </svg>
                    <span>{{ section.label }}</span>
                </button>
            </nav>
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: 'fungsi',
    },
});

defineEmits(['update:modelValue']);

const sections = [
    { id: 'fungsi', label: 'Fungsi / Unit Organisasi' },
    { id: 'prosedur', label: 'Prosedur / SOP' },
    { id: 'diagram', label: 'Diagram Alir' }
];
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>