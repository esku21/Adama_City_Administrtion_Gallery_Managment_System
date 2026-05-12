<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    feedbacks: Array,
    hallName: String,
});

const page = usePage();
const showSuccessPopup = ref(false);
const successMessage = ref("");

watch(
    () => page.props.flash.success,
    (msg) => {
        if (msg) {
            successMessage.value = msg;
            showSuccessPopup.value = true;
            setTimeout(() => {
                showSuccessPopup.value = false;
            }, 3000);
        }
    },
    { immediate: true },
);

const searchQuery = ref("");

const deleteFeedback = (id) => {
    if (confirm(t("feedbacks_for_guide.delete_confirm"))) {
        router.delete(route("guide.feedbacks.destroy", id), {
            preserveScroll: true,
        });
    }
};

const filteredFeedbacks = computed(() => {
    return props.feedbacks.filter((fb) => {
        const userName =
            `${fb.user?.firstName} ${fb.user?.lastName}`.toLowerCase();
        const message = fb.message?.toLowerCase() || "";
        return (
            userName.includes(searchQuery.value.toLowerCase()) ||
            message.includes(searchQuery.value.toLowerCase())
        );
    });
});
</script>

<template>
    <Head :title="`${t('feedbacks_for_guide.title')} - ${hallName}`" />

    <GuideLayout>
        <transition name="pop">
            <div
                v-if="showSuccessPopup"
                class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-700"
            >
                <span class="material-icons text-emerald-400"
                    >check_circle</span
                >
                <p class="text-xs font-bold uppercase tracking-widest">
                    {{ successMessage }}
                </p>
                <button
                    @click="showSuccessPopup = false"
                    class="hover:text-rose-400 transition-colors"
                >
                    <span class="material-icons text-sm">close</span>
                </button>
            </div>
        </transition>

        <div class="p-8 space-y-8 w-full">
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm"
            >
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-slate-900 uppercase"
                    >
                        {{ hallName }}
                        <span class="text-indigo-600">{{
                            t("feedbacks_for_guide.title")
                        }}</span>
                    </h2>
                    <p
                        class="text-[10px] text-slate-400 uppercase tracking-[0.2em] font-bold mt-1"
                    >
                        {{ t("feedbacks_for_guide.portal_label") }}
                    </p>
                </div>

                <div class="relative flex items-center group">
                    <span
                        class="material-icons-outlined absolute left-4 text-slate-400 text-lg group-focus-within:text-indigo-500 transition-colors pointer-events-none"
                        >search</span
                    >
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="
                            t('feedbacks_for_guide.search_placeholder')
                        "
                        class="bg-slate-50 border border-slate-200 rounded-2xl py-3 pl-12 pr-4 text-xs w-72 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-bold text-slate-700"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div
                    v-for="feedback in filteredFeedbacks"
                    :key="feedback.id"
                    class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 group relative"
                >
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black text-lg uppercase shadow-lg shadow-indigo-100"
                            >
                                {{ feedback.user?.firstName?.charAt(0) || "V" }}
                            </div>
                            <div>
                                <h4
                                    class="font-black text-slate-900 text-sm uppercase tracking-tight"
                                >
                                    {{ feedback.user?.firstName }}
                                    {{ feedback.user?.lastName }}
                                </h4>
                                <p
                                    class="text-[10px] text-indigo-500 font-bold tracking-widest"
                                >
                                    {{
                                        feedback.user?.email ||
                                        t("feedbacks_for_guide.visitor")
                                    }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="deleteFeedback(feedback.id)"
                            class="h-9 w-9 flex items-center justify-center rounded-xl text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition-all border border-transparent hover:border-rose-100"
                        >
                            <span class="material-icons-outlined text-xl"
                                >delete_outline</span
                            >
                        </button>
                    </div>

                    <div
                        class="bg-slate-50 p-5 rounded-3xl border border-slate-100 relative mb-6"
                    >
                        <span
                            class="material-icons text-slate-200 absolute -top-2 -left-1 text-3xl"
                            >format_quote</span
                        >
                        <p
                            class="text-xs text-slate-600 leading-relaxed font-medium relative z-10"
                        >
                            {{ feedback.message }}
                        </p>
                    </div>

                    <div
                        class="pt-5 border-t border-slate-50 flex items-center justify-between"
                    >
                        <div class="flex flex-col">
                            <span
                                class="text-[9px] font-black text-slate-300 uppercase tracking-widest"
                                >{{ t("feedbacks_for_guide.received") }}</span
                            >
                            <span class="text-[10px] font-bold text-slate-500">
                                {{
                                    feedback.created_at
                                        ? new Date(
                                              feedback.created_at,
                                          ).toLocaleDateString()
                                        : t("feedbacks_for_guide.today")
                                }}
                            </span>
                        </div>
                        <div
                            class="flex gap-0.5 bg-slate-100 px-3 py-1.5 rounded-xl"
                        >
                            <span
                                v-for="i in 5"
                                :key="i"
                                class="material-icons text-[14px]"
                                :class="
                                    i <= feedback.rating
                                        ? 'text-amber-400'
                                        : 'text-slate-200'
                                "
                                >star</span
                            >
                        </div>
                    </div>
                </div>

                <div
                    v-if="filteredFeedbacks.length === 0"
                    class="col-span-full py-32 bg-white rounded-[2rem] border border-dashed border-slate-200 flex flex-col items-center justify-center"
                >
                    <div
                        class="h-20 w-20 bg-slate-50 rounded-full flex items-center justify-center mb-4"
                    >
                        <span
                            class="material-icons-outlined text-4xl text-slate-200"
                            >sentiment_neutral</span
                        >
                    </div>
                    <p
                        class="text-slate-400 font-black uppercase text-[10px] tracking-widest text-center px-4"
                    >
                        {{ t("feedbacks_for_guide.no_feedback") }}
                        {{ hallName }}
                    </p>
                </div>
            </div>
        </div>
    </GuideLayout>
</template>

<style scoped>
.material-icons-outlined,
.material-icons {
    display: flex;
    align-items: center;
    justify-content: center;
}
.pop-enter-active,
.pop-leave-active {
    transition: all 0.3s ease;
}
.pop-enter-from,
.pop-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.9);
}
.leading-relaxed {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
