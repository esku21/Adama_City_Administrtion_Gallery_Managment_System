<script setup>
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import {
    Sparkles,
    ArrowRight,
    Ticket,
    Clock,
    CheckCircle,
    ListOrdered,
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

const getStatusClass = (status) => {
    const s = status?.toLowerCase();

    if (s === "approved" || s === "completed") {
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
        <div class="space-y-6 pb-10">
            <!-- HERO -->
            <div
                class="relative overflow-hidden bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-sm"
            >
                <div
                    class="flex flex-col md:flex-row justify-between items-center gap-8"
                >
                    <div class="max-w-xl text-center md:text-left">
                        <div
                            class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[11px] font-bold mb-4 uppercase tracking-widest border border-indigo-100"
                        >
                            <Sparkles :size="14" />
                            {{ t("dashboard.welcome_back") }}
                        </div>

                        <h2
                            class="text-4xl font-black text-slate-900 tracking-tight mb-2"
                        >
                            {{ t("Hello") }},
                            <span class="text-indigo-600">{{
                                displayName
                            }}</span>
                            👋
                        </h2>

                        <p class="text-slate-600 font-medium mb-6">
                            {{
                                t(
                                    "Explore your booking options and manage your bookings!",
                                )
                            }}
                        </p>

                        <div
                            class="flex gap-3 flex-wrap justify-center md:justify-start"
                        >
                            <Link
                                :href="route('visitor.booking.create')"
                                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-md"
                            >
                                {{ t("dashboard.btn_book") }}
                                <ArrowRight class="inline ml-2" :size="16" />
                            </Link>

                            <Link
                                :href="route('visitor.history')"
                                class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-indigo-100 border border-indigo-100 transition"
                            >
                                {{ t("dashboard.btn_history") }}
                            </Link>
                        </div>
                    </div>

                    <div
                        class="hidden lg:flex bg-indigo-50 p-8 rounded-3xl border border-indigo-100"
                    >
                        <Ticket :size="80" class="text-indigo-600" />
                    </div>
                </div>
            </div>

            <!-- STATS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div
                    class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-amber-50 text-amber-700 rounded-xl">
                            <Clock :size="20" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-bold text-indigo-400 uppercase tracking-widest"
                            >
                                {{ t("Pending Bookings") }}
                            </p>
                            <h3 class="text-2xl font-black text-slate-900">
                                {{ stats.pendingVisits }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-emerald-50 text-emerald-700 rounded-xl"
                        >
                            <CheckCircle :size="20" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-bold text-indigo-400 uppercase tracking-widest"
                            >
                                {{ t("Approved Bookings") }}
                            </p>
                            <h3 class="text-2xl font-black text-slate-900">
                                {{ stats.completedVisits }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-indigo-50 text-indigo-700 rounded-xl"
                        >
                            <ListOrdered :size="20" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-bold text-indigo-400 uppercase tracking-widest"
                            >
                                {{ t("Total Status") }}
                            </p>
                            <h3 class="text-2xl font-black text-slate-900">
                                {{ stats.totalBookings }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-black text-slate-900">
                        {{ t("dashboard.recent_bookings") }}
                    </h3>

                    <Link
                        :href="route('visitor.history')"
                        class="text-indigo-600 text-xs font-bold uppercase tracking-widest hover:text-indigo-800"
                    >
                        {{ t("dashboard.view_all") }} →
                    </Link>
                </div>

                <div
                    v-if="bookings.length"
                    class="bg-white rounded-2xl border border-slate-100 overflow-hidden"
                >
                    <table class="w-full text-left">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase"
                                >
                                    Hall
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-xs font-black text-indigo-700 uppercase"
                                >
                                    Notes
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="booking in bookings"
                                :key="booking.id"
                                class="hover:bg-indigo-50/40 transition"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-semibold text-slate-800 flex items-center gap-2"
                                >
                                    <MapPin
                                        :size="14"
                                        class="text-indigo-400"
                                    />
                                    {{ booking.hall_names }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ booking.booking_date }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            getStatusClass(booking.status),
                                            'px-3 py-1 rounded-full text-xs font-bold border',
                                        ]"
                                    >
                                        {{ booking.status }}
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-4 text-sm text-slate-500 italic"
                                >
                                    {{ booking.admin_feedback || "No notes" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- EMPTY -->
                <div
                    v-else
                    class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-200"
                >
                    <AlertCircle
                        class="mx-auto text-indigo-200 mb-4"
                        :size="40"
                    />

                    <h4 class="font-black text-slate-900 mb-1">
                        No bookings yet
                    </h4>

                    <p class="text-slate-500 text-sm mb-6">
                        Start by creating your first booking.
                    </p>

                    <Link
                        :href="route('visitor.booking.create')"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-xs font-bold uppercase hover:bg-indigo-700"
                    >
                        Create Booking
                    </Link>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>
