<?php

declare(strict_types=1);

namespace App\Services\Property;

use App\Models\Room;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * One implementation of "save a room type" shared by the admin panel and the
 * vendor extranet.
 */
class RoomFormService
{
    private const MAX_IMAGES = 40;

    public function rules(?Room $room = null): array
    {
        return [
            'name'              => 'required|string|min:2|max:255',
            'bed_type'          => ($room ? 'nullable' : 'required') . '|string|max:255',
            'price_per_night'   => 'required|numeric|min:1|max:99999999',
            'original_price'    => 'nullable|numeric|min:0|max:99999999',
            'max_adults'        => 'required|integer|min:1|max:30',
            'max_children'      => 'nullable|integer|min:0|max:20',
            'room_size_sqm'     => 'nullable|integer|min:1|max:5000',
            'total_rooms'       => ($room ? 'nullable' : 'required') . '|integer|min:1|max:10000',
            'view_type'         => 'nullable|string|max:100',
            'bathroom_count'    => 'nullable|integer|min:0|max:20',
            'bathroom_features' => 'nullable|array|max:50',
            'bathroom_features.*' => 'string|max:120',
            'smoking_policy'    => 'nullable|string|max:50',
            'balcony_type'      => 'nullable|string|max:100',
            'cancellation_policy' => 'nullable|string|max:5000',
            'amenities'         => 'nullable|array|max:200',
            'amenities.*'       => 'string|max:120',
            'facilities'        => 'nullable|array|max:200',
            'facilities.*'      => 'string|max:120',
            'facilities_text'   => 'nullable|string|max:5000',
            'status'            => ['nullable', Rule::in(['active', 'inactive'])],
            'image_file'        => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'image_files'       => 'nullable|array|max:' . self::MAX_IMAGES,
            'image_files.*'     => 'image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'image_url'         => 'nullable|string|max:10000',
            'image_urls'        => 'nullable|string|max:10000',
            'delete_images'     => 'nullable|array|max:' . self::MAX_IMAGES,
            'delete_images.*'   => 'string|max:2000',
        ];
    }

    public function attributes(Request $request, ?Room $room): array
    {
        $creating = $room === null;
        $adults   = (int) $request->input('max_adults');
        $children = $request->filled('max_children') ? (int) $request->input('max_children') : (int) ($room?->max_children ?? 0);

        $attrs = [
            'name'            => trim((string) $request->input('name')),
            'price_per_night' => round((float) $request->input('price_per_night'), 2),
            'max_adults'      => $adults,
            'max_children'    => $children,
            'max_guests'      => $adults + $children,
            'images'          => $this->images($request, $room),
        ];

        // Text / numeric fields: written when the form contains the control.
        $defaults = [
            'bed_type'       => null,
            'view_type'      => null,
            'smoking_policy' => 'Non-Smoking',
            'balcony_type'   => null,
        ];
        foreach ($defaults as $field => $default) {
            if ($request->has($field)) {
                $value = trim((string) $request->input($field, ''));
                $attrs[$field] = $value !== '' ? $value : ($field === 'bed_type' ? ($room?->bed_type ?? 'Double Bed') : $default);
            } elseif ($creating) {
                $attrs[$field] = $field === 'bed_type' ? 'Double Bed' : $default;
            }
        }

        // "Was" price only when it is higher than the selling price.
        if ($creating || $request->has('original_price')) {
            $was = (float) $request->input('original_price', 0);
            $attrs['original_price'] = $was > $attrs['price_per_night'] ? round($was, 2) : null;
        }

        if ($request->has('cancellation_policy')) {
            $attrs['cancellation_policy'] = trim((string) $request->input('cancellation_policy')) ?: null;
        }

        if ($request->has('room_size_sqm') || $creating) {
            $attrs['room_size_sqm'] = $request->filled('room_size_sqm') ? (int) $request->input('room_size_sqm') : null;
        }

        if ($request->filled('total_rooms')) {
            $attrs['total_rooms'] = (int) $request->input('total_rooms');
        }

        if ($request->has('bathroom_count') || $creating) {
            $attrs['bathroom_count'] = $request->filled('bathroom_count') ? (int) $request->input('bathroom_count') : 1;
        }

        // Tick lists. A hidden "_lists" marker in the form tells us the full form was posted,
        // so an empty list means "none selected" rather than "field not on this form".
        $fullForm = $creating || $request->boolean('_full_form') || $request->has('amenities') || $request->has('facilities') || $request->has('facilities_text');
        if ($fullForm) {
            $facilities = array_merge((array) $request->input('amenities', []), (array) $request->input('facilities', []));
            foreach (preg_split('/\R/', (string) $request->input('facilities_text', '')) as $line) {
                $facilities[] = $line;
            }
            $attrs['facilities'] = $this->cleanList($facilities);
        }

        if ($creating || $request->boolean('_full_form') || $request->has('bathroom_features')) {
            $attrs['bathroom_features'] = $this->cleanList((array) $request->input('bathroom_features', []));
        }

        // Checkboxes (forms post a hidden "0" twin).
        foreach (['breakfast_included', 'free_cancellation', 'extra_bed_allowed'] as $flag) {
            if ($request->has($flag)) {
                $attrs[$flag] = $request->boolean($flag);
            } elseif ($creating) {
                $attrs[$flag] = false;
            }
        }

        if ($request->filled('status')) {
            $attrs['status'] = $request->input('status');
        } elseif ($creating) {
            $attrs['status'] = 'active';
        }

        return $attrs;
    }

