<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'author_id',
        'author_name',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'featured_image',
        'views_count',
        'is_published',
        'is_featured',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public static function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $count = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }
        return $slug;
    }
}
