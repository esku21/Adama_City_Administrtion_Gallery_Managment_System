<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
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

// --- LANGUAGE LOGIC ---
const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("lang", lang);
};

// --- DARK MODE LOGIC ---
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

// --- SEARCH + PAGINATION ---
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

// --- STATS ---
const total = computed(() => props.guides.length);
const assigned = computed(() => props.guides.filter((g) => g.hall).length);
const unassigned = computed(() => props.guides.filter((g) => !g.hall).length);

// --- FORM LOGIC ---
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
    form.name = g.name;
    form.email = g.email;
    form.phone = g.phone;
    form.gender = g.gender;
    form.hall_id = g.hall_id;
    window.scrollTo({ top: 0, behavior: "smooth" });
};

// --- BULK UPLOAD LOGIC ---
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
    <Head :title="t('guides.title')" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
            <div
                class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6"
            >
                <div class="w-full lg:w-auto">
                    <h1
                        class="text-2xl sm:text-3xl font-black dark:text-white tracking-tighter uppercase leading-none"
                    >
                        {{ t("GUIDES MANAGEMENT CENTER") }}
                    </h1>
                    <p
                        class="text-xs sm:text-sm text-slate-400 font-medium mt-1"
                    >
                        {{ t("System-wide Guides Management") }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div
                        class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700"
                    >
                        <button
                            v-for="l in ['en', 'am', 'or']"
                            :key="l"
                            @click="changeLanguage(l)"
                            :class="[
                                'px-3 py-1.5 text-[10px] font-black rounded-lg uppercase transition-all',
                                locale === l
                                    ? 'bg-white dark:bg-slate-700 shadow-sm text-indigo-600 dark:text-indigo-400'
                                    : 'text-slate-400 hover:text-slate-600',
                            ]"
                        >
                            {{ l }}
                        </button>
                    </div>

                    <input
                        type="file"
                        ref="fileInput"
                        class="hidden"
                        accept=".csv, .xlsx"
                        @change="handleFileUpload"
                    />
                    <button
                        @click="triggerFileInput"
                        :disabled="bulkForm.processing"
                        class="flex-1 lg:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-emerald-100 transition disabled:opacity-50"
                    >
                        <Upload :size="14" />
                        <span>{{
                            bulkForm.processing
                                ? t("guides.uploading")
                                : t("guides.bulkImport")
                        }}</span>
                    </button>

                    <button
                        @click="toggleTheme"
                        class="p-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:scale-105 transition shadow-sm border border-slate-200 dark:border-slate-700"
                    >
                        <Sun v-if="dark" :size="20" class="text-yellow-500" />
                        <Moon v-else :size="20" />
                    </button>
                </div>
            </div>

            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
            >
                <div
                    class="card bg-white dark:bg-slate-900 border-indigo-100 dark:border-indigo-500/10 relative overflow-hidden group"
                >
                    <div class="flex items-center gap-4 mb-2">
                        <p class="stats-label text-indigo-600/70">
                            {{ t("Total Guides") }}
                        </p>
                    </div>
                    <h2
                        class="text-3xl sm:text-4xl font-black dark:text-white tracking-tighter"
                    >
                        {{ total }}<span class="text-indigo-500 ml-1">+</span>
                    </h2>
                </div>

                <div
                    class="card bg-white dark:bg-slate-900 border-emerald-100 dark:border-emerald-500/10 relative overflow-hidden group"
                >
                    <div class="flex items-center gap-4 mb-2">
                        <p class="stats-label text-emerald-600/70">
                            {{ t("assigned GUIDES") }}
                        </p>
                    </div>
                    <h2
                        class="text-3xl sm:text-4xl font-black dark:text-white tracking-tighter"
                    >
                        {{ assigned
                        }}<span class="text-emerald-500 ml-1">+</span>
                    </h2>
                </div>

                <div
                    class="card bg-white dark:bg-slate-900 border-amber-100 dark:border-amber-500/10 relative overflow-hidden group sm:col-span-2 lg:col-span-1"
                >
                    <div class="flex items-center gap-4 mb-2">
                        <p class="stats-label text-amber-600/70">
                            {{ t("unassigned GUIDES") }}
                        </p>
                    </div>
                    <h2
                        class="text-3xl sm:text-4xl font-black dark:text-white tracking-tighter"
                    >
                        {{ unassigned
                        }}<span class="text-amber-500 ml-1">+</span>
                    </h2>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 order-2 lg:order-1">
                    <div
                        class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm lg:sticky lg:top-8"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <h2
                                class="text-lg sm:text-xl font-black uppercase tracking-tighter dark:text-white"
                            >
                                {{
                                    isEditing
                                        ? t("guides.update")
                                        : t("guides.register")
                                }}
                            </h2>
                            <button
                                v-if="isEditing"
                                @click="resetForm"
                                class="text-slate-400 hover:text-red-500 p-1"
                            >
                                <X :size="20" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="label">{{
                                    t("guides.fields.name")
                                }}</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Full Name"
                                    class="input"
                                    required
                                />
                            </div>

                            <div>
                                <label class="label">{{
                                    t("guides.fields.email")
                                }}</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="email@example.com"
                                    class="input"
                                    required
                                />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="label">{{
                                        t("guides.fields.phone")
                                    }}</label>
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        placeholder="09..."
                                        class="input"
                                    />
                                </div>
                                <div>
                                    <label class="label">{{
                                        t("guides.fields.gender")
                                    }}</label>
                                    <select v-model="form.gender" class="input">
                                        <option value="">
                                            {{ t("guides.select") }}
                                        </option>
                                        <option value="Male">
                                            {{ t("guides.male") }}
                                        </option>
                                        <option value="Female">
                                            {{ t("guides.female") }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="label">{{
                                    t("guides.fields.hall")
                                }}</label>
                                <select v-model="form.hall_id" class="input">
                                    <option value="">
                                        {{ t("guides.unassigned") }}
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

                            <button
                                :disabled="form.processing"
                                class="btn w-full mt-2"
                            >
                                <Plus v-if="!isEditing" :size="18" />
                                <Pencil v-else :size="18" />
                                {{
                                    isEditing
                                        ? t("guides.buttons.save")
                                        : t("guides.buttons.confirm")
                                }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6 order-1 lg:order-2">
                    <div class="relative group">
                        <Search
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition"
                            :size="20"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            :placeholder="t('guides.search')"
                            class="w-full pl-12 pr-4 py-4 rounded-2xl border border-slate-200 bg-white dark:bg-slate-900 dark:border-slate-800 text-sm dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none shadow-sm"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="g in paginated"
                            :key="g.id"
                            class="card bg-white dark:bg-slate-900 hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                        >
                            <div>
                                <div
                                    class="flex justify-between items-start mb-4"
                                >
                                    <div
                                        class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20"
                                    >
                                        <span class="font-black text-lg">{{
                                            g.name.charAt(0)
                                        }}</span>
                                    </div>
                                    <div class="flex gap-1">
                                        <button
                                            @click="editGuide(g)"
                                            class="p-2 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-xl transition"
                                        >
                                            <Pencil :size="18" />
                                        </button>
                                        <Link
                                            :href="
                                                route(
                                                    'admin.guides.destroy',
                                                    g.id,
                                                )
                                            "
                                            method="delete"
                                            as="button"
                                            class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition"
                                        >
                                            <Trash2 :size="18" />
                                        </Link>
                                    </div>
                                </div>

                                <h3
                                    class="font-black text-slate-900 dark:text-white text-base truncate"
                                >
                                    {{ g.name }}
                                </h3>
                                <p
                                    class="text-sm text-slate-500 font-medium truncate mb-6"
                                >
                                    {{ g.email }}
                                </p>
                            </div>

                            <div
                                class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-800"
                            >
                                <span
                                    :class="[
                                        'text-[10px] font-black uppercase px-2.5 py-1.5 rounded-lg',
                                        g.hall
                                            ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'
                                            : 'bg-slate-100 text-slate-400 dark:bg-slate-800',
                                    ]"
                                >
                                    {{ g.hall?.name || t("guides.unassigned") }}
                                </span>
                                <span
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"
                                    >ID: #{{ g.id }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="totalPages > 1"
                        class="flex flex-wrap justify-center items-center gap-2 pt-6"
                    >
                        <button
                            v-for="p in totalPages"
                            :key="p"
                            @click="currentPage = p"
                            :class="[
                                'w-10 h-10 rounded-xl font-black text-xs transition-all',
                                currentPage === p
                                    ? 'bg-slate-950 text-white shadow-lg'
                                    : 'bg-white dark:bg-slate-900 text-slate-400 border border-slate-200 dark:border-slate-800 hover:border-indigo-500',
                            ]"
                        >
                            {{ p }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.card {
    @apply p-5 sm:p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm transition-all;
}
.input {
    @apply w-full px-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-sm dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all;
}
.btn {
    @apply flex items-center justify-center gap-2 bg-slate-950 dark:bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest hover:bg-slate-800 dark:hover:bg-indigo-500 transition-all shadow-lg active:scale-95 disabled:opacity-50;
}
.label {
    @apply text-xs font-black uppercase text-slate-500 dark:text-slate-400 ml-1 mb-2 block;
}
.stats-label {
    @apply text-[11px] font-black uppercase tracking-widest;
}
</style>
