<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useI18n } from "vue-i18n";

/**
 * Social Media Links
 */
const socialLinks = [
    {
        name: "Telegram",
        icon: "send",
        url: "https://t.me/yourusername",
        color: "text-[#26A5E4]",
        bg: "bg-[#26A5E4]/10",
    },
    {
        name: "Instagram",
        icon: "camera_alt",
        url: "https://instagram.com/yourusername",
        color: "text-[#E4405F]",
        bg: "bg-[#E4405F]/10",
    },
    {
        name: "FaceBook",
        icon: "facebook",
        url: "https://facebook.com/yourpage",
        color: "text-[#1877F2]",
        bg: "bg-[#1877F2]/10",
    },
    {
        name: "Tiktok",
        icon: "music_note",
        url: "https://tiktok.com/@yourusername",
        color: "text-white",
        bg: "bg-black/40",
    },
];

// Navigation Logic
const footerRef = ref(null);
const isSideNavOpen = ref(false);

const scrollToFooter = (e) => {
    e.preventDefault();
    isSideNavOpen.value = false;
    footerRef.value?.scrollIntoView({ behavior: "smooth" });
};

const safeRoute = (routeName) => {
    try {
        // @ts-ignore - route is provided by Ziggy/Inertia
        return route(routeName);
    } catch (e) {
        console.warn(`Route "${routeName}" is not defined.`);
        return "#";
    }
};

const messages = {
    en: {
        nav: {
            portal: "Gallery Portal",
            signIn: "Sign In",
            start: "Regisiter",
            home: "Home",
            about: "About",
            gallery: "Gallery",
            reports: "Reports",
            contacts: "Contacts",
        },
        hero: {
            welcome: "Welcome to the Adama City Administration Gallery Portal",
            bookingNote:
                "Please create an account to unlock booking features. Already have one? Just log in!",
        },
        cards: {
            join: "Create Account",
            joinDesc: "Create your account to explore!",
            login: "Login",
            loginDesc: "Access your secure dashboard.",
        },
        slides: [
            {
                title: "SITUATION ROOM",
                desc: "Monitor, analyze, and coordinate city operations through real-time surveillance and smart systems..",
            },
            {
                title: "ICT LAB",
                desc: "The dedicated workspace for employees to manage and execute assigned digital tasks.",
            },
            {
                title: "Meeting ROOM",
                desc: "A collaborative space for team presentations and reviewing project visual archives.",
            },
            {
                title: "CALL CENTER",
                desc: "The primary help desk where visitors can explore all available system services and features.",
            },
            {
                title: "DATA CENTER",
                desc: "Access the central information hub to retrieve technical documentation and system data.",
            },
            { title: "Situation Room", desc: "Strength in unity." },
        ],
    },
};

const { locale, t } = useI18n({
    legacy: false,
    locale: localStorage.getItem("lang") || "en",
    messages,
});

const isLangOpen = ref(false);
const languages = [
    { code: "en", label: "English", color: "bg-gray-500" },
    { code: "am", label: "Amharic", color: "bg-green-500" },
    { code: "om", label: "Oromo", color: "bg-red-500" },
];

const currentLangLabel = computed(
    () =>
        languages.find((l) => l.code === locale.value)?.label ||
        "Select Language",
);

const changeLanguage = (langCode) => {
    locale.value = langCode;
    localStorage.setItem("lang", langCode);
    isLangOpen.value = false;
};

// Slider Logic
const currentSlide = ref(0);
const slideImages = [
    "/storage/images/gallery6.jpg",
    "/storage/images/gallery9.jpg",
    "/storage/images/gallery11.jpg",
    "/storage/images/gallery12.jpg",
    "/storage/images/gallery13.png",
    "/storage/images/gallery6.jpg",
];

let slideInterval;
onMounted(() => {
    slideInterval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slideImages.length;
    }, 5000);
});
onUnmounted(() => clearInterval(slideInterval));
</script>

