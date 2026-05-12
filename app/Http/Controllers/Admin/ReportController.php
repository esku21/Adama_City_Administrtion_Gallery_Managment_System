<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\AnalyticsPdfService;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display analytics reports page.
     */
    public function index(Request $request)
    {
        // Get filters from request
        $statusFilter = $request->input('status_filter', 'weekly');
        $chartStyle = $request->input('chart_style', 'bar');

        // Get optimized data using service
        $summary = AnalyticsService::getSummaryData();
        $chartData = AnalyticsService::getChartData($statusFilter);

        // Return data to Vue component
        return Inertia::render('Admin/Reports', [
            'reportData' => $chartData,
            'summary'    => $summary,
            'filters'    => [
                'status_filter' => $statusFilter,
                'chart_style'   => $chartStyle,
            ],
        ]);
    }

    /**
     * Export analytics report as CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $statusFilter = $request->input('status_filter', 'weekly');

        // Get optimized data using service
        $summary = AnalyticsService::getSummaryData();
        $chartData = AnalyticsService::getChartData($statusFilter);

        // Generate CSV
        $filename = 'analytics_report_' . $statusFilter . '_' . Carbon::now()->format('Y-m-d') . '.csv';

        return new StreamedResponse(function() use ($summary, $chartData, $statusFilter) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel display
            fwrite($file, "\xEF\xBB\xBF");

            // Summary Section
            fputcsv($file, ['ANALYTICS REPORT - OVERALL SUMMARY']);
            fputcsv($file, ['Generated on:', Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);

            fputcsv($file, ['METRIC', 'TOTAL COUNT']);
            fputcsv($file, ['Registered Users', number_format($summary['totalRegisteredUsers'])]);
            fputcsv($file, ['Total Bookings', number_format($summary['totalBookings'])]);
            fputcsv($file, ['Total Visitors', number_format($summary['totalVisitors'])]);
            fputcsv($file, ['Satisfied Feedback', number_format($summary['totalSatisfied'])]);
            fputcsv($file, ['Unsatisfied Feedback', number_format($summary['totalUnsatisfied'])]);

            // Filtered Analytics Section
            fputcsv($file, []);
            fputcsv($file, ['FILTERED ANALYTICS DATA']);
            fputcsv($file, ['Time Period:', ucfirst($statusFilter)]);
            fputcsv($file, []);

            fputcsv($file, ['Date/Period', 'Total Bookings']);

            foreach ($chartData as $data) {
                fputcsv($file, [$data['label'], $data['bookings']]);
            }

            // Add totals
            $totalBookings = array_sum(array_column($chartData, 'bookings'));
            fputcsv($file, ['TOTAL', $totalBookings]);

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export analytics report as PDF data
     */
    public function exportPdf(Request $request)
    {
        try {
            // Validate the request data
            $data = $request->validate([
                'status_filter' => 'required|string|in:weekly,monthly,yearly',
                'chart_type' => 'required|string|in:bar,line,pie',
                'chart_image' => 'nullable|string',
            ]);

            // Generate report data using the service
            $pdfService = new AnalyticsPdfService();
            $reportData = $pdfService->generateReport($data);

            // Return JSON data for Vue-based PDF generation
            return response()->json($reportData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate report data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
