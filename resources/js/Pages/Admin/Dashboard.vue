<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

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

const quickActions = [
    {
        label: t("admin_dashboard.manage_users"),
        icon: "person_outline",
        route: "admin.dashboard",
        color: "bg-blue-600",
    },
    {
        label: t("admin_dashboard.view_reports"),
        icon: "bar_chart",
        route: "admin.dashboard",
        color: "bg-orange-500",
    },
    {
        label: t("admin_dashboard.system_settings"),
        icon: "tune",
        route: "admin.dashboard",
        color: "bg-blue-500",
    },
];
</script>

<template>
    <Head :title="$t('admin_dashboard.dashboard')" />

    <AuthenticatedLayout>
        <div class="px-8 pt-4 flex gap-2 justify-end">
            <button
                @click="locale = 'en'"
                :class="
                    locale === 'en' ? 'bg-blue-600 text-white' : 'bg-slate-200'
                "
                class="px-3 py-1 text-[10px] font-black rounded-lg uppercase"
            >
                EN
            </button>
            <button
                @click="locale = 'or'"
                :class="
                    locale === 'or' ? 'bg-blue-600 text-white' : 'bg-slate-200'
                "
                class="px-3 py-1 text-[10px] font-black rounded-lg uppercase"
            >
                OR
            </button>
            <button
                @click="locale = 'am'"
                :class="
                    locale === 'am' ? 'bg-blue-600 text-white' : 'bg-slate-200'
                "
                class="px-3 py-1 text-[10px] font-black rounded-lg uppercase"
            >
                AM
            </button>
        </div>

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
                        <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                        {{ $t("admin_dashboard.overview") }}
                    </h2>
                </div>
                <div
                    class="flex items-center gap-3 bg-white p-2 pr-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div
                        class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-white animate-pulse"
                    >
                        <span class="material-icons-outlined text-lg"
                            >sensors</span
                        >
                    </div>
                    <div>
                        <p
                            class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1"
                        >
                            {{ $t("admin_dashboard.latency") }}
                        </p>
                        <p class="text-sm font-bold text-slate-700">
                            24ms (Optimal)
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    class="stat-card border-blue-200 bg-gradient-to-br from-blue-50 to-orange-50"
                >
                    <p class="text-blue-700 text-xs font-black uppercase">
                        {{ $t("admin_dashboard.total_users") }}
                    </p>
                    <h3 class="text-3xl font-black text-slate-900">
                        {{ (stats?.total_users ?? 0).toLocaleString() }}
                    </h3>
                </div>
                <div
                    class="stat-card border-orange-200 bg-gradient-to-br from-orange-50 to-blue-50"
                >
                    <p class="text-orange-700 text-xs font-black uppercase">
                        {{ $t("admin_dashboard.total_bookings") }}
                    </p>
                    <h3 class="text-3xl font-black text-slate-900">
                        {{ (stats?.total_bookings ?? 0).toLocaleString() }}
                    </h3>
                </div>
                <Link
                    :href="route('admin.bookings.index')"
                    class="stat-card border-blue-200 bg-gradient-to-br from-blue-50 to-orange-50 group"
                >
                    <p class="text-blue-700 text-xs font-black uppercase">
                        {{ $t("admin_dashboard.pending_verification") }}
                    </p>
                    <h3 class="text-3xl font-black text-slate-900">
                        {{ stats?.pending_bookings ?? 0 }}
                    </h3>
                    <div
                        class="mt-4 h-2 w-full bg-blue-100 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-gradient-to-r from-blue-500 to-orange-500 transition-all"
                            :style="{ width: pendingPercentage + '%' }"
                        ></div>
                    </div>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 space-y-6">
                    <div
                        class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-orange-500 rounded-[2.5rem] p-10 text-white shadow-2xl"
                    >
                        <h2 class="text-4xl font-black uppercase mb-3">
                            {{ $t("admin_dashboard.command_center") }}
                        </h2>
                        <Link
                            :href="route('admin.bookings.index')"
                            class="inline-flex px-8 py-3 bg-orange-500 rounded-xl font-black text-xs uppercase"
                            >{{ $t("admin_dashboard.execute_audit") }}</Link
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <Link
                            v-for="action in quickActions"
                            :key="action.label"
                            :href="route(action.route)"
                            class="p-6 bg-white rounded-3xl border shadow-sm flex flex-col items-center"
                        >
                            <div
                                :class="[
                                    'w-14 h-14 rounded-2xl mb-4 flex items-center justify-center text-white',
                                    action.color,
                                ]"
                            >
                                <span
                                    class="material-icons-outlined text-3xl"
                                    >{{ action.icon }}</span
                                >
                            </div>
                            <span
                                class="text-xs font-black uppercase text-slate-500"
                                >{{ action.label }}</span
                            >
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="bg-white rounded-[2.5rem] p-8 border shadow-sm">
                        <h4
                            class="text-xs font-black uppercase text-slate-400 mb-8"
                        >
                            {{ $t("admin_dashboard.live_protocols") }}
                        </h4>
                        <button
                            class="w-full mt-8 py-4 border-2 border-dashed border-blue-100 rounded-2xl text-xs font-black uppercase text-blue-500 hover:bg-orange-50"
                        >
                            {{ $t("admin_dashboard.view_activity") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.stat-card {
    @apply bg-white p-6 rounded-[2rem] border shadow-sm hover:shadow-lg transition-all;
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
