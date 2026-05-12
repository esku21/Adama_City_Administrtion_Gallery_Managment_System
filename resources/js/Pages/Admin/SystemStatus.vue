<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    settings: Object,
    auth: Object,
});

const form = useForm({
    system_status: props.settings?.system_status || "active",
});

const submit = () => {
    form.post(route("admin.system.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="t('system_status.page_title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800">
                {{ t("system_status.header") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white shadow-sm rounded-[32px] border border-slate-100"
                >
                    <div class="p-10">
                        <!-- HEADER -->
                        <div class="mb-10 flex items-center gap-5">
                            <div
                                class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100"
                            >
                                <span
                                    class="material-icons-outlined text-white text-3xl"
                                >
                                    settings_remote
                                </span>
                            </div>

                            <div>
                                <h2
                                    class="text-3xl font-black uppercase tracking-tight text-slate-800"
                                >
                                    {{ t("system_status.title") }}
                                </h2>

                                <p class="text-slate-500 text-base font-medium">
                                    {{ t("system_status.subtitle") }}
                                </p>
                            </div>
                        </div>

                        <!-- STATUS CARDS -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12"
                        >
                            <div
                                v-for="status in [
                                    'active',
                                    'maintenance',
                                    'inactive',
                                ]"
                                :key="status"
                                @click="form.system_status = status"
                                :class="[
                                    form.system_status === status
                                        ? 'border-indigo-600 bg-indigo-50 ring-4 ring-indigo-100'
                                        : 'border-slate-200 bg-white hover:border-indigo-300',
                                    'p-8 rounded-[28px] border-2 cursor-pointer transition-all group',
                                ]"
                            >
                                <div class="flex flex-col gap-4">
                                    <!-- ICON -->
                                    <span
                                        :class="
                                            form.system_status === status
                                                ? 'text-indigo-600'
                                                : 'text-slate-400'
                                        "
                                        class="material-icons-outlined text-5xl"
                                    >
                                        {{
                                            status === "active"
                                                ? "public"
                                                : status === "maintenance"
                                                  ? "handyman"
                                                  : "cloud_off"
                                        }}
                                    </span>

                                    <!-- TEXT -->
                                    <div>
                                        <span
                                            class="text-sm font-semibold uppercase text-slate-500 tracking-wide"
                                        >
                                            {{ t("system_status.mode") }}
                                        </span>

                                        <h3
                                            class="font-black uppercase text-xl tracking-tight text-slate-800 mt-1"
                                        >
                                            {{ t(`system_status.${status}`) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <div
                            class="flex justify-end border-t border-slate-100 pt-8"
                        >
                            <button
                                @click="submit"
                                :disabled="form.processing"
                                class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold uppercase text-sm tracking-wider hover:bg-indigo-600 transition-all disabled:opacity-50 shadow-xl"
                            >
                                {{
                                    form.processing
                                        ? t("system_status.updating_btn")
                                        : t("system_status.save_btn")
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
