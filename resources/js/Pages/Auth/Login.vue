<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const props = defineProps({
    status: String,
    canResetPassword: {
        type: Boolean,
        default: true,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const changeLanguage = (lang) => {
    locale.value = lang;
};

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head :title="t('login.title')" />

    <div class="fixed top-5 right-5 z-50 flex gap-2">
        <button
            @click="changeLanguage('en')"
            :class="
                locale === 'en'
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-slate-600'
            "
            class="px-3 py-1 rounded-lg text-xs font-bold shadow-sm border border-slate-200 transition-all"
        >
            EN
        </button>
        <button
            @click="changeLanguage('am')"
            :class="
                locale === 'am'
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-slate-600'
            "
            class="px-3 py-1 rounded-lg text-xs font-bold shadow-sm border border-slate-200 transition-all"
        >
            አማ
        </button>
        <button
            @click="changeLanguage('or')"
            :class="
                locale === 'or'
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-slate-600'
            "
            class="px-3 py-1 rounded-lg text-xs font-bold shadow-sm border border-slate-200 transition-all"
        >
            OR
        </button>
    </div>

    <div class="min-h-screen flex flex-col lg:flex-row bg-[#F8FAFC]">
        <div
            class="hidden lg:flex lg:w-1/2 relative bg-[#0F172A] text-white p-12 xl:p-16 items-center justify-center overflow-hidden"
        >
            <div
                class="absolute w-[600px] h-[600px] bg-blue-600 opacity-10 blur-[120px] rounded-full -top-40 -left-40"
            ></div>
            <div
                class="absolute w-[400px] h-[400px] bg-emerald-500 opacity-5 blur-[100px] rounded-full bottom-0 right-0"
            ></div>

            <div class="relative z-10 w-full max-w-lg">
                <div class="flex items-center gap-6">
                    <div
                        class="h-20 w-20 bg-white rounded-2xl shadow-2xl flex items-center justify-center p-3 shrink-0"
                    >
                        <img
                            src="/storage/images/adama.png"
                            class="w-full h-full object-contain"
                            alt="Logo"
                        />
                    </div>
                    <div>
                        <h2
                            class="text-3xl font-black tracking-tight leading-none uppercase"
                        >
                            {{ t("login.brand_city") }}
                            <span class="text-blue-400 font-light block mt-1">
                                {{ t("login.brand_admin") }}
                            </span>
                        </h2>
                        <div
                            class="w-12 h-1 bg-blue-500/40 my-4 rounded-full"
                        ></div>
                        <p
                            class="text-[11px] text-slate-400 uppercase font-bold tracking-[0.4em]"
                        >
                            {{ t("login.subtitle") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex flex-1 items-center justify-center p-4 sm:p-12 lg:p-20"
        >
            <div class="w-full max-w-md">
                <div class="flex flex-col items-center mb-8 lg:hidden">
                    <img
                        src="/storage/images/adama.png"
                        class="h-16 w-auto mb-4"
                        alt="Logo"
                    />
                    <h1
                        class="text-xl font-black text-slate-900 uppercase tracking-tight"
                    >
                        {{ t("login.brand_city") }}
                    </h1>
                    <p
                        class="text-[10px] text-slate-500 uppercase tracking-widest font-bold"
                    >
                        {{ t("login.subtitle") }}
                    </p>
                </div>

                <div
                    class="bg-white rounded-[2rem] sm:rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.04)] border border-slate-100 p-8 sm:p-12"
                >
                    <div class="mb-10 text-center sm:text-left">
                        <h2
                            class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"
                        >
                            {{ t("login.welcome") }}
                        </h2>
                        <p class="text-slate-400 text-sm mt-2 font-medium">
                            {{ t("login.description") }}
                        </p>
                    </div>

                    <div
                        v-if="status"
                        class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-bold border border-green-100"
                    >
                        {{ status }}
                    </div>

                    <form
                        @submit.prevent="submit"
                        class="space-y-5"
                        autocomplete="off"
                    >
                        <div class="space-y-2">
                            <label
                                class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1"
                            >
                                {{ t("login.email_label") }}
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                :placeholder="t('login.email_placeholder')"
                                class="w-full px-5 py-4 border-slate-200 rounded-2xl bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm transition-all duration-300"
                                :class="{ 'border-red-500': form.errors.email }"
                                required
                                autofocus
                            />
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1"
                            >
                                {{ t("login.password_label") }}
                            </label>
                            <div class="relative group">
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="w-full px-5 py-4 border-slate-200 rounded-2xl bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm transition-all duration-300"
                                    :class="{
                                        'border-red-500': form.errors.password,
                                    }"
                                    required
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors p-2"
                                >
                                    <svg
                                        v-if="!showPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex justify-end pr-1">
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-[11px] font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider"
                                >
                                    {{ t("login.forgot_password") }}
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center px-1">
                            <label
                                class="flex items-center gap-3 cursor-pointer group w-fit"
                            >
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="peer sr-only"
                                />
                                <div
                                    class="h-5 w-5 rounded-lg border-2 border-slate-200 bg-slate-50 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all duration-200 flex items-center justify-center group-hover:border-blue-400"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm text-slate-500 font-bold group-hover:text-slate-700 transition-colors"
                                >
                                    {{ t("login.remember_me") }}
                                </span>
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-[#0F172A] hover:bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-xl shadow-blue-900/10 transition-all duration-300 transform active:scale-[0.98] disabled:opacity-70 flex items-center justify-center gap-3"
                        >
                            <template v-if="!form.processing">
                                {{ t("login.sign_in_btn") }}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"
                                    />
                                </svg>
                            </template>
                            <template v-else>
                                <svg
                                    class="animate-spin h-5 w-5 text-white"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                {{ t("login.authenticating") }}
                            </template>
                        </button>
                    </form>

                    <div class="mt-8 text-center pt-6 border-t border-slate-50">
                        <p class="text-sm text-slate-500 font-medium">
                            {{ t("login.no_account") }}
                            <Link
                                :href="route('register')"
                                class="text-blue-600 font-bold hover:text-blue-700 ml-1 transition-colors underline underline-offset-4 decoration-2 decoration-blue-100 hover:decoration-blue-400"
                            >
                                {{ t("login.sign_up") }}
                            </Link>
                        </p>
                    </div>
                </div>

                <p
                    class="text-center text-[9px] sm:text-[10px] font-black text-slate-400 mt-10 uppercase tracking-[0.25em] px-4"
                >
                    {{ t("login.footer") }}
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
input::placeholder {
    @apply text-slate-300 transition-opacity;
}
input:focus::placeholder {
    @apply opacity-50;
}
::-webkit-scrollbar {
    width: 5px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    @apply bg-slate-200 rounded-full;
}
</style>
