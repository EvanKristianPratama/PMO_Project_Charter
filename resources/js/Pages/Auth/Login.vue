<template>
    <GuestLayout>
        <div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,560px)_1fr]">
            <section class="flex items-center justify-center px-6 py-14 sm:px-10 lg:px-16">
                <div class="w-full max-w-md">
                    <div class="mb-8 text-center">
                        <img
                            src="/logo-divusi.png"
                            alt="Divusi Logo"
                            class="mx-auto h-14 w-auto"
                        />
                        <p class="mt-5 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500">
                            IT Strategic Planning System
                        </p>
                        <h1 class="mt-7 text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Selamat Datang</h1>
                        <p class="mt-4 text-lg leading-relaxed text-slate-500 dark:text-slate-400">
                            Masuk dengan akun sosial media Anda untuk mengakses semua modul IT Strategic Planning System.
                        </p>
                    </div>

                    <a
                        :href="googleAuthUrl"
                        class="group flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-6 py-3.5 font-medium text-slate-700 shadow-sm transition-all duration-200 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-slate-600"
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>

                    <p class="mt-6 text-xs leading-relaxed text-slate-400 dark:text-slate-500">
                        Dengan melanjutkan, Anda menyetujui ketentuan layanan dan kebijakan privasi platform ini.
                    </p>
                </div>
            </section>

            <section class="relative hidden overflow-hidden lg:block">
                <div class="absolute inset-0">
                    <div
                        v-for="(slide, index) in slides"
                        :key="`bg-${index}`"
                        class="absolute inset-0 bg-cover bg-center transition-opacity duration-700"
                        :class="activeSlide === index ? 'opacity-100' : 'opacity-0'"
                        :style="{ backgroundImage: `url(${slide.image})` }"
                    ></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/65 via-slate-900/60 to-slate-950/80"></div>
                </div>

                <div class="relative z-10 flex h-full items-center justify-center p-12">
                    <div class="w-full max-w-xl">
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-8 shadow-2xl backdrop-blur-md">
                            <p class="text-lg font-light italic leading-relaxed text-white/90">
                                {{ slides[activeSlide].quote }}
                            </p>
                            <div class="mt-6 border-t border-white/15 pt-5">
                                <p class="text-sm font-semibold text-white">{{ slides[activeSlide].title }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.14em] text-white/65">{{ slides[activeSlide].subtitle }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button
                                    v-for="(slide, index) in slides"
                                    :key="`dot-${index}`"
                                    type="button"
                                    @click="activeSlide = index"
                                    class="h-1.5 rounded-full transition-all duration-300"
                                    :class="activeSlide === index ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/60'"
                                    :aria-label="`Go to slide ${index + 1}`"
                                ></button>
                            </div>
                            <p class="text-[11px] uppercase tracking-[0.14em] text-white/55">Photos: Unsplash</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <StatusModal
            :show="showModal"
            :status="statusData.status"
            :user="{ name: statusData.name, email: statusData.email }"
            @close="closeModal"
        />
    </GuestLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import StatusModal from '@/Components/StatusModal.vue';

const googleAuthUrl = '/auth/google';

const showModal = ref(false);
const statusData = ref({});
const activeSlide = ref(0);
let slideInterval = null;

const slides = [
    {
        image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1800&q=80',
        title: 'Program & Project Management',
        subtitle: 'Integrated Platform',
        quote: '"Satu wadah terintegrasi untuk seluruh siklus inisiatif perusahaan. Lebih terstruktur, lebih transparan."',
    },
    {
        image: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1800&q=80',
        title: 'Operational Excellence',
        subtitle: 'The "O" in PMO',
        quote: '"Mentransformasi "Office" menjadi pusat keunggulan operasional. Standarisasi proses untuk hasil yang konsisten."',
    },
    {
        image: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1800&q=80',
        title: 'Strategic Alignment',
        subtitle: 'Value Driven',
        quote: '"Memastikan setiap eksekusi proyek selaras dengan visi strategis organisasi."',
    },
];

onMounted(() => {
    // Check for query params 'status', 'name', 'email'
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status && ['pending', 'rejected', 'deactivated'].includes(status)) {
        statusData.value = {
            status: status,
            name: urlParams.get('name'),
            email: urlParams.get('email')
        };
        showModal.value = true;
    }

    // Start Carousel
    slideInterval = setInterval(() => {
        activeSlide.value = (activeSlide.value + 1) % slides.length;
    }, 5000);
});

onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
});

const closeModal = () => {
    showModal.value = false;
};
</script>
