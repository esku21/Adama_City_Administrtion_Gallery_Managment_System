<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";

const page = usePage();

/**
 * Fallback to empty strings if user data isn't loaded
 * Maps common database fields (name, firstName, etc.)
 */
const user = computed(() => page.props.auth?.user || {});
</script>

<template>
    <Head title="My Profile" />

    <VisitorLayout>
        <template #header>Registration Details</template>

        <div class="max-w-4xl mx-auto pb-20 animate-in">
            <div
                class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <p class="text-slate-500 text-sm font-medium">
                    This is your verified visitor profile. Information is
                    currently read-only.
                </p>
                <span
                    class="inline-flex items-center px-4 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-[10px] font-black uppercase tracking-widest text-slate-500"
                >
                    <span class="mr-2 text-indigo-500">●</span> System Locked
                </span>
            </div>

            <div
                class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden"
            >
                <div class="p-8 md:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-3"
                            >
                                First Name
                            </label>
                            <div
                                class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700"
                            >
                                {{ user.firstName || user.name || "—" }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-3"
                            >
                                Last Name
                            </label>
                            <div
                                class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700"
                            >
                                {{ user.lastName || "—" }}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label
                                class="block text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-3"
                            >
                                Registered Email
                            </label>
                            <div
                                class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700"
                            >
                                {{ user.email || "—" }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-3"
                            >
                                Phone Number
                            </label>
                            <div
                                class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700"
                            >
                                {{ user.phone_no || user.phone || "—" }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-3"
                            >
                                Visitor Category
                            </label>
                            <div
                                class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-5 font-bold text-slate-700"
                            >
                                {{ user.visitorType || "Local (Ethiopian)" }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-12 p-8 bg-indigo-50/50 rounded-[2rem] border border-indigo-100/50 flex flex-col sm:flex-row items-center sm:items-start gap-6"
                    >
                        <div
                            class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm border border-indigo-100"
                        >
                            🛡️
                        </div>
                        <div class="text-center sm:text-left">
                            <h4
                                class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-2"
                            >
                                Data Protection Notice
                            </h4>
                            <p
                                class="text-[13px] text-indigo-800 leading-relaxed"
                            >
                                To ensure security and verification, profile
                                updates require manual validation. Please visit
                                the
                                <strong>Adama City Administration</strong>
                                office with a valid government ID to change your
                                registered credentials.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </VisitorLayout>
</template>

<style scoped>
.animate-in {
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
