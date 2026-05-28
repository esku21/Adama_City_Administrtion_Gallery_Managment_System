<script setup>
import { computed, ref, onMounted, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";

/* =========================
   LOAD CHART JS
========================= */
const loadChartJS = () => {
    return new Promise((resolve) => {
        if (window.Chart) {
            return resolve(window.Chart);
        }

        const script = document.createElement("script");

        script.src =
            "https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js";

        script.onload = () => {
            resolve(window.Chart);
        };

        document.head.appendChild(script);
    });
};

/* =========================
   PROPS
========================= */
const props = defineProps({
    reportData: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    summary: {
        type: Object,
        default: () => ({}),
    },
});

/* =========================
   FORM STATE
========================= */
const form = useForm({
    status_filter: "",
    chart_style: "",
});

/* =========================
   UI STATE
========================= */
const selectedInterval = ref("");
const selectedChartStyle = ref("");

/* =========================
   VALIDATION ERRORS
========================= */
const intervalError = ref("");
const chartStyleError = ref("");

/* =========================
   LOGIC:
   EXCLUDE ADMIN USER
========================= */
const filteredUserCount = computed(() => {
    return Math.max(0, (props.summary.totalRegisteredUsers || 0) - 1);
});

/* =========================
   COMPUTED DATA
========================= */
const hasData = computed(() => {
    return props.reportData?.length > 0;
});

const bookingsTotal = computed(() => {
    return (
        props.reportData.find((d) => d.label === "Total Bookings")?.bookings ||
        0
    );
});

const visitorsTotal = computed(() => {
    return (
        props.reportData.find((d) => d.label === "Total Visitors")?.visitors ||
        0
    );
});

const chartLabels = computed(() => {
    if (!hasData.value) {
        return ["No Data"];
    }

    if (selectedChartStyle.value === "pie") {
        return ["Total Bookings", "Registered Users", "Total Visitors"];
    }

    return ["Analytics"];
});

const chartDatasets = computed(() => {
    if (!hasData.value) {
        return [
            {
                label: "Analytics Overview",

                data: [0, 0, 0],

                backgroundColor: [
                    "rgba(99, 102, 241, 0.8)",
                    "rgba(34, 197, 94, 0.8)",
                    "rgba(239, 68, 68, 0.8)",
                ],

                borderColor: ["#6366f1", "#22c55e", "#ef4444"],

                borderWidth: 2,
            },
        ];
    }

    const chartType = selectedChartStyle.value || "bar";

    if (chartType === "pie") {
        return [
            {
                label: "Analytics Overview",

                data: [
                    bookingsTotal.value,
                    filteredUserCount.value,
                    visitorsTotal.value,
                ],

                backgroundColor: [
                    "rgba(99, 102, 241, 0.8)",
                    "rgba(34, 197, 94, 0.8)",
                    "rgba(239, 68, 68, 0.8)",
                ],

                borderColor: ["#6366f1", "#22c55e", "#ef4444"],

                borderWidth: 2,
            },
        ];
    }

    const isLine = chartType === "line";

    return [
        {
            label: "Total Bookings",

            data: [bookingsTotal.value],

            backgroundColor: isLine
                ? "rgba(99, 102, 241, 0.2)"
                : "rgba(99, 102, 241, 0.8)",

            borderColor: "#6366f1",

            borderWidth: 2,

            tension: 0.4,

            fill: isLine,
        },

        {
            label: "Registered Users",

            data: [filteredUserCount.value],

            backgroundColor: isLine
                ? "rgba(34, 197, 94, 0.2)"
                : "rgba(34, 197, 94, 0.8)",

            borderColor: "#22c55e",

            borderWidth: 2,

            tension: 0.4,

            fill: isLine,
        },

        {
            label: "Total Visitors",

            data: [visitorsTotal.value],

            backgroundColor: isLine
                ? "rgba(239, 68, 68, 0.2)"
                : "rgba(239, 68, 68, 0.8)",

            borderColor: "#ef4444",

            borderWidth: 2,

            tension: 0.4,

            fill: isLine,
        },
    ];
});

/* =========================
   CHART OPTIONS
========================= */
const chartOptions = computed(() => ({
    responsive: true,

    maintainAspectRatio: false,

    plugins: {
        legend: {
            position: "bottom",

            labels: {
                font: {
                    weight: "bold",
                },

                padding: 20,
            },
        },

        tooltip: {
            callbacks: {
                label: function (context) {
                    if (selectedChartStyle.value === "pie") {
                        const labels = [
                            "Total Bookings",
                            "Registered Users",
                            "Total Visitors",
                        ];

                        return `${labels[context.dataIndex]}: ${context.parsed}`;
                    }

                    return `${context.dataset.label}: ${
                        context.parsed.y ?? context.parsed
                    }`;
                },
            },
        },
    },

    scales:
        selectedChartStyle.value !== "pie"
            ? {
                  y: {
                      beginAtZero: true,

                      ticks: {
                          precision: 0,
                      },
                  },
              }
            : {},
}));

/* =========================
   GENERATE REPORT
========================= */
const generateReport = async () => {
    if (form.processing) return;

    intervalError.value = "";
    chartStyleError.value = "";

    let hasError = false;

    /* REQUIRED VALIDATION */
    if (!selectedInterval.value) {
        intervalError.value = "Time interval is required";

        hasError = true;
    }

    if (!selectedChartStyle.value) {
        chartStyleError.value = "Chart style is required";

        hasError = true;
    }

    if (hasError) return;

    form.status_filter = selectedInterval.value;

    form.chart_style = selectedChartStyle.value;

    form.get(route("reports"), {
        preserveState: true,

        preserveScroll: true,

        onSuccess: async () => {
            await updateChart();
        },
    });
};

/* =========================
   CHART INSTANCE const chartLabels = computed(() => {
    return props.reportData.map(d => d.label);
});
========================= */
const chartRef = ref(null);

let chartInstance = null;

/* =========================
   UPDATE CHART
========================= */
const updateChart = async () => {
    if (!chartRef.value) return;

    const Chart = await loadChartJS();

    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }

    const ctx = chartRef.value.getContext("2d");

    chartInstance = new Chart(ctx, {
        type: selectedChartStyle.value || "bar",

        data: {
            labels: chartLabels.value,

            datasets: chartDatasets.value,
        },

        options: chartOptions.value,
    });
};

