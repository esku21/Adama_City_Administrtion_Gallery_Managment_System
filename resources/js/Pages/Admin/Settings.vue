<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const securityForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submitSecurity = () => {
    securityForm.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => securityForm.reset(),
    });
};
</script>

<template>
    <Head title="Security Settings" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4 mb-10">
                <div
                    class="h-12 w-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200"
                >
                    <span class="material-icons-outlined text-white"
                        >security</span
                    >
                </div>
                <div>
                    <h2
                        class="text-2xl font-black text-slate-800 uppercase tracking-tighter leading-none"
                    >
                        Security Settings
                    </h2>
                    <p
                        class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1"
                    >
                        Rotate administrative access credentials
                    </p>
                </div>
            </div>

            <div
                class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-10 max-w-2xl"
            >
                <form @submit.prevent="submitSecurity" class="space-y-6">
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1"
                            >Current Password</label
                        >
                        <input
                            v-model="securityForm.current_password"
                            type="password"
                            class="w-full px-5 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:ring-indigo-500 font-bold"
                        />
                        <p
                            v-if="securityForm.errors.current_password"
                            class="text-rose-500 text-[10px] font-bold uppercase"
                        >
                            {{ securityForm.errors.current_password }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1"
                            >New Password</label
                        >
                        <input
                            v-model="securityForm.password"
                            type="password"
                            class="w-full px-5 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:ring-indigo-500 font-bold"
                        />
                        <p
                            v-if="securityForm.errors.password"
                            class="text-rose-500 text-[10px] font-bold uppercase"
                        >
                            {{ securityForm.errors.password }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1"
                            >Confirm Password</label
                        >
                        <input
                            v-model="securityForm.password_confirmation"
                            type="password"
                            class="w-full px-5 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:ring-indigo-500 font-bold"
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="securityForm.processing"
                        class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-indigo-700 transition-all"
                    >
                        Update Access Credentials
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
