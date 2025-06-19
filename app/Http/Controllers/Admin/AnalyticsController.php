<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionPackage;
use App\Models\InspectionService;
use App\Models\InspectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Show analytics overview
     */
    public function overview(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Get basic metrics
        $metrics = [
            'total_revenue' => InspectionRequest::whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_cost'),
            'total_requests' => InspectionRequest::whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'active_packages' => InspectionPackage::where('is_active', true)->count(),
            'active_services' => InspectionService::where('is_active', true)->count(),
        ];

        // Get revenue trend
        $revenueTrend = DB::table('inspection_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_cost) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get request trend
        $requestTrend = DB::table('inspection_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get top packages
        $topPackages = DB::table('inspection_requests')
            ->join('inspection_packages', 'inspection_requests.package_id', '=', 'inspection_packages.id')
            ->whereBetween('inspection_requests.created_at', [$startDate, $endDate])
            ->selectRaw('inspection_packages.display_name as name, COUNT(*) as requests_count, SUM(inspection_requests.total_cost) as total_amount')
            ->groupBy('inspection_packages.id', 'inspection_packages.display_name')
            ->orderByDesc('requests_count')
            ->limit(5)
            ->get();

        // Get top services
        $topServices = DB::table('package_services')
            ->join('inspection_services', 'package_services.service_id', '=', 'inspection_services.id')
            ->join('inspection_requests', 'package_services.package_id', '=', 'inspection_requests.package_id')
            ->whereBetween('inspection_requests.created_at', [$startDate, $endDate])
            ->selectRaw('inspection_services.name, inspection_services.category, COUNT(*) as usage_count, inspection_services.estimated_duration_minutes as duration')
            ->groupBy('inspection_services.id', 'inspection_services.name', 'inspection_services.category', 'inspection_services.estimated_duration_minutes')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get()
            ->map(function ($service) {
                return [
                    'name' => $service->name,
                    'category' => str_replace('_', ' ', ucfirst($service->category)),
                    'usage_count' => $service->usage_count,
                    'duration' => $service->duration
                ];
            });

        // Get insights
        $insights = [
            'most_popular_package' => $topPackages->first()->name ?? null,
            'most_popular_package_count' => $topPackages->first()->requests_count ?? 0,
            'most_used_service' => $topServices->first()['name'] ?? null,
            'most_used_service_count' => $topServices->first()['usage_count'] ?? 0,
            'system_health' => $this->calculateSystemHealth($startDate, $endDate)
        ];

        return view('admin.analytics.overview', compact(
            'metrics',
            'revenueTrend',
            'requestTrend',
            'topPackages',
            'topServices',
            'insights',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show revenue analytics
     */
    public function revenue(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Get revenue data
        $revenueData = DB::table('inspection_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                DATE(created_at) as date,
                SUM(total_cost) as daily_revenue,
                COUNT(*) as daily_requests,
                AVG(total_cost) as avg_revenue_per_request
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get revenue by package
        $revenueByPackage = DB::table('inspection_requests')
            ->join('inspection_packages', 'inspection_requests.package_id', '=', 'inspection_packages.id')
            ->whereBetween('inspection_requests.created_at', [$startDate, $endDate])
            ->selectRaw('
                inspection_packages.display_name,
                SUM(inspection_requests.total_cost) as total_revenue,
                COUNT(*) as request_count,
                AVG(inspection_requests.total_cost) as avg_revenue
            ')
            ->groupBy('inspection_packages.id', 'inspection_packages.display_name')
            ->orderByDesc('total_revenue')
            ->get();

        // Get monthly revenue
        $monthlyRevenue = DB::table('inspection_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(total_cost) as monthly_revenue,
                COUNT(*) as monthly_requests
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.analytics.revenue', compact(
            'revenueData',
            'revenueByPackage',
            'monthlyRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show inspector analytics
     */
    public function inspectors(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Get inspector performance data
        $inspectorPerformance = DB::table('inspection_requests')
            ->join('inspectors', 'inspection_requests.assigned_inspector_id', '=', 'inspectors.id')
            ->whereBetween('inspection_requests.created_at', [$startDate, $endDate])
            ->whereNotNull('inspection_requests.assigned_inspector_id')
            ->selectRaw('
                inspectors.name,
                COUNT(*) as total_assignments,
                SUM(CASE WHEN inspection_requests.status = "completed" THEN 1 ELSE 0 END) as completed_assignments,
                AVG(CASE WHEN inspection_requests.status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate,
                AVG(inspection_requests.total_cost) as avg_revenue_per_assignment
            ')
            ->groupBy('inspectors.id', 'inspectors.name')
            ->orderByDesc('total_assignments')
            ->get();

        // Get inspector workload
        $inspectorWorkload = DB::table('inspection_requests')
            ->join('inspectors', 'inspection_requests.assigned_inspector_id', '=', 'inspectors.id')
            ->whereBetween('inspection_requests.created_at', [$startDate, $endDate])
            ->whereNotNull('inspection_requests.assigned_inspector_id')
            ->selectRaw('
                inspectors.name,
                DATE(inspection_requests.created_at) as date,
                COUNT(*) as daily_assignments
            ')
            ->groupBy('inspectors.id', 'inspectors.name', 'date')
            ->orderBy('inspectors.name')
            ->orderBy('date')
            ->get()
            ->groupBy('name');

        return view('admin.analytics.inspectors', compact(
            'inspectorPerformance',
            'inspectorWorkload',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Calculate system health percentage
     */
    private function calculateSystemHealth($startDate, $endDate)
    {
        $totalRequests = InspectionRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        
        if ($totalRequests === 0) {
            return 100; // Perfect health if no requests
        }

        $completedRequests = InspectionRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $activePackages = InspectionPackage::where('is_active', true)->count();
        $totalPackages = InspectionPackage::count();
        $packageHealth = $totalPackages > 0 ? ($activePackages / $totalPackages) * 100 : 100;

        $activeServices = InspectionService::where('is_active', true)->count();
        $totalServices = InspectionService::count();
        $serviceHealth = $totalServices > 0 ? ($activeServices / $totalServices) * 100 : 100;

        $completionRate = ($completedRequests / $totalRequests) * 100;

        // Weighted average: 40% completion rate, 30% package health, 30% service health
        $systemHealth = ($completionRate * 0.4) + ($packageHealth * 0.3) + ($serviceHealth * 0.3);

        return round($systemHealth, 1);
    }
} 