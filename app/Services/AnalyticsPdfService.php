<?php

namespace App\Services;

use Carbon\Carbon;

class AnalyticsPdfService
{
    /**
     * Generate analytics PDF report data
     */
    public function generateReport(array $data)
    {
        // Get optimized data using AnalyticsService
        $summary = AnalyticsService::getSummaryData();
        $statusFilter = $data['status_filter'] ?? 'weekly';
        $chartData = AnalyticsService::getChartData($statusFilter);

        // Prepare data for PDF (return as JSON)
        return [
            'title' => 'Analytics Report',
            'generated_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'time_interval' => ucfirst($statusFilter),
            'chart_type' => ucfirst($data['chart_type'] ?? 'bar'),
            'chart_image' => $data['chart_image'] ?? null,
            'summary' => $summary,
            'chart_data' => $chartData,
        ];
    }
}
