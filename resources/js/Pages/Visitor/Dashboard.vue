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
    Globe,
} from "lucide-vue-next";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t, te, locale } = useI18n();

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

// Change application locale dynamically
const changeLanguage = (lang) => {
    locale.value = lang;
    // Optional: Save to localStorage if your app setup reads configuration from it
    localStorage.setItem("locale", lang);
};

// Helper functions to sanitize raw backend datetime strings
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    if (dateString.includes("T")) {
        return dateString.split("T")[0];
    }
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
    <Head
        :title="
            te('visitor_dashboard.nav_title')
                ? t('visitor_dashboard.nav_title')
                : 'Dashboard'
        "
    />

    <VisitorLayout>
        <div
            class="space-y-6 pb-10 px-3 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full box-border overflow-hidden"
        >
            <div
                class="relative overflow-hidden bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-10 border border-slate-100 shadow-sm"
            >
                <nav
                    class="mb-6 lg:mb-0 lg:absolute lg:top-8 lg:right-8 flex items-center justify-center lg:justify-end gap-1 bg-slate-50 p-1.5 rounded-xl border border-slate-200/60 z-10"
                >
                    <div class="px-2 text-slate-400 flex items-center gap-1.5">
                        <Globe :size="15" class="text-slate-400" />
                    </div>

                    <button
                        @click="changeLanguage('or')"
                        :class="[
                            locale === 'or'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-slate-200/50',
                            'px-2.5 py-1 text-xs font-black rounded-lg transition-all tracking-wider',
                        ]"
                    >
                        OR
                    </button>

                    <button
                        @click="changeLanguage('am')"
                        :class="[
                            locale === 'am'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-slate-200/50',
                            'px-2.5 py-1 text-xs font-black rounded-lg transition-all tracking-wider',
                        ]"
                    >
                        AM
                    </button>

                    <button
                        @click="changeLanguage('en')"
                        :class="[
                            locale === 'en'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-slate-200/50',
                            'px-2.5 py-1 text-xs font-black rounded-lg transition-all tracking-wider',
                        ]"
                    >
                        EN
                    </button>
                </nav>

                <div
                    class="flex flex-col lg:flex-row justify-between items-center gap-6"
                >
                    <div class="w-full text-center lg:text-left">
                        <div
                            class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[10px] sm:text-[11px] font-bold mb-3 sm:mb-4 uppercase tracking-widest border border-indigo-100"
                        >
                            <Sparkles :size="12" />
                            {{
                                te("visitor_dashboard.welcome_back")
                                    ? t("visitor_dashboard.welcome_back")
                                    : "Welcome Back"
                            }}
                        </div>

                        <h2
                            class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-2 break-words"
                        >
                            {{
                                te("visitor_dashboard.hello")
                                    ? t("visitor_dashboard.hello")
                                    : "Hello"
                            }},
                            <span class="text-indigo-600 block sm:inline">{{
                                displayName
                            }}</span>
                            👋
                        </h2>

                        <p
                            class="text-xs sm:text-sm md:text-base text-slate-600 font-medium mb-6 max-w-lg mx-auto lg:mx-0"
                        >
                            {{
                                te("visitor_dashboard.explore_desc")
                                    ? t("visitor_dashboard.explore_desc")
                                    : "Explore your booking options and manage your bookings!"
                            }}
                        </p>

                        <div
                            class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start"
                        >
                            <Link
                                :href="route('visitor.booking.create')"
                                class="bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-md flex items-center justify-center gap-2 w-full sm:w-auto"
                            >
                                {{
                                    te("visitor_dashboard.btn_book")
                                        ? t("visitor_dashboard.btn_book")
                                        : "New Booking"
                                }}
                                <ArrowRight :size="16" />
                            </Link>

                            <Link
                                :href="route('visitor.history')"
                                class="bg-indigo-50 text-indigo-700 px-5 py-3 rounded-xl font-bold text-sm hover:bg-indigo-100 border border-indigo-100 transition text-center w-full sm:w-auto"
                            >
                                {{
                                    te("visitor_dashboard.btn_history")
                                        ? t("visitor_dashboard.btn_history")
                                        : "Booking History"
                                }}
                            </Link>
                        </div>
                    </div>

                    <div
                        class="hidden lg:flex bg-indigo-50 p-6 xl:p-8 rounded-3xl border border-indigo-100 items-center justify-center shrink-0"
                    >
                        <Ticket :size="64" class="text-indigo-600" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-amber-50 text-amber-700 rounded-xl shrink-0"
                        >
                            <Clock :size="20" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-widest truncate"
                            >
                                {{
                                    te("visitor_dashboard.pending_bookings")
                                        ? t(
                                              "visitor_dashboard.pending_bookings",
                                          )
                                        : "Pending Bookings"
                                }}
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
                    class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-3 bg-emerald-50 text-emerald-700 rounded-xl shrink-0"
                        >
                            <CheckCircle :size="20" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-widest truncate"
                            >
                                {{
                                    te("visitor_dashboard.approved_bookings")
                                        ? t(
                                              "visitor_dashboard.approved_bookings",
                                          )
                                        : "Approved Bookings"
                                }}
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
                    <h3 class="text-sm sm:text-base font-black text-slate-900">
                        {{
                            te("visitor_dashboard.recent_bookings")
                                ? t("visitor_dashboard.recent_bookings")
                                : "Recent Bookings"
                        }}
                    </h3>

                    <Link
                        :href="route('visitor.history')"
                        class="text-indigo-600 text-xs font-bold uppercase tracking-widest hover:text-indigo-800 transition"
                    >
                        {{
                            te("visitor_dashboard.view_all")
                                ? t("visitor_dashboard.view_all")
                                : "View All"
                        }}
                        →
                    </Link>
                </div>

                <div
                    v-if="bookings && bookings.length"
                    class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm w-full"
                >
                    <div class="overflow-x-auto w-full block scrollbar-thin">
                        <table
                            class="w-full text-left min-w-[650px] border-collapse table-fixed"
                        >
                            <thead class="bg-indigo-50/70">
                                <tr>
                                    <th
                                        class="px-6 py-3.5 text-[11px] font-black text-indigo-700 uppercase tracking-wider w-[30%]"
                                    >
                                        {{
                                            te("visitor_dashboard.table_hall")
                                                ? t(
                                                      "visitor_dashboard.table_hall",
                                                  )
                                                : "Hall"
                                        }}
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-[11px] font-black text-indigo-700 uppercase tracking-wider w-[20%]"
                                    >
                                        {{
                                            te("visitor_dashboard.table_date")
                                                ? t(
                                                      "visitor_dashboard.table_date",
                                                  )
                                                : "Date"
                                        }}
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-[11px] font-black text-indigo-700 uppercase tracking-wider w-[20%]"
                                    >
                                        {{
                                            te("visitor_dashboard.table_status")
                                                ? t(
                                                      "visitor_dashboard.table_status",
                                                  )
                                                : "Status"
                                        }}
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-[11px] font-black text-indigo-700 uppercase tracking-wider w-[30%]"
                                    >
                                        {{
                                            te("visitor_dashboard.table_notes")
                                                ? t(
                                                      "visitor_dashboard.table_notes",
                                                  )
                                                : "Notes"
                                        }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr
                                    v-for="booking in bookings"
                                    :key="booking.id"
                                    class="hover:bg-indigo-50/20 transition-colors"
                                >
                                    <td
                                        class="px-6 py-4 text-sm font-semibold text-slate-800 whitespace-nowrap overflow-hidden text-ellipsis"
                                    >
                                        <div class="flex items-center gap-2">
                                            <MapPin
                                                :size="14"
                                                class="text-indigo-400 shrink-0"
                                            />
                                            <span
                                                class="truncate max-w-[180px]"
                                            >
                                                {{
                                                    booking.hall_names || "N/A"
                                                }}
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
                                                'inline-block px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize',
                                            ]"
                                        >
                                            {{ booking.status }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-4 text-sm text-slate-500 italic whitespace-nowrap overflow-hidden text-ellipsis"
                                    >
                                        {{
                                            booking.admin_feedback || "No notes"
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-else
                    class="bg-white rounded-2xl p-6 sm:p-12 text-center border border-dashed border-slate-200 shadow-sm"
                >
                    <AlertCircle
                        class="mx-auto text-indigo-200 mb-3"
                        :size="40"
                    />
                    <h4
                        class="font-black text-slate-900 mb-1 text-sm sm:text-base"
                    >
                        {{
                            te("visitor_dashboard.no_bookings_found")
                                ? t("visitor_dashboard.no_bookings_found")
                                : "No bookings found"
                        }}
                    </h4>
                    <p class="text-slate-500 text-xs mb-5 max-w-xs mx-auto">
                        {{
                            te("visitor_dashboard.no_bookings_desc")
                                ? t("visitor_dashboard.no_bookings_desc")
                                : "You have not scheduled any exhibition site gallery appointments yet."
                        }}
                    </p>
                    <Link
                        :href="route('visitor.booking.create')"
                        class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-xl text-xs font-bold uppercase hover:bg-indigo-700 transition shadow-sm"
                    >
                        {{
                            te("visitor_dashboard.create_booking")
                                ? t("visitor_dashboard.create_booking")
                                : "Create Booking"
                        }}
                    </Link>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>
