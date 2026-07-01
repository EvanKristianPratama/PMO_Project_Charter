<template>
    <div class="flex h-full flex-col gap-3">
        <article
            class="relative flex min-h-[140px] flex-1 cursor-pointer flex-col justify-center rounded-2xl border border-[#1C75BC] bg-[#1C75BC] p-4 shadow-[0_4px_16px_rgba(28,117,188,0.3)] transition hover:shadow-[0_6px_24px_rgba(28,117,188,0.4)]"
            @click="emit('show-all')"
        >
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-[0.08em] text-white"
                    style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                >
                    Digital Initiatives
                </p>
                <p
                    class="mt-2 text-3xl font-bold text-white"
                    style="text-shadow: 0 2px 6px rgba(0,0,0,0.35);"
                >
                    {{ total }}
                </p>
            </div>
        </article>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    total: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['create', 'show-all']);
</script>
