<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const props = defineProps({
    feedbacks: Array,
    summary: Object,
});

const searchQuery = ref("");
const selectedRating = ref("");

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("lang", lang);
};

const filteredFeedbacks = computed(() => {
    if (!props.feedbacks) return [];
    return props.feedbacks.filter((fb) => {
        const userName =
            (fb.user?.firstName || "") + " " + (fb.user?.lastName || "") ||
            t("admin_feedbacks.visitor");
        const userEmail = fb.user?.email || "";
        const message = fb.message || "";
        const matchesSearch =
            userName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            userEmail.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            message.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesRating =
            selectedRating.value === "" || fb.rating == selectedRating.value;
        return matchesSearch && matchesRating;
    });
});

const getImageUrl = (url) => {
    if (!url) return null;
    const cleanUrl = url.replace(/[\[\]"']/g, "").replace(/^\//, "");
    return `/storage/${cleanUrl}`;
};

const confirmDelete = () => confirm(t("admin_feedbacks.delete_confirm"));

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? "N/A" : date.toLocaleDateString();
};
</script>

<template>
    <Head :title="t('admin_feedbacks.title')" />
    <AuthenticatedLayout>
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 py-6 lg:py-10 space-y-8 min-h-screen"
        >
            <div
                class="flex flex-col md:flex-row md:items-end justify-between gap-6"
            >
                <div class="space-y-1">
                    <h1
                        class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-slate-900"
                    >
                        {{ t("admin_feedbacks.visitor") }}
                        <span class="text-indigo-600">Feedbacks</span>
                    </h1>
                    <p class="text-xs text-slate-400 font-bold uppercase">
                        {{ t("admin_feedbacks.review_mgmt") }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <button
                        v-for="lang in ['en', 'or', 'am']"
                        :key="lang"
                        @click="changeLanguage(lang)"
                        class="px-4 py-2 bg-white border border-slate-300 rounded-lg uppercase font-bold text-xs hover:bg-indigo-600 hover:text-white transition"
                    >
                        {{ lang }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"
                >
                    <p class="text-[10px] uppercase font-bold text-slate-400">
                        {{ t("admin_feedbacks.total_satisfied") }}
                    </p>
                    <h3 class="text-3xl font-black text-emerald-600">
                        {{ summary?.total_satisfied || 0 }}+
                    </h3>
                </div>
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"
                >
                    <p class="text-[10px] uppercase font-bold text-slate-400">
                        {{ t("admin_feedbacks.natural_feedback") }}
                    </p>
                    <h3 class="text-3xl font-black text-amber-500">
                        {{ summary?.total_natural || 0 }}+
                    </h3>
                </div>
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100"
                >
                    <p class="text-[10px] uppercase font-bold text-slate-400">
                        {{ t("admin_feedbacks.total_unsatisfied") }}
                    </p>
                    <h3 class="text-3xl font-black text-rose-500">
                        {{ summary?.total_unsatisfied || 0 }}+
                    </h3>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <input
                    v-model="searchQuery"
                    :placeholder="t('admin_feedbacks.search_placeholder')"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-sm flex-grow"
                />
                <select
                    v-model="selectedRating"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-sm"
                >
                    <option value="">
                        {{ t("admin_feedbacks.all_ratings") }}
                    </option>
                    <option v-for="n in 5" :key="n" :value="n">
                        {{ n }} {{ t("admin_feedbacks.stars") }}
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="fb in filteredFeedbacks"
                    :key="fb.id"
                    class="bg-white p-6 rounded-2xl border border-slate-200"
                >
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="font-bold">
                            {{ fb.user?.firstName }} {{ fb.user?.lastName }}
                        </h4>
                        <Link
                            :href="route('admin.feedbacks.destroy', fb.id)"
                            method="delete"
                            as="button"
                            :onBefore="confirmDelete"
                            class="text-rose-500 text-xs"
                            >Delete</Link
                        >
                    </div>
                    <p class="text-sm text-slate-600 italic">
                        "{{ fb.message }}"
                    </p>
                    <p class="text-[10px] text-slate-400 mt-4">
                        {{ formatDate(fb.created_at) }}
                    </p>
                </div>
            </div>

            <div
                v-if="filteredFeedbacks.length === 0"
                class="text-center py-20 text-slate-400 font-bold"
            >
                {{ t("admin_feedbacks.no_matches") }}
            </div>
        </div>
    </AuthenticatedLayout>
</template>
