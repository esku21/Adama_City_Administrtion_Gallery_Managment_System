<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    guide: Object,
    hall: Object,
});

// IMAGE UPLOAD
const fileInput = ref(null);
const previewUrl = ref(null);

const form = useForm({
    image: null,
    _method: "POST",
});

const triggerFileInput = () => {
    if (fileInput.value) fileInput.value.click();
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];

    if (file) {
        form.image = file;
        previewUrl.value = URL.createObjectURL(file);

        form.post(route("guide.profile.image"), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                previewUrl.value = null;
            },
        });
    }
};
</script>

<template>
    <Head :title="t('guide_nav.profile')" />

    <GuideLayout>
        <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <!-- HEADER -->
            <div class="mb-10">
                <h2
                    class="text-3xl sm:text-4xl font-black text-zinc-900 uppercase tracking-tight"
                >
                    {{ t("guide_nav.management") }}
                </h2>

                <p
                    class="text-[10px] sm:text-xs text-zinc-400 font-bold uppercase tracking-[0.3em] mt-2"
                >
                    {{ t("profile.subtitle") }} • Adama City Hall
                </p>
            </div>

            <!-- CARD -->
            <div
                class="bg-white rounded-3xl border border-zinc-100 shadow-lg overflow-hidden"
            >
                <!-- COVER -->
                <div class="h-32 bg-zinc-900 w-full relative">
                    <!-- PROFILE IMAGE -->
                    <div class="absolute -bottom-12 left-10">
                        <input
                            type="file"
                            ref="fileInput"
                            class="hidden"
                            @change="handleImageUpload"
                        />

                        <div
                            @click="triggerFileInput"
                            class="w-28 h-28 rounded-3xl bg-emerald-500 border-4 border-white flex items-center justify-center shadow-xl cursor-pointer group relative overflow-hidden"
                        >
                            <!-- Preview -->
                            <img
                                v-if="previewUrl"
                                :src="previewUrl"
                                class="w-full h-full object-cover"
                            />

                            <!-- Saved -->
                            <img
                                v-else-if="guide?.profile_image"
                                :src="'/storage/' + guide.profile_image"
                                class="w-full h-full object-cover"
                            />

                            <!-- Default -->
                            <span
                                v-else
                                class="material-icons-outlined text-white text-5xl"
                            >
                                person
                            </span>

                            <!-- Overlay -->
                            <div
                                class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                            >
                                <span
                                    class="material-icons-outlined text-white"
                                >
                                    photo_camera
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div class="pt-20 pb-10 px-8 sm:px-10">
                    <!-- NAME + STATUS -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start mb-8"
                    >
                        <div>
                            <h3
                                class="text-2xl sm:text-3xl font-black text-zinc-900 uppercase"
                            >
                                {{ guide?.name }}
                            </h3>

                            <p
                                class="text-emerald-600 font-bold text-[10px] uppercase mt-1"
                            >
                                {{ t("nav.guides") }} • ID #{{ guide?.id }}
                            </p>
                        </div>

                        <div
                            class="bg-zinc-50 px-6 py-2 rounded-2xl border border-zinc-100 text-center mt-4 sm:mt-0"
                        >
                            <p
                                class="text-[8px] font-black text-zinc-400 uppercase mb-1"
                            >
                                {{ t("guide_dashboard.table_status") }}
                            </p>

                            <span
                                class="text-xs font-black text-emerald-600 uppercase"
                            >
                                {{ t("guide_dashboard.live_monitoring") }}
                            </span>
                        </div>
                    </div>

                    <!-- INFO GRID -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-zinc-50 pt-8"
                    >
                        <!-- EMAIL -->
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400"
                            >
                                <span class="material-icons-outlined"
                                    >alternate_email</span
                                >
                            </div>

                            <div>
                                <p
                                    class="text-[9px] font-black text-zinc-400 uppercase"
                                >
                                    {{ t("profile.email") }}
                                </p>
                                <p class="font-bold text-zinc-800 text-sm">
                                    {{ guide?.email }}
                                </p>
                            </div>
                        </div>

                        <!-- PHONE -->
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400"
                            >
                                <span class="material-icons-outlined"
                                    >phone_iphone</span
                                >
                            </div>

                            <div>
                                <p
                                    class="text-[9px] font-black text-zinc-400 uppercase"
                                >
                                    {{ t("profile.phone") }}
                                </p>
                                <p class="font-bold text-zinc-800 text-sm">
                                    {{ guide?.phone }}
                                </p>
                            </div>
                        </div>

                        <!-- HALL -->
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"
                            >
                                <span class="material-icons-outlined"
                                    >location_city</span
                                >
                            </div>

                            <div>
                                <p
                                    class="text-[9px] font-black text-emerald-600 uppercase"
                                >
                                    {{ t("guide_nav.station") }}
                                </p>
                                <p class="font-bold text-zinc-800 text-sm">
                                    {{ hall?.name || t("guide_nav.no_hall") }}
                                </p>
                            </div>
                        </div>

                        <!-- GENDER -->
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400"
                            >
                                <span class="material-icons-outlined">wc</span>
                            </div>

                            <div>
                                <p
                                    class="text-[9px] font-black text-zinc-400 uppercase"
                                >
                                    {{ t("guide_dashboard.table_type") }}
                                </p>
                                <p class="font-bold text-zinc-800 text-sm">
                                    {{ guide?.gender }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="px-8 pb-8 text-center border-t border-zinc-50 pt-4">
                    <p class="text-[10px] text-zinc-400 font-bold uppercase">
                        {{ t("profile.security_footer") }}
                    </p>
                </div>
            </div>
        </div>
    </GuideLayout>
</template>