    /** Stored room photos: relative paths for uploads, validated links for external images. */
    private function images(Request $request, ?Room $room): array
    {
        // Raw stored values — the model accessor returns fully-resolved URLs, which must not be written back.
        $stored = [];
        if ($room) {
            $raw = $room->getRawOriginal('images');
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            foreach (is_array($decoded) ? $decoded : [] as $img) {
                if (is_string($img) && trim($img) !== '') {
                    $stored[] = trim($img);
                }
            }
        }

        // The form lists photos by their resolved URL, so match deletions against both forms.
        $delete = array_map('strval', (array) $request->input('delete_images', []));
        $kept = [];
        foreach ($stored as $img) {
            if (in_array($img, $delete, true) || in_array(MediaService::url($img), $delete, true)) {
                $this->deleteIfOwnUpload($img);
                continue;
            }
            $kept[] = $img;
        }

        // Apply drag & drop reordered sequence of existing images
        if ($request->has('existing_images_order') && is_array($request->input('existing_images_order'))) {
            $orderedRefs = array_map('strval', (array) $request->input('existing_images_order'));
            $reorderedKept = [];
            foreach ($orderedRefs as $ref) {
                foreach ($kept as $k) {
                    if ($k === $ref || MediaService::url($k) === $ref) {
                        if (!in_array($k, $reorderedKept, true)) {
                            $reorderedKept[] = $k;
                        }
                    }
                }
            }
            foreach ($kept as $k) {
                if (!in_array($k, $reorderedKept, true)) {
                    $reorderedKept[] = $k;
                }
            }
            $kept = $reorderedKept;
        }

        $files = (array) $request->file('image_files', []);
        if ($request->file('image_file')) {
            $files[] = $request->file('image_file');
        }
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $kept[] = MediaService::uploadImage($file, 'uploads/rooms');
            }
        }

        $pasted = (string) $request->input('image_urls', '') . "\n" . (string) $request->input('image_url', '');
        foreach (preg_split('/\R/', $pasted) as $line) {
            $line = trim($line);
            if ($line !== '' && strlen($line) <= 2000 && preg_match('#^https?://#i', $line) && filter_var($line, FILTER_VALIDATE_URL)) {
                $kept[] = $line;
            }
        }

        return array_slice(array_values(array_unique($kept)), 0, self::MAX_IMAGES);
    }

    private function cleanList(array $items): array
    {
        return array_values(array_unique(array_filter(array_map(fn ($v) => trim((string) $v), $items), fn ($v) => $v !== '')));
    }

    private function deleteIfOwnUpload(string $reference): void
    {
        $path = preg_match('#^https?://#i', $reference) ? (string) parse_url($reference, PHP_URL_PATH) : $reference;
        $pos  = strpos($path, 'storage/uploads/rooms/');
        if ($pos === false) {
            return;
        }

        try {
            MediaService::delete(substr($path, $pos));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
