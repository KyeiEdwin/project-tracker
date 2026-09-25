<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardMetricsService $metricsService
    ) {}

    /**
     * Display the dashboard with all metrics
     */
    public function index(): Response
    {
        abort_unless(request()->user('web')?->hasPermission('dashboard.admin.view'), 403);

        // Get all metrics from service
        $metrics = $this->metricsService->getAllMetrics();

        return Inertia::render('Dashboard.Enhanced', [
            'metrics' => $metrics,
        ]);
    }

    /**
     * API endpoint to refresh metrics (for manual refresh button)
     */
    public function refresh(): JsonResponse
    {
        abort_unless(request()->user('web')?->hasPermission('dashboard.admin.view'), 403);

        // Clear cache and get fresh metrics
        $this->metricsService->clearCache();
        $metrics = $this->metricsService->getAllMetrics();

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
            'refreshedAt' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get cache statistics (useful for monitoring)
     */
    public function cacheStats(): JsonResponse
    {
        abort_unless(request()->user('web')?->hasPermission('dashboard.admin.view'), 403);

        $stats = $this->metricsService->getCacheStats();

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Invalidate specific metric cache
     */
    public function invalidateMetric(string $metricKey): JsonResponse
    {
        abort_unless(request()->user('web')?->hasPermission('dashboard.admin.view'), 403);

        $this->metricsService->invalidateMetric($metricKey);

        return response()->json([
            'success' => true,
            'message' => "Metric '{$metricKey}' cache invalidated successfully.",
        ]);
    }

    /**
     * Warm up the cache with all metrics
     */
    public function warmCache(): JsonResponse
    {
        abort_unless(request()->user('web')?->hasPermission('dashboard.admin.view'), 403);

        $metrics = $this->metricsService->warmCache();

        return response()->json([
            'success' => true,
            'message' => 'Cache warmed up successfully.',
            'metrics' => $metrics,
        ]);
    }
}
