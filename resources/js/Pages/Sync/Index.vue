<script setup>
import { computed, ref, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useRouteHelper } from '@/Composables/useRouteHelper';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
    CloudArrowDownIcon,
    ServerStackIcon,
    CircleStackIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    CommandLineIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const route = useRouteHelper();

const isProcessing = ref(false);
const logs = ref([]);
const finalMessage = ref(null);
const finalStatus = ref(null); // 'success', 'error', 'warning'
const logContainerRef = ref(null);

const scrollToBottom = async () => {
    await nextTick();
    if (logContainerRef.value) {
        logContainerRef.value.scrollTop = logContainerRef.value.scrollHeight;
    }
};

async function submitSync() {
    if (!confirm('Peringatan: Seluruh data di database LOKAL akan ditimpa dengan data dari MASTER SERVER. Lanjutkan?')) {
        return;
    }

    isProcessing.value = true;
    logs.value = [];
    finalMessage.value = null;
    finalStatus.value = null;

    try {
        const response = await fetch(route('sync.pull'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'text/event-stream'
            }
        });

        if (!response.ok) {
            const text = await response.text();
            // Try to parse title from Laravel error HTML page if thrown
            const match = text.match(/<title>(.*?)<\/title>/i);
            const msg = match ? match[1] : `HTTP ${response.status} ${response.statusText}`;
            throw new Error(msg);
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;
            
            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            
            // Keep the incomplete last line in the buffer
            buffer = lines.pop() || '';

            for (const line of lines) {
                if (!line.trim()) continue;
                
                try {
                    const data = JSON.parse(line);
                    
                    if (data.event === 'progress') {
                        logs.value.push({
                            time: new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                            message: data.message,
                            type: data.type
                        });
                        scrollToBottom();
                    } 
                    else if (data.event === 'complete') {
                        finalStatus.value = data.success ? 'success' : 'warning';
                        finalMessage.value = data.message;
                    } 
                    else if (data.event === 'error') {
                        finalStatus.value = 'error';
                        finalMessage.value = data.message;
                    }
                } catch (e) {
                    console.debug('Partial line decode skipped', line);
                }
            }
        }

    } catch (err) {
        finalStatus.value = 'error';
        finalMessage.value = 'Koneksi ke server terputus: ' + err.message;
    } finally {
        isProcessing.value = false;
        scrollToBottom();
    }
}

const getLogClass = (type) => {
    switch (type) {
        case 'success': return 'text-emerald-400';
        case 'error': return 'text-rose-400';
        case 'warning': return 'text-amber-400';
        default: return 'text-slate-300';
    }
};
</script>

