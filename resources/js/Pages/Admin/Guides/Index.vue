<script setup>
import { ref, computed, watch, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import {
    Search,
    Plus,
    Pencil,
    Trash2,
    Sun,
    Moon,
    Upload,
    X,
} from "lucide-vue-next";

const { t, locale } = useI18n();

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("lang", lang);
};

const dark = ref(false);
onMounted(() => {
    dark.value = localStorage.getItem("theme") === "dark";
    document.documentElement.classList.toggle("dark", dark.value);
});
const toggleTheme = () => {
    dark.value = !dark.value;
    document.documentElement.classList.toggle("dark", dark.value);
    localStorage.setItem("theme", dark.value ? "dark" : "light");
};

const props = defineProps({ guides: Array, halls: Array });

const searchQuery = ref("");
const currentPage = ref(1);
const perPage = ref(6);

const filtered = computed(() =>
    props.guides.filter(
        (g) =>
            g.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            g.email.toLowerCase().includes(searchQuery.value.toLowerCase()),
    ),
);

const paginated = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filtered.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
    Math.ceil(filtered.value.length / perPage.value),
);
watch(searchQuery, () => (currentPage.value = 1));

const total = computed(() => props.guides.length);
const assigned = computed(() => props.guides.filter((g) => g.hall).length);
const unassigned = computed(() => props.guides.filter((g) => !g.hall).length);

const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: "",
    email: "",
    phone: "",
    gender: "",
    hall_id: "",
});

const submit = () => {
    if (isEditing.value) {
        form.put(route("admin.guides.update", editingId.value), {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post(route("admin.guides.store"), {
            onSuccess: () => resetForm(),
        });
    }
};

const resetForm = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
};

const editGuide = (g) => {
    isEditing.value = true;
    editingId.value = g.id;
    form.defaults({
        name: g.name,
        email: g.email,
        phone: g.phone,
        gender: g.gender,
        hall_id: g.hall_id,
    });
    form.reset();
    window.scrollTo({ top: 0, behavior: "smooth" });
};

const fileInput = ref(null);
const bulkForm = useForm({ file: null });
const triggerFileInput = () => fileInput.value.click();

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        bulkForm.file = file;
        bulkForm.post(route("admin.guides.bulk-store"), {
            preserveScroll: true,
            onSuccess: () => {
                bulkForm.reset();
                e.target.value = "";
            },
        });
    }
};
</script>

