<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import Swal from "sweetalert2";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";

import {
    Building2,
    Gavel,
    Palmtree,
    TrendingUp,
    Layers,
    Banknote,
    Crown,
    User,
    ArrowRight,
    ArrowLeft,
    CheckCircle2,
    FileUp,
    Calendar,
    Users,
    Clock,
    MapPin,
} from "lucide-vue-next";

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    halls: {
        type: Array,
        default: () => [],
    },
});

const step = ref(1);

/**
 * GLOBAL ERROR HANDLER
 */
watch(
    () => page.props.flash?.error,
    (message) => {
        if (message) {
            Swal.fire({
                icon: "error",
                title: "Booking Restricted",
                text: message,
                confirmButtonColor: "#4f46e5",
            });
        }
    },
    { immediate: true },
);

const steps = computed(() => [
    { number: 1, label: t("bookings.create_title") },
    { number: 2, label: t("bookings.identify_title") },
    { number: 3, label: t("bookings.schedule_title") },
]);

const getHallIcon = (name) => {
    const map = {
        "Adama City Hall": Building2,
        "Aba Gadaa Cultural Hall": Palmtree,
        "Municipal Council Chamber": Gavel,
        "Investment Promotion Hall": TrendingUp,
        "Revenue Service Hall": Banknote,
    };
    return map[name] || Layers;
};

const morningSlots = [
    { id: "m1", time: "09:00 AM - 09:30 AM", label: "Early Morning" },
    { id: "m2", time: "10:00 AM - 10:30 AM", label: "Mid Morning" },
    { id: "m3", time: "11:00 AM - 11:30 AM", label: "Late Morning" },
];

