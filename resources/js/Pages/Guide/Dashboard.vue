<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    hallName: String,
    stats: Object,
    bookings: {
        type: [Object, Array],
        default: () => [],
    },
});

// Ensures we can handle both paginated data and simple arrays
const bookingList = computed(() => {
    if (Array.isArray(props.bookings)) return props.bookings;
    return props.bookings?.data ?? [];
});

const updateStatus = (id, newStatus) => {
    if (!id) return;

    // Translation-ready confirmation dialog
    if (confirm(t("guide_dashboard.confirm_status", { status: newStatus }))) {
        router.patch(
            route("guide.bookings.update", id),
            { status: newStatus },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Success logic can be added here (e.g., Toast notifications)
                },
            },
        );
    }
};

const getStatusClass = (status) => {
    switch (status?.toLowerCase()) {
        case "arrived":
            return "bg-green-100 text-green-700 border-green-200";
        case "off-schedule":
        case "late":
            return "bg-amber-100 text-amber-700 border-amber-200";
        case "no-show":
        case "missed":
            return "bg-red-100 text-red-700 border-red-200";
        case "pending":
        case "approved":
            return "bg-blue-100 text-blue-700 border-blue-200";
        default:
            return "bg-gray-100 text-gray-700 border-gray-200";
    }
};
</script>

<template>
    <Head :title="$t('guide_nav.management')" />

    <GuideLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row items-center justify-between gap-4"
            >
                <h2
                    class="font-bold text-2xl sm:text-3xl text-gray-800 leading-tight"
                >
                    {{ hallName || $t("guide_nav.no_hall") }} —
                    {{ $t("guide_dashboard.monitor_portal") }}
                </h2>
                <Link
                    :href="route('guide.scanner')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg flex items-center gap-2 active:scale-95"
                >
                    <span>📷</span> {{ $t("guide_dashboard.open_scanner") }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-white p-1 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <div
                            class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-6 rounded-xl text-white relative overflow-hidden h-full"
                        >
                            <div class="relative z-10">
                                <p
                                    class="text-xs font-black uppercase tracking-tighter opacity-80"
                                >
                                    {{ $t("guide_dashboard.total_bookings") }}
                                </p>
                                <p class="text-4xl font-black mt-2">
                                    {{ stats?.total_bookings !== undefined && stats?.total_bookings !== null ? stats.total_bookings : 0 }}
                                </p>
                                <p
                                    class="text-[10px] mt-2 font-medium opacity-70"
                                >
                                    {{ $t("guide_dashboard.total_desc") }}
                                </p>
                            </div>
                            <span
                                class="absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12"
                                >📊</span
                            >
                        </div>
                    </div>

                    <div
                        class="bg-white p-1 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <div
                            class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 rounded-xl text-white relative overflow-hidden h-full"
                        >
                            <div class="relative z-10">
                                <p
                                    class="text-xs font-black uppercase tracking-tighter opacity-80"
                                >
                                    {{ $t("guide_dashboard.arrived_today") }}
                                </p>
                                <p class="text-4xl font-black mt-2">
                                    {{ stats?.arrived_today !== undefined && stats?.arrived_today !== null ? stats.arrived_today : 0 }}
                                </p>
                                <p
                                    class="text-[10px] mt-2 font-medium opacity-70"
                                >
                                    {{ $t("guide_dashboard.arrived_desc") }}
                                </p>
                            </div>
                            <span
                                class="absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12"
                                >✅</span
                            >
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white shadow-xl sm:rounded-3xl border border-gray-100 overflow-hidden"
                >
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3
                                class="font-black text-xl text-gray-800 uppercase tracking-tight"
                            >
                                {{ $t("guide_dashboard.live_log") }}
                            </h3>
                            <span
                                class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-full border border-indigo-100"
                            >
                                {{ $t("guide_dashboard.live_monitoring") }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table
                                class="w-full text-left border-separate border-spacing-y-3"
                            >
                                <thead>
                                    <tr
                                        class="text-gray-400 text-[10px] uppercase font-black tracking-widest"
                                    >
                                        <th class="px-6 py-3">
                                            {{
                                                $t(
                                                    "guide_dashboard.table_visitor",
                                                )
                                            }}
                                        </th>
                                        <th class="px-6 py-3 text-center">
                                            {{
                                                $t("guide_dashboard.table_type")
                                            }}
                                        </th>
                                        <th class="px-6 py-3 text-center">
                                            {{
                                                $t("guide_dashboard.table_slot")
                                            }}
                                        </th>
                                        <th class="px-6 py-3 text-center">
                                            {{
                                                $t(
                                                    "guide_dashboard.table_status",
                                                )
                                            }}
                                        </th>
                                        <th class="px-6 py-3 text-right">
                                            {{
                                                $t(
                                                    "guide_dashboard.table_actions",
                                                )
                                            }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="booking in bookingList"
                                        :key="booking.id"
                                        class="bg-gray-50 hover:bg-gray-100 transition-colors"
                                    >
                                        <td class="px-6 py-4 rounded-l-2xl">
                                            <div
                                                class="font-bold text-gray-900"
                                            >
                                                {{
                                                    booking.user?.firstName && booking.user?.lastName
                                                        ? booking.user.firstName + " " + booking.user.lastName
                                                        : (booking.visitor_name || "Visitor")
                                                }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ booking.user?.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="text-xs font-medium px-2 py-1 bg-white border rounded-lg text-gray-600"
                                            >
                                                {{ booking.visitor_type }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center text-sm font-bold text-gray-600"
                                        >
                                            {{ booking.readable_slot }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                :class="
                                                    getStatusClass(
                                                        booking.status,
                                                    )
                                                "
                                                class="px-3 py-1 rounded-full text-[10px] font-black border uppercase"
                                            >
                                                {{ booking.status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right rounded-r-2xl space-x-2"
                                        >
                                            <button
                                                v-if="
                                                    booking.status === 'approved'
                                                "
                                                @click="
                                                    updateStatus(
                                                        booking.id,
                                                        'arrived',
                                                    )
                                                "
                                                class="text-[10px] font-bold bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors"
                                            >
                                                {{ $t("Arrived") }}
                                            </button>
                                            <button
                                                v-if="
                                                    booking.status ===
                                                        'pending' ||
                                                    booking.status ===
                                                        'approved'
                                                "
                                                @click="
                                                    updateStatus(
                                                        booking.id,
                                                        'no-show',
                                                    )
                                                "
                                                class="text-[10px] font-bold bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors"
                                            >
                                                {{ $t("NoShow") }}
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="bookingList.length === 0">
                                        <td
                                            colspan="5"
                                            class="px-6 py-10 text-center text-gray-400 italic"
                                        >
                                            {{
                                                $t("guide_dashboard.empty_log")
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuideLayout>
</template>
