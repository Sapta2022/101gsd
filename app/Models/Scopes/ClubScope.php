<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope enforcing tenant isolation: a club-owned row is only ever
 * visible when the currently authenticated user is bound to that club.
 * Super admins bypass this scope explicitly (see BelongsToClub::forAllClubs()).
 */
class ClubScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user !== null && $user->club_id !== null) {
            $builder->where($model->getTable().'.club_id', $user->club_id);
        }
    }
}