const afternoonSlots = [
    { id: "a1", time: "02:00 PM - 02:30 PM", label: "Early Afternoon" },
    { id: "a2", time: "03:00 PM - 03:30 PM", label: "Mid Afternoon" },
    { id: "a3", time: "04:00 PM - 04:30 PM", label: "Late Afternoon" },
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

const today = new Date().toISOString().split("T")[0];

const isWeekday = computed(() => {
    if (!form.booking_date) return false;
    const d = new Date(form.booking_date).getDay();
    return d !== 0 && d !== 6;
});

// Updated to allow selecting one or more galleries
const toggleHall = (id) => {
    const index = form.hall_ids.indexOf(id);
    if (index > -1) {
        form.hall_ids.splice(index, 1); // Deselect if already selected
    } else {
        form.hall_ids.push(id); // Select if not present
    }
};

const setCategory = (cat) => {
    form.visitor_category = cat;
    form.visitor_type =
        cat === "VIP" ? "Official Government Body" : "Local Resident";
    step.value = 3;
};

const handleFileUpload = (e) => {
    form.attachment = e.target.files[0];
};

const validateAndSubmit = () => {
    if (!form.hall_ids.length) {
        return Swal.fire(
            "Select Hall",
            "Please select at least one gallery/hall",
            "warning",
        );
    }

    if (form.booking_date && !isWeekday.value) {
        return Swal.fire(
            "Weekend Not Allowed",
            "Only Monday–Friday bookings allowed",
            "error",
        );
    }

    if (form.visitor_category === "VIP" && !form.attachment) {
        return Swal.fire(
            "Missing Document",
            "VIP bookings require official letter",
            "info",
        );
    }

    if (!form.slot_id) {
        return Swal.fire(
            "Select Time Slot",
            "Please pick a time slot before confirming",
            "warning",
        );
    }

    form.post(route("visitor.booking.store"), {
        forceFormData: true,
        onSuccess: (page) => {
            if (page?.props?.flash?.error) return;
            form.reset();
            step.value = 1;
            Swal.fire({
                icon: "success",
                title: page?.props?.flash?.success || t("bookings.success_msg"),
                confirmButtonColor: "#4f46e5",
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Request Failed",
                text: "Please check your input details",
            });
        },
    });
};
</script>

<template>
    <Head :title="`${$t('nav.new_booking')} - ACAGMS`" />

    <VisitorLayout>
        <div class="max-w-5xl mx-auto px-4 py-10">
            <div class="flex justify-between mb-12 max-w-2xl mx-auto relative">
                <div
                    v-for="s in steps"
                    :key="s.number"
                    class="flex-1 text-center z-10"
                >
                    <div
                        class="w-12 h-12 mx-auto rounded-full flex items-center justify-center font-bold shadow-sm transition-all"
                        :class="
                            step >= s.number
                                ? 'bg-indigo-600 text-white ring-4 ring-indigo-100'
                                : 'bg-white text-gray-400 border'
                        "
                    >
                        <CheckCircle2 v-if="step > s.number" :size="22" />
                        <span v-else>{{ s.number }}</span>
                    </div>
                    <p
                        class="text-[11px] mt-3 font-bold uppercase"
                        :class="
                            step >= s.number
                                ? 'text-indigo-600'
                                : 'text-gray-400'
                        "
                    >
                        {{ s.label }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl border overflow-hidden">
                <div v-if="step === 1" class="p-8">
                    <h2 class="text-3xl font-black mb-2">
                        {{ $t("bookings.create_title") }}
                    </h2>
                    <p class="text-gray-500 mb-8">
                        Choose one or more galleries to continue
                    </p>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div
                            v-for="hall in props.halls"
                            :key="hall.id"
                            @click="toggleHall(hall.id)"
                            class="p-6 border rounded-2xl cursor-pointer transition hover:shadow-lg relative overflow-hidden"
                            :class="
                                form.hall_ids.includes(hall.id)
                                    ? 'border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-600/20'
                                    : 'border-gray-200 hover:border-gray-300'
                            "
                        >
                            <div
                                v-if="form.hall_ids.includes(hall.id)"
                                class="absolute top-3 right-3 bg-indigo-600 text-white rounded-full p-0.5"
                            >
                                <CheckCircle2
                                    class="w-4 h-4 text-white fill-current"
                                />
                            </div>

                            <component
                                :is="getHallIcon(hall.name)"
                                class="w-8 h-8 mb-3 text-indigo-600"
                            />
                            <h3 class="font-bold text-gray-800">
                                {{ hall.name }}
                            </h3>
                            <p
                                class="text-sm text-gray-500 flex items-center gap-1 mt-1"
                            >
                                <MapPin class="w-4 h-4" />
                                {{ hall.location }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 text-right">
                        <button
                            @click="step = 2"
                            :disabled="!form.hall_ids.length"
                            class="bg-indigo-600 text-white px-6 py-3 rounded-xl disabled:opacity-50 transition-all font-semibold shadow-md shadow-indigo-600/10 hover:bg-indigo-700"
                        >
                            Continue <ArrowRight class="inline w-4 h-4 ml-1" />
                        </button>
                    </div>
                </div>

                <div v-if="step === 2" class="p-8">
                    <h2 class="text-2xl font-bold mb-6 text-center">
                        Select Visitor Type
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div
                            @click="setCategory('Normal')"
                            class="p-8 border rounded-2xl text-center cursor-pointer transition hover:shadow-lg hover:border-indigo-500"
                        >
                            <User
                                class="mx-auto w-10 h-10 mb-3 text-indigo-600"
                            />
                            <span class="font-semibold text-gray-700"
                                >Normal Visitor</span
                            >
                        </div>

                        <div
                            @click="setCategory('VIP')"
                            class="p-8 border rounded-2xl text-center cursor-pointer transition hover:shadow-lg hover:border-indigo-500"
                        >
                            <Crown
                                class="mx-auto w-10 h-10 mb-3 text-indigo-600"
                            />
                            <span class="font-semibold text-gray-700"
                                >VIP Visitor</span
                            >
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <button
                            @click="step = 1"
                            class="text-gray-500 hover:text-gray-700 font-medium"
                        >
                            <ArrowLeft class="inline w-4 h-4 mr-1" /> Back to
                            Galleries
                        </button>
                    </div>
                </div>

                <div v-if="step === 3" class="p-8">
                    <h2 class="text-2xl font-bold mb-6">Schedule Booking</h2>

                    <form
                        @submit.prevent="validateAndSubmit"
                        class="grid md:grid-cols-2 gap-6"
                    >
                        <div>
                            <label class="font-bold text-gray-700 block mb-1"
                                >Choice Date</label
                            >
                            <input
                                type="date"
                                v-model="form.booking_date"
                                :min="today"
                                class="w-full p-3 border rounded-xl focus:outline-indigo-600"
                            />

                            <label
                                class="font-bold mt-4 block text-gray-700 mb-1"
                                >How many Visitors?</label
                            >
                            <input
                                type="number"
                                v-model="form.number_of_visitors"
                                min="1"
                                max="50"
                                class="w-full p-3 border rounded-xl focus:outline-indigo-600"
                            />

                            <div
                                v-if="form.visitor_category === 'VIP'"
                                class="mt-4 p-4 border border-dashed rounded-xl bg-gray-50"
                            >
                                <label
                                    class="font-bold block text-gray-700 mb-2"
                                    >Upload Supporting Letter</label
                                >
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-gray-700 block mb-1"
                                >Time Slot</label
                            >
                            <div class="max-h-[300px] overflow-y-auto pr-1">
                                <div
                                    v-for="slot in [
                                        ...morningSlots,
                                        ...afternoonSlots,
                                    ]"
                                    :key="slot.id"
                                    @click="form.slot_id = slot.id"
                                    class="p-3 border rounded-xl mt-2 cursor-pointer transition-all text-sm font-medium text-gray-700"
                                    :class="
                                        form.slot_id === slot.id
                                            ? 'bg-indigo-50 border-indigo-600 text-indigo-900 ring-1 ring-indigo-600/30'
                                            : 'hover:bg-gray-50 border-gray-200'
                                    "
                                >
                                    {{ slot.label }} - {{ slot.time }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="md:col-span-2 flex justify-between items-center mt-6 pt-4 border-t"
                        >
                            <button
                                type="button"
                                @click="step = 2"
                                class="text-gray-500 hover:text-gray-700 font-medium"
                            >
                                <ArrowLeft class="inline w-4 h-4 mr-1" /> Back
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold transition-all hover:bg-indigo-700 disabled:opacity-50"
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
        </div>
    </VisitorLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
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
</style>
