<template>
    <!-- Page Header (print:hidden) -->
    <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717] print:hidden">
        <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

        <div class="relative flex flex-col gap-5">
            <!-- Title and Info -->
            <div class="flex flex-col gap-1.5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">
                    {{ activeRegulation?.judul || 'PEDOMAN TATA KELOLA TEKNOLOGI INFORMASI' }}
                </p>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Kelola Dokumen</h1>
            <            <            <            <            <            <       id         er            <            <         secti            <            <            <            <            <            <       id         er            <            <         secti            <    ulation: {
        type: Object,
        default: () => null
    }
});
</script>
