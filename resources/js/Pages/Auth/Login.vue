<template>
    <GuestLayout>
        <div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,560px)_1fr]">
            <section class="flex items-center justify-center px-6 py-14 sm:px-10 lg:px-16">
                <div class="w-full max-w-md">
                    <div class="mb-8 text-center">
                        <img
                            src="/CISS1.png"
                            alt="Logo"
                            class="mx-auto mb-4 h-16 w-auto"
                        />
                        <p class="mt-5 text-[15px] font-semibold uppercase tracking-[0.16em] text-[#1c3b6e]">
                            Collaboration Information System
                        </p>
                    </div>

                    <!-- ABSOLUTE SYNC BUTTON -->
                    <button
                        type="button"
                        @click="triggerSync"
                        :disabled="isSyncing"
                        class="fixed right-14 top-4 z-30 flex items-center gap-1.5 rounded-lg bg-white/80 backdrop-blur p-2.5 text-xs font-bold uppercase tracking-wider text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-white dark:bg-white/5 dark:text-slate-300 dark:ring-white/10 dark:hover:bg-white/10 disabled:opacity-50"
                        title="Sync Data dari Master"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644h4.992m0 0a2.25 2.25 0 104.5 0m-4.5 0a2.25 2.25 0 014.5 0m0 0h8.523m-16.5-10.3h8.523m0 0a2.25 2.25 0 104.5 0m-4.5 0a2.25 2.25 0 014.5 0" />
                        </svg>
                        <span class="hidden sm:inline">Sync Master</span>
                    </button>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                id="email"
                                placeholder="Masukkan email Anda"
                                required
                                autocomplete="email"
                                class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                    class="block w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm placeholder:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600"
                                    @click="showPassword = !showPassword"
                                >
                                    <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input
                                    v-model="form.remember"
                                    id="remember-me"
                                    name="remember-me"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 dark:border-slate-700"
                                />
                                <label for="remember-me" class="ml-2 block text-sm font-medium text-slate-600 dark:text-slate-400">Ingat Saya</label>
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex w-full justify-center rounded-xl bg-[#1c3b6e] px-4 py-3 text-sm font-bold text-white shadow-md hover:bg-[#152c53] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1c3b6e] disabled:opacity-50 transition-all duration-200"
                            >
                                {{ form.processing ? 'Memuat...' : 'Masuk' }}
                            </button>
                        </div>
                    </form>


                </div>
            </section>

            <!-- Full Screen Sync Loader Overlay -->
            <div v-if="isSyncing" class="fixed inset-0 z-[100] flex items-center justify-center bg-white/80 backdrop-blur-md dark:bg-black/80 transition-all">
                <LoadingProgress 
                    :progress="Math.floor(syncProgress)" 
                    title="Menyinkronkan Data" 
                    message="Sedang memperbarui data lokal dari Server Master..." 
                />
            </div>

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
import { Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import StatusModal from '@/Components/StatusModal.vue';
import LoadingProgress from '@/Components/Shared/LoadingProgress.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const showModal = ref(false);
const statusData = ref({});
const activeSlide = ref(0);
const isSyncing = ref(false);
const syncProgress = ref(0);
let slideInterval = null;
let progressInterval = null;

const triggerSync = async () => {
    isSyncing.value = true;
    syncProgress.value = 0;
    
    // Start simple dynamic fake progress interval
    progressInterval = setInterval(() => {
        if (syncProgress.value < 90) {
            syncProgress.value += Math.random() * 15; // jumpy but natural
        } else if (syncProgress.value < 98) {
            syncProgress.value += 0.5; // slow down
        }
    }, 600);

    try {
        const response = await axios.post(route('public.sync'));
        
        // Force to 100 immediately
        clearInterval(progressInterval);
        syncProgress.value = 100;
        
        // Brief wait for UI animation satisfaction
        await new Promise(r => setTimeout(r, 400));

        if (response.data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sync Berhasil',
                text: response.data.message,
                confirmButtonColor: '#1c3b6e',
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Selesai dengan Catatan',
                text: response.data.message,
                confirmButtonColor: '#1c3b6e',
            });
        }
    } catch (error) {
        clearInterval(progressInterval);
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Sync Gagal',
            text: error.response?.data?.message || 'Terjadi kesalahan koneksi saat memperbarui data.',
            confirmButtonColor: '#1c3b6e',
        });
    } finally {
        isSyncing.value = false;
        clearInterval(progressInterval);
    }
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

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
