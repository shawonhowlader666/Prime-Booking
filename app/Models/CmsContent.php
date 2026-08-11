<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'group',
        'content',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public static function getContent(string $key, string $defaultTitle = '', string $defaultContent = ''): static
    {
        return Cache::remember("cms_content:{$key}", 1800, function () use ($key, $defaultTitle, $defaultContent) {
            return static::firstOrCreate(
                ['key' => $key],
                ['title' => $defaultTitle ?: str_replace('_', ' ', ucfirst($key)), 'content' => $defaultContent]
            );
        });
    }

    public static function setContent(string $key, string $title, string $content, array $metaData = []): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['title' => $title, 'content' => $content, 'meta_data' => $metaData]
        );
        Cache::forget("cms_content:{$key}");
    }
}
