<?php

namespace App\Models\Concerns;

use App\Models\Club;
use App\Models\Scopes\ClubScope;

/**
 * Applied to every club-owned model (shows, entries, bookings, payments,
 * ...) from Phase 2 onward. Ties the row to a club and auto-scopes every
 * query to the logged-in club admin's own club, so one club can never
 * read another club's data. A super admin query must call
 * static::forAllClubs() to intentionally bypass this.
 */
trait BelongsToClub
{
    public static function bootBelongsToClub(): void
    {
        static::addGlobalScope(new ClubScope);

        static::creating(function ($model) {
            if (empty($model->club_id) && auth()->check() && auth()->user()->club_id !== null) {
                $model->club_id = auth()->user()->club_id;
            }
        });
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Explicit super-admin bypass — never used implicitly.
     */
    public static function forAllClubs()
    {
        return static::withoutGlobalScope(ClubScope::class);
    }
}
