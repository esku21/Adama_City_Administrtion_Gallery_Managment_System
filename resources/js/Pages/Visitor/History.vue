<script setup>
import { computed, ref } from "vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const page = usePage();

// Data from Controller
const bookings = computed(() => page.props.bookings || []);
const feedbacks = computed(() => page.props.feedbacks || []);

// State Management
const activeTab = ref("bookings");
const showQrModal = ref(false);
const selectedQrUrl = ref("");
const selectedBooking = ref(null);

const openQrModal = (booking) => {
    const qrData = booking.qr_token || `BOOKING-${booking.id}`;
    selectedQrUrl.value = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData)}`;
    selectedBooking.value = booking;
    showQrModal.value = true;
};

const downloadTicket = (id) => {
    if (!id) return;
    window.location.href = route("visitor.booking.download", id);
};

const deleteBooking = (id) => {
    Swal.fire({
        title: t("history.swal_title"),
        text: t("history.swal_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: t("history.swal_confirm"),
        cancelButtonText: t("bookings.btn_cancel"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("visitor.booking.destroy", id), {
                preserveScroll: true,
            });
        }
    });
};

const getHallName = (booking) => {
    return booking.hall?.name || t("history.general_access");
};

const getStatusClass = (status) => {
    const s = status?.toString().toLowerCase().trim();
    if (s === "approved" || s === "completed")
        return "bg-emerald-100 text-emerald-700 border border-emerald-200";
    if (s === "pending")
        return "bg-amber-100 text-amber-700 border border-amber-200";
    if (s === "cancelled" || s === "rejected")
        return "bg-rose-100 text-rose-700 border border-rose-200";
    return "bg-slate-100 text-slate-700 border border-slate-200";
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>

<template>
    <Head :title="t('nav.history')" />

    <VisitorLayout>
        <template #header>{{ t("nav.history") }}</template>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 animate-in">
            <div
                class="flex border-b border-slate-200 mb-6 sm:mb-8 gap-4 sm:gap-8 overflow-x-auto no-scrollbar"
            >
                <button
                    @click="activeTab = 'bookings'"
                    :class="
                        activeTab === 'bookings'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-slate-400'
                    "
                    class="pb-4 px-2 font-black text-[10px] whitespace-nowrap uppercase tracking-[0.2em] border-b-4 transition-all"
                >
                    {{ t("nav.bookings") }} ({{ bookings.length }})
                </button>
                <button
                    @click="activeTab = 'feedback'"
                    :class="
                        activeTab === 'feedback'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-slate-400'
                    "
                    class="pb-4 px-2 font-black text-[10px] whitespace-nowrap uppercase tracking-[0.2em] border-b-4 transition-all"
                >
                    {{ t("nav.feedback") }} ({{ feedbacks.length }})
                </button>
            </div>

            <div v-if="activeTab === 'bookings'">
                <div
                    v-if="bookings.length === 0"
                    class="bg-white rounded-[2rem] p-20 text-center text-slate-400 font-bold uppercase text-xs border border-slate-100"
                >
                    {{ t("dashboard.empty_title") }}
                </div>

                <div v-else>
                    <div class="grid grid-cols-1 gap-4 lg:hidden">
                        <div
                            v-for="booking in bookings"
                            :key="booking.id"
                            class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm"
                        >
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div
                                        class="font-black text-slate-700 text-sm"
                                    >
                                        {{ formatDate(booking.booking_date) }}
                                    </div>
                                    <div
                                        class="text-[10px] text-indigo-500 font-bold uppercase"
                                    >
                                        {{ booking.visitor_type }}
                                    </div>
                                </div>
                                <span
                                    :class="getStatusClass(booking.status)"
                                    class="text-[8px] font-black px-2 py-1 rounded-full uppercase"
                                >
                                    {{ booking.status }}
                                </span>
                            </div>

                            <div
                                class="flex items-center gap-4 py-3 border-y border-slate-50 mb-4"
                            >
                                <div
                                    v-if="
                                        booking.status?.toLowerCase() !==
                                            'rejected' && booking.qr_token
                                    "
                                    @click="openQrModal(booking)"
                                    class="flex-shrink-0 cursor-pointer"
                                >
                                    <img
                                        :src="`https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=${encodeURIComponent(booking.qr_token)}`"
                                        class="w-10 h-10 rounded-lg border border-slate-100"
                                    />
                                </div>
                                <div
                                    class="text-[11px] text-slate-600 font-bold"
                                >
                                    <span
                                        class="block text-[8px] uppercase text-slate-400 mb-0.5"
                                    >
                                        {{ t("dashboard.table_hall") }}
                                    </span>
                                    {{ getHallName(booking) }}
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button
                                    v-if="
                                        ['approved', 'pending'].includes(
                                            booking.status?.toLowerCase(),
                                        )
                                    "
                                    @click="downloadTicket(booking.id)"
                                    class="flex-1 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase border border-indigo-100"
                                >
                                    {{ t("history.btn_ticket") }}
                                </button>
                                <Link
                                    v-if="
                                        booking.status?.toLowerCase() ===
                                        'approved'
                                    "
                                    :href="
                                        route('visitor.feedback.create', {
                                            booking_id: booking.id,
                                        })
                                    "
                                    class="flex-1 py-2 text-center bg-amber-50 text-amber-600 rounded-lg text-[9px] font-black uppercase border border-amber-200"
                                >
                                    {{ t("nav.feedback") }}
                                </Link>
                                <button
                                    v-if="
                                        booking.status?.toLowerCase() ===
                                        'pending'
                                    "
                                    @click="deleteBooking(booking.id)"
                                    class="px-3 py-2 text-rose-500 bg-rose-50 rounded-lg border border-rose-100"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 mx-auto"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="hidden lg:block bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden"
                    >
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50/50 border-b border-slate-100"
                                    >
                                        <th
                                            class="p-6 text-[10px] font-black uppercase text-slate-400"
                                        >
                                            {{ t("dashboard.table_date") }}
                                        </th>
                                        <th
                                            class="p-6 text-[10px] font-black uppercase text-slate-400 text-center"
                                        >
                                            {{ t("history.qr_pass") }}
                                        </th>
                                        <th
                                            class="p-6 text-[10px] font-black uppercase text-slate-400"
                                        >
                                            {{ t("dashboard.table_hall") }}
                                        </th>
                                        <th
                                            class="p-6 text-[10px] font-black uppercase text-slate-400"
                                        >
                                            {{ t("dashboard.table_status") }}
                                        </th>
                                        <th
                                            class="p-6 text-[10px] font-black uppercase text-slate-400 text-right"
                                        >
                                            {{ t("history.actions") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <tr
                                        v-for="booking in bookings"
                                        :key="booking.id"
                                        class="hover:bg-slate-50/30 transition"
                                    >
                                        <td class="p-6">
                                            <div
                                                class="font-black text-slate-700 text-sm"
                                            >
                                                {{
                                                    formatDate(
                                                        booking.booking_date,
                                                    )
                                                }}
                                            </div>
                                            <div
                                                class="text-[10px] text-indigo-500 font-bold uppercase"
                                            >
                                                {{ booking.visitor_type }}
                                            </div>
                                        </td>
                                        <td class="p-6 text-center">
                                            <div
                                                v-if="
                                                    booking.status?.toLowerCase() !==
                                                        'rejected' &&
                                                    booking.qr_token
                                                "
                                                @click="openQrModal(booking)"
                                                class="inline-flex flex-col items-center gap-1 cursor-pointer group"
                                            >
                                                <img
                                                    :src="`https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=${encodeURIComponent(booking.qr_token)}`"
                                                    class="w-12 h-12 rounded-xl border-2 border-slate-100 p-1 bg-white"
                                                />
                                                <span
                                                    class="text-[8px] font-black text-slate-400 uppercase"
                                                >
                                                    {{ t("history.zoom") }}
                                                </span>
                                            </div>
                                            <div
                                                v-else
                                                class="text-slate-300 text-[8px] uppercase font-black"
                                            >
                                                N/A
                                            </div>
                                        </td>
                                        <td class="p-6">
                                            <div
                                                class="text-[11px] text-slate-600 font-bold bg-slate-100 px-3 py-1 rounded-full inline-block"
                                            >
                                                {{ getHallName(booking) }}
                                            </div>
                                        </td>
                                        <td class="p-6">
                                            <span
                                                :class="
                                                    getStatusClass(
                                                        booking.status,
                                                    )
                                                "
                                                class="text-[9px] font-black px-3 py-1 rounded-full uppercase"
                                            >
                                                {{ booking.status }}
                                            </span>
                                        </td>
                                        <td class="p-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    v-if="
                                                        [
                                                            'approved',
                                                            'pending',
                                                        ].includes(
                                                            booking.status?.toLowerCase(),
                                                        )
                                                    "
                                                    @click="
                                                        downloadTicket(
                                                            booking.id,
                                                        )
                                                    "
                                                    class="px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase border border-indigo-100"
                                                >
                                                    {{
                                                        t("history.btn_ticket")
                                                    }}
                                                </button>
                                                <Link
                                                    v-if="
                                                        booking.status?.toLowerCase() ===
                                                        'approved'
                                                    "
                                                    :href="
                                                        route(
                                                            'visitor.feedback.create',
                                                            {
                                                                booking_id:
                                                                    booking.id,
                                                            },
                                                        )
                                                    "
                                                    class="px-3 py-2 bg-amber-50 text-amber-600 rounded-lg text-[9px] font-black uppercase border border-amber-200"
                                                >
                                                    {{ t("nav.feedback") }}
                                                </Link>
                                                <button
                                                    v-if="
                                                        booking.status?.toLowerCase() ===
                                                        'pending'
                                                    "
                                                    @click="
                                                        deleteBooking(
                                                            booking.id,
                                                        )
                                                    "
                                                    class="p-2 text-slate-300 hover:text-rose-500 transition-colors"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-if="feedbacks.length === 0"
                    class="col-span-full bg-white p-20 rounded-[2.5rem] border-2 border-dashed border-slate-200 text-center text-slate-400 font-black uppercase text-xs"
                >
                    {{ t("history.empty_feedback") }}
                </div>
                <div
                    v-for="item in feedbacks"
                    :key="item.id"
                    class="bg-white p-6 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow"
                >
                    <div class="flex justify-between items-start mb-6">
                        <span
                            class="text-[9px] font-black px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 uppercase"
                        >
                            {{ t("nav.bookings") }} #{{ item.booking_id }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">
                            {{ formatDate(item.created_at) }}
                        </span>
                    </div>
                    <p
                        class="text-slate-600 text-sm leading-relaxed mb-6 italic"
                    >
                        "{{ item.message }}"
                    </p>
                    <div class="flex items-center gap-1">
                        <span
                            v-for="star in 5"
                            :key="star"
                            :class="
                                star <= item.rating
                                    ? 'text-amber-400'
                                    : 'text-slate-200'
                            "
                            class="text-xl"
                        >
                            ★
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </VisitorLayout>

    <div
        v-if="showQrModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md"
        @click.self="showQrModal = false"
    >
        <div
            class="bg-white rounded-[2rem] sm:rounded-[3rem] p-6 sm:p-10 max-w-sm w-full text-center shadow-2xl scale-in"
        >
            <h3
                class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6"
            >
                {{ t("history.qr_title") }}
            </h3>
            <div
                class="bg-slate-50 p-6 sm:p-8 rounded-[2rem] border-4 border-dashed border-slate-100 mb-8 flex justify-center"
            >
                <img
                    :src="selectedQrUrl"
                    class="w-full h-auto max-w-[200px]"
                    alt="QR Large"
                />
            </div>
            <div class="flex flex-col gap-3">
                <button
                    v-if="
                        ['approved', 'pending'].includes(
                            selectedBooking?.status?.toLowerCase(),
                        )
                    "
                    @click="downloadTicket(selectedBooking?.id)"
                    class="w-full py-4 bg-emerald-500 text-white rounded-2xl font-black uppercase text-xs tracking-widest active:scale-95 transition-transform"
                >
                    {{ t("history.btn_download") }}
                </button>
                <button
                    @click="showQrModal = false"
                    class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase text-xs tracking-widest active:scale-95 transition-transform"
                >
                    {{ t("history.btn_close") }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.animate-in {
    animation: fadeIn 0.4s ease-out;
}
.scale-in {
    animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
