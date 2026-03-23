<script setup>
import { ref, computed } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";

const page = usePage();
const user = computed(() => page.props.auth?.user);

// Sidebar links specifically for the Guide/Staff Portal
const navItems = [
    { name: "Dashboard", route: "guide.dashboard", icon: "dashboard" },
    { name: "Assignments", route: "guide.dashboard", icon: "assignment" }, // Guides check assigned tasks here
    {
        name: "Verify Bookings",
        route: "guide.scanner",
        icon: "qr_code_scanner",
    },
    { name: "Feedback", route: "guide.dashboard", icon: "maps_ugc" }, // Guides view visitor feedback
];

// Account & System section for Guides
const secondaryItems = [
    { name: "My Profile", route: "profile.edit", icon: "account_circle" },
    { name: "Preferences", route: "profile.edit", icon: "tune" },
];

const handleLogout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div
        class="min-h-screen bg-zinc-50 flex font-sans antialiased text-zinc-900"
    >
        <aside
            class="w-72 bg-zinc-900 border-r border-zinc-800 flex flex-col fixed inset-y-0 z-50"
        >
            <div class="p-8 mb-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20"
                >
                    <span class="material-icons-outlined text-white text-2xl"
                        >badge</span
                    >
                </div>
                <div>
                    <h1
                        class="text-white text-lg font-black italic tracking-tighter uppercase"
                    >
                        Guide Portal
                    </h1>
                    <p
                        class="text-[8px] text-emerald-400 font-bold uppercase tracking-[0.2em]"
                    >
                        Live on Duty
                    </p>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <p
                    class="px-4 py-3 text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em]"
                >
                    Operational
                </p>

                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="
                        route().current(item.route + '*')
                            ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20'
                            : 'text-zinc-500 hover:bg-zinc-800 hover:text-white'
                    "
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all font-bold text-[11px] uppercase tracking-widest group"
                >
                    <span class="material-icons-outlined text-xl">{{
                        item.icon
                    }}</span>
                    {{ item.name }}
                </Link>

                <div class="pt-8 pb-3 px-4">
                    <p
                        class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em]"
                    >
                        Account Management
                    </p>
                </div>

                <Link
                    v-for="item in secondaryItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="
                        route().current(item.route + '*')
                            ? 'bg-zinc-800 text-white'
                            : 'text-zinc-500 hover:bg-zinc-800 hover:text-white'
                    "
                    class="flex items-center gap-4 px-5 py-4 rounded-2xl transition-all font-bold text-[11px] uppercase tracking-widest group"
                >
                    <span class="material-icons-outlined text-xl">{{
                        item.icon
                    }}</span>
                    {{ item.name }}
                </Link>
            </nav>

            <div class="p-6 mt-auto border-t border-zinc-800">
                <div
                    class="bg-zinc-800/50 p-4 rounded-2xl mb-4 border border-zinc-800 flex items-center gap-3"
                >
                    <div
                        class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center"
                    >
                        <span
                            class="material-icons-outlined text-emerald-500 text-sm"
                            >person</span
                        >
                    </div>
                    <div class="truncate">
                        <p class="text-[10px] font-black text-white truncate">
                            {{ user?.firstName }} {{ user?.lastName }}
                        </p>
                        <p
                            class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest"
                        >
                            Guide Access
                        </p>
                    </div>
                </div>

                <button
                    @click="handleLogout"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-zinc-800 hover:bg-rose-600/10 hover:text-rose-500 text-zinc-400 rounded-xl transition-all font-bold text-[10px] uppercase tracking-widest"
                >
                    <span class="material-icons-outlined text-sm">logout</span>
                    Sign Out
                </button>
            </div>
        </aside>

        <div class="flex-1 ml-72">
            <header
                class="h-20 bg-white border-b border-zinc-200 sticky top-0 z-40 px-12 flex items-center justify-between"
            >
                <div
                    class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400"
                >
                    <span>Portal</span>
                    <span class="text-zinc-200">/</span>
                    <span class="text-emerald-600">{{
                        route().current().split(".").pop().replace("_", " ")
                    }}</span>
                </div>

                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 hover:opacity-80 transition-opacity"
                >
                    <span class="text-[9px] font-black uppercase text-zinc-400"
                        >Settings</span
                    >
                    <div
                        class="w-10 h-10 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600"
                    >
                        <span class="material-icons-outlined">settings</span>
                    </div>
                </Link>
            </header>

            <main class="p-12 max-w-6xl mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
@import url("https://fonts.googleapis.com/icon?family=Material+Icons+Outlined");

nav::-webkit-scrollbar {
    width: 0px;
}
</style>
