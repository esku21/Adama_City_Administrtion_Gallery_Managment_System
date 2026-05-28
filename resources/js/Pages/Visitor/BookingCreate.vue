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
    MapPin,
    Languages,
    Home,
    HelpCircle,
} from "lucide-vue-next";

// Destructure t and locale from vue-i18n
const { t, locale } = useI18n();
const page = usePage();

const props = defineProps({
    halls: {
        type: Array,
        default: () => [],
    },
});

const step = ref(1);

/**
 * LANGUAGE SWITCHER HANDLER
 */
const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("locale", lang); // Persist selection across reloads
};

/**
 * GLOBAL ERROR HANDLER
 */
watch(
    () => page.props.flash?.error,
    (message) => {
        if (message) {
            Swal.fire({
                icon: "error",
                title: t("visitor_booking.alert_restricted_title"),
                text: message,
                confirmButtonColor: "#4f46e5",
            });
        }
    },
    { immediate: true },
);

const steps = computed(() => [
    { number: 1, label: t("visitor_booking.step_select_hall") },
    { number: 2, label: t("visitor_booking.step_visitor_type") },
    { number: 3, label: t("visitor_booking.step_schedule") },
]);

/**
 * FIXED: Mapping updated to handle all explicit dynamic galleries from database securely
 */
const getHallIcon = (name) => {
    const map = {
        "Adama City Hall": Building2,
        "Aba Gadaa Cultural Hall": Palmtree,
        "Municipal Council Chamber": Gavel,
        "Investment Promotion Hall": TrendingUp,
        "Revenue Service Hall": Banknote,
        "Land Management Hall": Home,
        "Multi-Purpose Hall": Layers,
    };
    return map[name] || HelpCircle;
};

const morningSlots = computed(() => [
    {
        id: "m1",
        time: "09:00 AM - 09:30 AM",
        label: t("visitor_booking.slot_early_morning"),
    },
    {
        id: "m2",
        time: "10:00 AM - 10:30 AM",
        label: t("visitor_booking.slot_mid_morning"),
    },
    {
        id: "m3",
        time: "11:00 AM - 11:30 AM",
        label: t("visitor_booking.slot_late_morning"),
    },
]);

const afternoonSlots = computed(() => [
    {
        id: "a1",
        time: "02:00 PM - 02:30 PM",
        label: t("visitor_booking.slot_early_afternoon"),
    },
    {
        id: "a2",
        time: "03:00 PM - 03:30 PM",
        label: t("visitor_booking.slot_mid_afternoon"),
    },
    {
        id: "a3",
        time: "04:00 PM - 04:30 PM",
        label: t("visitor_booking.slot_late_afternoon"),
    },
]);

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

const toggleHall = (id) => {
    const index = form.hall_ids.indexOf(id);
    if (index > -1) {
        form.hall_ids.splice(index, 1);
    } else {
        form.hall_ids.push(id);
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
            t("visitor_booking.alert_select_hall_title"),
            t("visitor_booking.alert_select_hall_text"),
            "warning",
        );
    }

    if (form.booking_date && !isWeekday.value) {
        return Swal.fire(
            t("visitor_booking.alert_weekend_title"),
            t("visitor_booking.alert_weekend_text"),
            "error",
        );
    }

    if (form.visitor_category === "VIP" && !form.attachment) {
        return Swal.fire(
            t("visitor_booking.alert_missing_doc_title"),
            t("visitor_booking.alert_missing_doc_text"),
            "info",
        );
    }

    if (!form.slot_id) {
        return Swal.fire(
            t("visitor_booking.alert_select_slot_title"),
            t("visitor_booking.alert_select_slot_text"),
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
                title:
                    page?.props?.flash?.success ||
                    t("visitor_booking.alert_success_text"),
                confirmButtonColor: "#4f46e5",
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: t("visitor_booking.alert_failed_title"),
                text: t("visitor_booking.alert_failed_text"),
            });
        },
    });
};
</script>

