<script setup>
import { computed, watch } from "vue";
import VisitorLayout from "@/Layouts/VisitorLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import {
    CheckCircleIcon,
    CalendarDaysIcon,
    ClockIcon,
    BuildingOffice2Icon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    hasSubmittedFeedback: Boolean,
    stats: Object,
    bookings: Array,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            Swal.fire({
                title: "Success!",
                text: flash.success,
                icon: "success",
                confirmButtonColor: "#4f46e5",
            });
        }
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <Head title="Visitor Dashboard" />

    <VisitorLayout>
        <template #header>System Overview</template>

        <div class="mb-6">
            <p class="text-sm text-slate-500 font-medium">
                Authorized Access:
                <span class="text-indigo-600 font-bold"
                    >{{ user?.firstName }} {{ user?.lastName }}</span
                >
            </p>
        </div>

        <div
            v-if="hasSubmittedFeedback"
            class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center"
        >
            <div
                class="bg-emerald-500 p-2 rounded-xl mr-4 text-white shadow-lg"
            >
                <CheckCircleIcon class="w-5 h-5" />
            </div>
            <div>
                <p class="text-emerald-800 font-bold text-sm">
                    Feedback Acknowledged
                </p>
                <p class="text-emerald-600/80 text-xs font-medium">
                    Your visit report has been processed.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div
                v-for="(val, label, index) in {
                    'Pending Auth': stats.pendingVisits,
                    'Logs Completed': stats.completedVisits,
                    'Total Requests': stats.totalBookings,
                }"
                :key="index"
                class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm transition-all hover:shadow-md group"
            >
                <p
                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 group-hover:text-indigo-500 transition-colors"
                >
                    {{ label }}
                </p>
                <p class="text-5xl font-black text-slate-900 tabular-nums">
                    {{ val }}
                </p>
                <div
                    class="mt-4 w-12 h-1.5 rounded-full"
                    :class="
                        index === 0
                            ? 'bg-amber-400'
                            : index === 1
                              ? 'bg-emerald-500'
                              : 'bg-indigo-600'
                    "
                ></div>
            </div>
        </div>

        <div
            class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm"
        >
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                <h3
                    class="font-black text-slate-800 uppercase text-[12px] tracking-[0.15em]"
                >
                    Access Logs
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="bg-slate-50/30 text-slate-400 text-[10px] uppercase font-bold tracking-widest"
                    >
                        <tr>
                            <th class="px-8 py-4">Security Slot</th>
                            <th class="px-8 py-4">Location</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Verification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="booking in bookings"
                            :key="booking.id"
                            class="text-sm hover:bg-slate-50/80 transition-all"
                        >
                            <td class="px-8 py-6">
                                <div
                                    class="font-bold text-slate-700 flex items-center gap-2"
                                >
                                    <CalendarDaysIcon
                                        class="w-4 h-4 text-slate-400"
                                    />
                                    {{ booking.booking_date }}
                                </div>
                                <div
                                    class="text-[10px] text-indigo-500 font-bold uppercase mt-1.5 flex items-center gap-1"
                                >
                                    <ClockIcon class="w-3 h-3" /> Slot:
                                    {{ booking.slot_id }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-slate-500 font-medium">
                                <div class="flex items-center gap-2">
                                    <BuildingOffice2Icon
                                        class="w-4 h-4 text-slate-300"
                                    />
                                    {{ booking.hall_names }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span
                                    :class="{
                                        'bg-amber-50 text-amber-600 border-amber-200/50':
                                            booking.status.toLowerCase() ===
                                            'pending',
                                        'bg-emerald-50 text-emerald-600 border-emerald-200/50':
                                            ['approved', 'completed'].includes(
                                                booking.status.toLowerCase(),
                                            ),
                                        'bg-rose-50 text-rose-600 border-rose-200/50':
                                            ['cancelled', 'rejected'].includes(
                                                booking.status.toLowerCase(),
                                            ),
                                    }"
                                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase border"
                                >
                                    {{ booking.status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a
                                    v-if="
                                        ['approved', 'completed'].includes(
                                            booking.status.toLowerCase(),
                                        )
                                    "
                                    :href="
                                        route(
                                            'visitor.booking.download',
                                            booking.id,
                                        )
                                    "
                                    class="text-indigo-600 font-black hover:text-indigo-800 text-[11px] uppercase transition-colors"
                                >
                                    Get Ticket
                                </a>
                                <span
                                    v-else
                                    class="text-slate-300 italic text-[11px]"
                                    >Verifying...</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </VisitorLayout>
</template>
