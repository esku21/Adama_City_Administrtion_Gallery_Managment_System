<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import Swal from "sweetalert2";

const form = useForm({
    firstName: "",
    lastName: "",
    email: "",
    phone_no: "",
    visitorType: "Local",
    citizenship: "Ethiopian",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onSuccess: () => {
            Swal.fire({
                title: "Welcome!",
                text: "Your account has been created successfully. Ready to explore the gallery?",
                icon: "success",
                confirmButtonColor: "#1e3a8a",
                confirmButtonText: "Open Dashboard",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = route("visitor.dashboard");
                }
            });
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors)[0];
            Swal.fire({
                title: "Validation Failed",
                text: errorMsg || "Please check the form for errors.",
                icon: "error",
                confirmButtonColor: "#0f172a",
            });
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Visitor Registration | Adama City Gallery" />

    <div
        class="min-h-screen bg-[#fcfdfe] flex font-sans antialiased selection:bg-blue-100"
    >
        <div
            class="hidden lg:flex lg:w-[35%] bg-[#020617] relative items-center justify-center p-12 overflow-hidden shrink-0"
        >
            <div
                class="absolute top-[-20%] left-[-20%] w-[600px] h-[600px] bg-blue-600 rounded-full blur-[160px] opacity-[0.1]"
            ></div>
            <div
                class="absolute bottom-[-10%] right-[0%] w-[400px] h-[400px] bg-indigo-500 rounded-full blur-[140px] opacity-[0.06]"
            ></div>
            <div
                class="absolute inset-0 opacity-[0.02]"
                style="
                    background-image:
                        linear-gradient(#fff 1.5px, transparent 1.5px),
                        linear-gradient(90deg, #fff 1.5px, transparent 1.5px);
                    background-size: 40px 40px;
                "
            ></div>

            <div class="relative z-10 w-full max-w-xs text-center lg:text-left">
                <div
                    class="flex items-center justify-center lg:justify-start gap-5 mb-16"
                >
                    <div
                        class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-2xl aspect-square shrink-0"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Adama Logo"
                            class="w-10 h-10 object-contain"
                        />
                    </div>
                    <div>
                        <p
                            class="text-white font-black text-xl tracking-tighter leading-none uppercase"
                        >
                            Adama City
                        </p>
                        <p
                            class="text-blue-500 text-[8px] font-bold tracking-[0.4em] uppercase mt-1"
                        >
                            Visitor Gateway
                        </p>
                    </div>
                </div>

                <h2
                    class="text-4xl font-black text-white mb-6 tracking-tight leading-[1.1]"
                >
                    Join the <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300"
                        >Cultural Hub</span
                    >
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-10">
                    Register to access priority bookings, digital tours, and
                    historical archives.
                </p>

                <div class="pt-8 border-t border-white/5">
                    <p
                        class="text-[9px] font-black text-slate-600 uppercase tracking-[0.3em]"
                    >
                        Institutional Grade Security
                    </p>
                </div>
            </div>
        </div>

        <div
            class="flex-1 flex items-center justify-center p-6 lg:p-12 overflow-y-auto"
        >
            <div class="w-full max-w-2xl py-10">
                <div
                    class="bg-white p-8 lg:p-14 rounded-[3rem] shadow-[0_20px_50px_-15px_rgba(0,0,0,0.04)] border border-slate-100"
                >
                    <header class="mb-12 text-center">
                        <h1
                            class="text-2xl font-black text-slate-900 tracking-tight"
                        >
                            Create Visitor Account
                        </h1>
                        <p
                            class="text-slate-400 text-[10px] font-black mt-3 uppercase tracking-[0.2em]"
                        >
                            Institutional Registration Protocol
                        </p>
                        <div
                            class="w-12 h-1.5 bg-blue-600 mx-auto mt-5 rounded-full"
                        ></div>
                    </header>

                    <form
                        @submit.prevent="submit"
                        class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-7"
                    >
                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >First Name</label
                            >
                            <input
                                v-model="form.firstName"
                                type="text"
                                placeholder="Eskedar"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                :class="{
                                    'border-red-400 bg-red-50':
                                        form.errors.firstName,
                                }"
                                required
                            />
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Last Name</label
                            >
                            <input
                                v-model="form.lastName"
                                type="text"
                                placeholder="Ketema"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                :class="{
                                    'border-red-400 bg-red-50':
                                        form.errors.lastName,
                                }"
                                required
                            />
                        </div>

                        <div class="group md:col-span-2">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Personal Email Address</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="eskedar@gmail.com"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                :class="{
                                    'border-red-400 bg-red-50':
                                        form.errors.email,
                                }"
                                required
                            />
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Phone Number</label
                            >
                            <input
                                v-model="form.phone_no"
                                type="text"
                                placeholder="09..."
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                :class="{
                                    'border-red-400 bg-red-50':
                                        form.errors.phone_no,
                                }"
                                required
                            />
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Visitor Category</label
                            >
                            <select
                                v-model="form.visitorType"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold appearance-none cursor-pointer"
                            >
                                <option value="Local">Local (Ethiopian)</option>
                                <option value="Foreign">
                                    Foreign (International)
                                </option>
                            </select>
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Secure Password</label
                            >
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                :class="{
                                    'border-red-400 bg-red-50':
                                        form.errors.password,
                                }"
                                required
                            />
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Confirm Identity Key</label
                            >
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                required
                            />
                        </div>

                        <div class="md:col-span-2 pt-6">
                            <button
                                :disabled="form.processing"
                                type="submit"
                                class="w-full bg-[#020617] text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.25em] hover:bg-blue-600 shadow-[0_20px_40px_-10px_rgba(2,6,23,0.3)] hover:shadow-blue-600/30 transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                            >
                                <span v-if="!form.processing"
                                    >Register & Continue</span
                                >
                                <template v-else>
                                    <svg
                                        class="animate-spin h-4 w-4 text-blue-400"
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
                                    <span>Processing...</span>
                                </template>
                            </button>
                        </div>
                    </form>

                    <div
                        class="mt-12 pt-8 border-t border-slate-50 text-center"
                    >
                        <p
                            class="text-[11px] text-slate-400 font-bold uppercase tracking-widest"
                        >
                            Existing Visitor?
                            <Link
                                :href="route('login')"
                                class="text-slate-900 hover:text-blue-600 ml-2 transition-all underline decoration-slate-200 hover:decoration-blue-200 underline-offset-4"
                                >Sign in here</Link
                            >
                        </p>
                    </div>
                </div>

                <div class="mt-10 text-center">
                    <p
                        class="text-slate-300 text-[9px] font-black uppercase tracking-[0.5em] leading-relaxed"
                    >
                        Public Access Portal <br />
                        <span class="opacity-60 text-slate-400"
                            >&copy; 2026 Adama City Gallery Infrastructure</span
                        >
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom hide scrollbar for clean institutional look */
::-webkit-scrollbar {
    width: 4px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
