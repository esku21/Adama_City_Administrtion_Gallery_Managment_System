<script setup>
import GuideLayout from "@/Layouts/GuideLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    guide: Object,
    hall: Object,
});
</script>

<template>
    <Head title="My Profile" />

    <GuideLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-10">
                <h2
                    class="text-3xl font-black text-zinc-900 uppercase tracking-tighter italic"
                >
                    Staff
                    <span
                        class="text-emerald-500 underline decoration-4 underline-offset-8"
                        >Identity</span
                    >
                </h2>
                <p
                    class="text-[10px] text-zinc-400 font-bold uppercase tracking-[0.3em] mt-3"
                >
                    Official Personnel Information • Adama City Hall
                </p>
            </div>

            <div
                class="bg-white rounded-[2.5rem] border border-zinc-100 shadow-sm overflow-hidden"
            >
                <div class="h-32 bg-zinc-900 w-full relative">
                    <div class="absolute -bottom-12 left-10">
                        <div
                            class="w-24 h-24 rounded-3xl bg-emerald-500 border-4 border-white flex items-center justify-center shadow-xl"
                        >
                            <span
                                class="material-icons-outlined text-white text-4xl"
                                >person</span
                            >
                        </div>
                    </div>
                </div>

                <div class="pt-16 pb-10 px-10">
                    <div class="flex justify-between items-start mb-10">
                        <div>
                            <h3
                                class="text-2xl font-black text-zinc-900 uppercase tracking-tight"
                            >
                                {{ guide.name }}
                            </h3>
                            <p
                                class="text-emerald-600 font-bold text-[10px] uppercase tracking-widest mt-1"
                            >
                                Authorized Guide • ID #{{
                                    guide.id.toString().padStart(4, "0")
                                }}
                            </p>
                        </div>
                        <div
                            class="bg-zinc-50 px-6 py-2 rounded-2xl border border-zinc-100 text-center"
                        >
                            <p
                                class="text-[8px] font-black text-zinc-400 uppercase mb-1"
                            >
                                Status
                            </p>
                            <span
                                class="text-[10px] font-black text-emerald-600 uppercase italic"
                                >On Duty</span
                            >
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-zinc-50 pt-10"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400"
                            >
                                <span class="material-icons-outlined"
                                    >alternate_email</span
                                >
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-black text-zinc-400 uppercase tracking-widest"
                                >
                                    Email Address
                                </p>
                                <p class="font-bold text-zinc-800 text-sm mt-1">
                                    {{ guide.email }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400"
                            >
                                <span class="material-icons-outlined"
                                    >phone_iphone</span
                                >
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-black text-zinc-400 uppercase tracking-widest"
                                >
                                    Phone Number
                                </p>
                                <p class="font-bold text-zinc-800 text-sm mt-1">
                                    {{ guide.phone }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"
                            >
                                <span class="material-icons-outlined"
                                    >location_city</span
                                >
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-black text-emerald-600 uppercase tracking-widest"
                                >
                                    Assigned Station
                                </p>
                                <p class="font-bold text-zinc-800 text-sm mt-1">
                                    {{ hall?.name || "Unassigned" }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center text-zinc-400"
                            >
                                <span class="material-icons-outlined">wc</span>
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-black text-zinc-400 uppercase tracking-widest"
                                >
                                    Gender
                                </p>
                                <p class="font-bold text-zinc-800 text-sm mt-1">
                                    {{ guide.gender }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuideLayout>
</template>
