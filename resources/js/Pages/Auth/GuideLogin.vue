<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3"; // Added Link
import { useI18n } from "vue-i18n";
import { ref, onMounted } from "vue";
import {
    Loader2,
    Building2,
    Mail,
    Lock,
    Eye,
    EyeOff,
    Globe,
} from "lucide-vue-next";

const { t, locale } = useI18n();

const props = defineProps({
    halls: Array,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    hall_id: "",
    remember: false,
});

const showPassword = ref(false);
const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

onMounted(() => {
    const savedLang = localStorage.getItem("lang");
    if (savedLang) locale.value = savedLang;
});

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("lang", lang);
};

const submit = () => {
    form.post(route("guide.login.submit"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head :title="t('guide_login.title')" />

    <div class="min-h-screen flex">
        <div
            class="hidden md:flex w-1/2 bg-slate-100 items-center justify-center"
        >
            <img
                src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                alt="illustration"
                class="w-80 opacity-90"
            />
        </div>

        <div
            class="flex-1 flex items-center justify-center bg-blue-600 relative px-4"
        >
            <div
                class="absolute top-5 right-5 flex items-center gap-2 text-white"
            >
                <Globe class="w-4 h-4" />
                <select
                    @change="changeLanguage($event.target.value)"
                    :value="locale"
                    class="bg-transparent border border-white/30 text-white text-xs rounded px-2 py-1 outline-none"
                >
                    <option value="en" class="text-black">EN</option>
                    <option value="am" class="text-black">AM</option>
                    <option value="or" class="text-black">OR</option>
                </select>
            </div>

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8">
                <h2 class="text-xl font-bold text-slate-800 mb-1">Hello!</h2>
                <p class="text-xs text-slate-400 mb-6">
                    {{ t("guide_login.subtitle") }}
                </p>

                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="relative">
                        <Building2
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                        />
                        <select
                            v-model="form.hall_id"
                            required
                            class="w-full pl-10 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200"
                        >
                            <option value="" disabled>
                                {{ t("guide_login.hall_placeholder") }}
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

                    <div class="relative">
                        <Mail
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                        />
                        <input
                            v-model="form.email"
                            type="email"
                            :placeholder="t('login.email_label')"
                            required
                            class="w-full pl-10 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200"
                        />
                    </div>

                    <div class="relative">
                        <Lock
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                        />
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Password"
                            required
                            class="w-full pl-10 pr-10 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-slate-200"
                        />
                        <button
                            type="button"
                            @click="togglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600"
                        >
                            <Eye v-if="!showPassword" class="w-4 h-4" />
                            <EyeOff v-else class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="flex justify-end">
                        <Link
                            :href="route('guide.password.request')"
                            class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors uppercase tracking-tight"
                        >
                            Forgot Password?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 transition-all active:scale-[0.98]"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="w-4 h-4 animate-spin"
                        />
                        {{
                            form.processing
                                ? t("guide_login.btn_verifying")
                                : t("guide_login.btn_access")
                        }}
                    </button>
                </form>

                <p
                    class="text-xs text-center text-slate-400 mt-6 cursor-pointer hover:text-slate-600"
                >
                    {{ t("guide_login.back_link") }}
                </p>
            </div>
        </div>
    </div>
</template>
