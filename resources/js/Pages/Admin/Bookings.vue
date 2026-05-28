<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    bookings: Array,
});

const isExpired = (bookingDate) => {
    if (!bookingDate) return false;
    const datePart = bookingDate.split("T")[0];
    const today = new Date().toISOString().split("T")[0];
    return datePart < today;
};

const isFinalized = (status) => {
    return ["arrived", "no-show"].includes(status);
};

const searchQuery = ref("");

const filteredBookings = computed(() => {
    if (!searchQuery.value) return props.bookings;
    const q = searchQuery.value.toLowerCase();
    return props.bookings.filter((b) => {
        const dateStr = b.booking_date ? b.booking_date.split("T")[0] : "";
        return (
            (b.visitor_name && b.visitor_name.toLowerCase().includes(q)) ||
            (b.id && b.id.toString().includes(q)) ||
            (dateStr && dateStr.includes(q))
        );
    });
});

const updateStatus = (booking, newStatus) => {
    if (isExpired(booking.booking_date) || isFinalized(booking.status)) return;

    Swal.fire({
        title: t("admin_Booking.status_change_title"),
        text: `${t("admin_Booking.status_confirm")} ${booking.visitor_name}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: t("action.update_confirm"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(
                route("admin.bookings.update", { booking: booking.id }),
                { status: newStatus },
                { preserveScroll: true },
            );
        }
    });
};

const deleteBooking = (id) => {
    Swal.fire({
        title: t("admin_Booking.delete_title"),
        text: t("admin_Booking.delete_warning"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: t("action.delete"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.bookings.destroy", { booking: id }));
        }
    });
};
</script>

<template>
    <Head :title="t('admin_Booking.registry_title')" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-800">
                    {{ t("admin_Booking.registry_title") }}
                </h2>
                <div class="w-full max-w-sm">
                    <input
                        v-model="searchQuery"
                        :placeholder="t('action.search_placeholder') + '...'"
                        class="w-full border-slate-200 rounded-xl p-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>
            </div>

            <div
                class="overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-100"
            >
                <table class="w-full table-fixed">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th
                                class="p-5 w-1/4 uppercase text-[11px] font-black text-slate-500 text-left"
                            >
                                {{ t("admin_Booking.visitor") }}
                            </th>
                            <th
                                class="p-5 w-1/5 uppercase text-[11px] font-black text-slate-500 text-left"
                            >
                                {{ t("admin_Booking.date") }}
                            </th>
                            <th
                                class="p-5 w-1/5 uppercase text-[11px] font-black text-slate-500 text-left"
                            >
                                {{ t("admin_Booking.slot") }}
                            </th>
                            <th
                                class="p-5 w-1/5 uppercase text-[11px] font-black text-slate-500 text-left"
                            >
                                {{ t("admin_Booking.status") }}
                            </th>
                            <th
                                class="p-5 w-1/6 uppercase text-[11px] font-black text-slate-500 text-left"
                            >
                                {{ t("action.actions") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="booking in filteredBookings"
                            :key="booking.id"
                            class="hover:bg-slate-50 transition"
                        >
                            <td
                                class="p-5 text-sm font-bold text-slate-700 truncate"
                            >
                                {{ booking.visitor_name }}
                            </td>
                            <td class="p-5 text-sm text-slate-600">
                                {{
                                    booking.booking_date
                                        ? booking.booking_date.split("T")[0]
                                        : ""
                                }}
                            </td>
                            <td class="p-5 text-sm text-slate-600">
                                {{ booking.readable_slot }}
                            </td>
                            <td class="p-5">
                                <select
                                    v-model="booking.status"
                                    @change="
                                        updateStatus(
                                            booking,
                                            $event.target.value,
                                        )
                                    "
                                    :disabled="
                                        isExpired(booking.booking_date) ||
                                        isFinalized(booking.status)
                                    "
                                    class="border-slate-200 rounded-lg p-2 text-[11px] uppercase font-bold cursor-pointer w-full max-w-[140px] disabled:bg-slate-100 disabled:text-slate-400"
                                >
                                    <option value="pending">
                                        {{ t("status.pending") }}
                                    </option>
                                    <option value="approved">
                                        {{ t("status.approved") }}
                                    </option>
                                    <option value="confirmed">
                                        {{ t("status.confirmed") }}
                                    </option>
                                    <option value="arrived">
                                        {{ t("status.arrived") }}
                                    </option>
                                    <option value="no-show">
                                        {{ t("status.no_show") }}
                                    </option>
                                    <option value="cancelled">
                                        {{ t("status.cancelled") }}
                                    </option>
                                    <option value="rejected">
                                        {{ t("status.rejected") }}
                                    </option>
                                    <option value="completed">
                                        {{ t("status.completed") }}
                                    </option>
                                </select>
                            </td>
                            <td class="p-5">
                                <button
                                    @click="deleteBooking(booking.id)"
                                    class="text-rose-600 text-xs font-bold hover:underline"
                                >
                                    {{ t("action.delete") }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
