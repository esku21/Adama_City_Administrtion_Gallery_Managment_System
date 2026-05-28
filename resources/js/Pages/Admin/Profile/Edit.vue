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
        <div
            class="max-w-5xl mx-auto animate-in fade-in duration-700 p-4 md:p-6"
        >
            <div
                class="bg-white border border-slate-200 p-8 md:p-12 rounded-3xl shadow-lg relative overflow-hidden mt-4"
            >
                <form
                    @submit.prevent="updateProfile"
                    class="space-y-10 relative z-10"
                >
                    <div
                        class="flex flex-col sm:flex-row items-center gap-6 p-6 bg-slate-50 rounded-3xl border border-dashed border-slate-300"
                    >
                        <div class="relative">
                            <div
                                class="h-28 w-28 rounded-3xl bg-white border-2 border-white shadow-xl overflow-hidden flex items-center justify-center"
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
                                    class="material-icons-outlined text-slate-300 text-5xl"
                                    >person</span
                                >
                            </div>
                            <label
                                class="absolute -bottom-2 -right-2 h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center text-white cursor-pointer shadow-lg hover:bg-indigo-700 transition-all border-4 border-white"
                            >
                                <span class="material-icons-outlined text-sm"
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

                        <div class="flex-1 text-center sm:text-left">
                            <h4
                                class="text-sm font-black uppercase text-slate-800 tracking-wider"
                            >
                                {{ t("admin_profile.avatar_title") }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm">
                                {{ t("admin_profile.avatar_help") }}
                            </p>
                            <button
                                type="button"
                                @click="fileInput.click()"
                                class="mt-4 text-xs font-bold uppercase tracking-widest px-6 py-2.5 bg-white border border-slate-300 rounded-xl hover:bg-slate-100 transition-all"
                            >
                                {{ t("admin_profile.change_photo") }}
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="label">{{
                                t("admin_profile.first_name")
                            }}</label>
                            <input
                                v-model="form.firstName"
                                type="text"
                                class="input-field"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="label">{{
                                t("admin_profile.last_name")
                            }}</label>
                            <input
                                v-model="form.lastName"
                                type="text"
                                class="input-field"
                            />
                        </div>
                        <div class="space-y-2">
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
                                class="text-rose-500 text-xs font-bold uppercase mt-1"
                            >
                                {{ form.errors.phone_no }}
                            </div>
                        </div>
                        <div class="space-y-2">
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

                    <div class="pt-4">
                        <button
                            :disabled="form.processing"
                            class="btn-primary w-full md:w-64"
                        >
                            <span
                                class="flex items-center justify-center gap-2"
                            >
                                {{
                                    form.processing
                                        ? t("admin_profile.updating")
                                        : t("admin_profile.save")
                                }}
                                <span class="material-icons-outlined text-base"
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
    @apply text-xs font-bold uppercase tracking-wider block ml-1 text-slate-600;
}
.input-field {
    @apply w-full border border-slate-300 rounded-2xl py-4 px-6 text-sm font-semibold text-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all;
}
.btn-primary {
    @apply bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-5 rounded-2xl text-sm uppercase tracking-[0.1em] transition-all shadow-xl shadow-indigo-200 active:scale-[0.98] disabled:opacity-50;
}
</style>
