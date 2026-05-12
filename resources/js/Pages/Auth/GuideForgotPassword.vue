<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { Mail, Loader2, ChevronLeft, ShieldCheck } from "lucide-vue-next";

const form = useForm({ email: "" });

const submit = () => {
    form.post(route("guide.password.email"));
};
</script>

<template>
    <Head title="Reset Security Access" />
    <div class="min-h-screen flex bg-[#0F172A] items-center justify-center p-6">
        <div
            class="w-full max-w-[420px] bg-white rounded-[2.5rem] p-10 shadow-2xl"
        >
            <div class="mb-8 text-center">
                <div class="inline-flex p-3 rounded-2xl bg-blue-50 mb-4">
                    <ShieldCheck class="w-8 h-8 text-blue-600" />
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                    Forgot Password?
                </h2>
                <p
                    class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2"
                >
                    Identity Verification Required
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-1.5">
                    <label
                        class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1"
                        >Email Address</label
                    >
                    <div class="relative group">
                        <Mail
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-400 group-focus-within:text-blue-600 transition-colors"
                        />
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Enter your registered email"
                            required
                            class="w-full pl-12 pr-4 py-4 text-sm bg-slate-50 border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 outline-none transition-all"
                        />
                    </div>
                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-[11px] font-bold mt-1 ml-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-[#0F172A] hover:bg-black text-white py-4 rounded-2xl text-sm font-black shadow-lg transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                >
                    <Loader2
                        v-if="form.processing"
                        class="w-5 h-5 animate-spin"
                    />
                    <span>{{
                        form.processing
                            ? "Sending..."
                            : "Send Verification Email"
                    }}</span>
                </button>
            </form>

            <div
                class="mt-8 pt-6 border-t border-slate-100 flex justify-center"
            >
                <Link
                    :href="route('guide.login')"
                    class="inline-flex items-center gap-2 text-[13px] font-bold text-slate-400 hover:text-blue-600 transition-colors group"
                >
                    <ChevronLeft
                        class="w-4 h-4 group-hover:-translate-x-1 transition-transform"
                    />
                    <span>Back to Secure Login</span>
                </Link>
            </div>
        </div>
    </div>
</template>
