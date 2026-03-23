<script setup>
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    booking: Object,
});

const form = useForm({
    booking_id: props.booking.id,
    hall_id: props.booking.hall_id || null,
    type: "hall",
    subject: `Review for ${props.booking.hall_names || "General Visit"}`,
    message: "",
    rating: 5,
    image: null,
});

const previewUrl = ref(null);

// Character counter logic
const messageLength = computed(() => form.message.trim().length);

// This frontend check mirrors the backend "gibberish" check
const isMessageValid = computed(() => {
    const hasVowels = /[aeiouyAEIOUY]/.test(form.message);
    const hasLetters = /[a-zA-Z]/.test(form.message);
    return messageLength.value >= 10 && hasVowels && hasLetters;
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire(
                "File too large",
                "Please upload an image smaller than 2MB",
                "error",
            );
            return;
        }
        form.image = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submitFeedback = () => {
    // Prevent submission if frontend check fails
    if (!isMessageValid.value) {
        Swal.fire(
            "Invalid Message",
            "Please write a clear comment with at least 10 characters.",
            "warning",
        );
        return;
    }

    form.post(route("visitor.feedback.store"), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                title: "Thank You!",
                text: "Your feedback has been submitted successfully.",
                icon: "success",
                confirmButtonColor: "#4f46e5",
            });
            form.reset();
            previewUrl.value = null;
        },
        onError: (errors) => {
            // CRITICAL: This displays the 'unread text' error from Laravel
            let errorMsg = "Please check the form for errors.";

            if (errors.message) {
                errorMsg = errors.message;
            } else if (errors.booking_id) {
                errorMsg = errors.booking_id;
            }

            Swal.fire({
                title: "Submission Failed",
                text: errorMsg,
                icon: "error",
                confirmButtonColor: "#ef4444",
            });
        },
    });
};
</script>

<template>
    <Head title="Submit Feedback" />

    <VisitorLayout>
        <template #header>Visit Feedback</template>

        <div class="max-w-4xl mx-auto pb-20 animate-in">
            <div
                class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden"
            >
                <div
                    class="p-8 bg-indigo-50/50 border-b border-indigo-100 flex justify-between items-center"
                >
                    <div>
                        <h3
                            class="font-black text-indigo-900 uppercase text-sm tracking-tight"
                        >
                            Reviewing Visit #{{ booking.id }}
                        </h3>
                        <p class="text-xs text-indigo-600 font-bold mt-1">
                            {{ booking.booking_date }} —
                            {{ booking.hall_names }}
                        </p>
                    </div>
                    <span
                        class="px-4 py-1 bg-white rounded-full text-[10px] font-black text-indigo-600 uppercase shadow-sm"
                    >
                        Verified Visit
                    </span>
                </div>

                <form @submit.prevent="submitFeedback" class="p-10 space-y-10">
                    <div class="space-y-4 text-center">
                        <label
                            class="block text-[10px] font-black uppercase text-slate-400 tracking-widest"
                        >
                            How was your overall experience?
                        </label>
                        <div class="flex justify-center gap-2">
                            <label
                                v-for="star in 5"
                                :key="star"
                                class="cursor-pointer group"
                            >
                                <input
                                    type="radio"
                                    v-model="form.rating"
                                    :value="star"
                                    class="hidden"
                                />
                                <span
                                    :class="
                                        form.rating >= star
                                            ? 'text-amber-400 scale-110'
                                            : 'text-slate-200'
                                    "
                                    class="text-5xl transition-all duration-200 group-hover:scale-125 inline-block"
                                >
                                    ★
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <label
                                for="message"
                                class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                            >
                                Your Comments
                            </label>
                            <span
                                :class="
                                    messageLength < 10
                                        ? 'text-red-400'
                                        : 'text-green-500'
                                "
                                class="text-[9px] font-bold uppercase"
                            >
                                {{ messageLength }} / 10 characters min
                            </span>
                        </div>

                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="5"
                            :class="
                                form.errors.message
                                    ? 'ring-2 ring-red-500 border-red-500'
                                    : 'border-none'
                            "
                            class="w-full bg-slate-50 rounded-3xl p-6 font-medium focus:ring-2 focus:ring-indigo-500 transition-all"
                            placeholder="Please provide clear details about your visit..."
                        ></textarea>

                        <p
                            v-if="form.errors.message"
                            class="text-red-500 text-[10px] font-black uppercase mt-1 flex items-center gap-1"
                        >
                            <span>⚠️</span> {{ form.errors.message }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                        >
                            Upload a Photo (Optional)
                        </label>
                        <input
                            type="file"
                            @change="handleImageChange"
                            accept="image/*"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all"
                        />

                        <div
                            v-if="previewUrl"
                            class="mt-4 relative inline-block"
                        >
                            <img
                                :src="previewUrl"
                                class="h-48 w-72 object-cover rounded-[2rem] border-4 border-white shadow-lg"
                            />
                            <button
                                type="button"
                                @click="
                                    previewUrl = null;
                                    form.image = null;
                                "
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-2 shadow-lg hover:bg-red-600"
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
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-8 border-t border-slate-50"
                    >
                        <Link
                            :href="route('visitor.history')"
                            class="text-[10px] font-black uppercase text-slate-400 hover:text-slate-600 tracking-widest"
                        >
                            Back to History
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || !isMessageValid"
                            class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            {{
                                form.processing
                                    ? "Submitting..."
                                    : "Submit Feedback"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </VisitorLayout>
</template>
