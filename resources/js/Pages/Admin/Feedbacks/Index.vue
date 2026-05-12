<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    feedbacks: Array,
    summary: Object,
});

const searchQuery = ref("");
const selectedRating = ref("");

/**
 * Filtered feedbacks based on search query and star rating.
 */
const filteredFeedbacks = computed(() => {
    if (!props.feedbacks) return [];
    return props.feedbacks.filter((fb) => {
        const userName =
            (fb.user?.firstName || "") + " " + (fb.user?.lastName || "") ||
            t("feedbacks.visitor");
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

/**
 * Helper function to get the full image URL.
 */
const getImageUrl = (url) => {
    if (!url) return null;
    const cleanUrl = url.replace(/[\[\]"']/g, "").replace(/^\//, "");
    return `/storage/${cleanUrl}`;
};

/**
 * Function to handle deletion confirmation.
 */
const confirmDelete = () => {
    return window.confirm(
        t("feedbacks.delete_confirm") ||
            "Are you sure you want to delete this?",
    );
};

/**
 * Format date correctly
 */
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? "N/A" : date.toLocaleDateString();
};
</script>

<template>
    <Head :title="t('feedbacks.title')" />

    <AuthenticatedLayout>
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 py-6 lg:py-10 space-y-8 bg-slate-50 min-h-screen"
        >
            <div
                class="flex flex-col md:flex-row md:items-end justify-between gap-6"
            >
                <div class="space-y-1">
                    <h1
                        class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-slate-900"
                    >
                        {{ t("feedbacks.visitor") }}
                        <span class="text-indigo-600">Feedbacks</span>
                    </h1>
                    <p
                        class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest"
                    >
                        {{ t("feedbacks.review_mgmt") }} •
                        {{ filteredFeedbacks.length }}
                        {{ t("feedbacks.submissions") }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-grow sm:flex-grow-0">
                        <span
                            class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"
                            >search</span
                        >
                        <input
                            v-model="searchQuery"
                            type="text"
                            :placeholder="t('feedbacks.search_placeholder')"
                            class="pl-10 pr-4 py-2.5 w-full md:w-64 rounded-xl bg-white border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                        />
                    </div>

                    <select
                        v-model="selectedRating"
                        class="px-4 py-2.5 w-full sm:w-44 rounded-xl bg-white border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                    >
                        <option value="">
                            {{ t("feedbacks.all_ratings") }}
                        </option>
                        <option
                            v-for="star in [5, 4, 3, 2, 1]"
                            :key="star"
                            :value="star"
                        >
                            {{ star }} {{ t("feedbacks.stars") }}
                        </option>
                    </select>
                </div>
            </div>

            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
            >
                <div
                    v-for="(stat, key) in [
                        {
                            label: 'Total Satisfied',
                            val: summary?.total_satisfied,
                            color: 'text-emerald-600',
                            bg: 'bg-emerald-50',
                            icon: 'sentiment_satisfied_alt',
                        },
                        {
                            label: 'Natural Feedback',
                            val: summary?.total_natural,
                            color: 'text-amber-500',
                            bg: 'bg-amber-50',
                            icon: 'sentiment_neutral',
                        },
                        {
                            label: 'Total Unsatisfied',
                            val: summary?.total_unsatisfied,
                            color: 'text-rose-500',
                            bg: 'bg-rose-50',
                            icon: 'sentiment_very_dissatisfied',
                        },
                    ]"
                    :key="key"
                    class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition group"
                >
                    <div>
                        <p
                            class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1"
                        >
                            {{ stat.label }}
                        </p>
                        <h3
                            class="text-2xl sm:text-3xl font-black"
                            :class="stat.color"
                        >
                            {{ stat.val || 0 }}+
                        </h3>
                    </div>
                    <div
                        :class="[stat.bg, stat.color]"
                        class="h-12 w-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110"
                    >
                        <span class="material-icons-outlined">{{
                            stat.icon
                        }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="feedback in filteredFeedbacks"
                    :key="feedback.id"
                    class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col group"
                >
                    <div class="flex justify-between items-start mb-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 sm:h-11 sm:w-11 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-black shrink-0"
                            >
                                {{ feedback.user?.firstName?.charAt(0) || "V" }}
                            </div>
                            <div class="overflow-hidden">
                                <h4
                                    class="font-black text-xs sm:text-sm uppercase text-slate-900 truncate"
                                >
                                    {{
                                        feedback.user
                                            ? `${feedback.user.firstName} ${feedback.user.lastName}`
                                            : t("feedbacks.visitor")
                                    }}
                                </h4>
                                <p
                                    class="text-[10px] text-indigo-500 font-bold truncate"
                                >
                                    {{
                                        feedback.user?.email ||
                                        t("feedbacks.no_email")
                                    }}
                                </p>
                            </div>
                        </div>

                        <Link
                            :href="
                                route('admin.feedbacks.destroy', feedback.id)
                            "
                            method="delete"
                            as="button"
                            :onBefore="confirmDelete"
                            class="text-rose-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-xl transition"
                        >
                            <span class="material-icons-outlined text-sm"
                                >delete</span
                            >
                        </Link>
                    </div>

                    <div
                        class="bg-slate-50 p-4 rounded-xl border border-slate-100 mb-4 flex-grow"
                    >
                        <p
                            class="text-xs text-slate-600 leading-relaxed italic"
                        >
                            "{{ feedback.message }}"
                        </p>
                    </div>

                    <div v-if="feedback.image_urls?.length" class="mb-4">
                        <p
                            class="text-[9px] font-black text-slate-400 uppercase mb-2 tracking-tighter"
                        >
                            Evidence Attachments:
                        </p>
                        <div class="grid grid-cols-3 gap-2">
                            <div
                                v-for="(path, index) in feedback.image_urls"
                                :key="index"
                                class="relative group/img overflow-hidden rounded-lg border border-slate-200"
                            >
                                <img
                                    :src="getImageUrl(path)"
                                    class="w-full h-16 sm:h-20 object-cover"
                                    alt="Feedback Image"
                                    @error="
                                        (e) =>
                                            (e.target.src =
                                                'https://placehold.co/200x200?text=Error')
                                    "
                                />
                                <a
                                    :href="getImageUrl(path)"
                                    target="_blank"
                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center"
                                >
                                    <span
                                        class="material-icons-outlined text-white text-xs"
                                        >visibility</span
                                    >
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-auto pt-4 flex justify-between items-center border-t border-slate-100"
                    >
                        <span
                            class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase"
                        >
                            {{ formatDate(feedback.created_at) }}
                        </span>

                        <div class="flex text-amber-400">
                            <span
                                v-for="i in 5"
                                :key="i"
                                class="material-icons text-[12px] sm:text-[14px]"
                            >
                                {{
                                    i <= feedback.rating
                                        ? "star"
                                        : "star_border"
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="filteredFeedbacks.length === 0"
                class="bg-white p-12 sm:p-20 rounded-3xl border border-dashed border-slate-300 text-center"
            >
                <span
                    class="material-icons-outlined text-5xl sm:text-6xl text-slate-200 mb-4"
                    >rate_review</span
                >
                <p
                    class="text-slate-400 font-bold uppercase tracking-widest text-[10px] sm:text-xs"
                >
                    No feedbacks found matching your criteria
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Optional: Hide scrollbars for cleaner inputs on mobile */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>
