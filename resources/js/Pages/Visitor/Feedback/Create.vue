<script setup>
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    halls: Array,
});

const form = useForm({
    type: "general",
    hall_id: "",
    subject: "",
    message: "",
    rating: 5,
    image: null,
});

const imagePreview = ref(null);

watch(
    () => form.type,
    (newType) => {
        if (newType === "general") form.hall_id = "";
    },
);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (imagePreview.value) URL.revokeObjectURL(imagePreview.value);
    form.image = file;
    if (file) imagePreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route("visitor.feedback.store"), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire("Success!", "Feedback sent successfully.", "success");
            form.reset();
            imagePreview.value = null;
        },
    });
};
</script>

<template>
    <Head title="Send Feedback" />

    <VisitorLayout>
        <template #header>Visitor Feedback</template>

        <div class="max-w-4xl mx-auto pb-20">
            <div
                class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden p-8 sm:p-12"
            >
                <div
                    class="flex mb-10 bg-slate-100 p-1.5 rounded-3xl w-full max-w-md mx-auto"
                >
                    <button
                        @click="form.type = 'general'"
                        type="button"
                        class="flex-1 py-3 text-xs font-black uppercase tracking-widest rounded-2xl transition-all"
                        :class="
                            form.type === 'general'
                                ? 'bg-white shadow-sm text-indigo-600'
                                : 'text-slate-500 hover:text-slate-700'
                        "
                    >
                        General
                    </button>
                    <button
                        @click="form.type = 'hall'"
                        type="button"
                        class="flex-1 py-3 text-xs font-black uppercase tracking-widest rounded-2xl transition-all"
                        :class="
                            form.type === 'hall'
                                ? 'bg-white shadow-sm text-indigo-600'
                                : 'text-slate-500 hover:text-slate-700'
                        "
                    >
                        Hall Specific
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div
                        v-if="form.type === 'hall'"
                        class="space-y-2 animate-in"
                    >
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                            >Select Hall</label
                        >
                        <select
                            v-model="form.hall_id"
                            class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="">Choose a location...</option>
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
                        class="space-y-4 text-center py-4 bg-slate-50/50 rounded-[2rem]"
                    >
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                            >Rate your experience</label
                        >
                        <div class="flex justify-center gap-2">
                            <button
                                v-for="i in 5"
                                :key="i"
                                type="button"
                                @click="form.rating = i"
                                class="text-4xl transition-all hover:scale-120"
                                :class="
                                    i <= form.rating
                                        ? 'text-amber-400'
                                        : 'text-slate-200'
                                "
                            >
                                ★
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                            >Subject</label
                        >
                        <input
                            v-model="form.subject"
                            type="text"
                            placeholder="e.g., Amazing Guide, Facility Quality"
                            class="w-full bg-slate-50 border-none rounded-2xl p-4 font-medium focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                            >Your Message</label
                        >
                        <textarea
                            v-model="form.message"
                            rows="5"
                            class="w-full bg-slate-50 border-none rounded-3xl p-6 font-medium focus:ring-2 focus:ring-indigo-500"
                            placeholder="Tell us more..."
                        ></textarea>
                    </div>

                    <div class="space-y-4">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest"
                            >Attachment (Optional)</label
                        >
                        <div class="flex items-center gap-4">
                            <input
                                type="file"
                                @change="onFileChange"
                                accept="image/*"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"
                            />
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                class="h-16 w-16 object-cover rounded-xl border-2 border-white shadow-md"
                            />
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-6 border-t border-slate-50"
                    >
                        <Link
                            :href="route('visitor.dashboard')"
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest hover:text-indigo-600"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg hover:bg-indigo-700 disabled:opacity-50 transition-all"
                        >
                            {{
                                form.processing
                                    ? "Sending..."
                                    : "Submit Feedback"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </VisitorLayout>
</template>
