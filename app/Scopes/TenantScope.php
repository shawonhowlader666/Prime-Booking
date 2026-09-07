<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Superadmins can inspect across hotels if explicit hotel_id is not filtered
            if ($user->role === 'SUPERADMIN' && empty($user->hotel_id)) {
                return;
            }

            if ($user->hotel_id) {
                $builder->where($model->getTable() . '.hotel_id', $user->hotel_id);
            }
        }
    }
}
