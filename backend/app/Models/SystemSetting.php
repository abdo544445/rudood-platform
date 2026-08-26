<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public const CACHE_KEY_MAINTENANCE = 'system_setting_maintenance_mode';

    /**
     * Check if platform maintenance mode is active.
     */
    public static function isMaintenanceActive(): bool
    {
        $details = static::getMaintenanceDetails();
        return (bool)($details['is_active'] ?? false);
    }

    /**
     * Get maintenance configuration details with fallback defaults.
     */
    public static function getMaintenanceDetails(): array
    {
        return Cache::remember(static::CACHE_KEY_MAINTENANCE, 3600, function () {
            $setting = static::where('key', 'maintenance_mode')->first();
            $val = $setting?->value ?? [];

            return [
                'is_active'         => (bool)($val['is_active'] ?? false),
                'title'             => $val['title'] ?? 'أعمال صيانة وتطوير مجدولة 🛠️',
                'message'           => $val['message'] ?? 'نقوم حالياً بإجراء تحسينات مجدولة وتحديثات دورية على أنظمة منصة ردود لتقديم خدمة أسرع وتجربة استثنائية. سنعود للعمل بكامل طاقتنا في الموعد المحدد أدناه.',
                'scheduled_ends_at' => $val['scheduled_ends_at'] ?? null,
                'activated_at'      => $val['activated_at'] ?? null,
                'activated_by'      => $val['activated_by'] ?? null,
            ];
        });
    }

    /**
     * Update maintenance mode configuration and refresh cache.
     */
    public static function setMaintenance(bool $active, array $params = []): array
    {
        $current = static::getMaintenanceDetails();

        $newData = [
            'is_active'         => $active,
            'title'             => !empty($params['title']) ? $params['title'] : $current['title'],
            'message'           => !empty($params['message']) ? $params['message'] : $current['message'],
            'scheduled_ends_at' => array_key_exists('scheduled_ends_at', $params) ? $params['scheduled_ends_at'] : $current['scheduled_ends_at'],
            'activated_at'      => $active ? ($current['activated_at'] ?? now()->toDateTimeString()) : null,
            'activated_by'      => $active ? ($params['activated_by'] ?? auth()->id()) : null,
        ];

        static::updateOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => $newData]
        );

        Cache::forget(static::CACHE_KEY_MAINTENANCE);
        Cache::put(static::CACHE_KEY_MAINTENANCE, $newData, 3600);

        return $newData;
    }
}
