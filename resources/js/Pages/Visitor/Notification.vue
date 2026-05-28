<script setup>
import { ref, computed } from "vue";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import {
    BellOff,
    CheckCheck,
    Clock,
    Calendar,
    ArrowRight,
    Trash2,
    RefreshCw,
    AlertTriangle,
    Filter,
    Globe,
} from "lucide-vue-next";

const props = defineProps({
    announcements: {
        type: Array,
        default: () => [],
    },
});

// Extract tools from useI18n to dynamically switch active language contexts
const { t, locale } = useI18n();

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("locale", lang);
};

// -------------------- REACTIVE COUNTER --------------------
const notificationsCount = computed(() => {
    return props.announcements.filter((n) => !n.is_read).length;
});

// -------------------- ACTIONS --------------------
const markAllRead = () => {
    router.post(
        route("visitor.notifications.markAllRead"),
        {},
        { preserveScroll: true },
    );
};

const deleteNotification = (id) => {
    if (!confirm(t("visitor_notification.confirm_delete"))) return;
    router.delete(route("visitor.notifications.destroy", id), {
        preserveScroll: true,
    });
};

const clearAllNotifications = () => {
    if (!confirm(t("visitor_notification.confirm_clear"))) return;
    router.delete(route("visitor.notifications.destroyAll"), {
        preserveScroll: true,
    });
};

// -------------------- FILTER & SORT --------------------
const filterType = ref("all");

const prioritizedNotifications = computed(() => {
    return [...props.announcements]
        .map((item) => {
            let priority = 0;
            if (item.type === "danger" || item.type === "warning")
                priority += 3;
            if (!item.is_read) priority += 2;
            if (item.reschedule_date) priority += 1;
            return { ...item, priority };
        })
        .sort((a, b) => b.priority - a.priority);
});

const filteredNotifications = computed(() => {
    if (filterType.value === "unread") {
        return prioritizedNotifications.value.filter((n) => !n.is_read);
    }
    if (filterType.value === "important") {
        return prioritizedNotifications.value.filter((n) => n.priority >= 3);
    }
    return prioritizedNotifications.value;
});

const formatDate = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>

