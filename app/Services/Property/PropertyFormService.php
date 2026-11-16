<?php

declare(strict_types=1);

namespace App\Services\Property;

use App\Models\Property;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * One implementation of "save a hotel listing" for both the admin panel and
 * the vendor extranet, so the two can never drift apart.
 *
 * What a vendor may never write: ratings, review counts, badges, commission,
 * featured flag, owner and publication status. Those are either earned from
 * real guest reviews or decided by the platform.
 */
class PropertyFormService
{
    public const GALLERY_CATEGORIES = ['property_views', 'rooms', 'facilities', 'dining', 'other'];
    public const TYPES = ['hotel', 'resort', 'apartment', 'homestay', 'houseboat', 'villa', 'guesthouse', 'hostel'];
    public const STATUSES = ['active', 'published', 'inactive', 'pending', 'rejected'];
    private const MAX_GALLERY_ITEMS = 120;

    /** Optional text columns: an empty input clears the value. */
    private const NULLABLE_TEXT = [
        'nearest_landmark', 'map_embed_url', 'postal_code', 'contact_phone', 'contact_email',
        'house_rules', 'cancellation_policy', 'feature_snippet',
    ];

    public function rules(bool $isAdmin, ?Property $property = null): array
    {
        $rules = [
            'name'              => 'required|string|min:3|max:255',
            'type'              => ['required', 'string', 'max:50'],
            'city'              => 'required|string|max:120',
            'star_rating'       => 'required|integer|min:1|max:5',
            'address'           => 'required|string|max:255',
            'price_per_night'   => 'required|numeric|min:1|max:99999999',
            'original_price'    => 'nullable|numeric|min:0|max:99999999',
            'description'       => 'required|string|min:20|max:20000',
            'nearest_landmark'  => 'nullable|string|max:255',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'map_embed_url'     => 'nullable|string|max:2000',
            'postal_code'       => 'nullable|string|max:20',
            'checkin_time'      => 'nullable|string|max:20',
            'checkout_time'     => 'nullable|string|max:20',
            'contact_phone'     => 'nullable|string|max:50',
            'contact_email'     => 'nullable|email|max:100',
            'house_rules'       => 'nullable|string|max:10000',
            'cancellation_policy' => 'nullable|string|max:10000',
            'feature_snippet'   => 'nullable|string|max:255',
            'total_floors'      => 'nullable|integer|min:0|max:300',
            'total_rooms_count' => 'nullable|integer|min:0|max:100000',
            'year_built'        => 'nullable|integer|min:1800|max:' . ((int) date('Y') + 2),
            'languages_spoken'  => 'nullable|array|max:30',
            'languages_spoken.*' => 'string|max:60',
            'pets_policy'       => 'nullable|string|max:100',
            'amenities'         => 'nullable|array|max:300',
            'amenities.*'       => 'string|max:120',
            'primary_image'     => 'nullable|string|max:2000',
            'set_primary_image_url' => 'nullable|string|max:2000',
            'video_url'         => 'nullable|string|max:2000',
            'gallery_images'    => 'nullable|string|max:20000',
            'new_gallery_category' => ['nullable', Rule::in(self::GALLERY_CATEGORIES)],
            'primary_image_file'    => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'gallery_image_files'   => 'nullable|array|max:40',
            'gallery_image_files.*' => 'image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'existing_gallery'      => 'nullable|array|max:' . self::MAX_GALLERY_ITEMS,
            'existing_gallery_replace.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'video_file'            => 'nullable|file|mimes:mp4,webm,mov|max:102400',
        ];

        if ($isAdmin) {
            $rules += [
                'commission_rate' => 'nullable|numeric|min:0|max:100',
                'status'          => ['nullable', Rule::in(self::STATUSES)],
                'vendor_id'       => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'vendor')],
                'rating_score'    => 'nullable|numeric|min:0|max:10',
                'location_score'  => 'nullable|numeric|min:0|max:10',
                'total_reviews'   => 'nullable|integer|min:0|max:10000000',
                'rooms_left'      => 'nullable|integer|min:0|max:100000',
                'ribbon_badge'    => 'nullable|string|max:100',
            ];
        }

        return $rules;
    }

    /**
     * Attributes to write for this request. Uploads are stored as a side effect.
     */
    public function attributes(Request $request, ?Property $property, bool $isAdmin): array
    {
        $creating = $property === null;

        $attrs = [
            'name'            => trim((string) $request->input('name')),
            'type'            => trim((string) $request->input('type', $property?->type ?? 'hotel')),
            'city'            => trim((string) $request->input('city', $property?->city)),
            'star_rating'     => (int) $request->input('star_rating', $property?->star_rating ?? 3),
            'address'         => trim((string) $request->input('address', $property?->address)),
            'price_per_night' => round((float) $request->input('price_per_night'), 2),
            'description'     => (string) $request->input('description'),
            'checkin_time'    => $request->input('checkin_time') ?: ($property?->checkin_time ?: '14:00'),
            'checkout_time'   => $request->input('checkout_time') ?: ($property?->checkout_time ?: '12:00'),
            'pets_policy'     => $request->input('pets_policy') ?: ($property?->pets_policy ?: 'Pets Not Allowed'),
        ];

        // "Was" price only makes sense when it is higher than the selling price.
        if ($creating || $request->has('original_price')) {
            $original = (float) $request->input('original_price', 0);
            $attrs['original_price'] = $original > $attrs['price_per_night'] ? round($original, 2) : null;
        }

        foreach (self::NULLABLE_TEXT as $field) {
            if ($creating || $request->has($field)) {
                $value = trim((string) $request->input($field, ''));
                $attrs[$field] = $value !== '' ? $value : null;
            }
        }

        foreach (['latitude', 'longitude'] as $field) {
            if ($creating || $request->has($field)) {
                $attrs[$field] = $request->filled($field) ? (string) (float) $request->input($field) : null;
            }
        }

        foreach (['total_floors', 'total_rooms_count', 'year_built'] as $field) {
            if ($creating || $request->has($field)) {
                $attrs[$field] = $request->filled($field) ? (int) $request->input($field) : null;
            }
        }

        if ($creating || $request->has('languages_spoken')) {
            $attrs['languages_spoken'] = array_values(array_unique(array_filter(
                array_map('trim', (array) $request->input('languages_spoken', ['English', 'Bengali']))
            )));
        }

        // The amenity picker always posts its full selection, so an absent list means "none".
        $attrs['amenities'] = array_values(array_unique(array_filter(
            array_map(fn ($a) => trim((string) $a), (array) $request->input('amenities', []))
        )));

        // Checkboxes: forms post a hidden "0" next to each box, so "present" means "the form has this control".
        foreach (['free_cancellation' => true, 'no_credit_card_required' => false, 'is_sold_out' => false] as $flag => $default) {
            if ($request->has($flag)) {
                $attrs[$flag] = $request->boolean($flag);
            } elseif ($creating) {
                $attrs[$flag] = $default;
            }
        }

        $attrs += $this->media($request, $property);

        if ($isAdmin) {
            $attrs += $this->adminOnly($request, $property);
        }

        return $attrs;
    }

    /** Fields only the platform may set. */
    private function adminOnly(Request $request, ?Property $property): array
    {
        $creating = $property === null;
        $attrs = [];

        if ($request->filled('commission_rate')) {
            $attrs['commission_rate'] = round((float) $request->input('commission_rate'), 2);
        }

        if ($request->has('is_featured') || $creating) {
            $attrs['is_featured'] = $request->boolean('is_featured');
        }

        if ($request->has('vendor_id')) {
            $attrs['vendor_id'] = $request->filled('vendor_id') ? (int) $request->input('vendor_id') : null;
        }

        if ($request->has('ribbon_badge')) {
            $badge = trim((string) $request->input('ribbon_badge'));
            $attrs['ribbon_badge'] = $badge !== '' ? $badge : null;
        }

        // Scores for listings imported with an existing review history. They are replaced
        // automatically by the real average as soon as the first guest review is approved.
        foreach (['rating_score' => 'float', 'location_score' => 'float', 'total_reviews' => 'int', 'rooms_left' => 'int'] as $field => $type) {
            if ($request->filled($field)) {
                $attrs[$field] = $type === 'int' ? (int) $request->input($field) : round((float) $request->input($field), 1);
            } elseif ($creating) {
                $attrs[$field] = 0;
            }
        }

        return $attrs;
    }

    /** Cover image, gallery and video. Paths are stored relative so they survive a domain or CDN change. */
    private function media(Request $request, ?Property $property): array
    {
        $name     = trim((string) $request->input('name'));
        $category = in_array($request->input('new_gallery_category'), self::GALLERY_CATEGORIES, true)
            ? $request->input('new_gallery_category')
            : 'property_views';

        $before  = $this->normaliseGallery($property?->images, $property?->name ?? $name);
        $gallery = [];

        // 1. Existing photos: keep / re-categorise / re-title / replace / delete.
        if (is_array($request->input('existing_gallery'))) {
            foreach ($request->input('existing_gallery') as $index => $item) {
                if (!is_array($item) || !empty($item['delete'])) {
                    continue;
                }

                $url = $this->safeMediaReference((string) ($item['url'] ?? ''));

                $replacement = $request->file("existing_gallery_replace.{$index}");
                if ($replacement && $replacement->isValid()) {
                    $url = MediaService::uploadImage($replacement, 'uploads/properties/gallery');
                }

                if ($url === null) {
                    continue;
                }

                $cat = in_array($item['category'] ?? null, self::GALLERY_CATEGORIES, true) ? $item['category'] : 'property_views';

                $gallery[] = [
                    'url'      => $url,
                    'category' => $cat,
                    'title'    => Str::limit(trim((string) ($item['title'] ?? '')) ?: $this->defaultTitle($name, $cat), 150, ''),
                ];
            }
        } else {
            $gallery = $before;
        }

        // 2. New uploads.
        foreach ((array) $request->file('gallery_image_files', []) as $file) {
            if ($file && $file->isValid()) {
                $gallery[] = [
                    'url'      => MediaService::uploadImage($file, 'uploads/properties/gallery'),
                    'category' => $category,
                    'title'    => $this->defaultTitle($name, $category),
                ];
            }
        }

        // 3. Pasted image links, one per line.
        foreach (preg_split('/\R/', (string) $request->input('gallery_images', '')) as $line) {
            $url = $this->safeMediaReference(trim($line));
            if ($url !== null) {
                $gallery[] = ['url' => $url, 'category' => $category, 'title' => $this->defaultTitle($name, $category)];
            }
        }

        // De-duplicate by URL, keep order, cap the size.
        $seen = [];
        $gallery = array_values(array_filter($gallery, function ($item) use (&$seen) {
            if (isset($seen[$item['url']])) {
                return false;
            }
            return $seen[$item['url']] = true;
        }));
        $gallery = array_slice($gallery, 0, self::MAX_GALLERY_ITEMS);

        // Cover image.
        $cover = $property?->primary_image;
        $coverFile = $request->file('primary_image_file');
        if ($coverFile && $coverFile->isValid()) {
            $cover = MediaService::uploadImage($coverFile, 'uploads/properties');
        } elseif (($picked = $this->safeMediaReference((string) $request->input('set_primary_image_url', ''))) !== null) {
            $cover = $picked;
        } elseif (($typed = $this->safeMediaReference((string) $request->input('primary_image', ''))) !== null) {
            $cover = $typed;
        }
        if (empty($cover) && $gallery !== []) {
            $cover = $gallery[0]['url'];
        }

        // Video.
        $video = $property?->video_url;
        $videoFile = $request->file('video_file');
        if ($request->boolean('remove_video')) {
            $video = null;
        } elseif ($videoFile && $videoFile->isValid()) {
            $video = MediaService::uploadVideo($videoFile, 'uploads/properties/videos');
        } elseif ($request->has('video_url')) {
            $video = $this->normaliseVideoUrl((string) $request->input('video_url', '')) ?? ($request->filled('video_url') ? $video : null);
        }

        $this->deleteOrphans($before, $gallery, $property, $cover, $video);

        return ['images' => $gallery, 'primary_image' => $cover ?: null, 'video_url' => $video];
    }

    /** Gallery rows as [{url, category, title}], whatever shape they were stored in. */
    public function normaliseGallery(mixed $images, string $name = ''): array
    {
        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        $out = [];
        foreach (is_array($images) ? $images : [] as $item) {
            $url = is_array($item) ? ($item['url'] ?? $item['path'] ?? null) : $item;
            if (!is_string($url) || trim($url) === '') {
                continue;
            }
            $cat = is_array($item) && in_array($item['category'] ?? null, self::GALLERY_CATEGORIES, true) ? $item['category'] : 'property_views';
            $out[] = [
                'url'      => trim($url),
                'category' => $cat,
                'title'    => is_array($item) && !empty($item['title']) ? (string) $item['title'] : $this->defaultTitle($name, $cat),
            ];
        }

        return $out;
    }

    /** Accept only http(s) links or files in our own storage. Anything else (javascript:, data:, ../) is dropped. */
    private function safeMediaReference(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || strlen($value) > 2000) {
            return null;
        }

        if (preg_match('#^https?://#i', $value)) {
            return filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
        }

        if (preg_match('#^/?storage/[A-Za-z0-9_\-./]+$#', $value) && !str_contains($value, '..')) {
            return '/' . ltrim($value, '/');
        }

        return null;
    }

    private function normaliseVideoUrl(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/i', $value, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return $this->safeMediaReference($value);
    }

    private function defaultTitle(string $name, string $category): string
    {
        return Str::limit(trim($name . ' - ' . Str::headline($category)), 150, '');
    }

    /** Remove uploaded files that this save dropped from the listing. External links are left alone. */
    private function deleteOrphans(array $before, array $after, ?Property $property, ?string $cover, ?string $video): void
    {
        if (!$property) {
            return;
        }

        $kept = array_column($after, 'url');
        $kept[] = (string) $cover;
        $kept[] = (string) $video;

        $candidates = array_column($before, 'url');
        $candidates[] = (string) $property->primary_image;
        $candidates[] = (string) $property->video_url;

        foreach (array_unique(array_filter($candidates)) as $old) {
            if (in_array($old, $kept, true) || !$this->isOwnUpload($old)) {
                continue;
            }
            try {
                MediaService::delete($this->relativeStoragePath($old));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    private function isOwnUpload(string $reference): bool
    {
        return str_contains($this->relativeStoragePath($reference), 'uploads/properties/');
    }

    /** "/storage/uploads/x.webp" and "https://host/storage/uploads/x.webp" both become "storage/uploads/x.webp". */
    private function relativeStoragePath(string $reference): string
    {
        $path = preg_match('#^https?://#i', $reference) ? (string) parse_url($reference, PHP_URL_PATH) : $reference;
        $pos  = strpos($path, 'storage/');

        return $pos === false ? '' : substr($path, $pos);
    }

    /** A unique, readable slug that does not change when the name is edited later. */
    public function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'property';
        $slug = $base;
        $n = 2;
        while (Property::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $n++;
        }

        return $slug;
    }
}
