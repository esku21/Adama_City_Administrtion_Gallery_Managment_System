<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const safeRoute = (name) => {
    try {
        // @ts-ignore
        return route(name);
    } catch {
        return "/";
    }
};

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

    <!-- FULL PAGE WRAPPER WITH BACKGROUND -->
    <div
        class="min-h-screen font-sans relative flex flex-col overflow-x-hidden selection:bg-blue-500/30"
    >
        <!-- FIXED BACKGROUND IMAGE -->
        <div
            class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat scale-105"
            style="
                background-image: url(&quot;/storage/images/gallery7.jpg&quot;);
            "
        ></div>

        <!-- ENHANCED DOCK OVERLAY - Darker to fix readability -->
        <div
            class="fixed inset-0 z-0 bg-gradient-to-b from-black/80 via-black/40 to-black/90"
        ></div>

        <!-- ================= NAVBAR ================= -->
        <nav
            class="fixed top-0 w-full z-[100] bg-black/60 backdrop-blur-xl border-b border-white/10 shadow-2xl"
        >
            <div
                class="w-full px-4 md:px-8 py-3 flex justify-between items-center"
            >
                <Link
                    :href="safeRoute('home')"
                    class="flex items-center gap-3 group"
                >
                    <div
                        class="bg-white p-1.5 rounded-xl shadow-lg group-hover:rotate-6 transition-transform"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Logo"
                            class="h-9 w-9 object-contain"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <h2
                            class="text-white font-black text-sm md:text-base leading-tight tracking-tight uppercase drop-shadow-sm"
                        >
                            ADAMA CITY
                        </h2>
                        <span
                            class="text-[9px] text-blue-400 uppercase tracking-[0.2em] font-bold leading-none"
                        >
                            Administration
                        </span>
                    </div>
                </Link>

                <Link
                    :href="safeRoute('home')"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl active:scale-95"
                >
                    ← {{ t("Back Home") || "Back Home" }}
                </Link>
            </div>
        </nav>

        <!-- ================= CONTENT AREA ================= -->
        <main class="relative z-10 flex-grow pt-32 pb-16">
            <!-- HERO SECTION (Higher Contrast) -->
            <section
                class="flex items-center justify-center text-center text-white py-12 px-6"
            >
                <div
                    class="max-w-4xl backdrop-blur-2xl bg-black/40 border border-white/20 p-10 md:p-16 rounded-[3rem] shadow-2xl drop-shadow-2xl"
                >
                    <h1
                        class="text-4xl md:text-6xl font-black tracking-tighter leading-tight uppercase mb-6 text-white drop-shadow-md"
                    >
                        {{
                            pageTitle && pageTitle !== "aboutPage.title"
                                ? pageTitle
                                : "About Adama City"
                        }}
                    </h1>
                    <div
                        class="h-1.5 w-24 bg-blue-500 mx-auto mb-8 rounded-full"
                    ></div>
                    <p
                        class="text-white text-base md:text-xl font-bold leading-relaxed max-w-2xl mx-auto drop-shadow-sm opacity-90"
                    >
                        A rapidly growing urban center in Ethiopia, driven by
                        innovation, smart governance, and rich cultural heritage
                        preservation.
                    </p>
                </div>
            </section>

            <!-- CORE PRINCIPLES (Visible Grid) -->
            <section class="container mx-auto px-6 lg:px-12 mt-12">
                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <!-- Vision -->
                    <div
                        class="group p-10 rounded-[2.5rem] bg-black/50 backdrop-blur-xl border border-white/20 hover:border-blue-500/50 transition-all duration-500 shadow-2xl"
                    >
                        <div
                            class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-lg group-hover:scale-110 transition-transform"
                        >
                            👁️
                        </div>
                        <h3
                            class="font-black text-white text-xl mb-4 uppercase tracking-tight drop-shadow-sm"
                        >
                            Vision
                        </h3>
                        <p
                            class="text-gray-200 text-sm font-bold leading-relaxed opacity-90"
                        >
                            Building a smart, sustainable, and modern city for
                            future generations through technology.
                        </p>
                    </div>

                    <!-- Mission -->
                    <div
                        class="group p-10 rounded-[2.5rem] bg-black/50 backdrop-blur-xl border border-white/20 hover:border-red-500/50 transition-all duration-500 shadow-2xl"
                    >
                        <div
                            class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-lg group-hover:scale-110 transition-transform"
                        >
                            🎯
                        </div>
                        <h3
                            class="font-black text-white text-xl mb-4 uppercase tracking-tight drop-shadow-sm"
                        >
                            Mission
                        </h3>
                        <p
                            class="text-gray-200 text-sm font-bold leading-relaxed opacity-90"
                        >
                            To deliver efficient, transparent, and
                            citizen-focused digital services to every resident.
                        </p>
                    </div>

                    <!-- Values -->
                    <div
                        class="group p-10 rounded-[2.5rem] bg-black/50 backdrop-blur-xl border border-white/20 hover:border-amber-500/50 transition-all duration-500 shadow-2xl"
                    >
                        <div
                            class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-lg group-hover:scale-110 transition-transform"
                        >
                            ⭐
                        </div>
                        <h3
                            class="font-black text-white text-xl mb-4 uppercase tracking-tight drop-shadow-sm"
                        >
                            Values
                        </h3>
                        <p
                            class="text-gray-200 text-sm font-bold leading-relaxed opacity-90"
                        >
                            Built on the pillars of transparency,
                            accountability, and unwavering public trust.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- ================= FOOTER ================= -->
        <footer
            class="relative z-10 bg-black/80 backdrop-blur-md py-8 border-t border-white/10 mt-auto"
        >
            <div
                class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-300"
                    >
                        Adama City Administration
                    </span>
                </div>
                <p
                    class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-500"
                >
                    © 2026 — All Rights Reserved
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Higher quality font rendering for white-on-dark text */
div {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Ensure the scrollbar doesn't ruin the dark look */
::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-track {
    background: #000;
}
::-webkit-scrollbar-thumb {
    background: #333;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #444;
}
</style>