<template>
    <Head title="Adama City Administration Gallery" />

    <div class="min-h-screen bg-white flex flex-col font-sans relative">
        <!-- Mobile Sidebar -->
        <Transition name="slide">
            <aside
                v-if="isSideNavOpen"
                class="fixed inset-y-0 left-0 w-72 bg-[#0a192f] z-[150] shadow-2xl flex flex-col p-8 lg:hidden"
            >
                <div class="flex justify-between items-center mb-12">
                    <img
                        src="/storage/images/adama.png"
                        alt="Logo"
                        class="h-12 w-12 object-contain"
                    />
                    <button
                        @click="isSideNavOpen = false"
                        class="text-white hover:text-red-400"
                    >
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
                <nav class="flex flex-col gap-6">
                    <Link
                        :href="safeRoute('home')"
                        @click="isSideNavOpen = false"
                        class="nav-mobile-link text-blue-400"
                        >{{ t("nav.home") }}</Link
                    >
                    <Link
                        :href="safeRoute('about')"
                        @click="isSideNavOpen = false"
                        class="nav-mobile-link"
                        >{{ t("nav.about") }}</Link
                    >
                    <Link
                        :href="safeRoute('gallery.index')"
                        @click="isSideNavOpen = false"
                        class="nav-mobile-link"
                        >{{ t("nav.gallery") }}</Link
                    >
                    <Link
                        :href="safeRoute('reports.index')"
                        @click="isSideNavOpen = false"
                        class="nav-mobile-link"
                        >{{ t("nav.reports") }}</Link
                    >
                    <a
                        href="#footer"
                        @click="scrollToFooter"
                        class="nav-mobile-link"
                        >{{ t("nav.contacts") }}</a
                    >
                </nav>
            </aside>
        </Transition>

        <div
            v-if="isSideNavOpen"
            @click="isSideNavOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[140] lg:hidden"
        ></div>

        <!-- Desktop Navigation -->
        <nav
            class="bg-[#0a192f] py-4 sticky top-0 z-[100] shadow-2xl border-b border-white/5"
        >
            <div class="w-full px-4 md:px-8 flex justify-between items-center">
                <div class="flex items-center">
                    <button
                        @click="isSideNavOpen = true"
                        class="lg:hidden text-white mr-4 focus:outline-none"
                    >
                        <span class="material-icons-outlined text-3xl"
                            >menu</span
                        >
                    </button>
                    <Link
                        :href="safeRoute('home')"
                        class="flex items-center gap-3 group"
                    >
                        <div
                            class="bg-white/10 p-1.5 rounded-xl group-hover:bg-white/20 transition-all"
                        >
                            <img
                                src="/storage/images/adama.png"
                                alt="Logo"
                                class="h-10 w-10 sm:h-11 sm:w-11 object-contain"
                            />
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2
                                class="font-black text-white text-sm sm:text-lg leading-tight tracking-tight uppercase"
                            >
                                ADAMA CITY ADMINISTRATION
                            </h2>
                            <span
                                class="text-[9px] text-gray-400 uppercase tracking-[0.2em] font-bold leading-none"
                                >{{ t("nav.portal") }}</span
                            >
                        </div>
                    </Link>
                </div>

                <div class="hidden lg:flex items-center gap-8">
                    <Link
                        :href="safeRoute('home')"
                        class="nav-desktop-link"
                        :class="{ active: $page.url === '/' }"
                        >{{ t("nav.home") }}</Link
                    >
                    <Link
                        :href="safeRoute('about')"
                        class="nav-desktop-link"
                        :class="{ active: $page.url.startsWith('/about') }"
                        >{{ t("nav.about") }}</Link
                    >
                    <Link
                        :href="safeRoute('gallery.index')"
                        class="nav-desktop-link"
                        :class="{ active: $page.url.startsWith('/gallery') }"
                        >{{ t("nav.gallery") }}</Link
                    >
                    <Link
                        :href="safeRoute('reports.index')"
                        class="nav-desktop-link"
                        :class="{ active: $page.url.startsWith('/reports') }"
                        >{{ t("nav.reports") }}</Link
                    >
                    <a
                        href="#footer"
                        @click="scrollToFooter"
                        class="nav-desktop-link"
                        >{{ t("nav.contacts") }}</a
                    >
                </div>

                <div class="flex items-center gap-3 md:gap-6">
                    <div class="relative">
                        <button
                            @click="isLangOpen = !isLangOpen"
                            class="flex items-center gap-2 bg-white/5 border border-white/10 px-3 py-2 rounded-xl text-white text-xs font-bold hover:bg-white/15 transition-all"
                        >
                            <span class="material-icons-outlined text-sm"
                                >translate</span
                            >
                            <span class="hidden sm:inline">{{
                                currentLangLabel
                            }}</span>
                        </button>
                        <div
                            v-if="isLangOpen"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl z-[110] p-2 border border-slate-100"
                        >
                            <button
                                v-for="lang in languages"
                                :key="lang.code"
                                @click="changeLanguage(lang.code)"
                                class="w-full text-left px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-blue-50 rounded-lg flex items-center gap-2"
                            >
                                <span
                                    :class="[
                                        'w-2.5 h-2.5 rounded-full',
                                        lang.color,
                                    ]"
                                ></span>
                                {{ lang.label }}
                            </button>
                        </div>
                    </div>
                    <Link
                        :href="safeRoute('login')"
                        class="hidden sm:block text-slate-300 hover:text-white text-xs font-black uppercase tracking-wider transition-colors"
                        >{{ t("nav.signIn") }}</Link
                    >
                    <Link
                        :href="safeRoute('register')"
                        class="bg-blue-600 text-white px-5 py-3 rounded-xl text-xs font-black hover:bg-blue-500 shadow-lg transition-all hover:scale-105 uppercase tracking-wider"
                        >{{ t("nav.start") }}</Link
                    >
                </div>
            </div>
        </nav>

        <main class="flex-grow py-6 overflow-hidden">
            <div class="container mx-auto px-6 lg:px-12">
                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    <!-- Slider Section (Likes/Dislikes Removed) -->
                    <div
                        class="lg:col-span-8 relative h-[400px] md:h-[600px] w-full overflow-hidden rounded-[30px] md:rounded-[40px] shadow-2xl bg-slate-900 group"
                    >
                        <TransitionGroup name="fade">
                            <div
                                v-for="(img, index) in slideImages"
                                :key="index"
                                v-show="currentSlide === index"
                                class="absolute inset-0"
                            >
                                <img
                                    :src="img"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[6000ms]"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"
                                ></div>
                                <div
                                    class="absolute bottom-6 left-6 md:bottom-10 md:left-10 text-white max-w-lg"
                                >
                                    <h3
                                        class="text-2xl md:text-5xl font-black uppercase mb-2 leading-tight"
                                    >
                                        {{ t(`slides[${index}].title`) }}
                                    </h3>
                                    <p
                                        class="text-white/80 text-sm md:text-lg font-medium"
                                    >
                                        {{ t(`slides[${index}].desc`) }}
                                    </p>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <!-- Hero Content -->
                    <div class="lg:col-span-4 space-y-6 md:space-y-8">
                        <div>
                            <p
                                class="text-4xl md:text-3xl font-black text-[#0a192f] tracking-tighter leading-[1.1] mb-4"
                            >
                                {{ t("hero.welcome") }}
                            </p>
                            <p
                                class="text-slate-600 text-sm font-medium leading-relaxed"
                            >
                                {{ t("hero.bookingNote") }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <Link
                                :href="safeRoute('register')"
                                class="flex items-center justify-between bg-[#0a192f] text-white p-5 md:p-6 rounded-[1.5rem] hover:-translate-y-1 shadow-xl transition-all group"
                            >
                                <div>
                                    <span
                                        class="block text-lg md:text-xl font-black uppercase tracking-tight"
                                        >{{ t("cards.join") }}</span
                                    >
                                    <span
                                        class="text-[10px] md:text-xs text-slate-400"
                                        >{{ t("cards.joinDesc") }}</span
                                    >
                                </div>
                                <div
                                    class="p-3 bg-blue-600 rounded-xl group-hover:scale-110 transition-transform"
                                >
                                    <span
                                        class="material-icons-outlined text-white text-sm"
                                        >person_add</span
                                    >
                                </div>
                            </Link>
                            <Link
                                :href="safeRoute('login')"
                                class="flex items-center justify-between bg-white border border-slate-200 text-[#0a192f] p-5 md:p-6 rounded-[1.5rem] hover:-translate-y-1 shadow-lg transition-all group"
                            >
                                <div>
                                    <span
                                        class="block text-lg md:text-xl font-black uppercase tracking-tight"
                                        >{{ t("cards.login") }}</span
                                    >
                                    <span
                                        class="text-[10px] md:text-xs text-slate-500"
                                        >{{ t("cards.loginDesc") }}</span
                                    >
                                </div>
                                <div
                                    class="p-3 bg-slate-100 group-hover:bg-blue-600 group-hover:text-white text-slate-400 rounded-xl transition-all"
                                >
                                    <span
                                        class="material-icons-outlined text-sm"
                                        >login</span
                                    >
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer
            ref="footerRef"
            id="footer"
            class="bg-[#0a192f] text-white pt-16 pb-8 border-t border-white/10 mt-auto"
        >
            <div
                class="container mx-auto px-6 lg:px-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12"
            >
                <div class="space-y-6">
                    <h3
                        class="text-lg font-black uppercase tracking-tight leading-tight"
                    >
                        Adama City<br /><span class="text-blue-400 text-sm"
                            >Administration</span
                        >
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-xs">
                        You can contact us at any time these social media
                        platforms:
                    </p>
                    <div class="flex gap-3 pt-2">
                        <a
                            v-for="social in socialLinks"
                            :key="social.name"
                            :href="social.url"
                            target="_blank"
                            class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg"
                            :class="[social.bg, social.color]"
                        >
                            <span class="material-icons-outlined text-xl">{{
                                social.icon
                            }}</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h4
                        class="text-blue-400 font-black uppercase tracking-widest text-xs mb-6"
                    >
                        Navigation
                    </h4>
                    <ul class="space-y-3">
                        <li
                            v-for="link in [
                                'Home',
                                'Gallery',
                                'About',
                                'Reports',
                            ]"
                            :key="link"
                        >
                            <Link
                                :href="
                                    safeRoute(
                                        link.toLowerCase() === 'home'
                                            ? 'home'
                                            : link.toLowerCase() === 'gallery'
                                              ? 'gallery.index'
                                              : link.toLowerCase() === 'reports'
                                                ? 'reports.index'
                                                : 'about',
                                    )
                                "
                                class="text-slate-300 hover:text-white text-sm font-bold uppercase transition-colors flex items-center gap-2 group"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                ></span>
                                {{ link }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4
                        class="text-blue-400 font-black uppercase tracking-widest text-xs mb-6"
                    >
                        Contacts
                    </h4>
                    <ul class="space-y-4 text-slate-300 text-sm font-medium">
                        <li class="flex items-start gap-3">
                            <span class="material-icons-outlined text-blue-500"
                                >place</span
                            >
                            <span>Adama, Oromiya, Ethiopia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-icons-outlined text-blue-500"
                                >email</span
                            >
                            <a
                                href="mailto:asto@adamacity.gov.et"
                                class="hover:text-white transition-colors"
                            >
                                asto@adamacity.gov.et
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-icons-outlined text-blue-500"
                                >phone</span
                            >
                            <a
                                href="tel:+251221112061"
                                class="hover:text-white transition-colors"
                            >
                                +251 221 112 061
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4
                        class="text-blue-400 font-black uppercase tracking-widest text-xs mb-6"
                    >
                        Updates
                    </h4>
                    <p class="text-slate-300 text-sm mb-4">
                        Subscribe to our Youtub channel:
                    </p>
                    <div class="flex flex-col gap-3">
                        <input
                            type="email"
                            placeholder="Enter your email"
                            class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all"
                        />
                        <button
                            class="bg-blue-600 hover:bg-blue-500 text-white font-black py-3 rounded-xl uppercase text-xs tracking-widest transition-all shadow-lg"
                        >
                            Subscribe
                        </button>
                    </div>
                </div>
            </div>
            <div
                class="container mx-auto px-6 lg:px-12 mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4"
            >
                <p
                    class="text-slate-500 text-[10px] uppercase font-bold tracking-[0.2em]"
                >
                    &copy; {{ new Date().getFullYear() }} Adama City
                    Administration.
                </p>
                <p class="text-slate-600 text-[9px] uppercase">
                    Built with Excellence for Oromiya
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.nav-desktop-link {
    @apply text-[18px] font-bold text-slate-300 uppercase tracking-widest hover:text-blue-400 transition-all relative py-2;
}
.nav-desktop-link.active {
    @apply text-blue-400;
}
.nav-desktop-link.active::after {
    content: "";
    @apply absolute bottom-0 left-0 w-full h-0.5 bg-blue-400 rounded-full;
}
.nav-mobile-link {
    @apply text-lg font-black text-slate-300 uppercase tracking-widest py-4 border-b border-white/5 block transition-colors hover:text-white;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}
</style>
