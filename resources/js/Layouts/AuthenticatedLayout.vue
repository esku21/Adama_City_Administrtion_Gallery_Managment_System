<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const page = usePage();

const user = computed(() => page.props.auth?.user || null);
const userRole = computed(() => user.value?.role || "visitor");

const isMobileMenuOpen = ref(false);
const isSidebarCollapsed = ref(false);
const showToast = ref(false);
const toastMessage = ref("");

// Flash Message Watcher
watch(
    () => page.props.flash?.message,
    (msg) => {
        if (msg) {
            toastMessage.value = msg;
            showToast.value = true;
            setTimeout(() => (showToast.value = false), 4000);
        }
    },
    { immediate: true },
);

// Localization Toggle
const toggleLocale = () => {
    const locales = ["en", "am", "or"];
    let currentIndex = locales.indexOf(locale.value);
    if (currentIndex === -1) currentIndex = 0;
    const nextIndex = (currentIndex + 1) % locales.length;
    const nextLocale = locales[nextIndex];
    locale.value = nextLocale;
    localStorage.setItem("lang", nextLocale);
};

onMounted(() => {
    const savedLang = localStorage.getItem("lang");
    if (savedLang) locale.value = savedLang;
});

// Dynamic Navigation Items
const navItems = computed(() => {
    if (userRole.value === "admin") {
        return [
            {
                name: "dashboard",
                route: "admin.dashboard",
                icon: "grid_view",
                prefix: "admin",
            },
            {
                name: "bookings",
                route: "admin.bookings.index",
                icon: "confirmation_number",
                prefix: "admin",
            },
            {
                name: "halls",
                route: "admin.halls.index",
                icon: "domain",
                prefix: "admin",
            },
            {
                name: "announcements",
                route: "admin.announcements.index",
                icon: "campaign",
                prefix: "admin",
            },
            {
                name: "reports",
                route: "admin.reports",
                icon: "bar_chart",
                prefix: "admin",
            },
            {
                name: "guides",
                route: "admin.guides.index",
                icon: "hail",
                prefix: "admin",
            },
            {
                name: "feedback",
                route: "admin.feedbacks.index",
                icon: "forum",
                prefix: "admin",
            },
            {
                name: "gallery",
                route: "admin.gallery.index",
                icon: "collections",
                prefix: "admin",
            },
        ];
    }
    const prefix = userRole.value === "guide" ? "guide" : "nav1";
    return [
        { name: "dashboard", route: "dashboard", icon: "grid_view", prefix },
        {
            name: "new_booking",
            route: "bookings.create",
            icon: "add_task",
            prefix,
        },
        { name: "history", route: "bookings.index", icon: "history", prefix },
        {
            name: "notifications",
            route: "notifications.index",
            icon: "notifications_active",
            prefix,
        },
        {
            name: "gallery",
            route: "gallery.index",
            icon: "collections",
            prefix,
        },
    ];
});

const secondaryItems = computed(() => {
    const prefix = userRole.value === "admin" ? "admin" : "nav1";
    const items = [
        {
            name: "profile",
            route:
                userRole.value === "admin"
                    ? "admin.profile.edit"
                    : "profile.edit",
            icon: "account_circle",
            prefix,
        },
        {
            name: "security",
            route:
                userRole.value === "admin"
                    ? "admin.settings"
                    : "settings.index",
            icon: "verified_user",
            prefix,
        },
    ];
    if (userRole.value === "admin") {
        items.push({
            name: "system_health",
            route: "admin.system.index",
            icon: "tune",
            prefix: "admin",
        });
    }
    return items;
});

const handleLogout = () => router.post(route("logout"));
const closeMobileMenu = () => (isMobileMenuOpen.value = false);
</script>

