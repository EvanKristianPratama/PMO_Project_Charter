<template>
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#171717]">
        <!-- Header -->
        <div class="bg-[#0b2545] px-4 py-2.5 text-center">
            <h2 class="text-xs font-semibold tracking-wider text-white uppercase">IT Steering Commitee</h2>
        </div>

        <!-- Empty State -->
        <div v-if="!steeringRows || steeringRows.length === 0" class="px-2 py-8 text-center bg-[#e8edf2] dark:bg-[#1e2530]">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a5.97 5.97 0 00-.94 3.197M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Data IT Steering Committee</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada data IT Steering Committee.</p>
        </div>

        <!-- Diagram Canvas -->
        <div v-else class="w-full overflow-x-auto bg-[#e8edf2] dark:bg-[#1e2530] px-4 py-6 flex justify-center">
            <div class="relative w-[800px] h-[300px] flex-shrink-0 select-none">
                
                <!-- SVG Connector Lines -->
                <svg class="absolute inset-0 w-full h-full pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Main Vertical Line from Ketua to Anggota Tetap -->
                    <line x1="210" y1="80" x2="210" y2="180" stroke="#0b3350" stroke-width="2" class="dark:stroke-slate-400" />
                    
                    <!-- Horizontal Line to Sekretaris (Ends at left of Sekretaris: 440) -->
                    <line x1="210" y1="95" x2="440" y2="95" stroke="#0b3350" stroke-width="2" class="dark:stroke-slate-400" />
                    
                    <!-- Dotted Blue Line to Anggota Ad Hoc (Ends at center of Ad Hoc: 580) -->
                    <path d="M 210,145 L 580,145 L 580,180" stroke="#009fe3" stroke-width="2" stroke-dasharray="3,3" fill="none" />
                </svg>

                <!-- 1. Ketua Komite Card (Smaller Container & Content) -->
                <div class="absolute left-[115px] top-[20px] w-[190px] h-[60px] bg-gradient-to-b from-[#114e7a] to-[#0b3452] rounded-lg shadow-md flex flex-col justify-center items-center text-white px-2 text-center border border-white/10">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider mb-0.5">Ketua Komite</h3>
                    <div class="text-[9.5px] font-light leading-snug">
                        <div v-for="member in ketuaMembers" :key="member.code">
                            {{ member.organization_name }}
                        </div>
                        <div v-if="ketuaMembers.length === 0" class="italic opacity-60">Direktur Utama</div>
                    </div>
                </div>

                <!-- 2. Sekretaris Komite Card (Wider Card, Smaller Content) -->
                <div class="absolute left-[440px] top-[62px] w-[280px] h-[66px] bg-gradient-to-b from-[#114e7a] to-[#0b3452] rounded-lg shadow-md flex flex-col justify-center items-center text-white px-3 text-center border border-white/10">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider mb-0.5">Sekretaris Komite</h3>
                    <div class="text-[9.5px] font-light leading-normal space-y-0.5 w-full">
                        <template v-if="sekretarisMembers.length > 0">
                            <div v-for="member in sekretarisMembers" :key="member.code" class="truncate w-full">
                                {{ member.organization_name }}
                            </div>
                        </template>
                        <template v-else>
                            <div class="truncate">SVP Enterprise IT</div>
                            <div class="truncate">VP Enterprise IT SARM</div>
                        </template>
                    </div>
                </div>

                <!-- 3. Anggota Tetap Komite Card (Dynamic height based on content) -->
                <div class="absolute left-[65px] top-[180px] w-[290px] min-h-[75px] h-auto bg-gradient-to-b from-[#114e7a] to-[#0b3452] rounded-xl shadow-lg flex flex-col justify-start text-white p-3 border border-white/10">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-center mb-2">Anggota Tetap Komite</h3>
                    <div class="text-[9.5px] font-light space-y-1">
                        <template v-if="anggotaTetapMembers.length > 0">
                            <div v-for="(member, idx) in anggotaTetapMembers" :key="member.code" class="flex items-start gap-1">
                                <span class="font-semibold">{{ idx + 1 }}.</span>
                                <span>{{ member.organization_name }}</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-start gap-1"><span class="font-semibold">1.</span> <span>Direktur Penunjang Bisnis</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">2.</span> <span>Direktur Manajemen Risiko</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">3.</span> <span>Direktur Keuangan</span></div>
                        </template>
                    </div>
                </div>

                <!-- 4. Anggota Ad Hoc Card (Dynamic height based on content) -->
                <div class="absolute left-[435px] top-[180px] w-[290px] min-h-[75px] h-auto bg-gradient-to-b from-[#009fe3] to-[#0082c3] border border-[#009fe3] rounded-xl shadow-lg flex flex-col justify-start text-white p-3">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-center mb-2">
                        Anggota Ad Hoc
                    </h3>
                    <div class="text-[9.5px] font-light space-y-1">
                        <template v-if="anggotaAdHocMembers.length > 0">
                            <div v-for="(member, idx) in anggotaAdHocMembers" :key="member.code" class="flex items-start gap-1">
                                <span class="font-semibold">{{ idx + 1 }}.</span>
                                <span>{{ member.organization_name }}</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-start gap-1"><span class="font-semibold">1.</span> <span>Direktur Holding</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">2.</span> <span>Direktur Utama Subholding</span></div>
                            <div class="flex items-start gap-1"><span class="font-semibold">3.</span> <span>Direktur Utama APFS</span></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

defineOptions({
    name: 'ITSteeringComitte',
});

const props = defineProps({
    steeringRows: {
        type: Array,
        default: () => [],
    },
});

/**
 * Parse code LLDDSSNN:
 * LL = Level Organisasi (e.g. 10 = Komite IT)
 * DD = Level Ketua/Member (01 = Ketua, 02 = Member)
 * SS = Sub Struktur (01 = Sekretaris, 02 = Anggota Tetap, 03 = Anggota Ad Hoc)
 * NN = Nomor Urut (01-99)
 */
const parseCode = (code) => {
    const c = String(code ?? '').trim().padEnd(8, '0');
    return {
        ll: c.substring(0, 2), // Level Organisasi
        dd: c.substring(2, 4), // Level Ketua/Member
        ss: c.substring(4, 6), // Sub Struktur
        nn: c.substring(6, 8), // Nomor Urut
    };
};

const sortedRows = computed(() => {
    if (!props.steeringRows || props.steeringRows.length === 0) return [];
    return [...props.steeringRows].sort((a, b) => {
        const codeA = String(a.code ?? '').trim();
        const codeB = String(b.code ?? '').trim();
        return codeA.localeCompare(codeB);
    });
});

// DD=01 → Ketua
const ketuaMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd } = parseCode(row.code);
        return dd === '01';
    });
});

// DD=02, SS=01 → Sekretaris
const sekretarisMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd, ss } = parseCode(row.code);
        return dd === '02' && ss === '01';
    });
});

// DD=02, SS=02 → Anggota Tetap
const anggotaTetapMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd, ss } = parseCode(row.code);
        return dd === '02' && ss === '02';
    });
});

// DD=02, SS=03 → Anggota Ad Hoc
const anggotaAdHocMembers = computed(() => {
    return sortedRows.value.filter((row) => {
        const { dd, ss } = parseCode(row.code);
        return dd === '02' && ss === '03';
    });
});
</script>