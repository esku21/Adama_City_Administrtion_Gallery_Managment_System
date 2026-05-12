<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    images: Array,
});

const activeImg = ref(null);

const handleImageClick = (img) => {
    activeImg.value = img.url;
    document.body.style.overflow = "hidden";

    if (img.id) {
        router.post(
            route("gallery.view", { id: img.id }),
            {},
            { preserveScroll: true, preserveState: true },
        );
    }
};

const handleLike = (imgId) => {
    if (imgId) {
        router.post(
            route("gallery.like", { id: imgId }),
            {},
            { preserveScroll: true, preserveState: true },
        );
    }
};

const closeLightbox = () => {
    activeImg.value = null;
    document.body.style.overflow = "auto";
};
</script>

<template>
    <Head title="Gallery - Adama City" />

    <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
        <!-- FIXED NAVIGATION BAR -->
        <nav class="bg-[#0a192f] sticky top-0 z-[100] shadow-xl py-3">
            <div
                class="container mx-auto px-4 md:px-6 flex justify-between items-center"
            >
                <!-- BRANDING SECTION (Matches Image 1) -->
                <Link
                    :href="route('home')"
                    class="flex items-center gap-4 group"
                >
                    <!-- Circular Logo Wrapper -->
                    <div
                        class="bg-white p-2 rounded-full shadow-lg group-hover:scale-105 transition-transform duration-300"
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
                            class="text-white font-black text-sm md:text-lg tracking-tight uppercase"
                        >
                            ADAMA CITY
                        </h2>
                        <span
                            class="text-[10px] text-blue-400 uppercase tracking-[0.2em] font-bold leading-none"
                        >
                            Official Gallery
                        </span>
                    </div>
                </Link>

                <!-- BACK BUTTON -->
                <Link
                    :href="route('home')"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-blue-500/20 active:scale-95"
                >
                    Back Home
                </Link>
            </div>
        </nav>

        <!-- HERO HEADER -->
        <header class="py-16 text-center bg-white border-b border-slate-200">
            <h1
                class="text-4xl md:text-5xl font-black text-[#0a192f] mb-4 tracking-tight"
            >
                ADAMA CITY ADMINISTRATION GALLERY
            </h1>
            <p class="text-slate-500 max-w-2xl mx-auto px-6 text-lg">
                Explore the halls of Adama City Administration, where visitors
                engage, learn, and experience our services firsthand.
            </p>
        </header>

        <!-- GALLERY GRID -->
        <main class="container mx-auto px-4 md:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    v-for="img in images"
                    :key="img.id"
                    class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 border border-slate-200 flex flex-col"
                >
                    <div
                        class="relative aspect-video cursor-zoom-in overflow-hidden bg-slate-200"
                        @click="handleImageClick(img)"
                    >
                        <img
                            :src="img.url"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out"
                            :alt="img.title"
                        />
                        <div
                            class="absolute inset-0 bg-[#0a192f]/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"
                        >
                            <div
                                class="bg-white/20 backdrop-blur-md p-3 rounded-full border border-white/30"
                            >
                                <span
                                    class="material-icons-outlined text-white text-3xl"
                                    >zoom_in</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <h3
                                class="font-bold text-slate-800 text-xl leading-tight mb-2 truncate"
                            >
                                {{ img.title || "Adama City View" }}
                            </h3>
                            <div class="flex items-center gap-4 text-slate-400">
                                <span
                                    class="flex items-center gap-1.5 text-xs font-semibold"
                                >
                                    <span
                                        class="material-icons-outlined text-sm"
                                        >visibility</span
                                    >
                                    {{ img.views_count || 0 }} Views
                                </span>
                                <span
                                    class="text-[10px] font-black uppercase tracking-tighter bg-slate-100 px-2 py-1 rounded text-slate-500"
                                >
                                    City Project
                                </span>
                            </div>
                        </div>

                        <div
                            class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center"
                        >
                            <button
                                @click.stop="handleLike(img.id)"
                                class="flex items-center gap-2 px-4 py-2 rounded-full transition-all"
                                :class="
                                    img.is_liked
                                        ? 'bg-red-50 text-red-600'
                                        : 'bg-slate-50 text-slate-500 hover:bg-slate-100'
                                "
                            >
                                <span class="material-icons-outlined text-xl">
                                    {{
                                        img.is_liked
                                            ? "favorite"
                                            : "favorite_border"
                                    }}
                                </span>
                                <span class="font-bold text-sm">{{
                                    img.likes_count || 0
                                }}</span>
                            </button>
                            <span
                                class="text-[10px] text-slate-300 font-medium uppercase"
                                >ADAMA ADMIN</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- LIGHTBOX MODAL -->
        <Transition name="fade">
            <div
                v-if="activeImg"
                class="fixed inset-0 z-[1000] bg-[#0a192f]/95 backdrop-blur-md flex items-center justify-center p-4"
                @click.self="closeLightbox"
            >
                <button
                    @click="closeLightbox"
                    class="absolute top-6 right-6 text-white hover:rotate-90 transition-transform duration-300 focus:outline-none"
                >
                    <span class="material-icons-outlined text-5xl">close</span>
                </button>

                <img
                    :src="activeImg"
                    class="max-w-full max-h-[85vh] rounded-xl shadow-2xl border-4 border-white/5"
                />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Material+Icons+Outlined&display=swap");

.font-sans {
    font-family: "Plus Jakarta Sans", sans-serif;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

:global(body) {
    scroll-behavior: smooth;
}
</style>
