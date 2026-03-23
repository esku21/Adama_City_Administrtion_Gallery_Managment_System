<script setup>
import { computed, ref } from "vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import Swal from "sweetalert2";

const page = usePage();

// Data from Controller
const bookings = computed(() => page.props.bookings || []);
const feedbacks = computed(() => page.props.feedbacks || []);

// State Management
const activeTab = ref("bookings");
const showQrModal = ref(false);
const selectedQrUrl = ref("");
const selectedBooking = ref(null);

/**
 * Modal Logic
 */
const openQrModal = (booking) => {
    const qrData = booking.qr_token || `BOOKING-${booking.id}`;
    selectedQrUrl.value = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData)}`;
    selectedBooking.value = booking;
    showQrModal.value = true;
};

/**
 * Download PDF Ticket
 */
const downloadTicket = (id) => {
    // Matches the route name in web.php: visitor.booking.download
    window.location.href = route("visitor.booking.download", id);
};

/**
 * Delete Booking
 */
const deleteBooking = (id) => {
    Swal.fire({
        title: "Cancel Booking?",
        text: "Are you sure you want to remove this pending booking?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#64748b",
        confirmButtonText: "Yes, cancel it!",
        customClass: {
            popup: "rounded-[2rem]",
            confirmButton: "rounded-xl px-6 py-3",
            cancelButton: "rounded-xl px-6 py-3",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("visitor.booking.destroy", id), {
                preserveScroll: true,
                onSuccess: () =>
                    Swal.fire({
                        title: "Cancelled!",
                        text: "Your booking has been removed.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    }),
            });
        }
    });
};

/**
 * Helpers
 */
const getHallNames = (halls) => {
    if (!halls || halls.length === 0) return "General Access";
    return halls.map((h) => h.name).join(", ");
};

const getStatusClass = (status) => {
    const s = status?.toLowerCase();
    if (s === "approved" || s === "completed")
        return "bg-emerald-100 text-emerald-700 border border-emerald-200";
    if (s === "pending")
        return "bg-amber-100 text-amber-700 border border-amber-200";
    if (s === "cancelled" || s === "rejected")
        return "bg-rose-100 text-rose-700 border border-rose-200";
    return "bg-slate-100 text-slate-700 border border-slate-200";
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>

<template>
    <Head title="My History" />

    <VisitorLayout>
        <template #header>Activity History</template>

        <div class="max-w-6xl mx-auto pb-20 animate-in">
            <div class="flex border-b border-slate-200 mb-8 gap-8">
                <button
                    @click="activeTab = 'bookings'"
                    :class="
                        activeTab === 'bookings'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-slate-400'
                    "
                    class="pb-4 px-2 font-black text-[10px] uppercase tracking-[0.2em] border-b-4 transition-all"
                >
                    Bookings ({{ bookings.length }})
                </button>
                <button
                    @click="activeTab = 'feedback'"
                    :class="
                        activeTab === 'feedback'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-slate-400'
                    "
                    class="pb-4 px-2 font-black text-[10px] uppercase tracking-[0.2em] border-b-4 transition-all"
                >
                    My Feedback ({{ feedbacks.length }})
                </button>
            </div>

            <div
                v-if="activeTab === 'bookings'"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden"
            >
                <div
                    v-if="bookings.length === 0"
                    class="p-20 text-center text-slate-400 font-bold uppercase text-xs"
                >
                    No bookings found.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/50 border-b border-slate-100"
                            >
                                <th
                                    class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-widest"
                                >
                                    Visit Info
                                </th>
                                <th
                                    class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center"
                                >
                                    QR Pass
                                </th>
                                <th
                                    class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-widest"
                                >
                                    Halls
                                </th>
                                <th
                                    class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-widest"
                                >
                                    Status
                                </th>
                                <th
                                    class="p-6 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right"
                                >
                                    Actions
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
                                        {{ formatDate(booking.booking_date) }}
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
                                            booking.status.toLowerCase() !==
                                            'rejected'
                                        "
                                        @click="openQrModal(booking)"
                                        class="inline-flex flex-col items-center gap-1 cursor-pointer group"
                                    >
                                        <img
                                            :src="`https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=${encodeURIComponent(booking.qr_token)}`"
                                            class="w-12 h-12 rounded-xl border-2 border-slate-100 group-hover:border-indigo-400 transition-all p-1 bg-white"
                                        />
                                        <span
                                            class="text-[8px] font-black text-slate-400 uppercase group-hover:text-indigo-600"
                                            >Zoom</span
                                        >
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
                                        {{ getHallNames(booking.halls) }}
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span
                                        :class="getStatusClass(booking.status)"
                                        class="text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-tighter"
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
                                                    booking.status.toLowerCase(),
                                                )
                                            "
                                            @click="downloadTicket(booking.id)"
                                            class="px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase hover:bg-indigo-100 border border-indigo-100 flex items-center gap-1 transition-all"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3 w-3"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="3"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                                />
                                            </svg>
                                            Ticket
                                        </button>

                                        <Link
                                            v-if="
                                                booking.status.toLowerCase() ===
                                                'approved'
                                            "
                                            :href="
                                                route(
                                                    'visitor.feedback.create',
                                                    { booking_id: booking.id },
                                                )
                                            "
                                            class="px-3 py-2 bg-amber-50 text-amber-600 rounded-lg text-[9px] font-black uppercase hover:bg-amber-100 border border-amber-200"
                                        >
                                            Feedback
                                        </Link>

                                        <button
                                            v-if="
                                                booking.status.toLowerCase() ===
                                                'pending'
                                            "
                                            @click="deleteBooking(booking.id)"
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

            <div v-else class="grid md:grid-cols-2 gap-6">
                <div
                    v-if="feedbacks.length === 0"
                    class="col-span-full bg-white p-20 rounded-[2.5rem] border-2 border-dashed border-slate-200 text-center text-slate-400 font-black uppercase text-xs"
                >
                    No feedback submitted yet.
                </div>
                <div
                    v-for="item in feedbacks"
                    :key="item.id"
                    class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow"
                >
                    <div class="flex justify-between items-start mb-6">
                        <span
                            class="text-[9px] font-black px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 uppercase"
                            >Booking #{{ item.booking_id }}</span
                        >
                        <span class="text-[10px] font-bold text-slate-400">{{
                            formatDate(item.created_at)
                        }}</span>
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
                            >★</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </VisitorLayout>

    <div
        v-if="showQrModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md transition-all"
        @click.self="showQrModal = false"
    >
        <div
            class="bg-white rounded-[3rem] p-10 max-w-sm w-full text-center shadow-2xl scale-in"
        >
            <h3
                class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6"
            >
                Your Entry Pass
            </h3>
            <div
                class="bg-slate-50 p-8 rounded-[2rem] border-4 border-dashed border-slate-100 mb-8 flex justify-center"
            >
                <img
                    :src="selectedQrUrl"
                    class="w-full h-auto"
                    alt="QR Large"
                />
            </div>

            <div class="flex flex-col gap-3">
                <button
                    v-if="
                        ['approved', 'pending'].includes(
                            selectedBooking?.status.toLowerCase(),
                        )
                    "
                    @click="downloadTicket(selectedBooking.id)"
                    class="w-full py-4 bg-emerald-500 text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-emerald-600 transition-all shadow-lg flex justify-center items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                        />
                    </svg>
                    Download PDF
                </button>
                <button
                    @click="showQrModal = false"
                    class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-slate-200 transition-all"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
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
