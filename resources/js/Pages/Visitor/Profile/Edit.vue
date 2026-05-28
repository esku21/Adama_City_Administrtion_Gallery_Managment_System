<script setup>
import { usePage, Head } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";

const { locale } = useI18n();
const user = computed(() => usePage().props.auth?.user || {});

const userData = {
    firstName: user.value.firstName || "",
    lastName: user.value.lastName || "",
    email: user.value.email || "",
    phone_no: user.value.phone_no || "",
};
</script>

<template>
    <Head :title="$t('visitor_profile.title')" />

    <VisitorLayout>
        <div class="min-h-screen py-10 px-4 sm:px-6">
            <div class="max-w-4xl mx-auto">
                <div class="flex gap-2 mb-6">
                    <button
                        @click="locale = 'en'"
                        :class="
                            locale === 'en'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-slate-200'
                        "
                        class="px-3 py-1 rounded"
                    >
                        EN
                    </button>
                    <button
                        @click="locale = 'or'"
                        :class="
                            locale === 'or'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-slate-200'
                        "
                        class="px-3 py-1 rounded"
                    >
                        OR
                    </button>
                    <button
                        @click="locale = 'am'"
                        :class="
                            locale === 'am'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-slate-200'
                        "
                        class="px-3 py-1 rounded"
                    >
                        AM
                    </button>
                </div>

                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h1
                            class="text-3xl font-extrabold text-slate-900 tracking-tight"
                        >
                            {{ $t("visitor_profile.title") }}
                        </h1>
                        <p class="text-slate-500 mt-2">
                            {{ $t("visitor_profile.subtitle") }}
                        </p>
                    </div>
                    <span
                        class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-widest rounded-full border border-slate-200"
                    >
                        {{ $t("visitor_profile.read_only") }}
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
                                        {{ userData.firstName?.charAt(0)
                                        }}{{ userData.lastName?.charAt(0) }}
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
                                <p
                                    class="text-sm text-amber-800 leading-relaxed"
                                >
                                    {{ $t("visitor_profile.verified_notice") }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("visitor_profile.first_name") }}
                                    </label>
                                    <input
                                        type="text"
                                        :value="userData.firstName"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("visitor_profile.last_name") }}
                                    </label>
                                    <input
                                        type="text"
                                        :value="userData.lastName"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("visitor_profile.email") }}
                                    </label>
                                    <input
                                        type="email"
                                        :value="userData.email"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"
                                    >
                                        {{ $t("visitor_profile.phone") }}
                                    </label>
                                    <input
                                        type="text"
                                        :value="userData.phone_no"
                                        disabled
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 font-medium text-slate-500 cursor-not-allowed border-dashed"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-center text-slate-400 text-sm mt-8">
                    {{ $t("visitor_profile.security_footer") }}
                </p>
            </div>
        </div>
    </VisitorLayout>
</template>
