<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";

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
    <Head title="Account Settings" />

    <GuideLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-10">
                <h2
                    class="text-3xl font-black text-zinc-900 uppercase tracking-tighter italic"
                >
                    Security
                    <span
                        class="text-emerald-500 underline decoration-4 underline-offset-8"
                        >Settings</span
                    >
                </h2>
                <p
                    class="text-[10px] text-zinc-400 font-bold uppercase tracking-[0.3em] mt-3"
                >
                    Manage your access credentials • Adama City Hall
                </p>
            </div>

            <div
                class="bg-white rounded-[2.5rem] border border-zinc-100 shadow-sm overflow-hidden p-10"
            >
                <header class="mb-8">
                    <h3 class="text-lg font-bold text-zinc-900">
                        Update Password
                    </h3>
                    <p class="text-sm text-zinc-500 mt-1">
                        Ensure your account is using a long, random password to
                        stay secure.
                    </p>
                </header>

                <form
                    @submit.prevent="updatePassword"
                    class="space-y-6 max-w-xl"
                >
                    <div>
                        <label
                            class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1"
                            >Current Password</label
                        >
                        <input
                            ref="currentPasswordInput"
                            v-model="form.current_password"
                            type="password"
                            class="w-full bg-zinc-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all"
                            autocomplete="current-password"
                        />
                        <p
                            v-if="form.errors.current_password"
                            class="text-rose-500 text-xs mt-2 ml-1 font-bold"
                        >
                            {{ form.errors.current_password }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1"
                            >New Password</label
                        >
                        <input
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="w-full bg-zinc-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all"
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-rose-500 text-xs mt-2 ml-1 font-bold"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1"
                            >Confirm Password</label
                        >
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full bg-zinc-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all"
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-rose-500 text-xs mt-2 ml-1 font-bold"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-4 bg-zinc-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all disabled:opacity-50"
                        >
                            Save Changes
                        </button>

                        <Transition
                            enter-from-class="opacity-0"
                            leave-to-class="opacity-0"
                            class="transition ease-in-out"
                        >
                            <p
                                v-if="form.recentlySuccessful"
                                class="text-sm text-emerald-600 font-bold italic"
                            >
                                Saved successfully.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </GuideLayout>
</template>
