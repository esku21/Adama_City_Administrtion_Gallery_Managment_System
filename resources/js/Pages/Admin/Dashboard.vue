<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_visitors: 0,
            pending_bookings: 0,
            total_users: 0,
        }),
    },
});

// Mock data for the new activity section
const activities = [
    {
        id: 1,
        user: "Admin",
        action: "Updated System Protocol",
        time: "2 mins ago",
        icon: "settings_suggest",
    },
    {
        id: 2,
        user: "System",
        action: "New Booking Received",
        time: "15 mins ago",
        icon: "add_chart",
    },
    {
        id: 3,
        user: "Guide_04",
        action: "Profile Verified",
        time: "1 hour ago",
        icon: "verified_user",
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />
    <AuthenticatedLayout>
        <div
            class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6"
        >
            <div class="animate-in fade-in slide-in-from-left duration-700">
                <h2
                    class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none"
                >
                    Overview
                </h2>
                <p
                    class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-3"
                >
                    System Health & Real-time Statistics
                </p>
            </div>

            <div
                class="flex gap-3 animate-in fade-in slide-in-from-right duration-700"
            >
                <div
                    class="px-5 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-3 shadow-sm"
                >
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"
                        ></span>
                    </span>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-slate-500"
                        >Protocol: Optimal</span
                    >
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div
                class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden group"
            >
                <div class="flex justify-between items-start mb-6">
                    <div
                        class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600"
                    >
                        <span class="material-icons-outlined">insights</span>
                    </div>
                    <div
                        class="flex items-center gap-1 text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg"
                    >
                        <span class="material-icons-outlined text-xs"
                            >trending_up</span
                        >
                        <span class="text-[9px] font-black uppercase"
                            >Live</span
                        >
                    </div>
                </div>
                <p
                    class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1"
                >
                    Total Visitors
                </p>
                <h3 class="text-5xl font-black text-slate-900 tracking-tighter">
                    {{ (stats?.total_visitors ?? 0).toLocaleString() }}
                </h3>
            </div>

            <Link
                :href="route('admin.bookings.index')"
                class="bg-white p-8 rounded-[2.5rem] border border-amber-200 shadow-sm hover:shadow-xl hover:shadow-amber-100/20 hover:-translate-y-1 transition-all duration-500 group relative overflow-hidden"
            >
                <div class="flex justify-between items-start mb-6">
                    <div
                        class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500"
                    >
                        <span class="material-icons-outlined"
                            >notification_important</span
                        >
                    </div>
                    <span
                        class="material-icons-outlined text-amber-300 group-hover:translate-x-1 transition-transform"
                        >arrow_forward</span
                    >
                </div>
                <p
                    class="text-amber-600 text-[10px] font-black uppercase tracking-widest mb-1"
                >
                    Action Required
                </p>
                <h3 class="text-5xl font-black text-slate-900 tracking-tighter">
                    {{ stats?.pending_bookings ?? 0 }}
                </h3>
                <div
                    class="mt-4 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden"
                >
                    <div
                        class="h-full bg-amber-500 rounded-full"
                        :style="{
                            width: stats?.pending_bookings > 0 ? '65%' : '0%',
                        }"
                    ></div>
                </div>
            </Link>

            <div
                class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden group"
            >
                <div class="flex justify-between items-start mb-6">
                    <div
                        class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600"
                    >
                        <span class="material-icons-outlined">groups</span>
                    </div>
                    <span
                        class="text-slate-400 text-[9px] font-black uppercase bg-slate-100 px-2 py-1 rounded-lg"
                        >Verified</span
                    >
                </div>
                <p
                    class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1"
                >
                    Active Guides
                </p>
                <h3 class="text-5xl font-black text-slate-900 tracking-tighter">
                    {{ (stats?.total_users ?? 0).toLocaleString() }}
                </h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div
                class="lg:col-span-2 bg-slate-950 rounded-[3.5rem] p-10 text-white relative overflow-hidden shadow-2xl border border-slate-800"
            >
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <span
                            class="w-10 h-1 bg-indigo-500 rounded-full"
                        ></span>
                        <h2
                            class="text-3xl font-black uppercase tracking-tighter text-nowrap"
                        >
                            Command Center
                        </h2>
                    </div>
                    <p
                        class="text-slate-400 text-base leading-relaxed mb-10 max-w-lg"
                    >
                        Welcome to the primary administrative hub. From here,
                        you can intercept booking requests, audit guide
                        performance, and regulate system-wide protocols.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link
                            :href="route('admin.bookings.index')"
                            class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-500 transition-all"
                        >
                            Execute Audit
                        </Link>
                        <div
                            class="flex items-center gap-6 px-6 py-4 bg-white/5 border border-white/10 rounded-2xl"
                        >
                            <div>
                                <p
                                    class="text-indigo-400 text-[9px] font-black uppercase tracking-widest"
                                >
                                    Uptime
                                </p>
                                <p class="text-xl font-black text-white">
                                    99.9%
                                </p>
                            </div>
                            <div class="w-[1px] h-8 bg-white/10"></div>
                            <div>
                                <p
                                    class="text-indigo-400 text-[9px] font-black uppercase tracking-widest"
                                >
                                    Load
                                </p>
                                <p class="text-xl font-black text-white">
                                    0.42ms
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <span
                    class="material-icons-outlined absolute -right-12 -bottom-12 text-[250px] text-white/[0.03] rotate-12 pointer-events-none"
                >
                    terminal
                </span>
            </div>

            <div
                class="bg-white border border-slate-200 rounded-[3rem] p-8 shadow-sm"
            >
                <h4
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6"
                >
                    System Logs
                </h4>
                <div class="space-y-6">
                    <div
                        v-for="log in activities"
                        :key="log.id"
                        class="flex items-center gap-4"
                    >
                        <div
                            class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400"
                        >
                            <span class="material-icons-outlined text-lg">{{
                                log.icon
                            }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-[11px] font-black text-slate-800 truncate"
                            >
                                {{ log.action }}
                            </p>
                            <p
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-tight"
                            >
                                {{ log.user }} • {{ log.time }}
                            </p>
                        </div>
                    </div>
                </div>
                <button
                    class="w-full mt-8 py-3 border border-slate-100 rounded-xl text-[9px] font-black uppercase text-slate-400 hover:bg-slate-50 transition-all"
                >
                    View All Logs
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-in {
    animation-fill-mode: both;
}
@keyframes slide-in-from-left {
    0% {
        transform: translateX(-20px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}
@keyframes slide-in-from-right {
    0% {
        transform: translateX(20px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}
.fade-in {
    animation: fadeIn 0.8s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