/* =========================
   WATCHERS
========================= */
watch(
    () => props.reportData,
    async () => {
        await updateChart();
    },

    {
        deep: true,
        immediate: true,
    },
);

watch(
    () => selectedChartStyle.value,
    async () => {
        if (selectedChartStyle.value) {
            chartStyleError.value = "";
        }

        await updateChart();
    },
);

watch(
    () => selectedInterval.value,
    () => {
        if (selectedInterval.value) {
            intervalError.value = "";
        }
    },
);

/* =========================
   MOUNTED
========================= */
onMounted(async () => {
    await updateChart();
});

/* =========================
   PRINT
========================= */
const handlePrint = () => {
    window.print();
};

/* =========================
   EXPORT PDF
========================= */
const generateBrowserPDF = () => {
    const printWindow = window.open("", "_blank");

    let chartImage = "";

    if (chartInstance) {
        chartImage = chartInstance.toBase64Image();
    }

    const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Analytics Report</title>

            <style>
                body{
                    font-family:Arial,sans-serif;
                    margin:20px;
                }

                h1{
                    text-align:center;
                }

                .summary-grid{
                    display:grid;
                    grid-template-columns:repeat(5,1fr);
                    gap:15px;
                    margin-top:20px;
                }

                .card{
                    border:1px solid #ddd;
                    padding:15px;
                    border-radius:10px;
                    text-align:center;
                }

                table{
                    width:100%;
                    border-collapse:collapse;
                    margin-top:30px;
                }

                table th,
                table td{
                    border:1px solid #ddd;
                    padding:10px;
                }

                table th{
                    background:#f5f5f5;
                }

                .chart{
                    margin-top:30px;
                    text-align:center;
                }
            </style>
        </head>

        <body>

            <h1>Analytics Report</h1>

            <p style="text-align:center">
                Generated:
                ${new Date().toLocaleString()}
            </p>

            <div class="summary-grid">

                <div class="card">
                    <h3>Registered Users</h3>
                    <p>${filteredUserCount.value}</p>
                </div>

                <div class="card">
                    <h3>Total Bookings</h3>
                    <p>${bookingsTotal.value}</p>
                </div>

                <div class="card">
                    <h3>Total Visitors</h3>
                    <p>${visitorsTotal.value}</p>
                </div>

                <div class="card">
                    <h3>Satisfied</h3>
                    <p>${props.summary.totalSatisfied || 0}</p>
                </div>

                <div class="card">
                    <h3>Unsatisfied</h3>
                    <p>${props.summary.totalUnsatisfied || 0}</p>
                </div>

            </div>

            ${
                chartImage
                    ? `
                <div class="chart">
                    <img
                        src="${chartImage}"
                        style="max-width:100%;height:auto;"
                    />
                </div>
            `
                    : ""
            }

        </body>
        </html>
    `;

    printWindow.document.write(html);

    printWindow.document.close();

    printWindow.onload = () => {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
};
</script>

<template>
    <Head title="Admin Reports" />

    <div class="p-6 lg:p-10 space-y-8 max-w-[1600px]">
        <!-- HEADER -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 print:hidden"
        >
            <div>
                <h1
                    class="text-4xl font-black uppercase tracking-tight text-slate-900"
                >
                    Analytics Reports
                </h1>

                <p
                    class="text-sm text-slate-500 font-bold uppercase tracking-widest mt-1"
                >
                    System Performance & Feedback Insights
                </p>
            </div>

            <div class="flex gap-3">
                <button
                    @click="generateBrowserPDF"
                    class="px-6 py-3 bg-slate-900 text-white rounded-xl font-black uppercase hover:bg-indigo-700 transition-all"
                >
                    Export PDF
                </button>

                <button
                    @click="handlePrint"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black uppercase hover:bg-indigo-700 transition-all"
                >
                    Print
                </button>
            </div>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- USERS -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg border-l-8 border-violet-600"
            >
                <p class="text-sm font-black text-slate-600 uppercase mb-2">
                    Registered Users
                </p>

                <h2 class="text-4xl font-black text-slate-900">
                    {{ filteredUserCount.toLocaleString() }}
                    <span class="text-violet-500">+</span>
                </h2>
            </div>

            <!-- BOOKINGS -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg border-l-8 border-indigo-600"
            >
                <p class="text-sm font-black text-slate-600 uppercase mb-2">
                    Total Bookings
                </p>

                <h2 class="text-4xl font-black text-slate-900">
                    {{ summary.totalBookings?.toLocaleString() }}
                    <span class="text-indigo-500">+</span>
                </h2>
            </div>

            <!-- VISITORS -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg border-l-8 border-blue-600"
            >
                <p class="text-sm font-black text-slate-600 uppercase mb-2">
                    Total Visitors
                </p>

                <h2 class="text-4xl font-black text-slate-900">
                    {{ summary.totalVisitors?.toLocaleString() }}
                    <span class="text-blue-500">+</span>
                </h2>
            </div>

            <!-- SATISFIED -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg border-l-8 border-emerald-600"
            >
                <p class="text-sm font-black text-slate-600 uppercase mb-2">
                    Satisfied
                </p>

                <h2 class="text-4xl font-black text-emerald-700">
                    {{ summary.totalSatisfied?.toLocaleString() }}
                    <span class="text-emerald-500">+</span>
                </h2>
            </div>

            <!-- UNSATISFIED -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg border-l-8 border-rose-600"
            >
                <p class="text-sm font-black text-slate-600 uppercase mb-2">
                    Unsatisfied
                </p>

                <h2 class="text-4xl font-black text-rose-700">
                    {{ summary.totalUnsatisfied?.toLocaleString() }}
                    <span class="text-rose-500">+</span>
                </h2>
            </div>
        </div>

        <!-- FILTERS + CHART -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- FILTER -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md"
                >
                    <h3
                        class="text-base font-black uppercase mb-5 border-b pb-3"
                    >
                        Filter Configuration
                    </h3>

                    <div class="space-y-5">
                        <!-- INTERVAL -->
                        <div>
                            <label
                                class="text-xs font-black uppercase text-slate-500 mb-2 block"
                            >
                                Time Interval
                            </label>

                            <select
                                v-model="selectedInterval"
                                class="w-full p-4 bg-slate-50 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"
                            >
                                <option value="" disabled>
                                    Select Time Interval
                                </option>

                                <option value="weekly">Weekly</option>

                                <option value="monthly">Monthly</option>

                                <option value="yearly">Yearly</option>
                            </select>

                            <!-- ERROR -->
                            <p
                                v-if="intervalError"
                                class="text-red-500 text-sm mt-2 font-semibold"
                            >
                                {{ intervalError }}
                            </p>
                        </div>

                        <!-- CHART -->
                        <div>
                            <label
                                class="text-xs font-black uppercase text-slate-500 mb-2 block"
                            >
                                Chart Style
                            </label>

                            <select
                                v-model="selectedChartStyle"
                                class="w-full p-4 bg-slate-50 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"
                            >
                                <option value="" disabled>
                                    Select Chart Style
                                </option>

                                <option value="bar">Bar Chart</option>

                                <option value="line">Line Chart</option>

                                <option value="pie">Pie Chart</option>
                            </select>

                            <!-- ERROR -->
                            <p
                                v-if="chartStyleError"
                                class="text-red-500 text-sm mt-2 font-semibold"
                            >
                                {{ chartStyleError }}
                            </p>
                        </div>

                        <!-- BUTTON -->
                        <button
                            @click="generateReport"
                            :disabled="form.processing"
                            class="w-full py-4 bg-indigo-600 text-white rounded-xl font-black uppercase hover:bg-indigo-700 transition-all"
                        >
                            {{
                                form.processing
                                    ? "Updating..."
                                    : "Apply Filters"
                            }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- CHART -->
            <div
                class="lg:col-span-3 bg-white p-8 rounded-2xl border border-slate-200 shadow-md"
            >
                <h3 class="text-xl font-black uppercase text-slate-800 mb-6">
                    Data Visualization
                </h3>

                <div class="w-full h-[450px]">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>
