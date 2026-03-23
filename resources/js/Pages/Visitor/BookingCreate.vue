<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";

const props = defineProps({
    halls: Array,
});

const step = ref(1);

const availableHalls = [
    {
        id: 1,
        icon: "🏛️",
        name: "Adama City Hall",
        description: "Central hub for city assemblies.",
    },
    {
        id: 2,
        icon: "⚖️",
        name: "Municipal Council Chamber",
        description: "Space for local governance.",
    },
    {
        id: 3,
        icon: "🎭",
        name: "Aba Gadaa Cultural Hall",
        description: "Cultural and historical venue.",
    },
    {
        id: 4,
        icon: "📈",
        name: "Investment Promotion Hall",
        description: "Center for economic growth.",
    },
    {
        id: 5,
        icon: "🗺️",
        name: "Land Management Hall",
        description: "Urban development services.",
    },
    {
        id: 6,
        icon: "🌟",
        name: "Multi-Purpose Hall",
        description: "Versatile space for gatherings.",
    },
    {
        id: 7,
        icon: "📢",
        name: "Public Relations Hall",
        description: "Communication bridge.",
    },
    {
        id: 8,
        icon: "🛡️",
        name: "Justice & Human Rights Hall",
        description: "Legal awareness hall.",
    },
    {
        id: 9,
        icon: "💰",
        name: "Revenue Service Hall",
        description: "Central tax services.",
    },
];

/** * UPDATED: The 'id' now stores the actual time string.
 * This is what will be saved in your database 'slot_id' column.
 */
const morningSlots = [
    { id: "3:00 - 3:30", label: "Morning Slot 1" },
    { id: "4:00 - 4:30", label: "Morning Slot 2" },
    { id: "5:00 - 5:30", label: "Morning Slot 3" },
];

const afternoonSlots = [
    { id: "8:00 - 8:30", label: "Afternoon Slot 1" },
    { id: "9:00 - 9:30", label: "Afternoon Slot 2" },
    { id: "10:00 - 10:30", label: "Afternoon Slot 3" },
];

const form = useForm({
    hall_ids: [],
    visitor_category: "",
    visitor_type: "Local Resident",
    organization_name: "",
    number_of_visitors: 1,
    booking_date: "",
    slot_id: "",
    attachment: null,
});

const isWeekday = computed(() => {
    if (!form.booking_date) return false;
    const date = new Date(form.booking_date);
    const day = date.getDay();
    return day !== 0 && day !== 6; // 0=Sun, 6=Sat
});

const toggleHall = (id) => {
    const index = form.hall_ids.indexOf(id);
    if (index > -1) {
        form.hall_ids.splice(index, 1);
    } else {
        form.hall_ids.push(id);
    }
};

const setCategory = (category) => {
    form.visitor_category = category;
    form.visitor_type =
        category === "VIP" ? "Federal-Authority" : "Local Resident";
    step.value = 3;
};

const handleFileUpload = (e) => {
    form.attachment = e.target.files[0];
};

const validateAndSubmit = () => {
    if (form.hall_ids.length === 0) {
        Swal.fire(
            "Selection Required",
            "Please select at least one hall.",
            "warning",
        );
        step.value = 1;
        return;
    }
    if (!form.booking_date || !isWeekday.value) {
        Swal.fire(
            "Invalid Date",
            "Please select a weekday (Monday-Friday).",
            "error",
        );
        return;
    }
    if (!form.slot_id) {
        Swal.fire("Slot Required", "Please select a time slot.", "warning");
        return;
    }
    if (form.visitor_category === "VIP" && !form.attachment) {
        Swal.fire(
            "Attachment Required",
            "VIP visits require a supporting document.",
            "error",
        );
        return;
    }

    form.post(route("visitor.booking.store"), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                title: "Booking Confirmed!",
                text: "Your request has been sent successfully.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
            });
        },
        onError: (errors) => {
            const errorMsg =
                Object.values(errors)[0] || "Something went wrong.";
            Swal.fire("Error", errorMsg, "error");
        },
    });
};

const today = new Date().toISOString().split("T")[0];
</script>

