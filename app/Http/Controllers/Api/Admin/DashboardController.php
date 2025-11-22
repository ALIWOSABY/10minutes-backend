<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Trainer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        // Total counts
        $totalUsers = User::where('role', 'customer')->count();
        $totalSessions = Session::count();
        $totalBookings = Booking::count();
        $totalRevenue = Transaction::where('type', 'credit')
            ->where('source', 'purchase')
            ->sum('amount');

        // Recent stats (last 30 days)
        $last30Days = now()->subDays(30);

        $newUsersLast30Days = User::where('role', 'customer')
            ->where('created_at', '>=', $last30Days)
            ->count();

        $newBookingsLast30Days = Booking::where('created_at', '>=', $last30Days)
            ->count();

        $revenueLast30Days = Transaction::where('type', 'credit')
            ->where('source', 'purchase')
            ->where('created_at', '>=', $last30Days)
            ->sum('amount');

        // Bookings by status
        $bookingsByStatus = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top categories
        $topCategories = Category::withCount('sessions')
            ->orderBy('sessions_count', 'desc')
            ->limit(5)
            ->get();

        // Top trainers
        $topTrainers = Trainer::orderBy('total_sessions', 'desc')
            ->limit(5)
            ->get();

        // Recent bookings
        $recentBookings = Booking::with(['user', 'session'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Monthly revenue chart (last 12 months)
        $monthlyRevenue = Transaction::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('type', 'credit')
            ->where('source', 'purchase')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'users' => $totalUsers,
                    'sessions' => $totalSessions,
                    'bookings' => $totalBookings,
                    'revenue' => $totalRevenue,
                ],
                'last_30_days' => [
                    'new_users' => $newUsersLast30Days,
                    'new_bookings' => $newBookingsLast30Days,
                    'revenue' => $revenueLast30Days,
                ],
                'bookings_by_status' => $bookingsByStatus,
                'top_categories' => $topCategories,
                'top_trainers' => $topTrainers,
                'recent_bookings' => $recentBookings,
                'monthly_revenue' => $monthlyRevenue,
            ]
        ]);
    }
}
