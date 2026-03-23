<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({ status: String });

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Official Login | Adama City Gallery" />

    <div
        class="min-h-screen bg-[#fcfdfe] flex font-sans antialiased selection:bg-blue-100"
    >
        <div
            class="hidden lg:flex lg:w-[42%] bg-[#020617] relative items-center justify-center p-16 overflow-hidden"
        >
            <div
                class="absolute top-[-20%] left-[-20%] w-[700px] h-[700px] bg-blue-600 rounded-full blur-[160px] opacity-[0.12]"
            ></div>
            <div
                class="absolute bottom-[-10%] right-[0%] w-[500px] h-[500px] bg-indigo-500 rounded-full blur-[140px] opacity-[0.08]"
            ></div>
            <div
                class="absolute inset-0 opacity-[0.02]"
                style="
                    background-image:
                        linear-gradient(#fff 1.5px, transparent 1.5px),
                        linear-gradient(90deg, #fff 1.5px, transparent 1.5px);
                    background-size: 50px 50px;
                "
            ></div>

            <div class="relative z-10 w-full max-w-sm">
                <div class="flex items-center gap-6 mb-20">
                    <div
                        class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-2xl border border-white/20 aspect-square shrink-0"
                    >
                        <img
                            src="/storage/images/adama.png"
                            alt="Adama Logo"
                            class="w-10 h-10 object-contain"
                        />
                    </div>
                    <div>
                        <p
                            class="text-white font-black text-xl tracking-tighter leading-none"
                        >
                            ADAMA CITY
                        </p>
                        <p
                            class="text-blue-500 text-[9px] font-bold tracking-[0.4em] uppercase mt-1"
                        >
                            Gallery Admin
                        </p>
                    </div>
                </div>

                <h2
                    class="text-5xl font-black text-white mb-8 tracking-tight leading-[1.1]"
                >
                    Institutional <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-200"
                        >Access Portal</span
                    >
                </h2>

                <p
                    class="text-slate-500 text-lg leading-relaxed font-medium mb-12"
                >
                    Authorized administrative gateway for the Adama City
                    infrastructure. Multi-layer security protocols active.
                </p>

                <div class="flex gap-10 border-t border-white/5 pt-10">
                    <div class="flex flex-col">
                        <span class="text-white font-bold text-xl tabular-nums"
                            >v.4.0</span
                        >
                        <span
                            class="text-slate-600 text-[9px] uppercase tracking-widest font-black mt-1"
                            >Core Engine</span
                        >
                    </div>
                    <div class="flex flex-col">
                        <span class="text-white font-bold text-xl"
                            >256-bit</span
                        >
                        <span
                            class="text-slate-600 text-[9px] uppercase tracking-widest font-black mt-1"
                            >Encryption</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-12 bg-white lg:bg-transparent"
        >
            <div class="w-full max-w-[420px]">
                <div
                    class="bg-white p-8 sm:p-10 lg:p-12 rounded-[2.5rem] shadow-[0_20px_50px_-15px_rgba(0,0,0,0.04)] border border-slate-100"
                >
                    <header class="mb-10 text-center">
                        <h1
                            class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight"
                        >
                            System Login
                        </h1>
                        <p
                            class="text-slate-400 text-[10px] font-black mt-3 uppercase tracking-[0.15em]"
                        >
                            Secure Personnel Authentication
                        </p>
                        <div
                            class="w-10 h-1 bg-blue-600 mx-auto mt-4 rounded-full"
                        ></div>
                    </header>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Administrative Email</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="name@adama.gov.et"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                required
                            />
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1 group-focus-within:text-blue-600 transition-colors"
                                >Security Key</label
                            >
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••••••"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-[6px] focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-bold placeholder:text-slate-300"
                                required
                            />
                            <div class="mt-3 flex justify-end">
                                <Link
                                    :href="route('password.request')"
                                    class="text-[9px] font-black text-slate-400 hover:text-blue-600 transition-colors uppercase tracking-widest"
                                >
                                    Trouble accessing?
                                    <span
                                        class="text-blue-600 underline underline-offset-4 decoration-2"
                                        >Forgot Key</span
                                    >
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center pt-2">
                            <label
                                class="flex items-center gap-3 cursor-pointer group/check"
                            >
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/10 transition-all cursor-pointer"
                                />
                                <span
                                    class="text-[10px] font-bold text-slate-400 group-hover/check:text-slate-900 transition-colors uppercase tracking-tight"
                                    >Maintain active session</span
                                >
                            </label>
                        </div>

                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="w-full bg-[#020617] text-white py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.25em] hover:bg-blue-600 shadow-[0_20px_40px_-10px_rgba(2,6,23,0.3)] hover:shadow-blue-600/30 transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                        >
                            <span v-if="!form.processing"
                                >Establish Identity</span
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
                                <span>Verifying...</span>
                            </template>
                        </button>
                    </form>

                    <div
                        class="mt-10 pt-8 border-t border-slate-100 text-center"
                    >
                        <p
                            class="text-[10px] text-slate-400 font-black uppercase tracking-widest"
                        >
                            Official Personnel Only
                            <Link
                                :href="route('register')"
                                class="text-slate-900 hover:text-blue-600 ml-2 transition-all underline decoration-slate-200 hover:decoration-blue-200 underline-offset-4"
                                >Request Access</Link
                            >
                        </p>
                    </div>
                </div>

                <div class="mt-10 text-center">
                    <p
                        class="text-slate-300 text-[9px] font-black uppercase tracking-[0.5em] leading-relaxed"
                    >
                        DigitalNational Infrastructure <br />
                        <span class="opacity-60 text-slate-400"
                            >&copy; 2026 Adama City Administration</span
                        >
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
