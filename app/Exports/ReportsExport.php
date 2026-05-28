<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return new Collection([
            [
                'registered_users' => User::count() - 1,
                'total_bookings' => Booking::count(),
                'total_visitors' => Booking::sum('visitor_count'),
                'satisfied' => Feedback::where('status', 'satisfied')->count(),
                'unsatisfied' => Feedback::where('status', 'unsatisfied')->count(),
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Registered Users',
            'Total Bookings',
            'Total Visitors',
            'Satisfied',
            'Unsatisfied',
        ];
    }
}