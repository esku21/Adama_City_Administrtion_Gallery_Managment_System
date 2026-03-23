<script setup>
import { Head, useForm } from "@inertiajs/vue3"; // Removed unused router
import { Lock } from "lucide-vue-next";
import Swal from "sweetalert2";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    // This points to the global password.update route defined in your web.php
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            Swal.fire({
                title: "Security Updated",
                text: "Your password has been successfully changed.",
                icon: "success",
                confirmButtonColor: "#4f46e5",
                customClass: {
                    popup: "rounded-[2rem]",
                    confirmButton: "rounded-xl font-bold px-6 py-3",
                },
            });
        },
        onFinish: () => {
            // Always reset passwords on finish to keep the UI clean
            form.reset("password", "password_confirmation");
        },
    });
};
</script>

<template>
    <Head title="Security Settings" />

    <VisitorLayout>
        <template #header>Security Settings</template>

        <div class="max-w-4xl mx-auto pb-20 animate-in">
            <div
                class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden"
            >
                <div class="p-8 md:p-12">
                    <div
                        class="flex flex-col md:flex-row items-center md:items-start mb-12 gap-6"
                    >
                        <div
                            class="w-16 h-16 bg-indigo-50 rounded-[1.5rem] flex items-center justify-center border border-indigo-100 shrink-0"
                        >
                            <Lock class="w-8 h-8 text-indigo-600" />
                        </div>
                        <div class="text-center md:text-left">
                            <h2
                                class="text-2xl font-black text-slate-800 uppercase tracking-tight"
                            >
                                Change Password
                            </h2>
                            <p class="text-slate-500 font-medium mt-1">
                                Update your account credentials to keep your
                                profile safe.
                            </p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-8">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3"
                            >
                                Current Password
                            </label>
                            <input
                                type="password"
                                v-model="form.current_password"
                                class="w-full bg-slate-50/50 border-slate-200 rounded-2xl p-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold placeholder:text-slate-300"
                                placeholder="••••••••"
                                :class="{
                                    'border-red-400 bg-red-50/30':
                                        form.errors.current_password,
                                }"
                            />
                            <p
                                v-if="form.errors.current_password"
                                class="text-red-500 text-xs mt-3 font-bold px-1"
                            >
                                {{ form.errors.current_password }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3"
                                >
                                    New Password
                                </label>
                                <input
                                    type="password"
                                    v-model="form.password"
                                    class="w-full bg-slate-50/50 border-slate-200 rounded-2xl p-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold placeholder:text-slate-300"
                                    placeholder="••••••••"
                                    :class="{
                                        'border-red-400 bg-red-50/30':
                                            form.errors.password,
                                    }"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3"
                                >
                                    Confirm New Password
                                </label>
                                <input
                                    type="password"
                                    v-model="form.password_confirmation"
                                    class="w-full bg-slate-50/50 border-slate-200 rounded-2xl p-5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold placeholder:text-slate-300"
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>

                        <p
                            v-if="form.errors.password"
                            class="text-red-500 text-xs font-bold px-1 mt-[-1rem]"
                        >
                            {{ form.errors.password }}
                        </p>

                        <div
                            class="pt-6 border-t border-slate-50 flex justify-end"
                        >
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full md:w-auto px-10 py-5 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:translate-y-0"
                            >
                                {{
                                    form.processing
                                        ? "Updating Security..."
                                        : "Save New Password"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>

<style scoped>
.animate-in {
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
