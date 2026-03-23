<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    notifications: { type: Array, default: () => [] },
});

// Mark all notifications as read
const markAllRead = () => {
    router.post(
        route("visitor.notifications.readAll"),
        {},
        {
            preserveScroll: true,
        },
    );
};

// Mark a single notification as read
const markAsRead = (id) => {
    router.post(
        route("visitor.notifications.read", id),
        {},
        {
            preserveScroll: true,
        },
    );
};

// Helper to format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head title="My Notifications" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notification Center
                </h2>
                <button
                    v-if="notifications.some((n) => !n.read_at)"
                    @click="markAllRead"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold text-xs uppercase hover:bg-blue-700 transition"
                >
                    Mark All as Read
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100"
                >
                    <div
                        v-if="notifications.length === 0"
                        class="p-20 text-center"
                    >
                        <div
                            class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4"
                        >
                            <span class="text-gray-300 text-2xl">✓</span>
                        </div>
                        <p class="text-gray-500 font-medium">
                            All caught up! No new updates.
                        </p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="item in notifications"
                            :key="item.id"
                            class="p-6 flex items-start gap-4 hover:bg-gray-50 transition-colors group"
                            :class="{ 'bg-blue-50/30': !item.read_at }"
                        >
                            <div class="mt-2">
                                <div
                                    class="w-2.5 h-2.5 rounded-full"
                                    :class="
                                        item.read_at
                                            ? 'bg-gray-300'
                                            : 'bg-blue-600 animate-pulse'
                                    "
                                ></div>
                            </div>

                            <div class="flex-1">
                                <p
                                    class="text-gray-900 font-semibold text-base mb-1"
                                >
                                    {{
                                        item.data.message ||
                                        "New update regarding your visit"
                                    }}
                                </p>
                                <p
                                    class="text-gray-500 text-xs uppercase tracking-wider font-medium"
                                >
                                    {{ formatDate(item.created_at) }}
                                </p>
                            </div>

                            <button
                                v-if="!item.read_at"
                                @click="markAsRead(item.id)"
                                class="opacity-0 group-hover:opacity-100 transition-opacity px-3 py-1.5 bg-gray-100 text-gray-600 rounded-md text-xs font-bold uppercase hover:bg-gray-200"
                            >
                                Mark Read
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p
                        class="text-xs font-bold text-gray-400 uppercase tracking-widest"
                    >
                        Adama City Administration Gallery Management System
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Optional: specific transitions or fonts */
</style>
