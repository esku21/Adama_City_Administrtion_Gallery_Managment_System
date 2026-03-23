<script setup>
import { computed } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const dashboardRoute = computed(() => {
    if (!user.value) return "login";
    if (user.value.role === "admin") return "admin.dashboard";
    if (user.value.role === "guide") return "guide.dashboard";
    return "visitor.dashboard";
});

// FIXED: Changed from .edit to .index to match web.php
const settingsRoute = computed(() => {
    if (user.value?.role === "admin") return "admin.settings.index";
    return "profile.edit";
});

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <Link
        :href="route(settingsRoute)"
        :class="
            route().current(settingsRoute)
                ? 'bg-indigo-600/10 text-indigo-400 border border-indigo-500/20'
                : 'text-gray-400 hover:bg-white/5'
        "
        class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-200 group"
    >
        <span class="material-icons-outlined text-xl">settings</span>
        <span class="text-xs font-bold uppercase tracking-widest"
            >Settings</span
        >
    </Link>
</template>
