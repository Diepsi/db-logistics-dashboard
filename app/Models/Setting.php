<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $guarded = ['id'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function logoUrl(?string $default = 'images/logo-anl.jpg'): string
    {
        $path = static::get('site.logo') ?: $default;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return file_exists(public_path('storage/'.$path))
            ? asset('storage/'.$path)
            : asset($path);
    }
}
