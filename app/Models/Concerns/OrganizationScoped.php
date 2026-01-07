<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait OrganizationScoped
{
    protected static function bootOrganizationScoped(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = auth()->user();

            if (! $user || ! $user->organization_id) {
                return;
            }

            $model = $builder->getModel();
            $table = $model->getTable();

            if (property_exists($model, 'organizationScopeIncludeNull') && $model->organizationScopeIncludeNull) {
                $builder->where(function (Builder $query) use ($table, $user) {
                    $query->where("{$table}.organization_id", $user->organization_id)
                        ->orWhereNull("{$table}.organization_id");
                });
            } else {
                $builder->where("{$table}.organization_id", $user->organization_id);
            }
        });

        static::creating(function ($model): void {
            if (! $model->organization_id && auth()->check()) {
                $model->organization_id = auth()->user()->organization_id;
            }
        });
    }
}
