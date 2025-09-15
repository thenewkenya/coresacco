<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Cache model queries with a custom key
     */
    public static function cacheQuery($key, $callback, $ttl = 300)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Cache model with relationships
     */
    public function cacheWith($relationships, $ttl = 300)
    {
        $key = get_class($this) . '_' . $this->id . '_' . implode('_', (array) $relationships);
        
        return Cache::remember($key, $ttl, function () use ($relationships) {
            return $this->load($relationships);
        });
    }

    /**
     * Clear cache for this model
     */
    public function clearCache()
    {
        $pattern = get_class($this) . '_' . $this->id . '_*';
        Cache::forget($pattern);
    }

    /**
     * Boot the trait
     */
    protected static function bootCacheable()
    {
        static::saved(function ($model) {
            $model->clearCache();
        });

        static::deleted(function ($model) {
            $model->clearCache();
        });
    }
}
