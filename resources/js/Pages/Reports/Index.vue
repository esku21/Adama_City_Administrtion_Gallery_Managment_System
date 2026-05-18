<script setup>
import { ref } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const isMenuOpen = ref(false);

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalVisitors: 0,
            totalSatisfaction: 0,
            lastSync: "N/A",
        }),
    },
});

// Helper to handle routing safely
const safeRoute = (routeName) => {
    try {
        if (routeName === "Home") return route("home");
        if (routeName === "Gallery") return route("gallery.index");
        if (routeName === "About") return route("about");
        if (routeName === "Reports") return route("reports.index");
        if (routeName === "Contacts") return route("contacts");
        return route(routeName.toLowerCase());
    } catch (e) {
        console.warn(`Route ${routeName} not found, defaulting to #`);
        return "#";
    }
};

const navLinks = ["Home", "About", "Gallery", "Reports", "Contacts"];
</script>

<template>
    <Head :title="t('nav.reports')" />

    <div class="min-h-screen bg-white text-slate-700 antialiased font-sans">
        <!-- NAVIGATION BAR -->
        <nav
            class="fixed top-0 inset-x-0 z-50 bg-[#0f172a] border-b border-slate-800 shadow-lg"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- BRANDING SECTION (Left) -->
                    <div class="flex-shrink-0 w-48">
                        <Link
                            :href="route('home')"
                            class="flex items-center gap-3 group"
                        >
                            <div
                                class="bg-white p-1.5 rounded-full shadow-md group-hover:scale-105 transition-transform duration-300"
                            >
                                <img
                                    src="/storage/images/adama.png"
                                    alt="Adama City Logo"
                                    class="h-8 w-8 object-contain"
                                />
                            </div>
                            <div
                                class="flex flex-col justify-center leading-tight"
                            >
                                <h2
                                    class="text-white font-black text-lg md:text-xl tracking-tight uppercase"
                                >
                                    ADAMA
                                </h2>
                                <p
                                    class="text-[9px] text-slate-400 uppercase tracking-[0.1em] font-bold"
                                >
                                    Official
                                </p>
                            </div>
                        </Link>
                    </div>

                    <!-- DESKTOP MENU (Centered & Increased Size) -->
                    <div class="hidden lg:flex flex-grow justify-center">
                        <div class="flex items-center gap-12">
                            <!-- Increased gap for larger text -->
                            <Link
                                v-for="link in navLinks"
                                :key="link"
                                :href="safeRoute(link)"
                                class="text-lg font-black uppercase tracking-widest transition-all duration-300"
                                :class="
                                    $page.url.startsWith(
                                        '/' + link.toLowerCase(),
                                    ) ||
                                    ($page.url === '/reports' &&
                                        link === 'Reports')
                                        ? 'text-indigo-400 border-b-2 border-indigo-400 pb-1'
                                        : 'text-slate-300 hover:text-white'
                                "
                            >
                                {{ link }}
                            </Link>
                        </div>
                    </div>

                    <!-- RIGHT SPACER -->
                    <div class="hidden lg:block w-48 text-right"></div>

                    <!-- MOBILE TOGGLE -->
                    <div class="flex lg:hidden justify-end items-center">
                        <button
                            @click="isMenuOpen = !isMenuOpen"
                            class="text-white p-2"
                        >
                            <span class="material-icons-outlined text-4xl">
                                {{ isMenuOpen ? "close" : "menu" }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MOBILE MENU -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <div
                    v-show="isMenuOpen"
                    class="lg:hidden bg-[#1e293b] border-t border-slate-700 px-6 py-8 space-y-4 shadow-2xl"
                >
                    <Link
                        v-for="link in navLinks"
                        :key="link"
                        :href="safeRoute(link)"
                        @click="isMenuOpen = false"
                        class="block text-slate-200 text-2xl font-black uppercase tracking-widest hover:text-indigo-400 transition-colors"
                    >
                        {{ link }}
                    </Link>
                </div>
            </transition>
        </nav>

        <main class="pt-28 pb-10">
            <!-- Header Section -->
            <div class="px-6 max-w-7xl mx-auto mb-8">
                <div
                    class="flex flex-col md:flex-row md:items-end justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-3xl md:text-5xl font-black text-[#1e293b] mb-2 tracking-tighter"
                        >
                            Analytics Overview
                        </h1>
                        <div
                            class="h-1.5 w-16 bg-indigo-600 rounded-full"
                        ></div>
                    </div>
                    <div class="flex flex-col md:items-end">
                        <p
                            class="text-slate-400 text-[9px] font-black uppercase tracking-[0.2em]"
                        >
                            Last Updated Data
                        </p>
                        <p class="text-slate-600 font-bold text-xs">
                            {{ stats.lastSync }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid Section -->
            <section
                class="bg-[#f8faff] py-10 px-6 border-y border-slate-100 relative"
            >
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Total Visitors Card -->
                        <div
                            class="group bg-white rounded-3xl p-6 shadow-xl shadow-indigo-900/5 border border-white flex flex-col items-center text-center transition-all hover:translate-y-[-4px]"
                        >
                            <div
                                class="bg-indigo-50 p-3 rounded-xl mb-4 text-indigo-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-7 w-7"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                            </div>
                            <span
                                class="text-4xl md:text-5xl font-black text-slate-800 mb-1 tracking-tighter"
                            >
                                {{ stats.totalVisitors }}+
                            </span>
                            <h3
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                            >
                                Total Visitors
                            </h3>
                        </div>

                        <!-- Satisfaction Feedbacks Card -->
                        <div
                            class="group bg-white rounded-3xl p-6 shadow-xl shadow-emerald-900/5 border border-white flex flex-col items-center text-center transition-all hover:translate-y-[-4px]"
                        >
                            <div
                                class="bg-emerald-50 p-3 rounded-xl mb-4 text-emerald-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-7 w-7"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <span
                                class="text-4xl md:text-5xl font-black text-slate-800 mb-1 tracking-tighter"
                            >
                                {{ stats.totalSatisfaction }}+
                            </span>
                            <h3
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                            >
                                Satisfaction Feedbacks
                            </h3>
                        </div>

                        <!-- Integrity Status Card -->
                        <div
                            class="group bg-white rounded-3xl p-6 shadow-xl shadow-slate-900/5 border border-white flex flex-col items-center text-center transition-all hover:translate-y-[-4px]"
                        >
                            <div
                                class="bg-blue-50 p-3 rounded-xl mb-4 text-blue-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-7 w-7"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                            </div>
                            <span
                                class="text-4xl md:text-5xl font-black text-slate-800 mb-1 tracking-tighter"
                            >
                                Active
                            </span>
                            <h3
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                            >
                                Data Integrity Check
                            </h3>
                        </div>
                    </div>
                </div>
            </section>

            <!-- System Logic Section -->
            <div class="mt-10 px-6 max-w-4xl mx-auto">
                <div
                    class="flex flex-col items-center text-center bg-slate-50 rounded-[2rem] p-8 border border-slate-100"
                >
                    <div
                        class="bg-indigo-600 px-4 py-1 rounded-full shadow-md mb-4 font-black text-[9px] uppercase tracking-widest text-white"
                    >
                        System Logic
                    </div>
                    <h2
                        class="text-xl md:text-2xl font-black text-slate-800 mb-3 tracking-tight"
                    >
                        Real-Time Data Integration
                    </h2>
                    <p
                        class="text-slate-500 leading-relaxed font-medium text-base"
                    >
                        Statistics reflect approved bookings from the
                        <span class="text-indigo-600 font-bold"
                            >Adama Central Hub</span
                        >. Data integrity is enforced to ensure unique counts.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.font-sans {
    font-family: "Plus Jakarta Sans", sans-serif;
}

section {
    background-image:
        radial-gradient(
            circle at 10% 10%,
            rgba(79, 70, 229, 0.03) 0%,
            transparent 40%
        ),
        radial-gradient(
            circle at 90% 90%,
            rgba(16, 185, 129, 0.03) 0%,
            transparent 40%
        );
}
</style>
