<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import Swal from "sweetalert2";
import {
    User,
    Mail,
    Phone,
    Lock,
    ArrowRight,
    Loader2,
    Users,
    Eye,
    EyeOff,
} from "lucide-vue-next";

const { t } = useI18n();

// Component Logic
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    firstName: "",
    lastName: "",
    email: "",
    phone_no: "",
    visitorType: "",
    citizenship: "Ethiopian",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.clearErrors();

    let hasError = false;
    // Regex: Only letters, allows for Ethiopian names that might be single words
    const nameRegex = /^[A-Za-z]+$/;

    // First Name Validation
    if (!form.firstName || !nameRegex.test(form.firstName)) {
        form.setError(
            "firstName",
            "First name must contain only letters (no spaces or numbers).",
        );
        hasError = true;
    }

    // Last Name Validation (Fixed & Checked)
    if (!form.lastName || !nameRegex.test(form.lastName)) {
        form.setError(
            "lastName",
            "Last name must contain only letters (no spaces or numbers).",
        );
        hasError = true;
    }

    // Password Match Validation
    if (form.password !== form.password_confirmation) {
        Swal.fire({
            title: t("register.validation_failed") || "Validation Error",
            text: t("register.password_mismatch") || "Passwords do not match.",
            icon: "error",
            confirmButtonColor: "#2563eb",
        });
        return;
    }

    if (hasError) return;

    form.post(route("register"), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: t("register.welcome_title") || "Success!",
                text:
                    t("register.welcome_text") ||
                    "Your account has been created successfully.",
                icon: "success",
                confirmButtonColor: "#2563eb",
            }).then(() => {
                window.location.href = route("visitor.dashboard");
            });
        },
        onError: (errors) => {
            const errorList = Object.values(errors).join("<br>");
            Swal.fire({
                title: t("register.validation_failed") || "Registration Error",
                html: `<div style="text-align: left;">${errorList}</div>`,
                icon: "error",
                confirmButtonColor: "#2563eb",
            });
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head :title="t('register.title') || 'Create Account'" />

    <div class="flex min-h-screen bg-white font-sans overflow-x-hidden">
        <!-- Sidebar: Branding & Visuals (Hidden on mobile/tablet, visible on LG up) -->
        <aside
            class="relative hidden lg:flex lg:w-[35%] xl:w-[40%] flex-col justify-between p-12 text-white"
        >
            <!-- Background Image and Overlay -->
            <div
                class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat"
                style="
                    background-image: url(&quot;/storage/images/gallery8.jpg&quot;);
                "
            ></div>
            <div
                class="absolute inset-0 z-10 bg-gradient-to-br from-blue-900/90 to-slate-900/70"
            ></div>

            <div class="relative z-20 space-y-6">
                <div class="inline-block rounded-2xl bg-white p-2.5 shadow-xl">
                    <img
                        src="/storage/images/adama.png"
                        alt="Adama City Logo"
                        class="h-12 w-auto"
                    />
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-wide uppercase">
                        ADAMA CITY ADMINISTRATION
                    </h2>
                    <p class="text-sm font-medium text-slate-400">
                        Gallery Management System
                    </p>
                </div>
            </div>

            <div class="relative z-20">
                <h1
                    class="mb-5 text-4xl xl:text-5xl font-extrabold leading-tight"
                >
                    Welcome To Our Community
                </h1>
                <p class="text-lg leading-relaxed text-slate-300">
                    Sign up today to access the official Adama City gallery,
                    manage your visits, and stay updated with city events.
                </p>
            </div>

            <footer class="relative z-20 text-xs text-slate-400">
                © {{ new Date().getFullYear() }} Adama City Administration. All
                rights reserved.
            </footer>
        </aside>

        <!-- Main Content (Responsive) -->
        <main
            class="flex flex-1 items-center justify-center bg-slate-50 p-4 sm:p-8 md:p-12"
        >
            <div
                class="w-full max-w-[650px] rounded-[24px] md:rounded-[32px] bg-white p-6 sm:p-10 md:p-14 shadow-2xl shadow-slate-200"
            >
                <header class="mb-8">
                    <!-- Mobile Logo (Shows only when sidebar is hidden) -->
                    <div class="lg:hidden mb-6 flex justify-center">
                        <img
                            src="/storage/images/adama.png"
                            alt="Logo"
                            class="h-10 w-auto"
                        />
                    </div>
                    <h1
                        class="text-2xl sm:text-3xl font-extrabold text-slate-800 text-center lg:text-left"
                    >
                        {{ t("register.title") || "Create Account" }}
                    </h1>
                    <p
                        class="mt-2 text-sm sm:text-base text-slate-500 text-center lg:text-left"
                    >
                        {{
                            t("register.subtitle") ||
                            "Fill in the details below to get started"
                        }}
                    </p>
                </header>

                <form @submit.prevent="submit" class="space-y-5 sm:space-y-6">
                    <!-- Names Row -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="space-y-2">
                            <label
                                class="ml-1 text-sm font-semibold text-slate-600"
                                >First Name</label
                            >
                            <div class="relative flex items-center">
                                <User
                                    class="absolute left-4 h-5 w-5 text-slate-400"
                                />
                                <input
                                    v-model="form.firstName"
                                    type="text"
                                    placeholder="First Name"
                                    required
                                    class="w-full rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-4 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                    :class="{
                                        '!border-red-500 !bg-red-50':
                                            form.errors.firstName,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.firstName"
                                class="ml-1 text-xs text-red-500"
                            >
                                {{ form.errors.firstName }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="ml-1 text-sm font-semibold text-slate-600"
                                >Last Name</label
                            >
                            <div class="relative flex items-center">
                                <User
                                    class="absolute left-4 h-5 w-5 text-slate-400"
                                />
                                <input
                                    v-model="form.lastName"
                                    type="text"
                                    placeholder="Last Name"
                                    required
                                    class="w-full rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-4 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                    :class="{
                                        '!border-red-500 !bg-red-50':
                                            form.errors.lastName,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.lastName"
                                class="ml-1 text-xs text-red-500"
                            >
                                {{ form.errors.lastName }}
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="ml-1 text-sm font-semibold text-slate-600"
                            >Email Address</label
                        >
                        <div class="relative flex items-center">
                            <Mail
                                class="absolute left-4 h-5 w-5 text-slate-400"
                            />
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="example@gmail.com"
                                required
                                class="w-full rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-4 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                :class="{
                                    '!border-red-500 !bg-red-50':
                                        form.errors.email,
                                }"
                            />
                        </div>
                    </div>

                    <!-- Phone and Visitor Type Row -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="space-y-2">
                            <label
                                class="ml-1 text-sm font-semibold text-slate-600"
                                >Phone Number</label
                            >
                            <div class="relative flex items-center">
                                <Phone
                                    class="absolute left-4 h-5 w-5 text-slate-400"
                                />
                                <input
                                    v-model="form.phone_no"
                                    type="tel"
                                    placeholder="+251..."
                                    required
                                    class="w-full rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-4 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="ml-1 text-sm font-semibold text-slate-600"
                                >Visitor Type</label
                            >
                            <div class="relative flex items-center">
                                <Users
                                    class="absolute left-4 h-5 w-5 text-slate-400"
                                />
                                <select
                                    v-model="form.visitorType"
                                    required
                                    class="w-full appearance-none rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-10 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                >
                                    <option value="" disabled>
                                        Select Type
                                    </option>
                                    <option value="Local">Local Visitor</option>
                                    <option value="Foreign">
                                        Foreign Visitor
                                    </option>
                                </select>
                                <div
                                    class="pointer-events-none absolute right-4"
                                >
                                    <svg
                                        class="h-4 w-4 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Row -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="space-y-2">
                            <label
                                class="ml-1 text-sm font-semibold text-slate-600"
                                >Password</label
                            >
                            <div class="relative flex items-center">
                                <Lock
                                    class="absolute left-4 h-5 w-5 text-slate-400"
                                />
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    placeholder="••••••••"
                                    required
                                    class="no-eye w-full rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-11 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-4 text-slate-400 hover:text-blue-600 focus:outline-none"
                                >
                                    <Eye
                                        v-if="!showPassword"
                                        class="h-4 w-4 sm:h-5 sm:w-5"
                                    />
                                    <EyeOff
                                        v-else
                                        class="h-4 w-4 sm:h-5 sm:w-5"
                                    />
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="ml-1 text-sm font-semibold text-slate-600"
                                >Confirm Password</label
                            >
                            <div class="relative flex items-center">
                                <Lock
                                    class="absolute left-4 h-5 w-5 text-slate-400"
                                />
                                <input
                                    :type="
                                        showConfirmPassword
                                            ? 'text'
                                            : 'password'
                                    "
                                    v-model="form.password_confirmation"
                                    placeholder="••••••••"
                                    required
                                    class="no-eye w-full rounded-xl border-2 border-transparent bg-slate-100 py-3 pl-11 pr-11 text-sm transition-all focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10"
                                />
                                <button
                                    type="button"
                                    @click="
                                        showConfirmPassword =
                                            !showConfirmPassword
                                    "
                                    class="absolute right-4 text-slate-400 hover:text-blue-600 focus:outline-none"
                                >
                                    <Eye
                                        v-if="!showConfirmPassword"
                                        class="h-4 w-4 sm:h-5 sm:w-5"
                                    />
                                    <EyeOff
                                        v-else
                                        class="h-4 w-4 sm:h-5 sm:w-5"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="mt-4 flex w-full items-center justify-center gap-3 rounded-xl bg-blue-600 py-3.5 text-base sm:text-lg font-bold text-white transition-all hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="h-5 w-5 animate-spin"
                        />
                        <span v-else>Register Now</span>
                        <ArrowRight v-if="!form.processing" class="h-5 w-5" />
                    </button>
                </form>

                <div
                    class="mt-8 text-center text-sm sm:text-base text-slate-500"
                >
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="ml-1 font-bold text-blue-600 hover:underline transition-colors"
                        >Sign In</Link
                    >
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Remove standard browser password toggle to avoid double icons */
.no-eye::-ms-reveal,
.no-eye::-ms-clear {
    display: none;
}
/* Prevent zoom on input focus for iOS devices */
@media screen and (max-width: 768px) {
    input,
    select {
        font-size: 16px !important;
    }
}
</style>
