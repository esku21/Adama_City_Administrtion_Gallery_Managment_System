<script setup>
import { ref, computed, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ToastNotification from "@/Components/ToastNotification.vue";

const isSidebarOpen = ref(true);
const page = usePage();

// 1. Safe access to user data
const guide = computed(() => page.props.auth.user);
const assignedHall = computed(
    () => guide.value?.hall?.name || "No Hall Assigned",
);

// 2. Flash Message Logic
const flash = computed(() => page.props.flash);
const showToast = ref(false);

// Watch for flash changes to trigger the popup
watch(
    () => page.props.flash,
    (newVal) => {
        if (newVal?.message) {
            showToast.value = true;
            setTimeout(() => {
                showToast.value = false;
            }, 3500);
        }
    },
    { deep: true },
);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
    <div class="min-h-screen bg-zinc-50 flex font-sans antialiased">
        <ToastNotification
            v-if="showToast && flash.message"
            :message="flash.message"
            :type="flash.type || 'success'"
        />

        <aside
            :class="isSidebarOpen ? 'w-64' : 'w-20'"
            class="bg-indigo-950 text-white transition-all duration-300 flex flex-col shadow-xl fixed inset-y-0 z-50"
        >
            <div class="p-6 border-b border-white/10">
                <div
                    v-if="isSidebarOpen"
                    class="transition-opacity duration-300"
                >
                    <h1
                        class="text-lg font-black tracking-tighter uppercase italic"
                    >
                        Adama <span class="text-emerald-400">GMS</span>
                    </h1>
                    <p
                        class="text-[9px] text-indigo-300 mt-1 uppercase tracking-widest font-bold"
                    >
                        📍 {{ assignedHall }}
                    </p>
                </div>
                <div v-else class="text-center font-black text-emerald-400">
                    A
                </div>
            </div>

            <nav class="mt-6 flex-1 px-4 space-y-1">
                <Link
                    :href="route('guide.dashboard')"
                    class="nav-link"
                    :class="{
                        'nav-active': route().current('guide.dashboard'),
                    }"
                >
                    <span class="material-icons-outlined"
                        >confirmation_number</span
                    >
                    <span v-if="isSidebarOpen" class="ml-3">Hall Bookings</span>
                </Link>

                <Link
                    :href="route('guide.scanner')"
                    class="nav-link"
                    :class="{ 'nav-active': route().current('guide.scanner') }"
                >
                    <span class="material-icons-outlined">qr_code_scanner</span>
                    <span v-if="isSidebarOpen" class="ml-3"
                        >Ticket Scanner</span
                    >
                </Link>

                <div class="py-4">
                    <div class="border-t border-white/5 mx-2"></div>
                </div>

                <Link
                    :href="route('guide.profile.edit')"
                    class="nav-link"
                    :class="{
                        'nav-active': route().current('guide.profile.edit'),
                    }"
                >
                    <span class="material-icons-outlined">person</span>
                    <span v-if="isSidebarOpen" class="ml-3">My Profile</span>
                </Link>

                <Link
                    :href="route('guide.settings.index')"
                    class="nav-link"
                    :class="{
                        'nav-active': route().current('guide.settings.index'),
                    }"
                >
                    <span class="material-icons-outlined">settings</span>
                    <span v-if="isSidebarOpen" class="ml-3"
                        >Account Settings</span
                    >
                </Link>
            </nav>

            <div class="p-4 border-t border-white/5">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="logout-btn"
                >
                    <span class="material-icons-outlined"
                        >power_settings_new</span
                    >
                    <span v-if="isSidebarOpen" class="ml-3">Logout</span>
                </Link>
            </div>
        </aside>

        <div
            class="flex-1 transition-all duration-300"
            :class="isSidebarOpen ? 'ml-64' : 'ml-20'"
        >
            <header
                class="bg-white/80 backdrop-blur-md border-b border-zinc-100 h-16 flex items-center justify-between px-8 sticky top-0 z-40"
            >
                <button
                    @click="toggleSidebar"
                    class="p-2 hover:bg-zinc-100 rounded-lg transition-colors text-zinc-500"
                >
                    <span class="material-icons-outlined">menu</span>
                </button>

                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <p
                            class="text-[11px] font-black text-zinc-900 uppercase tracking-tighter"
                        >
                            {{ guide?.firstName }} {{ guide?.lastName }}
                        </p>
                        <p
                            class="text-[9px] text-emerald-500 font-bold uppercase tracking-[0.2em]"
                        >
                            {{ assignedHall }} • {{ guide?.visitorType }}
                        </p>
                    </div>
                    <div
                        class="h-10 w-10 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-indigo-100"
                    >
                        {{ guide?.firstName?.charAt(0) }}
                    </div>
                </div>
            </header>

            <main class="p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.nav-link {
    @apply flex items-center p-3 rounded-2xl transition-all duration-200 text-indigo-200 hover:bg-white/5 hover:text-white font-bold text-xs uppercase tracking-widest;
}
.nav-active {
    @apply bg-emerald-500 text-white shadow-lg shadow-emerald-900/20;
}
.logout-btn {
    @apply w-full flex items-center p-3 text-rose-300 hover:bg-rose-500/10 rounded-2xl transition-all font-bold text-xs uppercase tracking-widest;
}
</style>
