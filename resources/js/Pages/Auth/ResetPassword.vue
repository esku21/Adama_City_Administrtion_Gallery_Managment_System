<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { Lock, ShieldCheck, ArrowRight } from "lucide-vue-next";

// These props are passed from your NewPasswordController@create method
const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    // Hits the 'store' method in your NewPasswordController
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <div
        class="min-h-screen bg-[#f1f5f9] flex items-center justify-center p-6 font-sans"
    >
        <Head title="Reset Password" />

        <div class="w-full max-w-[440px]">
            <div
                class="bg-white p-10 rounded-[3rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-slate-100"
            >
                <div class="mb-10 text-center">
                    <div
                        class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-sm"
                    >
                        <ShieldCheck :size="40" stroke-width="1.5" />
                    </div>
                    <h2
                        class="text-3xl font-black text-slate-900 tracking-tight"
                    >
                        New Password
                    </h2>
                    <p
                        class="text-slate-400 text-sm font-medium mt-3 leading-relaxed"
                    >
                        Update password for
                        <span class="text-slate-900 font-bold">{{
                            email
                        }}</span>
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <input type="hidden" v-model="form.token" />
                    <input type="hidden" v-model="form.email" />

                    <div class="group">
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1"
                        >
                            Choose Password
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"
                            >
                                <Lock :size="18" />
                            </span>
                            <input
                                v-model="form.password"
                                type="password"
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 outline-none transition-all text-sm font-bold"
                                :class="{
                                    'border-red-300 bg-red-50/50':
                                        form.errors.password,
                                }"
                                required
                                autocomplete="new-password"
                                autofocus
                            />
                        </div>
                        <p
                            v-if="form.errors.password"
                            class="text-red-600 text-[11px] mt-2 font-bold px-1"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="group">
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1"
                        >
                            Confirm Password
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"
                            >
                                <Lock :size="18" />
                            </span>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-600/5 focus:border-indigo-600 outline-none transition-all text-sm font-bold"
                                :class="{
                                    'border-red-300 bg-red-50/50':
                                        form.errors.password_confirmation,
                                }"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-red-600 text-[11px] mt-2 font-bold px-1"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="pt-4">
                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="w-full bg-slate-950 text-white py-5 rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                        >
                            {{
                                form.processing
                                    ? "Saving..."
                                    : "Update Password"
                            }}
                            <ArrowRight v-if="!form.processing" :size="18" />
                        </button>
                    </div>
                </form>

                <p
                    class="mt-8 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest"
                >
                    Adama City Admin Security
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.min-h-screen {
    animation: fadeIn 0.6s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
