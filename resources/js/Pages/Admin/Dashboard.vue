<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t, te } = useI18n();

// Helper to handle missing translation keys gracefully
const translate = (key, fallback) => (te(key) ? t(key) : fallback);

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_visitors: 0,
            pending_bookings: 0,
            total_users: 0,
            total_bookings: 0,
        }),
    },
});

const pendingPercentage = computed(() => {
    const pending = props.stats?.pending_bookings ?? 0;
    const total = props.stats?.total_bookings ?? 1;
    return Math.min((pending / total) * 100, 100);
});

const activities = [
    {
        id: 1,
        user: "Admin",
        action: "Updated System Protocol",
        time: "2 mins ago",
        icon: "settings_suggest",
        color: "text-blue-500",
        bg: "bg-blue-50",
    },
    {
        id: 2,
        user: "System",
        action: "New Booking Received",
        time: "15 mins ago",
        icon: "add_chart",
        color: "text-emerald-500",
        bg: "bg-emerald-50",
    },
    {
        id: 3,
        user: "Guide_04",
        action: "Profile Verified",
        time: "1 hour ago",
        icon: "verified_user",
        color: "text-amber-500",
        bg: "bg-amber-50",
    },
];

const quickActions = [
    {
        label: "Manage Users",
        icon: "person_outline",
        route: "admin.dashboard",
        color: "bg-indigo-600",
    },
    {
        label: "View Reports",
        icon: "bar_chart",
        route: "admin.dashboard",
        color: "bg-slate-800",
    },
    {
        label: "System Settings",
        icon: "tune",
        route: "admin.dashboard",
        color: "bg-emerald-600",
    },
];
</script>

