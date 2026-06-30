<template>
    <ModulLayout title="Kelola Aliran Informasi ITSP">
        <div class="animate-fade-in-up space-y-6">
            <!-- Page Header -->
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-[#821f44]/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-blue-500/5 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">Pengelolaan Aliran Informasi ITSP</p>
                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kelola Aliran Informasi</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            :href="route('raci.itsp-infoflow.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            </svg>
                            Lihat Diagram
                        </Link>
                        <button
                            @click="showCobitPreview = !showCobitPreview"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#821f44] dark:text-[#a83262]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            </svg>
                            {{ showCobitPreview ? 'Sembunyikan COBIT' : 'Bandingkan COBIT' }}
                        </button>
                        <button
                            @click="triggerBulkSync"
                            :disabled="syncing"
                            class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-800 shadow-sm transition hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-300 dark:hover:bg-amber-900/30 active:scale-95 disabled:opacity-50"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4" :class="{'animate-spin': syncing}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            {{ syncing ? 'Menyinkronkan...' : 'Sinkronisasi Default COBIT' }}
                        </button>
                        <button
                            @click="openAddModal"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#821f44] px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-[#821f44]/25 transition-all hover:bg-[#9c2552] hover:shadow-[#821f44]/40 active:scale-95"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Data
                        </button>
                    </div>
                </div>
            </section>

            <section v-if="showCobitPreview" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="flex flex-col gap-4 border-b border-slate-200 p-5 dark:border-white/10 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#821f44] dark:text-[#a83262]">
                            Referensi COBIT
                        </p>
                        <h2 class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
                            Preview Information Flow COBIT untuk komparasi
                        </h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Panel ini membantu membandingkan referensi COBIT saat mengisi data ITSP di bawahnya.
                        </p>
                    </div>
                    <Link
                        :href="route('raci.infoflow.index')"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                    >
                        Buka di Tab Baru
                    </Link>
                </div>
                <div class="bg-slate-50 p-3 dark:bg-black/20">
                    <iframe
                        :src="route('raci.infoflow.index')"
                        class="h-[900px] w-full rounded-xl border border-slate-200 bg-white dark:border-white/10 dark:bg-[#111111]"
                        title="COBIT Information Flow Preview"
                    ></iframe>
                </div>
            </section>

            <!-- Tab Navigation & Search Filter -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-white/10 pb-4">
                <div class="flex items-center gap-2">
                    <button
                        @click="activeTab = 'inputs'"
                        class="px-5 py-2.5 text-sm font-extrabold rounded-xl transition cursor-pointer border"
                        :class="activeTab === 'inputs'
                            ? 'bg-[#821f44] text-white border-[#821f44] shadow-sm'
                            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 dark:bg-transparent dark:text-slate-400 dark:border-white/10 dark:hover:bg-white/5'"
                    >
                        Inputs (Masukan)
                    </button>
                    <button
                        @click="activeTab = 'outputs'"
                        class="px-5 py-2.5 text-sm font-extrabold rounded-xl transition cursor-pointer border"
                        :class="activeTab === 'outputs'
                            ? 'bg-[#821f44] text-white border-[#821f44] shadow-sm'
                            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 dark:bg-transparent dark:text-slate-400 dark:border-white/10 dark:hover:bg-white/5'"
                    >
                        Outputs (Keluaran)
                    </button>
                </div>

                <!-- Client-Side Search input -->
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari Kode Aktifitas / Deskripsi..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-white dark:bg-[#1a1a1a] text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-white/10 rounded-xl focus:border-[#821f44] focus:ring-1 focus:ring-[#821f44]/20 focus:outline-none"
                    />
                </div>
            </div>

            <!-- Inputs Tab Content -->
            <div v-if="activeTab === 'inputs'" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                                <th scope="col" class="px-6 py-4 w-44">Kode Aktifitas</th>
                                <th scope="col" class="px-6 py-4 w-48">Dari (Source)</th>
                                <th scope="col" class="px-6 py-4">Deskripsi Masukan</th>
                                <th scope="col" class="w-28 px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-if="filteredInputs.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data input. Klik "+ Tambah Data" atau "Sinkronisasi Default COBIT" untuk memasukkan data.
                                </td>
                            </tr>
                            <tr
                                v-for="(input, idx) in filteredInputs"
                                :key="input.id"
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                            >
                                <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                    {{ idx + 1 }}
                                </td>
                                <td class="px-6 py-4 text-slate-900 dark:text-white font-bold">
                                    {{ input.practice_id }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-300 border border-blue-100 dark:border-blue-900/40">
                                        {{ cleanText(input.from) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                                    {{ cleanText(input.description) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="openEditModal('input', input)"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#821f44] hover:bg-[#821f44]/5 hover:border-[#821f44]/20 transition active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-[#db588c]"
                                            title="Edit Input"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteItem('input', input)"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-rose-400"
                                            title="Hapus Input"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Outputs Tab Content -->
            <div v-if="activeTab === 'outputs'" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-slate-500 dark:text-slate-400">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700 dark:bg-white/5 dark:text-slate-300">
                            <tr>
                                <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                                <th scope="col" class="px-6 py-4 w-44">Kode Aktifitas</th>
                                <th scope="col" class="px-6 py-4 w-48">Ke (Destination)</th>
                                <th scope="col" class="px-6 py-4">Deskripsi Keluaran</th>
                                <th scope="col" class="w-28 px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                            <tr v-if="filteredOutputs.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data output. Klik "+ Tambah Data" atau "Sinkronisasi Default COBIT" untuk memasukkan data.
                                </td>
                            </tr>
                            <tr
                                v-for="(output, idx) in filteredOutputs"
                                :key="output.id"
                                class="group hover:bg-slate-50/50 dark:hover:bg-white/5 transition duration-150"
                            >
                                <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">
                                    {{ idx + 1 }}
                                </td>
                                <td class="px-6 py-4 text-slate-900 dark:text-white font-bold">
                                    {{ output.practice_id }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold bg-pink-50 text-pink-700 dark:bg-pink-950/30 dark:text-pink-300 border border-pink-100 dark:border-pink-900/40">
                                        {{ cleanText(output.to) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                                    {{ cleanText(output.description) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="openEditModal('output', output)"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-[#821f44] hover:bg-[#821f44]/5 hover:border-[#821f44]/20 transition active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-[#db588c]"
                                            title="Edit Output"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteItem('output', output)"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:border-rose-200 transition active:scale-90 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-400 dark:hover:text-rose-400"
                                            title="Hapus Output"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add/Edit Modal dialog -->
            <Teleport to="body">
                <transition name="fade">
                    <div v-if="isModalOpen" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                        <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl dark:bg-[#1a1a1a] border border-slate-200 dark:border-white/10 overflow-y-auto max-h-[85vh] animate-scale-up">
                            <!-- Modal Header -->
                            <div class="bg-[#821f44] p-5 text-white flex items-center justify-between sticky top-0 z-10 shrink-0">
                                <h3 class="text-base font-bold flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    {{ editingId ? 'Edit Data Aliran' : 'Tambah Data Aliran Baru' }}
                                </h3>
                                <button @click="closeModal" class="text-white/80 hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Modal Form body -->
                            <form @submit.prevent="submitForm">
                                <div class="p-6 space-y-4">
                                    <!-- Type Selection (Inputs or Outputs) - Only active when creating new -->
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tipe Aliran:</label>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                <input
                                                    v-model="modalType"
                                                    type="radio"
                                                    value="input"
                                                    :disabled="!!editingId"
                                                    class="text-[#821f44] focus:ring-[#821f44]"
                                                />
                                                Input (Masukan)
                                            </label>
                                            <label class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                <input
                                                    v-model="modalType"
                                                    type="radio"
                                                    value="output"
                                                    :disabled="!!editingId"
                                                    class="text-[#821f44] focus:ring-[#821f44]"
                                                />
                                                Output (Keluaran)
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Practice Select Dropdown -->
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilih Aktifitas (Practice):</label>
                                        <select
                                            v-model="form.practice_id"
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                            required
                                        >
                                            <option value="" disabled>-- Pilih Aktifitas --</option>
                                            <option
                                                v-for="p in practices"
                                                :key="p.practice_id"
                                                :value="p.practice_id"
                                            >
                                                {{ p.practice_id }} - {{ cleanText(p.practice_name) }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.practice_id" class="text-xs text-rose-500 font-medium">{{ form.errors.practice_id }}</div>
                                    </div>

                                    <!-- From / To (Source / Destination) -->
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                            {{ modalType === 'input' ? 'Dari (Sumber Masukan):' : 'Ke (Tujuan Keluaran):' }}
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.fromOrTo"
                                            :placeholder="modalType === 'input' ? 'Contoh: 2.2.05 atau Outside COBIT' : 'Contoh: 3.1.01 atau Outside COBIT'"
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10"
                                            required
                                        />
                                        <div v-if="form.errors.from" class="text-xs text-rose-500 font-medium">{{ form.errors.from }}</div>
                                        <div v-if="form.errors.to" class="text-xs text-rose-500 font-medium">{{ form.errors.to }}</div>
                                    </div>

                                    <!-- Description -->
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Deskripsi Aliran:</label>
                                        <textarea
                                            v-model="form.description"
                                            rows="4"
                                            placeholder="Deskripsi detail tentang input/output yang dialirkan..."
                                            class="w-full bg-slate-50 text-slate-800 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#821f44]/20 focus:bg-white dark:bg-black/20 dark:text-white dark:border-white/10 leading-relaxed"
                                            required
                                        ></textarea>
                                        <div v-if="form.errors.description" class="text-xs text-rose-500 font-medium">{{ form.errors.description }}</div>
                                    </div>
                                </div>

                                <!-- Modal Footer Actions -->
                                <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-100 dark:bg-black/20 dark:border-white/5 sticky bottom-0 z-10 shrink-0">
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-white/10 dark:bg-[#1a1a1a] dark:text-slate-300 dark:hover:bg-white/5"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-[#821f44] px-4 py-2 text-sm font-bold text-white shadow-md shadow-[#821f44]/20 transition hover:bg-[#9c2552] disabled:opacity-60"
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1"></span>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </transition>
            </Teleport>
        </div>
    </ModulLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    inputs: {
        type: Array,
        required: true
    },
    outputs: {
        type: Array,
        required: true
    },
    practices: {
        type: Array,
        required: true
    },
    objectives: {
        type: Array,
        required: true
    }
});

const activeTab = ref('inputs');
const searchQuery = ref('');
const syncing = ref(false);
const showCobitPreview = ref(false);

const isModalOpen = ref(false);
const editingId = ref(null);
const modalType = ref('input'); // 'input' or 'output'

const form = useForm({
    practice_id: '',
    fromOrTo: '', // Unified field mapped to 'from' or 'to'
    description: '',
});

// Clean up text characters
function cleanText(val) {
    if (!val) return '';
    return String(val)
        .replace(/^["'\\\s]+|["'\\\s]+$/g, '')
        .replace(/\\"/g, '"')
        .trim();
}

// Client-Side Search Filters
const filteredInputs = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.inputs;
    return props.inputs.filter(item => 
        (item.practice_id && item.practice_id.toLowerCase().includes(q)) ||
        (item.from && item.from.toLowerCase().includes(q)) ||
        (item.description && item.description.toLowerCase().includes(q))
    );
});

const filteredOutputs = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.outputs;
    return props.outputs.filter(item => 
        (item.practice_id && item.practice_id.toLowerCase().includes(q)) ||
        (item.to && item.to.toLowerCase().includes(q)) ||
        (item.description && item.description.toLowerCase().includes(q))
    );
});

// Modals Setup
function openAddModal() {
    editingId.value = null;
    modalType.value = activeTab.value === 'inputs' ? 'input' : 'output';
    form.reset();
    form.clearErrors();
    form.practice_id = '';
    isModalOpen.value = true;
}

function openEditModal(type, item) {
    editingId.value = item.id;
    modalType.value = type;
    form.clearErrors();

    form.practice_id = item.practice_id;
    form.fromOrTo = type === 'input' ? item.from : item.to;
    form.description = item.description;

    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
}

// Submit forms via standard Inertia routes
function submitForm() {
    // Map unified field to standard form attributes
    const payload = {
        practice_id: form.practice_id,
        description: form.description
    };
    if (modalType.value === 'input') {
        payload.from = form.fromOrTo;
    } else {
        payload.to = form.fromOrTo;
    }

    // Set the properties on the useForm instance manually
    form.clearErrors();
    
    // Assign fields dynamically to matching useForm attributes
    if (modalType.value === 'input') {
        form.from = form.fromOrTo;
        delete form.to;
    } else {
        form.to = form.fromOrTo;
        delete form.from;
    }

    if (editingId.value) {
        const routeName = modalType.value === 'input' 
            ? 'raci.itsp-infoflow.input.update' 
            : 'raci.itsp-infoflow.output.update';
            
        form.put(route(routeName, editingId.value), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Perubahan aliran informasi berhasil disimpan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            onError: () => {
                Swal.fire('Error', 'Gagal memperbarui data.', 'error');
            }
        });
    } else {
        const routeName = modalType.value === 'input' 
            ? 'raci.itsp-infoflow.input.store' 
            : 'raci.itsp-infoflow.output.store';

        form.post(route(routeName), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Aliran informasi berhasil ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            onError: () => {
                Swal.fire('Error', 'Gagal menambahkan data baru.', 'error');
            }
        });
    }
}

// Delete Item
function deleteItem(type, item) {
    const displayName = type === 'input' ? 'Input' : 'Output';
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus data ${displayName} untuk Aktifitas: ${item.practice_id}. Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const routeName = type === 'input' 
                ? 'raci.itsp-infoflow.input.destroy' 
                : 'raci.itsp-infoflow.output.destroy';

            router.delete(route(routeName, item.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Data berhasil dihapus.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                onError: () => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
}

// Bulk sync from COBIT API
async function triggerBulkSync() {
    Swal.fire({
        title: 'Sinkronisasi Default COBIT',
        text: 'Tindakan ini akan mengunduh seluruh data input/output standar dari COBIT API dan menimpa data lokal yang ada saat ini. Proses ini dapat memakan waktu 1-2 menit.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, sinkronkan!',
        cancelButtonText: 'Batal'
    }).then(async (result) => {
        if (result.isConfirmed) {
            syncing.value = true;
            
            // Show loading alert
            Swal.fire({
                title: 'Menyinkronkan...',
                text: 'Mengunduh data standar dari COBIT API, mohon tunggu sebentar...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('/raci/itsp-infoflow/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Server returned status ${response.status}`);
                }

                const res = await response.json();
                if (!res.success) {
                    throw new Error(res.message || 'Gagal sinkronisasi');
                }

                Swal.fire({
                    title: 'Berhasil!',
                    text: res.message || 'Sinkronisasi data default COBIT berhasil diselesaikan.',
                    icon: 'success'
                });

                // Reload the page content to show newly fetched items
                router.reload();

            } catch (err) {
                console.error(err);
                Swal.fire({
                    title: 'Gagal Sinkronisasi',
                    text: err.message || 'Terjadi kesalahan koneksi saat mengunduh data default COBIT.',
                    icon: 'error'
                });
            } finally {
                syncing.value = false;
            }
        }
    });
}
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}

.animate-scale-up {
    animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
