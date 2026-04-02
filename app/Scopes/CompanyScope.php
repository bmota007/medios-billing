<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // No auth → do nothing
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // ✅ SUPER ADMIN = SEE EVERYTHING
        if ($user->role === 'super_admin') {
            return;
        }

        // ✅ SUPPORT = ALSO SEE EVERYTHING (optional)
        if ($user->role === 'support') {
            return;
        }

        // ✅ NORMAL USERS = RESTRICT TO COMPANY
        if ($user->company_id) {
            $builder->where(
                $model->getTable() . '.company_id',
                $user->company_id
            );
        }
    }
}
