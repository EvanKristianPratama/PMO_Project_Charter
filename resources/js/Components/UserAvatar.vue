<template>
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-sm">
        {{ initials }}
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const initials = computed(() => {
    const words = (props.user.name || '').trim().split(/\s+/);
    return words
        .slice(0, 2)
        .map(w => w.charAt(0).toUpperCase())
        .join('');
});
</script>
