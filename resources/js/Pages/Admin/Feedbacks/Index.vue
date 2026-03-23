<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    // Important: The controller must pass feedbacks with the 'user' relationship loaded
    feedbacks: Array,
});

// --- SEARCH & FILTER STATE ---
const searchQuery = ref("");
const selectedRating = ref("");

// --- FILTERING LOGIC ---
const filteredFeedbacks = computed(() => {
    return props.feedbacks.filter((fb) => {
        // Safe access to user relationship data
        const userName = fb.user?.name || "Visitor";
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
</script>

<template>
    <Head title="Visitor Feedbacks" />

    <AuthenticatedLayout>
        <div class="p-8 space-y-8 w-full">
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm"
            >
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-slate-900 uppercase"
                    >
                        Visitor <span class="text-indigo-600">Feedbacks</span>
                    </h2>
                    <p
                        class="text-[10px] text-slate-400 uppercase tracking-[0.2em] font-bold mt-1"
                    >
                        Review Management •
                        {{ filteredFeedbacks.length }} Submissions
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex items-center group">
                        <span
                            class="material-icons-outlined absolute left-4 text-slate-400 text-lg group-focus-within:text-indigo-500 transition-colors pointer-events-none"
                            >search</span
                        >
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by name or message..."
                            class="bg-slate-50 border border-slate-200 rounded-2xl py-3 pl-12 pr-4 text-xs w-64 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-bold text-slate-700"
                        />
                    </div>

                    <select
                        v-model="selectedRating"
                        class="bg-slate-50 border border-slate-200 rounded-2xl py-3 px-4 text-xs w-40 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-slate-600 outline-none transition-all cursor-pointer"
                    >
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div
                    v-for="feedback in filteredFeedbacks"
                    :key="feedback.id"
                    class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group relative"
                >
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black text-lg shadow-lg shadow-indigo-100 uppercase"
                            >
                                {{
                                    feedback.user
                                        ? feedback.user.name.charAt(0)
                                        : "V"
                                }}
                            </div>
                            <div>
                                <h4
                                    class="font-black text-slate-900 text-sm uppercase tracking-tight"
                                >
                                    {{
                                        feedback.user
                                            ? feedback.user.name
                                            : "Unknown User"
                                    }}
                                </h4>
                                <p
                                    class="text-[10px] text-indigo-500 font-bold tracking-widest"
                                >
                                    {{
                                        feedback.user
                                            ? feedback.user.email
                                            : "No email"
                                    }}
                                </p>
                            </div>
                        </div>

                        <Link
                            :href="route('admin.feedbacks.delete', feedback.id)"
                            method="delete"
                            as="button"
                            onBefore="return confirm('Are you sure you want to delete this feedback?')"
                            class="h-10 w-10 flex items-center justify-center rounded-2xl text-rose-500 hover:bg-rose-50 transition-colors border border-transparent hover:border-rose-100"
                        >
                            <span class="material-icons-outlined text-xl"
                                >delete_sweep</span
                            >
                        </Link>
                    </div>

                    <div
                        class="bg-slate-50 p-5 rounded-3xl border border-slate-100 relative"
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
                        class="mt-6 pt-5 border-t border-slate-50 flex items-center justify-between"
                    >
                        <div class="flex flex-col">
                            <span
                                class="text-[9px] font-black text-slate-300 uppercase tracking-widest"
                                >Submitted</span
                            >
                            <span class="text-[10px] font-bold text-slate-500">
                                {{
                                    feedback.created_at
                                        ? new Date(
                                              feedback.created_at,
                                          ).toLocaleDateString()
                                        : "N/A"
                                }}
                            </span>
                        </div>

                        <div
                            class="flex gap-0.5 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-100"
                        >
                            <span
                                v-for="i in 5"
                                :key="i"
                                class="material-icons text-[14px]"
                                :class="
                                    i <= feedback.rating
                                        ? 'text-amber-400'
                                        : 'text-slate-300'
                                "
                            >
                                star
                            </span>
                        </div>
                    </div>

                    <div class="absolute top-0 right-12 mt-2">
                        <span
                            class="text-[8px] bg-slate-800 text-white px-2 py-0.5 rounded-full uppercase font-bold tracking-widest"
                        >
                            {{ feedback.type }}
                        </span>
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
                            >maps_ugc</span
                        >
                    </div>
                    <p
                        class="text-slate-400 font-black uppercase text-[10px] tracking-widest"
                    >
                        No feedbacks found
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.material-icons-outlined,
.material-icons {
    display: flex;
    align-items: center;
    justify-content: center;
}

.leading-relaxed {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
