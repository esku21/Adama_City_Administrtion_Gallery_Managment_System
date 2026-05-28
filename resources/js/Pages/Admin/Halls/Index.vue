<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import {
    Landmark,
    Plus,
    Trash2,
    CheckCircle,
    AlertCircle,
    Pencil,
    X,
    Languages,
    Loader2,
} from "lucide-vue-next";

const { t, locale } = useI18n();

const props = defineProps({
    halls: { type: Array, default: () => [] },
});

// ---------------- LANGUAGE ----------------
const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("locale", lang);
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
    name: "",
    location: "",
    description: "",
});

const editingHall = ref(null);

const submit = () => {
    if (editingHall.value) {
        form.put(route("admin.halls.update", editingHall.value.id), {
            preserveScroll: true,
            onSuccess: () => resetForm(),
        });
    } else {
        form.post(route("admin.halls.store"), {
            preserveScroll: true,
            onSuccess: () => resetForm(),
        });
    }
};

const editHall = (hall) => {
    editingHall.value = hall;
    form.name = hall.name;
    form.location = hall.location;
    form.description = hall.description;
    form.clearErrors(); // Clean slate when starting edit
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
    editingHall.value = null;
};

const deleteHall = (id) => {
    if (!confirm(t("halls.delete_confirm"))) return;
    router.delete(route("admin.halls.destroy", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="t('halls.title')" />

    <AuthenticatedLayout>
        <!-- NOTIFICATION TOAST -->
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
                class="fixed top-5 right-5 z-[100] min-w-[300px]"
            >
                <div
                    :class="[
                        'px-4 py-3 rounded-xl shadow-lg border flex items-center gap-3',
                        notification.type === 'success'
                            ? 'bg-emerald-50 border-emerald-100 text-emerald-800'
                            : 'bg-red-50 border-red-100 text-red-800',
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

        <!-- LANGUAGE SWITCH -->
        <div class="bg-white border-b px-6 py-2 flex justify-end gap-4">
            <div class="flex items-center gap-2 text-slate-400">
                <Languages :size="16" />
            </div>
            <button
                v-for="lang in ['en', 'am', 'or']"
                :key="lang"
                @click="changeLanguage(lang)"
                :class="
                    locale === lang
                        ? 'text-indigo-600 font-bold underline'
                        : 'text-slate-500'
                "
                class="text-xs uppercase"
            >
                {{ lang === "am" ? "አማ" : lang.toUpperCase() }}
            </button>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- HEADER -->
            <div
                class="flex flex-col sm:flex-row sm:justify-between gap-4 mb-6"
            >
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        {{ t("halls.title") }}
                    </h1>
                    <p class="text-sm text-slate-500">
                        {{ t("halls.subtitle") }}
                    </p>
                </div>
                <div
                    class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl font-bold self-start"
                >
                    {{ t("halls.total") }}: {{ halls.length }}
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- FORM -->
                <div
                    class="bg-white rounded-2xl shadow-sm border p-5 h-fit sticky top-6"
                >
                    <div class="flex justify-between mb-4">
                        <h2
                            class="font-semibold flex items-center gap-2 text-slate-800"
                        >
                            <Plus v-if="!editingHall" :size="18" />
                            <Pencil v-else :size="18" />
                            {{
                                editingHall
                                    ? t("halls.edit_hall")
                                    : t("halls.add_new")
                            }}
                        </h2>
                        <button
                            v-if="editingHall"
                            @click="resetForm"
                            class="text-red-500 text-xs flex items-center gap-1 hover:underline"
                        >
                            <X :size="14" /> {{ t("halls.cancel") }}
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <input
                                v-model="form.name"
                                type="text"
                                :class="{
                                    'border-red-500 bg-red-50':
                                        form.errors.name,
                                }"
                                class="w-full border rounded-lg p-2.5 text-sm transition focus:ring-2 focus:ring-indigo-500 outline-none"
                                :placeholder="t('halls.name_placeholder')"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <input
                                v-model="form.location"
                                type="text"
                                :class="{
                                    'border-red-500 bg-red-50':
                                        form.errors.location,
                                }"
                                class="w-full border rounded-lg p-2.5 text-sm transition focus:ring-2 focus:ring-indigo-500 outline-none"
                                :placeholder="t('halls.location_placeholder')"
                            />
                            <p
                                v-if="form.errors.location"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.location }}
                            </p>
                        </div>

                        <div>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                :class="{
                                    'border-red-500 bg-red-50':
                                        form.errors.description,
                                }"
                                class="w-full border rounded-lg p-2.5 text-sm transition focus:ring-2 focus:ring-indigo-500 outline-none"
                                :placeholder="t('halls.desc_placeholder')"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition disabled:opacity-50"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="animate-spin"
                                :size="18"
                            />
                            <CheckCircle v-else :size="18" />
                            {{
                                editingHall
                                    ? t("halls.update")
                                    : t("halls.save")
                            }}
                        </button>
                    </form>
                </div>

                <!-- TABLE -->
                <div
                    class="lg:col-span-2 bg-white rounded-2xl shadow-sm border overflow-hidden"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead
                                class="bg-slate-50 text-slate-500 text-xs uppercase border-b"
                            >
                                <tr>
                                    <th class="px-6 py-4 text-left font-bold">
                                        {{ t("halls.table_details") }}
                                    </th>
                                    <th class="px-6 py-4 text-left font-bold">
                                        {{ t("halls.table_location") }}
                                    </th>
                                    <th class="px-6 py-4 text-right font-bold">
                                        {{ t("halls.table_actions") }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr
                                    v-for="hall in halls"
                                    :key="hall.id"
                                    class="hover:bg-slate-50/80 transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2 bg-indigo-50 text-indigo-600 rounded-lg"
                                            >
                                                <Landmark :size="18" />
                                            </div>
                                            <div
                                                class="font-bold text-slate-900"
                                            >
                                                {{ hall.name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ hall.location || "---" }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                @click="editHall(hall)"
                                                class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-lg transition"
                                            >
                                                <Pencil :size="18" />
                                            </button>
                                            <button
                                                @click="deleteHall(hall.id)"
                                                class="p-2 hover:bg-red-50 text-slate-400 hover:text-red-600 rounded-lg transition"
                                            >
                                                <Trash2 :size="18" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="halls.length === 0" class="p-16 text-center">
                        <div
                            class="inline-flex p-4 rounded-full bg-slate-50 text-slate-300 mb-4"
                        >
                            <AlertCircle :size="40" />
                        </div>
                        <p class="font-bold text-slate-900 text-lg">
                            {{ t("halls.empty_title") }}
                        </p>
                        <p class="text-sm text-slate-500 max-w-xs mx-auto">
                            {{ t("halls.empty_desc") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
