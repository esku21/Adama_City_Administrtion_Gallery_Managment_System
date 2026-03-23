<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const user = computed(() => usePage().props.auth.user);

const form = useForm({
    firstName: user.value?.firstName || "",
    lastName: user.value?.lastName || "",
    email: user.value?.email || "",
});

const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updateProfile = () => {
    form.patch(route("profile.update"), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Add custom success logic here
        },
    });
};

const updatePassword = () => {
    passwordForm.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => passwordForm.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Admin Profile" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto animate-in fade-in duration-700">
            <header class="mb-10 flex items-center gap-6">
                <div
                    class="h-16 w-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center shadow-2xl shadow-indigo-200"
                >
                    <span class="material-icons-outlined text-white text-3xl"
                        >admin_panel_settings</span
                    >
                </div>
                <div>
                    <h1
                        class="text-4xl font-black text-slate-900 uppercase tracking-tighter leading-none"
                    >
                        Identity & <span class="text-indigo-600">Access</span>
                    </h1>
                    <p
                        class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mt-2"
                    >
                        System Admin ID:
                        <span class="text-slate-600">#00{{ user.id }}</span> •
                        Role: <span class="text-indigo-500">Superuser</span>
                    </p>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div
                    class="bg-white border border-slate-200 p-10 rounded-[3rem] shadow-sm relative overflow-hidden group"
                >
                    <div class="flex items-center gap-3 mb-10">
                        <div
                            class="h-10 w-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600"
                        >
                            <span class="material-icons-outlined"
                                >fingerprint</span
                            >
                        </div>
                        <h3
                            class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-800"
                        >
                            Identity Registry
                        </h3>
                    </div>

                    <form
                        @submit.prevent="updateProfile"
                        class="space-y-6 relative z-10"
                    >
                        <div class="grid grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="label">First Name</label>
                                <input
                                    v-model="form.firstName"
                                    type="text"
                                    class="input-field"
                                    placeholder="Entry required..."
                                />
                                <div
                                    v-if="form.errors.firstName"
                                    class="text-[9px] font-bold text-rose-500 uppercase mt-1 ml-1"
                                >
                                    {{ form.errors.firstName }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="label">Last Name</label>
                                <input
                                    v-model="form.lastName"
                                    type="text"
                                    class="input-field"
                                    placeholder="Entry required..."
                                />
                                <div
                                    v-if="form.errors.lastName"
                                    class="text-[9px] font-bold text-rose-500 uppercase mt-1 ml-1"
                                >
                                    {{ form.errors.lastName }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="label">Admin Email Address</label>
                            <input
                                v-model="form.email"
                                type="email"
                                readonly
                                class="input-field bg-slate-50 text-slate-400 border-dashed cursor-not-allowed"
                            />
                            <p
                                class="text-[8px] font-bold text-slate-400 uppercase ml-1 italic"
                            >
                                Contact system owner to change primary email
                            </p>
                        </div>

                        <button
                            :disabled="form.processing"
                            class="btn-primary w-full group"
                        >
                            <span
                                class="flex items-center justify-center gap-2"
                            >
                                Update Identity
                                <span
                                    class="material-icons-outlined text-sm group-hover:translate-x-1 transition-transform"
                                    >arrow_forward</span
                                >
                            </span>
                        </button>
                    </form>

                    <span
                        class="material-icons-outlined absolute -right-12 -bottom-12 text-[200px] text-slate-50 pointer-events-none group-hover:text-indigo-50/50 transition-colors duration-500"
                    >
                        badge
                    </span>
                </div>

                <div
                    class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl text-white relative overflow-hidden border border-slate-800"
                >
                    <div class="flex items-center gap-3 mb-10">
                        <div
                            class="h-10 w-10 bg-white/10 rounded-2xl flex items-center justify-center text-amber-500"
                        >
                            <span class="material-icons-outlined"
                                >security</span
                            >
                        </div>
                        <h3
                            class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400"
                        >
                            Security Protocol
                        </h3>
                    </div>

                    <form
                        @submit.prevent="updatePassword"
                        class="space-y-6 relative z-10"
                    >
                        <div class="space-y-2">
                            <label class="label text-slate-500"
                                >Current Security Key</label
                            >
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="input-field-dark"
                                placeholder="••••••••"
                            />
                            <div
                                v-if="passwordForm.errors.current_password"
                                class="text-[9px] font-bold text-rose-400 uppercase mt-1 ml-1"
                            >
                                {{ passwordForm.errors.current_password }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="label text-slate-500"
                                >New Password Key</label
                            >
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                class="input-field-dark"
                                placeholder="••••••••"
                            />
                            <div
                                v-if="passwordForm.errors.password"
                                class="text-[9px] font-bold text-rose-400 uppercase mt-1 ml-1"
                            >
                                {{ passwordForm.errors.password }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="label text-slate-500"
                                >Confirm Rotation Key</label
                            >
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="input-field-dark"
                                placeholder="••••••••"
                            />
                        </div>

                        <button
                            :disabled="passwordForm.processing"
                            class="w-full bg-white text-slate-900 font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.2em] hover:bg-indigo-50 transition-all shadow-xl active:scale-[0.98]"
                        >
                            Rotate Credentials
                        </button>
                    </form>

                    <span
                        class="material-icons-outlined absolute -right-12 -bottom-12 text-[200px] text-white/[0.02] pointer-events-none"
                    >
                        lock
                    </span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.label {
    @apply text-[9px] font-black uppercase tracking-widest block ml-1;
}

.input-field {
    @apply w-full border border-slate-200 rounded-2xl py-4 px-6 text-xs font-bold text-slate-700 placeholder:text-slate-300 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all;
}

.input-field-dark {
    @apply w-full bg-slate-800/40 border border-slate-700/50 rounded-2xl py-4 px-6 text-xs font-bold text-white placeholder:text-slate-600 focus:ring-4 focus:ring-white/5 focus:border-white/20 outline-none transition-all;
}

.btn-primary {
    @apply bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 rounded-2xl text-[10px] uppercase tracking-[0.2em] transition-all shadow-lg shadow-indigo-200 disabled:opacity-50 active:scale-[0.98];
}

/* Animations */
.fade-in {
    animation: fadeIn 0.8s ease-out forwards;
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
