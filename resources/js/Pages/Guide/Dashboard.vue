<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    hallName: String,
    stats: Object,
    bookings: {
        type: [Object, Array],
        default: () => [],
    },
});

/**
 * Normalizes the bookings data.
 */
const bookingList = computed(() => {
    if (Array.isArray(props.bookings)) {
        return props.bookings;
    }
    return props.bookings?.data ?? [];
});

/**
 * Sends the status update to the GuideController
 */
const updateStatus = (id, newStatus) => {
    if (!id) return;

    // We use a confirmation dialog for safety
    if (confirm(`Mark this visitor as ${newStatus}?`)) {
        router.patch(
            route("guide.bookings.update", id),
            { status: newStatus },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Success logic here if needed
                },
            },
        );
    }
};

/**
 * Status Badge Styling
 */
const getStatusClass = (status) => {
    const s = status?.toLowerCase();
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
    <Head title="Guide Dashboard" />

    <GuideLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ hallName || "Station" }} — Monitor Portal
                </h2>
                <Link
                    :href="route('guide.scanner')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2"
                >
                    <span>📷</span> Open Scanner
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <p
                            class="text-xs font-black text-gray-400 uppercase tracking-widest"
                        >
                            Total Hall Bookings
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ stats?.total_bookings ?? 0 }}
                        </p>
                    </div>

                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <p
                            class="text-xs font-black text-amber-500 uppercase tracking-widest"
                        >
                            Pending Today
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ stats?.pending_today ?? 0 }}
                        </p>
                    </div>

                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
                    >
                        <p
                            class="text-xs font-black text-green-500 uppercase tracking-widest"
                        >
                            Already Arrived
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ stats?.arrived_today ?? 0 }}
                        </p>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100"
                >
                    <div class="p-6 text-gray-900">
                        <h3 class="font-bold text-lg mb-4">Visitor Log</h3>

                        <div class="overflow-x-auto">
                            <table
                                class="w-full text-left border-separate border-spacing-y-2"
                            >
                                <thead>
                                    <tr
                                        class="text-gray-400 text-xs uppercase font-black tracking-widest"
                                    >
                                        <th class="px-4 py-3">
                                            Visitor Details
                                        </th>
                                        <th class="px-4 py-3 text-center">
                                            Slot
                                        </th>
                                        <th class="px-4 py-3 text-center">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-right">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="booking in bookingList"
                                        :key="booking.id"
                                        class="bg-gray-50 hover:bg-gray-100 transition-colors group"
                                    >
                                        <td class="px-4 py-4">
                                            <div
                                                class="font-bold text-gray-800"
                                            >
                                                {{
                                                    booking.visitor_name ||
                                                    "Unnamed Visitor"
                                                }}
                                            </div>
                                            <div
                                                class="text-[10px] text-gray-500 font-medium uppercase mt-0.5"
                                            >
                                                {{
                                                    booking.hall_names ||
                                                    "General"
                                                }}
                                                •
                                                {{
                                                    booking.visitor_type ||
                                                    "Standard"
                                                }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="bg-white border border-gray-200 px-2 py-1 rounded text-xs font-bold shadow-sm"
                                            >
                                                {{
                                                    booking.readable_slot ||
                                                    "N/A"
                                                }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                :class="
                                                    getStatusClass(
                                                        booking.status,
                                                    )
                                                "
                                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase border"
                                            >
                                                {{ booking.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div
                                                v-if="
                                                    [
                                                        'pending',
                                                        'approved',
                                                        'Approved',
                                                    ].includes(booking.status)
                                                "
                                                class="flex justify-end gap-2"
                                            >
                                                <button
                                                    @click="
                                                        updateStatus(
                                                            booking.id,
                                                            'Arrived',
                                                        )
                                                    "
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all active:scale-95"
                                                >
                                                    Arrived
                                                </button>

                                                <button
                                                    @click="
                                                        updateStatus(
                                                            booking.id,
                                                            'Late',
                                                        )
                                                    "
                                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all active:scale-95"
                                                >
                                                    Late
                                                </button>

                                                <button
                                                    @click="
                                                        updateStatus(
                                                            booking.id,
                                                            'Missed',
                                                        )
                                                    "
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all active:scale-95"
                                                >
                                                    Missed
                                                </button>
                                            </div>
                                            <div
                                                v-else
                                                class="text-xs text-gray-400 italic"
                                            >
                                                Marked
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-if="bookingList.length === 0">
                                        <td
                                            colspan="4"
                                            class="px-4 py-20 text-center text-gray-400 font-medium"
                                        >
                                            No bookings found for your station
                                            today.
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
