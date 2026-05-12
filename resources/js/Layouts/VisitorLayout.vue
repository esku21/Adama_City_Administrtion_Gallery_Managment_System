<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import {
    LayoutDashboard,
    PlusCircle,
    History,
    MessageSquare,
    User,
    Settings,
    LogOut,
    Menu,
    X,
    Bell,
    ChevronLeft,
    ChevronRight,
} from "lucide-vue-next";

const { t } = useI18n();
const page = usePage();

// AUTH
const user = computed(() => page.props.auth?.user || null);
const unreadCount = computed(() => page.props.auth?.notifications_count || 0);

// STATE
const isMobileMenuOpen = ref(false);
const isSidebarCollapsed = ref(false);

// CLOSE MOBILE ON ROUTE CHANGE
watch(
    () => page.url,
    () => {
        isMobileMenuOpen.value = false;
    },
);

const logoUrl = "/storage/images/adama.png";

const mainNav = [
    {
        name: "nav1.dashboard",
        icon: LayoutDashboard,
        route: "visitor.dashboard",
    },
    {
        name: "nav1.new_booking",
        icon: PlusCircle,
        route: "visitor.booking.create",
    },
    {
        name: "nav1.history",
        icon: History,
        route: "visitor.history",
    },
    {
        name: "nav1.notifications",
        icon: Bell,
        route: "visitor.notifications.index",
        badge: true,
    },
];

const accountNav = [
    {
        name: "nav1.feedback",
        icon: MessageSquare,
        route: "visitor.feedback.create",
    },
    {
        name: "nav1.profile",
        icon: User,
        route: "visitor.profile.edit",
    },
    {
        name: "nav1.settings",
        icon: Settings,
        route: "visitor.settings.index",
    },
];

const isActive = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + ".*");
    } catch {
        return false;
    }
};
</script>

<template>
    <div class="min-h-screen flex bg-slate-100">
        <div
            v-if="isMobileMenuOpen"
            @click="isMobileMenuOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
        ></div>

        <aside
            :class="[
                'fixed top-0 left-0 h-full z-50 flex flex-col transition-all duration-300 ease-in-out',
                'bg-[#0f172a] text-white shadow-2xl border-r border-white/5',
                isSidebarCollapsed ? 'w-20' : 'w-72',
                isMobileMenuOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0',
            ]"
        >
            <div
                class="h-24 flex items-center justify-between px-5 border-b border-white/10"
            >
                <Link
                    :href="route('visitor.dashboard')"
                    class="flex items-center gap-4"
                >
                    <div class="p-1 bg-white/10 rounded-xl shadow-inner">
                        <img
                            :src="logoUrl"
                            class="h-10 w-10 object-contain rounded-lg"
                        />
                    </div>

                    <div
                        v-if="!isSidebarCollapsed"
                        class="transition-opacity duration-300"
                    >
                        <h2
                            class="text-xl font-black tracking-tight leading-none"
                        >
                            ACAGMS
                        </h2>
                        <p
                            class="text-[11px] font-bold text-indigo-400 uppercase tracking-widest mt-1"
                        >
                            Visitor Portal
                        </p>
                    </div>
                </Link>

                <button
                    @click="isSidebarCollapsed = !isSidebarCollapsed"
                    class="hidden lg:flex p-2 hover:bg-white/10 rounded-full transition-colors"
                >
                    <ChevronLeft v-if="!isSidebarCollapsed" :size="20" />
                    <ChevronRight v-else :size="20" />
                </button>
            </div>

            <nav class="flex-1 px-4 py-8 space-y-8 overflow-y-auto">
                <div class="space-y-2">
                    <p
                        v-if="!isSidebarCollapsed"
                        class="text-sm font-bold text-white/30 px-3 uppercase tracking-widest mb-4"
                    >
                        {{ t("nav1.main") }}
                    </p>

                    <Link
                        v-for="item in mainNav"
                        :key="item.name"
                        :href="route(item.route)"
                        :class="[
                            'flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-200 group',
                            isActive(item.route)
                                ? 'bg-indigo-600 shadow-lg shadow-indigo-600/30'
                                : 'hover:bg-white/5',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :size="24"
                            :class="
                                isActive(item.route)
                                    ? 'text-white'
                                    : 'text-white/60 group-hover:text-white'
                            "
                        />

                        <span
                            v-if="!isSidebarCollapsed"
                            class="text-md font-semibold tracking-wide"
                        >
                            {{ t(item.name) }}
                        </span>

                        <span
                            v-if="item.badge && unreadCount > 0"
                            class="ml-auto bg-red-500 text-[10px] font-black px-2 py-0.5 rounded-full ring-2 ring-red-500/20"
                        >
                            {{ unreadCount }}
                        </span>
                    </Link>
                </div>

                <div class="space-y-2">
                    <p
                        v-if="!isSidebarCollapsed"
                        class="text-sm font-bold text-white/30 px-3 uppercase tracking-widest mb-4"
                    >
                        {{ t("nav1.preferences") }}
                    </p>

                    <Link
                        v-for="item in accountNav"
                        :key="item.name"
                        :href="route(item.route)"
                        :class="[
                            'flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-200 group',
                            isActive(item.route)
                                ? 'bg-indigo-600 shadow-lg shadow-indigo-600/30'
                                : 'hover:bg-white/5',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :size="24"
                            :class="
                                isActive(item.route)
                                    ? 'text-white'
                                    : 'text-white/60 group-hover:text-white'
                            "
                        />

                        <span
                            v-if="!isSidebarCollapsed"
                            class="text-md font-semibold tracking-wide"
                        >
                            {{ t(item.name) }}
                        </span>
                    </Link>
                </div>
            </nav>

            <div class="p-4 bg-white/5 border-t border-white/10">
                <div
                    v-if="user && !isSidebarCollapsed"
                    class="px-4 mb-6 flex flex-col"
                >
                    <span class="text-md font-bold truncate text-white">{{
                        user.firstName
                    }}</span>
                    <span class="text-xs text-indigo-400 font-medium">{{
                        user.role || "Visitor"
                    }}</span>
                </div>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center gap-4 px-4 py-3.5 text-red-400 hover:bg-red-400/10 rounded-xl transition-colors font-semibold"
                >
                    <LogOut :size="24" />
                    <span v-if="!isSidebarCollapsed" class="text-md">
                        {{ t("nav1.logout") }}
                    </span>
                </Link>
            </div>
        </aside>

        <main
            :class="[
                'flex-1 flex flex-col transition-all duration-300',
                isSidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72',
            ]"
        >
            <header
                class="h-16 bg-white flex justify-between items-center px-8 border-b border-slate-200 shadow-sm"
            >
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="lg:hidden p-2 text-slate-600"
                >
                    <Menu v-if="!isMobileMenuOpen" :size="24" />
                    <X v-else :size="24" />
                </button>

                <h1 class="text-lg font-bold text-slate-800">
                    {{ t("nav1.overview") }}
                </h1>

                <div
                    v-if="user"
                    class="flex items-center gap-2 font-semibold text-slate-700"
                >
                    <div
                        class="h-8 w-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs"
                    >
                        {{ user.firstName?.charAt(0) }}
                    </div>
                    {{ user.firstName }}
                </div>
            </header>

            <div class="p-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
nav::-webkit-scrollbar {
    width: 5px;
}
nav::-webkit-scrollbar-track {
    background: transparent;
}
nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
