<script setup>
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import {
    Sparkles,
    ArrowRight,
    Ticket,
    Clock,
    CheckCircle,
    MapPin,
    AlertCircle,
} from "lucide-vue-next";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    stats: Object,
    bookings: Array,
});

const page = usePage();

const displayName = computed(() => {
    return (
        page.props.auth.user?.firstName ||
        page.props.auth.user?.name ||
        "Visitor"
    );
});

// Helper functions to sanitize raw backend datetime strings (e.g., 2026-05-14T00:00:00:000000Z)
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    // Splitting by the standard 'T' separator to fetch just the standard date part
    if (dateString.includes("T")) {
        return dateString.split("T")[0];
    }
    // Fallback split fallback rule to clean out accidental trailing sub-seconds or custom time parameters
    return dateString.substring(0, 10);
};

const getStatusClass = (status) => {
    const s = status?.toLowerCase();

    if (s === "approved" || s === "completed" || s === "arrived") {
        return "bg-emerald-50 text-emerald-700 border-emerald-100";
    }

    if (s === "pending") {
        return "bg-amber-50 text-amber-700 border-amber-100";
    }

    return "bg-indigo-50 text-indigo-700 border-indigo-100";
};
</script>

<template>
    <Head :title="t('nav.dashboard')" />

    <VisitorLayout>
        <div
            class="space-y-6 pb-10 px-2 sm:px-4 md:px-6 lg:px-8 max-w-7xl mx-auto w-full box-border overflow-hidden"
        >
            <div
                class="relative overflow-hidden bg-white rounded-3xl p-4 sm:p-6 lg:p-10 border border-slate-100 shadow-sm"
            >
                <div
                    class="flex flex-col lg:flex-row justify-between items-center gap-6 lg:gap-8"
                >
                    <div class="w-full max-w-xl text-center lg:text-left">
                        <div
                            class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[11px] font-bold mb-4 uppercase tracking-widest border border-indigo-100"
                        >
                            <Sparkles :size="14" />
                            {{ t("dashboard.welcome_back") }}
                        </div>

                        <h2
                            class="text-xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-2 break-words"
                        >
                            {{ t("Hello") }},
                            <span class="text-indigo-600">{{
                                displayName
                            }}</span>
                            👋
                        </h2>

                        <p
                            class="text-xs sm:text-base text-slate-600 font-medium mb-6"
                        >
                            {{
                                t(
                                    "Explore your booking options and manage your bookings!",
                                )
                            }}
                        </p>

                        <div
                            class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start"
                        >
                            <Link
                                :href="route('visitor.booking.create')"
                                class="bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-md flex items-center justify-center gap-2 w-full sm:w-auto"
                            >
                                {{ t("dashboard.btn_book") }}
                                <ArrowRight :size="16" />
                            </Link>

                            <Link
                                :href="route('visitor.history')"
                                class="bg-indigo-50 text-indigo-700 px-5 py-3 rounded-xl font-bold text-sm hover:bg-indigo-100 border border-indigo-100 transition text-center w-full sm:w-auto"
                            >
                                {{ t("dashboard.btn_history") }}
                            </Link>
                        </div>
                    </div>

                    <div
                        class="hidden lg:flex bg-indigo-50 p-6 xl:p-8 rounded-3xl border border-indigo-100 items-center justify-center shrink-0"
                    >
                        <Ticket :size="70" class="text-indigo-600" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-amber-50 text-amber-700 rounded-xl shrink-0"
                        >
                            <Clock :size="20" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-xs font-bold text-indigo-400 uppercase tracking-widest truncate"
                            >
                                {{ t("Pending Bookings") }}
                            </p>
                            <h3
                                class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5"
                            >
                                {{ stats?.pendingVisits ?? 0 }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-emerald-50 text-emerald-700 rounded-xl shrink-0"
                        >
                            <CheckCircle :size="20" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-xs font-bold text-indigo-400 uppercase tracking-widest truncate"
                            >
                                {{ t("Approved Bookings") }}
                            </p>
                            <h3
                                class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5"
                            >
                                {{ stats?.completedVisits ?? 0 }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-3 px-1">
                    <h3 class="text-sm sm:text-lg font-black text-slate-900">
                        {{ t("dashboard.recent_bookings") }}
                    </h3>

                    <Link
                        :href="route('visitor.history')"
                        class="text-indigo-600 text-xs font-bold uppercase tracking-widest hover:text-indigo-800 transition"
                    >
                        {{ t("dashboard.view_all") }} →
                    </Link>
                </div>

                <div
                    v-if="bookings && bookings.length"
                    class="bg-white rounded-2xl border border-slate-100 overflow-x-auto shadow-sm w-full"
                >
                    <table
                        class="w-full text-left min-w-[600px] border-collapse"
                    >
                        <thead class="bg-indigo-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase tracking-wider w-1/4"
                                >
                                    Hall
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase tracking-wider w-1/4"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase tracking-wider w-1/4"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase tracking-wider w-1/4"
                                >
                                    Notes
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr
                                v-for="booking in bookings"
                                :key="booking.id"
                                class="hover:bg-indigo-50/30 transition-colors"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-slate-800 whitespace-nowrap"
                                >
                                    <div class="flex items-center gap-2">
                                        <MapPin
                                            :size="14"
                                            class="text-indigo-400 shrink-0"
                                        />
                                        <span class="truncate max-w-[180px]">
                                            {{ booking.hall_names || "N/A" }}
                                        </span>
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap"
                                >
                                    {{ formatDate(booking.booking_date) }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            getStatusClass(booking.status),
                                            'inline-block px-3 py-1 rounded-full text-xs font-bold border capitalize',
                                        ]"
                                    >
                                        {{ booking.status }}
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-4 text-sm text-slate-500 italic max-w-[200px] truncate whitespace-nowrap"
                                >
                                    {{ booking.admin_feedback || "No notes" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="bg-white rounded-3xl p-8 sm:p-12 text-center border border-dashed border-slate-200 shadow-sm"
                >
                    <AlertCircle
                        class="mx-auto text-indigo-200 mb-4"
                        :size="40"
                    />

                    <h4
                        class="font-black text-slate-900 mb-1 text-base sm:text-lg"
                    >
                        No bookings yet
                    </h4>

                    <p
                        class="text-slate-500 text-xs sm:text-sm mb-6 max-w-sm mx-auto"
                    >
                        Start by creating your first booking options inside the
                        portal dashboard profile.
                    </p>

                    <Link
                        :href="route('visitor.booking.create')"
                        class="inline-block bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold uppercase hover:bg-indigo-700 transition"
                    >
                        Create Booking
                    </Link>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>
