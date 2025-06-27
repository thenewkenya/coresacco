<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get all settings grouped by group
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember('settings.all', 3600, function () {
            $settings = static::all();
            $grouped = [];

            foreach ($settings as $setting) {
                $grouped[$setting->group][$setting->key] = [
                    'value' => static::castValue($setting->value, $setting->type),
                    'label' => $setting->label,
                    'description' => $setting->description,
                    'type' => $setting->type,
                    'raw_value' => $setting->value
                ];
            }

            return $grouped;
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group): array
    {
        $settings = static::where('group', $group)->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = [
                'value' => static::castValue($setting->value, $setting->type),
                'label' => $setting->label,
                'description' => $setting->description,
                'type' => $setting->type,
                'raw_value' => $setting->value
            ];
        }

        return $result;
    }

    /**
     * Cast value to appropriate type
     */
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
            case 'integer':
                return is_numeric($value) ? (int) $value : 0;
            case 'float':
            case 'decimal':
                return is_numeric($value) ? (float) $value : 0.0;
            case 'json':
                return json_decode($value, true) ?? [];
            case 'array':
                return is_array($value) ? $value : explode(',', $value);
            default:
                return $value;
        }
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('settings.all');
        
        // Clear individual setting caches
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
    }

    /**
     * Boot method to clear cache when settings change
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
