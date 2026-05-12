<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { Mail, ShieldCheck, ArrowRight, Loader2 } from "lucide-vue-next";
import Swal from "sweetalert2";

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"), {
        onStart: () => {
            // Optional: You could show a "Connecting to Mail Server..." toast here
        },
        onSuccess: (page) => {
            Swal.fire({
                icon: "success",
                title: "Email Sent!",
                text: "Please check your Gmail inbox for the reset link.",
                confirmButtonColor: "#0f172a",
                timer: 5000,
            });
            form.reset();
        },
        onError: (errors) => {
            // If the SMTP connection fails, Laravel usually sends a 500 error
            // or validation errors if the email doesn't exist.
            if (errors.email) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: errors.email,
                    confirmButtonColor: "#0f172a",
                });
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Mail Server Error",
                    text: "Could not connect to Gmail. Please check your internet or SMTP settings.",
                    confirmButtonColor: "#0f172a",
                });
            }
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-6">
        <Head title="Forgot Password" />

        <div
            class="w-full max-w-[440px] bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100"
        >
            <div class="mb-8 text-center">
                <div
                    class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center mx-auto mb-4"
                >
                    <ShieldCheck :size="32" />
                </div>
                <h2 class="text-3xl font-black text-slate-900">
                    Forgot Password?
                </h2>
                <p class="text-slate-400 text-sm mt-2">
                    Enter your email and we'll send you a reset link via Gmail.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label
                        class="block text-[10px] font-black uppercase text-slate-500 mb-2"
                    >
                        Email Address
                    </label>
                    <div class="relative">
                        <span
                            class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"
                        >
                            <Mail :size="18" />
                        </span>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="example@gmail.com"
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-900/5 transition-all"
                            :class="{ 'border-red-500': form.errors.email }"
                            required
                        />
                    </div>
                    <p
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-2 font-medium italic"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-black uppercase text-[11px] flex items-center justify-center gap-2 transition-all active:scale-[0.98] disabled:opacity-70"
                >
                    <Loader2
                        v-if="form.processing"
                        class="animate-spin"
                        :size="16"
                    />
                    <span>{{
                        form.processing
                            ? "Connecting to Gmail..."
                            : "Send Reset Link"
                    }}</span>
                    <ArrowRight v-if="!form.processing" :size="16" />
                </button>

                <div class="text-center mt-4">
                    <a
                        :href="route('login')"
                        class="text-[11px] font-bold text-slate-400 hover:text-slate-900 uppercase transition-colors"
                    >
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>