<template>
    <UserLayout title="Sync Data (Git Pull Concept)">
        <div class="space-y-6 animate-fade-in">
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#171717]">
                <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-blue-500/10 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-cyan-400/10 blur-2xl"></div>

                <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-500 dark:text-blue-400">Maintenance Control</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Sinkronisasi Data (Pull New Data)</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Tarik data terbaru dari Master Server ke database Lokal Anda (Clone/Update konsep Git).
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        <div :class="[
                            'flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium',
                            stats.is_cloud_accessible 
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' 
                                : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300'
                        ]">
                            <div :class="['h-2 w-2 rounded-full', stats.is_cloud_accessible ? 'bg-emerald-500' : 'bg-rose-500']"></div>
                            {{ stats.is_cloud_accessible ? 'Master Connected' : 'Master Offline' }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notification Messages from local stream -->
            <div v-if="finalMessage && finalStatus === 'success'" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300 animate-fade-in">
                <CheckCircleIcon class="h-5 w-5 shrink-0" />
                <div>{{ finalMessage }}</div>
            </div>
            
            <div v-if="finalMessage && finalStatus === 'warning'" class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300 animate-fade-in">
                <ExclamationTriangleIcon class="h-5 w-5 shrink-0" />
                <div>{{ finalMessage }}</div>
            </div>

            <div v-if="finalMessage && finalStatus === 'error'" class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300 animate-fade-in">
                <ExclamationTriangleIcon class="h-5 w-5 shrink-0" />
                <div>{{ finalMessage }}</div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Connection Status Card -->
                <section class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="border-b border-slate-100 p-5 dark:border-white/10">
                        <h2 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            <ServerStackIcon class="h-5 w-5 text-blue-500" />
                            Konfigurasi Sinkronisasi
                        </h2>
                    </div>
                    <div class="flex-1 p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-white/5">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Database Lokal (Tujuan)</p>
                                <p class="mt-1 font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                    <CircleStackIcon class="h-4 w-4" />
                                    {{ stats.local_driver.toUpperCase() }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-white/5">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Database Master (Sumber)</p>
                                <p class="mt-1 font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                    <CloudArrowDownIcon class="h-4 w-4 text-blue-500" />
                                    MYSQL MASTER SERVER
                                </p>
                            </div>
                        </div>
                        
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-black/20 border border-slate-200 dark:border-white/10">
                            <h3 class="text-xs font-semibold text-slate-800 dark:text-slate-200 mb-2">Bagaimana cara kerjanya?</h3>
                            <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                                <li class="flex items-start gap-2">
                                    <span class="font-bold">1.</span> Sistem mematikan check relationship database sementara.
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="font-bold">2.</span> Menghapus record tabel di database lokal yang akan diperbarui.
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="font-bold">3.</span> Menarik row demi row data terbaru dari Master Server.
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="font-bold">4.</span> Memasukkan data ke database lokal secara bulk chunks.
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Action Card -->
                <section class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white p-8 text-center shadow-sm dark:border-white/10 dark:bg-[#171717]">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 mb-4">
                        <CloudArrowDownIcon class="h-10 w-10" :class="{ 'animate-bounce': isProcessing }" />
                    </div>
                    
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Tarik Data dari Master</h2>
                    <p class="max-w-xs text-sm text-slate-500 dark:text-slate-400 mb-6">
                        Tekan tombol di bawah untuk menyinkronkan data lokal Anda dengan data Master Server terbaru.
                    </p>

                    <button
                        @click="submitSync"
                        type="button"
                        class="w-full max-w-sm flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-blue-500/20 transition hover:bg-blue-700 hover:shadow-blue-500/40 focus:ring-4 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="isProcessing || !stats.is_cloud_accessible"
                    >
                        <span v-if="isProcessing" class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent mr-2"></span>
                        <CloudArrowDownIcon v-else class="h-5 w-5" />
                        {{ isProcessing ? 'Sedang Menarik Data...' : 'PULL NEW DATA NOW' }}
                    </button>
                    
                    <p v-if="!stats.is_cloud_accessible" class="mt-4 text-xs font-medium text-rose-500 flex items-center gap-1">
                        <ExclamationTriangleIcon class="h-4 w-4" />
                        Koneksi Master Server tidak tersedia. Cek .env atau koneksi internet Anda.
                    </p>
                    
                    <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/5 dark:text-amber-300">
                        <p><strong>Catatan:</strong> Proses ini memakan waktu tergantung kecepatan internet Anda.</p>
                    </div>
                </section>
            </div>

            <!-- Logs View Box -->
            <section v-if="logs.length > 0" class="rounded-2xl border border-slate-900 bg-slate-900 shadow-xl overflow-hidden animate-fade-in">
                <div class="flex items-center justify-between px-4 py-2 bg-slate-800 border-b border-slate-700">
                    <div class="flex items-center gap-2">
                        <CommandLineIcon class="h-4 w-4 text-slate-400" />
                        <span class="text-xs font-mono font-bold text-slate-300">SYNC LOGS</span>
                    </div>
                    <div v-if="isProcessing" class="flex items-center gap-2 text-xs text-blue-400">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        Running...
                    </div>
                </div>
                <div 
                    ref="logContainerRef"
                    class="h-72 overflow-y-auto p-4 font-mono text-[13px] leading-relaxed text-slate-300"
                >
                    <div v-for="(log, idx) in logs" :key="idx" class="mb-1 whitespace-pre-wrap">
                        <span class="text-slate-500">[{{ log.time }}]</span>
                        <span :class="['ml-2', getLogClass(log.type)]">{{ log.message }}</span>
                    </div>
                    <div v-if="isProcessing" class="inline-block mt-1 w-2 h-4 bg-white animate-pulse"></div>
                </div>
            </section>
        </div>
    </UserLayout>
</template>
