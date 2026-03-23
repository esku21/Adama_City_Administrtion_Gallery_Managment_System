<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
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
} from "lucide-vue-next";

const page = usePage();
const user = computed(() => page.props.auth.user);
const isMobileMenuOpen = ref(false);

// Helper to check if route exists before calling it to prevent Ziggy crashes
const routeExists = (name) => {
    return typeof route !== "undefined" && route().check(name);
};
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] flex">
        <div
            v-if="isMobileMenuOpen"
            @click="isMobileMenuOpen = false"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 md:hidden"
        ></div>

        <aside
            class="w-64 bg-white border-r border-slate-100 fixed h-full flex flex-col z-50 transition-transform duration-300 md:translate-x-0"
            :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="p-8">
                <h2 class="text-2xl font-black text-indigo-600 tracking-tight">
                    ACAGMS
                </h2>
                <p
                    class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
                >
                    Visitor Portal
                </p>
            </div>

            <nav class="px-4 flex-1 space-y-2 overflow-y-auto">
                <Link
                    :href="route('visitor.dashboard')"
                    :class="
                        route().current('visitor.dashboard')
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-slate-500 hover:bg-slate-50'
                    "
                    class="flex items-center gap-3 px-4 py-3 font-semibold rounded-xl transition-all"
                >
                    <LayoutDashboard :size="20" />
                    <span>Dashboard</span>
                </Link>

                <Link
                    :href="route('visitor.booking.create')"
                    :class="
                        route().current('visitor.booking.create')
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-slate-500 hover:bg-slate-50'
                    "
                    class="flex items-center gap-3 px-4 py-3 font-semibold rounded-xl transition-all"
                >
                    <PlusCircle :size="20" />
                    <span>New Booking</span>
                </Link>

                <Link
                    :href="route('visitor.history')"
                    :class="
                        route().current('visitor.history')
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-slate-500 hover:bg-slate-50'
                    "
                    class="flex items-center gap-3 px-4 py-3 font-semibold rounded-xl transition-all"
                >
                    <History :size="20" />
                    <span>My History</span>
                </Link>

                <div class="my-4 px-4 border-t border-slate-100"></div>

                <Link
                    :href="route('visitor.feedback.create')"
                    :class="
                        route().current('visitor.feedback.*')
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-slate-500 hover:bg-slate-50'
                    "
                    class="flex items-center gap-3 px-4 py-3 font-semibold rounded-xl transition-all"
                >
                    <MessageSquare :size="20" />
                    <span>Feedback</span>
                </Link>

                <Link
                    :href="route('profile.edit')"
                    :class="
                        route().current('profile.*')
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-slate-500 hover:bg-slate-50'
                    "
                    class="flex items-center gap-3 px-4 py-3 font-semibold rounded-xl transition-all"
                >
                    <User :size="20" />
                    <span>Profile</span>
                </Link>

                <Link
                    :href="route('visitor.settings.index')"
                    :class="
                        route().current('visitor.settings.index')
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-slate-500 hover:bg-slate-50'
                    "
                    class="flex items-center gap-3 px-4 py-3 font-semibold rounded-xl transition-all"
                >
                    <Settings :size="20" />
                    <span>Settings</span>
                </Link>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 font-bold rounded-xl transition-colors"
                >
                    <LogOut :size="20" />
                    <span>Logout</span>
                </Link>
            </div>
        </aside>

        <main class="flex-1 md:ml-64 min-h-screen flex flex-col">
            <header
                class="bg-white/80 backdrop-blur-md sticky top-0 z-10 p-4 md:p-6 md:px-10 flex justify-between items-center border-b border-slate-100"
            >
                <div class="flex items-center gap-4">
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg"
                    >
                        <Menu v-if="!isMobileMenuOpen" :size="24" />
                        <X v-else :size="24" />
                    </button>
                    <h1 class="text-xl md:text-2xl font-black text-slate-800">
                        <slot name="header">Dashboard</slot>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p
                            class="text-sm font-bold text-slate-700 leading-none mb-1"
                        >
                            {{ user?.firstName || "Visitor" }}
                        </p>
                        <p
                            class="text-[10px] text-slate-400 uppercase font-black tracking-wider"
                        >
                            Verified Guest
                        </p>
                    </div>
                    <div
                        class="h-10 w-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold"
                    >
                        {{ user?.firstName?.charAt(0) || "V" }}
                    </div>
                </div>
            </header>

            <div class="p-6 md:p-10">
                <slot />
            </div>
        </main>
    </div>
</template>