<template>
    <div
        class="min-h-screen flex bg-slate-50 font-sans text-slate-900 overflow-x-hidden"
    >
        <link
            href="https://fonts.googleapis.com/icon?family=Material+Icons+Round"
            rel="stylesheet"
        />

        <Transition name="fade">
            <div
                v-if="isMobileMenuOpen"
                @click="closeMobileMenu"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] lg:hidden"
            ></div>
        </Transition>

        <aside
            :class="[
                'fixed inset-y-0 left-0 bg-white z-[70] transition-all duration-300 flex flex-col shadow-2xl border-r-2 border-slate-200',
                isMobileMenuOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0',
                isSidebarCollapsed ? 'lg:w-24' : 'lg:w-72',
                'w-72',
            ]"
        >
            <div
                class="h-24 flex items-center px-6 border-b-2 border-slate-100 shrink-0 bg-slate-50/50"
            >
                <div class="flex items-center gap-4 overflow-hidden">
                    <div
                        class="w-12 h-12 rounded-2xl overflow-hidden flex items-center justify-center shrink-0 shadow-md border-2 border-white bg-white"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Logo"
                            class="w-full h-full object-contain"
                        />
                    </div>
                    <div
                        v-if="!isSidebarCollapsed || isMobileMenuOpen"
                        class="transition-opacity duration-300"
                    >
                        <p
                            class="text-[10px] text-indigo-600 uppercase font-bold tracking-widest leading-tight"
                        >
                            {{
                                userRole === "admin"
                                    ? "Management"
                                    : "User Portal"
                            }}
                        </p>
                        <p
                            class="text-lg font-bold text-slate-900 truncate tracking-tight"
                        >
                            City Gallery
                        </p>
                    </div>
                </div>
            </div>

            <nav
                class="flex-1 px-4 py-8 space-y-1 overflow-y-auto custom-scrollbar"
            >
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="route(item.route)"
                    @click="closeMobileMenu"
                    class="nav-link group"
                    :class="
                        route().current(item.route)
                            ? 'nav-active'
                            : 'nav-inactive'
                    "
                >
                    <span
                        class="material-icons-round text-2xl transition-all duration-300 group-hover:scale-110"
                    >
                        {{ item.icon }}
                    </span>
                    <span
                        v-if="!isSidebarCollapsed || isMobileMenuOpen"
                        class="text-sm font-bold uppercase tracking-wider truncate"
                    >
                        {{ t(`${item.prefix}.${item.name}`) }}
                    </span>
                </Link>

                <div class="my-6 border-t-2 border-slate-100"></div>

                <Link
                    v-for="item in secondaryItems"
                    :key="item.name"
                    :href="route(item.route)"
                    @click="closeMobileMenu"
                    class="nav-link group"
                    :class="
                        route().current(item.route)
                            ? 'nav-active'
                            : 'nav-inactive'
                    "
                >
                    <span
                        class="material-icons-round text-2xl transition-all duration-300 group-hover:scale-110"
                    >
                        {{ item.icon }}
                    </span>
                    <span
                        v-if="!isSidebarCollapsed || isMobileMenuOpen"
                        class="text-sm font-bold uppercase tracking-wider truncate"
                    >
                        {{ t(`${item.prefix}.${item.name}`) }}
                    </span>
                </Link>

                <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-5 px-5 py-4 mt-6 text-rose-600 hover:bg-rose-50 rounded-2xl transition-all group"
                >
                    <span
                        class="material-icons-round text-2xl group-hover:rotate-12 transition-transform"
                        >logout</span
                    >
                    <span
                        v-if="!isSidebarCollapsed || isMobileMenuOpen"
                        class="text-sm font-bold uppercase tracking-wider"
                    >
                        {{ t("nav1.logout") }}
                    </span>
                </button>
            </nav>
        </aside>

        <div
            :class="[
                'flex-1 flex flex-col transition-all duration-300 min-w-0 relative',
                isSidebarCollapsed ? 'lg:pl-24' : 'lg:pl-72',
            ]"
        >
            <header
                class="h-24 bg-white/90 backdrop-blur-xl sticky top-0 z-40 flex justify-between items-center px-4 lg:px-10 border-b-2 border-slate-200/60 shadow-sm"
            >
                <div class="flex items-center gap-4">
                    <button
                        @click="isMobileMenuOpen = true"
                        class="lg:hidden p-2 hover:bg-slate-100 rounded-xl"
                    >
                        <span
                            class="material-icons-round text-3xl text-slate-700"
                            >menu</span
                        >
                    </button>
                    <button
                        @click="isSidebarCollapsed = !isSidebarCollapsed"
                        class="hidden lg:flex items-center justify-center w-12 h-12 rounded-xl bg-slate-50 hover:bg-indigo-600 text-slate-500 hover:text-white border-2 border-slate-200 transition-all active:scale-90"
                    >
                        <span class="material-icons-round text-2xl">
                            {{
                                isSidebarCollapsed
                                    ? "menu_open"
                                    : "format_indent_decrease"
                            }}
                        </span>
                    </button>
                </div>

                <div class="flex items-center gap-3 lg:gap-6">
                    <button
                        @click="toggleLocale"
                        class="text-xs font-bold px-3 py-2 bg-white rounded-xl border-2 border-slate-200 hover:border-indigo-500 uppercase tracking-widest shadow-sm"
                    >
                        {{ locale }}
                    </button>

                    <div
                        class="flex items-center gap-3 pl-3 lg:pl-6 border-l-2 border-slate-200"
                    >
                        <div class="text-right hidden sm:block">
                            <p
                                class="text-sm font-bold text-slate-900 leading-none mb-1"
                            >
                                {{ user?.name }}
                            </p>
                            <span
                                class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold uppercase"
                                >{{ userRole }}</span
                            >
                        </div>
                        <div
                            class="w-10 h-10 lg:w-12 lg:h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-lg border-2 border-white"
                        >
                            {{ user?.name?.charAt(0).toUpperCase() }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
                <div class="max-w-[1600px] mx-auto">
                    <slot />
                </div>
            </main>
        </div>

        <Transition name="slide-up">
            <div
                v-if="showToast"
                class="fixed bottom-6 right-6 lg:bottom-10 lg:right-10 z-[100] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4"
            >
                <span class="material-icons-round text-green-400"
                    >check_circle</span
                >
                <p class="text-sm font-bold">{{ toastMessage }}</p>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.nav-link {
    @apply flex items-center gap-5 px-5 py-4 rounded-2xl transition-all duration-300 border-l-4;
}

.nav-active {
    @apply bg-indigo-600 text-white shadow-xl shadow-indigo-200 border-indigo-800 translate-x-1;
}

.nav-inactive {
    @apply text-slate-500 hover:bg-slate-100 hover:text-slate-900 border-transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 rounded-full;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-up-enter-active {
    transition: all 0.3s ease-out;
}
.slide-up-leave-active {
    transition: all 0.2s ease-in;
}
.slide-up-enter-from {
    transform: translateY(20px);
    opacity: 0;
}
</style>
