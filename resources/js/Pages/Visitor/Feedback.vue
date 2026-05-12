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

const messageLength = computed(() => form.message.trim().length);

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
            let errorMsg = "Please check the form for errors.";

            if (errors.message) errorMsg = errors.message;
            else if (errors.booking_id) errorMsg = errors.booking_id;

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
        <template #header>
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-2"
            >
                <h1 class="text-lg md:text-xl font-extrabold text-slate-800">
                    Visit Feedback
                </h1>
                <span class="text-xs text-slate-400"
                    >Share your experience</span
                >
            </div>
        </template>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div
                class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden"
            >
                <!-- Header -->
                <div
                    class="p-6 md:p-8 bg-gradient-to-r from-indigo-50 to-white border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4"
                >
                    <div>
                        <h3
                            class="font-bold text-indigo-900 text-sm md:text-base"
                        >
                            Reviewing Visit #{{ booking.id }}
                        </h3>
                        <p class="text-xs text-indigo-600 mt-1">
                            {{ booking.booking_date }} •
                            {{ booking.hall_names }}
                        </p>
                    </div>
                    <span
                        class="px-3 py-1 bg-white rounded-full text-xs font-semibold text-indigo-600 shadow"
                    >
                        Verified Visit
                    </span>
                </div>

                <!-- Form -->
                <form
                    @submit.prevent="submitFeedback"
                    class="p-6 md:p-10 space-y-8"
                >
                    <!-- Rating -->
                    <div class="text-center space-y-3">
                        <label
                            class="text-xs font-semibold text-slate-500 uppercase tracking-wide"
                        >
                            Rate your experience
                        </label>
                        <div class="flex justify-center gap-2">
                            <label
                                v-for="star in 5"
                                :key="star"
                                class="cursor-pointer"
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
                                            ? 'text-yellow-400 scale-110'
                                            : 'text-slate-300'
                                    "
                                    class="text-3xl md:text-4xl transition"
                                    >★</span
                                >
                            </label>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <label
                                class="text-xs font-semibold text-slate-500 uppercase"
                                >Your Comment</label
                            >
                            <span
                                :class="
                                    messageLength < 10
                                        ? 'text-red-400'
                                        : 'text-green-500'
                                "
                                class="text-xs"
                            >
                                {{ messageLength }}/10
                            </span>
                        </div>

                        <textarea
                            v-model="form.message"
                            rows="5"
                            placeholder="Describe your experience clearly..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        ></textarea>

                        <p
                            v-if="form.errors.message"
                            class="text-red-500 text-xs"
                        >
                            {{ form.errors.message }}
                        </p>
                    </div>

                    <!-- Image Upload -->
                    <div class="space-y-3">
                        <label
                            class="text-xs font-semibold text-slate-500 uppercase"
                            >Upload Image (optional)</label
                        >
                        <input
                            type="file"
                            @change="handleImageChange"
                            accept="image/*"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"
                        />

                        <div v-if="previewUrl" class="relative w-fit">
                            <img
                                :src="previewUrl"
                                class="h-40 w-64 object-cover rounded-xl shadow"
                            />
                            <button
                                type="button"
                                @click="
                                    previewUrl = null;
                                    form.image = null;
                                "
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t"
                    >
                        <Link
                            :href="route('visitor.history')"
                            class="text-sm text-slate-500 hover:text-slate-700"
                        >
                            ← Back to History
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing || !isMessageValid"
                            class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold shadow hover:bg-indigo-700 disabled:opacity-50"
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
