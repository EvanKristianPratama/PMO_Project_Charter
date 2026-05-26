<template>
    <!-- Floating Action Buttons (Fixed Bottom Right) -->
    <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-4 print:hidden">
        <!-- Scroll to Top Button -->
        <button @click="scrollToTop" title="Kembali ke Atas"
            class="group flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white/90 shadow-2xl backdrop-blur-md transition-all hover:bg-white hover:text-[#821f44] dark:border-white/10 dark:bg-[#1a1a1a]/90 dark:text-slate-300 dark:hover:bg-[#1a1a1a] active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
        </button>

        <!-- Print Button -->
        <bu        <bu        <bu        <bu        <bu        <bu        <bu        <bu-1        <em        <bu        <bu        <bu        <bu        late-2        <bu        <bu        <bu        <bu        <bu        <bu        <ber:        <bu        <bu        <bu   da        <bu        <bu        <bu        <bu        <bu  1a1a] active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.72 13.897l-1.2-6.82a2.25 2.25 0 012.23-2.64h9.5c1.12 0 2.07.82 2.23 1.94l.8 4.54a2.25 2.25 0 01-2.23 2.64H6.72z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 12h-15m15 0a2.25 2.25 0 012.25 2.25v3a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 013 1                  0              25z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 16.5h6m-6 3h6m-6-10.5h6m-6-3h6" />
            </svg>
        </button>

        <!-- Dynamic CRUD Buttons / Additional Actions via Slot -->
        <slot></slot>
    </div>
</template>

<script setup>
function printDocument() {
    window.print();
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>
