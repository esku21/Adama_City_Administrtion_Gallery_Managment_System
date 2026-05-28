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

/**
 * Navigation Logic
 */
const footerRef = ref(null);
const isSideNavOpen = ref(false);

const scrollToFooter = (e) => {
    e.preventDefault();
    isSideNavOpen.value = false;
    footerRef.value?.scrollIntoView({ behavior: "smooth" });
};

const safeRoute = (routeName) => {
    try {
        return route(routeName);
    } catch (e) {
        console.warn(`Route "${routeName}" is not defined.`);
        return "#";
    }
};

/**
 * i18n Messages
 */
const messages = {
    en: {
        nav: {
            portal: "Gallery Portal",
            signIn: "Sign In",
            start: "Register",
            home: "Home",
            about: "About",
            gallery: "Gallery",
            reports: "Reports",
            contacts: "Contacts",
            navigation: "Navigation",
            updates: "Updates",
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

        footer: {
            admin: "Administration",
            contactText:
                "You can contact us at any time using these social media platforms:",
            contacts: "Contacts",
            updates: "Updates",
            subscribeText: "Subscribe to our YouTube channel:",
            emailPlaceholder: "Enter your email",
            subscribe: "Subscribe",
            built: "Built with Excellence for Oromiya",
            copyright: "Adama City Administration.",
            location: "Adama, Oromiya, Ethiopia",
        },

        slides: [
            {
                title: "SITUATION ROOM",
                desc: "Monitor, analyze, and coordinate city operations through real-time surveillance and smart systems.",
            },
            {
                title: "ICT LAB",
                desc: "The dedicated workspace for employees to manage and execute assigned digital tasks.",
            },
            {
                title: "MEETING ROOM",
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
            {
                title: "SITUATION ROOM",
                desc: "Strength in unity.",
            },
        ],
    },

    am: {
        nav: {
            portal: "የጋለሪ ፖርታል",
            signIn: "ግባ",
            start: "ተመዝገብ",
            home: "መነሻ",
            about: "ስለ እኛ",
            gallery: "ጋለሪ",
            reports: "ሪፖርቶች",
            contacts: "እውቂያ",
            navigation: "አሰሳ",
            updates: "ዝማኔዎች",
        },

        hero: {
            welcome: "ወደ አዳማ ከተማ አስተዳደር ጋለሪ ፖርታል እንኳን በደህና መጡ",
            bookingNote: "የቦታ ማስያዣ አገልግሎት ለመጠቀም እባክዎ መለያ ይፍጠሩ። መለያ ካለዎት ይግቡ!",
        },

        cards: {
            join: "መለያ ፍጠር",
            joinDesc: "ለማስሰስ መለያዎን ይፍጠሩ!",
            login: "ግባ",
            loginDesc: "ወደ ደህንነቱ የተጠበቀ ዳሽቦርድዎ ይግቡ።",
        },

        footer: {
            admin: "አስተዳደር",
            contactText: "በማንኛውም ጊዜ በእነዚህ የማህበራዊ ሚዲያ መድረኮች ማግኘት ይችላሉ።",
            contacts: "እውቂያዎች",
            updates: "ዝማኔዎች",
            subscribeText: "የYouTube ቻናላችንን ይከተሉ:",
            emailPlaceholder: "ኢሜይልዎን ያስገቡ",
            subscribe: "ተመዝገብ",
            built: "ለኦሮሚያ በጥራት የተገነባ",
            copyright: "አዳማ ከተማ አስተዳደር።",
            location: "አዳማ፣ ኦሮሚያ፣ ኢትዮጵያ",
        },

        slides: [
            {
                title: "ሲቱኤሽን ሩም",
                desc: "የከተማውን እንቅስቃሴ በቅጽበት ስርዓቶች ይቆጣጠሩ፣ ይተንትኑ እና ያስተባብሩ።",
            },
            {
                title: "ICT ላብ",
                desc: "ሰራተኞች የዲጂታል ስራዎችን የሚያከናውኑበት ቦታ።",
            },
            {
                title: "የስብሰባ ክፍል",
                desc: "ለቡድን ውይይት እና ማቅረቢያ የሚያገለግል ቦታ።",
            },
            {
                title: "ጥሪ ማዕከል",
                desc: "ጎብኚዎች ሁሉንም አገልግሎቶች የሚያገኙበት ማዕከል።",
            },
            {
                title: "ዳታ ማዕከል",
                desc: "ቴክኒካል ሰነዶችን እና መረጃዎችን የሚያገኙበት ማዕከል።",
            },
            {
                title: "ሲቱኤሽን ሩም",
                desc: "ኃይል በአንድነት።",
            },
        ],
    },

    om: {
        nav: {
            portal: "Poortaala Galerii",
            signIn: "Seeni",
            start: "Galmaa'i",
            home: "Mana",
            about: "Waa'ee",
            gallery: "Galerii",
            reports: "Gabaasa",
            contacts: "Qunnamtii",
            navigation: "Daandii",
            updates: "Haaromsa",
        },

        hero: {
            welcome:
                "Baga Nagaan Gara Poortaala Galerii Bulchiinsa Magaalaa Adaamaa Dhufte",
            bookingNote:
                "Tajaajila qabannoo fayyadamuuf akkaawuntii uumuu qabdu. Yoo akkaawuntii qabdu seeni!",
        },

        cards: {
            join: "Akkaawuntii Uumi",
            joinDesc: "Akkaawuntii kee uumii keessa daawwadhu!",
            login: "Seeni",
            loginDesc: "Daashboordii nageenya qabu keessa seeni.",
        },

        footer: {
            admin: "Bulchiinsa",
            contactText:
                "Yeroo kamiyyuu karaa miidiyaa hawaasaa kanaan nu qunnamaa:",
            contacts: "Qunnamtii",
            updates: "Haaromsa",
            subscribeText: "Chaanaalii YouTube keenya subscribe godhaa:",
            emailPlaceholder: "Imeelii kee galchi",
            subscribe: "Subscribe Godhi",
            built: "Oromiyaaf qulqullinaan ijaarame",
            copyright: "Bulchiinsa Magaalaa Adaamaa.",
            location: "Adaamaa, Oromiyaa, Itoophiyaa",
        },

        slides: [
            {
                title: "KUTAA TO'ANNOO",
                desc: "Hojii magaalaa yeroo dhugaa keessatti hordofi fi qindeessi.",
            },
            {
                title: "ICT LAB",
                desc: "Bakki hojii dijitaalaa hojjettoonni itti hojjetan.",
            },
            {
                title: "KUTAA WALGA'II",
                desc: "Bakki marii fi hojii garee.",
            },
            {
                title: "CALL CENTER",
                desc: "Bakki odeeffannoo fi tajaajilli kennamu.",
            },
            {
                title: "DATA CENTER",
                desc: "Bakki odeeffannoo fi sanadoonni itti argaman.",
            },
            {
                title: "KUTAA TO'ANNOO",
                desc: "Ciminni jechuun tokkummaa dha.",
            },
        ],
    },
};

const { locale, t } = useI18n({
    legacy: false,
    locale: localStorage.getItem("lang") || "en",
    fallbackLocale: "en",
    messages,
});

/**
 * Language Dropdown
 */
const isLangOpen = ref(false);

const languages = [
    { code: "en", label: "English" },
    { code: "am", label: "አማርኛ" },
    { code: "om", label: "Afaan Oromo" },
];

const currentLangLabel = computed(() => {
    return (
        languages.find((l) => l.code === locale.value)?.label ||
        "Select Language"
    );
});

const changeLanguage = (langCode) => {
    locale.value = langCode;
    localStorage.setItem("lang", langCode);
    isLangOpen.value = false;
};

/**
 * Slider Logic
 */
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
        <nav class="bg-[#0a192f] py-4 sticky top-0 z-[100] shadow-2xl">
            <div class="w-full px-4 md:px-8 flex justify-between items-center">
                <Link :href="safeRoute('home')" class="flex items-center gap-3">
                    <img
                        src="/storage/images/adama.png"
                        alt="Logo"
                        class="h-12 w-12 object-contain"
                    />

                    <div>
                        <h2
                            class="font-black text-white text-sm sm:text-lg uppercase"
                        >
                            ADAMA CITY ADMINISTRATION
                        </h2>

                        <span class="text-[10px] text-gray-400 uppercase">
                            {{ t("nav.portal") }}
                        </span>
                    </div>
                </Link>

                <div class="hidden lg:flex items-center gap-8">
                    <Link :href="safeRoute('home')" class="nav-desktop-link">
                        {{ t("nav.home") }}
                    </Link>

                    <Link :href="safeRoute('about')" class="nav-desktop-link">
                        {{ t("nav.about") }}
                    </Link>

                    <Link
                        :href="safeRoute('gallery.index')"
                        class="nav-desktop-link"
                    >
                        {{ t("nav.gallery") }}
                    </Link>

                    <Link
                        :href="safeRoute('reports.index')"
                        class="nav-desktop-link"
                    >
                        {{ t("nav.reports") }}
                    </Link>

                    <a
                        href="#footer"
                        @click="scrollToFooter"
                        class="nav-desktop-link"
                    >
                        {{ t("nav.contacts") }}
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative">
                        <button
                            @click="isLangOpen = !isLangOpen"
                            class="flex items-center gap-2 bg-white/10 px-5 py-2.5 rounded-xl text-white text-base md:text-lg tracking-wide font-medium shadow-md transition-all duration-200 hover:bg-white/20"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="w-5 h-5 shrink-0"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.003 9.003 0 018.716 6.747M12 3a9.003 9.003 0 00-8.716 6.747M12 9h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <span>{{ currentLangLabel }}</span>
                        </button>

                        <div
                            v-if="isLangOpen"
                            class="absolute right-0 mt-2 bg-white rounded-xl shadow-2xl p-2 w-48 z-[110] border border-gray-100 animate-fade-in"
                        >
                            <button
                                v-for="lang in languages"
                                :key="lang.code"
                                @click="changeLanguage(lang.code)"
                                class="w-full text-left px-4 py-3 hover:bg-gray-100 rounded-lg text-base font-semibold text-slate-800 transition-colors"
                            >
                                {{ lang.label }}
                            </button>
                        </div>
                    </div>

                    <Link
                        :href="safeRoute('login')"
                        class="text-white font-bold text-sm sm:text-base hover:text-blue-400 transition-colors"
                    >
                        {{ t("nav.signIn") }}
                    </Link>

                    <Link
                        :href="safeRoute('register')"
                        class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold text-sm sm:text-base hover:bg-blue-500 transition-colors"
                    >
                        {{ t("nav.start") }}
                    </Link>
                </div>
            </div>
        </nav>

        <main class="flex-grow py-8">
            <div class="container mx-auto px-6 lg:px-12">
                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    <div
                        class="lg:col-span-8 relative h-[500px] overflow-hidden rounded-[30px]"
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
                                    class="w-full h-full object-cover"
                                    alt="Slider Image"
                                />

                                <div class="absolute inset-0 bg-black/50"></div>

                                <div
                                    class="absolute bottom-10 left-10 text-white right-10"
                                >
                                    <h3 class="text-4xl font-black uppercase">
                                        {{ t(`slides[${index}].title`) }}
                                    </h3>

                                    <p class="mt-2 text-lg text-slate-200">
                                        {{ t(`slides[${index}].desc`) }}
                                    </p>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <div class="lg:col-span-4 space-y-8">
                        <div>
                            <h1
                                class="text-4xl font-black text-[#0a192f] leading-tight"
                            >
                                {{ t("hero.welcome") }}
                            </h1>

                            <p class="text-slate-600 mt-4">
                                {{ t("hero.bookingNote") }}
                            </p>
                        </div>

                        <div class="grid gap-4">
                            <Link
                                :href="safeRoute('register')"
                                class="bg-[#0a192f] text-white p-6 rounded-3xl hover:opacity-90 transition-opacity"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="material-icons-outlined text-3xl text-blue-400"
                                        >
                                            person_add
                                        </span>
                                    </div>

                                    <div>
                                        <h3 class="text-xl font-black">
                                            {{ t("cards.join") }}
                                        </h3>

                                        <p class="text-slate-300 mt-1 text-sm">
                                            {{ t("cards.joinDesc") }}
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                :href="safeRoute('login')"
                                class="bg-white border border-slate-200 p-6 rounded-3xl hover:bg-slate-50 transition-colors"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center shrink-0"
                                    >
                                        <span
                                            class="material-icons-outlined text-3xl text-[#0a192f]"
                                        >
                                            login
                                        </span>
                                    </div>

                                    <div>
                                        <h3
                                            class="text-xl font-black text-[#0a192f]"
                                        >
                                            {{ t("cards.login") }}
                                        </h3>

                                        <p class="text-slate-500 mt-1 text-sm">
                                            {{ t("cards.loginDesc") }}
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer
            ref="footerRef"
            id="footer"
            class="bg-[#0a192f] text-white pt-16 pb-8 border-t border-white/10"
        >
            <div
                class="container mx-auto px-6 lg:px-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12"
            >
                <div class="space-y-6">
                    <h3 class="text-lg font-black uppercase tracking-tight">
                        Adama City
                        <br />
                        <span class="text-blue-400 text-sm">
                            {{ t("footer.admin") }}
                        </span>
                    </h3>

                    <p class="text-slate-400 text-sm leading-relaxed">
                        {{ t("footer.contactText") }}
                    </p>

                    <div class="flex gap-3 pt-2">
                        <a
                            v-for="social in socialLinks"
                            :key="social.name"
                            :href="social.url"
                            target="_blank"
                            class="w-10 h-10 flex items-center justify-center rounded-xl transition-transform hover:scale-105"
                            :class="[social.bg, social.color]"
                        >
                            <span class="material-icons-outlined">
                                {{ social.icon }}
                            </span>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-blue-400 font-black uppercase text-xs mb-6">
                        {{ t("nav.navigation") }}
                    </h4>

                    <ul class="space-y-3">
                        <li>
                            <Link
                                :href="safeRoute('home')"
                                class="text-slate-300 hover:text-white transition-colors"
                            >
                                {{ t("nav.home") }}
                            </Link>
                        </li>

                        <li>
                            <Link
                                :href="safeRoute('gallery.index')"
                                class="text-slate-300 hover:text-white transition-colors"
                            >
                                {{ t("nav.gallery") }}
                            </Link>
                        </li>

                        <li>
                            <Link
                                :href="safeRoute('about')"
                                class="text-slate-300 hover:text-white transition-colors"
                            >
                                {{ t("nav.about") }}
                            </Link>
                        </li>

                        <li>
                            <Link
                                :href="safeRoute('reports.index')"
                                class="text-slate-300 hover:text-white transition-colors"
                            >
                                {{ t("nav.reports") }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-blue-400 font-black uppercase text-xs mb-6">
                        {{ t("footer.contacts") }}
                    </h4>

                    <ul class="space-y-4 text-slate-300 text-sm">
                        <li class="flex items-start gap-3">
                            <span
                                class="material-icons-outlined text-blue-500 shrink-0"
                            >
                                place
                            </span>
                            <span>
                                {{ t("footer.location") }}
                            </span>
                        </li>

                        <li class="flex items-center gap-3">
                            <span
                                class="material-icons-outlined text-blue-500 shrink-0"
                            >
                                email
                            </span>
                            <a
                                href="mailto:asto@adamacity.gov.et"
                                class="hover:text-white transition-colors"
                            >
                                asto@adamacity.gov.et
                            </a>
                        </li>

                        <li class="flex items-center gap-3">
                            <span
                                class="material-icons-outlined text-blue-500 shrink-0"
                            >
                                phone
                            </span>
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
                    <h4 class="text-blue-400 font-black uppercase text-xs mb-6">
                        {{ t("footer.updates") }}
                    </h4>

                    <p class="text-slate-300 text-sm mb-4">
                        {{ t("footer.subscribeText") }}
                    </p>

                    <div class="flex flex-col gap-3">
                        <input
                            type="email"
                            :placeholder="t('footer.emailPlaceholder')"
                            class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500"
                        />

                        <button
                            class="bg-blue-600 hover:bg-blue-500 text-white font-black py-3 rounded-xl uppercase text-xs transition-colors"
                        >
                            {{ t("footer.subscribe") }}
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
                    &copy; {{ new Date().getFullYear() }}
                    {{ t("footer.copyright") }}
                </p>

                <p class="text-slate-600 text-[10px] uppercase">
                    {{ t("footer.built") }}
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.nav-desktop-link {
    @apply text-sm font-bold text-slate-300 uppercase tracking-widest hover:text-blue-400 transition-all;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