<template>
    <Head :title="`${$t('visitor_booking.title')} - ACAGMS`" />

    <VisitorLayout>
        <div class="max-w-6xl mx-auto px-4 py-6 animate-fade-in">
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-4 border-b border-gray-100"
            >
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                        {{
                            step === 1
                                ? $t("visitor_booking.header_choose_halls")
                                : $t("visitor_booking.title")
                        }}
                    </h1>
                </div>

                <div
                    class="flex items-center gap-1 bg-gray-100/80 p-1.5 rounded-xl border border-gray-200/50"
                >
                    <div
                        class="flex items-center gap-1.5 px-2 text-gray-500 border-r border-gray-200 mr-1"
                    >
                        <Languages class="w-4 h-4 text-gray-500" />
                    </div>

                    <button
                        type="button"
                        @click="changeLanguage('or')"
                        class="px-3 py-1 text-xs font-semibold rounded-lg transition-all duration-200"
                        :class="
                            locale === 'or'
                                ? 'bg-white text-indigo-600 shadow-sm font-bold'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'
                        "
                    >
                        Oromoo
                    </button>

                    <button
                        type="button"
                        @click="changeLanguage('am')"
                        class="px-3 py-1 text-xs font-semibold rounded-lg transition-all duration-200"
                        :class="
                            locale === 'am'
                                ? 'bg-white text-indigo-600 shadow-sm font-bold'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'
                        "
                    >
                        አማርኛ
                    </button>

                    <button
                        type="button"
                        @click="changeLanguage('en')"
                        class="px-3 py-1 text-xs font-semibold rounded-lg transition-all duration-200"
                        :class="
                            locale === 'en'
                                ? 'bg-white text-indigo-600 shadow-sm font-bold'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-white/50'
                        "
                    >
                        English
                    </button>
                </div>
            </div>

            <div class="flex justify-between mb-10 max-w-2xl mx-auto relative">
                <div
                    v-for="s in steps"
                    :key="s.number"
                    class="flex-1 text-center z-10"
                >
                    <div
                        class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold shadow-sm transition-all"
                        :class="
                            step >= s.number
                                ? 'bg-indigo-600 text-white ring-4 ring-indigo-100'
                                : 'bg-white text-gray-400 border'
                        "
                    >
                        <CheckCircle2 v-if="step > s.number" :size="20" />
                        <span v-else class="text-sm">{{ s.number }}</span>
                    </div>
                    <p
                        class="text-[11px] mt-2 font-bold uppercase tracking-wider"
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

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden"
            >
                <div v-if="step === 1" class="p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">
                            {{ $t("visitor_booking.header_choose_halls") }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            {{ $t("visitor_booking.subtitle_choose_halls") }}
                        </p>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div
                            v-for="hall in props.halls"
                            :key="hall.id"
                            @click="toggleHall(hall.id)"
                            class="p-5 border rounded-xl cursor-pointer transition-all duration-200 relative overflow-hidden group select-none"
                            :class="
                                form.hall_ids.includes(hall.id)
                                    ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600'
                                    : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50/50'
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

                            <div
                                class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform duration-200"
                            >
                                <component
                                    :is="getHallIcon(hall.name)"
                                    class="w-5 h-5 text-indigo-600"
                                />
                            </div>
                            <h3 class="font-bold text-gray-800 text-base">
                                {{ hall.name }}
                            </h3>
                            <p
                                class="text-xs text-gray-500 flex items-center gap-1 mt-1.5"
                            >
                                <MapPin class="w-3.5 h-3.5 text-gray-400" />
                                {{ hall.location }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-100 text-right">
                        <button
                            type="button"
                            @click="step = 2"
                            :disabled="!form.hall_ids.length"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg disabled:opacity-50 transition-all font-semibold shadow-sm hover:bg-indigo-700 flex items-center gap-1 ml-auto text-sm"
                        >
                            {{ $t("visitor_booking.btn_continue") }}
                            <ArrowRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div v-if="step === 2" class="p-8">
                    <h2
                        class="text-xl font-bold mb-6 text-center text-gray-800"
                    >
                        {{ $t("visitor_booking.header_visitor_type") }}
                    </h2>

                    <div class="grid sm:grid-cols-2 gap-5 max-w-2xl mx-auto">
                        <div
                            @click="setCategory('Normal')"
                            class="p-6 border border-gray-200 rounded-xl text-center cursor-pointer transition-all duration-200 hover:shadow-md hover:border-indigo-500 hover:bg-indigo-50/10 group"
                        >
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-3 group-hover:scale-105 transition-transform"
                            >
                                <User class="w-6 h-6 text-indigo-600" />
                            </div>
                            <span
                                class="font-bold text-gray-800 block text-base"
                            >
                                {{ $t("visitor_booking.category_normal") }}
                            </span>
                        </div>

                        <div
                            @click="setCategory('VIP')"
                            class="p-6 border border-gray-200 rounded-xl text-center cursor-pointer transition-all duration-200 hover:shadow-md hover:border-indigo-500 hover:bg-indigo-50/10 group"
                        >
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-3 group-hover:scale-105 transition-transform"
                            >
                                <Crown class="w-6 h-6 text-indigo-600" />
                            </div>
                            <span
                                class="font-bold text-gray-800 block text-base"
                            >
                                {{ $t("visitor_booking.category_vip") }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-100 text-center">
                        <button
                            type="button"
                            @click="step = 1"
                            class="text-sm text-gray-500 hover:text-gray-700 font-medium inline-flex items-center gap-1"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            {{ $t("visitor_booking.btn_back_halls") }}
                        </button>
                    </div>
                </div>

                <div v-if="step === 3" class="p-8">
                    <h2 class="text-xl font-bold mb-6 text-gray-800">
                        {{ $t("visitor_booking.header_schedule") }}
                    </h2>

                    <form
                        @submit.prevent="validateAndSubmit"
                        class="grid md:grid-cols-2 gap-6"
                    >
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="text-sm font-bold text-gray-700 block mb-1.5"
                                >
                                    {{ $t("visitor_booking.label_date") }}
                                </label>
                                <input
                                    type="date"
                                    v-model="form.booking_date"
                                    :min="today"
                                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none text-sm text-gray-800"
                                />
                            </div>

                            <div>
                                <label
                                    class="text-sm font-bold text-gray-700 block mb-1.5"
                                >
                                    {{
                                        $t(
                                            "visitor_booking.label_visitor_count",
                                        )
                                    }}
                                </label>
                                <input
                                    type="number"
                                    v-model="form.number_of_visitors"
                                    min="1"
                                    max="50"
                                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none text-sm text-gray-800"
                                />
                            </div>

                            <div
                                v-if="form.visitor_category === 'VIP'"
                                class="p-4 border border-dashed border-indigo-200 rounded-xl bg-indigo-50/20 animate-fade-in"
                            >
                                <label
                                    class="text-xs font-bold block text-indigo-900 mb-2"
                                >
                                    {{
                                        $t(
                                            "visitor_booking.label_upload_letter",
                                        )
                                    }}
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    class="text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer text-gray-600"
                                />
                            </div>
                        </div>

                        <div>
                            <label
                                class="text-sm font-bold text-gray-700 block mb-1.5"
                            >
                                {{ $t("visitor_booking.label_time_slot") }}
                            </label>
                            <div
                                class="max-h-[260px] overflow-y-auto border border-gray-200 rounded-xl p-2 space-y-2 bg-gray-50/50"
                            >
                                <div
                                    v-for="slot in [
                                        ...morningSlots,
                                        ...afternoonSlots,
                                    ]"
                                    :key="slot.id"
                                    @click="form.slot_id = slot.id"
                                    class="p-2.5 border rounded-lg cursor-pointer transition-all duration-150 text-xs font-semibold flex justify-between items-center"
                                    :class="
                                        form.slot_id === slot.id
                                            ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm'
                                            : 'hover:bg-white border-gray-200 bg-white text-gray-700'
                                    "
                                >
                                    <span>{{ slot.label }}</span>
                                    <span
                                        :class="
                                            form.slot_id === slot.id
                                                ? 'text-indigo-100'
                                                : 'text-gray-500'
                                        "
                                        >{{ slot.time }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="md:col-span-2 flex justify-between items-center mt-6 pt-4 border-t border-gray-100"
                        >
                            <button
                                type="button"
                                @click="step = 2"
                                class="text-sm text-gray-500 hover:text-gray-700 font-medium inline-flex items-center gap-1"
                            >
                                <ArrowLeft class="w-4 h-4" />
                                {{ $t("visitor_booking.btn_back") }}
                            </button>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all hover:bg-indigo-700 disabled:opacity-50 shadow-sm"
                            >
                                {{
                                    form.processing
                                        ? $t("visitor_booking.btn_processing")
                                        : $t("visitor_booking.btn_confirm")
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
    animation: fadeIn 0.35s ease-out;
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

/* Custom scrollbar styling for high fidelity UI */
::-webkit-scrollbar {
    width: 5px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
