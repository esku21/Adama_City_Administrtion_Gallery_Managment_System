<script setup>
import { computed, ref, onMounted, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";

/* =========================
   LOAD CHART JS
========================= */
const loadChartJS = () => {
    return new Promise((resolve) => {
        if (window.Chart) return resolve(window.Chart);

        const script = document.createElement("script");
        script.src =
            "https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js";

        script.onload = () => {
            window.Chart.register(...window.Chart.registerables);
            resolve(window.Chart);
        };

        document.head.appendChild(script);
    });
};

/* =========================
   PROPS
========================= */
const props = defineProps({
    reportData: { type: Array, default: () => [] },
    filters: Object,
    summary: { type: Object, default: () => ({}) },
});

/* =========================
   FORM STATE
========================= */
const form = useForm({
    status_filter: "",
    chart_style: "",
});

/* Working UI state */
const selectedInterval = ref("");
const selectedChartStyle = ref("");

/* =========================
   COMPUTED DATA
========================= */
const hasData = computed(() => props.reportData?.length > 0);

const chartLabels = computed(() => (hasData.value ? [""] : ["No Data"]));

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

    const bookingsTotal =
        props.reportData.find((d) => d.label === "Total Bookings")?.bookings ||
        0;
    const usersTotal =
        props.reportData.find((d) => d.label === "Registered Users")?.users ||
        0;
    const visitorsTotal =
        props.reportData.find((d) => d.label === "Total Visitors")?.visitors ||
        0;

    const chartType =
        selectedChartStyle.value && selectedChartStyle.value.trim() !== ""
            ? selectedChartStyle.value
            : "bar";

    if (chartType === "pie") {
        return [
            {
                label: "Analytics Overview",
                data: [bookingsTotal, usersTotal, visitorsTotal],
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
            data: [bookingsTotal],
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
            data: [usersTotal],
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
            data: [visitorsTotal],
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
                font: { weight: "bold" },
                padding: 20,
                generateLabels:
                    selectedChartStyle.value === "pie"
                        ? function (chart) {
                              return [
                                  {
                                      text: "Total Bookings",
                                      fillStyle: "#6366f1",
                                      strokeStyle: "#6366f1",
                                      lineWidth: 2,
                                  },
                                  {
                                      text: "Registered Users",
                                      fillStyle: "#22c55e",
                                      strokeStyle: "#22c55e",
                                      lineWidth: 2,
                                  },
                                  {
                                      text: "Total Visitors",
                                      fillStyle: "#ef4444",
                                      strokeStyle: "#ef4444",
                                      lineWidth: 2,
                                  },
                              ];
                          }
                        : undefined,
            },
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    if (selectedChartStyle.value === "pie") {
                        const datasetLabels = [
                            "Total Bookings",
                            "Registered Users",
                            "Total Visitors",
                        ];
                        const label = datasetLabels[context.dataIndex];
                        const value = context.parsed;
                        return `${label}: ${value}`;
                    }
                    return `${context.dataset.label}: ${context.parsed.y || context.parsed}`;
                },
            },
        },
    },
    scales:
        selectedChartStyle.value !== "pie"
            ? { y: { beginAtZero: true, ticks: { precision: 0 } } }
            : {},
}));

/* =========================
   APPLY FILTER (FIXED CORE LOGIC)
========================= */
const generateReport = () => {
    if (form.processing) return;

    form.processing = true;

    // STRICT DEFAULTS
    const interval =
        selectedInterval.value?.trim() !== ""
            ? selectedInterval.value
            : "weekly";

    const chartStyle =
        selectedChartStyle.value?.trim() !== ""
            ? selectedChartStyle.value
            : "bar";

    form.status_filter = interval;
    form.chart_style = chartStyle;

    form.get(route("admin.reports"), {
        preserveState: true,
        preserveScroll: true,

        onSuccess: async () => {
            selectedInterval.value = interval;
            selectedChartStyle.value = chartStyle;

            await updateChart();
        },

        onFinish: () => {
            form.processing = false;
        },
    });
};

