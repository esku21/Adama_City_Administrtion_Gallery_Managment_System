<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { Lock, ShieldCheck, KeyRound, EyeOff } from "lucide-vue-next";
import Swal from "sweetalert2";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

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
                title: t("security.success_title"),
                text: t("security.success_text"),
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
    <Head :title="$t('security.page_title')" />

    <VisitorLayout>
        <template #header>{{ $t("security.page_title") }}</template>

        <div class="max-w-3xl mx-auto pb-20 animate-in">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-black text-slate-900 tracking-tight"
                    >
                        {{ $t("security.title") }}
                    </h1>
                    <p class="text-slate-500 font-medium mt-1 text-sm">
                        {{ $t("security.subtitle") }}
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
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <label
                                    class="flex items-center gap-2 text-[11px] font-black text-slate-500 uppercase tracking-widest"
                                >
                                    <KeyRound
                                        class="w-3.5 h-3.5 text-indigo-500"
                                    />
                                    {{ $t("security.current_password") }}
                                </label>
                            </div>
                            <div class="relative group">
                                <input
                                    type="password"
                                    v-model="form.current_password"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 focus:bg-white transition-all font-semibold placeholder:text-slate-300"
                                    :placeholder="
                                        $t('security.placeholder_current')
                                    "
                                    :class="{
                                        'border-red-400 bg-red-50/30':
                                            form.errors.current_password,
                                    }"
                                />
                                <div
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400 transition-colors"
                                >
                                    <Lock class="w-5 h-5" />
                                </div>
                            </div>
                            <Transition name="slide-fade">
                                <p
                                    v-if="form.errors.current_password"
                                    class="text-red-500 text-[11px] mt-2 font-bold flex items-center gap-1 ml-1"
                                >
                                    <span
                                        class="w-1 h-1 bg-red-500 rounded-full"
                                    ></span>
                                    {{ form.errors.current_password }}
                                </p>
                            </Transition>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="relative">
                                <label
                                    class="flex items-center gap-2 text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3"
                                >
                                    <EyeOff
                                        class="w-3.5 h-3.5 text-indigo-500"
                                    />
                                    {{ $t("security.new_password") }}
                                </label>
                                <input
                                    type="password"
                                    v-model="form.password"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 focus:bg-white transition-all font-semibold placeholder:text-slate-300"
                                    :placeholder="
                                        $t('security.placeholder_new')
                                    "
                                    :class="{
                                        'border-red-400 bg-red-50/30':
                                            form.errors.password,
                                    }"
                                />
                            </div>

                            <div class="relative">
                                <label
                                    class="flex items-center gap-2 text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3"
                                >
                                    {{ $t("security.confirm_password") }}
                                </label>
                                <input
                                    type="password"
                                    v-model="form.password_confirmation"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 focus:bg-white transition-all font-semibold placeholder:text-slate-300"
                                    :placeholder="
                                        $t('security.placeholder_confirm')
                                    "
                                />
                            </div>
                        </div>

                        <Transition name="slide-fade">
                            <p
                                v-if="form.errors.password"
                                class="text-red-500 text-[11px] font-bold flex items-center gap-1 ml-1 -mt-4"
                            >
                                <span
                                    class="w-1 h-1 bg-red-500 rounded-full"
                                ></span>
                                {{ form.errors.password }}
                            </p>
                        </Transition>

                        <div
                            class="pt-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4"
                        >
                            <div class="text-[11px] text-slate-400 font-medium">
                                {{ $t("security.footer_note") }}
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full md:w-auto px-10 py-4 bg-indigo-600 text-white rounded-[1.2rem] font-black uppercase text-[11px] tracking-[0.15em] transition-all shadow-xl shadow-indigo-200/50 hover:bg-indigo-700 hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                            >
                                <span v-if="form.processing">{{
                                    $t("security.btn_processing")
                                }}</span>
                                <span v-else>{{
                                    $t("security.btn_update")
                                }}</span>
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
    animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}
.slide-fade-enter-from {
    transform: translateY(-5px);
    opacity: 0;
}
</style>