<template>
    <Head :title="translate('admin_dashboard.dashboard', 'Admin Dashboard')" />

    <AuthenticatedLayout>
        <div
            class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto space-y-6 animate-fade-in"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-6"
            >
                <div>
                    <h2
                        class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight uppercase flex items-center gap-3"
                    >
                        <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                        {{
                            translate(
                                "admin_dashboard.overview",
                                "System Overview",
                            )
                        }}
                    </h2>
                    <p
                        class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1 ml-5"
                    >
                        Management Unit • Operational
                    </p>
                </div>

                <div
                    class="flex items-center gap-3 bg-white p-2 pr-5 rounded-2xl border border-slate-100 shadow-sm self-start sm:self-auto"
                >
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white animate-pulse"
                    >
                        <span class="material-icons-outlined text-lg"
                            >sensors</span
                        >
                    </div>
                    <div>
                        <p
                            class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1"
                        >
                            Latency
                        </p>
                        <p class="text-sm font-bold text-slate-700">
                            24ms (Optimal)
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="stat-card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner"
                        >
                            <span class="material-icons-outlined text-2xl"
                                >groups</span
                            >
                        </div>
                        <span
                            class="text-[10px] font-black px-2 py-1 bg-slate-50 text-slate-400 rounded-md border border-slate-100"
                            >REAL-TIME</span
                        >
                    </div>
                    <p
                        class="text-slate-500 text-xs font-black uppercase tracking-wider mb-1"
                    >
                        Active Personnel
                    </p>
                    <h3 class="text-3xl font-black text-slate-900">
                        {{ (stats?.total_users ?? 0).toLocaleString() }}
                    </h3>
                </div>

                <div class="stat-card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner"
                        >
                            <span class="material-icons-outlined text-2xl"
                                >confirmation_number</span
                            >
                        </div>
                    </div>
                    <p
                        class="text-slate-500 text-xs font-black uppercase tracking-wider mb-1"
                    >
                        Database Bookings
                    </p>
                    <h3 class="text-3xl font-black text-slate-900">
                        {{ (stats?.total_bookings ?? 0).toLocaleString() }}
                    </h3>
                </div>

                <Link
                    :href="route('admin.bookings.index')"
                    class="stat-card border-amber-100 bg-gradient-to-br from-white to-amber-50/30 group sm:col-span-2 lg:col-span-1"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform"
                        >
                            <span class="material-icons-outlined text-2xl"
                                >priority_high</span
                            >
                        </div>
                        <span
                            class="material-icons-outlined text-amber-400 text-xl group-hover:translate-x-1 transition-transform"
                            >east</span
                        >
                    </div>
                    <p
                        class="text-amber-700 text-xs font-black uppercase tracking-wider mb-1"
                    >
                        Pending Verification
                    </p>
                    <h3 class="text-3xl font-black text-slate-900">
                        {{ stats?.pending_bookings ?? 0 }}
                    </h3>
                    <div
                        class="mt-4 h-2 w-full bg-slate-100 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-amber-500 transition-all duration-700"
                            :style="{ width: pendingPercentage + '%' }"
                        ></div>
                    </div>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 space-y-6">
                    <div
                        class="relative bg-slate-950 rounded-[2.5rem] p-8 lg:p-10 text-white overflow-hidden shadow-2xl border border-white/5"
                    >
                        <div
                            class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8"
                        >
                            <div class="text-left">
                                <h4
                                    class="text-indigo-400 text-[10px] font-black uppercase tracking-[0.4em] mb-3"
                                >
                                    Core Management
                                </h4>
                                <h2
                                    class="text-3xl sm:text-4xl font-black uppercase tracking-tight mb-3"
                                >
                                    Command Center
                                </h2>
                                <p
                                    class="text-slate-400 text-sm sm:text-base max-w-md font-medium mb-6 leading-relaxed"
                                >
                                    Execute audits and monitor traffic from one
                                    central interface with real-time analytics.
                                </p>
                                <Link
                                    :href="route('admin.bookings.index')"
                                    class="inline-flex items-center px-8 py-3 bg-indigo-600 hover:bg-white hover:text-indigo-600 rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-lg"
                                >
                                    Execute Audit
                                </Link>
                            </div>

                            <div
                                class="flex items-center gap-6 px-6 py-5 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-md w-full md:w-auto justify-around"
                            >
                                <div class="text-center">
                                    <p
                                        class="text-indigo-400 text-[10px] font-black uppercase mb-1"
                                    >
                                        Uptime
                                    </p>
                                    <p class="text-xl font-black tabular-nums">
                                        99.9%
                                    </p>
                                </div>
                                <div class="w-px h-10 bg-white/10"></div>
                                <div class="text-center">
                                    <p
                                        class="text-indigo-400 text-[10px] font-black uppercase mb-1"
                                    >
                                        Load
                                    </p>
                                    <p class="text-xl font-black tabular-nums">
                                        0.42ms
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span
                            class="material-icons-outlined absolute -right-10 -bottom-10 text-[200px] text-white/[0.03] rotate-12 pointer-events-none"
                            >terminal</span
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Link
                            v-for="action in quickActions"
                            :key="action.label"
                            :href="route(action.route)"
                            class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col items-center text-center"
                        >
                            <div
                                :class="[
                                    'w-14 h-14 rounded-2xl mb-4 flex items-center justify-center text-white shadow-sm transition-transform group-hover:rotate-6',
                                    action.color,
                                ]"
                            >
                                <span
                                    class="material-icons-outlined text-3xl"
                                    >{{ action.icon }}</span
                                >
                            </div>
                            <span
                                class="text-xs font-black uppercase text-slate-500 group-hover:text-indigo-600 tracking-wide"
                                >{{ action.label }}</span
                            >
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div
                        class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm lg:sticky lg:top-8"
                    >
                        <div class="flex items-center justify-between mb-8">
                            <h4
                                class="text-xs font-black uppercase tracking-widest text-slate-400"
                            >
                                Live Protocols
                            </h4>
                            <span class="flex h-3 w-3 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"
                                ></span>
                                <span
                                    class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"
                                ></span>
                            </span>
                        </div>

                        <div class="space-y-6">
                            <div
                                v-for="log in activities"
                                :key="log.id"
                                class="flex items-center gap-4 group"
                            >
                                <div
                                    :class="[
                                        'w-12 h-12 rounded-xl flex items-center justify-center border border-slate-50 flex-shrink-0 shadow-sm',
                                        log.bg,
                                        log.color,
                                    ]"
                                >
                                    <span
                                        class="material-icons-outlined text-xl"
                                        >{{ log.icon }}</span
                                    >
                                </div>
                                <div class="min-w-0">
                                    <p
                                        class="text-xs font-black text-slate-800 uppercase leading-tight truncate mb-1"
                                    >
                                        {{ log.action }}
                                    </p>
                                    <p
                                        class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"
                                    >
                                        {{ log.user }} • {{ log.time }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button
                            class="w-full mt-8 py-4 border-2 border-dashed border-slate-100 rounded-2xl text-xs font-black uppercase text-slate-400 hover:bg-slate-50 hover:border-indigo-100 hover:text-indigo-400 transition-all"
                        >
                            View Activity History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.stat-card {
    @apply bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300;
}

.animate-fade-in {
    animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@import url("https://fonts.googleapis.com/icon?family=Material+Icons+Outlined");
</style>
