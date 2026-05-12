<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    bookings: Array,
    halls: Array,
});

// --- SEARCH ---
const searchQuery = ref("");
const filteredBookings = computed(() => {
    if (!searchQuery.value) return props.bookings;
    const q = searchQuery.value.toLowerCase();
    return props.bookings.filter((b) => {
        return (
            (b.visitor_name && b.visitor_name.toLowerCase().includes(q)) ||
            b.id.toString().includes(q)
        );
    });
});

// --- STATUS UPDATE ---
const updateStatus = (booking, newStatus) => {
    Swal.fire({
        title: t("bookings.status_change_title"),
        text: `Update status for ${booking.visitor_name} to ${newStatus.toUpperCase()}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        confirmButtonText: t("action.update_confirm"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(
                route("admin.bookings.update", { booking: booking.id }),
                {
                    visitor_name: booking.visitor_name,
                    booking_date: booking.booking_date,
                    number_of_visitors: booking.number_of_visitors,
                    hall_id: booking.hall_id,
                    status: newStatus,
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: t("action.updated"),
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                        });
                    },
                },
            );
        } else {
            router.get(
                route("admin.bookings.index"),
                {},
                { preserveScroll: true },
            );
        }
    });
};

// --- MODAL & FORM ---
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const form = useForm({
    visitor_name: "",
    booking_date: "",
    number_of_visitors: 1,
    hall_id: "",
    status: "pending",
});

const openCreateModal = () => {
    isEditing.value = false;
    currentId.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (booking) => {
    isEditing.value = true;
    currentId.value = booking.id;
    form.visitor_name = booking.visitor_name;
    form.booking_date = booking.booking_date;
    form.number_of_visitors = booking.number_of_visitors;
    form.hall_id = booking.hall_id;
    form.status = booking.status;
    showModal.value = true;
};

const submitForm = () => {
    const options = {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            Swal.fire(t("action.success"), "Operation successful", "success");
        },
    };

    if (isEditing.value) {
        form.put(
            route("admin.bookings.update", { booking: currentId.value }),
            options,
        );
    } else {
        form.post(route("admin.post.bookings.store"), options);
    }
};

const deleteBooking = (id) => {
    Swal.fire({
        title: t("bookings.delete_title"),
        text: t("bookings.delete_warning"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e11d48",
        confirmButtonText: t("action.delete"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.bookings.destroy", { booking: id }), {
                onSuccess: () => Swal.fire(t("action.deleted"), "", "success"),
            });
        }
    });
};
</script>

<template>
    <Head :title="t('bookings.registry_title')" />
    <AuthenticatedLayout>
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6"
        >
            <div
                class="flex flex-col sm:flex-row justify-between gap-4 sm:items-center"
            >
                <div>
                    <h2
                        class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight"
                    >
                        {{ t("bookings.info_heading") }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">
                        {{ t("bookings.sub_heading") }}
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md flex items-center justify-center gap-2 transition-all active:scale-95"
                >
                    <span class="material-icons-outlined">add_circle</span>
                    {{ t("bookings.create_entry") }}
                </button>
            </div>

            <div class="max-w-full sm:max-w-md relative">
                <span
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                >
                    <span class="material-icons-outlined text-xl">search</span>
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="t('action.search_placeholder')"
                    class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm"
                />
            </div>

            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
            >
                <div class="overflow-x-auto scrollbar-hide">
                    <table
                        class="w-full text-left border-collapse min-w-[700px]"
                    >
                        <thead>
                            <tr class="bg-slate-50 border-b">
                                <th
                                    class="p-4 text-[11px] font-black uppercase text-slate-500 tracking-wider"
                                >
                                    Visitor & Hall
                                </th>
                                <th
                                    class="p-4 text-[11px] font-black uppercase text-slate-500 text-center tracking-wider"
                                >
                                    Date
                                </th>
                                <th
                                    class="p-4 text-[11px] font-black uppercase text-slate-500 text-center tracking-wider"
                                >
                                    Document
                                </th>
                                <th
                                    class="p-4 text-[11px] font-black uppercase text-slate-500 text-center tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="p-4 text-[11px] font-black uppercase text-slate-500 text-right tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="booking in filteredBookings"
                                :key="booking.id"
                                class="hover:bg-indigo-50/40 transition-colors"
                            >
                                <td class="p-4">
                                    <div
                                        class="font-bold text-slate-800 text-sm"
                                    >
                                        {{ booking.visitor_name }}
                                    </div>
                                    <div
                                        class="text-[11px] text-indigo-600 font-bold uppercase tracking-tight"
                                    >
                                        {{
                                            booking.hall?.name ||
                                            t("bookings.no_hall")
                                        }}
                                        <span class="text-slate-300 mx-1"
                                            >|</span
                                        >
                                        {{ booking.number_of_visitors }}
                                        {{ t("bookings.visitors") }}
                                    </div>
                                </td>
                                <td
                                    class="p-4 text-center text-sm text-slate-600 font-medium"
                                >
                                    {{ booking.booking_date }}
                                </td>
                                <td class="p-4 text-center">
                                    <div v-if="booking.attachment_url">
                                        <a
                                            :href="booking.attachment_url"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-black transition-colors shadow-sm"
                                        >
                                            <span
                                                class="material-icons-outlined text-sm"
                                                >visibility</span
                                            >
                                            VIEW PDF
                                        </a>
                                    </div>
                                    <div
                                        v-else
                                        class="text-slate-300 flex flex-col items-center"
                                    >
                                        <span
                                            class="material-icons-outlined text-lg"
                                            >block</span
                                        >
                                        <span
                                            class="text-[9px] uppercase font-black"
                                            >No File</span
                                        >
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <select
                                        :value="booking.status"
                                        @change="
                                            updateStatus(
                                                booking,
                                                $event.target.value,
                                            )
                                        "
                                        class="text-[10px] font-black uppercase px-3 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-indigo-500 cursor-pointer"
                                    >
                                        <option
                                            v-for="s in [
                                                'pending',
                                                'approved',
                                                'cancelled',
                                                'extended',
                                                'completed',
                                                'rejected',
                                            ]"
                                            :key="s"
                                            :value="s"
                                        >
                                            {{ s.toUpperCase() }}
                                        </option>
                                    </select>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openEditModal(booking)"
                                            class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                            title="Update"
                                        >
                                            <span
                                                class="material-icons-outlined text-lg"
                                                >edit</span
                                            >
                                        </button>
                                        <button
                                            @click="deleteBooking(booking.id)"
                                            class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                            title="Delete"
                                        >
                                            <span
                                                class="material-icons-outlined text-lg"
                                                >delete</span
                                            >
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-if="filteredBookings.length === 0"
                    class="p-12 text-center"
                >
                    <span
                        class="material-icons-outlined text-5xl text-slate-200"
                        >history_edu</span
                    >
                    <p
                        class="mt-2 text-slate-400 font-bold uppercase text-xs tracking-widest"
                    >
                        No Records Found
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom scrollbar for better appearance on Windows/PC */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