<template>
    <Head title="Create Booking" />

    <VisitorLayout>
        <template #header>Create New Booking</template>

        <div class="max-w-6xl mx-auto pb-20">
            <div v-if="step === 1" class="space-y-10 animate-in">
                <p
                    class="text-center text-slate-400 font-bold text-sm uppercase tracking-widest"
                >
                    Step 1: Select halls to visit
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="hall in availableHalls"
                        :key="hall.id"
                        @click="toggleHall(hall.id)"
                        :class="
                            form.hall_ids.includes(hall.id)
                                ? 'border-indigo-500 bg-white ring-2 ring-indigo-500/10 shadow-xl'
                                : 'border-transparent bg-white shadow-sm hover:shadow-md'
                        "
                        class="p-8 rounded-[2rem] border-2 cursor-pointer transition-all duration-300 flex flex-col items-center text-center group"
                    >
                        <div
                            class="text-4xl mb-4 group-hover:scale-110 transition-transform"
                        >
                            {{ hall.icon }}
                        </div>
                        <h3
                            class="text-sm font-black text-slate-900 uppercase tracking-tight mb-2"
                        >
                            {{ hall.name }}
                        </h3>
                        <p
                            class="text-slate-400 text-[10px] font-medium leading-relaxed"
                        >
                            {{ hall.description }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button
                        @click="step = 2"
                        :disabled="form.hall_ids.length === 0"
                        class="w-full max-w-md py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg hover:bg-indigo-700 disabled:opacity-30 transition-all"
                    >
                        Continue to Category
                    </button>
                </div>
            </div>

            <div v-if="step === 2" class="max-w-4xl mx-auto py-10 animate-in">
                <h1
                    class="text-2xl font-black text-slate-900 text-center uppercase mb-12"
                >
                    Step 2: Visitor Category
                </h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div
                        @click="setCategory('VIP')"
                        class="bg-white p-12 rounded-[3rem] shadow-sm border-2 border-transparent hover:border-amber-400 cursor-pointer text-center group transition-all"
                    >
                        <div
                            class="text-6xl mb-6 group-hover:scale-110 transition-transform"
                        >
                            🏆
                        </div>
                        <h2 class="text-xl font-black text-slate-900 uppercase">
                            VIP / Official
                        </h2>
                        <p class="text-slate-400 mt-2 text-xs font-bold">
                            Government/Org visits
                        </p>
                    </div>
                    <div
                        @click="setCategory('Normal')"
                        class="bg-white p-12 rounded-[3rem] shadow-sm border-2 border-transparent hover:border-indigo-500 cursor-pointer text-center group transition-all"
                    >
                        <div
                            class="text-6xl mb-6 group-hover:scale-110 transition-transform"
                        >
                            👤
                        </div>
                        <h2 class="text-xl font-black text-slate-900 uppercase">
                            Normal Visitor
                        </h2>
                        <p class="text-slate-400 mt-2 text-xs font-bold">
                            Residents or Students
                        </p>
                    </div>
                </div>
                <button
                    @click="step = 1"
                    class="block mx-auto mt-8 text-slate-400 font-bold uppercase text-[10px] hover:text-indigo-600 transition-colors"
                >
                    Back to Halls
                </button>
            </div>

            <div v-if="step === 3" class="max-w-4xl mx-auto animate-in">
                <form
                    @submit.prevent="validateAndSubmit"
                    class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase text-slate-400"
                                >Date of Visit</label
                            >
                            <input
                                type="date"
                                v-model="form.booking_date"
                                :min="today"
                                class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase text-slate-400"
                                >Total Visitors</label
                            >
                            <input
                                type="number"
                                v-model="form.number_of_visitors"
                                min="1"
                                class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold"
                            />
                        </div>
                    </div>

                    <div
                        v-if="form.visitor_category === 'VIP'"
                        class="space-y-6 animate-in"
                    >
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase text-slate-400"
                                >Organization Name</label
                            >
                            <input
                                type="text"
                                v-model="form.organization_name"
                                placeholder="e.g. Ministry of Education"
                                class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold"
                            />
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase text-slate-400"
                                >Supporting Document (PDF/JPG)</label
                            >
                            <input
                                type="file"
                                @change="handleFileUpload"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"
                            />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400"
                            >Select Time Slot</label
                        >
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label
                                v-for="slot in [
                                    ...morningSlots,
                                    ...afternoonSlots,
                                ]"
                                :key="slot.id"
                                :class="
                                    form.slot_id === slot.id
                                        ? 'bg-indigo-600 text-white shadow-lg'
                                        : 'bg-slate-50 text-slate-500 hover:bg-slate-100'
                                "
                                class="cursor-pointer p-4 rounded-xl text-center transition-all border border-transparent"
                            >
                                <input
                                    type="radio"
                                    :value="slot.id"
                                    v-model="form.slot_id"
                                    class="hidden"
                                />
                                <span
                                    class="block font-black text-[10px] uppercase"
                                    >{{ slot.label }}</span
                                >
                                <span class="text-[11px] font-bold">{{
                                    slot.id
                                }}</span>
                            </label>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-6 border-t border-slate-50"
                    >
                        <button
                            type="button"
                            @click="step = 2"
                            class="text-slate-400 font-black text-[10px] uppercase hover:text-slate-600"
                        >
                            Back
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-10 py-4 bg-indigo-600 text-white rounded-xl font-black uppercase text-xs shadow-xl hover:bg-indigo-700 transition-all disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? "Processing..."
                                    : "Confirm Booking"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </VisitorLayout>
</template>

<style scoped>
.animate-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
