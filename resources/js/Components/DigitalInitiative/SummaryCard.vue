<template>
    <div class="flex h-full flex-col gap-3">
        <article
            class="relative flex min-h-[220px] flex-1 cursor-pointer flex-col justify-between rounded-2xl border border-[#1C75BC] bg-[#1C75BC] p-5 shadow-[0_4px_16px_rgba(28,117,188,0.3)] transition hover:shadow-[0_6px_24px_rgba(28,117,188,0.4)]"
            @click="emit('show-all')"
        >
            <div>
                <p
                    class="text-xs font-semibold uppercase tracking-[0.08em] text-white"
                    style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                >
                    Total Digital Inisiatif
                </p>
                <p
                    class="mt-2 text-3xl font-bold text-white"
                    style="text-shadow: 0 2px 6px rgba(0,0,0,0.35);"
                >
                    {{ total }}
                </p>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 bg-white/15 text-lg font-bold text-white transition hover:bg-white/25"
                    title="New Initiative"
                    @click.stop="emit('create')"
                >
                    +
                </button>
                <button
                    type="button"
                    class="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/25"
                    @click="emit('show-all')"
                >
                    Digital Initiative
                </button>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/compendium"
                    class="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/25"
                    @click.stop
                >
                    Compendium List
                </Link>
                <Link
                    href="/program-planning/program-definition/digital-initiatives/appendix"
                    class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/90 transition hover:bg-white/20"
                    @click.stop
                >
                    Appendix List
                </Link>
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
