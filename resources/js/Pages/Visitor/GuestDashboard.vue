<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";

// Reactive state for the mobile menu toggle
const isMenuOpen = ref(false);
</script>

<template>
    <Head title="Adama City Gallery Administration" />

    <div class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">
        <nav
            class="bg-white border-b border-slate-200 px-4 md:px-6 py-4 sticky top-0 z-50 shadow-sm"
        >
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <img
                        src="/storage/images/adama.png"
                        alt="Adama City Logo"
                        class="h-10 w-10 md:h-12 md:w-12 object-contain"
                    />
                    <div class="flex flex-col text-left">
                        <h2
                            class="font-bold text-slate-800 text-sm md:text-base leading-none"
                        >
                            Adama City
                        </h2>
                        <span
                            class="text-[9px] md:text-[10px] text-slate-500 uppercase tracking-wider font-semibold"
                        >
                            Gallery Administration
                        </span>
                    </div>
                </div>

                <div class="hidden lg:flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter"
                        >
                            Language:
                        </span>
                        <div id="google_translate_element"></div>
                    </div>
                    <div class="h-6 w-[1px] bg-slate-200"></div>
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-slate-600 hover:text-blue-900 transition-colors"
                        >
                            Sign In
                        </Link>
                        <Link
                            :href="route('register')"
                            class="bg-blue-900 text-white px-5 py-2 rounded-full text-sm font-bold hover:bg-blue-800 transition-all shadow-md shadow-blue-100"
                        >
                            Register
                        </Link>
                    </div>
                </div>

                <button
                    @click="isMenuOpen = !isMenuOpen"
                    class="lg:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            v-if="!isMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <div
                v-show="isMenuOpen"
                class="lg:hidden mt-4 pt-4 border-t border-slate-100 space-y-4"
            >
                <div class="flex flex-col gap-4">
                    <Link
                        :href="route('login')"
                        class="text-sm font-bold text-slate-700"
                        >Sign In</Link
                    >
                    <Link
                        :href="route('register')"
                        class="bg-blue-900 text-white text-center py-3 rounded-xl font-bold"
                        >Register</Link
                    >
                    <div class="flex items-center gap-2 py-2">
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase"
                            >Language:</span
                        >
                        <div id="google_translate_element_mobile"></div>
                    </div>
                </div>
            </div>
        </nav>

        <main
            class="max-w-6xl mx-auto px-6 py-12 md:py-16 flex flex-col items-center"
        >
            <div class="text-center mb-12 md:mb-16">
                <div
                    class="inline-block bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-widest mb-6"
                >
                    Smart City Project 2026
                </div>
                <h1
                    class="text-3xl md:text-6xl font-black text-slate-900 tracking-tight mb-6 leading-tight"
                >
                    Adama City Gallery <br class="hidden md:block" />
                    <span class="text-blue-900">Administration</span>
                </h1>
                <p
                    class="text-slate-500 text-base md:text-xl max-w-2xl mx-auto leading-relaxed"
                >
                    Access the official portal to manage your gallery visits,
                    book appointments, and generate digital QR tickets for your
                    next visit.
                </p>
            </div>

            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 w-full max-w-4xl"
            >
                <Link
                    :href="route('login')"
                    class="group bg-white p-8 md:p-10 rounded-[24px] md:rounded-[32px] border border-slate-200 shadow-sm hover:border-blue-500 hover:shadow-xl transition-all duration-300"
                >
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-blue-50 text-blue-900 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-900 group-hover:text-white transition-all"
                    >
                        <svg
                            class="w-7 h-7 md:w-8 md:h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                            />
                        </svg>
                    </div>
                    <h3
                        class="text-xl md:text-2xl font-bold text-slate-900 mb-3"
                    >
                        Login to Dashboard
                    </h3>
                    <p class="text-slate-500 text-sm md:text-base mb-6">
                        View your existing bookings, download your QR codes, and
                        manage your profile settings.
                    </p>
                    <div
                        class="text-blue-900 font-bold flex items-center gap-2 group-hover:translate-x-2 transition-transform"
                    >
                        Proceed to Login <span>→</span>
                    </div>
                </Link>

                <Link
                    :href="route('register')"
                    class="group bg-white p-8 md:p-10 rounded-[24px] md:rounded-[32px] border border-slate-200 shadow-sm hover:border-blue-900 hover:shadow-xl transition-all duration-300"
                >
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-slate-50 text-slate-900 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-900 group-hover:text-white transition-all"
                    >
                        <svg
                            class="w-7 h-7 md:w-8 md:h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                            />
                        </svg>
                    </div>
                    <h3
                        class="text-xl md:text-2xl font-bold text-slate-900 mb-3"
                    >
                        New Registration
                    </h3>
                    <p class="text-slate-500 text-sm md:text-base mb-6">
                        Create a new citizen account to start booking gallery
                        visits and receiving city updates.
                    </p>
                    <div
                        class="text-blue-900 font-bold flex items-center gap-2 group-hover:translate-x-2 transition-transform"
                    >
                        Create Account <span>→</span>
                    </div>
                </Link>
            </div>

            <div
                class="mt-20 md:mt-24 w-full pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-6"
            >
                <p
                    class="text-slate-400 text-xs md:text-sm font-medium text-center"
                >
                    © 2026 Adama City Administration. All Rights Reserved.
                </p>
                <div class="flex gap-4 md:gap-6">
                    <span
                        class="text-[9px] md:text-[10px] font-black text-slate-300 uppercase tracking-widest"
                        >Innovation</span
                    >
                    <span
                        class="text-[9px] md:text-[10px] font-black text-slate-300 uppercase tracking-widest"
                        >Culture</span
                    >
                    <span
                        class="text-[9px] md:text-[10px] font-black text-slate-300 uppercase tracking-widest"
                        >Community</span
                    >
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
:deep(.goog-te-gadget-simple) {
    margin-top: 2px;
}
</style>
