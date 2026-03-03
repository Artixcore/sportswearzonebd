<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $cacheKey = 'setting_' . $key;
            return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
                $row = static::find($key);
                return $row ? $row->value : $default;
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        try {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
            Cache::forget('setting_' . $key);
        } catch (\Throwable $e) {
            // ignore when table does not exist
        }
    }
}
