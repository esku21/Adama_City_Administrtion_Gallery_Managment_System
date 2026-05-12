<script setup>
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    halls: Array,
});

const form = useForm({
    type: "general",
    hall_id: "",
    subject: "",
    message: "",
    rating: 5,
    sentiment: "Satisfaction",
    images: [], // Changed from image: null to images: []
});

const imagePreviews = ref([]);

// Update rating based on dropdown selection
watch(
    () => form.sentiment,
    (newVal) => {
        if (newVal === "Satisfaction") form.rating = 5;
        else if (newVal === "Neutral") form.rating = 3;
        else if (newVal === "Unsatisfactory") form.rating = 1;
    },
);

// Update dropdown selection based on star rating clicks
watch(
    () => form.rating,
    (newVal) => {
        if (newVal >= 4) form.sentiment = "Satisfaction";
        else if (newVal === 3) form.sentiment = "Neutral";
        else form.sentiment = "Unsatisfactory";
    },
);

watch(
    () => form.type,
    (newType) => {
        if (newType === "general") form.hall_id = "";
    },
);

/**
 * Image Processing: Resize and Compress
 */
const processImage = (file) => {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement("canvas");
                let width = img.width;
                let height = img.height;

                // Max width: 1200px
                if (width > 1200) {
                    height *= 1200 / width;
                    width = 1200;
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, width, height);

                // Compress to 0.7 quality
                canvas.toBlob(
                    (blob) => {
                        const processedFile = new File([blob], file.name, {
                            type: "image/jpeg",
                            lastModified: Date.now(),
                        });
                        resolve({
                            file: processedFile,
                            preview: URL.createObjectURL(processedFile),
                        });
                    },
                    "image/jpeg",
                    0.7,
                );
            };
        };
    });
};

const onFileChange = async (e) => {
    const files = Array.from(e.target.files);
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];

    // Limit to 3 images
    if (files.length + form.images.length > 3) {
        Swal.fire(
            "Error",
            "You can only upload a maximum of 3 images",
            "error",
        );
        return;
    }

    for (const file of files) {
        // Validate Type
        if (!allowedTypes.includes(file.type)) {
            Swal.fire(
                "Error",
                `${file.name} is not a supported format.`,
                "error",
            );
            continue;
        }

        // Validate Size (3MB)
        if (file.size > 3 * 1024 * 1024) {
            Swal.fire(
                "Error",
                `${file.name} is too large. Max size is 3MB.`,
                "error",
            );
            continue;
        }

        const processed = await processImage(file);
        form.images.push(processed.file);
        imagePreviews.value.push(processed.preview);
    }
};

const removeImage = (index) => {
    URL.revokeObjectURL(imagePreviews.value[index]);
    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const submit = () => {
    const msg = form.message.trim();
    const longConsonants = /[^aeiou\s]{6,}/i;
    const repetitions = /(.)\1{3,}/;
    const hasSpace = /\s/.test(msg);
    const hasVowels = /[aeiou]/i.test(msg);

    if (
        longConsonants.test(msg) ||
        repetitions.test(msg) ||
        !hasSpace ||
        !hasVowels
    ) {
        Swal.fire({
            title: t("feedback.error_gibberish_title"),
            text: t("feedback.error_gibberish_text"),
            icon: "error",
            confirmButtonColor: "#4f46e5",
        });
        return;
    }

    form.post(route("visitor.feedback.store"), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: t("feedback.success_title"),
                text: t("feedback.success_text"),
                icon: "success",
                confirmButtonColor: "#4f46e5",
            });
            form.reset();
            imagePreviews.value.forEach((p) => URL.revokeObjectURL(p));
            imagePreviews.value = [];
        },
    });
};
</script>

