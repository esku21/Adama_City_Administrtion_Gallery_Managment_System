<script setup>
import { ref, onMounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import {
    Megaphone,
    Info,
    Sparkles,
    Moon,
    Sun,
    Loader2,
    CheckCircle,
    AlertCircle,
} from "lucide-vue-next";

const { t } = useI18n();

// ---------------- THEME ----------------
const darkMode = ref(false);

onMounted(() => {
    const savedTheme = localStorage.getItem("theme");
    darkMode.value = savedTheme === "dark";
    document.documentElement.classList.toggle("dark", darkMode.value);
});

const toggleTheme = () => {
    darkMode.value = !darkMode.value;
    document.documentElement.classList.toggle("dark", darkMode.value);
    localStorage.setItem("theme", darkMode.value ? "dark" : "light");
};

// ---------------- NOTIFICATION ----------------
const notification = ref(null);

const showNotification = (message, type = "success") => {
    notification.value = { message, type };
    setTimeout(() => (notification.value = null), 4000);
};

watch(
    () => usePage().props.flash,
    (flash) => {
        if (flash?.success) showNotification(flash.success, "success");
        if (flash?.error) showNotification(flash.error, "error");
    },
    { deep: true },
);

// ---------------- FORM ----------------
const form = useForm({
    title: "",
    target_date: "",
    reschedule_date: "",
    message: "",
});

const submit = () => {
    form.post(route("admin.announcements.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head :title="t('admin_announcement.page_title')" />

    <AuthenticatedLayout>
        <Transition
            enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="translate-y-[-100%] opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="notification"
                class="fixed top-5 right-5 z-[100] w-[calc(100%-40px)] sm:min-w-[300px] sm:w-auto"
            >
                <div
                    :class="[
                        'px-4 py-3 rounded-xl shadow-lg border flex items-center gap-3',
                        notification.type === 'success'
                            ? 'bg-emerald-50 border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400'
                            : 'bg-red-50 border-red-100 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                    ]"
                >
                    <CheckCircle
                        v-if="notification.type === 'success'"
                        :size="20"
                    />
                    <AlertCircle v-else :size="20" />
                    <span class="font-medium text-sm">{{
                        notification.message
                    }}</span>
                </div>
            </div>
        </Transition>

        <div
            class="max-w-6xl mx-auto px-4 py-6 sm:py-8 grid grid-cols-1 lg:grid-cols-12 gap-6"
        >
            <div class="lg:col-span-8 order-1">
                <form
                    @submit.prevent="submit"
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden h-full"
                >
                    <div
                        class="bg-gradient-to-r from-rose-600 to-rose-500 px-6 py-5 text-white"
                    >
                        <h2
                            class="text-lg sm:text-xl font-black flex items-center gap-2 uppercase tracking-tight"
                        >
                            <Sparkles :size="20" />
                            {{ t("admin_announcement.broadcast_title") }}
                        </h2>
                        <p class="text-xs opacity-80 font-medium">
                            {{ t("admin_announcement.broadcast_subtitle") }}
                        </p>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="label-style">{{
                                    t("admin_announcement.target_date")
                                }}</label>
                                <input
                                    v-model="form.target_date"
                                    type="date"
                                    :class="{
                                        'border-red-500 ring-1 ring-red-500':
                                            form.errors.target_date,
                                    }"
                                    class="input"
                                />
                                <p
                                    v-if="form.errors.target_date"
                                    class="text-red-500 text-[10px] font-bold mt-1 uppercase"
                                >
                                    {{ form.errors.target_date }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="label-style">
                                    {{
                                        t("admin_announcement.reschedule_date")
                                    }}
                                    <span
                                        class="text-slate-400 lowercase italic font-normal"
                                        >({{
                                            t("admin_announcement.optional")
                                        }})</span
                                    >
                                </label>
                                <input
                                    v-model="form.reschedule_date"
                                    type="date"
                                    class="input"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="label-style">{{
                                t("admin_announcement.alert_title")
                            }}</label>
                            <input
                                v-model="form.title"
                                type="text"
                                :class="{
                                    'border-red-500 ring-1 ring-red-500':
                                        form.errors.title,
                                }"
                                class="input"
                                :placeholder="
                                    t('admin_announcement.alert_placeholder')
                                "
                            />
                            <p
                                v-if="form.errors.title"
                                class="text-red-500 text-[10px] font-bold mt-1 uppercase"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="label-style">{{
                                t("admin_announcement.message_content")
                            }}</label>
                            <textarea
                                v-model="form.message"
                                rows="4"
                                :class="{
                                    'border-red-500 ring-1 ring-red-500':
                                        form.errors.message,
                                }"
                                class="input resize-none"
                                :placeholder="
                                    t('admin_announcement.message_placeholder')
                                "
                            ></textarea>
                            <p
                                v-if="form.errors.message"
                                class="text-red-500 text-[10px] font-bold mt-1 uppercase"
                            >
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="btn-primary group"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="animate-spin"
                                :size="20"
                            />
                            <Megaphone
                                v-else
                                :size="20"
                                class="group-hover:rotate-12 transition-transform"
                            />
                            <span class="uppercase tracking-widest text-sm">
                                {{
                                    form.processing
                                        ? t("admin_announcement.broadcasting")
                                        : t("admin_announcement.dispatch")
                                }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-4 space-y-5 order-2">
                <div class="side-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-slate-400">
                            <Info :size="14" />
                            <span
                                class="text-[10px] font-black uppercase tracking-widest"
                                >{{ t("admin_announcement.preview") }}</span
                            >
                        </div>
                        <div
                            class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"
                        ></div>
                    </div>

                    <div class="border-l-4 border-rose-500 pl-4 py-1">
                        <h3
                            class="font-black text-slate-800 dark:text-white text-base leading-tight break-words"
                        >
                            {{
                                form.title ||
                                t("admin_announcement.preview_default_title")
                            }}
                        </h3>
                        <p
                            class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed break-words"
                        >
                            {{
                                form.message ||
                                t("admin_announcement.preview_default_msg")
                            }}
                        </p>
                    </div>

                    <div
                        v-if="form.target_date"
                        class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-tighter"
                    >
                        <span>Expiration:</span>
                        <span class="text-rose-500">{{
                            form.target_date
                        }}</span>
                    </div>
                </div>

                <div
                    class="side-card bg-indigo-50/50 dark:bg-indigo-900/10 border-indigo-100 dark:border-indigo-900/50"
                >
                    <div
                        class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400"
                    >
                        <Sparkles :size="16" />
                        <span
                            class="text-[10px] font-black uppercase tracking-widest"
                            >{{ t("admin_announcement.smart_tool") }}</span
                        >
                    </div>
                    <p
                        class="text-xs mt-2.5 text-indigo-700/80 dark:text-indigo-300/80 leading-relaxed font-medium"
                    >
                        {{ t("admin_announcement.smart_tool_desc") }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="toggleTheme"
                    class="w-full flex items-center justify-center gap-3 py-3 rounded-xl border-2 border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-sm font-black text-slate-700 dark:text-slate-300 hover:border-rose-500/30 transition-all duration-300 shadow-sm"
                >
                    <Sun v-if="darkMode" :size="18" class="text-amber-500" />
                    <Moon v-else :size="18" class="text-indigo-500" />
                    <span class="uppercase tracking-tighter">{{
                        darkMode ? "Switch to Light" : "Switch to Dark"
                    }}</span>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.label-style {
    @apply text-[11px] font-black text-slate-500 uppercase tracking-wider block mb-1;
}

.input {
    @apply w-full px-4 py-3 text-sm rounded-xl border-2 border-slate-100 bg-slate-50 transition-all duration-200 
           focus:ring-0 focus:border-rose-500 focus:bg-white outline-none 
           dark:bg-slate-800 dark:border-slate-800 dark:text-white dark:focus:border-rose-500;
}

.btn-primary {
    @apply w-full flex items-center justify-center gap-3 py-4 rounded-xl bg-rose-600 text-white font-black 
           hover:bg-rose-700 hover:shadow-lg hover:shadow-rose-500/30 transition-all active:scale-[0.98] disabled:opacity-50;
}

.side-card {
    @apply p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm;
}

/* Fix for date input appearance on some browsers */
input[type="date"] {
    min-height: 45px;
}
</style>
