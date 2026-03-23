<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { watch } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    // We use the direct URL path to ensure the browser sends a POST request
    // and bypasses any Ziggy route-list errors.
    form.post("/forgot-password", {
        onSuccess: () => {
            // Optional: Any logic after successful post
        },
        onFinish: () => {
            if (form.errors.email) {
                form.reset("email");
            }
        },
    });
};

// Listen for the 'status' prop from Laravel to trigger the SweetAlert
watch(
    () => props.status,
    (newStatus) => {
        if (newStatus) {
            Swal.fire({
                title: "Email Sent!",
                text: newStatus,
                icon: "success",
                confirmButtonColor: "#0f172a",
                customClass: {
                    confirmButton: "rounded-xl px-6 py-3 font-bold",
                },
            });
            form.reset();
        }
    },
);
</script>

<template>
    <div
        class="min-h-screen bg-[#f1f5f9] flex items-center justify-center p-6 font-sans"
    >
        <Head title="Forgot Password" />

        <div class="w-full max-w-[420px]">
            <div
                class="bg-white p-10 rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-slate-100"
            >
                <div class="mb-10 text-center">
                    <h2
                        class="text-3xl font-black text-slate-900 tracking-tight"
                    >
                        Reset Password
                    </h2>
                    <p
                        class="text-slate-400 text-sm font-medium mt-3 leading-relaxed"
                    >
                        Enter your email and we'll send you a secure link to get
                        back into your account.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="group">
                        <label
                            class="block text-sm font-bold text-slate-700 mb-2 ml-1"
                        >
                            Email Address*
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="e.g. eskedar@gmail.com"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all text-sm font-medium placeholder:text-slate-300"
                            :class="{
                                'border-red-300 bg-red-50/50':
                                    form.errors.email,
                            }"
                            required
                            autofocus
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-red-600 text-[11px] mt-1.5 font-bold"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="w-full bg-slate-950 text-white py-4 rounded-2xl font-bold text-sm hover:bg-black hover:shadow-2xl hover:shadow-black/20 transition-all active:scale-[0.97] disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <svg
                                v-if="form.processing"
                                class="animate-spin h-4 w-4 text-white"
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
                            {{
                                form.processing
                                    ? "Sending Link..."
                                    : "Send Reset Link"
                            }}
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-50 text-center">
                    <Link
                        href="/login"
                        class="text-sm font-bold text-slate-400 hover:text-slate-900 transition-colors flex items-center justify-center gap-2"
                    >
                        ← Back to Login
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
