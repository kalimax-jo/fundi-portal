<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\InspectionStatusHistory;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('layouts.headtech', function ($view) {
            $recentActivity = InspectionStatusHistory::with('inspectionRequest.property')
                ->whereIn('new_status', ['pending', 'in_progress', 'completed'])
                ->where('changed_at', '>=', Carbon::now()->subDay())
                ->orderByDesc('changed_at')
                ->limit(5)
                ->get();

            $notificationCount = $recentActivity->count();

            $view->with([
                'ht_notifications' => $recentActivity,
                'ht_notification_count' => $notificationCount,
            ]);
        });
    }
}
