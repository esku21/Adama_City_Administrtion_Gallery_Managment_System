<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    auth: Object,
    settings: Object,
});

const isDeactivated = computed(
    () => props.settings?.system_status === "inactive",
);

// FIX: Create a function for the reload
const refreshPage = () => {
    window.location.reload();
};
</script>

<template>
    <Head title="System Unavailable" />

    <div
        class="min-h-screen bg-slate-50 flex items-center justify-center p-6 font-sans"
    >
        <div class="max-w-md w-full text-center">
            <div class="relative mb-8 inline-block">
                <div
                    class="absolute inset-0 bg-rose-200 rounded-3xl blur-2xl opacity-40 animate-pulse"
                ></div>
                <div
                    class="relative w-24 h-24 bg-white rounded-3xl shadow-xl border border-slate-100 flex items-center justify-center"
                >
                    <span
                        class="material-icons-outlined text-5xl text-rose-500"
                    >
                        {{ isDeactivated ? "block" : "construction" }}
                    </span>
                </div>
            </div>

            <h1
                class="text-3xl font-black text-slate-800 uppercase tracking-tighter mb-2"
            >
                {{ isDeactivated ? "System Offline" : "Under Maintenance" }}
            </h1>

            <p class="text-slate-500 font-medium leading-relaxed mb-8">
                The ACAGMS protocol is currently
                <span
                    class="text-rose-600 font-bold uppercase tracking-widest text-[10px] px-2 py-1 bg-rose-50 rounded-lg"
                >
                    {{ isDeactivated ? "Deactivated" : "Updating" }} </span
                >. Access is temporarily restricted. Please check back later.
            </p>

            <div class="space-y-3">
                <button
                    @click="refreshPage"
                    class="w-full py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm"
                >
                    Check Status Again
                </button>

                <Link
                    v-if="auth?.user?.role === 'admin'"
                    :href="route('admin.dashboard')"
                    class="w-full flex items-center justify-center gap-2 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"
                >
                    <span class="material-icons-outlined text-sm"
                        >dashboard</span
                    >
                    Back to Command Center
                </Link>

                <Link
                    v-else
                    :href="route('login')"
                    class="block text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em] hover:text-indigo-500 transition-colors pt-4"
                >
                    Administrative Login
                </Link>
            </div>

            <div
                class="mt-12 flex items-center justify-center gap-2 opacity-30"
            >
                <div
                    class="w-6 h-6 rounded-lg bg-slate-400 flex items-center justify-center"
                >
                    <span class="material-icons-outlined text-white text-[12px]"
                        >token</span
                    >
                </div>
                <span
                    class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-600"
                    >ACAGMS</span
                >
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/icon?family=Material+Icons+Outlined");

@keyframes pulse-gentle {
    0%,
    100% {
        opacity: 0.4;
        transform: scale(1);
    }
    50% {
        opacity: 0.6;
        transform: scale(1.1);
    }
}
.animate-pulse {
    animation: pulse-gentle 3s infinite ease-in-out;
}
</style>
