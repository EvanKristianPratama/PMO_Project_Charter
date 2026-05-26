<template>
    <div class="w-full">
        <!-- Horizontal Segmented Chapter Switcher -->
        <div class="relative overflow-x-auto pb-2 scrollbar-none">
            <nav class="flex flex-row gap-1.5 rounded-2xl bg-slate-100 p-1.5 dark:bg-white/5 min-w-[760px] md:min-w-0 md:w-full">
                <Link
                    v-for="chapter in chapters"
                    :key="chapter.id"
                    :href="chapter.href"
                    class="flex-1 flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-center text-xs font-black tracking-wide uppercase transition-all duration-300 transform active:scale-[0.98]"
                    :class="[
                        chapter.active
                            ? 'bg-white text-[#821f44] shadow-md shadow-[#821f44]/5 scale-[1.01] border border-slate-200/50 dark:bg-[#1a1a1a] dark:text-white dark:border-white/5'
                            : 'text-slate-500 hover:text-slate-900 hover:bg-white/40 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-white/5'
                    ]"
                >
                    <component
                        :is="chapter.icon"
                        class="w-4 h-4 shrink-0 transition-transform duration-300"
                        :class="[chapter.active ? 'text-[#821f44] dark:text-[#db588c] scale-110' : 'text-slate-400 group-hover:text-slate-600']"
                    />
                    <span>{{ chapter.label }}</span>
                </Link>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    BookOpenIcon, 
    ShieldCheckIcon, 
    UsersIcon, 
    FlagIcon 
} from '@heroicons/vue/24/outline';

// Define the 4 Chapters with icons, labels and safe route paths
const chapters = computed(() => [
    {
        id: 'bab1',
        label: 'Bab I: Pendahuluan',
        href: route('policy.guidance.introduction'),
        active: route().current('policy.guidance.introduction'),
        icon: BookOpenIcon,
    },
    {
        id: 'bab2',
        label: 'Bab II: Kebijakan',
        href: route('policy.general.index'),
        active: route().current('policy.general.index') || route().current('policy.general.manage') || route().current('policy.specific.manage'),
        icon: ShieldCheckIcon,
    },
    {
        id: 'bab3',
        label: 'Bab III: Tanggung Jawab',
        href: route('policy.roles.index'),
        active: route().current('policy.roles.index') || route().current('policy.roles.manage'),
        icon: UsersIcon,
    },
    {
        id: 'bab4',
        label: 'Bab IV: Penutup',
        href: route('policy.guidance.closing'),
        active: route().current('policy.guidance.closing'),
        icon: FlagIcon,
    },
]);
</script>

<style scoped>
/* Hide standard scrollbars for clean aesthetic */
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
