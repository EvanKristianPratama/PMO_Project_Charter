<template>
    <div class="w-full">
        <!-- Horizontal Segmented Chapter Switcher -->
        <div class="relative overflow-x-auto pb-1 scrollbar-none">
            <nav class="flex flex-row gap-1 rounded-xl bg-slate-100 p-1 dark:bg-white/5 min-w-[760px] md:min-w-0 md:w-full">
                <Link
                    v-for="chapter in chapters"
                    :key="chapter.id"
                    :href="chapter.href"
                    class="flex-1 flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-center text-xs font-semibold transition active:scale-[0.98]"
                    :class="[
                        chapter.active
                            ? 'bg-white text-[#821f44] shadow-sm border border-slate-200/50 dark:bg-[#1a1a1a] dark:text-white dark:border-white/5'
                            : 'text-slate-500 hover:text-slate-900 hover:bg-white/40 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-white/5'
                    ]"
                >
                    <component
                        :is="chapter.icon"
                        class="h-3 w-3 shrink-0 transition"
                        :class="[chapter.active ? 'text-[#821f44] dark:text-[#db588c]' : 'text-slate-400']"
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

const currentRegId = computed(() => {
    return new URLSearchParams(window.location.search).get('regulation_id') || null;
});

// Define the 4 Chapters with icons, labels and safe route paths
const chapters = computed(() => {
    const params = currentRegId.value ? { regulation_id: currentRegId.value } : {};
    return [
        {
            id: 'bab1',
            label: 'Bab I: Pendahuluan',
            href: route('itom.policy.guidance.introduction', params),
            active: route().current('itom.policy.guidance.introduction'),
            icon: BookOpenIcon,
        },
        {
            id: 'bab2',
            label: 'Bab II: Kebijakan',
            href: route('itom.policy.general.index', params),
            active: route().current('itom.policy.general.index') || route().current('itom.policy.general.manage') || route().current('itom.policy.specific.manage'),
            icon: ShieldCheckIcon,
        },
        {
            id: 'bab3',
            label: 'Bab III: Tanggung Jawab',
            href: route('itom.policy.roles.index', params),
            active: route().current('itom.policy.roles.index') || route().current('itom.policy.roles.manage'),
            icon: UsersIcon,
        },
        {
            id: 'bab4',
            label: 'Bab IV: Penutup',
            href: route('itom.policy.guidance.closing', params),
            active: route().current('itom.policy.guidance.closing'),
            icon: FlagIcon,
        },
    ];
});
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
