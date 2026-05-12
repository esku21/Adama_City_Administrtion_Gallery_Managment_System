<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const page = usePage();
const user = computed(() => page.props.auth.user);
const photoPreview = ref(null);
const fileInput = ref(null);

const routePrefix = computed(() =>
    user.value?.role === "admin" ? "admin." : "visitor.",
);

const form = useForm({
    firstName: user.value?.firstName || "",
    lastName: user.value?.lastName || "",
    email: user.value?.email || "",
    phone_no: user.value?.phone_no || "",
    photo: null,
    _method: "PATCH",
});

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const updateProfile = () => {
    form.post(route(`${routePrefix.value}profile.update`), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            photoPreview.value = null;
            form.photo = null;
            if (fileInput.value) fileInput.value.value = null;
        },
    });
};
</script>

<template>
    <Head :title="t('admin_profile.title')" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto animate-in fade-in duration-700 p-4">
            <div
                class="bg-white border border-slate-200 p-6 md:p-8 rounded-3xl shadow-sm relative overflow-hidden mt-2"
            >
                <form
                    @submit.prevent="updateProfile"
                    class="space-y-6 relative z-10"
                >
                    <div
                        class="flex flex-col md:flex-row md:items-center gap-5 p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200"
                    >
                        <div class="relative inline-block">
                            <div
                                class="h-20 w-20 rounded-2xl bg-white border-2 border-white shadow-md overflow-hidden flex items-center justify-center"
                            >
                                <img
                                    v-if="photoPreview"
                                    :src="photoPreview"
                                    class="h-full w-full object-cover"
                                />
                                <img
                                    v-else-if="user.profile_photo_path"
                                    :src="'/storage/' + user.profile_photo_path"
                                    class="h-full w-full object-cover"
                                />
                                <span
                                    v-else
                                    class="material-icons-outlined text-slate-300 text-3xl"
                                    >person</span
                                >
                            </div>

                            <label
                                class="absolute -bottom-1 -right-1 h-7 w-7 bg-indigo-600 rounded-lg flex items-center justify-center text-white cursor-pointer shadow-md hover:bg-indigo-700 transition-all"
                            >
                                <span class="material-icons-outlined text-xs"
                                    >photo_camera</span
                                >
                                <input
                                    ref="fileInput"
                                    type="file"
                                    class="hidden"
                                    @change="handlePhotoChange"
                                    accept="image/*"
                                />
                            </label>
                        </div>

                        <div class="flex-1">
                            <h4
                                class="text-[10px] font-black uppercase text-slate-800 tracking-wider"
                            >
                                {{ t("admin_profile.avatar_title") }}
                            </h4>
                            <p class="text-[10px] text-slate-500 mt-1">
                                {{ t("admin_profile.avatar_help") }}
                            </p>
                            <div class="flex gap-2 mt-3">
                                <button
                                    type="button"
                                    @click="fileInput.click()"
                                    class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 bg-white border border-slate-200 rounded-md hover:bg-slate-100 transition-all"
                                >
                                    {{ t("admin_profile.change_photo") }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="label">{{
                                t("admin_profile.first_name")
                            }}</label>
                            <input
                                v-model="form.firstName"
                                type="text"
                                class="input-field"
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="label">{{
                                t("admin_profile.last_name")
                            }}</label>
                            <input
                                v-model="form.lastName"
                                type="text"
                                class="input-field"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="label">{{
                                t("admin_profile.phone")
                            }}</label>
                            <input
                                v-model="form.phone_no"
                                type="text"
                                placeholder="+251..."
                                class="input-field"
                            />
                            <div
                                v-if="form.errors.phone_no"
                                class="text-rose-500 text-[9px] font-bold uppercase mt-1"
                            >
                                {{ form.errors.phone_no }}
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="label">{{
                                t("admin_profile.email")
                            }}</label>
                            <input
                                v-model="form.email"
                                type="email"
                                readonly
                                class="input-field bg-slate-50 text-slate-400 border-dashed cursor-not-allowed"
                            />
                        </div>
                    </div>

                    <div class="pt-2 flex items-center">
                        <button
                            :disabled="form.processing"
                            class="btn-primary w-full md:w-auto md:px-10 group"
                        >
                            <span
                                class="flex items-center justify-center gap-2"
                            >
                                {{
                                    form.processing
                                        ? t("admin_profile.updating")
                                        : t("admin_profile.save")
                                }}
                                <span
                                    class="material-icons-outlined text-sm group-hover:translate-x-1 transition-transform"
                                    >arrow_forward</span
                                >
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.label {
    @apply text-[9px] font-black uppercase tracking-widest block ml-1 text-slate-500;
}
.input-field {
    @apply w-full border border-slate-200 rounded-xl py-3 px-5 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all;
}
.btn-primary {
    @apply bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-[0.2em] transition-all shadow-lg shadow-indigo-200 active:scale-[0.98] disabled:opacity-50;
}
</style>
