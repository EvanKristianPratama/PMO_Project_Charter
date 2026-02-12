<template>
    <GuestLayout>
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Left Panel: Login Form -->
            <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
                <div class="w-full max-w-md animate-fade-in">
                    <!-- Logo -->
                    <div class="text-center mb-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 mb-4 shadow-lg shadow-indigo-500/25">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">PMO Portal</h1>
                        <p class="text-xs font-medium text-indigo-500 dark:text-indigo-400 tracking-[0.2em] uppercase mt-1">Single Sign-On Platform</p>
                    </div>

                    <!-- Welcome -->
                    <div class="text-center mb-8 animate-fade-in delay-100">
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Selamat Datang</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Masuk dengan akun Google Anda untuk mengakses<br class="hidden sm:block" />
                            semua modul PMO Portal.
                        </p>
                    </div>

                    <!-- SSO Button -->
                    <div class="animate-fade-in delay-200">
                        <a
                            :href="googleAuthUrl"
                            class="group flex items-center justify-center gap-3 w-full px-6 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-medium shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Lanjutkan dengan Google</span>
                        </a>
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 text-center text-xs text-slate-400 animate-fade-in delay-300">
                        <p>
                            Dengan melanjutkan, Anda menyetujui
                            <a href="#" class="text-indigo-500 hover:text-indigo-600 underline-offset-2 hover:underline">Ketentuan Layanan</a>
                            dan
                            <a href="#" class="text-indigo-500 hover:text-indigo-600 underline-offset-2 hover:underline">Kebijakan Privasi</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Hero/Testimonial (hidden on mobile) -->
            <div class="hidden lg:flex flex-1 relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950">
                <!-- Decorative circles -->
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-purple-500/10 blur-3xl"></div>

                <div class="flex items-center justify-center p-12 w-full relative z-10">
                    <div class="max-w-lg animate-fade-in delay-300">
                        <!-- Quote Card -->
                        <div class="glass-dark rounded-2xl p-8 border border-white/10 shadow-2xl">
                            <p class="text-lg text-white/90 font-light leading-relaxed italic">
                                "Satu akun untuk mengakses semua modul enterprise. Lebih aman, lebih cepat, lebih efisien."
                            </p>

                            <div class="flex items-center gap-4 mt-6 pt-6 border-t border-white/10">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    P
                                </div>
                                <div>
                                    <p class="text-white font-semibold">PMO Portal</p>
                                    <p class="text-white/50 text-sm">Enterprise Architecture Solutions</p>
                                </div>
                            </div>
                        </div>

                        <!-- Carousel Dots -->
                        <div class="flex items-center justify-center gap-2 mt-8">
                            <span class="w-6 h-1.5 rounded-full bg-white/80"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-white/30"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-white/30"></span>
                        </div>
                    </div>
                </div>
            </div>
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
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import StatusModal from '@/Components/StatusModal.vue';

const googleAuthUrl = '/auth/google';
const page = usePage();

const showModal = ref(false);
const statusData = ref({});

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
});

const closeModal = () => {
    showModal.value = false;
};
</script>
