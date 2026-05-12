<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("guide.settings.password.update"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <Head :title="t('settings.title')" />

    <GuideLayout>
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- HEADER -->
            <div class="mb-6">
                <h2
                    class="text-2xl sm:text-3xl font-black text-zinc-900 uppercase tracking-tight"
                >
                    {{ t("settings.security") }}
                    <span
                        class="text-emerald-500 underline decoration-2 underline-offset-4"
                    >
                        {{ t("settings.settings") }}
                    </span>
                </h2>

                <p
                    class="text-[10px] text-zinc-400 font-bold uppercase tracking-[0.2em] mt-1"
                >
                    {{ t("settings.subtitle") }} • Adama City Hall
                </p>
            </div>

            <!-- CARD -->
            <div
                class="bg-white rounded-2xl border border-zinc-100 shadow-md overflow-hidden p-6 sm:p-8"
            >
                <header class="mb-6 border-b border-zinc-50 pb-4">
                    <h3 class="text-lg font-bold text-zinc-900">
                        {{ t("settings.update_password") }}
                    </h3>
                    <p class="text-xs text-zinc-500 mt-1">
                        {{ t("settings.password_desc") }}
                    </p>
                </header>

                <!-- FORM -->
                <form @submit.prevent="updatePassword" class="space-y-4">
                    <!-- CURRENT PASSWORD -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1"
                        >
                            {{ t("settings.current_password") }}
                        </label>

                        <input
                            ref="currentPasswordInput"
                            v-model="form.current_password"
                            type="password"
                            class="w-full bg-zinc-50 border border-zinc-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all outline-none"
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />

                        <p
                            v-if="form.errors.current_password"
                            class="text-rose-500 text-[10px] mt-1.5 ml-1 font-bold italic"
                        >
                            {{ form.errors.current_password }}
                        </p>
                    </div>

                    <!-- NEW PASSWORD -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1"
                            >
                                {{ t("settings.new_password") }}
                            </label>

                            <input
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="w-full bg-zinc-50 border border-zinc-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all outline-none"
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />

                            <p
                                v-if="form.errors.password"
                                class="text-rose-500 text-[10px] mt-1.5 ml-1 font-bold italic"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1"
                            >
                                {{ t("settings.confirm_password") }}
                            </label>

                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="w-full bg-zinc-50 border border-zinc-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all outline-none"
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                        </div>
                    </div>

                    <!-- ACTION -->
                    <div class="flex items-center gap-4 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-zinc-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 active:scale-95 transition-all disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? t("settings.processing")
                                    : t("settings.save")
                            }}
                        </button>

                        <Transition
                            enter-from-class="opacity-0 translate-x-2"
                            leave-to-class="opacity-0"
                            class="transition duration-300"
                        >
                            <div
                                v-if="form.recentlySuccessful"
                                class="flex items-center gap-2 text-emerald-600"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="3"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                                <span
                                    class="text-xs font-bold uppercase tracking-tight"
                                >
                                    {{ t("settings.saved") }}
                                </span>
                            </div>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </GuideLayout>
</template>
