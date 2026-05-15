<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\FitnessClass;
use App\Models\ClassBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Monthly Revenue Query - Current Year
        $monthlyRevenue = PaymentTransaction::select(
            DB::raw('MONTH(transaction_date) as month'),
            DB::raw('SUM(amount) as total_revenue')
        )
        ->where('status', 'Completed')
        ->whereYear('transaction_date', Carbon::now()->year)
        ->groupBy(DB::raw('MONTH(transaction_date)'))
        ->orderBy('month')
        ->get()
        ->keyBy('month');

        // Fill missing months with 0 revenue
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[] = $monthlyRevenue->get($i)->total_revenue ?? 0;
        }

        // Class Attendance Analysis
        $classAttendance = FitnessClass::select(
            'fitness_classes.class_name',
            'fitness_classes.max_participants',
            DB::raw('COUNT(class_bookings.booking_id) as total_bookings'),
            DB::raw('ROUND((COUNT(class_bookings.booking_id) / (COUNT(DISTINCT class_schedules.schedule_id) * fitness_classes.max_participants)) * 100, 2) as attendance_rate')
        )
        ->leftJoin('class_schedules', 'fitness_classes.class_id', '=', 'class_schedules.class_id')
        ->leftJoin('class_bookings', function($join) {
            $join->on('class_schedules.schedule_id', '=', 'class_bookings.schedule_id')
                 ->where('class_bookings.status', '=', 'Confirmed');
        })
        ->whereNull('fitness_classes.archived_at')
        ->groupBy('fitness_classes.class_id', 'fitness_classes.class_name', 'fitness_classes.max_participants')
        ->having('total_bookings', '>', 0)
        ->get();

        // Summary Statistics
        $totalRevenue = PaymentTransaction::where('status', 'Completed')
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $totalBookings = ClassBooking::where('status', 'Confirmed')
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $activeMembers = DB::table('membership_subscriptions')
            ->where('status', 'Active')
            ->count();

        return view('management.reports.index', compact(
            'revenueData',
            'classAttendance',
            'totalRevenue',
            'totalBookings',
            'activeMembers'
        ));
    }
}