<template>
    <Head :title="$t('feedback.title')" />

    <VisitorLayout>
        <template #header>{{ $t("feedback.header") }}</template>

        <div class="max-w-4xl mx-auto px-4 pb-10">
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden"
            >
                <!-- Type Toggle -->
                <div class="bg-slate-50/50 p-6 border-b border-slate-100">
                    <div
                        class="flex bg-slate-200/60 p-1 rounded-2xl w-full max-w-xs mx-auto"
                    >
                        <button
                            @click="form.type = 'general'"
                            type="button"
                            class="flex-1 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all"
                            :class="
                                form.type === 'general'
                                    ? 'bg-white shadow-md text-indigo-700'
                                    : 'text-slate-600 hover:text-slate-800'
                            "
                        >
                            {{ $t("General Feedback") }}
                        </button>
                        <button
                            @click="form.type = 'hall'"
                            type="button"
                            class="flex-1 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all"
                            :class="
                                form.type === 'hall'
                                    ? 'bg-white shadow-md text-indigo-700'
                                    : 'text-slate-600 hover:text-slate-800'
                            "
                        >
                            {{ $t("feedback.type_hall") }}
                        </button>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-if="form.type === 'hall'" class="space-y-2">
                            <label
                                class="text-[11px] font-bold uppercase text-slate-500 tracking-widest ml-1"
                            >
                                {{ $t("feedback.label_hall") }}
                            </label>
                            <select
                                v-model="form.hall_id"
                                class="w-full bg-slate-50 border-none rounded-xl p-3 font-semibold text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500"
                                :class="{
                                    'ring-2 ring-red-500': form.errors.hall_id,
                                }"
                            >
                                <option value="">
                                    {{ $t("feedback.placeholder_hall") }}
                                </option>
                                <option
                                    v-for="hall in halls"
                                    :key="hall.id"
                                    :value="hall.id"
                                >
                                    {{ hall.name }}
                                </option>
                            </select>
                        </div>

                        <div
                            class="space-y-2"
                            :class="{
                                'md:col-span-2': form.type === 'general',
                            }"
                        >
                            <label
                                class="text-[11px] font-bold uppercase text-slate-500 tracking-widest ml-1"
                            >
                                {{ $t("feedback.label_subject") }}
                            </label>
                            <input
                                v-model="form.subject"
                                type="text"
                                class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500"
                                :placeholder="
                                    $t('feedback.placeholder_subject')
                                "
                                :class="{
                                    'ring-2 ring-red-500': form.errors.subject,
                                }"
                            />
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-indigo-50/40 rounded-2xl border border-indigo-100/50"
                    >
                        <div class="space-y-2">
                            <label
                                class="text-[11px] font-bold uppercase text-indigo-600 tracking-widest ml-1"
                                >Experience Category</label
                            >
                            <select
                                v-model="form.sentiment"
                                class="w-full bg-white border-slate-200 rounded-xl p-3 font-semibold text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="Satisfaction">
                                    Satisfaction
                                </option>
                                <option value="Neutral">Neutral</option>
                                <option value="Unsatisfactory">
                                    Unsatisfactory
                                </option>
                            </select>
                        </div>

                        <div
                            class="flex flex-col justify-center items-center md:items-end"
                        >
                            <span
                                class="text-[11px] font-bold uppercase text-slate-400 tracking-widest mb-2"
                                >Visual Rating</span
                            >
                            <div class="flex gap-1.5">
                                <button
                                    v-for="i in 5"
                                    :key="i"
                                    type="button"
                                    @click="form.rating = i"
                                    class="text-3xl transition-all hover:scale-125 focus:outline-none"
                                    :class="
                                        i <= form.rating
                                            ? 'text-amber-400 drop-shadow-sm'
                                            : 'text-slate-200'
                                    "
                                >
                                    ★
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label
                            class="text-[11px] font-bold uppercase text-slate-500 tracking-widest ml-1"
                            >{{ $t("feedback.label_message") }}</label
                        >
                        <textarea
                            v-model="form.message"
                            rows="5"
                            class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all"
                            :class="{
                                'ring-2 ring-red-500 bg-red-50/30':
                                    form.errors.message,
                            }"
                            :placeholder="$t('feedback.placeholder_message')"
                        ></textarea>
                    </div>

                    <!-- Image Upload & Actions -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <!-- Image Previews -->
                            <div
                                v-for="(preview, index) in imagePreviews"
                                :key="index"
                                class="relative group"
                            >
                                <img
                                    :src="preview"
                                    class="h-20 w-20 object-cover rounded-xl border border-indigo-200"
                                />
                                <button
                                    @click="removeImage(index)"
                                    type="button"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity"
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
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-6"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="relative"
                                    v-if="form.images.length < 3"
                                >
                                    <input
                                        type="file"
                                        @change="onFileChange"
                                        accept="image/jpeg,image/png,image/webp"
                                        multiple
                                        class="absolute inset-0 opacity-0 cursor-pointer"
                                    />
                                    <div
                                        class="px-4 py-2.5 bg-slate-100 rounded-lg text-[11px] font-bold uppercase text-slate-700 hover:bg-slate-200 transition-colors border border-slate-200 shadow-sm"
                                    >
                                        {{ $t("feedback.btn_add_photo") }} ({{
                                            form.images.length
                                        }}/3)
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 italic"
                                    >Max 3MB, JPG/PNG/WEBP</span
                                >
                            </div>

                            <div class="flex items-center gap-8">
                                <Link
                                    :href="route('visitor.dashboard')"
                                    class="text-[11px] font-bold uppercase text-slate-400 hover:text-red-500 transition-colors"
                                >
                                    {{ $t("feedback.btn_cancel") }}
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-10 py-3.5 bg-indigo-600 text-white rounded-xl font-bold uppercase text-[11px] tracking-widest shadow-lg shadow-indigo-200 hover:bg-indigo-700 disabled:opacity-50 transition-all active:scale-95"
                                >
                                    {{
                                        form.processing
                                            ? $t("feedback.sending")
                                            : $t("feedback.btn_submit")
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </VisitorLayout>
</template>
