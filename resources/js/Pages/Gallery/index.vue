<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    images: Array,
});

const activeImg = ref(null);
const isMenuOpen = ref(false);

/**
 * Helper to handle route resolution safely.
 */
const safeRoute = (name, id = null) => {
    try {
        // @ts-ignore - 'route' is provided globally by Ziggy
        return id ? route(name, { id }) : route(name);
    } catch {
        const fallbacks = {
            home: "/",
            "gallery.index": "/gallery",
            "gallery.view": `/gallery/${id}/view`,
            "gallery.like": `/gallery/${id}/like`,
            about: "/about",
            reports: "/reports",
            contacts: "/#contacts",
        };
        const key = name.toLowerCase();
        return fallbacks[key] || "/";
    }
};

const handleImageClick = (img) => {
    activeImg.value = img.url;
    document.body.style.overflow = "hidden";

    if (img.id) {
        router.post(
            safeRoute("gallery.view", img.id),
            {},
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }
};

const handleLike = (imgId) => {
    if (!imgId) return;

    router.post(
        safeRoute("gallery.like", imgId),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

const closeLightbox = () => {
    activeImg.value = null;
    document.body.style.overflow = "auto";
};
</script>

<template>
    <Head title="Gallery - Adama City" />

    <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
        <!-- ================= RESPONSIVE NAVIGATION ================= -->
        <nav
            class="bg-[#0a192f] sticky top-0 z-[100] shadow-xl border-b border-white/5"
        >
            <div
                class="container mx-auto px-4 md:px-8 flex justify-between items-center py-6"
            >
                <!-- BRANDING -->
                <Link
                    :href="safeRoute('home')"
                    class="flex items-center gap-4 group"
                >
                    <div
                        class="bg-white p-2 rounded-full shadow-lg group-hover:scale-105 transition-transform duration-300"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Logo"
                            class="h-10 w-10 object-contain"
                        />
                    </div>
                    <div class="flex flex-col justify-center leading-tight">
                        <h2
                            class="text-white font-black text-sm md:text-xl tracking-tight uppercase"
                        >
                            ADAMA CITY
                        </h2>
                        <span
                            class="text-[10px] text-blue-400 uppercase tracking-[0.2em] font-bold"
                        >
                            Official Gallery
                        </span>
                    </div>
                </Link>

                <!-- DESKTOP MENU (Updated text size and weight) -->
                <div class="hidden lg:flex items-center gap-12">
                    <Link
                        v-for="link in [
                            'Home',
                            'Gallery',
                            'About',
                            'Reports',
                            'Contacts',
                        ]"
                        :key="link"
                        :href="
                            link === 'Gallery'
                                ? safeRoute('gallery.index')
                                : safeRoute(link)
                        "
                        class="text-lg font-black uppercase tracking-widest transition-all"
                        :class="
                            $page.url.startsWith('/' + link.toLowerCase())
                                ? 'text-blue-400'
                                : 'text-gray-300 hover:text-white'
                        "
                    >
                        {{ link }}
                    </Link>
                </div>

                <!-- MOBILE TOGGLE -->
                <div class="flex items-center">
                    <button
                        @click="isMenuOpen = !isMenuOpen"
                        class="lg:hidden text-white p-2 focus:outline-none"
                    >
                        <span class="material-icons-outlined text-3xl">
                            {{ isMenuOpen ? "close" : "menu" }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- MOBILE MENU -->
            <div
                v-show="isMenuOpen"
                class="lg:hidden bg-[#0d213f] border-t border-white/10 px-6 py-8 space-y-6"
            >
                <Link
                    v-for="link in [
                        'Home',
                        'Gallery',
                        'About',
                        'Reports',
                        'Contacts',
                    ]"
                    :key="link"
                    :href="
                        link === 'Gallery'
                            ? safeRoute('gallery.index')
                            : safeRoute(link)
                    "
                    @click="isMenuOpen = false"
                    class="block text-white text-2xl font-black uppercase tracking-widest"
                >
                    {{ link }}
                </Link>
            </div>
        </nav>

        <!-- HERO SECTION -->
        <header class="py-20 text-center bg-white border-b border-slate-200">
            <h1
                class="text-4xl md:text-6xl font-black text-[#0a192f] mb-6 tracking-tight uppercase px-4"
            >
                Adama City Administration Gallery
            </h1>
            <p class="text-slate-500 max-w-2xl mx-auto px-6 text-xl">
                Explore the halls of Adama City Administration, where visitors
                engage, learn, and experience our services firsthand.
            </p>
        </header>

        <!-- GALLERY GRID -->
        <main class="container mx-auto px-4 md:px-6 py-12">
            <div
                v-if="images && images.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10"
            >
                <div
                    v-for="img in images"
                    :key="img.id"
                    class="group bg-white rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 border border-slate-200 flex flex-col"
                >
                    <!-- Thumbnail -->
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
                                class="bg-white/20 backdrop-blur-md p-4 rounded-full border border-white/30"
                            >
                                <span
                                    class="material-icons-outlined text-white text-4xl"
                                    >zoom_in</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-8 flex-grow flex flex-col justify-between">
                        <div>
                            <h3
                                class="font-bold text-slate-800 text-2xl leading-tight mb-3 truncate"
                            >
                                {{ img.title || "Adama City View" }}
                            </h3>
                            <div class="flex items-center gap-4 text-slate-400">
                                <span
                                    class="flex items-center gap-1.5 text-sm font-semibold"
                                >
                                    <span
                                        class="material-icons-outlined text-base"
                                        >visibility</span
                                    >
                                    {{ img.views_count || 0 }} Views
                                </span>
                                <span
                                    class="text-xs font-black uppercase tracking-tighter bg-slate-100 px-3 py-1.5 rounded-lg text-slate-500"
                                >
                                    City Project
                                </span>
                            </div>
                        </div>

                        <!-- Footer / Like Button -->
                        <div
                            class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center"
                        >
                            <button
                                @click.stop="handleLike(img.id)"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-full transition-all"
                                :class="
                                    img.is_liked
                                        ? 'bg-red-50 text-red-600'
                                        : 'bg-slate-50 text-slate-500 hover:bg-slate-100'
                                "
                            >
                                <span class="material-icons-outlined text-2xl">
                                    {{
                                        img.is_liked
                                            ? "favorite"
                                            : "favorite_border"
                                    }}
                                </span>
                                <span class="font-bold text-base">{{
                                    img.likes_count || 0
                                }}</span>
                            </button>
                            <span
                                class="text-xs text-slate-300 font-bold uppercase tracking-widest"
                            >
                                ADAMA ADMIN
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div v-else class="text-center py-24">
                <span
                    class="material-icons-outlined text-8xl text-slate-200 mb-6"
                    >image_not_supported</span
                >
                <p class="text-slate-400 text-xl font-bold">
                    No images available in the gallery yet.
                </p>
            </div>
        </main>

        <!-- LIGHTBOX -->
        <Transition name="fade">
            <div
                v-if="activeImg"
                class="fixed inset-0 z-[1000] bg-[#0a192f]/95 backdrop-blur-md flex items-center justify-center p-4"
                @click.self="closeLightbox"
            >
                <button
                    @click="closeLightbox"
                    class="absolute top-8 right-8 text-white hover:rotate-90 transition-transform duration-300 focus:outline-none"
                >
                    <span class="material-icons-outlined text-6xl">close</span>
                </button>
                <img
                    :src="activeImg"
                    class="max-w-full max-h-[85vh] rounded-[2rem] shadow-2xl border-8 border-white/5"
                />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Material+Icons+Outlined&display=swap");

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
