<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { Loader2, Building2, Mail, Lock } from "lucide-vue-next";

defineProps({
    halls: Array,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    hall_id: "", // Stores the selected Hall ID
    remember: false,
});

const submit = () => {
    form.post(route("guide.login.submit"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Guide Login" />

    <div
        class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-6"
    >
        <div
            class="w-full max-w-md bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-100"
        >
            <div class="text-center mb-10">
                <h1
                    class="text-3xl font-black tracking-tighter uppercase text-slate-900"
                >
                    Guide <span class="text-indigo-600">Portal</span>
                </h1>
                <p
                    class="text-[10px] text-slate-400 uppercase tracking-[0.3em] mt-2"
                >
                    Enter your hall assigned credentials
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label
                        class="text-[9px] text-slate-400 uppercase font-black mb-3 block tracking-widest"
                        >Assigned Hall</label
                    >
                    <div class="relative">
                        <Building2
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                        />
                        <select
                            v-model="form.hall_id"
                            required
                            class="w-full bg-slate-50 border-slate-200 rounded-2xl pl-12 p-4 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all"
                        >
                            <option value="" disabled>Select your Hall</option>
                            <option
                                v-for="hall in halls"
                                :key="hall.id"
                                :value="hall.id"
                            >
                                {{ hall.name }}
                            </option>
                        </select>
                    </div>
                    <p
                        v-if="form.errors.hall_id"
                        class="text-red-500 text-[10px] mt-2 font-bold uppercase"
                    >
                        {{ form.errors.hall_id }}
                    </p>
                </div>

                <div>
                    <label
                        class="text-[9px] text-slate-400 uppercase font-black mb-3 block tracking-widest"
                        >Email Address</label
                    >
                    <div class="relative">
                        <Mail
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                        />
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="guide@adama.gov.et"
                            class="w-full bg-slate-50 border-slate-200 rounded-2xl pl-12 p-4 text-sm focus:ring-2 focus:ring-indigo-600"
                            required
                        />
                    </div>
                    <p
                        v-if="form.errors.email"
                        class="text-red-500 text-[10px] mt-2 font-bold uppercase"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label
                        class="text-[9px] text-slate-400 uppercase font-black mb-3 block tracking-widest"
                        >Password</label
                    >
                    <div class="relative">
                        <Lock
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                        />
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="••••••••"
                            class="w-full bg-slate-50 border-slate-200 rounded-2xl pl-12 p-4 text-sm focus:ring-2 focus:ring-indigo-600"
                            required
                        />
                    </div>
                </div>

                <div class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="rounded text-indigo-600 border-slate-300 shadow-sm"
                    />
                    <span
                        class="ml-2 text-[11px] font-bold text-slate-500 uppercase tracking-tighter"
                        >Remember this session</span
                    >
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl uppercase text-[11px] tracking-[0.2em] transition-all shadow-lg shadow-indigo-100 flex justify-center items-center gap-2"
                >
                    <Loader2
                        v-if="form.processing"
                        class="w-4 h-4 animate-spin"
                    />
                    {{ form.processing ? "Verifying..." : "Access Dashboard" }}
                </button>
            </form>

            <div class="mt-8 text-center">
                <a
                    href="/"
                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest hover:text-indigo-600 transition-colors"
                >
                    ← Back to Main Gate
                </a>
            </div>
        </div>
    </div>
</template>
