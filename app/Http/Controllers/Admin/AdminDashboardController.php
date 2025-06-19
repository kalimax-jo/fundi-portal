<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Inspector;
use App\Models\BusinessPartner;
use App\Models\InspectionRequest;
use App\Models\Property;
use App\Models\Payment;
use App\Models\Role;
use App\Models\InspectionPackage;
use App\Models\InspectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard overview
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentActivities = $this->getRecentActivities();
        $charts = $this->getChartData();

        return view('admin.dashboard.index', compact('stats', 'recentActivities', 'charts'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            // User Statistics
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'inactive' => User::where('status', 'inactive')->count(),
                'suspended' => User::where('status', 'suspended')->count(),
                'new_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
                'by_role' => Role::withCount('users')->get()->pluck('users_count', 'display_name'),
            ],
            
            // Inspector Statistics (handle if table doesn't exist)
            'inspectors' => [
                'total' => $this->safeCount(Inspector::class),
                'available' => $this->safeCount(Inspector::class, ['availability_status' => 'available']),
                'busy' => $this->safeCount(Inspector::class, ['availability_status' => 'busy']),
                'offline' => $this->safeCount(Inspector::class, ['availability_status' => 'offline']),
                'avg_rating' => $this->safeAvg(Inspector::class, 'rating'),
                'total_inspections_completed' => $this->safeSum(Inspector::class, 'total_inspections'),
            ],
            
            // Business Partner Statistics
            'business_partners' => [
                'total' => $this->safeCount(BusinessPartner::class),
                'active' => $this->safeCount(BusinessPartner::class, ['partnership_status' => 'active']),
                'inactive' => $this->safeCount(BusinessPartner::class, ['partnership_status' => 'inactive']),
                'suspended' => $this->safeCount(BusinessPartner::class, ['partnership_status' => 'suspended']),
                'by_type' => $this->safeGroupCount(BusinessPartner::class, 'type'),
            ],
            
            // Inspection Request Statistics
            'inspection_requests' => [
                'total' => $this->safeCount(InspectionRequest::class),
                'pending' => $this->safeCount(InspectionRequest::class, ['status' => 'pending']),
                'assigned' => $this->safeCount(InspectionRequest::class, ['status' => 'assigned']),
                'in_progress' => $this->safeCount(InspectionRequest::class, ['status' => 'in_progress']),
                'completed' => $this->safeCount(InspectionRequest::class, ['status' => 'completed']),
                'cancelled' => $this->safeCount(InspectionRequest::class, ['status' => 'cancelled']),
                'on_hold' => $this->safeCount(InspectionRequest::class, ['status' => 'on_hold']),
                'this_month' => $this->safeCount(InspectionRequest::class, [], function($query) {
                    return $query->whereMonth('created_at', Carbon::now()->month);
                }),
                'today' => $this->safeCount(InspectionRequest::class, [], function($query) {
                    return $query->whereDate('created_at', Carbon::today());
                }),
            ],
            
            // Property Statistics
            'properties' => [
                'total' => $this->safeCount(Property::class),
                'by_type' => $this->safeGroupCount(Property::class, 'property_type'),
                'with_recent_inspection' => $this->safeCount(Property::class, [], function($query) {
                    return $query->whereNotNull('last_inspection_date')
                        ->where('last_inspection_date', '>=', Carbon::now()->subYear());
                }),
                'needing_inspection' => $this->safeCount(Property::class, [], function($query) {
                    return $query->where(function ($q) {
                        $q->whereNull('last_inspection_date')
                          ->orWhere('last_inspection_date', '<', Carbon::now()->subYear());
                    });
                }),
            ],
            
            // Financial Statistics
            'financial' => [
                'total_revenue' => $this->safeSum(Payment::class, 'amount', ['status' => 'completed']),
                'pending_payments' => $this->safeSum(Payment::class, 'amount', ['status' => 'pending']),
                'revenue_this_month' => $this->safeSum(Payment::class, 'amount', [], function($query) {
                    return $query->where('status', 'completed')
                        ->whereMonth('created_at', Carbon::now()->month);
                }),
                'revenue_last_month' => $this->safeSum(Payment::class, 'amount', [], function($query) {
                    return $query->where('status', 'completed')
                        ->whereMonth('created_at', Carbon::now()->subMonth()->month);
                }),
                'avg_payment_amount' => $this->safeAvg(Payment::class, 'amount', ['status' => 'completed']),
                'payment_methods' => $this->safeGroupCount(Payment::class, 'payment_method', ['status' => 'completed']),
            ],
            
            // Package & Service Statistics
            'packages' => [
                'total' => $this->safeCount(InspectionPackage::class),
                'active' => $this->safeCount(InspectionPackage::class, ['is_active' => true]),
                'inactive' => $this->safeCount(InspectionPackage::class, ['is_active' => false]),
                'fixed_price' => $this->safeCount(InspectionPackage::class, ['is_custom_quote' => false]),
                'custom_quote' => $this->safeCount(InspectionPackage::class, ['is_custom_quote' => true]),
            ],
            
            'services' => [
                'total' => $this->safeCount(InspectionService::class),
                'active' => $this->safeCount(InspectionService::class, ['is_active' => true]),
                'inactive' => $this->safeCount(InspectionService::class, ['is_active' => false]),
                'by_category' => $this->safeGroupCount(InspectionService::class, 'category'),
            ],
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        return [
            'recent_users' => User::with('roles')->latest()->take(10)->get(),
            'recent_inspection_requests' => collect([]), // Empty for now
            'recent_payments' => collect([]), // Empty for now
            'recent_inspectors' => collect([]), // Empty for now
            'recent_business_partners' => collect([]), // Empty for now
        ];
    }

    /**
     * Get chart data for analytics
     */
    private function getChartData()
    {
        return [
            'monthly_requests' => collect([]),
            'monthly_revenue' => collect([]),
            'user_registrations' => collect([]),
            'inspector_performance' => collect([]),
            'request_status_distribution' => collect([]),
        ];
    }

    // Helper methods to safely handle database queries
    private function safeCount($model, $conditions = [], $callback = null)
    {
        try {
            $query = $model::query();
            
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
            
            if ($callback) {
                $query = $callback($query);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeSum($model, $field, $conditions = [], $callback = null)
    {
        try {
            $query = $model::query();
            
            foreach ($conditions as $column => $value) {
                $query->where($column, $value);
            }
            
            if ($callback) {
                $query = $callback($query);
            }
            
            return $query->sum($field) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeAvg($model, $field, $conditions = [])
    {
        try {
            $query = $model::query();
            
            foreach ($conditions as $column => $value) {
                $query->where($column, $value);
            }
            
            return $query->avg($field) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeGroupCount($model, $field, $conditions = [])
    {
        try {
            $query = $model::query();
            
            foreach ($conditions as $column => $value) {
                $query->where($column, $value);
            }
            
            return $query->select($field, DB::raw('count(*) as count'))
                ->groupBy($field)
                ->pluck('count', $field);
        } catch (\Exception $e) {
            return collect([]);
        }
    }
}