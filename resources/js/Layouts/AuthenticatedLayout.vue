<script setup>
import { ref, computed, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const systemStatus = computed(
    () => page.props.settings?.system_status || "active",
);

const showToast = ref(false);
const toastMessage = ref("");

watch(
    () => page.props.flash?.message,
    (newMessage) => {
        if (newMessage) {
            toastMessage.value = newMessage;
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 4000);
        }
    },
    { immediate: true },
);

// Main Menu items
const navItems = [
    { name: "Dashboard", route: "admin.dashboard", icon: "dashboard" },
    {
        name: "Bookings",
        route: "admin.bookings.index",
        icon: "confirmation_number",
    },
    { name: "Reports", route: "admin.reports", icon: "insights" },
    { name: "Guides", route: "admin.guides.index", icon: "hail" },
    { name: "Feedback", route: "admin.feedbacks.index", icon: "forum" },
];

// Administration items
const secondaryItems = [
    { name: "Profile", route: "profile.edit", icon: "account_circle" },
    { name: "Settings", route: "admin.settings", icon: "settings" },
    { name: "System Status", route: "admin.system.index", icon: "tune" },
];

const handleLogout = () => router.post(route("logout"));

const statusConfig = computed(() => {
    const configs = {
        inactive: {
            label: "Deactivated",
            color: "text-rose-500",
            bg: "bg-rose-500",
            bgLight: "bg-rose-50",
            border: "border-rose-100",
        },
        maintenance: {
            label: "Maintenance",
            color: "text-amber-500",
            bg: "bg-amber-500",
            bgLight: "bg-amber-50",
            border: "border-amber-100",
        },
        active: {
            label: "Live System",
            color: "text-emerald-500",
            bg: "bg-emerald-500",
            bgLight: "bg-emerald-50",
            border: "border-emerald-100",
        },
    };
    return configs[systemStatus.value] || configs.active;
});
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 flex font-sans antialiased text-slate-900"
    >
        <aside
            class="w-72 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 z-50 shadow-sm"
        >
            <div class="p-8 flex items-center gap-3 shrink-0">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200"
                >
                    <span class="material-icons-outlined text-white"
                        >token</span
                    >
                </div>
                <h1
                    class="text-xl font-black italic tracking-tighter text-slate-800 uppercase"
                >
                    ACAGMS
                </h1>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
                <p
                    class="px-4 py-2 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]"
                >
                    Main Menu
                </p>
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="
                        route().current(item.route + '*')
                            ? 'bg-indigo-600 text-white shadow-md border-indigo-600'
                            : 'text-slate-500 hover:bg-slate-50 border-transparent'
                    "
                    class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all border font-bold text-[11px] uppercase tracking-widest mb-1"
                >
                    <span class="material-icons-outlined text-lg">{{
                        item.icon
                    }}</span>
                    {{ item.name }}
                </Link>

                <div class="pt-8 pb-2 px-4 text-nowrap">
                    <p
                        class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]"
                    >
                        Administration
                    </p>
                </div>
                <Link
                    v-for="item in secondaryItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="
                        route().current(item.route + '*')
                            ? 'bg-indigo-600 text-white shadow-md border-indigo-600'
                            : 'text-slate-500 hover:bg-slate-50 border-transparent'
                    "
                    class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all border font-bold text-[11px] uppercase tracking-widest mb-1"
                >
                    <span class="material-icons-outlined text-lg">{{
                        item.icon
                    }}</span>
                    {{ item.name }}
                </Link>
                <div class="h-4"></div>
            </nav>

            <div class="p-6 border-t border-slate-100 bg-slate-50/50 shrink-0">
                <div
                    :class="[statusConfig.bgLight, statusConfig.border]"
                    class="p-4 rounded-2xl mb-4 border shadow-sm transition-all duration-500"
                >
                    <div class="flex items-center justify-between mb-1">
                        <p
                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                        >
                            Protocol Status
                        </p>
                        <span
                            :class="statusConfig.bg"
                            class="w-2 h-2 rounded-full animate-pulse"
                        ></span>
                    </div>
                    <p
                        :class="statusConfig.color"
                        class="text-xs font-black uppercase italic tracking-tighter"
                    >
                        {{ statusConfig.label }}
                    </p>
                </div>
                <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all font-bold text-[10px] uppercase tracking-widest"
                >
                    <span class="material-icons-outlined"
                        >power_settings_new</span
                    >
                    Sign Out
                </button>
            </div>
        </aside>

        <div class="flex-1 ml-72 flex flex-col min-w-0">
            <header
                class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 px-8 flex items-center justify-between"
            >
                <div
                    class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400"
                >
                    System <span class="text-slate-300">/</span>
                    <span class="text-indigo-600">
                        {{
                            route()
                                .current()
                                ?.split(".")
                                .pop()
                                ?.replace("_", " ")
                        }}
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-end mr-2">
                        <span
                            class="text-[10px] font-black text-slate-800 leading-none mb-1"
                            >Environment</span
                        >
                        <span
                            :class="statusConfig.color"
                            class="text-[8px] font-bold uppercase tracking-tighter flex items-center gap-1"
                        >
                            <span
                                :class="statusConfig.bg"
                                class="w-1 h-1 rounded-full animate-pulse"
                            ></span>
                            {{ statusConfig.label }} Mode
                        </span>
                    </div>
                    <Link
                        :href="route('profile.edit')"
                        class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                    >
                        <span class="material-icons-outlined"
                            >manage_accounts</span
                        >
                    </Link>
                </div>
            </header>

            <main class="p-8 w-full flex-grow"><slot /></main>
        </div>

        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showToast"
                class="fixed bottom-10 right-10 z-[100] max-w-sm w-full bg-white border border-slate-200 shadow-2xl rounded-3xl overflow-hidden"
            >
                <div class="p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center"
                    >
                        <span class="material-icons-outlined">verified</span>
                    </div>
                    <div>
                        <p
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                        >
                            System Update
                        </p>
                        <p class="text-xs font-bold text-slate-700">
                            {{ toastMessage }}
                        </p>
                    </div>
                </div>
                <div class="h-1 bg-emerald-500 animate-shrink"></div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* SCROLLBAR STYLING */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

@keyframes shrink {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
.animate-shrink {
    animation: shrink 4000ms linear forwards;
}
</style>
