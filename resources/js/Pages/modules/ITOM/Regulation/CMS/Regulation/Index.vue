<template>
    <ModulLayout title="CMS - Regulation Management">
        <div class="animate-fade-in-up space-y-6 -mx-4 sm:-mx-6 lg:-mx-8 -mt-5">
            <!-- Navigation Tabs -->
            <div
                class="flex flex-col justify-between dark:border-white/10 sm:flex-row sm:items-center px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="flex items-center gap-1.5 rounded-lg bg-slate-100 p-1 dark:bg-white/5 self-start sm:self-auto"
                >
                    <Link
                        :href="route('itom.policy.CMS.index')"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            route().current('itom.policy.CMS.index')
                                ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1A1A1A] dark:text-[#db588c]'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                    >
                        Document
                    </Link>
                    <Link
                        :href="route('itom.policy.CMS.regulation.index')"
                        class="rounded-md px-4 py-1.5 text-xs font-semibold transition-all duration-200"
                        :class="
                            route().current('itom.policy.CMS.regulation.index')
                                ? 'bg-white text-[#821f44] shadow-sm dark:bg-[#1A1A1A] dark:text-[#db588c]'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'
                        "
                    >
                        Regulation
                    </Link>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Table with Deferred Loading -->
                <Deferred data="prosesBisnisV2">
                    <template #fallback>
                        <TableSkeleton />
                    </template>

                    <RegulationDocument
                        :proses-bisnis-v2="prosesBisnisV2"
                        :company-options="companyOptions"
                        :regulations="regulations"
                    />
                </Deferred>
            </div>
        </div>
    </ModulLayout>
</template>

<script setup>
import { Link, Deferred } from '@inertiajs/vue3';
import ModulLayout from '@/Layouts/ModulLayout.vue';
import TableSkeleton from '@/Components/Shared/TableSkeleton.vue';
import RegulationDocument from '@/Components/modules/ITOM/Regulation/CMS/Regulation/RegulationDocument.vue';

defineProps({
    prosesBisnisV2: {
        type: Array,
        default: () => [],
    },
    companyOptions: {
        type: Array,
        default: () => [],
    },
    regulations: {
        type: Array,
        default: () => [],
    },
});
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>