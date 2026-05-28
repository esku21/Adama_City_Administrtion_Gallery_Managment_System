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

const bookingList = computed(() => {
    if (Array.isArray(props.bookings)) return props.bookings;
    return props.bookings?.data ?? [];
});

const updateStatus = (id, newStatus) => {
    if (!id) return;
    if (confirm(t("guide_dashboard.confirm_status", { status: newStatus }))) {
        router.patch(
            route("guide.bookings.update", id),
            { status: newStatus },
            { preserveScroll: true },
        );
    }
};

const getStatusClass = (status) => {
    const s = status?.toLowerCase() || "";
    switch (s) {
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
    <Head :title="t('guide_nav.management')" />

    <GuideLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row items-center justify-between gap-4"
            >
                <h2
                    class="font-bold text-2xl sm:text-3xl text-gray-800 leading-tight"
                >
                    {{ hallName || t("guide_nav.no_hall") }} —
                    {{ t("guide_dashboard.monitor_portal") }}
                </h2>
                <Link
                    :href="route('guide.scanner')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg flex items-center gap-2"
                >
                    <span>📷</span> {{ t("guide_dashboard.open_scanner") }}
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
                            <p
                                class="text-xs font-black uppercase tracking-tighter opacity-80"
                            >
                                {{ t("guide_dashboard.total_bookings") }}
                            </p>
                            <p class="text-4xl font-black mt-2">
                                {{ stats?.total_bookings ?? 0 }}
                            </p>
                            <p class="text-[10px] mt-2 font-medium opacity-70">
                                {{ t("guide_dashboard.total_desc") }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="bg-white p-1 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <div
                            class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 rounded-xl text-white relative overflow-hidden h-full"
                        >
                            <p
                                class="text-xs font-black uppercase tracking-tighter opacity-80"
                            >
                                {{ t("guide_dashboard.arrived_today") }}
                            </p>
                            <p class="text-4xl font-black mt-2">
                                {{ stats?.arrived_today ?? 0 }}
                            </p>
                            <p class="text-[10px] mt-2 font-medium opacity-70">
                                {{ t("guide_dashboard.arrived_desc") }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white shadow-xl sm:rounded-3xl border border-gray-100 overflow-hidden"
                >
                    <div class="p-8">
                        <h3
                            class="font-black text-xl text-gray-800 uppercase tracking-tight mb-6"
                        >
                            {{ t("guide_dashboard.live_log") }}
                        </h3>
                        <table
                            class="w-full text-left border-separate border-spacing-y-3"
                        >
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] uppercase font-black tracking-widest"
                                >
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3 text-center">
                                        {{ t("guide_dashboard.table_type") }}
                                    </th>
                                    <th class="px-6 py-3 text-center">
                                        {{ t("guide_dashboard.table_slot") }}
                                    </th>
                                    <th class="px-6 py-3 text-center">Date</th>
                                    <th class="px-6 py-3 text-center">
                                        {{ t("guide_dashboard.table_status") }}
                                    </th>
                                    <th class="px-6 py-3 text-right">
                                        {{ t("guide_dashboard.table_actions") }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="booking in bookingList"
                                    :key="booking.id"
                                    class="bg-gray-50 hover:bg-gray-100"
                                >
                                    <td
                                        class="px-6 py-4 rounded-l-2xl font-bold"
                                    >
                                        {{
                                            booking.user?.firstName
                                                ? booking.user.firstName +
                                                  " " +
                                                  booking.user.lastName
                                                : booking.visitor_name
                                        }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs">
                                        {{ booking.visitor_type }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center text-sm font-bold"
                                    >
                                        {{ booking.readable_slot }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center text-sm font-bold"
                                    >
                                        {{ booking.booking_date }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            :class="
                                                getStatusClass(booking.status)
                                            "
                                            class="px-3 py-1 rounded-full text-[10px] font-black border uppercase"
                                        >
                                            {{
                                                t(
                                                    `statuses.${booking.status.toLowerCase()}`,
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right rounded-r-2xl space-x-2"
                                    >
                                        <button
                                            v-if="booking.status === 'approved'"
                                            @click="
                                                updateStatus(
                                                    booking.id,
                                                    'arrived',
                                                )
                                            "
                                            class="text-[10px] font-bold bg-emerald-600 text-white px-3 py-1.5 rounded-lg"
                                        >
                                            {{
                                                t("guide_dashboard.btn_arrived")
                                            }}
                                        </button>
                                        <button
                                            v-if="
                                                [
                                                    'pending',
                                                    'approved',
                                                ].includes(booking.status)
                                            "
                                            @click="
                                                updateStatus(
                                                    booking.id,
                                                    'no-show',
                                                )
                                            "
                                            class="text-[10px] font-bold bg-red-50 text-red-600 px-3 py-1.5 rounded-lg"
                                        >
                                            {{
                                                t("guide_dashboard.btn_no_show")
                                            }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </GuideLayout>
</template>