<template>
    <Head :title="t('admin_guides.title')" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
            <div
                class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6"
            >
                <div>
                    <h1
                        class="text-2xl sm:text-3xl font-black dark:text-white tracking-tighter uppercase"
                    >
                        {{ t("admin_guides.header") }}
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-slate-400 font-medium mt-1"
                    >
                        {{ t("admin_guides.sub_header") }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div
                        class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border"
                    >
                        <button
                            v-for="l in ['en', 'am', 'or']"
                            :key="l"
                            @click="changeLanguage(l)"
                            :class="[
                                'px-3 py-1.5 text-[10px] font-black rounded-lg uppercase transition-all',
                                locale === l
                                    ? 'bg-white shadow-sm text-indigo-600'
                                    : 'text-slate-400',
                            ]"
                        >
                            {{ l }}
                        </button>
                    </div>
                    <button
                        @click="triggerFileInput"
                        class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl font-bold text-[10px] uppercase"
                    >
                        <Upload :size="14" />
                        {{
                            bulkForm.processing
                                ? t("admin_guides.uploading")
                                : t("admin_guides.bulkImport")
                        }}
                    </button>
                    <button
                        @click="toggleTheme"
                        class="p-2.5 rounded-xl bg-white border shadow-sm"
                    >
                        <Sun v-if="dark" :size="20" class="text-yellow-500" />
                        <Moon v-else :size="20" />
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card bg-blue-600 text-white">
                    <p class="stats-label text-blue-100">
                        {{ t("admin_guides.total") }}
                    </p>
                    <h2 class="text-4xl font-black tracking-tighter">
                        {{ total }}
                    </h2>
                </div>
                <div class="card bg-orange-500 text-white">
                    <p class="stats-label text-orange-100">
                        {{ t("admin_guides.assigned") }}
                    </p>
                    <h2 class="text-4xl font-black tracking-tighter">
                        {{ assigned }}
                    </h2>
                </div>
                <div
                    class="card bg-slate-800 text-white sm:col-span-2 lg:col-span-1"
                >
                    <p class="stats-label text-slate-300">
                        {{ t("admin_guides.unassigned") }}
                    </p>
                    <h2 class="text-4xl font-black tracking-tighter">
                        {{ unassigned }}
                    </h2>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border shadow-sm"
                    >
                        <h2 class="text-xl font-black uppercase mb-6">
                            {{
                                isEditing
                                    ? t("admin_guides.update")
                                    : t("admin_guides.register")
                            }}
                        </h2>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="label">{{
                                    t("admin_guides.fields.name")
                                }}</label
                                ><input
                                    v-model="form.name"
                                    class="input"
                                    required
                                />
                            </div>
                            <div>
                                <label class="label">{{
                                    t("admin_guides.fields.email")
                                }}</label
                                ><input
                                    v-model="form.email"
                                    class="input"
                                    required
                                />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="label">{{
                                        t("admin_guides.fields.phone")
                                    }}</label
                                    ><input
                                        v-model="form.phone"
                                        class="input"
                                    />
                                </div>
                                <div>
                                    <label class="label">{{
                                        t("admin_guides.fields.gender")
                                    }}</label>
                                    <select v-model="form.gender" class="input">
                                        <option value="">
                                            {{ t("admin_guides.select") }}
                                        </option>
                                        <option value="Male">
                                            {{ t("admin_guides.male") }}
                                        </option>
                                        <option value="Female">
                                            {{ t("admin_guides.female") }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="label">{{
                                    t("admin_guides.fields.hall")
                                }}</label>
                                <select v-model="form.hall_id" class="input">
                                    <option value="">
                                        {{ t("admin_guides.unassigned") }}
                                    </option>
                                    <option
                                        v-for="h in halls"
                                        :key="h.id"
                                        :value="h.id"
                                    >
                                        {{ h.name }}
                                    </option>
                                </select>
                            </div>
                            <button class="btn w-full">
                                {{
                                    isEditing
                                        ? t("admin_guides.buttons.save")
                                        : t("admin_guides.buttons.confirm")
                                }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <input
                        v-model="searchQuery"
                        :placeholder="t('admin_guides.search')"
                        class="input"
                    />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="g in paginated"
                            :key="g.id"
                            class="card bg-white dark:bg-slate-900"
                        >
                            <div class="flex justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center font-black"
                                >
                                    {{ g.name.charAt(0) }}
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        @click="editGuide(g)"
                                        class="text-slate-400 hover:text-indigo-600"
                                    >
                                        <Pencil :size="18" />
                                    </button>
                                    <Link
                                        :href="
                                            route('admin.guides.destroy', g.id)
                                        "
                                        method="delete"
                                        as="button"
                                        ><Trash2
                                            :size="18"
                                            class="text-slate-400 hover:text-red-500"
                                    /></Link>
                                </div>
                            </div>
                            <h3 class="font-black">{{ g.name }}</h3>
                            <p class="text-sm text-slate-500">{{ g.email }}</p>
                            <div
                                class="pt-4 border-t mt-4 flex justify-between items-center"
                            >
                                <span
                                    class="text-[10px] font-black px-2 py-1 rounded-lg bg-indigo-50 text-indigo-600"
                                    >{{
                                        g.hall?.name ||
                                        t("admin_guides.unassigned")
                                    }}</span
                                >
                                <span class="text-xs text-slate-400"
                                    >#{{ g.id }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.card {
    @apply p-6 rounded-[2rem] border shadow-sm;
}
.input {
    @apply w-full px-4 py-3 rounded-xl border bg-slate-50 dark:bg-slate-800;
}
.btn {
    @apply bg-slate-950 text-white py-4 rounded-2xl font-black uppercase text-xs;
}
.label {
    @apply text-xs font-black uppercase text-slate-500 mb-1;
}
.stats-label {
    @apply text-[10px] font-black uppercase;
}
</style>
