<script setup>
import { computed, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    CheckCircleIcon,
    KeyIcon,
    PlusCircleIcon,
    ShieldCheckIcon,
    TrashIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
    permissions: {
        type: Array,
        default: () => [],
    },
    protectedRoles: {
        type: Array,
        default: () => [],
    },
});

const createRoleForm = reactive({
    name: '',
    permissions: [],
});

const createPermissionForm = reactive({
    name: '',
});

const rolePermissionState = reactive({});

watch(
    () => props.roles,
    (roles) => {
        for (const role of roles) {
            rolePermissionState[role.id] = [...(role.permissions || [])];
        }
    },
    { immediate: true }
);

const stats = computed(() => ({
    totalRoles: props.roles.length,
    totalPermissions: props.permissions.length,
    protectedRoles: props.roles.filter((role) => props.protectedRoles.includes(role.name)).length,
}));

const toggleRolePermission = (roleId, permission) => {
    const selected = rolePermissionState[roleId] || [];

    if (selected.includes(permission)) {
        rolePermissionState[roleId] = selected.filter((item) => item !== permission);
        return;
    }

    rolePermissionState[roleId] = [...selected, permission];
};

const toggleNewRolePermission = (permission) => {
    if (createRoleForm.permissions.includes(permission)) {
        createRoleForm.permissions = createRoleForm.permissions.filter((item) => item !== permission);
        return;
    }

    createRoleForm.permissions = [...createRoleForm.permissions, permission];
};

const submitCreateRole = () => {
    router.post('/admin/roles', {
        name: createRoleForm.name,
        permissions: createRoleForm.permissions,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            createRoleForm.name = '';
            createRoleForm.permissions = [];
        },
    });
};

const submitCreatePermission = () => {
    router.post('/admin/roles/permissions', {
        name: createPermissionForm.name,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            createPermissionForm.name = '';
        },
    });
};

const saveRolePermissions = (role) => {
    router.put(`/admin/roles/${role.id}/permissions`, {
        permissions: rolePermissionState[role.id] || [],
    }, {
        preserveScroll: true,
    });
};

const deleteRole = (role) => {
    if (!role.can_delete) {
        return;
    }

    if (confirm(`Hapus role ${role.name}?`)) {
        router.delete(`/admin/roles/${role.id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AdminLayout title="Role Management">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-[#111827]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-500 dark:text-indigo-300">Admin Control</p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Role Management</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buat role baru dan atur permission tiap role dari panel admin.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-[#0f172a]">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Roles</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">{{ stats.totalRoles }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-[#0f172a]">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Permissions</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">{{ stats.totalPermissions }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-[#0f172a]">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Protected</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">{{ stats.protectedRoles }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827] xl:col-span-3">
                    <div class="flex items-center gap-2">
                        <ShieldCheckIcon class="h-5 w-5 text-indigo-500 dark:text-indigo-300" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Tambah Role</h2>
                    </div>

                    <form class="mt-4 space-y-4" @submit.prevent="submitCreateRole">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Nama Role</label>
                            <input
                                v-model="createRoleForm.name"
                                type="text"
                                placeholder="Contoh: FinanceLead"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-slate-200 dark:placeholder:text-slate-500"
                            />
                        </div>

                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Permission Awal</p>
                            <div v-if="permissions.length" class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <button
                                    v-for="permission in permissions"
                                    :key="`new-${permission}`"
                                    type="button"
                                    class="flex items-center justify-between rounded-lg border px-3 py-2 text-left text-sm transition-colors"
                                    :class="createRoleForm.permissions.includes(permission)
                                        ? 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-500/40 dark:bg-indigo-500/15 dark:text-indigo-300'
                                        : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-white dark:border-white/10 dark:bg-[#0f172a] dark:text-slate-300 dark:hover:bg-white/5'"
                                    @click="toggleNewRolePermission(permission)"
                                >
                                    <span class="truncate">{{ permission }}</span>
                                    <CheckCircleIcon v-if="createRoleForm.permissions.includes(permission)" class="h-4 w-4" />
                                </button>
                            </div>
                            <p v-else class="rounded-xl border border-dashed border-slate-300 px-3 py-4 text-xs text-slate-500 dark:border-white/15 dark:text-slate-400">
                                Belum ada permission. Tambahkan permission dulu di panel kanan.
                            </p>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            <PlusCircleIcon class="h-5 w-5" />
                            Simpan Role
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827] xl:col-span-2">
                    <div class="flex items-center gap-2">
                        <KeyIcon class="h-5 w-5 text-indigo-500 dark:text-indigo-300" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Tambah Permission</h2>
                    </div>

                    <form class="mt-4 space-y-3" @submit.prevent="submitCreatePermission">
                        <input
                            v-model="createPermissionForm.name"
                            type="text"
                            placeholder="Contoh: it-initiatives.approve"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-[#0f172a] dark:text-slate-200 dark:placeholder:text-slate-500"
                        />
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                        >
                            <PlusCircleIcon class="h-5 w-5" />
                            Tambah Permission
                        </button>
                    </form>

                    <div class="mt-5">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Permission Terdaftar</p>
                        <div class="max-h-64 space-y-2 overflow-y-auto pr-1">
                            <p
                                v-for="permission in permissions"
                                :key="`list-${permission}`"
                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 dark:border-white/10 dark:bg-[#0f172a] dark:text-slate-300"
                            >
                                {{ permission }}
                            </p>
                            <p v-if="!permissions.length" class="text-xs text-slate-500 dark:text-slate-400">Belum ada permission.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Akses per Role</h2>

                <article
                    v-for="role in roles"
                    :key="role.id"
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#111827]"
                >
                    <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                                <ShieldCheckIcon class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-base font-semibold text-slate-900 dark:text-white">{{ role.name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Dipakai {{ role.assigned_users }} user</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700"
                                @click="saveRolePermissions(role)"
                            >
                                <CheckCircleIcon class="h-4 w-4" />
                                Simpan Akses
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition"
                                :class="role.can_delete
                                    ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-500/15 dark:text-rose-300 dark:hover:bg-rose-500/20'
                                    : 'cursor-not-allowed bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-600'"
                                :disabled="!role.can_delete"
                                @click="deleteRole(role)"
                            >
                                <TrashIcon class="h-4 w-4" />
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div v-if="permissions.length" class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <button
                                v-for="permission in permissions"
                                :key="`${role.id}-${permission}`"
                                type="button"
                                class="flex items-center justify-between rounded-lg border px-3 py-2 text-left text-sm transition-colors"
                                :class="(rolePermissionState[role.id] || []).includes(permission)
                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/15 dark:text-emerald-300'
                                    : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-white dark:border-white/10 dark:bg-[#0f172a] dark:text-slate-300 dark:hover:bg-white/5'"
                                @click="toggleRolePermission(role.id, permission)"
                            >
                                <span class="truncate">{{ permission }}</span>
                                <CheckCircleIcon v-if="(rolePermissionState[role.id] || []).includes(permission)" class="h-4 w-4" />
                            </button>
                        </div>

                        <div v-else class="rounded-xl border border-dashed border-slate-300 px-4 py-8 text-center dark:border-white/15">
                            <UserGroupIcon class="mx-auto h-6 w-6 text-slate-400 dark:text-slate-500" />
                            <p class="mt-2 text-sm font-medium text-slate-600 dark:text-slate-300">Belum ada permission untuk diatur.</p>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </AdminLayout>
</template>
