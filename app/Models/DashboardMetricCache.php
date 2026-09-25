<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DashboardMetricCache extends Model
{
    protected $table = 'dashboard_metrics_cache';

    protected $fillable = [
        'metric_key',
        'metric_value',
        'previous_value',
        'trend_direction',
        'trend_percentage',
        'calculated_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'trend_percentage' => 'decimal:2',
    ];

    /**
     * Check if the cached metric is still valid
     */
    public function isValid(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isFuture();
    }

    /**
     * Scope to get only valid (non-expired) metrics
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope to get expired metrics
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Get metric by key
     */
    public static function getMetric(string $key): ?self
    {
        return static::where('metric_key', $key)->valid()->first();
    }

    /**
     * Set or update metric
     */
    public static function setMetric(
        string $key,
        $value,
        $previousValue = null,
        ?string $trendDirection = null,
        ?float $trendPercentage = null,
        ?array $metadata = null,
        int $ttlMinutes = 15
    ): self {
        return static::updateOrCreate(
            ['metric_key' => $key],
            [
                'metric_value' => (string) $value,
                'previous_value' => $previousValue !== null ? (string) $previousValue : null,
                'trend_direction' => $trendDirection,
                'trend_percentage' => $trendPercentage,
                'calculated_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes($ttlMinutes),
                'metadata' => $metadata,
            ]
        );
    }

    /**
     * Clear metric by key
     */
    public static function clearMetric(string $key): bool
    {
        return static::where('metric_key', $key)->delete() > 0;
    }

    /**
     * Clear all metrics
     */
    public static function clearAll(): int
    {
        return static::query()->delete();
    }

    /**
     * Clean up expired metrics
     */
    public static function cleanupExpired(): int
    {
        return static::expired()->delete();
    }
}
