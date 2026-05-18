<script setup>
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const isMenuOpen = ref(false); // Mobile menu state

/**
 * Navigation Logic & Route Safety
 */
const safeRoute = (name) => {
    try {
        // Standardize the name to match typical Laravel route naming
        const routeName = name === "Home" ? "home" : name.toLowerCase();
        // @ts-ignore
        return route(routeName);
    } catch {
        const fallbacks = {
            home: "/",
            gallery: "/gallery",
            about: "/about",
            reports: "/reports",
            contacts: "#footer",
        };
        return fallbacks[name.toLowerCase()] || "/";
    }
};

const navLinks = [
    { name: "Home", route: "home" },
    { name: "About", route: "about" },
    { name: "Gallery", route: "gallery.index" },
    { name: "Reports", route: "reports" },
    { name: "Contacts", route: "contacts" },
];

const pageTitle = t("aboutPage.title");
</script>

<template>
    <Head
        :title="
            pageTitle && pageTitle !== 'aboutPage.title'
                ? pageTitle
                : 'About Adama City'
        "
    />

    <!-- FULL PAGE WRAPPER -->
    <div
        class="min-h-screen font-sans relative flex flex-col overflow-x-hidden selection:bg-blue-500/30"
    >
        <!-- FIXED BACKGROUND IMAGE -->
        <div
            class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat scale-100"
            style="
                background-image: url(&quot;/storage/images/gallery7.jpg&quot;);
            "
        ></div>

        <!-- LIGHTENED OVERLAY -->
        <div
            class="fixed inset-0 z-0 bg-gradient-to-b from-black/40 via-black/10 to-black/50"
        ></div>

        <!-- ================= NAVBAR ================= -->
        <nav
            class="fixed top-0 w-full z-[100] bg-black/20 backdrop-blur-md border-b border-white/10 shadow-2xl"
        >
            <div
                class="max-w-7xl mx-auto px-4 md:px-10 py-4 flex justify-between items-center"
            >
                <!-- Logo -->
                <Link
                    :href="safeRoute('home')"
                    class="flex items-center gap-3 group"
                >
                    <div
                        class="bg-white p-1 rounded-lg shadow-lg transition-transform group-hover:scale-105"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Logo"
                            class="h-8 w-8 object-contain"
                        />
                    </div>
                    <div class="flex flex-col">
                        <h2
                            class="text-white font-black text-sm md:text-lg leading-none tracking-tight uppercase"
                        >
                            ADAMA CITY
                        </h2>
                        <span
                            class="text-[9px] text-blue-400 uppercase tracking-widest font-bold"
                            >Administration</span
                        >
                    </div>
                </Link>

                <!-- Desktop Navigation (INCREASED TO text-lg) -->
                <div class="hidden lg:flex items-center gap-6">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="safeRoute(link.name)"
                        class="text-lg font-bold uppercase tracking-widest transition-all relative py-1"
                        :class="
                            route().current(link.route)
                                ? 'text-blue-400'
                                : 'text-gray-200 hover:text-white'
                        "
                    >
                        {{ link.name }}
                        <span
                            v-if="route().current(link.route)"
                            class="absolute -bottom-1 left-0 w-full h-0.5 bg-blue-500 rounded-full"
                        ></span>
                    </Link>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-6">
                        <Link
                            :href="safeRoute('login')"
                            class="text-gray-300 hover:text-white text-lg font-black uppercase tracking-widest"
                            >Sign In</Link
                        >
                        <Link
                            :href="safeRoute('register')"
                            class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all shadow-lg active:scale-95"
                        >
                            Register
                        </Link>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="isMenuOpen = !isMenuOpen"
                        class="lg:hidden text-white p-2 flex items-center justify-center"
                    >
                        <svg
                            v-if="!isMenuOpen"
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
                                d="M4 6h16M4 12h16m-7 6h7"
                            />
                        </svg>
                        <svg
                            v-else
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- MOBILE MENU OVERLAY -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-show="isMenuOpen"
                    class="fixed inset-0 z-[110] bg-blue-600 flex flex-col justify-center items-center gap-8 lg:hidden"
                >
                    <button
                        @click="isMenuOpen = false"
                        class="absolute top-8 right-8 text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="safeRoute(link.name)"
                        @click="isMenuOpen = false"
                        class="text-white text-3xl font-black uppercase tracking-[0.2em]"
                        :class="{
                            'opacity-100': route().current(link.route),
                            'opacity-60': !route().current(link.route),
                        }"
                    >
                        {{ link.name }}
                    </Link>
                </div>
            </transition>
        </nav>

        <!-- ================= CONTENT AREA ================= -->
        <main class="relative z-10 flex-grow pt-32 pb-20">
            <!-- HERO SECTION -->
            <section
                class="flex items-center justify-center text-center text-white py-8 px-6"
            >
                <div
                    class="max-w-3xl backdrop-blur-xl bg-black/20 border border-white/10 p-8 md:p-14 rounded-[2.5rem] shadow-2xl"
                >
                    <h1
                        class="text-3xl md:text-5xl font-black tracking-tighter leading-tight uppercase mb-4 text-white"
                    >
                        {{
                            pageTitle && pageTitle !== "aboutPage.title"
                                ? pageTitle
                                : "About Adama City"
                        }}
                    </h1>
                    <div
                        class="h-1 w-20 bg-blue-600 mx-auto mb-6 rounded-full"
                    ></div>
                    <p
                        class="text-gray-100 text-base md:text-lg font-medium leading-relaxed max-w-xl mx-auto"
                    >
                        A rapidly growing urban center in Ethiopia, driven by
                        innovation, smart governance, and rich cultural heritage
                        preservation.
                    </p>
                </div>
            </section>

            <!-- CORE PRINCIPLES -->
            <section class="container mx-auto px-6 lg:px-12 mt-8">
                <div
                    class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto"
                >
                    <div
                        v-for="card in [
                            {
                                title: 'Vision',
                                icon: '👁️',
                                color: 'blue',
                                text: 'Building a smart, sustainable, and modern city.',
                            },
                            {
                                title: 'Mission',
                                icon: '🎯',
                                color: 'red',
                                text: 'To deliver efficient, transparent digital services.',
                            },
                            {
                                title: 'Values',
                                icon: '⭐',
                                color: 'amber',
                                text: 'Built on transparency, and public trust.',
                            },
                        ]"
                        :key="card.title"
                        class="group p-8 rounded-[2rem] bg-black/40 backdrop-blur-lg border border-white/10 hover:border-blue-500/50 transition-all duration-300 shadow-xl"
                    >
                        <!-- Dynamic color binding fix -->
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-6 shadow-lg"
                            :class="{
                                'bg-blue-600': card.color === 'blue',
                                'bg-red-600': card.color === 'red',
                                'bg-amber-600': card.color === 'amber',
                            }"
                        >
                            {{ card.icon }}
                        </div>
                        <h3
                            class="font-black text-white text-xl mb-2 uppercase tracking-tight"
                        >
                            {{ card.title }}
                        </h3>
                        <p
                            class="text-gray-300 text-sm font-medium leading-relaxed"
                        >
                            {{ card.text }}
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- ================= FOOTER ================= -->
        <footer
            class="relative z-10 bg-black/40 backdrop-blur-md py-8 border-t border-white/5 mt-auto"
        >
            <div
                class="container mx-auto px-10 flex flex-col md:flex-row justify-between items-center gap-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-widest text-gray-400"
                >
                    Adama City Administration © 2026
                </p>
                <div class="flex gap-6">
                    <a
                        href="#"
                        class="text-gray-400 hover:text-white text-[9px] font-bold uppercase tracking-widest"
                        >Privacy Policy</a
                    >
                    <a
                        href="#"
                        class="text-gray-400 hover:text-white text-[9px] font-bold uppercase tracking-widest"
                        >Terms of Service</a
                    >
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
div {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

::-webkit-scrollbar {
    width: 4px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: rgba(37, 99, 235, 0.5);
    border-radius: 20px;
}
</style>