<template>
    <Head :title="$t('visitor_notification.nav_title')" />

    <VisitorLayout>
        <template #header>
            {{ $t("visitor_notification.nav_title") }}
        </template>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <transition name="fade">
                <div v-if="notificationsCount > 0" class="mb-8">
                    <div
                        class="bg-rose-50 border-2 border-rose-100 rounded-[2rem] p-6 flex gap-5 items-start shadow-sm"
                    >
                        <div
                            class="bg-rose-500 p-3 rounded-2xl text-white shadow-lg animate-pulse shrink-0"
                        >
                            <AlertTriangle :size="24" />
                        </div>
                        <div class="flex-1">
                            <h3
                                class="font-black text-rose-950 uppercase tracking-wider text-xs"
                            >
                                {{ $t("visitor_notification.emergency_title") }}
                            </h3>
                            <p
                                class="text-rose-800 text-base font-semibold mt-1"
                            >
                                {{
                                    $t("visitor_notification.emergency_desc", {
                                        count: notificationsCount,
                                    })
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </transition>

            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8"
            >
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div>
                        <h1
                            class="text-2xl font-black text-slate-800 tracking-tight"
                        >
                            {{ $t("visitor_notification.title") }}
                        </h1>
                        <p class="text-sm text-slate-500 font-medium">
                            {{ $t("visitor_notification.subtitle") }}
                        </p>
                    </div>

                    <div
                        class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-start md:self-center"
                    >
                        <div class="p-1.5 text-slate-400">
                            <Globe class="w-4 h-4" />
                        </div>
                        <button
                            @click="changeLanguage('or')"
                            :class="[
                                locale === 'or'
                                    ? 'bg-white text-indigo-600 shadow-sm font-bold'
                                    : 'text-slate-600 hover:text-slate-900',
                            ]"
                            class="px-2.5 py-1 text-xs rounded-lg transition-all"
                        >
                            OR
                        </button>
                        <button
                            @click="changeLanguage('am')"
                            :class="[
                                locale === 'am'
                                    ? 'bg-white text-indigo-600 shadow-sm font-bold'
                                    : 'text-slate-600 hover:text-slate-900',
                            ]"
                            class="px-2.5 py-1 text-xs rounded-lg transition-all"
                        >
                            AM
                        </button>
                        <button
                            @click="changeLanguage('en')"
                            :class="[
                                locale === 'en'
                                    ? 'bg-white text-indigo-600 shadow-sm font-bold'
                                    : 'text-slate-600 hover:text-slate-900',
                            ]"
                            class="px-2.5 py-1 text-xs rounded-lg transition-all"
                        >
                            EN
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        @click="clearAllNotifications"
                        class="px-4 py-2 bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-sm"
                    >
                        <RefreshCw class="w-4 h-4" />
                        {{ $t("visitor_notification.clear_all") }}
                    </button>

                    <button
                        @click="markAllRead"
                        class="px-5 py-2 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-lg"
                    >
                        <CheckCheck class="w-4 h-4" />
                        {{ $t("visitor_notification.mark_read") }}
                    </button>
                </div>
            </div>

            <div class="flex gap-2 mb-8 flex-wrap">
                <button
                    @click="filterType = 'all'"
                    :class="
                        filterType === 'all'
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'bg-white text-slate-600 border border-slate-100'
                    "
                    class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all"
                >
                    {{ $t("visitor_notification.filter_all") }}
                </button>
                <button
                    @click="filterType = 'unread'"
                    :class="
                        filterType === 'unread'
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'bg-white text-slate-600 border border-slate-100'
                    "
                    class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all"
                >
                    {{ $t("visitor_notification.filter_unread") }}
                </button>
                <button
                    @click="filterType = 'important'"
                    :class="
                        filterType === 'important'
                            ? 'bg-rose-600 text-white shadow-md'
                            : 'bg-white text-slate-600 border border-slate-100'
                    "
                    class="px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all"
                >
                    <Filter class="w-4 h-4" />
                    {{ $t("visitor_notification.filter_important") }}
                </button>
            </div>

            <div
                v-if="filteredNotifications.length === 0"
                class="bg-white border-2 border-dashed border-slate-100 rounded-[2.5rem] py-20 text-center"
            >
                <div
                    class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                    <BellOff class="w-8 h-8 text-slate-300" />
                </div>
                <h3 class="text-lg font-bold text-slate-800">
                    {{ $t("visitor_notification.empty_title") }}
                </h3>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $t("visitor_notification.empty_desc") }}
                </p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="item in filteredNotifications"
                    :key="item.id"
                    :class="[
                        !item.is_read
                            ? 'bg-white ring-2 ring-indigo-50 border-indigo-100'
                            : 'bg-white border-slate-100 opacity-80',
                    ]"
                    class="border rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all relative group"
                >
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                :class="[
                                    item.priority >= 3
                                        ? 'bg-rose-100 text-rose-600'
                                        : 'bg-indigo-100 text-indigo-600',
                                ]"
                                class="p-2 rounded-xl"
                            >
                                <AlertTriangle
                                    v-if="item.priority >= 3"
                                    class="w-5 h-5"
                                />
                                <Clock v-else class="w-5 h-5" />
                            </div>

                            <div>
                                <h3
                                    class="text-base font-black text-slate-800 flex items-center gap-2"
                                >
                                    {{ item.title }}
                                    <span
                                        v-if="!item.is_read"
                                        class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"
                                    ></span>
                                </h3>
                                <span
                                    class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
                                >
                                    {{ item.created_at }}
                                </span>
                            </div>
                        </div>

                        <button
                            @click="deleteNotification(item.id)"
                            class="p-2 hover:bg-rose-50 rounded-xl opacity-0 group-hover:opacity-100 transition-all text-slate-400 hover:text-rose-600"
                        >
                            <Trash2 class="w-5 h-5" />
                        </button>
                    </div>

                    <p
                        class="text-sm text-slate-600 mb-4 leading-relaxed font-medium"
                    >
                        {{ item.content }}
                    </p>

                    <div
                        v-if="item.reschedule_date"
                        class="mt-4 p-5 bg-indigo-600 rounded-2xl flex justify-between items-center flex-wrap gap-4 shadow-lg shadow-indigo-200"
                    >
                        <div class="text-white">
                            <p
                                class="text-[10px] font-black uppercase tracking-widest opacity-80"
                            >
                                {{ $t("visitor_notification.suggested_slot") }}
                            </p>
                            <p class="text-sm font-bold mt-0.5">
                                {{ formatDate(item.reschedule_date) }}
                            </p>
                        </div>
                        <Link
                            :href="
                                route('visitor.booking.create', {
                                    date: item.reschedule_date,
                                })
                            "
                            class="px-5 py-2.5 bg-white text-indigo-600 text-xs font-black rounded-xl flex items-center gap-2 transition hover:bg-slate-50"
                        >
                            {{ $t("visitor_notification.accept_rebook") }}
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.5s ease,
        transform 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
