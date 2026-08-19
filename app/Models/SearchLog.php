<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SearchLog — Captures every user search for trending, personalization & analytics.
 *
 * Design:
 *  - Lightweight: only scalar fields, no JSON blobs
 *  - Indexed for fast trending aggregation (query + created_at)
 *  - user_id nullable → supports both guest & authenticated users
 *  - Written asynchronously via LogSearchJob → zero latency on API response
 *
 * @property int         $id
 * @property string      $query          Raw typed query
 * @property string|null $resolved_city  Normalized city (e.g. "Khulna")
 * @property string|null $check_in
 * @property string|null $check_out
 * @property int         $guests
 * @property int         $rooms
 * @property int         $result_count   How many properties matched
 * @property int|null    $user_id
 * @property string      $ip
 * @property string      $session_id
 * @property string|null $search_type    hotel|houseboat|homestay|transfer
 * @property bool        $resulted_in_booking
 */
class SearchLog extends Model
{
    protected $fillable = [
        'query',
        'resolved_city',
        'check_in',
        'check_out',
        'guests',
        'rooms',
        'result_count',
        'user_id',
        'ip',
        'session_id',
        'search_type',
        'resulted_in_booking',
    ];

    protected $casts = [
        'guests'               => 'integer',
        'rooms'                => 'integer',
        'result_count'         => 'integer',
        'resulted_in_booking'  => 'boolean',
        'check_in'             => 'date',
        'check_out'            => 'date',
    ];

    // ─── RELATIONSHIPS ────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── SCOPES ───────────────────────────────────────────────────────────

    /** Searches in the last N days (for trending). */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /** Only searches that had results. */
    public function scopeWithResults($query)
    {
        return $query->where('result_count', '>', 0);
    }
}
