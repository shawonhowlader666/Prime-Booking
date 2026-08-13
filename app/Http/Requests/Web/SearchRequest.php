<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SearchRequest — Validates & sanitizes all search input before it reaches the controller.
 *
 * Industry principle: Never trust raw request input in controller or repository.
 * All validation, casting, and default-value logic lives HERE, not scattered in controllers.
 *
 * Benefits at billion-scale:
 *  - Prevents malformed queries hitting the DB
 *  - Enforces max per_page to prevent memory-exhaustion attacks
 *  - All defaults in one place — easy to change
 */
class SearchRequest extends FormRequest
{
    /** Anyone can search — no auth required. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination'  => ['nullable', 'string', 'max:150'],
            'division'     => ['nullable', 'string', 'max:100'],
            'district'     => ['nullable', 'string', 'max:100'],
            'upazila'      => ['nullable', 'string', 'max:100'],
            'search_type'  => ['nullable', 'string', 'max:50'],
            'type'         => ['nullable', 'string', 'max:50'],
            'check_in'     => ['nullable', 'date'],
            'check_out'    => ['nullable', 'date'],
            'guests'       => ['nullable', 'integer', 'min:1', 'max:50'],
            'rooms'        => ['nullable', 'integer', 'min:1', 'max:10'],
            'entire_home'  => ['nullable', 'boolean'],
            'min_price'    => ['nullable', 'numeric', 'min:0'],
            'max_price'    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'star_rating'  => ['nullable', 'array'],
            'star_rating.*'=> ['integer', 'between:1,5'],
            'guest_rating' => ['nullable', 'array'],
            'guest_rating.*'=> ['numeric', 'between:1,10'],
            'amenities'    => ['nullable', 'array'],
            'amenities.*'  => ['string', 'max:100'],
            'sort_by'      => ['nullable', 'string', 'in:featured,price_low,price_high,rating,newest'],
            'page'         => ['nullable', 'integer', 'min:1', 'max:9999'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:50'],  // cap at 50 — never let user request 10,000 rows
        ];
    }

    // ─── Typed, sanitized getters ─────────────────────────────────────────
    // Controllers call these methods — never access $request->input() directly.

    public function destination(): string
    {
        return trim((string) ($this->validated()['destination'] ?? ''));
    }

    public function searchType(): string
    {
        return strtolower(trim((string) ($this->validated()['type'] ?? 'all')));
    }

    public function checkIn(): string
    {
        return $this->validated()['check_in'] ?? now()->format('Y-m-d');
    }

    public function checkOut(): string
    {
        return $this->validated()['check_out'] ?? now()->addDays(2)->format('Y-m-d');
    }

    public function guests(): int
    {
        return (int) ($this->validated()['guests'] ?? 2);
    }

    public function minPrice(): float
    {
        return (float) ($this->validated()['min_price'] ?? 0);
    }

    public function maxPrice(): float
    {
        return (float) ($this->validated()['max_price'] ?? 10_000_000);
    }

    /** @return list<int> */
    public function starRatings(): array
    {
        return array_map('intval', (array) ($this->validated()['star_rating'] ?? []));
    }

    /** @return list<float> */
    public function guestRatings(): array
    {
        return array_map('floatval', (array) ($this->validated()['guest_rating'] ?? []));
    }

    /** @return list<string> */
    public function amenities(): array
    {
        return array_filter(
            array_map('strval', (array) ($this->validated()['amenities'] ?? []))
        );
    }

    public function sortBy(): string
    {
        return (string) ($this->validated()['sort_by'] ?? 'featured');
    }

    public function page(): int
    {
        return (int) ($this->validated()['page'] ?? 1);
    }

    public function perPage(): int
    {
        return (int) ($this->validated()['per_page'] ?? 12);
    }

    /** Compile into a plain array for the repository. */
    public function toSearchParams(): array
    {
        return [
            'destination'  => $this->destination(),
            'division'     => trim((string) ($this->validated()['division'] ?? '')),
            'district'     => trim((string) ($this->validated()['district'] ?? '')),
            'upazila'      => trim((string) ($this->validated()['upazila'] ?? '')),
            'search_type'  => $this->searchType(),
            'check_in'     => $this->checkIn(),
            'check_out'    => $this->checkOut(),
            'guests'       => $this->guests(),
            'rooms'        => (int) ($this->validated()['rooms'] ?? 1),
            'entire_home'  => (bool) ($this->validated()['entire_home'] ?? false),
            'min_price'    => $this->minPrice(),
            'max_price'    => $this->maxPrice(),
            'star_rating'  => $this->starRatings(),
            'guest_rating' => $this->guestRatings(),
            'amenities'    => $this->amenities(),
            'sort_by'      => $this->sortBy(),
            'page'         => $this->page(),
            'per_page'     => $this->perPage(),
        ];
    }
}
