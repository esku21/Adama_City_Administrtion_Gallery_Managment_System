<script setup>
import { ref, computed, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import ToastNotification from "@/Components/ToastNotification.vue";

const { t, locale } = useI18n();
const page = usePage();
const isSidebarOpen = ref(true);

// --- AUTH & SYSTEM DATA ---
const guide = computed(() => page.props.auth?.user);
const assignedHall = computed(
    () => guide.value?.hall?.name || t("guide_nav.no_hall"),
);

// --- TOAST NOTIFICATION ---
const flash = computed(() => page.props.flash);
const showToast = ref(false);

watch(
    () => page.props.flash,
    (newVal) => {
        if (newVal?.message) {
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 3500);
        }
    },
    { deep: true, immediate: true },
);

// --- LOCALE SWITCHING ---
const toggleLocale = () => {
    const locales = ["en", "am", "om"];
    const currentIndex = locales.indexOf(locale.value);
    locale.value = locales[(currentIndex + 1) % locales.length];
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

// --- NAVIGATION CONFIG ---
const navItems = [
    {
        labelKey: "guide_nav.bookings",
        route: "guide.dashboard",
        icon: "dashboard",
    },
    {
        labelKey: "guide_nav.scanner",
        route: "guide.scanner",
        icon: "qr_code_scanner",
    },
    {
        labelKey: "guide_nav.feedbacks",
        route: "guide.feedbacks.index",
        icon: "reviews",
    },
];

const accountItems = [
    {
        labelKey: "guide_nav.profile",
        route: "guide.profile.edit",
        icon: "account_circle",
    },
    {
        labelKey: "guide_nav.security",
        route: "guide.settings.index",
        icon: "settings_suggest",
    },
];
</script>

<template>
    <div
        class="min-h-screen flex bg-[#F9FAFB] font-sans antialiased text-slate-900 overflow-x-hidden"
    >
        <ToastNotification
            v-if="showToast && flash.message"
            :message="flash.message"
            :type="flash.type || 'success'"
        />

        <aside
            :class="[
                'fixed inset-y-0 z-50 bg-[#0F172A] text-slate-300 flex flex-col transition-all duration-300 shadow-2xl border-r border-white/5',
                isSidebarOpen ? 'w-72' : 'w-24',
            ]"
        >
            <div
                class="h-20 flex items-center px-6 border-b border-white/5 mb-4 shrink-0"
            >
                <div class="flex items-center gap-4 overflow-hidden">
                    <div
                        class="h-10 w-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0"
                    >
                        <span class="text-white font-black text-xl leading-none"
                            >A</span
                        >
                    </div>
                    <div
                        v-if="isSidebarOpen"
                        class="flex flex-col whitespace-nowrap"
                    >
                        <h1
                            class="text-white font-bold tracking-tight text-lg leading-tight uppercase"
                        >
                            Adama <span class="text-emerald-400">GMS</span>
                        </h1>
                        <p
                            class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1"
                        >
                            {{ t("guide_nav.management") }}
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar">
                <p
                    v-if="isSidebarOpen"
                    class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mb-4 mt-6"
                >
                    {{ t("nav.overview") }}
                </p>

                <Link
                    v-for="item in navItems"
                    :key="item.labelKey"
                    :href="route(item.route)"
                    class="nav-link group"
                    :class="{ 'nav-active': route().current(item.route) }"
                >
                    <span
                        class="material-icons-outlined transition-transform duration-300 group-hover:scale-110"
                        >{{ item.icon }}</span
                    >
                    <span
                        v-if="isSidebarOpen"
                        class="ml-4 font-semibold tracking-wide truncate"
                        >{{ t(item.labelKey) }}</span
                    >
                </Link>

                <div class="pt-6 pb-2">
                    <div class="border-t border-white/5 mx-4"></div>
                </div>

                <p
                    v-if="isSidebarOpen"
                    class="px-4 text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mb-4"
                >
                    {{ t("nav.account") }}
                </p>

                <Link
                    v-for="item in accountItems"
                    :key="item.labelKey"
                    :href="route(item.route)"
                    class="nav-link group"
                    :class="{ 'nav-active': route().current(item.route) }"
                >
                    <span
                        class="material-icons-outlined transition-transform duration-300 group-hover:scale-110"
                        >{{ item.icon }}</span
                    >
                    <span
                        v-if="isSidebarOpen"
                        class="ml-4 font-semibold tracking-wide truncate"
                        >{{ t(item.labelKey) }}</span
                    >
                </Link>
            </nav>

            <div class="p-4 mt-auto shrink-0">
                <div
                    v-if="isSidebarOpen"
                    class="mb-4 p-4 rounded-2xl bg-white/5 border border-white/5"
                >
                    <p
                        class="text-[9px] text-slate-500 uppercase font-black tracking-widest mb-1"
                    >
                        {{ t("nav.station") }}
                    </p>
                    <p
                        class="text-xs text-emerald-400 font-bold truncate flex items-center gap-1"
                    >
                        <span class="material-icons-outlined text-sm"
                            >place</span
                        >
                        {{ assignedHall }}
                    </p>
                </div>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="logout-btn"
                >
                    <span class="material-icons-outlined">logout</span>
                    <span
                        v-if="isSidebarOpen"
                        class="ml-4 font-bold tracking-widest uppercase"
                        >{{ t("nav.logout") }}</span
                    >
                </Link>
            </div>
        </aside>

        <div
            class="flex-1 transition-all duration-300 min-w-0"
            :class="isSidebarOpen ? 'ml-72' : 'ml-24'"
        >
            <header
                class="bg-white/80 backdrop-blur-xl border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-40"
            >
                <button
                    @click="toggleSidebar"
                    class="h-10 w-10 flex items-center justify-center hover:bg-slate-100 rounded-xl text-slate-500 cursor-pointer"
                >
                    <span class="material-icons-outlined">{{
                        isSidebarOpen ? "menu_open" : "menu"
                    }}</span>
                </button>

                <div class="flex items-center gap-4">
                    <button
                        @click="toggleLocale"
                        class="text-[10px] font-bold px-3 py-1 bg-emerald-50 border border-emerald-100 rounded-full text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all uppercase cursor-pointer"
                    >
                        {{ locale }}
                    </button>

                    <div class="text-right hidden sm:block">
                        <p
                            class="text-sm font-bold text-slate-900 leading-none truncate max-w-[150px]"
                        >
                            {{ guide?.firstName }} {{ guide?.lastName }}
                        </p>
                        <p
                            class="text-[10px] text-emerald-600 font-black uppercase tracking-widest mt-1.5 flex items-center justify-end gap-1"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"
                            ></span>
                            Online
                        </p>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-slate-100 border-2 border-white shadow-sm flex items-center justify-center text-emerald-600 font-black text-lg"
                    >
                        {{ guide?.firstName?.charAt(0) }}
                    </div>
                </div>
            </header>

            <main class="p-8 max-w-[1400px] mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.nav-link {
    @apply flex items-center px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-white/5 hover:text-white transition-all duration-300 text-[13px] cursor-pointer;
}
.nav-active {
    @apply bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 border-l-4 border-emerald-300;
}
.logout-btn {
    @apply w-full flex items-center px-4 py-3.5 text-rose-400 hover:bg-rose-500/10 hover:text-rose-500 rounded-2xl transition-all text-[11px] font-black cursor-pointer;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-white/10 rounded-full;
}
</style>
