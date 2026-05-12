<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            totalSatisfaction: 0,
            lastSync: "N/A",
        }),
    },
});
</script>

<template>
    <Head :title="t('nav.reports')" />

    <div class="min-h-screen bg-white text-slate-700 antialiased font-sans">
        <!-- FIXED NAVIGATION BAR -->
        <nav
            class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 py-3 shadow-sm"
        >
            <div
                class="max-w-7xl mx-auto px-6 flex justify-between items-center"
            >
                <!-- BRANDING SECTION (Circular Badge Style) -->
                <Link
                    :href="route('home')"
                    class="flex items-center gap-4 group"
                >
                    <!-- Circular Logo Badge -->
                    <div
                        class="bg-white p-2 rounded-full shadow-md border border-slate-50 group-hover:scale-105 transition-transform duration-300"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Adama City Logo"
                            class="h-9 w-9 object-contain"
                        />
                    </div>

                    <!-- Text Brand -->
                    <div class="flex flex-col justify-center leading-tight">
                        <h2
                            class="text-[#1e293b] font-black text-sm md:text-lg tracking-tight uppercase"
                        >
                            ADAMA CITY
                        </h2>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-[10px] text-indigo-500 uppercase tracking-[0.2em] font-bold leading-none"
                            >
                                Reports
                            </span>
                            <!-- Minimal Live Status Indicator -->
                            <div
                                class="flex items-center gap-1.5 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100"
                            >
                                <span class="relative flex h-1.5 w-1.5">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"
                                    ></span>
                                </span>
                                <span
                                    class="text-[8px] font-black text-emerald-600 uppercase tracking-tighter"
                                    >Live</span
                                >
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- ACTION BUTTONS -->
                <div class="flex items-center gap-4">
                    <span
                        class="text-[11px] font-bold text-slate-300 uppercase tracking-widest hidden lg:block"
                    >
                        Digital Services
                    </span>
                    <Link
                        :href="route('home')"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-indigo-500/20 active:scale-95 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        {{ t("gallery.back_home") }}
                    </Link>
                </div>
            </div>
        </nav>

        <main class="pt-32 pb-20">
            <!-- Header Section -->
            <div class="px-6 max-w-7xl mx-auto mb-16">
                <div
                    class="flex flex-col md:flex-row md:items-end justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-4xl md:text-5xl font-black text-[#1e293b] mb-3 tracking-tight"
                        >
                            Analytics Overview
                        </h1>
                        <div class="h-2 w-20 bg-indigo-600 rounded-full"></div>
                    </div>
                    <div class="flex flex-col md:items-end">
                        <p
                            class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]"
                        >
                            Last Updated Data
                        </p>
                        <p class="text-slate-600 font-bold text-sm">
                            {{ stats.lastSync }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid Section -->
            <section
                class="bg-[#f8faff] py-24 px-6 border-y border-slate-100 relative overflow-hidden"
            >
                <div class="max-w-6xl mx-auto relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Registered Users Card -->
                        <div
                            class="group bg-white rounded-[3rem] p-12 shadow-2xl shadow-indigo-900/5 border border-white flex flex-col items-center text-center transition-all hover:-translate-y-2"
                        >
                            <div
                                class="bg-indigo-50 p-5 rounded-2xl mb-8 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 text-indigo-600"
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
                                        stroke-width="1.5"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                            </div>
                            <span
                                class="text-7xl font-black text-slate-800 mb-2 tracking-tighter"
                            >
                                {{ stats.totalUsers?.toLocaleString() ?? "0"
                                }}<span class="text-indigo-600 ml-1">+</span>
                            </span>
                            <h3
                                class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]"
                            >
                                Registered Users
                            </h3>
                        </div>

                        <!-- Satisfaction Feedbacks Card -->
                        <div
                            class="group bg-white rounded-[3rem] p-12 shadow-2xl shadow-indigo-900/5 border border-white flex flex-col items-center text-center transition-all hover:-translate-y-2"
                        >
                            <div
                                class="bg-emerald-50 p-5 rounded-2xl mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 text-emerald-600"
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
                                        stroke-width="1.5"
                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <span
                                class="text-7xl font-black text-slate-800 mb-2 tracking-tighter"
                            >
                                {{
                                    stats.totalSatisfaction?.toLocaleString() ??
                                    "0"
                                }}<span class="text-indigo-600 ml-1">+</span>
                            </span>
                            <h3
                                class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]"
                            >
                                Satisfaction Feedbacks
                            </h3>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <div class="mt-24 px-6 max-w-5xl mx-auto">
                <div
                    class="flex flex-col items-center text-center bg-slate-50 rounded-[3rem] p-12 border border-slate-100"
                >
                    <div
                        class="bg-white px-6 py-2 rounded-full shadow-sm border border-slate-100 mb-6 font-black text-[10px] uppercase tracking-widest text-indigo-600"
                    >
                        System Logic
                    </div>
                    <h2
                        class="text-3xl font-black text-slate-800 mb-6 tracking-tight"
                    >
                        Real-Time Data Sync
                    </h2>
                    <p
                        class="text-slate-500 leading-relaxed font-medium text-lg"
                    >
                        Statistics are pulled directly from the
                        <span class="text-indigo-600 font-bold"
                            >Adama Central Hub</span
                        >. As citizens interact with digital services, these
                        metrics reflect live public sentiment and platform
                        growth.
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
            circle at 20% 30%,
            rgba(79, 70, 229, 0.04) 0%,
            transparent 50%
        ),
        radial-gradient(
            circle at 80% 70%,
            rgba(59, 130, 246, 0.04) 0%,
            transparent 50%
        );
}
</style>
