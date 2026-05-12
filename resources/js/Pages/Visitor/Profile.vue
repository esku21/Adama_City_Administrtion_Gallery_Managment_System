<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { ShieldCheck, Lock, Info } from "lucide-vue-next";

const { t } = useI18n();
const page = usePage();

const user = computed(() => page.props.auth?.user || {});

const displayVisitorType = computed(() => {
    return user.value.visitorType || t("profile.local_resident");
});
</script>

<template>
    <Head :title="t('profile.title')" />

    <VisitorLayout>
        <template #header>
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 w-full"
            >
                <div class="flex items-center gap-3">
                    <h1
                        class="text-xl md:text-2xl font-black text-slate-800 tracking-tight"
                    >
                        {{ t("nav.profile") }}
                    </h1>
                    <span
                        class="hidden md:block px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100"
                    >
                        Verified
                    </span>
                </div>
                <span
                    class="text-xs font-bold text-slate-400 uppercase tracking-widest"
                >
                    {{ t("profile.ref_id") }}: #{{ user.id || "N/A" }}
                </span>
            </div>
        </template>

        <div class="max-w-5xl mx-auto pb-16 fade-in">
            <div
                class="mb-8 flex flex-col md:flex-row md:items-center justify-between bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm gap-4"
            >
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-slate-100 rounded-2xl text-slate-500">
                        <Lock :size="20" />
                    </div>
                    <div>
                        <p class="text-slate-800 font-bold text-sm">
                            {{ t("profile.sys_locked") }}
                        </p>
                        <p class="text-slate-500 text-xs mt-0.5">
                            {{ t("profile.sys_locked_desc") }}
                        </p>
                    </div>
                </div>
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-50 rounded-xl"
                >
                    <div
                        class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"
                    ></div>
                    <span
                        class="text-[11px] font-black text-indigo-700 uppercase tracking-tighter"
                    >
                        {{ t("profile.read_only") }}
                    </span>
                </div>
            </div>

            <div
                class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden"
            >
                <div class="p-8 md:p-12">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8"
                    >
                        <div class="space-y-3">
                            <label
                                class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >
                                {{ t("profile.first_name") }}
                            </label>
                            <div
                                class="bg-slate-50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700 text-lg"
                            >
                                {{
                                    user.firstName ||
                                    user.name ||
                                    t("profile.not_set")
                                }}
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label
                                class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >
                                {{ t("profile.last_name") }}
                            </label>
                            <div
                                class="bg-slate-50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700 text-lg"
                            >
                                {{ user.lastName || "—" }}
                            </div>
                        </div>

                        <div class="md:col-span-2 space-y-3">
                            <label
                                class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >
                                {{ t("profile.email") }}
                            </label>
                            <div
                                class="bg-slate-50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700 flex items-center justify-between group"
                            >
                                <span class="text-lg">{{
                                    user.email || t("profile.no_email")
                                }}</span>
                                <ShieldCheck
                                    :size="20"
                                    class="text-emerald-500"
                                />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label
                                class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >
                                {{ t("profile.phone") }}
                            </label>
                            <div
                                class="bg-slate-50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700 text-lg"
                            >
                                {{
                                    user.phone_no ||
                                    user.phone ||
                                    t("profile.no_phone")
                                }}
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label
                                class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1"
                            >
                                {{ t("profile.authority") }}
                            </label>
                            <div
                                class="bg-slate-50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700 text-lg"
                            >
                                {{ displayVisitorType }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-12 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-[2rem] p-8 text-white relative overflow-hidden"
                    >
                        <ShieldCheck
                            :size="120"
                            class="absolute -right-5 -bottom-5 text-white/10 rotate-12"
                        />
                        <div
                            class="relative z-10 flex flex-col md:flex-row items-center gap-6"
                        >
                            <div
                                class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20"
                            >
                                <Info :size="32" />
                            </div>
                            <div class="text-center md:text-left">
                                <h4 class="text-xl font-black mb-2">
                                    {{ t("profile.update_title") }}
                                </h4>
                                <p
                                    class="text-indigo-100 text-sm leading-relaxed max-w-xl"
                                >
                                    {{ t("profile.update_desc") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>

<style scoped>
.fade-in {
    animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
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
</style>
