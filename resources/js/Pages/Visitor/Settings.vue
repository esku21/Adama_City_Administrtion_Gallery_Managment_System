<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { Lock, ShieldCheck, KeyRound, EyeOff } from "lucide-vue-next";
import Swal from "sweetalert2";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            Swal.fire({
                title: t("visitor_settings.success_title"),
                text: t("visitor_settings.success_text"),
                icon: "success",
                confirmButtonColor: "#4f46e5",
                background: "#ffffff",
                customClass: {
                    popup: "rounded-[2rem] border-none shadow-2xl",
                    confirmButton:
                        "rounded-xl font-bold px-8 py-3 bg-indigo-600 hover:bg-indigo-700",
                    title: "text-2xl font-black text-slate-800",
                },
            });
        },
        onFinish: () => {
            form.reset("password", "password_confirmation");
        },
    });
};
</script>

<template>
    <Head :title="$t('visitor_settings.page_title')" />

    <VisitorLayout>
        <!-- Language Switcher -->
        <div class="max-w-3xl mx-auto mb-6 flex gap-2 justify-end">
            <button
                @click="locale = 'en'"
                :class="
                    locale === 'en'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-slate-200'
                "
                class="px-3 py-1 text-xs font-bold rounded"
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
                class="px-3 py-1 text-xs font-bold rounded"
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
                class="px-3 py-1 text-xs font-bold rounded"
            >
                AM
            </button>
        </div>

        <div class="max-w-3xl mx-auto pb-20 animate-in">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-black text-slate-900 tracking-tight"
                    >
                        {{ $t("visitor_settings.title") }}
                    </h1>
                    <p class="text-slate-500 font-medium mt-1 text-sm">
                        {{ $t("visitor_settings.subtitle") }}
                    </p>
                </div>
                <div class="hidden md:block">
                    <ShieldCheck class="w-12 h-12 text-indigo-500/20" />
                </div>
            </div>

            <div
                class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden"
            >
                <div class="p-8 md:p-12">
                    <form @submit.prevent="submit" class="space-y-10">
                        <!-- Current Password -->
                        <div class="relative">
                            <label
                                class="flex items-center gap-2 text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3"
                            >
                                <KeyRound class="w-3.5 h-3.5 text-indigo-500" />
                                {{ $t("visitor_settings.current_password") }}
                            </label>
                            <input
                                type="password"
                                v-model="form.current_password"
                                class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-semibold"
                                :placeholder="
                                    $t('visitor_settings.placeholder_current')
                                "
                            />
                        </div>

                        <!-- New Password Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="flex items-center gap-2 text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3"
                                >
                                    <EyeOff
                                        class="w-3.5 h-3.5 text-indigo-500"
                                    />
                                    {{ $t("visitor_settings.new_password") }}
                                </label>
                                <input
                                    type="password"
                                    v-model="form.password"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-semibold"
                                    :placeholder="
                                        $t('visitor_settings.placeholder_new')
                                    "
                                />
                            </div>
                            <div>
                                <label
                                    class="flex items-center gap-2 text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3"
                                >
                                    {{
                                        $t("visitor_settings.confirm_password")
                                    }}
                                </label>
                                <input
                                    type="password"
                                    v-model="form.password_confirmation"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-semibold"
                                    :placeholder="
                                        $t(
                                            'visitor_settings.placeholder_confirm',
                                        )
                                    "
                                />
                            </div>
                        </div>

                        <div
                            class="pt-6 border-t border-slate-100 flex items-center justify-between"
                        >
                            <div class="text-[11px] text-slate-400 font-medium">
                                {{ $t("visitor_settings.footer_note") }}
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-10 py-4 bg-indigo-600 text-white rounded-[1.2rem] font-black uppercase text-[11px] tracking-[0.15em] transition-all hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{
                                    form.processing
                                        ? $t("visitor_settings.btn_processing")
                                        : $t("visitor_settings.btn_update")
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>