/* =========================
   CHART INSTANCE
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

    const chartType =
        selectedChartStyle.value && selectedChartStyle.value.trim() !== ""
            ? selectedChartStyle.value
            : "bar";

    chartInstance = new Chart(ctx, {
        type: chartType,
        data: {
            labels: chartLabels.value,
            datasets: chartDatasets.value,
        },
        options: chartOptions.value,
    });
};

/* =========================
   WATCHERS (FIXED)
========================= */
watch(
    () => props.reportData,
    async () => {
        if (hasData.value) {
            await updateChart();
        }
    },
    { deep: true, immediate: true },
);

onMounted(async () => {
    if (hasData.value) {
        await updateChart();
    }
});

const fetchNewData = () => {
    // Copy working values to form for submission
    form.status_filter = selectedInterval.value;
    form.chart_style = selectedChartStyle.value;

    form.get(route("admin.reports"), {
        preserveState: true,
        preserveScroll: true,
    });
};

const handlePrint = () => window.print();

const generateBrowserPDF = () => {
    // Create a new window for printing
    const printWindow = window.open("", "_blank");

    // Get the current chart image
    let chartImage = "";
    if (chartInstance) {
        chartImage = chartInstance.toBase64Image();
    }

    // Create the HTML content for PDF
    const pdfContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Analytics Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .summary-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 20px; }
                .summary-card { border: 1px solid #ddd; padding: 15px; text-align: center; }
                .chart-container { text-align: center; margin: 20px 0; }
                .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .data-table th { background-color: #f5f5f5; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Analytics Report</h1>
                <p>Generated on: ${new Date().toLocaleString()} | Time Interval: ${form.status_filter} | Chart Type: ${form.chart_style}</p>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <h3>Registered Users</h3>
                    <div>${props.summary.totalRegisteredUsers?.toLocaleString() || 0}</div>
                </div>
                <div class="summary-card">
                    <h3>Total Bookings</h3>
                    <div>${props.summary.totalBookings?.toLocaleString() || 0}</div>
                </div>
                <div class="summary-card">
                    <h3>Total Visitors</h3>
                    <div>${props.summary.totalVisitors?.toLocaleString() || 0}</div>
                </div>
                <div class="summary-card">
                    <h3>Satisfied</h3>
                    <div>${props.summary.totalSatisfied?.toLocaleString() || 0}</div>
                </div>
                <div class="summary-card">
                    <h3>Unsatisfied</h3>
                    <div>${props.summary.totalUnsatisfied?.toLocaleString() || 0}</div>
                </div>
            </div>

            ${chartImage ? `<div class="chart-container"><img src="${chartImage}" style="max-width: 100%; height: auto;" /></div>` : ""}

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time Period</th>
                        <th>Total Bookings</th>
                        <th>Registered Users</th>
                        <th>Total Visitors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${form.status_filter.charAt(0).toUpperCase() + form.status_filter.slice(1)}</td>
                        <td>${props.reportData.find((d) => d.label === "Total Bookings")?.bookings || 0}</td>
                        <td>${props.reportData.find((d) => d.label === "Registered Users")?.users || 0}</td>
                        <td>${props.reportData.find((d) => d.label === "Total Visitors")?.visitors || 0}</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>
    `;

    printWindow.document.write(pdfContent);
    printWindow.document.close();

    // Wait for content to load, then trigger print
    printWindow.onload = () => {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
};

const handleExport = async () => {
    try {
        console.log("Starting Vue-based PDF export...");

        // Get chart as base64 image
        let chartImage = null;
        if (chartInstance) {
            chartImage = chartInstance.toBase64Image();
            console.log("Chart image captured, length:", chartImage?.length);
        }

        // Prepare data for PDF generation
        const exportData = {
            status_filter: form.status_filter,
            chart_type: form.chart_style,
            chart_image: chartImage,
        };

        console.log("Export data prepared:", {
            status_filter: exportData.status_filter,
            chart_type: exportData.chart_type,
            has_chart_image: !!exportData.chart_image,
        });

        // Send request to backend for report data
        const response = await fetch(route("reports.export.pdf"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "",
            },
            body: JSON.stringify(exportData),
        });

        if (response.ok) {
            const reportData = await response.json();
            console.log("Report data received:", reportData);

            // Generate PDF using Vue component
            generateVuePDF(reportData);
        } else {
            const errorData = await response.json();
            console.error(
                "Failed to generate report data:",
                response.status,
                errorData,
            );
            alert(
                `Failed to generate report: ${errorData.error || response.status}`,
            );
        }
    } catch (error) {
        console.error("Error exporting report:", error);
        alert(`Error generating report: ${error.message}`);
    }
};

const generateVuePDF = (reportData) => {
    // Create a new window for printing
    const printWindow = window.open("", "_blank");

    // Generate HTML content using Vue template logic with Tailwind CSS
    const htmlContent =
        [
            "<!DOCTYPE html>",
            "<html>",
            "<head>",
            "<title>" + reportData.title + "</title>",
            '<script src="https://cdn.tailwindcss.com"><\/script>',
            "<script>",
            "tailwind.config = {",
            "theme: {",
            "extend: {}",
            "}",
            "}",
            "<\/script>",
            "<\/head>",
        ].join("") +
        [
            '<body class="font-sans text-xs m-5 leading-relaxed">',
            "<!-- Header -->",
            '<div class="text-center mb-8 border-b-2 border-gray-900 pb-5">',
            '<h1 class="text-2xl font-bold m-0 text-gray-900">' +
                reportData.title +
                "</h1>",
            '<p class="text-xs text-gray-600 mt-1">Generated on: ' +
                reportData.generated_date +
                " | Time Interval: " +
                reportData.time_interval +
                " | Chart Type: " +
                reportData.chart_type +
                "</p>",
            "</div>",
            "<!-- Summary Section -->",
            '<div class="mb-6">',
            '<div class="text-base font-bold mb-4 text-gray-900 border-b border-gray-300 pb-1">Overall Summary</div>',
            '<div class="grid grid-cols-5 gap-4 mb-5">',
            '<div class="border border-gray-300 p-4 text-center rounded">',
            '<h3 class="text-sm mb-2 text-gray-700">Registered Users</h3>',
            '<div class="text-lg font-bold text-gray-900">' +
                Number(
                    reportData.summary.totalRegisteredUsers,
                ).toLocaleString() +
                "</div>",
            "</div>",
            '<div class="border border-gray-300 p-4 text-center rounded">',
            '<h3 class="text-sm mb-2 text-gray-700">Total Bookings</h3>',
            '<div class="text-lg font-bold text-gray-900">' +
                Number(reportData.summary.totalBookings).toLocaleString() +
                "</div>",
            "</div>",
            '<div class="border border-gray-300 p-4 text-center rounded">',
            '<h3 class="text-sm mb-2 text-gray-700">Total Visitors</h3>',
            '<div class="text-lg font-bold text-gray-900">' +
                Number(reportData.summary.totalVisitors).toLocaleString() +
                "</div>",
            "</div>",
            '<div class="border border-gray-300 p-4 text-center rounded">',
            '<h3 class="text-sm mb-2 text-gray-700">Satisfied</h3>',
            '<div class="text-lg font-bold text-gray-900">' +
                Number(reportData.summary.totalSatisfied).toLocaleString() +
                "</div>",
            "</div>",
            '<div class="border border-gray-300 p-4 text-center rounded">',
            '<h3 class="text-sm mb-2 text-gray-700">Unsatisfied</h3>',
            '<div class="text-lg font-bold text-gray-900">' +
                Number(reportData.summary.totalUnsatisfied).toLocaleString() +
                "</div>",
            "</div>",
            "</div>",
            "</div>",
            reportData.chart_image
                ? [
                      "<!-- Chart Section -->",
                      '<div class="mb-6">',
                      '<div class="text-base font-bold mb-4 text-gray-900 border-b border-gray-300 pb-1">Analytics Chart Visualization</div>',
                      '<div class="text-center my-5 p-5 border border-gray-300 rounded">',
                      '<img src="' +
                          reportData.chart_image +
                          '" alt="Analytics Chart" class="max-w-full h-auto" />',
                      "</div>",
                      "</div>",
                  ].join("")
                : "",
            "<!-- Detailed Analytics Section -->",
            '<div class="mb-6">',
            '<div class="text-base font-bold mb-4 text-gray-900 border-b border-gray-300 pb-1">Analytics Summary (' +
                reportData.time_interval +
                ")</div>",
            '<table class="w-full border-collapse mt-5">',
            "<thead>",
            "<tr>",
            '<th class="border border-gray-300 p-2 text-left bg-gray-100 font-bold">Time Period</th>',
            '<th class="border border-gray-300 p-2 text-left bg-gray-100 font-bold">Total Bookings</th>',
            '<th class="border border-gray-300 p-2 text-left bg-gray-100 font-bold">Registered Users</th>',
            '<th class="border border-gray-300 p-2 text-left bg-gray-100 font-bold">Total Visitors</th>',
            "</tr>",
            "</thead>",
            "<tbody>",
            "<tr>",
            '<td class="border border-gray-300 p-2 text-left">' +
                reportData.time_interval +
                "</td>",
            '<td class="border border-gray-300 p-2 text-left">' +
                (reportData.chart_data.find((d) => d.label === "Total Bookings")
                    ?.bookings || 0) +
                "</td>",
            '<td class="border border-gray-300 p-2 text-left">' +
                (reportData.chart_data.find(
                    (d) => d.label === "Registered Users",
                )?.users || 0) +
                "</td>",
            '<td class="border border-gray-300 p-2 text-left">' +
                (reportData.chart_data.find((d) => d.label === "Total Visitors")
                    ?.visitors || 0) +
                "</td>",
            "</tr>",
            "</tbody>",
            "</table>",
            "</div>",
            "<!-- Footer -->",
            '<div class="mt-8 pt-5 border-t border-gray-300 text-center text-xs text-gray-600">',
            "<p>This report was automatically generated by the Analytics System.</p>",
            "</div>",
            "</body>",
            "<\/html>",
        ].join("");

    printWindow.document.write(htmlContent);
    printWindow.document.close();

    // Wait for content to load, then trigger print
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
                    class="text-4xl font-black uppercase tracking-tight text-slate-900 flex items-center gap-3"
                >
                    Analytics Reports
                </h1>
                <p
                    class="text-sm text-slate-500 font-bold uppercase tracking-widest mt-1"
                >
                    System Performance & Feedback Insights
                </p>
            </div>
            <div class="flex flex-col md:flex-row gap-4">
                <button
                    @click="generateBrowserPDF"
                    class="flex items-center justify-center gap-2 px-8 py-4 bg-slate-900 rounded-xl text-xs font-black uppercase tracking-widest text-white hover:bg-indigo-600 transition-all shadow-xl w-full md:w-auto"
                >
                    Export Report
                </button>
            </div>
        </div>
        <!-- STATS GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- Registered Users -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg flex flex-col justify-between transition-transform hover:scale-[1.02] border-l-8 border-violet-600"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <p
                            class="text-sm font-black text-slate-600 uppercase tracking-tighter mb-2"
                        >
                            Registered Users
                        </p>
                        <h2
                            class="text-4xl font-black tracking-tighter text-slate-900"
                        >
                            {{ summary.totalRegisteredUsers?.toLocaleString()
                            }}<span class="text-violet-500">+</span>
                        </h2>
                    </div>
                </div>
                <span
                    class="text-xs bg-violet-100 text-violet-700 px-3 py-1.5 rounded-lg font-black inline-block w-fit mt-5"
                    >USERS ACTIVE</span
                >
            </div>

            <!-- Bookings -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg flex flex-col justify-between transition-transform hover:scale-[1.02] border-l-8 border-indigo-600"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <p
                            class="text-sm font-black text-slate-600 uppercase tracking-tighter mb-2"
                        >
                            Total Bookings
                        </p>
                        <h2
                            class="text-4xl font-black tracking-tighter text-slate-900"
                        >
                            {{ summary.totalBookings?.toLocaleString()
                            }}<span class="text-indigo-500">+</span>
                        </h2>
                    </div>
                </div>
                <span
                    class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg font-black inline-block w-fit mt-5"
                    >TOTAL BOOKINGS</span
                >
            </div>

            <!-- Visitors -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg flex flex-col justify-between transition-transform hover:scale-[1.02] border-l-8 border-blue-600"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <p
                            class="text-sm font-black text-slate-600 uppercase tracking-tighter mb-2"
                        >
                            Total Visitors
                        </p>
                        <h2
                            class="text-4xl font-black tracking-tighter text-slate-900"
                        >
                            {{ summary.totalVisitors?.toLocaleString()
                            }}<span class="text-blue-500">+</span>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Satisfied -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg flex flex-col justify-between transition-transform hover:scale-[1.02] border-l-8 border-emerald-600"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <p
                            class="text-sm font-black text-slate-600 uppercase tracking-tighter mb-2"
                        >
                            Satisfied
                        </p>
                        <h2
                            class="text-4xl font-black tracking-tighter text-emerald-700"
                        >
                            {{ summary.totalSatisfied?.toLocaleString()
                            }}<span class="text-emerald-500">+</span>
                        </h2>
                    </div>
                </div>
                <span
                    class="text-xs bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg font-black inline-block w-fit mt-5"
                    >POSITIVE FEEDBACK</span
                >
            </div>
            <!-- Unsatisfied -->
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg flex flex-col justify-between transition-transform hover:scale-[1.02] border-l-8 border-rose-600"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <p
                            class="text-sm font-black text-slate-600 uppercase tracking-tighter mb-2"
                        >
                            Unsatisfied
                        </p>
                        <h2
                            class="text-4xl font-black tracking-tighter text-rose-700"
                        >
                            {{ summary.totalUnsatisfied?.toLocaleString()
                            }}<span class="text-rose-500">+</span>
                        </h2>
                    </div>
                </div>
                <span
                    class="text-xs bg-rose-100 text-rose-700 px-3 py-1.5 rounded-lg font-black inline-block w-fit mt-5"
                    >NEGATIVE FEEDBACK</span
                >
            </div>
        </div>

        <!-- FILTERS & CHART -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1 space-y-4">
                <div
                    class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md"
                >
                    <h3
                        class="text-base font-black uppercase mb-5 flex items-center gap-2 border-b-2 border-slate-100 pb-3"
                    >
                        Filter Configuration
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <label
                                class="text-xs font-black uppercase text-slate-500 mb-2 block tracking-widest"
                                >Time Interval</label
                            >
                            <select
                                v-model="selectedInterval"
                                class="w-full p-4 bg-slate-50 border-slate-300 border-2 rounded-xl text-sm font-black focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all"
                            >
                                <option value="" disabled selected>
                                    Select Time Interval
                                </option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="text-xs font-black uppercase text-slate-500 mb-2 block tracking-widest"
                                >Chart Style</label
                            >
                            <select
                                v-model="selectedChartStyle"
                                class="w-full p-4 bg-slate-50 border-slate-300 border-2 rounded-xl text-sm font-black focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all"
                            >
                                <option value="" disabled selected>
                                    Choose Chart Type
                                </option>
                                <option value="bar">Bar Chart</option>
                                <option value="line">Line Chart</option>
                                <option value="pie">Pie Chart</option>
                            </select>
                        </div>
                        <button
                            @click="generateReport"
                            :disabled="form.processing"
                            class="w-full py-4 bg-indigo-600 text-white rounded-xl font-black uppercase text-sm tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-95"
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
            <div
                class="lg:col-span-3 bg-white p-8 rounded-2xl border border-slate-200 shadow-md"
            >
                <h3
                    class="text-xl font-black uppercase text-slate-800 mb-6 border-b-2 border-slate-50 pb-2"
                >
                    Data Visualization
                </h3>
                <div class="w-full h-[450px]">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>
