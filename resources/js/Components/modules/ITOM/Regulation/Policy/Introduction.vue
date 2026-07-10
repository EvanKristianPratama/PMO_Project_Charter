<template>
    <div class="animate-fade-in-up space-y-6">

            <div class="max-w-4xl mx-auto bg-white dark:bg-[#1a1a1a] shadow-xl border border-slate-200 dark:border-white/10 p-4 rounded-2xl relative font-sans text-slate-800 dark:text-slate-200 print:shadow-none print:border-none print:p-0 print:m-0">
                <PertaminaDocumentHeader v-if="isHeaderVisible" :activeRegulation="activeRegulation" class="flex-1" />

                <div class="mt-8 text-center space-y-2 relative z-10">
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-[0.15em] text-slate-950 dark:text-white uppercase">
                        BAB I
                    </h2>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-[0.2em] text-slate-950 dark:text-white uppercase">
                        UMUM
                    </h2>
                </div>

                <div class="mt-10 space-y-4 text-slate-900 dark:text-slate-100 relative z-10 text-justify" style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5;">
                    <p class="indent-8">
                        Kata kunci yang sangat penting bagi suatu perusahaan agar mampu bertahan bahkan tumbuh dan berkembang, adalah pemanfaatan Informasi dan Teknologi (I&T). Dalam masyarakat informasi global saat ini, dimana informasi mengalir secara cepat tanpa terhalang oleh jarak dan waktu, menjadikan informasi sebagai aset yang sangat berharga dan dengan memanfaatkan teknologi secara optimal I&T merupakan aset yang sangat bernilai.
                    </p>
                    <p class="indent-8">
                        Pada saat ini perubahan lingkungan bisnis yang sangat cepat dan persaingan usaha yang sangat ketat, manajemen Perusahaan semakin menaruh harapan yang tinggi terhadap peran fungsi organisasi Teknologi Informasi (TI), yang tercermin dari keinginan manajemen Perusahaan terhadap peningkatan kualitas, mempercepat waktu penyerahan, dan peningkatan layanan yang berkesinambungan serta senantiasa mengupayakan agar semua kegiatan tersebut dapat dilaksanakan secara efektif dan efisien.
                    </p>
                    <p class="indent-8">
                        Sebagai antisipasi persaingan usaha yang sangat ketat sebagaimana disebut di atas, Perusahaan telah melakukan transformasi di segala bidang termasuk I&T and lingkungan operasinya. Transformasi bidang I&T menuntut pengelolaan I&T yang baik, sejalan dengan bisnis perusahaan dan kepatuhan (compliance) dengan aturan yang berlaku, serta mempertimbangkan aspek resiko yang mungkin timbul dan dihadapi. Kepatuhan (compliance) dimaksud sebagai upaya perwujudan Good Corporate Governance Perusahaan.
                    </p>
                    <p class="indent-8">
                        Di dalam Good Corporate Governance, Tata Kelola I&T (IT Governance) menjadi lebih berperan dan didefinisikan sebagai suatu struktur hubungan-hubungan dan proses-proses untuk mengarahkan dan mengendalikan Perusahaan agar mencapai tujuannya dengan menyeimbangkan resiko-resiko versus penghasilan/keuntungan melalui peran I&T.
                    </p>
                    <p class="indent-8">
                        Fungsi organisasi TI harus memenuhi tuntutan kualitas dan keamanan informasi perusahaan, sebagaimana diberlakukan terhadap aset lainnya. Manajemen juga harus mengoptimalkan penggunaan sumber daya yang tersedia, termasuk data, sistem aplikasi, teknologi, fasilitas-fasilitas dan SDM. Dalam rangka melaksanakan tanggung jawab untuk mencapai tujuan perusahaan, manajemen harus mengerti dan memahami kondisi I&T Perusahaan serta menetapkan pengaturan dan pengendaliannya melalui kebijakan-kebijakan.
                    </p>

                    <div class="space-y-4 mt-6">
                        <p class="indent-8">
                            Pedoman ini memuat kebijakan tentang kebutuhan manajemen untuk pengendalian, isu informasi, komunikasi dan teknologi untuk keperluan bisnis yang memenuhi kriteria keefektifan, keefisienan, kerahasiaan, keintegritasan, ketersediaan, keterpenuhan dan keandalan informasi yang dibutuhkan.
                        </p>
                        <p class="indent-8">
                            Mempertimbangkan hal-hal di atas dan dengan adanya perkembangan bisnis, perubahan kebijakan dan perubahan organisasi Pertamina, maka dipandang perlu untuk menerbitkan suatu Pedoman baru serta mencabut dan menggantikan Pedoman No. A-002/I10000/2010-S0 Revisi Ke - 1 tentang Penyelenggaraan Tata Kelola TI Perusahaan (IT Governance).
                        </p>
                    </div>

                    <div class="space-y-4 mt-6">
                        <h3 class="font-bold text-slate-950 dark:text-white pb-1">
                            A. Tujuan
                        </h3>
                        <p class="indent-8">
                            Pedoman ini disusun dengan tujuan:
                        </p>
                        <ol class="list-decimal pl-8 space-y-0">
                            <li>
                                Sebagai petunjuk untuk pelaksanaan tata kelola teknologi informasi dalam memenuhi persyaratan bisnis dan tata kelola I&T secara umum dengan pendekatan yang holistik sesuai struktur aturan penyelenggaraan Teknologi Informasi dari Kementerian BUMN Republik Indonesia.
                            </li>
                            <li>
                                Sebagai acuan dalam penyusunan Tata Kerja Organisasi (TKO), Tata Kerja Individu (TKI), Tata Kerja Penggunaan Alat (TKPA), dan Documented Information yang berkaitan dengan tata kelola TI di lingkungan Perusahaan.
                            </li>
                        </ol>
                    </div>

                    <div class="space-y-4 mt-6">
                        <h3 class="font-bold text-slate-950 dark:text-white pb-1">
                            B. Ruang Lingkup
                        </h3>
                        <p class="indent-8">
                            Ruang lingkup Pedoman ini mencakup seluruh aspek pengelolaan I&T perusahaan, yang meliputi kegiatan perencanaan, pengadaan, pengembangan, pengoperasian, pemeliharaan, pengawasan, dan pembinaan informasi, komunikasi dan teknologi di Perusahaan. Pedoman ini berlaku di Pertamina Holding dan dapat dijadikan acuan atau diratifikasi oleh Subholding/ Anak Perusahaan/ Perusahaan Terafiliasi Pertamina lainnya dengan terlebih dahulu melakukan mekanisme internal berdasarkan Anggaran Dasarnya masing-masing dan memenuhi ketentuan pemberlakuan STK Holding.
                        </p>
                    </div>
                </div>
            </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import PertaminaDocumentHeader from '@/Components/modules/ITOM/Regulation/PertaminaDocumentHeader.vue';

const props = defineProps({
    regulations: {
        type: Array,
        required: true,
    },
    selectedRegulationId: {
        type: Number,
        default: null,
    },
    isHeaderVisible: {
        type: Boolean,
        default: true,
    },
});

const selectedRegulationId = ref(props.selectedRegulationId);

const activeRegulation = computed(() => {
    if (!selectedRegulationId.value || props.regulations.length === 0) {
        return props.regulations[0] || null;
    }
    return props.regulations.find(r => r.id === selectedRegulationId.value) || null;
});
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
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
</style>
