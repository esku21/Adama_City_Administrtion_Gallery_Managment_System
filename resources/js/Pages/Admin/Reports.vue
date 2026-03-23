<script setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { Bar, Line, Pie, Doughnut } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
} from "chart.js";

// Register ChartJS components
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
);

const props = defineProps({
    reportData: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    summary: {
        type: Object,
        default: () => ({
            total_count: 0,
            total_visitors: 0,
            positive_feed: 0,
            negative_feed: 0,
        }),
    },
});

// --- REPORT FILTER FORM ---
const form = useForm({
    report_type: props.filters.report_type || "bookings",
    start_date: props.filters.start_date || "",
    end_date: props.filters.end_date || "",
    group_by: props.filters.group_by || "day",
    chart_style: props.filters.chart_style || "bar",
});

const generateReport = () => {
    form.get(route("admin.reports"), {
        preserveState: true,
        preserveScroll: true,
    });
};

// --- DYNAMIC CHART DATA ---
const dynamicChartData = computed(() => {
    const labels = props.reportData.length
        ? props.reportData.map((d) => d.label)
        : ["No Data"];
    const data = props.reportData.map((d) => d.total);

    // Professional color palette for distribution charts (Pie/Doughnut)
    const colors = [
        "#4f46e5",
        "#10b981",
        "#f59e0b",
        "#ef4444",
        "#8b5cf6",
        "#06b6d4",
    ];

    return {
        labels: labels,
        datasets: [
            {
                label: form.report_type.replace("_", " ").toUpperCase(),
                data: data,
                backgroundColor: ["pie", "doughnut"].includes(form.chart_style)
                    ? colors
                    : "rgba(79, 70, 229, 0.2)",
                borderColor: ["pie", "doughnut"].includes(form.chart_style)
                    ? "#ffffff"
                    : "#4f46e5",
                borderWidth: 2,
                pointBackgroundColor: "#4f46e5",
                pointRadius: 4,
                tension: 0.4,
                fill: true,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: ["pie", "doughnut"].includes(form.chart_style),
            position: "bottom",
            labels: { usePointStyle: true, padding: 20 },
        },
        tooltip: {
            backgroundColor: "#1e293b",
            padding: 12,
            cornerRadius: 8,
        },
    },
    scales: ["pie", "doughnut"].includes(form.chart_style)
        ? {}
        : {
              y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
              x: { grid: { display: false } },
          },
};

const printReport = () => window.print();
</script>

<template>
    <Head title="System Analytics" />

    <AuthenticatedLayout>
        <div class="space-y-6 pb-20 max-w-[1600px] mx-auto">
            <header
                class="flex justify-between items-center bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm"
            >
                <div>
                    <h1
                        class="text-2xl font-black text-slate-900 uppercase tracking-tight"
                    >
                        Report <span class="text-indigo-600">Intelligence</span>
                    </h1>
                    <p
                        class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1"
                    >
                        Official Analytical Documentation
                    </p>
                </div>
                <button
                    @click="printReport"
                    class="no-print flex items-center gap-2 bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>
                    Export to PDF
                </button>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <aside
                    class="lg:col-span-1 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm space-y-6 no-print h-fit"
                >
                    <h3
                        class="text-xs font-black uppercase text-slate-900 tracking-widest border-b border-slate-50 pb-4"
                    >
                        Configuration
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="report-label">Target Data</label>
                            <select
                                v-model="form.report_type"
                                class="report-input"
                            >
                                <option value="bookings">Booking Volume</option>
                                <option value="visitor_analysis">
                                    Visitor Demographics
                                </option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <label class="report-label">Date Range</label>
                            <input
                                type="date"
                                v-model="form.start_date"
                                class="report-input"
                            />
                            <input
                                type="date"
                                v-model="form.end_date"
                                class="report-input"
                            />
                        </div>

                        <div>
                            <label class="report-label">Visual Style</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    v-for="style in [
                                        'bar',
                                        'line',
                                        'pie',
                                        'doughnut',
                                    ]"
                                    :key="style"
                                    @click="form.chart_style = style"
                                    :class="
                                        form.chart_style === style
                                            ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100'
                                            : 'bg-slate-50 text-slate-500'
                                    "
                                    class="py-2 rounded-xl text-[9px] font-black uppercase transition-all"
                                >
                                    {{ style }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="generateReport"
                        :disabled="form.processing"
                        class="w-full relative overflow-hidden bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-700 disabled:opacity-70 transition-all"
                    >
                        <span v-if="!form.processing">Generate Analysis</span>
                        <span
                            v-else
                            class="flex items-center justify-center gap-2"
                        >
                            <svg
                                class="animate-spin h-4 w-4 text-white"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Syncing...
                        </span>
                    </button>
                </aside>

                <main class="lg:col-span-3 space-y-6 relative">
                    <div
                        v-if="form.processing"
                        class="absolute inset-0 z-50 bg-white/80 backdrop-blur-sm rounded-[2rem] flex flex-col items-center justify-center"
                    >
                        <div
                            class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mb-4"
                        ></div>
                        <p
                            class="text-[10px] font-black uppercase tracking-widest text-indigo-600 animate-pulse"
                        >
                            Processing Database...
                        </p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div
                            v-for="(val, label) in {
                                'Total Bookings': summary.total_count,
                                Visitors: summary.total_visitors,
                                Positive: summary.positive_feed,
                                Negative: summary.negative_feed,
                            }"
                            :key="label"
                            class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm"
                        >
                            <p
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                            >
                                {{ label }}
                            </p>
                            <p class="text-2xl font-black text-slate-900 mt-1">
                                {{ val }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm min-h-[550px]"
                    >
                        <div class="flex justify-between items-center mb-10">
                            <h3
                                class="text-xs font-black uppercase text-slate-900 tracking-widest"
                            >
                                Visual Analysis
                            </h3>
                            <span
                                class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest"
                                >Live Feedback</span
                            >
                        </div>

                        <div v-if="reportData.length > 0" class="h-[450px]">
                            <Bar
                                v-if="form.chart_style === 'bar'"
                                :data="dynamicChartData"
                                :options="chartOptions"
                            />
                            <Line
                                v-else-if="form.chart_style === 'line'"
                                :data="dynamicChartData"
                                :options="chartOptions"
                            />
                            <Pie
                                v-else-if="form.chart_style === 'pie'"
                                :data="dynamicChartData"
                                :options="chartOptions"
                            />
                            <Doughnut
                                v-else-if="form.chart_style === 'doughnut'"
                                :data="dynamicChartData"
                                :options="chartOptions"
                            />
                        </div>

                        <div
                            v-else
                            class="h-[400px] flex flex-col items-center justify-center"
                        >
                            <div
                                class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mb-4"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                            </div>
                            <p
                                class="text-sm font-bold text-slate-900 uppercase tracking-tight"
                            >
                                No Data Available
                            </p>
                            <p
                                class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest"
                            >
                                Try adjusting your filters
                            </p>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.report-label {
    @apply text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-widest;
}
.report-input {
    @apply w-full bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 p-4 transition-all mb-2;
}
@media print {
    .no-print {
        display: none !important;
    }
    main {
        width: 100% !important;
        grid-column: span 4;
    }
    .bg-white {
        border: none !important;
        shadow: none !important;
    }
}
</style>
