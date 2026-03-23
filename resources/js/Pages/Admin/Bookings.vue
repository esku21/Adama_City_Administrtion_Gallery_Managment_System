<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    bookings: Array,
});

// Search State
const searchQuery = ref("");

// Filtered Bookings Logic
const filteredBookings = computed(() => {
    if (!searchQuery.value) return props.bookings;

    const query = searchQuery.value.toLowerCase();
    return props.bookings.filter((booking) => {
        return (
            booking.visitor_name.toLowerCase().includes(query) ||
            booking.id.toString().includes(query) ||
            booking.booking_date.includes(query)
        );
    });
});

// Modal State
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

// Form State
const form = useForm({
    visitor_name: "",
    booking_date: "",
    number_of_visitors: 1,
    status: "Approved",
});

const openCreateModal = () => {
    isEditing.value = false;
    currentId.value = null;
    form.clearErrors();
    form.reset();
    form.status = "Approved";
    showModal.value = true;
};

const openEditModal = (booking) => {
    isEditing.value = true;
    currentId.value = booking.id;
    form.clearErrors();
    form.visitor_name = booking.visitor_name;
    form.booking_date = booking.booking_date;
    form.number_of_visitors = booking.number_of_visitors;
    form.status = "Approved";
    showModal.value = true;
};

const submitForm = () => {
    const requestOptions = {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            Swal.fire(
                "Success",
                `Entry ${isEditing.value ? "updated" : "created"} successfully`,
                "success",
            );
        },
        onError: () => {
            Swal.fire("Error", "Please check the form for errors.", "error");
        },
    };

    if (isEditing.value) {
        form.put(
            route("admin.bookings.update", { booking: currentId.value }),
            requestOptions,
        );
    } else {
        form.post(route("admin.bookings.store"), requestOptions);
    }
};

const deleteBooking = (id) => {
    Swal.fire({
        title: "Delete Record?",
        text: "This will permanently remove the booking.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e11d48",
        cancelButtonColor: "#64748b",
        confirmButtonText: "Yes, Delete",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.bookings.delete", { booking: id }), {
                preserveScroll: true,
                onSuccess: () =>
                    Swal.fire("Deleted!", "Record removed.", "success"),
            });
        }
    });
};
</script>

<template>
    <Head title="Booking Registry" />

    <AuthenticatedLayout>
        <div class="p-6 space-y-8">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <div>
                    <h2
                        class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase"
                    >
                        Registry
                    </h2>
                    <p
                        class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em]"
                    >
                        Authorized Visit Schedule
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-xl shadow-indigo-100 flex items-center gap-2"
                >
                    <span class="material-icons-outlined text-sm"
                        >add_circle</span
                    >
                    Create Available Entry
                </button>
            </div>

            <div class="relative max-w-md">
                <span
                    class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400"
                >
                    <span class="material-icons-outlined text-lg">search</span>
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="SEARCH VISITOR NAME OR ID..."
                    class="w-full bg-white border border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                />
            </div>

            <div
                class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden"
            >
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th
                                class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest"
                            >
                                Visitor Detail
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center"
                            >
                                Visit Date
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center"
                            >
                                System Status
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right"
                            >
                                Control Panel
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="filteredBookings.length === 0">
                            <td
                                colspan="4"
                                class="px-8 py-12 text-center text-slate-400 font-medium"
                            >
                                No records match your search.
                            </td>
                        </tr>
                        <tr
                            v-for="booking in filteredBookings"
                            :key="booking.id"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-slate-900">
                                    {{ booking.visitor_name }}
                                </p>
                                <p
                                    class="text-[10px] text-indigo-500 font-mono font-bold uppercase"
                                >
                                    UID: #{{ booking.id }}
                                </p>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="text-sm font-medium text-slate-600"
                                    >{{ booking.booking_date }}</span
                                >
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest px-4 py-2 rounded-xl border border-emerald-100"
                                >
                                    Approved
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-3">
                                    <button
                                        @click="openEditModal(booking)"
                                        class="flex items-center gap-1 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-600 hover:text-white transition-all font-black text-[9px] uppercase tracking-tighter border border-indigo-100"
                                    >
                                        <span
                                            class="material-icons-outlined text-sm"
                                            >edit</span
                                        >
                                        Update
                                    </button>
                                    <button
                                        @click="deleteBooking(booking.id)"
                                        class="flex items-center gap-1 px-4 py-2 bg-rose-50 text-rose-700 rounded-xl hover:bg-rose-600 hover:text-white transition-all font-black text-[9px] uppercase tracking-tighter border border-rose-100"
                                    >
                                        <span
                                            class="material-icons-outlined text-sm"
                                            >delete</span
                                        >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            v-if="showModal"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6"
        >
            <div
                class="bg-white rounded-[3rem] w-full max-w-lg p-12 shadow-2xl border border-white/20"
            >
                <h2
                    class="text-2xl font-black text-slate-900 italic uppercase mb-8 border-l-4 border-indigo-600 pl-4"
                >
                    {{ isEditing ? "Modify Schedule" : "Set Available Date" }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <label
                            class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest"
                            >Visitor Name (or Placeholder)</label
                        >
                        <input
                            v-model="form.visitor_name"
                            type="text"
                            required
                            class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest"
                                >Booking Date</label
                            >
                            <input
                                v-model="form.booking_date"
                                type="date"
                                required
                                class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                            />
                        </div>
                        <div>
                            <label
                                class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest"
                                >Pax Limit</label
                            >
                            <input
                                v-model="form.number_of_visitors"
                                type="number"
                                min="1"
                                class="w-full bg-slate-50 border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
                            />
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase text-slate-400 hover:bg-slate-100 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 px-6 py-4 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? "Saving..."
                                    : "Authorize Schedule"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
