<script setup>
import { useForm, usePage, Head } from "@inertiajs/vue3";
import { computed } from "vue";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";

const user = computed(() => usePage().props.auth.user);

const form = useForm({
    firstName: user.value?.firstName || "",
    lastName: user.value?.lastName || "",
    email: user.value?.email || "",
    phone_no: user.value?.phone_no || "",
});
</script>

<template>
    <Head :title="$t('nav.profile')" />

    <VisitorLayout>
        <div class="min-h-screen py-10 px-4 sm:px-6">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h1
                            class="text-3xl font-extrabold text-slate-900 tracking-tight"
                        >
                            {{ $t("profile.title") }}
                        </h1>
                        <p class="text-slate-500 mt-2">
                            {{ $t("profile.subtitle") }}
                        </p>
                    </div>
                    <span
                        class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-widest rounded-full border border-slate-200"
                    >
                        {{ $t("profile.read_only") }}
                    </span>
                </div>

                <div
                    class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden"
                >
                    <div
                        class="h-32 bg-gradient-to-r from-indigo-600 to-violet-600 relative"
                    >
                        <div class="absolute -bottom-10 left-10 z-10">
                            <div
                                class="h-24 w-24 rounded-3xl bg-white p-1 shadow-xl"
                            >
                                <div
                                    class="h-full w-full rounded-[1.25rem] bg-slate-100 flex items-center justify-center text-indigo-600 border border-slate-50"
                                >
                                    <span class="text-2xl font-bold uppercase">
                                        {{ user.firstName?.[0]
                                        }}{{ user.lastName?.[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-20 p-8 md:p-12">
                        <div class="space-y-8">
                            <div
                                class="flex items-start p-4 bg-amber-50 rounded-2xl border border-amber-100"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-amber-500 mt-0.5 mr-3"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <p
                                    class="text-sm text-amber-800 leading-relaxed"
                                >
                                    {{ $t("profile.verified_notice") }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("profile.first_name") }}
                                    </label>
                                    <input
                                        v-model="form.firstName"
                                        type="text"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("profile.last_name") }}
                                    </label>
                                    <input
                                        v-model="form.lastName"
                                        type="text"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("profile.email") }}
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("profile.phone") }}
                                    </label>
                                    <input
                                        v-model="form.phone_no"
                                        type="text"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-center text-slate-400 text-sm mt-8">
                    {{ $t("profile.security_footer") }}
                </p>
            </div>
        </div>
    </VisitorLayout>
</template>

<style scoped>
input:disabled {
    opacity: 0.8;
}
</style>
